@extends('admin.layouts.app')

@section('title', 'Phòng ban - Giám đốc')
@section('page_title', 'Phòng ban')

@section('head_extra')
<style>
    .pb-hero { background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; padding: 20px; display: flex; justify-content: space-between; align-items: center; gap: 18px; margin-bottom: 18px; }
    .pb-kicker { color: #E4002B; font-size: 11px; font-weight: 900; letter-spacing: .16em; text-transform: uppercase; }
    .pb-title { color: #111827; font-size: 24px; font-weight: 900; margin-top: 6px; }
    .pb-desc { color: #6B7280; font-size: 13px; line-height: 1.6; margin-top: 6px; }
    .pb-panel { background: #fff; border: 1px solid #E5E7EB; border-radius: 8px; overflow: hidden; }
    .pb-panel-head { min-height: 54px; padding: 12px 16px; border-bottom: 1px solid #E5E7EB; display: flex; align-items: center; gap: 8px; font-size: 14px; font-weight: 900; color: #111827; }
    .pb-table { width: 100%; border-collapse: collapse; font-size: 13px; }
    .pb-table th { background: #F9FAFB; color: #6B7280; text-align: left; padding: 12px 14px; font-size: 10px; text-transform: uppercase; letter-spacing: .08em; }
    .pb-table td { padding: 14px; border-top: 1px solid #F3F4F6; vertical-align: middle; color: #374151; }
    .pb-code { font-family: ui-monospace, SFMono-Regular, Consolas, monospace; color: #9CA3AF; font-size: 12px; }
    .pb-badge { display: inline-flex; align-items: center; min-height: 22px; border-radius: 999px; padding: 2px 9px; font-size: 11px; font-weight: 900; background: #F3F4F6; color: #4B5563; }
    .pb-btn { min-height: 36px; border-radius: 8px; border: 1px solid transparent; display: inline-flex; align-items: center; justify-content: center; gap: 6px; padding: 0 12px; text-decoration: none; font-family: inherit; font-size: 13px; font-weight: 800; cursor: pointer; }
    .pb-btn-primary { background: #E4002B; color: #fff; border-color: #E4002B; }
    .pb-btn-secondary { background: #fff; border-color: #D1D5DB; color: #374151; }
    .pb-btn-danger { background: #FEF2F2; border-color: #FECACA; color: #B91C1C; }
    .pb-empty { padding: 28px; color: #9CA3AF; text-align: center; }
    .pb-footer { padding: 14px 16px; border-top: 1px solid #E5E7EB; }
</style>
@endsection

@section('content')
<div class="space-y-5">
    <div class="pb-hero">
        <div>
            <div class="pb-kicker">Department directory</div>
            <h1 class="pb-title">Cấu trúc phòng ban</h1>
            <p class="pb-desc">Quản lý danh mục phòng ban để phân quyền, lọc nhân sự, giao việc trong phòng và tổng hợp báo cáo.</p>
        </div>
        <a href="{{ route('phongban.them') }}" class="pb-btn pb-btn-primary"><i data-lucide="plus"></i> Thêm phòng ban</a>
    </div>

    <div class="pb-panel">
        <div class="pb-panel-head"><i data-lucide="building-2"></i> Danh sách phòng ban</div>
        <div style="overflow-x:auto">
            <table class="pb-table">
                <thead><tr><th>Mã</th><th>Tên phòng</th><th>Nhân sự</th><th>Thao tác</th></tr></thead>
                <tbody>
                    @forelse($departments as $department)
                        <tr>
                            <td class="pb-code">PB-{{ str_pad($department->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td><strong style="color:#111827">{{ $department->TENPHONG ?? $department->name }}</strong></td>
                            <td><span class="pb-badge">{{ $department->users_count }} người</span></td>
                            <td>
                                <div style="display:flex;gap:8px;flex-wrap:wrap">
                                    <a href="{{ route('phongban.sua', $department->id) }}" class="pb-btn pb-btn-secondary">Sửa</a>
                                    <form method="POST" action="{{ route('phongban.xoa', $department->id) }}" onsubmit="return confirm('Xóa phòng ban này? Nhân sự trong phòng sẽ được bỏ gán phòng.')">
                                        @csrf @method('DELETE')
                                        <button class="pb-btn pb-btn-danger" type="submit">Xóa</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="pb-empty">Chưa có phòng ban.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pb-footer">{{ $departments->links() }}</div>
    </div>
</div>
@endsection
