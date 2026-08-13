<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh sửa thông tin - MobiFone WorkHub</title>
    <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { min-height: 100vh; font-family: "Be Vietnam Pro", sans-serif; color: #001F5B; background: #F2F6FC; }
        .page { min-height: 100vh; padding: 34px; }
        .top { display: flex; align-items: center; justify-content: space-between; gap: 16px; margin-bottom: 22px; }
        .brand { display: flex; align-items: center; gap: 12px; }
        .mark { width: 46px; height: 46px; display: grid; place-items: center; border-radius: 10px; background: #fff; color: #003DA5; font-weight: 900; box-shadow: inset 6px 0 0 #E4002B; border: 1px solid #D4E0F7; }
        .word {
            position: relative;
            overflow: hidden;
            display: inline-flex;
            align-items: baseline;
            padding: 6px 13px;
            border-radius: 7px;
            background: #fff;
            line-height: 1;
            box-shadow: 0 14px 28px rgba(0,0,0,.12);
        }

        .word::after {
            content: "";
            position: absolute;
            inset: -45% auto -45% -55%;
            width: 42%;
            transform: rotate(18deg);
            background: linear-gradient(90deg, transparent, rgba(255,255,255,.9), transparent);
            animation: shine 4.4s ease-in-out infinite;
        }

        .word strong { 
            font-family: Arial, Helvetica, sans-serif !important;
            font-weight: 700 !important;
            letter-spacing: -0.05em !important;
            font-size: 25px; 
            line-height: 1;
        }
        
        .word .blue { color: #003DA5; }
        .word .red { color: #E4002B; }

        /* Custom styling for the official MobiFone logo (lowercase, red dot on 'i') */
        .brand-i {
            position: relative;
            display: inline-block;
            color: inherit;
            font-style: normal;
            line-height: inherit;
            margin-right: -0.06em !important;
        }

        .brand-i::after {
            content: "";
            position: absolute;
            bottom: 0.66em;
            left: 50%;
            transform: translateX(-50%);
            width: 0.15em;
            height: 0.15em;
            background-color: #E4002B !important;
            border-radius: 0;
            display: block;
            z-index: 10;
        }

        @keyframes shine { 0%, 62% { left: -55%; } 78%, 100% { left: 118%; } }
        .sub { margin-top: 8px; color: #64748B; font-size: 11px; font-weight: 900; letter-spacing: .14em; }
        .back { min-height: 42px; display: inline-flex; align-items: center; gap: 8px; padding: 0 14px; border-radius: 8px; border: 1px solid #B9CDF5; background: #fff; color: #003DA5; text-decoration: none; font-weight: 900; }
        .card { max-width: 1120px; margin: 0 auto; overflow: hidden; border: 1px solid #B9CDF5; border-radius: 10px; background: #fff; box-shadow: inset 5px 0 0 #E4002B; }
        .head { display: flex; align-items: center; justify-content: space-between; gap: 18px; padding: 24px 26px; border-bottom: 1px solid #E5EDF8; background: linear-gradient(135deg, #fff 0%, #F4F8FF 68%, #FFF3F6 100%); }
        .head h1 { font-size: 30px; line-height: 1.15; }
        .head p { margin-top: 8px; color: #52637A; line-height: 1.6; }
        .avatar-upload { position: relative; width: 110px; height: 110px; flex: 0 0 110px; display: grid; place-items: center; overflow: hidden; border: 0; border-radius: 18px; background: #003DA5; color: #fff; font: inherit; font-size: 30px; font-weight: 900; cursor: pointer; box-shadow: inset 9px 0 0 #E4002B, 0 18px 34px rgba(0,31,91,.18); }
        .avatar-upload:hover .avatar-overlay, .avatar-upload:focus-within .avatar-overlay { opacity: 1; }
        .avatar-upload input { position: absolute; width: 1px; height: 1px; opacity: 0; pointer-events: none; }
        .avatar-preview { position: absolute; inset: 0; display: grid; place-items: center; overflow: hidden; border-radius: inherit; background: #003DA5; color: #fff; }
        .avatar-preview img { width: 100%; height: 100%; object-fit: cover; }
        .avatar-overlay { position: absolute; inset: 0; display: grid; place-items: end center; padding: 10px; opacity: .92; color: #fff; background: linear-gradient(180deg, rgba(0,31,91,0) 35%, rgba(0,31,91,.88) 100%); transition: opacity .2s ease; }
        .avatar-overlay span { display: inline-flex; align-items: center; gap: 6px; border-radius: 999px; padding: 6px 10px; background: rgba(255,255,255,.92); color: #003DA5; font-size: 11px; font-weight: 900; }
        form { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 16px; padding: 24px 26px 26px; }
        .field { display: grid; gap: 8px; }
        .field.full { grid-column: 1 / -1; }
        label { color: #334155; font-size: 12px; font-weight: 900; letter-spacing: .06em; text-transform: uppercase; }
        input { width: 100%; min-height: 48px; border: 1px solid #C9D8F2; border-radius: 8px; padding: 0 14px; color: #001F5B; font: inherit; font-weight: 800; background: #F8FBFF; outline: none; }
        input:focus { border-color: #003DA5; background: #fff; box-shadow: 0 0 0 4px rgba(0,61,165,.12); }
        .note { grid-column: 1 / -1; display: flex; gap: 10px; align-items: flex-start; padding: 12px 14px; border: 1px solid #D4E0F7; border-radius: 8px; color: #52637A; background: #F4F8FF; line-height: 1.5; }
        .errors { grid-column: 1 / -1; padding: 12px 14px; border: 1px solid #FFC2CC; border-radius: 8px; color: #C9002B; background: #FFF1F3; font-weight: 700; }
        .errors ul { margin-left: 18px; }
        .actions { grid-column: 1 / -1; display: flex; justify-content: flex-end; gap: 10px; padding-top: 4px; }
        .btn { min-height: 46px; border-radius: 8px; padding: 0 18px; font: inherit; font-weight: 900; cursor: pointer; display: inline-flex; align-items: center; gap: 8px; text-decoration: none; }
        .btn.ghost { border: 1px solid #B9CDF5; background: #fff; color: #003DA5; }
        .btn.primary { border: 0; background: #003DA5; color: #fff; }
        @media (max-width: 760px) { .page { padding: 20px; } .top, .head { align-items: flex-start; flex-direction: column; } form { grid-template-columns: 1fr; } }
    </style>
</head>
<body>
@php
    $initials = mb_strtoupper(mb_substr($user->name ?? 'NV', 0, 2));
    $avatarUrl = $user->avatar_path ? asset('storage/'.$user->avatar_path) : null;
@endphp
<div class="page">
    <div class="top">
        <div class="brand">
            <div>
                <div class="word"><strong class="blue">mob<span class="brand-i">ı</span></strong><strong class="red">fone</strong></div>
                <div class="sub">WORKHUB PROFILE</div>
            </div>
        </div>
        <a class="back" href="{{ route('profile.show') }}"><i data-lucide="arrow-left" style="width:18px;height:18px"></i>Quay lại hồ sơ</a>
    </div>

    <section class="card">
        <div class="head">
            <div>
                <h1>Chỉnh sửa thông tin</h1>
                <p>Cập nhật họ tên, email và ảnh đại diện của tài khoản.</p>
            </div>
            <label class="avatar-upload" for="profileAvatar" title="Đổi ảnh đại diện">
                <span class="avatar-preview" id="avatarPreview">
                    @if($avatarUrl)
                        <img src="{{ $avatarUrl }}" alt="Ảnh đại diện hiện tại">
                    @else
                        {{ $initials }}
                    @endif
                </span>
                <span class="avatar-overlay"><span><i data-lucide="camera" style="width:14px;height:14px"></i>Đổi ảnh</span></span>
                <input id="profileAvatar" name="avatar" type="file" accept="image/png,image/jpeg,image/webp" form="profileEditForm" aria-label="Chọn ảnh đại diện">
            </label>
        </div>

        <form id="profileEditForm" method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
            @csrf
            @method('PATCH')

            @if ($errors->any())
                <div class="errors">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="field">
                <label for="profileName">Họ tên</label>
                <input id="profileName" name="name" type="text" value="{{ old('name', $user->name) }}" required maxlength="255" autocomplete="name">
            </div>

            <div class="field">
                <label for="profileEmail">Email</label>
                <input id="profileEmail" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="255" autocomplete="email">
            </div>

            <div class="note">
                <i data-lucide="shield-alert" style="width:20px;height:20px;flex:0 0 auto;color:#003DA5"></i>
                <div>Phòng ban và chức vụ là thông tin phân quyền, chỉ quản trị viên hoặc trưởng phòng có quyền điều chỉnh trong màn hình nhân sự.</div>
            </div>

            <div class="actions">
                <a class="btn ghost" href="{{ route('profile.show') }}">Hủy</a>
                <button class="btn primary" type="submit"><i data-lucide="save" style="width:18px;height:18px"></i>Lưu thay đổi</button>
            </div>
        </form>
    </section>
</div>
<script>
    lucide.createIcons();

    const avatarInput = document.getElementById('profileAvatar');
    const avatarPreview = document.getElementById('avatarPreview');

    avatarInput?.addEventListener('change', (event) => {
        const file = event.target.files?.[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (readerEvent) => {
            avatarPreview.innerHTML = '';
            const image = document.createElement('img');
            image.src = readerEvent.target.result;
            image.alt = 'Ảnh đại diện mới';
            avatarPreview.appendChild(image);
        };
        reader.readAsDataURL(file);
    });
</script>
</body>
</html>
