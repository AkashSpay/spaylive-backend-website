<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('candidates', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->index();
            $table->foreignId('position_id')->constrained()->onDelete('cascade');
            $table->string('resume'); // file path
            $table->enum('status', ['pending', 'scheduled', 'accepted', 'rejected'])
                  ->default('pending');
            $table->timestamp('interview_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('candidates');
    }

};
