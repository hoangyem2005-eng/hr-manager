<?php

namespace App\Http\Controllers;

use App\Models\Document;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * DocumentController
 *
 * Quản lý toàn bộ vòng đời của file đính kèm:
 *   - Upload nhiều file → lưu vào disk 'public' (được symlink ra public/storage)
 *   - Download / xem trực tiếp file
 *   - Xóa file khỏi storage + DB
 *   - Liệt kê file theo task
 *
 * === Cơ chế Symlink ===
 *   storage/app/public/tasks/{taskId}/{uuid}.{ext}   ← file thực
 *          ↕  (php artisan storage:link)
 *   public/storage → storage/app/public              ← symlink
 *   URL: http://domain/storage/tasks/{taskId}/{uuid}.{ext}
 */
class DocumentController extends Controller
{
    /** Danh sách MIME type được phép */
    private const ALLOWED_MIMES = 'pdf,doc,docx,xls,xlsx,ppt,pptx,jpg,jpeg,png,gif,zip,rar,txt,csv';

    /** Kích thước tối đa mỗi file (KB) = 20 MB */
    private const MAX_FILE_SIZE = 20480;

    /** Tối đa số file mỗi lần upload */
    private const MAX_FILES_PER_REQUEST = 10;

    // ==================== LIỆT KÊ FILE THEO TASK ====================

    /**
     * Trả về danh sách file đính kèm của một task (JSON).
     */
    public function listByTask(int $taskId)
    {
        $docs = Document::where('task_id', $taskId)
            ->with(['task.assignee', 'uploader:id,name,department_id,role_id'])
            ->latest()
            ->get()
            ->filter(fn (Document $doc) => $this->canView($doc, Auth::user()))
            ->values()
            ->map(fn ($doc) => $this->formatDocResponse($doc));

        return response()->json([
            'success' => true,
            'data'    => $docs,
            'count'   => $docs->count(),
        ]);
    }

    // ==================== UPLOAD FILE ====================

    /**
     * Upload một hoặc nhiều file đính kèm cho một task.
     *
     * Flow:
     *   1. Validate request (loại file, kích thước, số lượng)
     *   2. Lưu mỗi file vào disk 'public' dưới thư mục tasks/{taskId}/
     *      – File đặt tên theo UUID để tránh xung đột
     *   3. Ghi metadata vào bảng documents
     *   4. Trả về danh sách URL công khai (qua symlink)
     *
     * Lưu ý: `php artisan storage:link` phải chạy một lần để tạo symlink.
     */
    public function upload(Request $request, int $taskId)
    {
        $request->validate([
            'files'   => 'required|array|min:1|max:' . self::MAX_FILES_PER_REQUEST,
            'files.*' => [
                'required',
                'file',
                'max:' . self::MAX_FILE_SIZE,
                'mimes:' . self::ALLOWED_MIMES,
            ],
        ], [
            'files.required'    => 'Vui lòng chọn ít nhất một tệp.',
            'files.max'         => 'Tối đa ' . self::MAX_FILES_PER_REQUEST . ' tệp mỗi lần tải lên.',
            'files.*.max'       => 'Mỗi tệp không được vượt quá 20 MB.',
            'files.*.mimes'     => 'Định dạng tệp không được hỗ trợ. Cho phép: ' . str_replace(',', ', ', self::ALLOWED_MIMES),
            'files.*.required'  => 'Tệp không hợp lệ.',
        ]);

        $results  = [];
        $errors   = [];
        $disk     = 'public';                         // Disk được symlink ra public/storage
        $directory = 'tasks/' . $taskId;             // Thư mục phân cấp theo task

        foreach ($request->file('files') as $file) {
            if (!$file->isValid()) {
                $errors[] = $file->getClientOriginalName() . ': Lỗi upload.';
                continue;
            }

            try {
                $originalName = $file->getClientOriginalName();
                $extension    = strtolower($file->getClientOriginalExtension());
                $storedName   = Str::uuid() . '.' . $extension;

                // === CORE: Lưu file vào storage/app/public/tasks/{taskId}/{uuid}.ext ===
                // Storage::disk('public')->putFileAs() → thực chất ghi vào storage/app/public
                // Nhờ symlink (public/storage → storage/app/public), file được phục vụ qua HTTP
                $storedPath = Storage::disk($disk)->putFileAs(
                    $directory,
                    $file,
                    $storedName
                );

                if (!$storedPath) {
                    $errors[] = $originalName . ': Không thể lưu tệp.';
                    continue;
                }

                // Ghi metadata vào DB
                $doc = Document::create([
                    'task_id'       => $taskId,
                    'user_id'       => Auth::id(),
                    'file_name'     => $originalName,
                    'file_path'     => $storedPath,
                    'file_type'     => $extension,
                    'disk'          => $disk,
                    'review_status' => $this->initialStatus(),
                ]);

                $results[] = $this->formatDocResponse($doc);

            } catch (\Exception $e) {
                $errors[] = ($file->getClientOriginalName() ?? 'File') . ': ' . $e->getMessage();
            }
        }

        $response = [
            'success'   => count($results) > 0,
            'uploaded'  => $results,
            'count'     => count($results),
        ];

        if (!empty($errors)) {
            $response['errors'] = $errors;
        }

        $statusCode = count($results) === 0 ? 422 : 201;

        return response()->json($response, $statusCode);
    }

    // ==================== DOWNLOAD / XEM FILE ====================

