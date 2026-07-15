# Test Cases Tải File Hồ Sơ Nhân Sự

| TC | Mục tiêu | Dữ liệu kiểm thử | Bước thực hiện | Kết quả mong đợi |
| --- | --- | --- | --- | --- |
| TC01 | Tải file PDF hợp lệ | hop_dong.pdf, 2MB | Mở Hồ sơ nhân sự, bấm Chọn file đính kèm, chọn file | Tên file hiển thị đúng trên UI |
| TC02 | Tải file DOCX hợp lệ | quyet_dinh.docx, 1MB | Chọn file DOCX | Tên file hiển thị đúng, không báo lỗi định dạng |
| TC03 | Tải file XLSX hợp lệ | bang_luong.xlsx, 3MB | Chọn file XLSX | UI chấp nhận file và hiện đúng tên |
| TC04 | Tải file ảnh JPG hợp lệ | anh_cccd.jpg, 800KB | Chọn file JPG | UI chấp nhận file ảnh |
| TC05 | Tải file ảnh PNG hợp lệ | avatar.png, 500KB | Chọn file PNG | UI chấp nhận file ảnh |
| TC06 | Từ chối file không đúng định dạng | script.exe | Chọn file EXE | Trình chọn file không ưu tiên/không chấp nhận theo accept |
| TC07 | File rỗng | empty.pdf, 0KB | Chọn file rỗng | UI vẫn hiện tên file, backend sau này cần chặn file 0KB |
| TC08 | File vượt dung lượng | hop_dong_lon.pdf, 11MB | Chọn file lớn hơn 10MB | UI hiện tên file, backend sau này cần báo lỗi vượt dung lượng |
| TC09 | Tên file có dấu tiếng Việt | ho_so_Nguyen_Van_A.pdf | Chọn file | Tên file hiển thị không vỡ chữ |
| TC10 | Tên file có ký tự đặc biệt | hdld_#2026_@a.pdf | Chọn file | Tên file hiển thị đúng, không vỡ layout |
| TC11 | Tên file rất dài | ten_file_150_ky_tu.pdf | Chọn file | Tên file tự xuống dòng, không tràn khỏi khung |
| TC12 | Chọn file rồi đổi file khác | a.pdf sau đó b.pdf | Chọn a.pdf, tiếp tục chọn b.pdf | UI cập nhật sang b.pdf |
| TC13 | Hủy chọn file | Bấm Chọn file rồi Cancel | Không chọn tệp | UI giữ trạng thái Chưa chọn file nào hoặc file trước đó theo hành vi trình duyệt |
| TC14 | Tải nhiều loại file liên tiếp | PDF, DOCX, PNG | Chọn từng file | UI cập nhật đúng tên mới mỗi lần |
| TC15 | Kiểm tra nút Lưu file khi chưa có backend | Bất kỳ file hợp lệ | Chọn file, quan sát nút Lưu file | Nút Lưu file đang disabled, không gửi request |
| TC16 | Refresh sau khi chọn file | a.pdf | Chọn file rồi refresh trang | Trang về trạng thái ban đầu, không còn tên file |
| TC17 | Responsive mobile | Màn hình 375px | Mở trang, chọn file | Nút Chọn file và tên file không bị tràn |
| TC18 | Responsive desktop | Màn hình 1366px | Mở trang, chọn file | Panel đính kèm nằm đúng cột phải, không lệch layout |
| TC19 | Kiểm tra truy cập bằng bàn phím | Không cần file | Tab đến nút Chọn file, Enter/Space | Hộp thoại chọn file mở được |
| TC20 | Kiểm tra route trang hồ sơ | URL /admin/nhanvien/hoso | Truy cập trực tiếp URL | Trang trả về 200 và hiển thị tiêu đề Hồ sơ nhân sự |
