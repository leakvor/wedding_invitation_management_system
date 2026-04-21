<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guests', function (Blueprint $table): void {
            $table->string('invitation_slug')->unique()->nullable()->after('qr_token');
            $table->text('notes')->nullable()->after('invitation_slug');
        });
    }

    public function down(): void
    {
        Schema::table('guests', function (Blueprint $table): void {
            $table->dropColumn(['invitation_slug', 'notes']);
        });
    }
};

