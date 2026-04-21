<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('phone')->nullable();
            $table->string('invitation_code')->unique();
            $table->enum('status', ['invited', 'confirmed', 'attended'])->default('invited');
            $table->enum('rsvp_status', ['yes', 'no', 'coming', 'pending'])->default('pending');
            $table->unsignedInteger('allowed_guests')->default(1);
            $table->decimal('money', 12, 2)->default(0);
            $table->timestamp('checked_in_at')->nullable();
            $table->uuid('qr_token')->unique();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};

