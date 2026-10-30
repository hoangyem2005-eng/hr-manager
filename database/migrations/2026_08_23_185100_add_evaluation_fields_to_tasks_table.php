<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->text('employee_note')->nullable()->after('proposal_step');
            $table->tinyInteger('evaluation_score')->unsigned()->nullable()->after('employee_note');
            $table->text('evaluation_comment')->nullable()->after('evaluation_score');
            $table->unsignedBigInteger('evaluated_by')->nullable()->after('evaluation_comment');
            $table->foreign('evaluated_by')->references('id')->on('users')->nullOnDelete();
            $table->timestamp('evaluated_at')->nullable()->after('evaluated_by');
        });
    }

    public function down(): void
    {
        Schema::table('tasks', function (Blueprint $table) {
            $table->dropForeign(['evaluated_by']);
            $table->dropColumn([
                'employee_note',
                'evaluation_score',
                'evaluation_comment',
                'evaluated_by',
                'evaluated_at',
            ]);
        });
    }
};