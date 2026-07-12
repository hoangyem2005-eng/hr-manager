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
    .pb-row-active { background: #F8FAFF; box-shadow: inset 3px 0 0 #E4002B; }
    .pb-link { color: #111827; text-decoration: none; }
    .pb-link:hover { color: #003DA5; text-decoration: underline; }
    .pb-selected-title { display: flex; align-items: center; justify-content: space-between; gap: 12px; flex-wrap: wrap; width: 100%; }
    .pb-people-shell { background: linear-gradient(180deg, #F6FAFF 0%, #FFFFFF 42%); padding: 18px; }
    .pb-people-summary { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 14px; flex-wrap: wrap; }
    .pb-summary-title { display: flex; align-items: center; gap: 10px; color: #001F5B; font-size: 13px; font-weight: 900; }
    .pb-summary-title i { color: #003DA5; }
    .pb-summary-pill { display: inline-flex; align-items: center; gap: 7px; min-height: 30px; padding: 0 11px; border-radius: 999px; background: #fff; border: 1px solid #D8E4F5; color: #003DA5; font-size: 12px; font-weight: 900; }
    .pb-people { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 14px; }
    .pb-person { position: relative; overflow: hidden; border: 1px solid #D8E4F5; border-radius: 8px; padding: 16px; background: #fff; min-width: 0; transition: transform .16s ease, border-color .16s ease, box-shadow .16s ease; }
    .pb-person::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 4px; background: #E4002B; opacity: .95; }
    .pb-person:hover { transform: translateY(-2px); border-color: #B9CDF5; box-shadow: 0 12px 26px rgba(0, 31, 91, .08); }
    .pb-person-top { display: flex; align-items: flex-start; gap: 12px; min-width: 0; }
    .pb-avatar { width: 46px; height: 46px; border-radius: 8px; background: #003DA5; color: #fff; display: flex; align-items: center; justify-content: center; font-size: 13px; font-weight: 900; flex-shrink: 0; box-shadow: inset 5px 0 0 #E4002B; }
    .pb-person-name { color: #001F5B; font-size: 15px; line-height: 1.25; font-weight: 900; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .pb-role-pill { display: inline-flex; align-items: center; min-height: 24px; padding: 0 9px; margin-top: 6px; border-radius: 999px; background: #E8F0FE; color: #003DA5; font-size: 11px; font-weight: 800; max-width: 100%; }
    .pb-mail { margin-top: 14px; display: flex; align-items: center; gap: 7px; color: #5F6F89; font-size: 12px; font-weight: 600; word-break: break-word; }
    .pb-mail i { width: 14px; height: 14px; color: #94A3B8; flex-shrink: 0; }
    .pb-empty-people { margin: 18px; padding: 34px 20px; border: 1px dashed #B9CDF5; border-radius: 8px; background: #F6FAFF; color: #64748B; text-align: center; }
    @media (max-width: 1100px) { .pb-people { grid-template-columns: repeat(2, minmax(0, 1fr)); } }
    @media (max-width: 640px) { .pb-hero { align-items: flex-start; flex-direction: column; } .pb-people { grid-template-columns: 1fr; } }
</style>
@endsection

@section('content')
<div class="space-y-5">
    <div class="pb-hero">
        <div>
            <div class="pb-kicker">Department directory</div>
            <h1 class="pb-title">Cấu trúc phòng ban</h1>
            <p class="pb-desc">Qu&#7843;n l&#253; danh m&#7909;c ph&#242;ng ban &#273;&#7875; ph&#226;n quy&#7873;n, l&#7885;c nh&#226;n s&#7921;, giao vi&#7879;c trong ph&#242;ng v&#224; t&#7893;ng h&#7907;p b&#225;o c&#225;o.</p>
        </div>
        <a href="{{ route('phongban.them') }}" class="pb-btn pb-btn-primary"><i data-lucide="plus"></i> Th&#234;m ph&#242;ng ban</a>
    </div>

    <div class="pb-panel">
        <div class="pb-panel-head"><i data-lucide="building-2"></i> Danh s&#225;ch ph&#242;ng ban</div>
        <div style="overflow-x:auto">
            <table class="pb-table">
                <thead><tr><th>M&#227;</th><th>T&#234;n ph&#242;ng</th><th>Nh&#226;n s&#7921;</th><th>Thao t&#225;c</th></tr></thead>
                <tbody>
                    @forelse($departments as $department)
                        @php $isSelected = optional($selectedDepartment)->id === $department->id; @endphp
                        <tr class="{{ $isSelected ? 'pb-row-active' : '' }}">
                            <td class="pb-code">PB-{{ str_pad($department->id, 3, '0', STR_PAD_LEFT) }}</td>
                            <td>
                                <a class="pb-link" href="{{ route('phongban.danhsach', ['department_id' => $department->id]) }}">
                                    <strong>{{ $department->TENPHONG ?? $department->name }}</strong>
                                </a>
                            </td>
                            <td><span class="pb-badge">{{ $department->users_count }} ng&#432;&#7901;i</span></td>
                            <td>
                                <div style="display:flex;gap:8px;flex-wrap:wrap">
                                    <a href="{{ route('phongban.danhsach', ['department_id' => $department->id]) }}" class="pb-btn pb-btn-secondary">
                                        <i data-lucide="users"></i> Nh&#226;n vi&#234;n
                                    </a>
                                    <a href="{{ route('phongban.sua', $department->id) }}" class="pb-btn pb-btn-secondary">S&#7917;a</a>
                                    <form method="POST" action="{{ route('phongban.xoa', $department->id) }}" onsubmit="return confirm('Xoa phong ban nay? Nhan su trong phong se duoc bo gan phong.')">
                                        @csrf @method('DELETE')
                                        <button class="pb-btn pb-btn-danger" type="submit">X&#243;a</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="4"><div class="pb-empty">Ch&#432;a c&#243; ph&#242;ng ban.</div></td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="pb-footer">{{ $departments->links() }}</div>
    </div>

    <div class="pb-panel">
        <div class="pb-panel-head">
            <div class="pb-selected-title">
                <span>
                    <i data-lucide="users"></i>
                    @if($selectedDepartment)
                        Nh&#226;n vi&#234;n ph&#242;ng {{ $selectedDepartment->TENPHONG ?? $selectedDepartment->name }}
                    @else
                        Nh&#226;n vi&#234;n theo ph&#242;ng ban
                    @endif
                </span>
                @if($selectedDepartment)
                    <a href="{{ route('phongban.danhsach') }}" class="pb-btn pb-btn-secondary">B&#7887; ch&#7885;n</a>
                @endif
            </div>
        </div>

        @if(!$selectedDepartment)
            <div class="pb-empty-people">
                <i data-lucide="mouse-pointer-click"></i>
                <div style="font-weight:900;color:#001F5B;margin-top:10px">Ch&#7885;n m&#7897;t ph&#242;ng ban</div>
                <div style="font-size:13px;margin-top:4px">Danh s&#225;ch nh&#226;n vi&#234;n s&#7869; hi&#7875;n th&#7883; t&#7841;i &#273;&#226;y.</div>
            </div>
        @elseif($selectedDepartment->users->isEmpty())
            <div class="pb-empty-people">
                <i data-lucide="user-round-x"></i>
                <div style="font-weight:900;color:#001F5B;margin-top:10px">Ch&#432;a c&#243; nh&#226;n vi&#234;n</div>
                <div style="font-size:13px;margin-top:4px">Ph&#242;ng ban n&#224;y ch&#432;a c&#243; nh&#226;n s&#7921; n&#224;o.</div>
            </div>
        @else
            <div class="pb-people-shell">
                <div class="pb-people-summary">
                    <div class="pb-summary-title">
                        <i data-lucide="sparkles"></i>
                        <span>{{ $selectedDepartment->TENPHONG ?? $selectedDepartment->name }}</span>
                    </div>
                    <span class="pb-summary-pill"><i data-lucide="users"></i>{{ $selectedDepartment->users->count() }} nh&#226;n vi&#234;n</span>
                </div>
                <div class="pb-people">
                    @foreach($selectedDepartment->users as $user)
                        @php
                            $nameParts = preg_split('/\s+/', trim($user->name ?? 'NV'));
                            $initials = count($nameParts) >= 2
                                ? mb_substr($nameParts[count($nameParts) - 2], 0, 1) . mb_substr($nameParts[count($nameParts) - 1], 0, 1)
                                : mb_substr($user->name ?? 'NV', 0, 2);
                        @endphp
                        <div class="pb-person">
                            <div class="pb-person-top">
                                <div class="pb-avatar">{{ mb_strtoupper($initials) }}</div>
                                <div style="min-width:0">
                                    <div class="pb-person-name">{{ $user->name }}</div>
                                    <div class="pb-role-pill">{{ $user->role->name ?? 'Chua co vai tro' }}</div>
                                </div>
                            </div>
                            <div class="pb-mail"><i data-lucide="mail"></i>{{ $user->email }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection
