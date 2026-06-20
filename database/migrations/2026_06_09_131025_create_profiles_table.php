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
    public function up(): void
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('employee_code')->unique(); // Mã nhân viên MobiFone (Ví dụ: MBF-2026)
            $table->string('department'); // Phòng ban (TT CNTT, Phòng Nhân Sự,...)
            $table->string('phone_number')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->string('avatar')->nullable(); // Đường dẫn ảnh đại diện
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
        Schema::dropIfExists('profiles');
    }
};
