<?php

namespace App\Http\Controllers;

class PageController extends Controller
{
    public function attendance()
    {
        return view('admin.layouts.pages.simple', [
            'title' => 'Chấm công',
            'subtitle' => 'Theo dõi ca làm, giờ vào ra và trạng thái đi làm của nhân viên.',
            'items' => [
                ['label' => 'Đi làm đúng giờ', 'value' => '0'],
                ['label' => 'Đi muộn', 'value' => '0'],
                ['label' => 'Chưa chấm công', 'value' => '0'],
            ],
        ]);
    }

    public function leave()
    {
        return view('admin.layouts.pages.simple', [
            'title' => 'Nghỉ phép',
            'subtitle' => 'Quản lý đơn nghỉ phép, lịch vắng mặt và trạng thái phê duyệt.',
            'items' => [
                ['label' => 'Đơn chờ duyệt', 'value' => '0'],
                ['label' => 'Đã duyệt', 'value' => '0'],
                ['label' => 'Từ chối', 'value' => '0'],
            ],
        ]);
    }

    public function report()
    {
        return view('admin.layouts.pages.simple', [
            'title' => 'Báo cáo',
            'subtitle' => 'Tổng hợp dữ liệu nhân sự, công việc và tiến độ xử lý.',
            'items' => [
                ['label' => 'Báo cáo nhân sự', 'value' => 'Sẵn sàng'],
                ['label' => 'Báo cáo công việc', 'value' => 'Sẵn sàng'],
                ['label' => 'Báo cáo tiến độ', 'value' => 'Sẵn sàng'],
            ],
        ]);
    }
}
