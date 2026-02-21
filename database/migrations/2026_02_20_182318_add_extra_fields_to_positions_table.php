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
        Schema::table('positions', function (Blueprint $table) {

            $table->string('experience')->nullable()->after('requirements');

            $table->string('salary_range')->nullable()->after('experience');

            $table->json('skills')->nullable()->after('salary_range');

            $table->enum('status', ['active', 'inactive'])
                  ->default('active')
                  ->after('skills');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('positions', function (Blueprint $table) {

            $table->dropColumn([
                'experience',
                'salary_range',
                'skills',
                'status'
            ]);
        });
    }
};