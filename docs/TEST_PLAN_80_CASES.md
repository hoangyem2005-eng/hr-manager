# MobiFone WorkHub - Master Test Plan 80+ Cases

## Muc tieu

Kiem thu cac luong chinh cua MobiFone WorkHub: xac thuc, phan quyen, cong viec, phong ban, ho so nhan su, tai lieu dinh kem, thong bao, email qua han va bao cao.

## Pham vi

- Public: landing, login, register, forgot/reset password.
- Giam doc: dashboard, thanh vien, phong ban, cong viec, bao cao, thong bao.
- Truong phong: dieu phoi phong ban, them nhan vien, phan cong viec, chuyen file len giam doc.
- Nhan vien: workbench ca nhan, cap nhat tien do, upload file.
- He thong: schedule email qua han, storage, migration, notification.

## Test Cases

| ID | Nhom | Kich ban | Ket qua mong doi |
|---|---|---|---|
| AUTH-001 | Auth | Mo trang login | Form hien thi dung, khong loi giao dien |
| AUTH-002 | Auth | Login voi user active dung mat khau | Chuyen dung dashboard theo vai tro |
| AUTH-003 | Auth | Login sai mat khau | Bao loi, khong dang nhap |
| AUTH-004 | Auth | Login email khong ton tai | Bao loi, khong dang nhap |
| AUTH-005 | Auth | Login user inactive | Bi chan dang nhap |
| AUTH-006 | Auth | Logout | Session bi huy, ve login |
| AUTH-007 | Auth | Toggle hien mat khau login | Password doi type dung |
| AUTH-008 | Auth | Remember me | Checkbox gui request hop le |
| AUTH-009 | Auth | Mo trang register | Form hien thi dung |
| AUTH-010 | Auth | Dang ky nhan vien moi | Tao user role Nhan vien |
| AUTH-011 | Auth | Dang ky email trung | Bao loi unique |
| AUTH-012 | Auth | Dang ky thieu phong ban | Bao loi validation |
| AUTH-013 | Auth | Dang ky role khac Nhan vien | Bi chan |
| AUTH-014 | Auth | Toggle mat khau register | Password va confirm toggle dung |
| AUTH-015 | Auth | Forgot password voi email ton tai | Gui OTP/email va sang reset |
| AUTH-016 | Auth | Forgot password email khong ton tai | Bao loi |
| AUTH-017 | Auth | Reset password OTP dung | Doi mat khau thanh cong |
| AUTH-018 | Auth | Reset password OTP sai | Bao loi |
| AUTH-019 | Auth | Reset password confirm khong khop | Bao loi |
| AUTH-020 | Auth | Truy cap dashboard khi chua login | Redirect login |
| ROLE-001 | Role | Giam doc vao dashboard | Thay giao dien giam doc |
| ROLE-002 | Role | Truong phong vao dashboard | Thay giao dien dieu phoi phong |
| ROLE-003 | Role | Nhan vien vao dashboard | Thay workbench ca nhan |
| ROLE-004 | Role | Nhan vien vao trang members | Bi chan/redirect |
| ROLE-005 | Role | Nhan vien vao trang notifications quan tri | Redirect ve employee dashboard |
| ROLE-006 | Role | Truong phong vao admin dashboard | Bi redirect dung pham vi |
| ROLE-007 | Role | Giam doc xem toan bo cong viec | Duoc xem |
| ROLE-008 | Role | Truong phong chi xem viec lien quan phong minh | Khong lo viec phong khac |
| ROLE-009 | Role | Nhan vien chi xem viec cua minh | Khong lo viec nguoi khac |
| ROLE-010 | Role | Cap nhat vai tro user | Role doi thanh cong |
| TASK-001 | Task | Giam doc giao viec cho 1 user | Tao task va notification |
| TASK-002 | Task | Giam doc giao viec cho nhieu user | Tao nhieu task tuong ung |
| TASK-003 | Task | Truong phong giao viec cho nhan vien trong phong | Tao task thanh cong |
| TASK-004 | Task | Truong phong giao viec ngoai phong | Bi chan |
| TASK-005 | Task | Nhan vien gui de xuat task | Task gan cho chinh minh |
| TASK-006 | Task | Task thieu ten | Bao loi validation |
| TASK-007 | Task | Task thieu deadline voi form bat buoc | Bao loi validation |
| TASK-008 | Task | Task deadline qua khu | Bao loi neu route yeu cau today tro di |
| TASK-009 | Task | Cap nhat tien do 0% | Status phu hop |
| TASK-010 | Task | Cap nhat tien do 50% | Luu progress |
| TASK-011 | Task | Cap nhat tien do 100% | Hoan thanh |
| TASK-012 | Task | Loc task qua han | Chi thay viec qua han/chua hoan thanh |
| TASK-013 | Task | Loc task cua toi | Chi thay viec assigned_to current user |
| TASK-014 | Task | Loc theo assignee | Ket qua dung assignee |
| TASK-015 | Task | Kanban render | Cac cot hien thi dung |
| TASK-016 | Task | List render | Danh sach hien thi dung |
| TASK-017 | Task | Truong phong nhan viec tu giam doc | Hien incoming task |
| TASK-018 | Task | Truong phong delegate incoming task | Task chuyen cho nhan vien |
| TASK-019 | Task | Delegate task khong assigned_to manager | Bi chan |
| TASK-020 | Task | Notification tao khi giao/delegate | Notification chua doc |
| MEMBER-001 | Member | Mo trang thanh vien | Hien danh sach va stats |
| MEMBER-002 | Member | Tim theo ten | Ket qua dung |
| MEMBER-003 | Member | Tim theo email | Ket qua dung |
| MEMBER-004 | Member | Tim theo ma NV | Ket qua dung |
| MEMBER-005 | Member | Loc theo phong ban | Ket qua dung phong |
| MEMBER-006 | Member | Phan trang 10/user | Hien dung so muc |
| MEMBER-007 | Member | Them thanh vien | Tao user moi active |
| MEMBER-008 | Member | Them email trung | Bao loi |
| MEMBER-009 | Member | Sua ten/email/phong ban | Cap nhat thanh cong |
| MEMBER-010 | Member | Doi mat khau thanh vien | Password hash moi |
| MEMBER-011 | Member | Doi role thanh vien | Role cap nhat |
| MEMBER-012 | Member | Tu doi role chinh minh | Bi chan |
| MEMBER-013 | Member | Vo hieu hoa thanh vien | `is_active=false` |
| MEMBER-014 | Member | Tu vo hieu hoa chinh minh | Bi chan |
| DEPT-001 | Department | Mo danh sach phong ban | Hien cac phong |
| DEPT-002 | Department | Chon phong ban | Hien nhan vien phong do |
| DEPT-003 | Department | Them phong ban | Tao record |
| DEPT-004 | Department | Sua phong ban | Cap nhat record |
| DEPT-005 | Department | Xoa phong ban | Xoa/bao loi neu lien ket |
| DOC-001 | Document | Upload 1 file hop le | Tao document va luu storage |
| DOC-002 | Document | Upload nhieu file | Tao nhieu document |
| DOC-003 | Document | Upload file qua 20MB | Bao loi |
| DOC-004 | Document | Upload dinh dang khong ho tro | Bao loi |
| DOC-005 | Document | Preview PDF | Inline response dung |
| DOC-006 | Document | Preview anh | Inline response dung |
| DOC-007 | Document | Download file | Tai ve dung file name |
| DOC-008 | Document | Xoa file cua minh | Xoa storage va DB |
| DOC-009 | Document | Nhan vien upload file | Review status manager_review |
| DOC-010 | Document | Giam doc chua thay file chua forward | Bi an |
| DOC-011 | Document | Truong phong forward file | Director visible |
| DOC-012 | Document | Forward file ngoai pham vi | Bi chan |
| DOC-013 | Document | Giam doc thay file da forward | Hien thi/download duoc |
| DOC-014 | Document | File khong ton tai tren disk | Tra 404 |
| NOTIF-001 | Notification | User click notification cua minh | Mark read va redirect |
| NOTIF-002 | Notification | User click notification nguoi khac | 403 |
| NOTIF-003 | Notification | Mark all read | Tat ca unread thanh read |
| NOTIF-004 | Notification | Broadcast notification voi director | Gui den active users |
| NOTIF-005 | Notification | Employee broadcast | Bi chan |
| NOTIF-006 | Notification | Deadline sap den | Tao reminder |
| NOTIF-007 | Notification | Recent notifications employee | Van hien sau khi doc |
| EMAIL-001 | Email | Task qua han gui mail | Gui 1 email den assignee |
| EMAIL-002 | Email | Task da gui overdue_email_sent_at | Khong gui lai |
| EMAIL-003 | Email | Task hoan thanh qua han | Khong gui |
| EMAIL-004 | Email | Task khong co email assignee | Bo qua |
| EMAIL-005 | Email | Command gap loi mail | Log warning, tiep tuc |
| REPORT-001 | Report | Mo trang bao cao | Render thanh cong |
| REPORT-002 | Report | Ty le hoan thanh | Tinh theo task |
| REPORT-003 | Report | Ty le qua han | Tinh theo task |
| REPORT-004 | Report | Chart status | Co du lieu |
| REPORT-005 | Report | Performance user | Co danh sach user |
| UI-001 | UI | Landing desktop | Hero khong vo layout |
| UI-002 | UI | Landing mobile | Cac section xuong dong dung |
| UI-003 | UI | Auth desktop | Icon input can giua, heading khong bi che |
| UI-004 | UI | Auth mobile | Form dung, khong overflow |
| UI-005 | UI | Sidebar role display | Dung chuc vu khi doi nav |

## Automated Tests Hien Co

Chay:

```bash
php artisan test
```

Ket qua gan nhat: `33 passed`.

## Manual Regression Checklist

- Chay `php artisan migrate:status` truoc demo.
- Chay `php artisan storage:link` neu moi clone va `public/storage` chua ton tai.
- Cau hinh mail `.env` truoc khi test email that.
- Cau hinh scheduler/cron tren server staging: `php artisan schedule:run`.
- Test upload file that voi PDF, anh va DOCX.
- Test tren Chrome o desktop va mobile width.