    /**
     * Download file đính kèm về máy client.
     */
    public function download(int $documentId)
    {
        $doc = Document::findOrFail($documentId);
        abort_unless($this->canView($doc, Auth::user()), 403);

        $disk = $doc->disk ?? 'public';

        if (!Storage::disk($disk)->exists($doc->file_path)) {
            abort(404, 'Tệp không tìm thấy trên máy chủ. Có thể đã bị xóa.');
        }

        return Storage::disk($disk)->download($doc->file_path, $doc->file_name);
    }

    /**
     * Hiển thị file trực tiếp trong trình duyệt (inline) – dành cho ảnh / PDF.
     */
    public function preview(int $documentId)
    {
        $doc  = Document::findOrFail($documentId);
        abort_unless($this->canView($doc, Auth::user()), 403);
        $disk = $doc->disk ?? 'public';

        if (!Storage::disk($disk)->exists($doc->file_path)) {
            abort(404, 'Tệp không tìm thấy.');
        }

        $mimeMap  = [
            'pdf'  => 'application/pdf',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png'  => 'image/png',
            'gif'  => 'image/gif',
        ];

        $mime     = $mimeMap[$doc->file_type] ?? 'application/octet-stream';
        $content  = Storage::disk($disk)->get($doc->file_path);

        return response($content, 200)
            ->header('Content-Type', $mime)
            ->header('Content-Disposition', 'inline; filename="' . $doc->file_name . '"');
    }

    // ==================== XÓA FILE ====================

    /**
     * Xóa một file đính kèm:
     *   1. Xóa file vật lý khỏi storage (disk 'public')
     *   2. Xóa record trong bảng documents
     *
     * Kiểm tra quyền: chỉ người tải lên / Trưởng phòng / Giám đốc được xóa.
     */
    public function destroy(int $documentId)
    {
        $doc  = Document::findOrFail($documentId);
        $user = Auth::user();

        // --- Kiểm tra quyền ---
        if (!$this->canDelete($doc, $user)) {
            if (request()->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Bạn không có quyền xóa tệp này.'], 403);
            }
            abort(403, 'Bạn không có quyền xóa tệp này.');
        }

        // --- Xóa file vật lý ---
        $disk = $doc->disk ?? 'public';
        if (Storage::disk($disk)->exists($doc->file_path)) {
            Storage::disk($disk)->delete($doc->file_path);

            // Dọn thư mục rỗng (tùy chọn)
            $directory = dirname($doc->file_path);
            $remaining = Storage::disk($disk)->files($directory);
            if (empty($remaining)) {
                Storage::disk($disk)->deleteDirectory($directory);
            }
        }

        // --- Xóa record DB ---
        $doc->delete();

        if (request()->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Đã xóa tệp thành công.',
            ]);
        }

        return redirect()->back()->with('thongbao', 'Đã xóa tệp đính kèm.');
    }

    // ==================== PRIVATE HELPERS ====================

    /**
     * Format thông tin Document thành mảng trả về client.
     * URL được tạo qua Storage::url() → trỏ đến symlink public/storage.
     */
    private function formatDocResponse(Document $doc): array
    {
        $disk = $doc->disk ?? 'public';

        return [
            'id'           => $doc->id,
            'task_id'      => $doc->task_id,
            'file_name'    => $doc->file_name,
            'file_type'    => $doc->file_type,
            'file_path'    => $doc->file_path,
            'download_url' => route('congviec.file.download', $doc->id),
            'preview_url'  => route('congviec.file.preview', $doc->id),
            // URL công khai qua Symlink (không cần qua controller)
            'public_url'   => Storage::disk($disk)->url($doc->file_path),
            'uploader'     => $doc->uploader ? [
                'id'   => $doc->uploader->id,
                'name' => $doc->uploader->name,
            ] : null,
            'uploaded_at'  => $doc->created_at?->format('d/m/Y H:i'),
            'review_status' => $doc->review_status,
            'forwarded_at' => $doc->forwarded_at?->format('d/m/Y H:i'),
            'is_image'     => in_array($doc->file_type, ['jpg', 'jpeg', 'png', 'gif']),
            'is_pdf'       => $doc->file_type === 'pdf',
        ];
    }

    /**
     * Kiểm tra người dùng có quyền xóa tệp không.
     */
    private function canDelete(Document $doc, $user): bool
    {
        if ($user->isDirector()) {
            return $this->canView($doc, $user);
        }

        if ($user->isLeader()) {
            return true;
        }

        return $doc->user_id === $user->id;
    }

    private function canView(Document $doc, ?User $user): bool
    {
        if (!$user) {
            return false;
        }

        if ((int) $doc->user_id === (int) $user->id) {
            return true;
        }

        if ($user->isDirector()) {
            return $doc->review_status === Document::STATUS_DIRECTOR_VISIBLE;
        }

        if ($user->isLeader()) {
            $doc->loadMissing(['task.assignee', 'uploader']);

            return (int) optional($doc->task?->assignee)->department_id === (int) $user->department_id
                || (int) optional($doc->uploader)->department_id === (int) $user->department_id
                || (int) optional($doc->task)->assigned_by === (int) $user->id
                || (int) optional($doc->task)->assigned_to === (int) $user->id;
        }

        return false;
    }

    private function initialStatus(): string
    {
        $user = Auth::user();

        return $user && $user->isEmployee()
            ? Document::STATUS_MANAGER_REVIEW
            : Document::STATUS_DIRECTOR_VISIBLE;
    }
}
