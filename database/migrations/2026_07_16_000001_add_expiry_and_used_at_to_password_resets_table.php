<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->timestamp('expires_at')->nullable()->after('created_at');
            $table->timestamp('used_at')->nullable()->after('expires_at');
        });
    }

    public function down()
    {
        Schema::table('password_resets', function (Blueprint $table) {
            $table->dropColumn(['expires_at', 'used_at']);
        });
    }
};
