<footer style="
    background: linear-gradient(135deg, #0d47a1, #1976d2);
    color: #fff;
    padding: 25px 20px;
    margin-top: 30px;
    border-radius: 12px 12px 0 0;
    box-shadow: 0 -2px 10px rgba(0,0,0,0.15);
    font-family: Arial, sans-serif;
">

    <div style="max-width: 1000px; margin: auto; text-align: center;">

        <h3 style="margin-bottom: 10px; font-weight: 600; letter-spacing: 0.5px;">
            📌 Liên hệ hỗ trợ
        </h3>

        <p style="margin: 5px 0; font-size: 15px;">
            📧 Email: <strong>{{ Auth::user()->email ?? 'vttt4cmu@mobifone.vn' }}</strong>
        </p>

        <p style="margin: 5px 0; font-size: 15px;">
            📞 Điện thoại: <strong>{{ Auth::user()->phone ?? '0123456789' }}</strong>
        </p>

        <div style="margin: 15px auto; width: 80px; height: 2px; background: rgba(255,255,255,0.5); border-radius: 5px;"></div>

        <p style="margin: 0; font-size: 13px; opacity: 0.9;">
            © 2026 - MobiFone tỉnh Cà Mau | Hệ thống quản lý nhân viên
        </p>

    </div>
</footer>