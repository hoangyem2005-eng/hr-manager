import React, { useState } from "react";
import {
  LayoutDashboard, CheckSquare, Users, FileText, BarChart2, Bell,
  Settings, LogOut, ChevronLeft, ChevronRight, Search, Calendar,
  Plus, List, Columns, Clock, AlertCircle, CheckCircle, PlayCircle,
  Clipboard, MoreVertical, Download, Shield, ArrowRight, Eye, EyeOff,
  Lock, Mail, X, Upload, Send, Edit, Crown, Briefcase,
  User, Check, Filter,
} from "lucide-react";
import {
  PieChart, Pie, Cell, LineChart, Line, XAxis, YAxis, CartesianGrid,
  Tooltip, Legend, BarChart, Bar, ResponsiveContainer,
} from "recharts";

// ─── Brand tokens ─────────────────────────────────────────────────
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

// ─── Mock data ────────────────────────────────────────────────────
const TASKS = [
  { id: "WH-001", name: "Cập nhật quy trình tuyển dụng Q3 2025", assignee: "Nguyễn Văn An", avatar: "NA", priority: "Cao",      deadline: "25/07/2025", status: "Đang làm",    progress: 65 },
  { id: "WH-002", name: "Báo cáo KPI tháng 6 phòng Nhân sự",     assignee: "Trần Thị Bích",  avatar: "TB", priority: "Trung bình", deadline: "30/06/2025", status: "Quá hạn",    progress: 30 },
  { id: "WH-003", name: "Tổ chức đào tạo kỹ năng mềm nội bộ",    assignee: "Lê Minh Châu",   avatar: "LC", priority: "Cao",      deadline: "10/08/2025", status: "Chờ xử lý",  progress: 0  },
  { id: "WH-004", name: "Review hợp đồng lao động mới ký",        assignee: "Phạm Quốc Dũng", avatar: "PD", priority: "Thấp",    deadline: "15/07/2025", status: "Đang review", progress: 80 },
  { id: "WH-005", name: "Cập nhật chính sách phúc lợi nhân viên", assignee: "Hoàng Thị Em",   avatar: "HE", priority: "Trung bình", deadline: "01/08/2025", status: "Hoàn thành", progress: 100},
];

const MEMBERS = [
  { id: "NV001", name: "Nguyễn Văn An",  email: "an.nguyen@mobifone.vn",   dept: "Nhân sự",  position: "Trưởng phòng", role: "Quản lý",  tasks: 12, joined: "15/03/2022", status: "active",   avatar: "NA", color: B.blue   },
  { id: "NV002", name: "Trần Thị Bích",  email: "bich.tran@mobifone.vn",   dept: "Nhân sự",  position: "Chuyên viên",  role: "Nhân viên",tasks: 8,  joined: "20/07/2023", status: "active",   avatar: "TB", color: B.navy   },
  { id: "NV003", name: "Lê Minh Châu",   email: "chau.le@mobifone.vn",     dept: "Đào tạo",  position: "Chuyên viên",  role: "Nhân viên",tasks: 5,  joined: "01/01/2024", status: "active",   avatar: "LC", color: "#7C3AED"},
  { id: "NV004", name: "Phạm Quốc Dũng", email: "dung.pham@mobifone.vn",   dept: "Pháp chế", position: "Luật sư",      role: "Nhân viên",tasks: 3,  joined: "10/09/2022", status: "inactive", avatar: "PD", color: "#0369A1"},
  { id: "NV005", name: "Hoàng Thị Em",   email: "em.hoang@mobifone.vn",    dept: "Nhân sự",  position: "Phó phòng",    role: "Quản lý",  tasks: 9,  joined: "05/11/2021", status: "active",   avatar: "HE", color: "#059669"},
];

const NOTIFS = [
  { id: 1, type: "task",     title: "Bạn được giao công việc mới",   desc: "WH-006: Phân tích dữ liệu nhân sự Q2",   time: "5 phút trước",   read: false },
  { id: 2, type: "overdue",  title: "Công việc quá hạn",             desc: "WH-002 đã quá hạn 3 ngày",              time: "2 giờ trước",    read: false },
  { id: 3, type: "deadline", title: "Nhắc nhở deadline",             desc: "WH-001 còn 2 ngày nữa đến hạn",         time: "Hôm qua",        read: true  },
  { id: 4, type: "complete", title: "Công việc hoàn thành",          desc: "WH-005 đã được đánh dấu hoàn thành",    time: "2 ngày trước",   read: true  },
  { id: 5, type: "task",     title: "Bình luận mới trong task",      desc: "Nguyễn Văn An đã bình luận trong WH-003", time: "3 ngày trước",  read: true  },
];

const PIE_DATA = [
  { name: "Hoàn thành", value: 58, color: B.success },
  { name: "Đang làm",   value: 42, color: B.warning },
  { name: "Đang review",value: 22, color: B.blue    },
  { name: "Chờ xử lý",  value: 20, color: "#6B7280" },
];

const LINE_DATA = [
  { week: "T1", assigned: 18, completed: 12 },
  { week: "T2", assigned: 25, completed: 20 },
  { week: "T3", assigned: 22, completed: 18 },
  { week: "T4", assigned: 30, completed: 25 },
  { week: "T5", assigned: 28, completed: 22 },
  { week: "T6", assigned: 35, completed: 30 },
  { week: "T7", assigned: 32, completed: 28 },
];

const PERF_DATA = [
  { name: "Nguyễn V. An",   completion: 85, overdue: 15 },
  { name: "Hoàng T. Em",    completion: 78, overdue: 10 },
  { name: "Trần T. Bích",   completion: 62, overdue: 20 },
  { name: "Lê M. Châu",     completion: 55, overdue:  8 },
  { name: "Phạm Q. Dũng",   completion: 40, overdue: 25 },
];

// ─── Shared atoms ─────────────────────────────────────────────────
function Av({ initials, size = 40, color = B.blue }: { initials: string; size?: number; color?: string }) {
  return (
    <div
      className="flex items-center justify-center rounded-full font-semibold text-white flex-shrink-0"
      style={{ width: size, height: size, background: color, fontSize: size * 0.36 }}
    >
      {initials}
    </div>
  );
}

