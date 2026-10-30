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
        Illuminate\Support\Facades\DB::table('tasks')
            ->whereIn('status', ['Đang review', 'Ä ang review', 'Ä‘ang review'])
            ->update(['status' => 'Chờ đánh giá']);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Illuminate\Support\Facades\DB::table('tasks')
            ->where('status', 'Chờ đánh giá')
            ->update(['status' => 'Đang review']);
    }
};
