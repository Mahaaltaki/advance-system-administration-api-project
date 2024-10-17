<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('task_dependencies', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('task_id'); // المهمة الرئيسية
            $table->unsignedBigInteger('dependent_task_id'); // المهمة التابعة
            $table->foreign('task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->foreign('dependent_task_id')->references('id')->on('tasks')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('task_dependencies');
    }
};
