<?php

namespace App\Http\Controllers;

use App\Models\HrDocument;
use App\Models\User; // Hoặc Profile tùy thuộc vào Model bạn dùng kết nối DB
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NhanVienController extends Controller
{
    public function hoso()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(8);
        $allUsers = User::orderBy('name')->get();
        $totalUsers = User::count();
        $totalDocuments = HrDocument::count();
        $documents = HrDocument::with('user')->latest()->take(8)->get();

        return view('admin.layouts.nhanvien.hoso', compact('users', 'allUsers', 'totalUsers', 'totalDocuments', 'documents'));
    }

    public function uploadHoso(Request $request)
    {
        $request->validate([
            'user_id' => 'nullable|exists:users,id',
            'profile_file' => 'required|file|max:10240|mimes:pdf,doc,docx,xls,xlsx,png,jpg,jpeg',
        ], [
            'profile_file.required' => 'Vui lòng chọn file trước khi lưu.',
            'profile_file.file' => 'Tệp tải lên không hợp lệ.',
            'profile_file.max' => 'File không được vượt quá 10MB.',
            'profile_file.mimes' => 'Chỉ hỗ trợ PDF, DOC, DOCX, XLS, XLSX, PNG, JPG, JPEG.',
        ]);

        $file = $request->file('profile_file');
        $baseName = Str::slug(pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME));
        $extension = $file->getClientOriginalExtension();
        $fileName = now()->format('YmdHis') . '-' . ($baseName ?: 'ho-so') . '.' . $extension;

        $filePath = $file->storeAs('hr-documents', $fileName, 'public');

        HrDocument::create([
            'user_id' => $request->input('user_id'),
            'original_name' => $file->getClientOriginalName(),
            'stored_name' => $fileName,
            'file_path' => $filePath,
            'disk' => 'public',
            'file_type' => strtoupper($extension),
            'mime_type' => $file->getClientMimeType(),
            'file_size' => $file->getSize(),
        ]);

        return redirect()
            ->route('nhanvien.hoso')
            ->with('thongbao', 'Lưu file hồ sơ thành công: ' . $file->getClientOriginalName());
    }

    public function downloadHoso($filename)
    {
        $document = HrDocument::where('stored_name', basename($filename))->first();
        $path = $document ? $document->file_path : 'hr-documents/' . basename($filename);
        $disk = $document ? $document->disk : 'public';

        if (!Storage::disk($disk)->exists($path)) {
            abort(404);
        }

        return Storage::disk($disk)->download($path, $document ? $document->original_name : null);
    }

    /**
     * Hiển thị danh sách nhân viên kèm tìm kiếm và phân trang
     */
    public function index(Request $request)
    {
        // 1. Lấy từ khóa tìm kiếm từ ô input (name="keyword")
        $keyword = $request->input('keyword');

        // 2. Khởi tạo câu lệnh truy vấn dữ liệu từ bảng users
        $query = User::query();

        // 3. Nếu có gõ từ khóa, tiến hành lọc theo Tên hoặc Email nhân viên
        if (!empty($keyword)) {
            $query->where(function($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        // 4. Sắp xếp nhân viên mới tạo lên đầu và phân trang 10 người/trang
        $users = $query->orderBy('created_at', 'desc')->paginate(10);

        // 5. Trả dữ liệu ra file Blade danhsach bạn vừa làm
        return view('admin.layouts.nhanvien.danhsach', compact('users'));
    }

    /**
     * Các hàm tạm thời để chạy không bị lỗi 404 khi bấm nút
     */
    public function create() {
        $departments = \App\Models\Department::all();
        return view('admin.layouts.nhanvien.them', compact('departments'));
    }

    public function store(Request $request) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'department_id' => $request->department_id,
        ]);

        return redirect('admin/nhanvien/danhsach')->with('thongbao', 'Thêm nhân viên thành công!');
    }

    public function edit($id) {
        $user = User::findOrFail($id);
        $departments = \App\Models\Department::all();
        return view('admin.layouts.nhanvien.sua', compact('user', 'departments'));
    }

    public function update(Request $request, $id) {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,'.$id,
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->department_id = $request->department_id;
        $user->save();

        return redirect('admin/nhanvien/danhsach')->with('thongbao', 'Cập nhật nhân viên thành công!');
    }

    public function thongke() {
        $thongke = \DB::table('users')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->select('departments.TENPHONG', \DB::raw('count(users.id) as total'))
            ->groupBy('departments.TENPHONG')
            ->get()
            ->map(function($item) {
                $tk = new \stdClass();
                $tk->phongban = new \stdClass();
                $tk->phongban->TENPHONG = $item->TENPHONG ?? 'Chưa có phòng ban';
                $tk->total = $item->total;
                return $tk;
            });

        return view('admin.layouts.nhanvien.thongke', compact('thongke'));
    }

    public function destroy($id) {
        // Tạm thời xử lý xóa nhanh quay lại kèm thông báo
        User::destroy($id);
        return redirect()->back()->with('thongbao', 'Xóa nhân viên thành công!');
    }
}