function StatusBadge({ status }: { status: string }) {
  const m: Record<string, string> = {
    "Đang làm":    "bg-amber-100 text-amber-700",
    "Hoàn thành":  "bg-green-100 text-green-700",
    "Quá hạn":     "bg-red-100 text-red-700",
    "Chờ xử lý":   "bg-gray-100 text-gray-500",
    "Đang review": "bg-blue-100 text-blue-700",
  };
  return <span className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${m[status] ?? "bg-gray-100 text-gray-500"}`}>{status}</span>;
}

function PriBadge({ priority }: { priority: string }) {
  const m: Record<string, string> = {
    "Cao":       "bg-red-100 text-red-700",
    "Trung bình":"bg-amber-100 text-amber-700",
    "Thấp":      "bg-blue-100 text-blue-700",
  };
  return <span className={`px-2.5 py-0.5 rounded-full text-xs font-medium ${m[priority] ?? "bg-gray-100 text-gray-500"}`}>{priority}</span>;
}

function Toggle({ on }: { on: boolean }) {
  return (
    <div className="w-10 h-5 rounded-full relative flex-shrink-0 transition-colors" style={{ background: on ? B.blue : "#D1D5DB" }}>
      <div className="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-all" style={{ left: on ? "calc(100% - 18px)" : "2px" }} />
    </div>
  );
}

// ─── LOGIN ────────────────────────────────────────────────────────
function LoginScreen({ onLogin }: { onLogin: () => void }) {
  const [showPass, setShowPass] = useState(false);
  const [email, setEmail] = useState("");
  const [pass, setPass] = useState("");

  return (
    <div className="flex h-screen w-full font-['Be_Vietnam_Pro',sans-serif]">
      {/* Left panel */}
      <div
        className="relative flex flex-col items-center justify-center w-[55%] overflow-hidden"
        style={{ background: `linear-gradient(145deg, ${B.navy} 0%, ${B.blueHv} 100%)` }}
      >
        <div
          className="absolute inset-0 opacity-[0.04]"
          style={{ backgroundImage: "radial-gradient(circle, white 1.5px, transparent 1.5px)", backgroundSize: "30px 30px" }}
        />
        {/* Floating orbs */}
        {[
          { s: 80, t: "8%",  l: "6%",  d: "3s"  },
          { s: 50, t: "20%", l: "80%", d: "4s"  },
          { s: 30, t: "60%", l: "5%",  d: "3.5s"},
          { s: 60, t: "75%", l: "75%", d: "5s"  },
          { s: 20, t: "45%", l: "90%", d: "2.5s"},
        ].map((o, i) => (
          <div
            key={i}
            className="absolute rounded-full"
            style={{ width: o.s, height: o.s, top: o.t, left: o.l, background: "rgba(255,255,255,0.06)", animation: `floatOrb ${o.d} ease-in-out infinite alternate` }}
          />
        ))}

        <div className="relative z-10 flex flex-col items-center gap-6 px-16 text-center">
          {/* Logo */}
          <div className="flex items-center gap-4 mb-2">
            <div className="w-14 h-14 rounded-2xl bg-white/15 backdrop-blur-sm flex items-center justify-center border border-white/20">
              <span className="text-white font-black text-2xl">M</span>
            </div>
            <div className="text-left">
              <div className="text-white text-2xl font-bold tracking-tight">MobiFone</div>
              <div className="text-blue-300 text-xs font-semibold tracking-[0.2em] uppercase">WorkHub</div>
            </div>
          </div>

          <p className="text-blue-200 text-lg font-light leading-relaxed max-w-xs">
            Hệ thống Quản lý Công việc Nội bộ
          </p>

          {/* Dashboard preview card */}
          <div className="w-80 rounded-2xl bg-white/10 backdrop-blur-sm border border-white/15 p-5 text-left mt-2">
            <p className="text-blue-300 text-xs font-medium mb-3 uppercase tracking-wider">Dashboard Overview</p>
            <div className="grid grid-cols-2 gap-2 mb-4">
              {[["142","Tổng CV",B.blue],["58","Hoàn thành",B.success],["42","Đang làm",B.warning],["12","Quá hạn",B.danger]].map(([n,l,c]) => (
                <div key={String(l)} className="rounded-xl p-3" style={{ background: "rgba(255,255,255,0.08)" }}>
                  <div className="text-xl font-bold text-white">{n}</div>
                  <div className="text-xs text-blue-200 mt-0.5">{l}</div>
                  <div className="h-1 rounded-full mt-2" style={{ background: `${c}60` }}>
                    <div className="h-1 rounded-full" style={{ width: `${Number(n) / 1.42}%`, background: String(c) }} />
                  </div>
                </div>
              ))}
            </div>
            <div className="text-xs text-blue-200 flex items-center justify-between">
              <span>Tiến độ tháng 6/2025</span>
              <span className="font-semibold text-white">72%</span>
            </div>
            <div className="h-1.5 rounded-full mt-1.5" style={{ background: "rgba(255,255,255,0.2)" }}>
              <div className="h-1.5 rounded-full" style={{ width: "72%", background: B.success }} />
            </div>
          </div>
        </div>
      </div>

      {/* Right panel */}
      <div className="flex items-center justify-center w-[45%] bg-white px-16">
        <div className="w-full max-w-sm">
          <h1 className="text-3xl font-bold mb-1" style={{ color: B.navy }}>Chào mừng trở lại 👋</h1>
          <p className="text-sm mb-8" style={{ color: B.gray400 }}>Đăng nhập để tiếp tục vào MobiFone WorkHub</p>

          <div className="space-y-4">
            {/* Email */}
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Email công ty</label>
              <div className="relative">
                <Mail size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2" style={{ color: B.gray400 }} />
                <input
                  type="email" value={email} onChange={e => setEmail(e.target.value)}
                  placeholder="ten.nguyen@mobifone.vn"
                  className="w-full pl-10 pr-4 py-3 border rounded-xl text-sm outline-none transition-all"
                  style={{ borderColor: B.gray200 }}
                  onFocus={e => (e.target.style.borderColor = B.blue)}
                  onBlur={e => (e.target.style.borderColor = B.gray200)}
                />
              </div>
            </div>

            {/* Password */}
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Mật khẩu</label>
              <div className="relative">
                <Lock size={15} className="absolute left-3.5 top-1/2 -translate-y-1/2" style={{ color: B.gray400 }} />
                <input
                  type={showPass ? "text" : "password"} value={pass} onChange={e => setPass(e.target.value)}
                  placeholder="••••••••"
                  className="w-full pl-10 pr-10 py-3 border rounded-xl text-sm outline-none transition-all"
                  style={{ borderColor: B.gray200 }}
                  onFocus={e => (e.target.style.borderColor = B.blue)}
                  onBlur={e => (e.target.style.borderColor = B.gray200)}
                />
                <button onClick={() => setShowPass(!showPass)} className="absolute right-3.5 top-1/2 -translate-y-1/2" style={{ color: B.gray400 }}>
                  {showPass ? <EyeOff size={15} /> : <Eye size={15} />}
                </button>
              </div>
            </div>

            <div className="flex items-center justify-between">
              <label className="flex items-center gap-2 text-sm cursor-pointer" style={{ color: B.gray700 }}>
                <input type="checkbox" className="rounded" /> Ghi nhớ đăng nhập
              </label>
              <button className="text-sm font-semibold" style={{ color: B.blue }}>Quên mật khẩu?</button>
            </div>

            <button
              onClick={onLogin}
              className="w-full py-3.5 rounded-xl text-white font-semibold flex items-center justify-center gap-2 transition-all active:scale-[0.97] hover:-translate-y-0.5"
              style={{ background: `linear-gradient(135deg, ${B.blue}, ${B.blueHv})`, boxShadow: `0 8px 24px ${B.blue}40`, fontSize: 15 }}
            >
              Đăng nhập <ArrowRight size={18} />
            </button>

            <div className="relative flex items-center gap-3">
              <div className="flex-1 h-px bg-gray-200" />
              <span className="text-xs" style={{ color: B.gray400 }}>Hoặc</span>
              <div className="flex-1 h-px bg-gray-200" />
            </div>

            <button
              className="w-full py-3 rounded-xl border text-sm font-medium flex items-center justify-center gap-2 transition-all hover:bg-gray-50"
              style={{ borderColor: B.gray200, color: B.gray700 }}
            >
              <div className="w-5 h-5 rounded flex items-center justify-center" style={{ background: B.blue }}>
                <span className="text-white text-xs font-black">M</span>
              </div>
              Đăng nhập bằng MobiFone SSO
            </button>
          </div>

          <p className="text-center text-xs mt-10" style={{ color: B.gray400 }}>
            © 2025 MobiFone. All rights reserved. · v2.4.1
          </p>
        </div>
      </div>

      <style>{`@keyframes floatOrb { from { transform: translateY(0); } to { transform: translateY(-18px); } }`}</style>
    </div>
  );
}

// ─── DASHBOARD ───────────────────────────────────────────────────
function DashboardScreen() {
  const kpis = [
    { label: "Tổng công việc", value: 142, sub: "Tháng 6/2025",     icon: Clipboard,    border: B.blue,    ic: B.blue    },
    { label: "Đang thực hiện", value:  42, sub: "Đang tiến hành",   icon: PlayCircle,   border: B.warning, ic: B.warning },
    { label: "Hoàn thành",     value:  58, sub: "Đã xong",          icon: CheckCircle,  border: B.success, ic: B.success },
    { label: "Quá hạn",        value:  12, sub: "Cần xử lý ngay",   icon: AlertCircle,  border: B.danger,  ic: B.danger, pulse: true },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <div>
          <h1 className="text-2xl font-bold" style={{ color: B.navy }}>Dashboard</h1>
          <p className="text-sm mt-0.5" style={{ color: B.gray400 }}>Tổng quan hệ thống — Tháng 6/2025</p>
        </div>
        <button className="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
          style={{ background: B.blue, boxShadow: `0 4px 16px ${B.blue}40` }}>
          <Plus size={16} /> Tạo công việc mới
        </button>
      </div>

      {/* KPI row */}
      <div className="grid grid-cols-4 gap-4">
        {kpis.map(({ label, value, sub, icon: Icon, border, ic, pulse }) => (
          <div key={label} className="bg-white rounded-2xl p-5 border-l-4 relative overflow-hidden"
            style={{ borderLeftColor: border, boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            {pulse && (
              <span className="absolute top-3.5 right-3.5 w-2.5 h-2.5 rounded-full" style={{ background: B.danger, animation: "kpiPulse 2s ease-in-out infinite" }} />
            )}
            <div className="flex items-start justify-between">
              <div>
                <p className="text-xs font-semibold uppercase tracking-wider mb-2" style={{ color: B.gray400 }}>{label}</p>
                <p className="text-4xl font-bold" style={{ color: B.navy }}>{value}</p>
                <p className="text-xs mt-1.5" style={{ color: B.gray400 }}>{sub}</p>
              </div>
              <div className="w-11 h-11 rounded-xl flex items-center justify-center" style={{ background: `${ic}15` }}>
                <Icon size={22} style={{ color: ic }} />
              </div>
            </div>
          </div>
        ))}
      </div>

      {/* Main grid */}
      <div className="grid grid-cols-5 gap-5">
        {/* Tasks table */}
        <div className="col-span-3 bg-white rounded-2xl shadow-sm overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <div className="px-5 py-4 border-b border-gray-100 flex items-center justify-between">
            <h3 className="font-bold" style={{ color: B.navy }}>Công việc gần đây</h3>
            <button className="text-sm font-semibold" style={{ color: B.blue }}>Xem tất cả →</button>
          </div>
          <div className="overflow-x-auto">
            <table className="w-full">
              <thead>
                <tr style={{ background: B.gray100 }}>
                  {["Mã CV", "Tên công việc", "Người thực hiện", "Ưu tiên", "Deadline", "Trạng thái"].map(h => (
                    <th key={h} className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style={{ color: B.gray400 }}>{h}</th>
                  ))}
                </tr>
              </thead>
              <tbody>
                {TASKS.map(task => (
                  <tr key={task.id} className="border-t border-gray-50 hover:bg-blue-50/60 transition-colors cursor-pointer">
                    <td className="px-4 py-3.5 text-xs font-mono" style={{ color: B.gray400 }}>{task.id}</td>
                    <td className="px-4 py-3.5 text-sm font-medium max-w-[150px] truncate" style={{ color: B.gray700 }}>{task.name}</td>
                    <td className="px-4 py-3.5">
                      <div className="flex items-center gap-2">
                        <Av initials={task.avatar} size={28} color={MEMBERS.find(m => m.avatar === task.avatar)?.color ?? B.blue} />
                        <span className="text-xs" style={{ color: B.gray700 }}>{task.assignee.split(" ").slice(-2).join(" ")}</span>
                      </div>
                    </td>
                    <td className="px-4 py-3.5"><PriBadge priority={task.priority} /></td>
                    <td className="px-4 py-3.5 text-xs font-medium" style={{ color: task.status === "Quá hạn" ? B.danger : B.gray400 }}>{task.deadline}</td>
                    <td className="px-4 py-3.5"><StatusBadge status={task.status} /></td>
                  </tr>
                ))}
              </tbody>
            </table>
          </div>
        </div>

        {/* Right column */}
        <div className="col-span-2 flex flex-col gap-4">
          {/* Donut */}
          <div className="bg-white rounded-2xl p-5 shadow-sm" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h3 className="font-bold mb-4" style={{ color: B.navy }}>Phân bổ công việc</h3>
            <div className="flex items-center gap-3">
              <ResponsiveContainer width={110} height={110}>
                <PieChart>
                  <Pie data={PIE_DATA} cx="50%" cy="50%" innerRadius={32} outerRadius={52} dataKey="value" strokeWidth={0}>
                    {PIE_DATA.map((e, i) => <Cell key={i} fill={e.color} />)}
                  </Pie>
                </PieChart>
              </ResponsiveContainer>
              <div className="space-y-2 flex-1">
                {PIE_DATA.map(d => (
                  <div key={d.name} className="flex items-center justify-between">
                    <div className="flex items-center gap-2">
                      <div className="w-2.5 h-2.5 rounded-full flex-shrink-0" style={{ background: d.color }} />
                      <span className="text-xs" style={{ color: B.gray700 }}>{d.name}</span>
                    </div>
                    <span className="text-xs font-bold" style={{ color: B.navy }}>{d.value}</span>
                  </div>
                ))}
              </div>
            </div>
          </div>

          {/* Active members */}
          <div className="bg-white rounded-2xl p-5 shadow-sm flex-1" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h3 className="font-bold mb-4" style={{ color: B.navy }}>Thành viên hoạt động</h3>
            <div className="space-y-3.5">
              {MEMBERS.filter(m => m.status === "active").map(m => (
                <div key={m.id} className="flex items-center gap-3">
                  <div className="relative">
                    <Av initials={m.avatar} size={36} color={m.color} />
                    <div className="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white" />
                  </div>
                  <div className="flex-1 min-w-0">
                    <div className="text-sm font-semibold truncate" style={{ color: B.gray700 }}>{m.name.split(" ").slice(-2).join(" ")}</div>
                    <div className="text-xs" style={{ color: B.gray400 }}>{m.dept}</div>
                  </div>
                  <span className="text-xs px-2 py-0.5 rounded-full font-semibold" style={{ background: B.light, color: B.blue }}>{m.tasks}</span>
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>

      <style>{`@keyframes kpiPulse { 0%,100%{opacity:1;transform:scale(1)} 50%{opacity:0.4;transform:scale(1.3)} }`}</style>
    </div>
  );
}

// ─── KANBAN ───────────────────────────────────────────────────────
function KanbanScreen({ onOpenModal }: { onOpenModal: () => void }) {
  const [view, setView]     = useState<"kanban" | "list">("kanban");
  const [filter, setFilter] = useState("Tất cả");
  const filters = ["Tất cả", "Của tôi", "Chưa giao", "Quá hạn"];

  const cols = [
    { key: "pending", label: "📋 Chờ xử lý", color: "#6B7280", bg: "#F9FAFB", tasks: [TASKS[2]] },
    { key: "doing",   label: "⚡ Đang làm",   color: B.warning, bg: "#FFFBEB", tasks: [TASKS[0]] },
    { key: "review",  label: "🔍 Đang review", color: B.blue,    bg: "#EFF6FF", tasks: [TASKS[3]] },
    { key: "done",    label: "✅ Hoàn thành",  color: B.success, bg: "#F0FDF4", tasks: [TASKS[4]] },
  ];

  return (
    <div className="space-y-5">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold" style={{ color: B.navy }}>Quản lý Công việc</h1>
        <button onClick={onOpenModal}
          className="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all active:scale-95 hover:-translate-y-0.5"
          style={{ background: B.blue, boxShadow: `0 4px 16px ${B.blue}40` }}>
          <Plus size={16} /> Tạo công việc mới
        </button>
      </div>

      {/* Toolbar */}
      <div className="flex items-center gap-3 flex-wrap">
        <div className="flex rounded-xl border overflow-hidden" style={{ borderColor: B.gray200 }}>
          {([["kanban", Columns, "Kanban"], ["list", List, "Danh sách"]] as const).map(([v, Icon, label]) => (
            <button key={v} onClick={() => setView(v)}
              className="flex items-center gap-1.5 px-3 py-2 text-sm font-semibold transition-colors"
              style={{ background: view === v ? B.blue : "white", color: view === v ? "white" : B.gray700 }}>
              <Icon size={14} /> {label}
            </button>
          ))}
        </div>
        <div className="flex gap-2">
          {filters.map(f => (
            <button key={f} onClick={() => setFilter(f)}
              className="px-3 py-1.5 rounded-full text-xs font-semibold transition-all"
              style={{ background: filter === f ? B.blue : "white", color: filter === f ? "white" : B.gray700, border: `1px solid ${filter === f ? B.blue : B.gray200}` }}>
              {f}
            </button>
          ))}
        </div>
      </div>

      {/* Kanban view */}
      {view === "kanban" && (
        <div className="grid grid-cols-4 gap-4">
          {cols.map(col => (
            <div key={col.key} className="rounded-2xl min-h-64" style={{ background: col.bg }}>
              <div className="flex items-center justify-between px-4 py-3 border-b" style={{ borderColor: `${col.color}25` }}>
                <span className="text-sm font-bold" style={{ color: col.color }}>{col.label}</span>
                <span className="text-xs px-2 py-0.5 rounded-full font-bold text-white" style={{ background: col.color }}>{col.tasks.length}</span>
              </div>
              <div className="p-3 space-y-3">
                {col.tasks.map(task => (
                  <div key={task.id}
                    className="bg-white rounded-2xl p-4 cursor-pointer transition-all duration-200 hover:-translate-y-1 border-l-3"
                    style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.08)" }}>
                    <div className="flex items-center justify-between mb-2">
                      <PriBadge priority={task.priority} />
                      <button style={{ color: B.gray400 }}><MoreVertical size={14} /></button>
                    </div>
                    <h4 className="text-sm font-bold mb-1 line-clamp-2" style={{ color: B.gray700 }}>{task.name}</h4>
                    <p className="text-xs mb-3 truncate" style={{ color: B.gray400 }}>Phòng Nhân sự · MobiFone</p>
                    <div className="mb-3">
                      <div className="flex items-center justify-between mb-1">
                        <span className="text-xs" style={{ color: B.gray400 }}>Tiến độ</span>
                        <span className="text-xs font-bold" style={{ color: col.color }}>{task.progress}%</span>
                      </div>
                      <div className="h-1.5 rounded-full" style={{ background: `${col.color}20` }}>
                        <div className="h-1.5 rounded-full transition-all" style={{ width: `${task.progress}%`, background: col.color }} />
                      </div>
                    </div>
                    <div className="flex items-center justify-between">
                      <div className="flex items-center gap-1 text-xs" style={{ color: task.status === "Quá hạn" ? B.danger : B.gray400 }}>
                        <Calendar size={11} /> {task.deadline}
                      </div>
                      <Av initials={task.avatar} size={24} color={MEMBERS.find(m => m.avatar === task.avatar)?.color ?? B.blue} />
                    </div>
                  </div>
                ))}
                <button
                  className="w-full py-2.5 rounded-xl text-xs font-semibold border-2 border-dashed transition-colors hover:border-opacity-80"
                  style={{ borderColor: `${col.color}40`, color: B.gray400 }}>
                  + Thêm công việc
                </button>
              </div>
            </div>
          ))}
        </div>
      )}

      {/* List view */}
      {view === "list" && (
        <div className="bg-white rounded-2xl shadow-sm overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <table className="w-full">
            <thead>
              <tr style={{ background: B.gray100 }}>
                {["Mã CV", "Tên công việc", "Người thực hiện", "Ưu tiên", "Deadline", "Trạng thái", "Tiến độ"].map(h => (
                  <th key={h} className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style={{ color: B.gray400 }}>{h}</th>
                ))}
              </tr>
            </thead>
            <tbody>
              {TASKS.map(task => (
                <tr key={task.id} className="border-t border-gray-50 hover:bg-blue-50/50 transition-colors cursor-pointer">
                  <td className="px-5 py-4 text-xs font-mono" style={{ color: B.gray400 }}>{task.id}</td>
                  <td className="px-5 py-4 text-sm font-semibold max-w-xs truncate" style={{ color: B.gray700 }}>{task.name}</td>
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-2">
                      <Av initials={task.avatar} size={28} color={MEMBERS.find(m => m.avatar === task.avatar)?.color ?? B.blue} />
                      <span className="text-sm" style={{ color: B.gray700 }}>{task.assignee}</span>
                    </div>
                  </td>
                  <td className="px-5 py-4"><PriBadge priority={task.priority} /></td>
                  <td className="px-5 py-4 text-xs font-medium" style={{ color: task.status === "Quá hạn" ? B.danger : B.gray400 }}>{task.deadline}</td>
                  <td className="px-5 py-4"><StatusBadge status={task.status} /></td>
                  <td className="px-5 py-4">
                    <div className="flex items-center gap-2">
                      <div className="flex-1 h-1.5 rounded-full min-w-16" style={{ background: `${B.blue}20` }}>
                        <div className="h-1.5 rounded-full" style={{ width: `${task.progress}%`, background: B.blue }} />
                      </div>
                      <span className="text-xs w-8 text-right font-mono" style={{ color: B.gray400 }}>{task.progress}%</span>
                    </div>
                  </td>
                </tr>
              ))}
            </tbody>
          </table>
        </div>
      )}
    </div>
  );
}

// ─── CREATE TASK MODAL ───────────────────────────────────────────
function CreateTaskModal({ onClose }: { onClose: () => void }) {
  return (
    <div className="fixed inset-0 z-50 flex items-center justify-center p-4" style={{ background: "rgba(0,0,0,0.5)" }}>
      <div className="bg-white rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto shadow-2xl">
        <div className="flex items-center justify-between px-6 py-5 border-b" style={{ borderColor: B.gray200 }}>
          <h2 className="text-xl font-bold" style={{ color: B.navy }}>Tạo công việc mới</h2>
          <button onClick={onClose} className="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-gray-100 transition-colors">
            <X size={18} style={{ color: B.gray400 }} />
          </button>
        </div>

        <div className="p-6 space-y-5">
          <div>
            <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Tên công việc *</label>
            <input className="w-full px-4 py-3 border rounded-xl text-sm outline-none transition-all" style={{ borderColor: B.gray200 }}
              placeholder="Nhập tên công việc..." />
          </div>
          <div>
            <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Mô tả</label>
            <textarea rows={3} className="w-full px-4 py-3 border rounded-xl text-sm outline-none resize-none" style={{ borderColor: B.gray200 }}
              placeholder="Mô tả chi tiết công việc..." />
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Ưu tiên</label>
              <select className="w-full px-4 py-3 border rounded-xl text-sm outline-none bg-white" style={{ borderColor: B.gray200 }}>
                <option>🔴 Cao</option><option>🟡 Trung bình</option><option>🔵 Thấp</option>
              </select>
            </div>
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Trạng thái</label>
              <select className="w-full px-4 py-3 border rounded-xl text-sm outline-none bg-white" style={{ borderColor: B.gray200 }}>
                <option>Chờ xử lý</option><option>Đang làm</option><option>Đang review</option><option>Hoàn thành</option>
              </select>
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Người thực hiện</label>
              <select className="w-full px-4 py-3 border rounded-xl text-sm outline-none bg-white" style={{ borderColor: B.gray200 }}>
                {MEMBERS.map(m => <option key={m.id}>{m.name}</option>)}
              </select>
            </div>
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Người báo cáo</label>
              <select className="w-full px-4 py-3 border rounded-xl text-sm outline-none bg-white" style={{ borderColor: B.gray200 }}>
                {MEMBERS.map(m => <option key={m.id}>{m.name}</option>)}
              </select>
            </div>
          </div>
          <div className="grid grid-cols-2 gap-4">
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Ngày bắt đầu</label>
              <input type="date" className="w-full px-4 py-3 border rounded-xl text-sm outline-none" style={{ borderColor: B.gray200 }} />
            </div>
            <div>
              <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Deadline *</label>
              <input type="date" className="w-full px-4 py-3 border rounded-xl text-sm outline-none" style={{ borderColor: B.gray200 }} />
            </div>
          </div>
          <div>
            <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>Đính kèm tệp</label>
            <div className="border-2 border-dashed rounded-xl p-6 flex flex-col items-center gap-2 hover:border-blue-400 transition-colors cursor-pointer" style={{ borderColor: B.gray200 }}>
              <Upload size={24} style={{ color: B.gray400 }} />
              <p className="text-sm" style={{ color: B.gray400 }}>Kéo thả tệp hoặc nhấn để chọn</p>
              <p className="text-xs" style={{ color: B.gray400 }}>PDF, DOCX, XLSX, PNG · Tối đa 10MB</p>
            </div>
          </div>
          <div className="flex items-center justify-between p-4 rounded-xl" style={{ background: B.light }}>
            <div>
              <p className="text-sm font-semibold" style={{ color: B.navy }}>Gửi thông báo email khi tạo task</p>
              <p className="text-xs mt-0.5" style={{ color: B.gray400 }}>Thông báo tới người thực hiện và quản lý</p>
            </div>
            <Toggle on={true} />
          </div>
        </div>

        <div className="px-6 py-4 border-t flex items-center justify-end gap-3" style={{ borderColor: B.gray200 }}>
          <button onClick={onClose} className="px-5 py-2.5 rounded-xl text-sm font-semibold border transition-colors hover:bg-gray-50"
            style={{ borderColor: B.gray200, color: B.gray700 }}>Hủy</button>
          <button className="px-5 py-2.5 rounded-xl text-sm font-semibold text-white" style={{ background: B.blue }}>
            Tạo công việc
          </button>
        </div>
      </div>
    </div>
  );
}

// ─── TASK DETAIL ──────────────────────────────────────────────────
function TaskDetailScreen() {
  const task = TASKS[0];
  const [tab, setTab] = useState("details");
  const tabs = [{ k: "details", l: "Chi tiết" }, { k: "files", l: "Phụ lục & File" }, { k: "subtasks", l: "Checklist con" }];

  const subtasks = [
    { id: 1, title: "Thu thập yêu cầu từ các phòng ban",   done: true,  av: "NA" },
    { id: 2, title: "Soạn thảo tài liệu quy trình mới",   done: true,  av: "TB" },
    { id: 3, title: "Review và phê duyệt nội bộ",          done: false, av: "NA" },
    { id: 4, title: "Trình duyệt Ban Giám đốc",            done: false, av: "HE" },
  ];

  const activity = [
    { av: "HE", user: "Hoàng Thị Em",  action: "đã tạo công việc này",                     time: "10/06/2025 09:15" },
    { av: "HE", user: "Hoàng Thị Em",  action: "đã giao cho Nguyễn Văn An",                time: "10/06/2025 10:30" },
    { av: "NA", user: "Nguyễn Văn An", action: "cập nhật trạng thái → Đang làm",           time: "12/06/2025 08:45" },
    { av: "TB", user: "Trần Thị Bích", action: "đính kèm Quy_trinh_v1.docx",               time: "14/06/2025 14:20" },
  ];

  return (
    <div className="space-y-4">
      <div className="flex items-center gap-1.5 text-sm" style={{ color: B.gray400 }}>
        <span>Công việc</span><ChevronRight size={14} /><span style={{ color: B.blue, fontWeight: 600 }}>WH-001</span>
      </div>

      <div className="grid grid-cols-5 gap-5">
        {/* Left */}
        <div className="col-span-3 space-y-4">
          <div className="bg-white rounded-2xl p-6" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h1 className="text-2xl font-bold mb-4" style={{ color: B.navy }}>{task.name}</h1>

            {/* Status pills */}
            <div className="flex gap-2 mb-4 flex-wrap">
              {["Chờ xử lý","Đang làm","Đang review","Hoàn thành"].map(s => (
                <button key={s} className="px-3 py-1.5 rounded-full text-xs font-semibold transition-all"
                  style={{ background: task.status === s ? B.blue : "transparent", color: task.status === s ? "white" : B.gray400, border: `1.5px solid ${task.status === s ? B.blue : B.gray200}` }}>
                  {s}
                </button>
              ))}
            </div>

            <div className="flex items-center gap-4 mb-5">
              <PriBadge priority={task.priority} />
              <div className="flex items-center gap-1.5 text-sm font-semibold" style={{ color: B.warning }}>
                <Clock size={14} /> Còn 2 ngày · {task.deadline}
              </div>
            </div>

            {/* Progress */}
            <div className="mb-5">
              <div className="flex items-center justify-between mb-1.5">
                <span className="text-xs font-semibold" style={{ color: B.gray700 }}>Tiến độ tổng thể</span>
                <span className="text-xs font-bold" style={{ color: B.blue }}>{task.progress}%</span>
              </div>
              <div className="h-2 rounded-full" style={{ background: B.light }}>
                <div className="h-2 rounded-full transition-all" style={{ width: `${task.progress}%`, background: `linear-gradient(90deg, ${B.blue}, ${B.blueHv})` }} />
              </div>
            </div>

            {/* Tabs */}
            <div className="flex border-b mb-5" style={{ borderColor: B.gray200 }}>
              {tabs.map(t => (
                <button key={t.k} onClick={() => setTab(t.k)}
                  className="px-4 py-2.5 text-sm font-semibold relative transition-colors"
                  style={{ color: tab === t.k ? B.blue : B.gray400 }}>
                  {t.l}
                  {tab === t.k && <div className="absolute bottom-0 left-0 right-0 h-0.5 rounded-t-full" style={{ background: B.blue }} />}
                </button>
              ))}
            </div>

            {tab === "details" && (
              <p className="text-sm leading-7" style={{ color: B.gray700 }}>
                Rà soát và cập nhật toàn bộ quy trình tuyển dụng nội bộ phòng Nhân sự để phù hợp với định hướng phát triển nhân lực của MobiFone trong quý 3 năm 2025. Bao gồm các bước: đăng tin tuyển dụng, sàng lọc hồ sơ, phỏng vấn vòng 1 và vòng 2, kiểm tra tham chiếu và phê duyệt chính thức từ Ban Lãnh đạo.
              </p>
            )}

            {tab === "subtasks" && (
              <div className="space-y-2">
                {subtasks.map(s => (
                  <div key={s.id} className="flex items-center gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                    <div className="w-5 h-5 rounded-md flex items-center justify-center flex-shrink-0"
                      style={{ background: s.done ? B.success : "transparent", border: `2px solid ${s.done ? B.success : B.gray200}` }}>
                      {s.done && <Check size={11} className="text-white" />}
                    </div>
                    <span className="flex-1 text-sm" style={{ color: s.done ? B.gray400 : B.gray700, textDecoration: s.done ? "line-through" : "none" }}>{s.title}</span>
                    <Av initials={s.av} size={24} color={B.blue} />
                  </div>
                ))}
              </div>
            )}

            {tab === "files" && (
              <div className="space-y-3">
                {[{ n: "Quy_trinh_tuyen_dung_v1.docx", s: "245 KB", d: "14/06/2025" }, { n: "KPI_Q3_2025.xlsx", s: "128 KB", d: "12/06/2025" }].map(f => (
                  <div key={f.n} className="flex items-center gap-3 p-3 rounded-xl border hover:bg-gray-50 transition-colors" style={{ borderColor: B.gray200 }}>
                    <div className="w-10 h-10 rounded-xl flex items-center justify-center" style={{ background: B.light }}>
                      <FileText size={18} style={{ color: B.blue }} />
                    </div>
                    <div className="flex-1">
                      <div className="text-sm font-semibold" style={{ color: B.gray700 }}>{f.n}</div>
                      <div className="text-xs mt-0.5" style={{ color: B.gray400 }}>{f.s} · {f.d}</div>
                    </div>
                    <button className="w-8 h-8 rounded-lg flex items-center justify-center hover:bg-blue-50">
                      <Download size={16} style={{ color: B.blue }} />
                    </button>
                  </div>
                ))}
              </div>
            )}
          </div>
        </div>

        {/* Right */}
        <div className="col-span-2 space-y-4">
          <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h3 className="text-sm font-bold mb-3" style={{ color: B.navy }}>Người thực hiện</h3>
            <div className="flex items-center gap-2">
              {[{ i: "NA", c: B.blue }, { i: "TB", c: B.navy }].map((a, i) => (
                <div key={i} className="relative">
                  <Av initials={a.i} size={38} color={a.c} />
                  <div className="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white" />
                </div>
              ))}
              <button className="w-9 h-9 rounded-full border-2 border-dashed flex items-center justify-center hover:border-blue-400 transition-colors" style={{ borderColor: B.gray200 }}>
                <Plus size={14} style={{ color: B.gray400 }} />
              </button>
            </div>
          </div>

          <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h3 className="text-sm font-bold mb-3" style={{ color: B.navy }}>Thông tin</h3>
            <div className="space-y-0">
              {([["Mã công việc", "WH-001", true], ["Tạo bởi", "Hoàng Thị Em", false], ["Ngày tạo", "10/06/2025 09:15", false], ["Cập nhật cuối", "14/06/2025 14:20", false], ["Phòng ban", "Nhân sự", false]] as const).map(([k, v, mono]) => (
                <div key={k} className="flex items-center justify-between py-2 border-b last:border-0" style={{ borderColor: "#F3F4F6" }}>
                  <span className="text-xs" style={{ color: B.gray400 }}>{k}</span>
                  <span className={`text-xs font-semibold ${mono ? "font-mono" : ""}`} style={{ color: B.gray700 }}>{v}</span>
                </div>
              ))}
            </div>
          </div>

          <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h3 className="text-sm font-bold mb-4" style={{ color: B.navy }}>Hoạt động</h3>
            <div className="space-y-4 mb-4">
              {activity.map((a, i) => (
                <div key={i} className="flex gap-3">
                  <Av initials={a.av} size={28} color={MEMBERS.find(m => m.avatar === a.av)?.color ?? B.blue} />
                  <div>
                    <p className="text-xs" style={{ color: B.gray700 }}>
                      <span className="font-bold">{a.user}</span> {a.action}
                    </p>
                    <p className="text-xs mt-0.5 font-mono" style={{ color: B.gray400 }}>{a.time}</p>
                  </div>
                </div>
              ))}
            </div>
            <div className="flex gap-2">
              <Av initials="HE" size={28} color={B.navy} />
              <div className="flex-1 relative">
                <input className="w-full px-3 py-2 text-sm border rounded-xl outline-none pr-9" style={{ borderColor: B.gray200 }} placeholder="Thêm bình luận..." />
                <button className="absolute right-2.5 top-1/2 -translate-y-1/2">
                  <Send size={14} style={{ color: B.blue }} />
                </button>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// ─── MEMBERS ─────────────────────────────────────────────────────
function MembersScreen() {
  const [search, setSearch] = useState("");
  const [showPanel, setShowPanel] = useState(false);

  const filtered = MEMBERS.filter(m =>
    m.name.toLowerCase().includes(search.toLowerCase()) ||
    m.email.toLowerCase().includes(search.toLowerCase())
  );

  const rolePill = (role: string) => {
    if (role === "Admin")      return { bg: B.navy,  text: "white" };
    if (role === "Quản lý")   return { bg: B.blue,  text: "white" };
    return { bg: "transparent", text: B.gray700 };
  };

  return (
    <div className="space-y-5 relative">
      <div className="flex items-center justify-between">
        <div className="flex items-center gap-3">
          <h1 className="text-2xl font-bold" style={{ color: B.navy }}>Quản lý Thành viên</h1>
          <span className="px-2.5 py-0.5 rounded-full text-sm font-bold" style={{ background: B.light, color: B.blue }}>{MEMBERS.length} người</span>
        </div>
        <button onClick={() => setShowPanel(true)}
          className="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all hover:-translate-y-0.5"
          style={{ background: B.blue, boxShadow: `0 4px 16px ${B.blue}40` }}>
          <Plus size={16} /> Thêm thành viên
        </button>
      </div>

      <div className="flex gap-3 flex-wrap">
        <div className="relative">
          <Search size={15} className="absolute left-3 top-1/2 -translate-y-1/2" style={{ color: B.gray400 }} />
          <input value={search} onChange={e => setSearch(e.target.value)}
            className="pl-9 pr-4 py-2.5 text-sm border rounded-xl outline-none w-64"
            style={{ borderColor: B.gray200 }} placeholder="Tìm kiếm thành viên..." />
        </div>
        <select className="px-3 py-2.5 text-sm border rounded-xl outline-none bg-white" style={{ borderColor: B.gray200, color: B.gray700 }}>
          <option>Tất cả vai trò</option><option>Quản lý</option><option>Nhân viên</option>
        </select>
        <select className="px-3 py-2.5 text-sm border rounded-xl outline-none bg-white" style={{ borderColor: B.gray200, color: B.gray700 }}>
          <option>Tất cả phòng ban</option><option>Nhân sự</option><option>Đào tạo</option><option>Pháp chế</option>
        </select>
      </div>

      <div className="bg-white rounded-2xl overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
        <table className="w-full">
          <thead>
            <tr style={{ background: B.gray100 }}>
              {["Nhân viên","Mã NV","Phòng ban","Chức vụ","Vai trò","CV đang giao","Ngày tham gia","Trạng thái","Thao tác"].map(h => (
                <th key={h} className="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide" style={{ color: B.gray400 }}>{h}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {filtered.map(m => {
              const rp = rolePill(m.role);
              return (
                <tr key={m.id} className="border-t border-gray-50 hover:bg-blue-50/50 transition-colors">
                  <td className="px-4 py-4">
                    <div className="flex items-center gap-3">
                      <div className="relative">
                        <Av initials={m.avatar} size={40} color={m.color} />
                        {m.status === "active" && <div className="absolute bottom-0 right-0 w-2.5 h-2.5 rounded-full bg-green-500 border-2 border-white" />}
                      </div>
                      <div>
                        <div className="text-sm font-bold" style={{ color: B.gray700 }}>{m.name}</div>
                        <div className="text-xs" style={{ color: B.gray400 }}>{m.email}</div>
                      </div>
                    </div>
                  </td>
                  <td className="px-4 py-4 text-xs font-mono" style={{ color: B.gray400 }}>{m.id}</td>
                  <td className="px-4 py-4 text-sm" style={{ color: B.gray700 }}>{m.dept}</td>
                  <td className="px-4 py-4 text-sm" style={{ color: B.gray700 }}>{m.position}</td>
                  <td className="px-4 py-4">
                    <span className="px-2.5 py-0.5 rounded-full text-xs font-semibold border"
                      style={{ background: rp.bg, color: rp.text, borderColor: m.role === "Nhân viên" ? B.gray200 : rp.bg }}>
                      {m.role}
                    </span>
                  </td>
                  <td className="px-4 py-4">
                    <span className="px-2.5 py-0.5 rounded-full text-xs font-bold" style={{ background: B.light, color: B.blue }}>{m.tasks}</span>
                  </td>
                  <td className="px-4 py-4 text-xs" style={{ color: B.gray400 }}>{m.joined}</td>
                  <td className="px-4 py-4">
                    <div className="flex items-center gap-1.5">
                      <div className="w-2 h-2 rounded-full" style={{ background: m.status === "active" ? B.success : B.gray400 }} />
                      <span className="text-xs font-semibold" style={{ color: m.status === "active" ? B.success : B.gray400 }}>
                        {m.status === "active" ? "Hoạt động" : "Vô hiệu"}
                      </span>
                    </div>
                  </td>
                  <td className="px-4 py-4">
                    <div className="flex items-center gap-1">
                      <button className="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50" title="Chỉnh sửa"><Edit size={13} style={{ color: B.blue }} /></button>
                      <button className="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-blue-50" title="Phân quyền"><Shield size={13} style={{ color: B.blue }} /></button>
                      <button className="w-7 h-7 rounded-lg flex items-center justify-center hover:bg-red-50" title="Vô hiệu hóa"><AlertCircle size={13} style={{ color: B.danger }} /></button>
                    </div>
                  </td>
                </tr>
              );
            })}
          </tbody>
        </table>
        <div className="px-5 py-3 border-t flex items-center justify-between" style={{ borderColor: B.gray200 }}>
          <div className="flex items-center gap-2 text-sm" style={{ color: B.gray400 }}>
            Hiển thị <select className="px-2 py-1 border rounded-lg text-xs" style={{ borderColor: B.gray200 }}><option>10</option><option>25</option><option>50</option></select> mục
          </div>
          <div className="flex gap-1">
            {[1,2,3].map(p => (
              <button key={p} className="w-8 h-8 rounded-lg text-sm font-semibold"
                style={{ background: p === 1 ? B.blue : "transparent", color: p === 1 ? "white" : B.gray400 }}>{p}</button>
            ))}
          </div>
        </div>
      </div>

      {/* Add member panel */}
      {showPanel && (
        <div className="fixed inset-0 z-50 flex justify-end" style={{ background: "rgba(0,0,0,0.3)" }} onClick={() => setShowPanel(false)}>
          <div className="w-96 bg-white h-full overflow-y-auto shadow-2xl" onClick={e => e.stopPropagation()}>
            <div className="px-6 py-4 border-b flex items-center justify-between" style={{ borderColor: B.gray200 }}>
              <h2 className="text-lg font-bold" style={{ color: B.navy }}>Thêm thành viên mới</h2>
              <button onClick={() => setShowPanel(false)}><X size={18} style={{ color: B.gray400 }} /></button>
            </div>
            <div className="p-6 space-y-4">
              {(["Họ và tên *","Mã nhân viên *","Email công ty *"] as const).map(l => (
                <div key={l}>
                  <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>{l}</label>
                  <input className="w-full px-4 py-2.5 border rounded-xl text-sm outline-none" style={{ borderColor: B.gray200 }} />
                </div>
              ))}
              {(["Phòng ban","Chức vụ"] as const).map(l => (
                <div key={l}>
                  <label className="block text-sm font-semibold mb-1.5" style={{ color: B.gray700 }}>{l}</label>
                  <select className="w-full px-4 py-2.5 border rounded-xl text-sm outline-none bg-white" style={{ borderColor: B.gray200 }}>
                    <option>— Chọn {l} —</option>
                  </select>
                </div>
              ))}
              <div>
                <label className="block text-sm font-semibold mb-2" style={{ color: B.gray700 }}>Vai trò hệ thống</label>
                {[["Admin","Toàn quyền hệ thống"],["Quản lý","Tạo và giao task, theo dõi nhóm"],["Nhân viên","Nhận và cập nhật task của mình"]].map(([r,d]) => (
                  <label key={r} className="flex items-start gap-3 p-3 rounded-xl mb-2 cursor-pointer hover:bg-gray-50 border" style={{ borderColor: B.gray200 }}>
                    <input type="radio" name="role" className="mt-0.5" />
                    <div>
                      <div className="text-sm font-semibold" style={{ color: B.gray700 }}>{r}</div>
                      <div className="text-xs" style={{ color: B.gray400 }}>{d}</div>
                    </div>
                  </label>
                ))}
              </div>
              <div className="flex items-center justify-between p-4 rounded-xl" style={{ background: B.light }}>
                <div>
                  <p className="text-sm font-semibold" style={{ color: B.navy }}>Gửi email mời tham gia</p>
                  <p className="text-xs mt-0.5" style={{ color: B.gray400 }}>Email onboarding tự động</p>
                </div>
                <Toggle on={true} />
              </div>
            </div>
            <div className="px-6 py-4 border-t" style={{ borderColor: B.gray200 }}>
              <button className="w-full py-3 rounded-xl text-sm font-bold text-white" style={{ background: B.blue }}>Gửi lời mời</button>
            </div>
          </div>
        </div>
      )}
    </div>
  );
}

// ─── ROLES & PERMISSIONS ──────────────────────────────────────────
function RolesScreen() {
  const roles = [
    { title: "Quản trị viên", sub: "Admin",       icon: Crown,    color: B.navy, perms: [
      {t:"Toàn quyền hệ thống",ok:true},{t:"Thêm/xóa thành viên",ok:true},{t:"Phân quyền người dùng",ok:true},
      {t:"Xem báo cáo tổng hợp",ok:true},{t:"Cấu hình hệ thống",ok:true},{t:"Xóa vĩnh viễn dữ liệu",ok:true},
    ]},
    { title: "Quản lý / Trưởng nhóm", sub: "Manager", icon: Briefcase, color: B.blue, perms: [
      {t:"Tạo & giao công việc",ok:true},{t:"Thêm thành viên vào task",ok:true},{t:"Theo dõi tiến độ toàn nhóm",ok:true},
      {t:"Gửi thông báo email",ok:true},{t:"Xem báo cáo nhóm",ok:true},{t:"Xóa thành viên hệ thống",ok:false},{t:"Phân quyền người dùng",ok:false},
    ]},
    { title: "Nhân viên", sub: "Staff", icon: User, color: "#6B7280", perms: [
      {t:"Xem task được giao",ok:true},{t:"Cập nhật trạng thái task",ok:true},{t:"Upload tài liệu đính kèm",ok:true},
      {t:"Bình luận trong task",ok:true},{t:"Tạo task mới",ok:false},{t:"Xem task của người khác",ok:false},{t:"Gửi thông báo email",ok:false},
    ]},
  ];

  const actions = ["Tạo công việc","Xóa công việc","Giao task cho người khác","Xem báo cáo nhóm","Quản lý thành viên","Cấu hình hệ thống"];
  const initMatrix: Record<string, boolean[]> = {
    "Tạo công việc":              [true, true,  false],
    "Xóa công việc":              [true, false, false],
    "Giao task cho người khác":   [true, true,  false],
    "Xem báo cáo nhóm":          [true, true,  false],
    "Quản lý thành viên":         [true, false, false],
    "Cấu hình hệ thống":          [true, false, false],
  };
  const [matrix, setMatrix] = useState(initMatrix);

  const toggle = (action: string, ri: number) => {
    if (ri === 0) return; // Admin always on
    setMatrix(prev => ({ ...prev, [action]: prev[action].map((v, i) => i === ri ? !v : v) }));
  };

  return (
    <div className="space-y-6">
      <h1 className="text-2xl font-bold" style={{ color: B.navy }}>Phân quyền Hệ thống</h1>

      <div className="grid grid-cols-3 gap-5">
        {roles.map(role => (
          <div key={role.sub} className="bg-white rounded-2xl overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <div className="px-5 py-5 text-white" style={{ background: role.color }}>
              <div className="flex items-center gap-3 mb-1">
                <role.icon size={22} />
                <span className="font-bold text-lg">{role.title}</span>
              </div>
              <p className="text-xs opacity-60 uppercase tracking-widest">{role.sub}</p>
            </div>
            <div className="p-5 space-y-2.5">
              {role.perms.map(p => (
                <div key={p.t} className="flex items-center gap-2.5">
                  <div className="w-4 h-4 rounded-full flex items-center justify-center flex-shrink-0"
                    style={{ background: p.ok ? `${B.success}20` : "#FEE2E2" }}>
                    {p.ok
                      ? <Check size={9} style={{ color: B.success }} />
                      : <X    size={9} style={{ color: B.danger  }} />
                    }
                  </div>
                  <span className="text-sm" style={{ color: p.ok ? B.gray700 : B.gray400 }}>{p.t}</span>
                </div>
              ))}
            </div>
          </div>
        ))}
      </div>

      {/* Permission matrix */}
      <div className="bg-white rounded-2xl overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
        <div className="px-5 py-4 border-b" style={{ borderColor: B.gray200 }}>
          <h2 className="font-bold" style={{ color: B.navy }}>Tuỳ chỉnh phân quyền chi tiết</h2>
        </div>
        <table className="w-full">
          <thead>
            <tr style={{ background: B.gray100 }}>
              <th className="px-5 py-3 text-left text-xs font-semibold uppercase tracking-wide" style={{ color: B.gray400 }}>Hành động</th>
              {["Admin","Quản lý","Nhân viên"].map(r => (
                <th key={r} className="px-5 py-3 text-center text-xs font-semibold uppercase tracking-wide" style={{ color: B.gray400 }}>{r}</th>
              ))}
            </tr>
          </thead>
          <tbody>
            {actions.map(action => (
              <tr key={action} className="border-t border-gray-50">
                <td className="px-5 py-3.5 text-sm" style={{ color: B.gray700 }}>{action}</td>
                {[0,1,2].map(ri => (
                  <td key={ri} className="px-5 py-3.5 text-center">
                    <button onClick={() => toggle(action, ri)}
                      className="w-10 h-5 rounded-full relative transition-colors mx-auto block"
                      style={{ background: matrix[action][ri] ? B.blue : "#D1D5DB", cursor: ri === 0 ? "not-allowed" : "pointer" }}>
                      <div className="absolute top-0.5 w-4 h-4 rounded-full bg-white shadow transition-all"
                        style={{ left: matrix[action][ri] ? "calc(100% - 18px)" : "2px" }} />
                    </button>
                  </td>
                ))}
              </tr>
            ))}
          </tbody>
        </table>
      </div>
    </div>
  );
}

// ─── NOTIFICATIONS ────────────────────────────────────────────────
function NotificationsScreen() {
  const [activeTab, setActiveTab] = useState("all");
  const tabs = [
    { k: "all",      l: "Tất cả"       },
    { k: "unread",   l: "Chưa đọc", ct: 2 },
    { k: "task",     l: "Task giao"    },
    { k: "deadline", l: "Nhắc deadline"},
  ];

  const typeConf: Record<string, { color: string; bg: string; icon: React.ElementType }> = {
    task:     { color: B.blue,    bg: "#EFF6FF", icon: CheckSquare  },
    overdue:  { color: B.danger,  bg: "#FEF2F2", icon: AlertCircle  },
    deadline: { color: B.warning, bg: "#FFFBEB", icon: Clock        },
    complete: { color: B.success, bg: "#F0FDF4", icon: CheckCircle  },
  };

  const filtered = activeTab === "unread"   ? NOTIFS.filter(n => !n.read) :
                   activeTab === "task"     ? NOTIFS.filter(n => n.type === "task") :
                   activeTab === "deadline" ? NOTIFS.filter(n => n.type === "deadline") : NOTIFS;

  return (
    <div className="space-y-5">
      <h1 className="text-2xl font-bold" style={{ color: B.navy }}>Thông báo & Email</h1>
      <div className="grid grid-cols-5 gap-5">
        {/* Left: notification list */}
        <div className="col-span-3 bg-white rounded-2xl shadow-sm overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <div className="px-5 py-3 border-b flex items-center justify-between" style={{ borderColor: B.gray200 }}>
            <div className="flex gap-1">
              {tabs.map(t => (
                <button key={t.k} onClick={() => setActiveTab(t.k)}
                  className="flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold transition-all"
                  style={{ background: activeTab === t.k ? B.blue : "transparent", color: activeTab === t.k ? "white" : B.gray400 }}>
                  {t.l}
                  {t.ct && activeTab !== t.k && (
                    <span className="px-1.5 py-0.5 rounded-full text-white font-bold" style={{ background: B.red, fontSize: 9 }}>{t.ct}</span>
                  )}
                </button>
              ))}
            </div>
            <button className="text-xs font-semibold" style={{ color: B.blue }}>Đánh dấu tất cả đã đọc</button>
          </div>
          <div className="divide-y divide-gray-50">
            {filtered.map(n => {
              const conf = typeConf[n.type] ?? typeConf.task;
              const Icon = conf.icon;
              return (
                <div key={n.id}
                  className="flex gap-4 p-4 hover:bg-gray-50 transition-colors cursor-pointer"
                  style={{ background: !n.read ? B.light : "white", borderLeft: `3px solid ${!n.read ? B.blue : "transparent"}` }}>
                  <div className="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0" style={{ background: conf.bg }}>
                    <Icon size={18} style={{ color: conf.color }} />
                  </div>
                  <div className="flex-1 min-w-0">
                    <p className="text-sm font-bold" style={{ color: B.navy }}>{n.title}</p>
                    <p className="text-xs mt-0.5 truncate" style={{ color: B.gray400 }}>{n.desc}</p>
                    <p className="text-xs mt-1 font-mono" style={{ color: B.gray400 }}>{n.time}</p>
                  </div>
                  {!n.read && <div className="w-2.5 h-2.5 rounded-full mt-1.5 flex-shrink-0" style={{ background: B.blue }} />}
                </div>
              );
            })}
          </div>
        </div>

        {/* Right: email preview */}
        <div className="col-span-2 space-y-4">
          <div className="bg-white rounded-2xl overflow-hidden" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <div className="px-5 py-4 border-b" style={{ borderColor: B.gray200 }}>
              <h3 className="font-bold mb-3" style={{ color: B.navy }}>Xem trước email thông báo</h3>
              <select className="w-full px-3 py-2 text-sm border rounded-xl outline-none bg-white" style={{ borderColor: B.gray200 }}>
                <option>Task được giao</option><option>Deadline sắp đến</option><option>Task quá hạn</option>
              </select>
            </div>
            <div className="p-4">
              <div className="border rounded-xl overflow-hidden text-xs" style={{ borderColor: B.gray200 }}>
                <div className="px-4 py-2.5 border-b space-y-0.5" style={{ borderColor: B.gray200, background: B.gray100 }}>
                  <p style={{ color: B.gray400 }}>Đến: <span style={{ color: B.gray700 }}>nv001@mobifone.vn</span></p>
                  <p style={{ color: B.gray400 }}>Tiêu đề: <span style={{ color: B.gray700 }}>[WorkHub] Bạn được giao công việc mới</span></p>
                </div>
                <div className="py-3 px-4 text-white text-center text-sm font-bold" style={{ background: B.blue }}>
                  🔷 MobiFone WorkHub
                </div>
                <div className="p-4 space-y-3">
                  <p className="text-sm" style={{ color: B.gray700 }}>Xin chào <strong>Nguyễn Văn An</strong>,</p>
                  <p style={{ color: B.gray400 }}>Bạn được giao công việc mới trong hệ thống WorkHub:</p>
                  <div className="p-3 rounded-xl border" style={{ background: B.gray100, borderColor: B.gray200 }}>
                    <p className="font-bold" style={{ color: B.navy }}>WH-001: Cập nhật quy trình tuyển dụng Q3</p>
                    <p className="mt-1" style={{ color: B.gray400 }}>Deadline: 25/07/2025 · Ưu tiên: Cao</p>
                  </div>
                  <button className="w-full py-2 rounded-xl text-white font-semibold" style={{ background: B.blue }}>
                    Xem chi tiết công việc →
                  </button>
                </div>
                <div className="p-3 border-t text-center space-y-0.5" style={{ borderColor: B.gray200 }}>
                  <p style={{ color: B.gray400 }}>© 2025 MobiFone · 59 Lý Thường Kiệt, Hà Nội</p>
                  <p style={{ color: B.blue }}>Hủy đăng ký thông báo</p>
                </div>
              </div>
            </div>
          </div>

          <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <h3 className="font-bold mb-4" style={{ color: B.navy }}>Cài đặt thông báo email</h3>
            <div className="space-y-3.5">
              {([["Được giao công việc",true],["Deadline sắp đến (1 ngày)",true],["Công việc quá hạn",true],["Có bình luận mới",false],["Công việc hoàn thành",false]] as const).map(([l, on]) => (
                <div key={l} className="flex items-center justify-between">
                  <span className="text-sm" style={{ color: B.gray700 }}>{l}</span>
                  <Toggle on={on} />
                </div>
              ))}
            </div>
          </div>
        </div>
      </div>
    </div>
  );
}

// ─── REPORTS ─────────────────────────────────────────────────────
function ReportsScreen() {
  const [range, setRange] = useState("month");

  const kpis = [
    { l: "Tổng công việc",       v: "142",     sub: "↑ +12% tháng trước", ok: true  },
    { l: "Tỷ lệ hoàn thành",    v: "72%",     sub: "↑ +5% tháng trước",  ok: true  },
    { l: "Thời gian TB hoàn thành", v: "4.2 ngày", sub: "↓ cải thiện 0.8 ngày", ok: true  },
    { l: "Tỷ lệ quá hạn",       v: "8.5%",    sub: "↓ -2% tháng trước",  ok: true  },
  ];

  return (
    <div className="space-y-6">
      <div className="flex items-center justify-between">
        <h1 className="text-2xl font-bold" style={{ color: B.navy }}>Báo cáo & Thống kê</h1>
        <div className="flex items-center gap-3">
          <div className="flex rounded-xl border overflow-hidden" style={{ borderColor: B.gray200 }}>
            {([["week","Tuần này"],["month","Tháng này"],["custom","Tuỳ chọn"]] as const).map(([v,l]) => (
              <button key={v} onClick={() => setRange(v)}
                className="px-3 py-2 text-xs font-semibold transition-colors"
                style={{ background: range === v ? B.blue : "white", color: range === v ? "white" : B.gray700 }}>
                {l}
              </button>
            ))}
          </div>
          <button className="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold border transition-colors hover:bg-gray-50" style={{ borderColor: B.gray200, color: B.gray700 }}>
            <Download size={13} /> Xuất PDF
          </button>
          <button className="flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs font-semibold border transition-colors hover:bg-gray-50" style={{ borderColor: B.gray200, color: B.gray700 }}>
            <Download size={13} /> Xuất Excel
          </button>
        </div>
      </div>

      {/* Mini KPIs */}
      <div className="grid grid-cols-4 gap-4">
        {kpis.map(k => (
          <div key={k.l} className="bg-white rounded-2xl p-4" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
            <p className="text-xs font-semibold uppercase tracking-wide mb-2" style={{ color: B.gray400 }}>{k.l}</p>
            <p className="text-3xl font-bold" style={{ color: B.navy }}>{k.v}</p>
            <p className="text-xs mt-1.5 font-semibold" style={{ color: B.success }}>{k.sub}</p>
          </div>
        ))}
      </div>

      {/* Charts row 1 */}
      <div className="grid grid-cols-3 gap-4">
        <div className="col-span-2 bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <h3 className="font-bold mb-4" style={{ color: B.navy }}>Tiến độ công việc theo tuần</h3>
          <ResponsiveContainer width="100%" height={200}>
            <LineChart data={LINE_DATA} margin={{ left: -10 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#F3F4F6" />
              <XAxis dataKey="week" tick={{ fontSize: 12, fill: B.gray400 }} axisLine={false} tickLine={false} />
              <YAxis tick={{ fontSize: 12, fill: B.gray400 }} axisLine={false} tickLine={false} />
              <Tooltip contentStyle={{ borderRadius: 12, border: "none", boxShadow: "0 8px 24px rgba(0,0,0,0.12)", fontSize: 13 }} />
              <Legend iconType="circle" iconSize={8} />
              <Line type="monotone" dataKey="assigned"  stroke={B.blue}    strokeWidth={2.5} dot={{ r: 4, fill: B.blue    }} name="Được giao"  />
              <Line type="monotone" dataKey="completed" stroke={B.success}  strokeWidth={2.5} dot={{ r: 4, fill: B.success }} name="Hoàn thành" />
            </LineChart>
          </ResponsiveContainer>
        </div>
        <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <h3 className="font-bold mb-4" style={{ color: B.navy }}>Phân bổ theo ưu tiên</h3>
          <ResponsiveContainer width="100%" height={200}>
            <BarChart data={[{n:"Cao",v:45},{n:"Trung bình",v:62},{n:"Thấp",v:35}]} layout="vertical" margin={{ left: -10 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#F3F4F6" horizontal={false} />
              <XAxis type="number" tick={{ fontSize: 11, fill: B.gray400 }} axisLine={false} tickLine={false} />
              <YAxis type="category" dataKey="n" tick={{ fontSize: 11, fill: B.gray400 }} axisLine={false} tickLine={false} width={60} />
              <Tooltip contentStyle={{ borderRadius: 12, border: "none", fontSize: 13 }} />
              <Bar dataKey="v" radius={[0, 6, 6, 0]} name="Số lượng">
                {[B.danger, B.warning, B.blue].map((color, i) => <Cell key={i} fill={color} />)}
              </Bar>
            </BarChart>
          </ResponsiveContainer>
        </div>
      </div>

      {/* Charts row 2 */}
      <div className="grid grid-cols-2 gap-4">
        <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <h3 className="font-bold mb-4" style={{ color: B.navy }}>Hiệu suất theo thành viên</h3>
          <ResponsiveContainer width="100%" height={200}>
            <BarChart data={PERF_DATA} layout="vertical" margin={{ left: 0 }}>
              <CartesianGrid strokeDasharray="3 3" stroke="#F3F4F6" horizontal={false} />
              <XAxis type="number" domain={[0, 100]} tick={{ fontSize: 11, fill: B.gray400 }} axisLine={false} tickLine={false} />
              <YAxis type="category" dataKey="name" tick={{ fontSize: 11, fill: B.gray400 }} axisLine={false} tickLine={false} width={100} />
              <Tooltip contentStyle={{ borderRadius: 12, border: "none", fontSize: 13 }} />
              <Bar dataKey="completion" stackId="a" fill={B.blue}   radius={[0, 0, 0, 0]} name="Hoàn thành %" />
              <Bar dataKey="overdue"    stackId="a" fill={B.danger} radius={[0, 4, 4, 0]} name="Quá hạn %"   />
            </BarChart>
          </ResponsiveContainer>
        </div>
        <div className="bg-white rounded-2xl p-5" style={{ boxShadow: "0 2px 8px rgba(0,61,165,0.07)" }}>
          <h3 className="font-bold mb-4" style={{ color: B.navy }}>Trạng thái công việc</h3>
          <ResponsiveContainer width="100%" height={200}>
            <PieChart>
              <Pie data={PIE_DATA} cx="45%" cy="50%" innerRadius={55} outerRadius={80} dataKey="value" strokeWidth={0}>
                {PIE_DATA.map((e, i) => <Cell key={i} fill={e.color} />)}
              </Pie>
              <Tooltip contentStyle={{ borderRadius: 12, border: "none", fontSize: 13 }} />
              <Legend iconType="circle" iconSize={9} layout="vertical" align="right" verticalAlign="middle" />
            </PieChart>
          </ResponsiveContainer>
        </div>
      </div>
    </div>
  );
}

// ─── SIDEBAR ─────────────────────────────────────────────────────
const NAV = [
  { k: "dashboard",   l: "Dashboard",    icon: LayoutDashboard                },
  { k: "tasks",       l: "Công việc",    icon: CheckSquare                    },
  { k: "members",     l: "Thành viên",   icon: Users                         },
  { k: "documents",   l: "Tài liệu",     icon: FileText                       },
  { k: "reports",     l: "Báo cáo",      icon: BarChart2                      },
  { k: "notifications",l: "Thông báo",   icon: Bell,       badge: 2           },
  { k: "roles",       l: "Phân quyền",   icon: Shield                         },
  { k: "settings",    l: "Cài đặt",      icon: Settings                       },
];

function Sidebar({ active, onNav, collapsed, onToggle, onLogout }: {
  active: string; onNav: (s: string) => void;
  collapsed: boolean; onToggle: () => void; onLogout: () => void;
}) {
  return (
    <div className="flex flex-col h-full transition-all duration-200 flex-shrink-0"
      style={{ width: collapsed ? 64 : 240, background: B.navy }}>
      {/* Logo */}
      <div className="flex items-center gap-3 px-4 py-5 border-b" style={{ borderColor: "rgba(255,255,255,0.1)" }}>
        <div className="w-8 h-8 rounded-xl bg-white/20 flex items-center justify-center flex-shrink-0">
          <span className="text-white font-black text-sm">M</span>
        </div>
        {!collapsed && (
          <div>
            <div className="text-white font-bold text-sm leading-tight">MobiFone</div>
            <div className="text-blue-300 text-xs font-semibold tracking-[0.2em]">WORKHUB</div>
          </div>
        )}
      </div>

      {/* Nav items */}
      <nav className="flex-1 py-4 space-y-0.5 px-2 overflow-y-auto">
        {NAV.map(({ k, l, icon: Icon, badge }) => {
          const isA = active === k;
          return (
            <button key={k} onClick={() => onNav(k)} title={collapsed ? l : undefined}
              className="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm font-semibold transition-all relative"
              style={{ background: isA ? B.blue : "transparent", color: isA ? "white" : "rgba(255,255,255,0.55)" }}>
              <Icon size={18} className="flex-shrink-0" />
              {!collapsed && <span>{l}</span>}
              {badge && !collapsed && (
                <span className="ml-auto px-1.5 py-0.5 rounded-full text-xs font-bold text-white" style={{ background: B.red, fontSize: 10 }}>{badge}</span>
              )}
              {badge && collapsed && <span className="absolute top-1.5 right-1.5 w-2 h-2 rounded-full" style={{ background: B.red }} />}
            </button>
          );
        })}
      </nav>

      {/* User / collapse */}
      <div className="border-t p-3 space-y-1" style={{ borderColor: "rgba(255,255,255,0.1)" }}>
        {!collapsed && (
          <div className="flex items-center gap-3 px-2 py-2">
            <Av initials="HE" size={32} color={B.blueHv} />
            <div className="flex-1 min-w-0">
              <div className="text-white text-xs font-bold truncate">Hoàng Thị Em</div>
              <div className="text-blue-300 text-xs">Admin</div>
            </div>
            <button onClick={onLogout} title="Đăng xuất" className="text-blue-300 hover:text-white transition-colors">
              <LogOut size={15} />
            </button>
          </div>
        )}
        <button onClick={onToggle}
          className="w-full flex items-center justify-center gap-2 py-2 rounded-xl text-xs font-medium transition-colors hover:bg-white/10"
          style={{ color: "rgba(255,255,255,0.4)" }}>
          {collapsed ? <ChevronRight size={15} /> : <><ChevronLeft size={14} /> Thu gọn</>}
        </button>
      </div>
    </div>
  );
}

// ─── HEADER ──────────────────────────────────────────────────────
function Header({ screen, onBell }: { screen: string; onBell: () => void }) {
  const crumbs: Record<string, string[]> = {
    dashboard:     ["Dashboard"],
    tasks:         ["Dashboard", "Công việc"],
    task_detail:   ["Dashboard", "Công việc", "WH-001"],
    members:       ["Dashboard", "Thành viên"],
    reports:       ["Dashboard", "Báo cáo"],
    notifications: ["Dashboard", "Thông báo"],
    roles:         ["Dashboard", "Phân quyền"],
    documents:     ["Dashboard", "Tài liệu"],
    settings:      ["Dashboard", "Cài đặt"],
  };
  const bc = crumbs[screen] ?? ["Dashboard"];

  return (
    <div className="h-16 bg-white border-b flex items-center px-6 gap-4 flex-shrink-0" style={{ borderColor: B.gray200 }}>
      <div className="flex items-center gap-1 text-sm flex-1 min-w-0">
        {bc.map((c, i) => (
          <React.Fragment key={c}>
            {i > 0 && <ChevronRight size={12} style={{ color: B.gray400 }} />}
            <span style={{ color: i === bc.length - 1 ? B.navy : B.gray400, fontWeight: i === bc.length - 1 ? 700 : 400 }}>{c}</span>
          </React.Fragment>
        ))}
      </div>
      <div className="relative">
        <Search size={14} className="absolute left-3 top-1/2 -translate-y-1/2" style={{ color: B.gray400 }} />
        <input className="pl-9 pr-4 py-2 text-sm border rounded-xl outline-none w-60"
          style={{ borderColor: B.gray200, background: B.gray100 }}
          placeholder="Tìm kiếm công việc, nhân viên..." />
      </div>
      <div className="flex items-center gap-2">
        <button className="relative w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors" onClick={onBell}>
          <Bell size={18} style={{ color: B.gray700 }} />
          <span className="absolute top-1 right-1 w-4 h-4 rounded-full text-white flex items-center justify-center font-bold"
            style={{ background: B.red, fontSize: 9 }}>2</span>
        </button>
        <button className="w-9 h-9 rounded-xl flex items-center justify-center hover:bg-gray-100 transition-colors">
          <Calendar size={18} style={{ color: B.gray700 }} />
        </button>
        <Av initials="HE" size={36} color={B.blue} />
      </div>
    </div>
  );
}

// ─── APP ─────────────────────────────────────────────────────────
export default function App() {
  const [loggedIn, setLoggedIn]       = useState(false);
  const [screen, setScreen]           = useState("dashboard");
  const [collapsed, setCollapsed]     = useState(false);
  const [showModal, setShowModal]     = useState(false);

  if (!loggedIn) return <LoginScreen onLogin={() => setLoggedIn(true)} />;

  const renderScreen = () => {
    switch (screen) {
      case "dashboard":     return <DashboardScreen />;
      case "tasks":         return <KanbanScreen onOpenModal={() => setShowModal(true)} />;
      case "task_detail":   return <TaskDetailScreen />;
      case "members":       return <MembersScreen />;
      case "reports":       return <ReportsScreen />;
      case "notifications": return <NotificationsScreen />;
      case "roles":         return <RolesScreen />;
      default:
        return (
          <div className="flex flex-col items-center justify-center h-full">
            <div className="w-16 h-16 rounded-2xl flex items-center justify-center mb-4" style={{ background: B.light }}>
              <Settings size={30} style={{ color: B.blue }} />
            </div>
            <h2 className="text-xl font-bold mb-2" style={{ color: B.navy }}>Đang phát triển</h2>
            <p style={{ color: B.gray400 }}>Tính năng sẽ sớm ra mắt</p>
          </div>
        );
    }
  };

  return (
    <div className="flex h-screen overflow-hidden" style={{ fontFamily: "'Be Vietnam Pro', sans-serif", background: B.bg }}>
      <Sidebar active={screen} onNav={setScreen} collapsed={collapsed} onToggle={() => setCollapsed(c => !c)} onLogout={() => setLoggedIn(false)} />
      <div className="flex flex-col flex-1 min-w-0 overflow-hidden">
        <Header screen={screen} onBell={() => setScreen("notifications")} />
        <main className="flex-1 overflow-y-auto p-8" style={{ background: B.bg }}>
          {renderScreen()}
        </main>
      </div>
      {showModal && <CreateTaskModal onClose={() => setShowModal(false)} />}
      <style>{`
        ::-webkit-scrollbar { width: 4px; height: 4px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,61,165,0.18); border-radius: 99px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,61,165,0.35); }
      `}</style>
    </div>
  );
}
