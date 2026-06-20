<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class GiamDocController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $query = User::where('role', 'giamdoc');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%");
            });
        }

        $giamdocs = $query->latest()->paginate(10);

        return view('admin.layouts.giamdoc.danhsach', compact('giamdocs'));
    }

    public function create()
    {
        return view('admin.layouts.giamdoc.them');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'giamdoc',
        ]);

        return redirect()->route('giamdoc.index')
            ->with('thongbao', 'Thêm giám đốc thành công');
    }

    public function edit($id)
    {
        $giamdoc = User::findOrFail($id);

        return view('admin.layouts.giamdoc.sua', compact('giamdoc'));
    }

    public function update(Request $request, $id)
    {
        $giamdoc = User::findOrFail($id);

        $giamdoc->name = $request->name;
        $giamdoc->email = $request->email;

        if ($request->password) {
            $giamdoc->password = bcrypt($request->password);
        }

        $giamdoc->save();

        return redirect()->route('giamdoc.index')
            ->with('thongbao', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        User::destroy($id);

        return back()->with('thongbao', 'Xóa thành công');
    }
}