<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            
            // ID của nhân viên sẽ nhận được thông báo này
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // (Tùy chọn) ID của công việc liên quan để khi click vào thông báo sẽ nhảy tới đúng công việc đó
            $table->foreignId('task_id')->nullable()->constrained('tasks')->onDelete('cascade');
            
            // Tiêu đề thông báo (VD: "Có công việc mới được giao")
            $table->string('title');
            
            // Nội dung chi tiết của thông báo
            $table->text('message');
            
            // Trạng thái: Đã đọc hay chưa? (Mặc định khi mới tạo là false - chưa đọc)
            $table->boolean('is_read')->default(false);
            
            $table->timestamps(); // Tự động tạo 2 cột created_at (thời gian gửi) và updated_at
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('notifications');
    }
};
