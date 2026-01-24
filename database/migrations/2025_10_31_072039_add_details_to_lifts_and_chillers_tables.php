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
        Schema::table('lifts', function (Blueprint $table) {
            $table->string('location')->nullable()->after('name');
            $table->string('model_number')->nullable()->after('location');
            $table->string('serial_number')->nullable()->after('model_number');
            $table->date('last_maintenance_date')->nullable()->after('serial_number');
        });

        Schema::table('chillers', function (Blueprint $table) {
            $table->string('location')->nullable()->after('name');
            $table->string('model_number')->nullable()->after('location');
            $table->string('serial_number')->nullable()->after('model_number');
            $table->date('last_maintenance_date')->nullable()->after('serial_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('lifts', function (Blueprint $table) {
            $table->dropColumn(['location', 'model_number', 'serial_number', 'last_maintenance_date']);
        });

        Schema::table('chillers', function (Blueprint $table) {
            $table->dropColumn(['location', 'model_number', 'serial_number', 'last_maintenance_date']);
        });
    }
};
