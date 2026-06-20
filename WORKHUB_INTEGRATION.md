# 🚀 MobiFone WorkHub - Tài Liệu Tích Hợp

## Tổng Quan

MobiFone WorkHub là hệ thống quản lý công việc tích hợp được xây dựng từ **demo-ui** (React + Tailwind CSS) và tích hợp vào project **hr-manager** (Laravel backend).

## 🎨 Kiến Trúc Giao Diện

### Landing Page
- **Đường dẫn:** `/workhub`
- **File:** `resources/js/pages/LandingPage.jsx`
- **Mô tả:** Trang chào mừng với các tính năng chính, lợi ích, và CTA đến dashboard
- **Design:** Đồng bộ với demo-ui (color palette: Navy #001F5B, Blue #003DA5, Success #16A34A)

### Dashboard (Task Management)
- **Đường dẫn:** `/workhub/dashboard`
- **File:** `resources/js/pages/Dashboard.tsx`
- **Mô tả:** Giao diện chính với Dashboard, Tasks, Members, Reports, Notifications, Roles

## 📁 Cấu Trúc Project

```
hr-manager/
├── resources/
│   ├── js/
│   │   ├── main.jsx                 # React entry point
│   │   ├── App.jsx                  # Main app wrapper (routing)
│   │   ├── pages/
│   │   │   ├── LandingPage.jsx      # Landing page
│   │   │   └── Dashboard.tsx        # Dashboard (from demo-ui)
│   │   └── bootstrap.js
│   ├── css/
│   │   └── app.css                  # Global styles
│   └── views/
│       └── app.blade.php            # React app wrapper
├── routes/
│   └── web.php                      # Updated routes
├── package.json                     # Updated with React deps
├── vite.config.js                   # Updated with React plugin
└── ...
```

## 🛠️ Cài Đặt & Chạy

### 1. Cài Đặt Dependencies

```bash
cd d:/mobifone/hr-manager
npm install
# hoặc
pnpm install
```

### 2. Chạy Development Server

```bash
npm run dev
```

### 3. Build cho Production

```bash
npm run build
```

## 🎯 Các Tính Năng

### Landing Page
- ✅ Header navigation
- ✅ Hero section với CTA buttons
- ✅ Feature cards (Dashboard, Kanban, Collaboration, Analytics)
- ✅ Features section (6 cards)
- ✅ Benefits section với checklist
- ✅ CTA section
- ✅ Footer

### Dashboard (Task Management)
- ✅ Login screen
- ✅ Dashboard overview với KPIs
- ✅ Recent tasks table
- ✅ Task distribution chart
- ✅ Active members sidebar
- ✅ Kanban board / List view
- ✅ Task detail screen
- ✅ Members management
- ✅ Permissions & Roles
- ✅ Notifications
- ✅ Reports & Analytics
- ✅ Sidebar navigation
- ✅ Header breadcrumbs

## 🎨 Design System

### Màu Sắc
```javascript
const B = {
  navy:    "#001F5B",    // Primary dark
  blue:    "#003DA5",    // Primary blue
  blueHv:  "#0057C8",    // Primary hover
  light:   "#E8F0FE",    // Light background
  red:     "#E4002B",    // Alert
  success: "#16A34A",    // Success
  warning: "#D97706",    // Warning
  danger:  "#DC2626",    // Danger
  bg:      "#F8F9FA",    // Page background
  gray100: "#F1F3F5",
  gray200: "#E5E7EB",
  gray400: "#9CA3AF",
  gray700: "#374151",
};
```

### Typography
- **Font:** Be Vietnam Pro (Segoe UI fallback)
- **Headings:** Font weight 700
- **Body:** Font weight 400
- **Links:** Font weight 600

### Border Radius
- **Small:** 8px
- **Medium:** 12px
- **Large:** 16px
- **Full:** 99px (badges, toggles)

## 🔄 Routing

### Public Routes
- `/login` - Login page (Laravel auth)

### Protected Routes (require auth)
- `/workhub` - Landing page (redirect to dashboard if logged in)
- `/workhub/dashboard` - Main dashboard
- `/workhub/tasks` - Task management
- `/workhub/members` - Member management
- `/workhub/reports` - Analytics & reports
- `/workhub/notifications` - Notifications
- `/workhub/roles` - Roles & permissions
- `/workhub/settings` - Settings (coming soon)

## 📊 Dữ Liệu Mock

Tất cả dữ liệu hiện tại đều là mock data định sẵn trong `Dashboard.tsx`:
- TASKS: 5 công việc mẫu
- MEMBERS: 5 thành viên mẫu
- NOTIFS: 5 thông báo mẫu
- Charts data (PIE_DATA, LINE_DATA, PERF_DATA)

## 🔗 Integration Points

### Backend (Laravel)
- Authentication middleware check
- User session management
- Future: API endpoints for real data

### Frontend (React)
- State management (local useState)
- Component composition
- Mock data display
- Future: API calls to backend

## 📱 Responsive Design

- **Desktop:** Full layout (1180px max-width)
- **Tablet:** 2-column grids reduce to 1
- **Mobile:** Single column, full width

Landing page:
- Desktop: 2-column hero
- Tablet: 1-column
- Mobile: Stacked

Dashboard:
- Responsive grid layouts
- Collapsible sidebar
- Adaptive cards

## 🚀 Deployment

### Production Build
```bash
npm run build
```

Output: `public/build/` (Vite output)

### Nginx/Apache
Serve `public/` directory as document root with Laravel routing.

## 🔐 Security Considerations

- ✅ Laravel auth middleware on all protected routes
- ✅ CSRF protection (Laravel defaults)
- ✅ XSS protection through React DOM
- 🔄 Future: API token authentication for SPA

## 📝 Next Steps

1. **Connect Backend:**
   - Create API endpoints for tasks, members, etc.
   - Replace mock data with API calls
   - Implement user authentication

2. **Enhance Features:**
   - Add real task creation/editing
   - Implement file uploads
   - Add email notifications
   - Create advanced filters/search

3. **Performance:**
   - Add code splitting
   - Optimize images
   - Implement caching

4. **Testing:**
   - Unit tests for components
   - E2E tests for user flows
   - Performance testing

## 📞 Support

For issues or questions about the integration, please refer to:
- Demo-ui source: `d:/mobifone/demo-ui/`
- HR-manager source: `d:/mobifone/hr-manager/`

---

**Last Updated:** 2025-06-20
**Version:** 2.4.1
