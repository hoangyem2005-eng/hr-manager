<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class PhoGiamDocController extends Controller
{
    public function index(Request $request)
    {
        $keyword = $request->keyword;

        $query = User::where('role', 'phogiamdoc');

        if ($keyword) {
            $query->where(function ($q) use ($keyword) {
                $q->where('name', 'like', "%$keyword%")
                  ->orWhere('email', 'like', "%$keyword%");
            });
        }

        $phogiamdocs = $query->latest()->paginate(10);

        return view('admin.layouts.phogiamdoc.danhsach', compact('phogiamdocs'));
    }

    public function create()
    {
        return view('admin.layouts.phogiamdoc.them');
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
            'role' => 'phogiamdoc',
        ]);

        return redirect()->route('phogiamdoc.index')
            ->with('thongbao', 'Thêm phó giám đốc thành công');
    }

    public function edit($id)
    {
        $phogiamdoc = User::findOrFail($id);

        return view('admin.layouts.phogiamdoc.sua', compact('phogiamdoc'));
    }

    public function update(Request $request, $id)
    {
        $phogiamdoc = User::findOrFail($id);

        $phogiamdoc->name = $request->name;
        $phogiamdoc->email = $request->email;

        if ($request->password) {
            $phogiamdoc->password = bcrypt($request->password);
        }

        $phogiamdoc->save();

        return redirect()->route('phogiamdoc.index')
            ->with('thongbao', 'Cập nhật thành công');
    }

    public function destroy($id)
    {
        User::destroy($id);

        return back()->with('thongbao', 'Xóa thành công');
    }
}