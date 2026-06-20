<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TruongPhongController extends Controller
{
    /**
     * Danh sách trưởng phòng
     */
    public function index(Request $request)
    {
        $keyword = $request->input('keyword');

        $query = User::where('role', 'truongphong');

        if (!empty($keyword)) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'LIKE', "%{$keyword}%")
                  ->orWhere('email', 'LIKE', "%{$keyword}%");
            });
        }

        $truongphongs = $query->orderBy('created_at', 'desc')
                              ->paginate(10);

        return view('admin.layouts.truongphong.danhsach', compact('truongphongs'));
    }

    /**
     * Form thêm trưởng phòng
     */
    public function create()
    {
        $departments = Department::all();

        return view('admin.layouts.truongphong.them', compact('departments'));
    }

    /**
     * Lưu trưởng phòng
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users',
            'password'      => 'required|string|min:6',
            'department_id' => 'nullable|exists:departments,id',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => bcrypt($request->password),
            'department_id' => $request->department_id,
            'role'          => 'truongphong',
        ]);

        return redirect()
            ->route('truongphong.index')
            ->with('thongbao', 'Thêm trưởng phòng thành công!');
    }

    /**
     * Form sửa trưởng phòng
     */
    public function edit($id)
    {
        $truongphong = User::where('role', 'truongphong')
                           ->findOrFail($id);

        $departments = Department::all();

        return view('admin.layouts.truongphong.sua', compact('truongphong', 'departments'));
    }

    /**
     * Cập nhật trưởng phòng
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|string|email|max:255|unique:users,email,' . $id,
            'department_id' => 'nullable|exists:departments,id',
        ]);

        $truongphong = User::findOrFail($id);

        $truongphong->name = $request->name;
        $truongphong->email = $request->email;
        $truongphong->department_id = $request->department_id;

        if ($request->filled('password')) {
            $truongphong->password = bcrypt($request->password);
        }

        $truongphong->save();

        return redirect()
            ->route('truongphong.index')
            ->with('thongbao', 'Cập nhật trưởng phòng thành công!');
    }

    /**
     * Xóa trưởng phòng
     */
    public function destroy($id)
    {
        $truongphong = User::where('role', 'truongphong')
                           ->findOrFail($id);

        $truongphong->delete();

        return redirect()->back()
            ->with('thongbao', 'Xóa trưởng phòng thành công!');
    }

    /**
     * Thống kê trưởng phòng theo phòng ban
     */
    public function thongke()
    {
        $thongke = DB::table('users')
            ->leftJoin('departments', 'users.department_id', '=', 'departments.id')
            ->where('users.role', 'truongphong')
            ->select(
                'departments.TENPHONG',
                DB::raw('count(users.id) as total')
            )
            ->groupBy('departments.TENPHONG')
            ->get()
            ->map(function ($item) {
                $tk = new \stdClass();
                $tk->phongban = new \stdClass();
                $tk->phongban->TENPHONG = $item->TENPHONG ?? 'Chưa có phòng ban';
                $tk->total = $item->total;
                return $tk;
            });

        return view('admin.layouts.truongphong.thongke', compact('thongke'));
    }
}