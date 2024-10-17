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
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('description');
            $table->enum('type',['Bug' , 'Feature', 'Improvement']);
            $table->enum('status',['open', 'InProgress', 'Completed', 'Blocked']);

            $table->enum('priority',[   'Low' , 'Medium' , 'High']);
            $table->date('due_date');
            $table->foreignId('assignedTo')->constrained('users')->cascadeOnDelete();
            
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
