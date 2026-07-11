<!doctype html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <title>Cong viec qua han</title>
</head>
<body style="font-family: Arial, sans-serif; color:#111827; line-height:1.5">
    <h2 style="margin:0 0 12px;color:#E4002B">Cong viec da qua han</h2>
    <p>Xin chao {{ optional($task->assignee)->name ?? 'anh/chi' }},</p>
    <p>Cong viec <strong>{{ $task->task_name }}</strong> ({{ $code }}) da qua han hoan thanh.</p>

    <table cellpadding="8" cellspacing="0" style="border-collapse:collapse;border:1px solid #E5E7EB">
        <tr>
            <td style="border:1px solid #E5E7EB;font-weight:bold">Deadline</td>
            <td style="border:1px solid #E5E7EB">{{ optional($task->deadline)->format('d/m/Y') ?? 'Khong co' }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #E5E7EB;font-weight:bold">Trang thai</td>
            <td style="border:1px solid #E5E7EB">{{ $task->status }}</td>
        </tr>
        <tr>
            <td style="border:1px solid #E5E7EB;font-weight:bold">Tien do</td>
            <td style="border:1px solid #E5E7EB">{{ $task->progress ?? 0 }}%</td>
        </tr>
    </table>

    <p>Vui long kiem tra va cap nhat tien do tren he thong HR Manager.</p>
    <p style="color:#6B7280;font-size:12px">Email nay duoc gui tu he thong MobiFone HR.</p>
</body>
</html>
