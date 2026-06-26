<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Thêm cột `progress` (0-100%) vào bảng tasks để theo dõi tiến độ chi tiết.
 * Thêm cột `disk` vào bảng documents để hỗ trợ đa disk (public / local / s3).
 */
return new class extends Migration
{
    public function up(): void
    {
        // --- 1. Thêm cột progress vào tasks ---
        Schema::table('tasks', function (Blueprint $table) {
            // Tiến độ % từ 0-100, mặc định 0 (chưa bắt đầu)
            $table->unsignedTinyInteger('progress')
                  ->default(0)
                  ->after('status')
                  ->comment('Phần trăm tiến độ hoàn thành (0–100)');
        });

        // --- 2. Thêm cột disk vào documents (nếu chưa có) ---
        if (!Schema::hasColumn('documents', 'disk')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->string('disk', 20)
                      ->default('public')
                      ->after('file_type')
                      ->comment('Disk lưu trữ: public | local | s3');
            });
        }
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropColumn('progress');
        });

        if (Schema::hasColumn('documents', 'disk')) {
            Schema::table('documents', function (Blueprint $table) {
                $table->dropColumn('disk');
            });
        }
    }
};
