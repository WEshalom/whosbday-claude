<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->foreignId('notification_contact_id')->constrained('notification_contacts')->cascadeOnDelete();
            $table->timestamp('sent_at');
            $table->enum('status', ['success', 'failed'])->default('success');
            $table->text('error_message')->nullable();
            $table->timestamps();

            $table->index(['person_id', 'sent_at']);
            $table->index(['status', 'sent_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
