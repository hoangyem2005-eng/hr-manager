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
        Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->onDelete('cascade'); // Thuộc đầu việc nào
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Ai là người tải lên
            $table->string('file_name'); // Tên file gốc (Báo cáo_MobiFone.pdf)
            $table->string('file_path'); // Đường dẫn mã hóa lưu an toàn trên server
            $table->string('file_type')->nullable(); // pdf, docx, xlsx...
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('documents');
    }
};
