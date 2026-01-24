<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['Lift', 'Chiller']);
            $table->string('equipment'); // lift or chiller name
            $table->foreignId('lift_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('chiller_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('frequency', ['weekly', 'monthly']);
            $table->foreignId('worker_id')->nullable()->constrained()->onDelete('set null');
            $table->enum('status', ['pending', 'inprogress', 'completed'])->default('pending');
            $table->text('proof')->nullable(); // base64 image
            $table->date('scheduled_date')->nullable();
            $table->time('scheduled_time')->nullable();
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
