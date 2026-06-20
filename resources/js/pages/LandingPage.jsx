import React from 'react';
import { ArrowRight, CheckCircle, Users, Briefcase, BarChart3, Shield, Zap } from 'lucide-react';

const B = {
  navy:     "#001F5B",
  blue:     "#003DA5",
  blueHv:   "#0057C8",
  light:    "#E8F0FE",
  red:      "#E4002B",
  success:  "#16A34A",
  warning:  "#D97706",
  danger:   "#DC2626",
  bg:       "#F8F9FA",
  gray100:  "#F1F3F5",
  gray200:  "#E5E7EB",
  gray400:  "#9CA3AF",
  gray700:  "#374151",
};

export default function LandingPage({ onOpenDashboard }) {
  return (
    <div style={{ fontFamily: "'Be Vietnam Pro', 'Segoe UI', sans-serif", background: B.bg }}>
      {/* Navigation Header */}
      <header style={{ background: 'white', borderBottom: `1px solid ${B.gray200}` }}>
        <div style={{ maxWidth: '1180px', margin: '0 auto', padding: '0 18px', height: '68px', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
          <div style={{ display: 'flex', alignItems: 'center', gap: '12px', cursor: 'pointer' }}>
            <div style={{ width: '42px', height: '42px', borderRadius: '8px', background: `linear-gradient(135deg, ${B.navy}, ${B.blue})`, display: 'flex', alignItems: 'center', justifyContent: 'center', color: 'white', fontSize: '20px', fontWeight: 'bold' }}>M</div>
            <div>
              <div style={{ fontSize: '18px', fontWeight: 700, color: B.navy }}>MobiFone</div>
              <div style={{ fontSize: '11px', fontWeight: 600, color: B.blue, letterSpacing: '0.2em' }}>WORKHUB</div>
            </div>
          </div>

          <nav style={{ display: 'flex', gap: '12px' }}>
            <a href="#features" style={{ padding: '8px 16px', fontSize: '14px', fontWeight: 600, color: B.gray700, textDecoration: 'none' }}>Tính năng</a>
            <a href="#benefits" style={{ padding: '8px 16px', fontSize: '14px', fontWeight: 600, color: B.gray700, textDecoration: 'none' }}>Lợi ích</a>
            <button onClick={onOpenDashboard} style={{ padding: '8px 20px', borderRadius: '8px', background: B.blue, color: 'white', border: 'none', fontWeight: 600, fontSize: '14px', cursor: 'pointer' }}>Đăng nhập</button>
          </nav>
        </div>
      </header>

      {/* Hero Section */}
      <section style={{ maxWidth: '1180px', margin: '0 auto', padding: '60px 18px' }}>
        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '40px', alignItems: 'center' }}>
          {/* Left: Text */}
          <div>
            <div style={{ fontSize: '13px', fontWeight: 700, color: B.blue, letterSpacing: '0.06em', textTransform: 'uppercase', marginBottom: '16px' }}>Hệ Thống Quản Lý Công Việc</div>
            <h1 style={{ fontSize: '48px', fontWeight: 700, color: B.navy, lineHeight: 1.1, marginBottom: '20px' }}>Quản lý công việc nội bộ hiệu quả</h1>
            <p style={{ fontSize: '17px', color: B.gray700, lineHeight: 1.6, marginBottom: '32px', maxWidth: '550px' }}>
              MobiFone WorkHub cung cấp giải pháp quản lý công việc tích hợp. Giao task, theo dõi tiến độ, báo cáo hiệu suất và quản lý nhân viên tất cả trong một nền tảng.
            </p>
            <div style={{ display: 'flex', gap: '12px' }}>
              <button onClick={onOpenDashboard} style={{ padding: '14px 28px', background: `linear-gradient(135deg, ${B.blue}, ${B.blueHv})`, color: 'white', border: 'none', borderRadius: '8px', fontWeight: 600, fontSize: '15px', cursor: 'pointer', display: 'flex', alignItems: 'center', gap: '8px' }}>
                Khám phá ngay <ArrowRight size={18} />
              </button>
              <button onClick={onOpenDashboard} style={{ padding: '14px 28px', border: `1px solid ${B.gray200}`, background: 'white', color: B.gray700, borderRadius: '8px', fontWeight: 600, fontSize: '15px', cursor: 'pointer' }}>Xem demo</button>
            </div>
          </div>

          {/* Right: Feature Cards */}
          <div style={{ display: 'grid', gap: '16px' }}>
            {[
              { icon: '📊', title: 'Dashboard Thông Minh', desc: 'Visualize công việc theo thời gian thực' },
              { icon: '⚡', title: 'Kanban Board', desc: 'Quản lý workflow với drag-and-drop' },
              { icon: '👥', title: 'Team Collaboration', desc: 'Cộng tác nhóm và giao tiếp tức thì' },
              { icon: '📈', title: 'Analytics', desc: 'Báo cáo chi tiết về hiệu suất nhóm' },
            ].map((item, i) => (
              <div key={i} style={{ background: 'white', padding: '20px', borderRadius: '12px', boxShadow: `0 2px 8px rgba(0,61,165,0.07)`, display: 'flex', gap: '12px' }}>
                <div style={{ fontSize: '28px' }}>{item.icon}</div>
                <div>
                  <h3 style={{ fontSize: '16px', fontWeight: 600, color: B.navy, margin: '0 0 4px 0' }}>{item.title}</h3>
                  <p style={{ fontSize: '13px', color: B.gray400, margin: 0 }}>{item.desc}</p>
                </div>
              </div>
            ))}
          </div>
        </div>
      </section>

      {/* Features Section */}
      <section id="features" style={{ background: 'white', padding: '60px 0', borderTop: `1px solid ${B.gray200}`, borderBottom: `1px solid ${B.gray200}` }}>
        <div style={{ maxWidth: '1180px', margin: '0 auto', padding: '0 18px' }}>
          <h2 style={{ fontSize: '36px', fontWeight: 700, color: B.navy, textAlign: 'center', marginBottom: '40px' }}>Tính Năng Chính</h2>

          <div style={{ display: 'grid', gridTemplateColumns: 'repeat(3, 1fr)', gap: '24px' }}>
            {[
              { icon: CheckCircle, title: 'Quản Lý Task', desc: 'Tạo, giao, theo dõi công việc với deadline rõ ràng' },
              { icon: Users, title: 'Quản Lý Team', desc: 'Quản lý thành viên, phân quyền và phân công công việc' },
              { icon: BarChart3, title: 'Báo Cáo', desc: 'Xem thống kê hiệu suất, hoàn thành công việc theo thời gian' },
              { icon: Shield, title: 'Bảo Mật', desc: 'Các quyền truy cập chi tiết theo vai trò người dùng' },
              { icon: Zap, title: 'Thông Báo', desc: 'Email và thông báo hệ thống tức thì cho deadline' },
              { icon: Briefcase, title: 'Tài Liệu', desc: 'Đính kèm và quản lý tài liệu cho từng task' },
            ].map((item, i) => {
              const Icon = item.icon;
              return (
                <div key={i} style={{ padding: '28px', background: 'white', border: `1px solid ${B.gray200}`, borderRadius: '12px', textAlign: 'center', transition: 'all 0.3s' }}>
                  <div style={{ width: '52px', height: '52px', borderRadius: '12px', background: B.light, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px', color: B.blue }}>
                    <Icon size={28} />
                  </div>
                  <h3 style={{ fontSize: '18px', fontWeight: 600, color: B.navy, marginBottom: '8px' }}>{item.title}</h3>
                  <p style={{ fontSize: '14px', color: B.gray400, lineHeight: 1.6 }}>{item.desc}</p>
                </div>
              );
            })}
          </div>
        </div>
      </section>

      {/* Benefits Section */}
      <section id="benefits" style={{ maxWidth: '1180px', margin: '0 auto', padding: '60px 18px' }}>
        <h2 style={{ fontSize: '36px', fontWeight: 700, color: B.navy, textAlign: 'center', marginBottom: '40px' }}>Lợi Ích Cho Tổ Chức</h2>

        <div style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '32px', alignItems: 'center' }}>
          <div>
            {[
              'Tăng năng suất làm việc lên đến 40%',
              'Giảm thời gian quản lý công việc thủ công',
              'Cải thiện giao tiếp nội bộ',
              'Dễ dàng theo dõi KPI nhóm',
              'Tiết kiệm thời gian report hàng tuần',
              'Quản lý nhân viên tập trung',
            ].map((benefit, i) => (
              <div key={i} style={{ display: 'flex', gap: '12px', marginBottom: '16px', alignItems: 'flex-start' }}>
                <CheckCircle size={20} style={{ color: B.success, marginTop: '2px', flexShrink: 0 }} />
                <span style={{ fontSize: '15px', color: B.gray700 }}>{benefit}</span>
              </div>
            ))}
          </div>

          <div style={{ background: B.light, padding: '40px', borderRadius: '16px', position: 'relative', overflow: 'hidden' }}>
            <div style={{ position: 'absolute', inset: 0, opacity: 0.05, backgroundImage: 'radial-gradient(circle, white 1.5px, transparent 1.5px)', backgroundSize: '30px 30px' }} />
            <div style={{ position: 'relative', zIndex: 1 }}>
              <div style={{ fontSize: '56px', fontWeight: 700, color: B.blue, marginBottom: '8px' }}>98%</div>
              <p style={{ fontSize: '15px', color: B.gray700, marginBottom: '16px' }}>Độ hài lòng người dùng</p>
              <p style={{ fontSize: '13px', color: B.gray400 }}>Được sử dụng bởi các tổ chức lớn tại Việt Nam</p>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section style={{ background: `linear-gradient(135deg, ${B.navy}, ${B.blueHv})`, color: 'white', padding: '60px 18px', textAlign: 'center' }}>
        <div style={{ maxWidth: '600px', margin: '0 auto' }}>
          <h2 style={{ fontSize: '36px', fontWeight: 700, marginBottom: '20px' }}>Sẵn sàng nâng cao hiệu quả?</h2>
          <p style={{ fontSize: '17px', lineHeight: 1.6, marginBottom: '32px', opacity: 0.9 }}>Tham gia ngay để trải nghiệm hệ thống quản lý công việc thế hệ mới của MobiFone.</p>
          <button onClick={onOpenDashboard} style={{ padding: '14px 40px', background: 'white', color: B.blue, border: 'none', borderRadius: '8px', fontWeight: 600, fontSize: '16px', cursor: 'pointer', display: 'inline-flex', alignItems: 'center', gap: '8px' }}>
            Đăng nhập WorkHub <ArrowRight size={18} />
          </button>
        </div>
      </section>

      {/* Footer */}
      <footer style={{ background: B.navy, color: 'white', padding: '40px 18px', textAlign: 'center' }}>
        <div style={{ maxWidth: '1180px', margin: '0 auto' }}>
          <p style={{ fontSize: '14px', marginBottom: '8px' }}>© 2025 MobiFone. Tất cả các quyền được bảo lưu.</p>
          <p style={{ fontSize: '12px', opacity: 0.6 }}>MobiFone WorkHub v2.4.1</p>
        </div>
      </footer>
    </div>
  );
}
