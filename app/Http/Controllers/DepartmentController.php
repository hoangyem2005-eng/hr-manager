<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DepartmentController extends Controller
{
    public function index(Request $request)
    {
        $departments = Department::query()
            ->withCount('users')
            ->orderBy('id')
            ->paginate(10);

        $selectedDepartment = null;
        if ($request->filled('department_id')) {
            $selectedDepartment = Department::with(['users.role'])
                ->withCount('users')
                ->find($request->integer('department_id'));
        }

        return view('admin.layouts.phongban.danhsach', compact('departments', 'selectedDepartment'));
    }

    public function create()
    {
        return view('admin.layouts.phongban.them');
    }

    public function store(Request $request)
    {
        $validated = $this->validateDepartment($request);

        Department::create($validated);

        return redirect()
            ->route('phongban.danhsach')
            ->with('thongbao', 'Thêm phòng ban thành công.');
    }

    public function edit($id)
    {
        $department = Department::findOrFail($id);

        return view('admin.layouts.phongban.sua', compact('department'));
    }

    public function update(Request $request, $id)
    {
        $department = Department::findOrFail($id);

        $validated = $this->validateDepartment($request, $department->id);
        $department->update($validated);

        return redirect()
            ->route('phongban.danhsach')
            ->with('thongbao', 'Cập nhật phòng ban thành công.');
    }

    public function destroy($id)
    {
        $department = Department::findOrFail($id);

        User::where('department_id', $department->id)->update(['department_id' => null]);
        $department->delete();
        $this->resetAutoIncrement('departments');

        return redirect()
            ->route('phongban.danhsach')
            ->with('thongbao', 'Xóa phòng ban thành công.');
    }

    private function validateDepartment(Request $request, ?int $ignoreId = null): array
    {
        $unique = 'unique:departments,TENPHONG';
        if ($ignoreId) {
            $unique .= ',' . $ignoreId;
        }

        return $request->validate([
            'TENPHONG' => 'required|string|max:255|' . $unique,
        ], [
            'TENPHONG.required' => 'Vui lòng nhập tên phòng ban.',
            'TENPHONG.unique' => 'Phòng ban này đã tồn tại.',
        ]);
    }

    private function resetAutoIncrement(string $table): void
    {
        $nextId = ((int) DB::table($table)->max('id')) + 1;
        DB::statement('ALTER TABLE ' . $table . ' AUTO_INCREMENT = ' . max(1, $nextId));
    }
}
