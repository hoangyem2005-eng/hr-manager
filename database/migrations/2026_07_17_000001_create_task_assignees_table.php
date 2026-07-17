<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('task_assignees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('task_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['task_id', 'user_id']);
        });

        DB::table('tasks')
            ->whereNotNull('assigned_to')
            ->orderBy('id')
            ->select(['id', 'assigned_to'])
            ->chunk(100, function ($tasks) {
                foreach ($tasks as $task) {
                    DB::table('task_assignees')->insertOrIgnore([
                        'task_id' => $task->id,
                        'user_id' => $task->assigned_to,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            });
    }

    public function down(): void
    {
        Schema::dropIfExists('task_assignees');
    }
};
