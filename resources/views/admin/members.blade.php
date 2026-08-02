@extends('admin.layouts.app')

@section('title', 'Tất cả nhân viên - Giám đốc')
@section('page_title', 'Tất cả nhân viên')

@section('content')
@php
    $visibleMembers = collect($usersList->items());
    $membersById = $visibleMembers->keyBy('db_id');
    $activeVisibleMembers = $visibleMembers->where('status', 'active')->count();
    $managerVisibleMembers = $visibleMembers->filter(fn ($member) => in_array($member['role'], ['Giám đốc', 'Trưởng phòng'], true))->count();
    $visibleTaskCount = $visibleMembers->sum('tasks');
@endphp

<style>
    .people-page { display: grid; gap: 20px; }
    .people-hero { position: relative; overflow: hidden; display: grid; grid-template-columns: 1fr auto; gap: 18px; align-items: end; padding: 26px; border: 1px solid #B9CDF5; border-radius: 8px; background: linear-gradient(135deg, #F8FBFF 0%, #EEF5FF 62%, #FFF5F7 100%); box-shadow: inset 5px 0 0 #E4002B; }
    .people-hero:after { content: ""; position: absolute; right: -44px; top: -60px; width: 210px; height: 210px; border: 34px solid rgba(0,61,165,.06); border-radius: 999px; }
    .people-kicker { color: #E4002B; font-size: 12px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
    .people-hero h2 { margin: 8px 0 8px; color: #001F5B; font-size: 34px; line-height: 1.08; font-weight: 900; letter-spacing: -.03em; }
    .people-hero p { color: #52637A; font-size: 15px; line-height: 1.6; max-width: 760px; }
    .hero-count { position: relative; z-index: 1; min-width: 180px; border: 1px solid #D4E0F7; border-radius: 8px; background: rgba(255,255,255,.86); padding: 16px; }
    .hero-count span { display: block; color: #64748B; font-size: 10px; font-weight: 900; letter-spacing: .1em; text-transform: uppercase; }
    .hero-count strong { display: block; margin-top: 8px; color: #003DA5; font-size: 34px; line-height: 1; font-weight: 900; }
    .people-toolbar { display: flex; align-items: center; justify-content: space-between; gap: 14px; flex-wrap: wrap; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .people-filter { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .filter-field { position: relative; }
    .filter-field i { position: absolute; left: 12px; top: 50%; transform: translateY(-50%); color: #8AA0BD; width: 16px; height: 16px; }
    .people-input, .people-select { height: 42px; border: 1px solid #D4E0F7; border-radius: 8px; background: #F8FAFF; color: #001F5B; font-size: 13px; outline: none; }
    .people-input { width: 320px; padding: 0 12px 0 38px; }
    .people-select { padding: 0 34px 0 12px; }
    .people-input:focus, .people-select:focus { border-color: #003DA5; background: #fff; box-shadow: 0 0 0 3px rgba(0,61,165,.1); }
    .filter-btn { height: 42px; border: 0; border-radius: 8px; padding: 0 16px; background: #003DA5; color: #fff; font-size: 13px; font-weight: 900; cursor: pointer; }
    .clear-link { color: #E4002B; font-size: 13px; font-weight: 900; text-decoration: none; }
    .people-stats { display: grid; grid-template-columns: repeat(4, minmax(0, 1fr)); gap: 14px; }
    .people-stat { border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; padding: 16px; }
    .people-stat span { display: block; color: #64748B; font-size: 10px; font-weight: 900; letter-spacing: .08em; text-transform: uppercase; }
    .people-stat strong { display: block; margin-top: 9px; color: #001F5B; font-size: 28px; line-height: 1; font-weight: 900; }
    .people-board { overflow: hidden; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; box-shadow: 0 16px 34px rgba(0,31,91,.06); }
    .board-head { display: flex; justify-content: space-between; align-items: center; gap: 12px; padding: 16px 18px; border-bottom: 1px solid #E5EDF8; }
    .board-title { display: flex; align-items: center; gap: 10px; color: #001F5B; font-size: 18px; font-weight: 900; }
    .people-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; padding: 18px; background: linear-gradient(180deg, #fff 0%, #F8FAFF 100%); }
    .person-card { position: relative; overflow: hidden; min-width: 0; border: 1px solid #D4E0F7; border-radius: 8px; background: #fff; transition: transform .16s ease, box-shadow .16s ease, border-color .16s ease; }
    .person-card:before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 5px; background: #E4002B; }
    .person-card:hover { transform: translateY(-2px); border-color: #B9CDF5; box-shadow: 0 14px 30px rgba(0,31,91,.08); }
    .person-main { display: flex; align-items: flex-start; gap: 12px; padding: 16px 16px 14px 20px; }
    .person-avatar { width: 52px; height: 52px; flex: 0 0 52px; display: grid; place-items: center; border-radius: 8px; background: #003DA5; color: #fff; font-size: 15px; font-weight: 900; box-shadow: inset 5px 0 0 #E4002B; }
    .person-name { color: #001F5B; font-size: 15px; line-height: 1.25; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-email { margin-top: 4px; color: #64748B; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-code { margin-top: 8px; color: #94A3B8; font-size: 11px; font-family: ui-monospace, SFMono-Regular, Menlo, monospace; font-weight: 900; }
    .person-meta { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; padding: 0 16px 16px 20px; }
    .meta-box { min-width: 0; border: 1px solid #EEF2F7; border-radius: 8px; background: #F8FAFF; padding: 10px; }
    .meta-box span { color: #7B8BA5; font-size: 10px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
    .meta-box strong { display: block; margin-top: 5px; color: #001F5B; font-size: 12px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .person-foot { display: flex; align-items: center; justify-content: space-between; gap: 10px; padding: 12px 16px 14px 20px; border-top: 1px solid #EEF2F7; background: #FBFDFF; }
    .role-pill, .status-pill { display: inline-flex; align-items: center; gap: 6px; min-height: 28px; border-radius: 999px; padding: 0 10px; font-size: 12px; font-weight: 900; }
    .role-pill.admin { background: #001F5B; color: #fff; }
    .role-pill.manager { background: #003DA5; color: #fff; }
    .role-pill.employee { border: 1px solid #E2E8F0; background: #F1F5F9; color: #334155; }
    .status-pill.active { background: #ECFDF5; color: #15803D; }
    .status-pill.inactive { background: #F1F5F9; color: #64748B; }
    .empty-state { grid-column: 1 / -1; padding: 46px 20px; border: 1px dashed #B9CDF5; border-radius: 8px; background: #F6FAFF; color: #64748B; text-align: center; font-weight: 800; }
    .people-footer { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 14px 18px; border-top: 1px solid #E5EDF8; background: #fff; color: #64748B; font-size: 13px; }
    .pagination-wrap :where(nav) { display: flex; justify-content: flex-end; }
    @media (max-width: 1280px) { .people-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 920px) { .people-hero { grid-template-columns: 1fr; } .people-stats { grid-template-columns: repeat(2, minmax(0,1fr)); } .people-input { width: 100%; } .people-filter, .filter-field { width: 100%; } .people-select, .filter-btn { width: 100%; } }
    @media (max-width: 620px) { .people-grid { grid-template-columns: 1fr; } .people-stats { grid-template-columns: 1fr; } .people-footer { align-items: stretch; flex-direction: column; } }
</style>

<div class="people-page">
    @if(session('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm font-bold">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="p-4 rounded-xl bg-red-50 border border-red-200 text-red-700 text-sm font-bold">
            {{ session('error') }}
        </div>
    @endif

    <section class="people-hero">
        <div>
            <div class="people-kicker">People Directory</div>
            <h2>Toàn bộ nhân viên MobiFone</h2>
            <p>Xem nhanh nhân sự theo phòng ban, chức vụ, trạng thái tài khoản và số công việc đang được giao trên toàn WorkHub.</p>
        </div>
        <div class="hero-count">
            <span>Tổng nhân viên</span>
            <strong>{{ $usersList->total() }}</strong>
        </div>
    </section>

    <section class="people-toolbar">
        <form method="GET" action="{{ route('admin.members') }}" class="people-filter">
            <div class="filter-field">
                <i data-lucide="search"></i>
                <input name="search" value="{{ $search }}" class="people-input" placeholder="Tìm tên, email hoặc mã NV..." />
            </div>
            <select name="dept" onchange="this.form.submit()" class="people-select">
                <option value="">Tất cả phòng ban</option>
                @foreach($departments as $d)
                    <option value="{{ $d->TENPHONG }}" {{ $deptFilter == $d->TENPHONG ? 'selected' : '' }}>{{ $d->TENPHONG }}</option>
                @endforeach
            </select>
            <button type="submit" class="filter-btn">Lọc</button>
            @if(!empty($search) || !empty($deptFilter))
                <a href="{{ route('admin.members') }}" class="clear-link">Xóa lọc</a>
            @endif
        </form>
        <button type="button" id="open-member-panel" class="filter-btn inline-flex items-center gap-2">
            <i data-lucide="user-plus" style="width:16px;height:16px"></i>
            Thêm nhân viên mới
        </button>
    </section>

    <section class="people-stats">
        <div class="people-stat"><span>Đang hiển thị</span><strong>{{ $visibleMembers->count() }}</strong></div>
        <div class="people-stat"><span>Hoạt động</span><strong>{{ $activeVisibleMembers }}</strong></div>
        <div class="people-stat"><span>Quản lý</span><strong>{{ $managerVisibleMembers }}</strong></div>
        <div class="people-stat"><span>Việc được giao</span><strong>{{ $visibleTaskCount }}</strong></div>
    </section>

    <section class="people-board">
        <div class="board-head">
            <div class="board-title"><i data-lucide="users" style="width:22px;height:22px"></i>Danh sách nhân viên</div>
            <span class="status-pill active">{{ $usersList->firstItem() ?? 0 }}-{{ $usersList->lastItem() ?? 0 }} / {{ $usersList->total() }}</span>
        </div>

        <div class="people-grid">
            @forelse($usersList as $member)
                @php
                    $roleClass = match($member['role_id']) {
                        \App\Models\User::ROLE_ADMIN => 'admin',
                        \App\Models\User::ROLE_MANAGER => 'manager',
                        default => 'employee',
                    };
                @endphp
                <article class="person-card">
                    <div class="person-main">
                        <div class="person-avatar">{{ $member['avatar'] }}</div>
                        <div style="min-width:0" class="flex-1">
                            <div class="person-name">{{ $member['name'] }}</div>
                            <div class="person-email">{{ $member['email'] }}</div>
                            <div class="person-code">{{ $member['id'] }}</div>
                        </div>
                    </div>
                    <div class="person-meta">
                        <div class="meta-box"><span>Phòng ban</span><strong>{{ $member['dept'] }}</strong></div>
                        <div class="meta-box"><span>Công việc</span><strong>{{ $member['tasks'] }} việc</strong></div>
                        <div class="meta-box"><span>Ngày tham gia</span><strong>{{ $member['joined'] }}</strong></div>
                        <div class="meta-box"><span>Trạng thái</span><strong>{{ $member['status'] === 'active' ? 'Hoạt động' : 'Tạm khóa' }}</strong></div>
                    </div>
                    <div class="person-foot">
                        <span class="role-pill {{ $roleClass }}">{{ $member['role'] }}</span>
                        <div class="flex items-center gap-1.5">
                            <button type="button"
                                    data-member-id="{{ $member['db_id'] }}"
                                    class="edit-member-btn w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50 text-[#003DA5] border border-gray-200"
                                    title="Chỉnh sửa (Chuyển phòng ban)"
                                    aria-label="Chỉnh sửa {{ $member['name'] }}">
                                <i data-lucide="edit-3" class="w-4 h-4"></i>
                            </button>
                            <button type="button"
                                    data-member-id="{{ $member['db_id'] }}"
                                    class="role-member-btn w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50 text-[#003DA5] border border-gray-200"
                                    title="Phân quyền"
                                    aria-label="Phân quyền {{ $member['name'] }}">
                                <i data-lucide="shield" class="w-4 h-4"></i>
                            </button>
                            <form method="POST"
                                  action="{{ route('dashboard.members.status', $member['db_id']) }}"
                                  onsubmit="return confirm('{{ $member['status'] == 'active' ? 'Vô hiệu hóa tài khoản '.$member['name'].'?' : 'Kích hoạt lại tài khoản '.$member['name'].'?' }}')">
                                @csrf
                                @method('PATCH')
                                <button type="submit"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center {{ $member['status'] == 'active' ? 'hover:bg-amber-50 text-amber-600' : 'hover:bg-green-50 text-green-600' }} border border-gray-200"
                                        title="{{ $member['status'] == 'active' ? 'Vô hiệu hóa' : 'Kích hoạt lại' }}"
                                        aria-label="{{ $member['status'] == 'active' ? 'Vô hiệu hóa '.$member['name'] : 'Kích hoạt lại '.$member['name'] }}">
                                    <i data-lucide="{{ $member['status'] == 'active' ? 'alert-circle' : 'rotate-ccw' }}" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <form method="POST"
                                  action="{{ route('dashboard.members.delete', $member['db_id']) }}"
                                  onsubmit="return confirm('Bạn có chắc chắn muốn XÓA vĩnh viễn nhân viên {{ addslashes($member['name']) }} khỏi hệ thống không?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-50 text-red-600 border border-gray-200"
                                        title="Xóa nhân viên"
                                        aria-label="Xóa {{ $member['name'] }}">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                </article>
            @empty
                <div class="empty-state">
                    <i data-lucide="user-x" style="width:34px;height:34px"></i>
                    <div style="margin-top:10px">Không tìm thấy nhân viên phù hợp.</div>
                </div>
            @endforelse
        </div>

        <div class="people-footer">
            <span>Hiển thị {{ $usersList->firstItem() ?? 0 }}-{{ $usersList->lastItem() ?? 0 }} / {{ $usersList->total() }} nhân viên</span>
            <div class="pagination-wrap">{{ $usersList->links() }}</div>
        </div>
    </section>
</div>

<div id="member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between" id="panel-content">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <h2 class="text-lg font-bold text-[#001F5B]">Thêm thành viên mới</h2>
            <button id="close-member-panel" type="button" aria-label="Đóng"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form action="{{ route('dashboard.members.save') }}" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Họ và tên *</label>
                <input name="name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="Nhập họ và tên..." />
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email công ty *</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="vi_du@mobifone.vn" />
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Mật khẩu khởi tạo *</label>
                <div class="relative">
                    <input type="password" name="password" required class="password-eye-input w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="••••••••" />
                    <button type="button" class="password-eye-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#003DA5] focus:outline-none focus:ring-2 focus:ring-[#003DA5]/25 rounded-lg" aria-label="Hiện mật khẩu" title="Hiện mật khẩu">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Phòng ban *</label>
                <select name="department_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                    <option value="">— Chọn phòng ban —</option>
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->TENPHONG }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Vai trò hệ thống *</label>
                @foreach($roles as $r)
                    @php
                        $desc = match($r->name) {
                            'Phụ trách chi nhánh' => 'Toàn quyền điều hành hệ thống chi nhánh',
                            'Phó giám đốc chi nhánh' => 'Hỗ trợ điều hành và quản lý chi nhánh',
                            'Nhân viên' => 'Nhận và cập nhật task được giao',
                            'Giám đốc trung tâm kinh doanh' => 'Quản lý và giao việc cho trung tâm kinh doanh',
                            'Phó giám đốc trung tâm kinh doanh' => 'Hỗ trợ quản lý trung tâm kinh doanh',
                            'Phụ trách phòng viễn thông' => 'Quản lý và giao việc cho phòng viễn thông',
                            'Phụ trách phòng tổng hợp' => 'Quản lý và giao việc cho phòng tổng hợp',
                            default => 'Nhận và cập nhật task được giao',
                        };
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 mb-2 cursor-pointer hover:bg-gray-50/50">
                        <input type="radio" name="role_id" value="{{ $r->id }}" class="mt-1 text-[#003DA5] focus:ring-[#003DA5]" required {{ $r->name == 'Nhân viên' ? 'checked' : '' }} />
                        <div>
                            <div class="text-sm font-semibold text-gray-700">{{ $r->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $desc }}</div>
                        </div>
                    </label>
                @endforeach
            </div>

            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Tạo tài khoản mới</button>
            </div>
        </form>
    </div>
</div>

<div id="edit-member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#001F5B]">Chỉnh sửa thành viên</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="edit-member-subtitle"></p>
            </div>
            <button type="button" id="close-edit-member-panel" aria-label="Đóng"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form id="edit-member-form" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Họ và tên *</label>
                <input id="edit_member_name" name="name" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Email công ty *</label>
                <input id="edit_member_email" type="email" name="email" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" />
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Mật khẩu mới</label>
                <div class="relative">
                    <input type="password" name="password" class="password-eye-input w-full px-4 py-2.5 pr-11 border border-gray-200 rounded-xl text-sm outline-none focus:border-[#003DA5]" placeholder="Bỏ trống nếu không đổi" />
                    <button type="button" class="password-eye-toggle absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-[#003DA5] focus:outline-none focus:ring-2 focus:ring-[#003DA5]/25 rounded-lg" aria-label="Hiện mật khẩu" title="Hiện mật khẩu">
                        <i data-lucide="eye" class="w-4 h-4"></i>
                    </button>
                </div>
            </div>
            <div>
                <label class="block text-sm font-semibold mb-1.5 text-gray-700">Phòng ban *</label>
                <select id="edit_member_department" name="department_id" required class="w-full px-4 py-2.5 border border-gray-200 rounded-xl text-sm outline-none bg-white focus:border-[#003DA5]">
                    @foreach($departments as $d)
                        <option value="{{ $d->id }}">{{ $d->TENPHONG }}</option>
                    @endforeach
                </select>
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Lưu thay đổi</button>
            </div>
        </form>
    </div>
</div>

<div id="role-member-panel" class="fixed inset-0 z-50 flex justify-end bg-black/30 hidden">
    <div class="w-96 bg-white h-full shadow-2xl flex flex-col justify-between">
        <div class="px-6 py-5 border-b border-gray-200 flex items-center justify-between">
            <div>
                <h2 class="text-lg font-bold text-[#001F5B]">Phân quyền thành viên</h2>
                <p class="text-xs text-gray-400 mt-0.5" id="role-member-subtitle"></p>
            </div>
            <button type="button" id="close-role-member-panel" aria-label="Đóng"><i data-lucide="x" class="w-5 h-5 text-gray-400"></i></button>
        </div>

        <form id="role-member-form" method="POST" class="p-6 space-y-4 overflow-y-auto flex-1 custom-scrollbar">
            @csrf
            @method('PATCH')
            <div>
                <label class="block text-sm font-semibold mb-2 text-gray-700">Vai trò hệ thống</label>
                @foreach($roles as $r)
                    @php
                        $desc = match($r->name) {
                            'Phụ trách chi nhánh' => 'Toàn quyền điều hành hệ thống chi nhánh',
                            'Phó giám đốc chi nhánh' => 'Hỗ trợ điều hành và quản lý chi nhánh',
                            'Nhân viên' => 'Nhận và cập nhật task được giao',
                            'Giám đốc trung tâm kinh doanh' => 'Quản lý và giao việc cho trung tâm kinh doanh',
                            'Phó giám đốc trung tâm kinh doanh' => 'Hỗ trợ quản lý trung tâm kinh doanh',
                            'Phụ trách phòng viễn thông' => 'Quản lý và giao việc cho phòng viễn thông',
                            'Phụ trách phòng tổng hợp' => 'Quản lý và giao việc cho phòng tổng hợp',
                            default => 'Nhận và cập nhật task được giao',
                        };
                    @endphp
                    <label class="flex items-start gap-3 p-3 rounded-xl border border-gray-200 mb-2 cursor-pointer hover:bg-gray-50/50">
                        <input type="radio" name="role_id" value="{{ $r->id }}" class="role-radio mt-1 text-[#003DA5] focus:ring-[#003DA5]" required />
                        <div>
                            <div class="text-sm font-semibold text-gray-700">{{ $r->name }}</div>
                            <div class="text-xs text-gray-400 mt-0.5">{{ $desc }}</div>
                        </div>
                    </label>
                @endforeach
            </div>
            <div class="rounded-xl bg-[#E8F0FE] p-4 text-xs text-[#001F5B] leading-relaxed">
                Thay đổi vai trò sẽ ảnh hưởng đến menu và phạm vi thao tác của thành viên ở lần truy cập tiếp theo.
            </div>
            <div class="pt-4">
                <button type="submit" class="w-full py-3 rounded-xl text-sm font-bold text-white bg-[#003DA5] hover:bg-[#0057C8] transition-all">Cập nhật vai trò</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const membersById = @json($membersById);
        const membersBaseUrl = @json(url('/dashboard/members'));
        const memberPanel = document.getElementById('member-panel');
        const openMemberBtn = document.getElementById('open-member-panel');
        const closeMemberBtn = document.getElementById('close-member-panel');
        const editPanel = document.getElementById('edit-member-panel');
        const rolePanel = document.getElementById('role-member-panel');
        const editForm = document.getElementById('edit-member-form');
        const roleForm = document.getElementById('role-member-form');

        const toggleMemberPanel = () => memberPanel.classList.toggle('hidden');
        const closeEditPanel = () => editPanel.classList.add('hidden');
        const closeRolePanel = () => rolePanel.classList.add('hidden');

        openMemberBtn?.addEventListener('click', toggleMemberPanel);
        closeMemberBtn?.addEventListener('click', toggleMemberPanel);
        document.getElementById('close-edit-member-panel')?.addEventListener('click', closeEditPanel);
        document.getElementById('close-role-member-panel')?.addEventListener('click', closeRolePanel);

        document.querySelectorAll('.password-eye-toggle').forEach((button) => {
            button.addEventListener('click', () => {
                const input = button.parentElement.querySelector('.password-eye-input');
                const icon = button.querySelector('i');
                const shouldShow = input.type === 'password';

                input.type = shouldShow ? 'text' : 'password';
                icon.setAttribute('data-lucide', shouldShow ? 'eye-off' : 'eye');
                button.setAttribute('aria-label', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                button.setAttribute('title', shouldShow ? 'Ẩn mật khẩu' : 'Hiện mật khẩu');
                lucide.createIcons();
            });
        });

        memberPanel?.addEventListener('click', (e) => {
            if (e.target === memberPanel) toggleMemberPanel();
        });
        editPanel?.addEventListener('click', (e) => {
            if (e.target === editPanel) closeEditPanel();
        });
        rolePanel?.addEventListener('click', (e) => {
            if (e.target === rolePanel) closeRolePanel();
        });

        document.querySelectorAll('.edit-member-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const member = membersById[button.dataset.memberId];
                if (!member) return;

                editForm.action = `${membersBaseUrl}/${member.db_id}`;
                document.getElementById('edit-member-subtitle').innerText = member.id + ' - ' + member.email;
                document.getElementById('edit_member_name').value = member.name;
                document.getElementById('edit_member_email').value = member.email;
                document.getElementById('edit_member_department').value = member.department_id;
                editPanel.classList.remove('hidden');
            });
        });

        document.querySelectorAll('.role-member-btn').forEach((button) => {
            button.addEventListener('click', () => {
                const member = membersById[button.dataset.memberId];
                if (!member) return;

                roleForm.action = `${membersBaseUrl}/${member.db_id}/role`;
                document.getElementById('role-member-subtitle').innerText = member.name + ' - ' + member.email;
                document.querySelectorAll('.role-radio').forEach((input) => {
                    input.checked = Number(input.value) === Number(member.role_id);
                });
                rolePanel.classList.remove('hidden');
            });
        });
    });
</script>
@endsection

