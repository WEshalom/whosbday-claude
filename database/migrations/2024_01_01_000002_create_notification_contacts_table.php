<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_contacts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('person_id')->constrained('people')->cascadeOnDelete();
            $table->string('contact_name')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->enum('method', ['sms', 'email', 'both'])->default('sms');
            $table->boolean('enabled')->default(true);
            $table->timestamps();

            $table->index(['person_id', 'enabled']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_contacts');
    }
};
