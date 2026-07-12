<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (!Schema::hasColumn('documents', 'review_status')) {
                $table->string('review_status')->default('director_visible')->after('disk');
            }

            if (!Schema::hasColumn('documents', 'forwarded_by')) {
                $table->unsignedBigInteger('forwarded_by')->nullable()->after('review_status');
            }

            if (!Schema::hasColumn('documents', 'forwarded_at')) {
                $table->timestamp('forwarded_at')->nullable()->after('forwarded_by');
            }
        });
    }

    public function down()
    {
        Schema::table('documents', function (Blueprint $table) {
            if (Schema::hasColumn('documents', 'forwarded_at')) {
                $table->dropColumn('forwarded_at');
            }

            if (Schema::hasColumn('documents', 'forwarded_by')) {
                $table->dropColumn('forwarded_by');
            }

            if (Schema::hasColumn('documents', 'review_status')) {
                $table->dropColumn('review_status');
            }
        });
    }
};
