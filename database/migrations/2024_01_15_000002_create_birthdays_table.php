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
        Schema::create('birthdays', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contact_id')->constrained()->cascadeOnDelete();
            $table->date('date');
            $table->integer('year')->nullable(); // nullable for when year is unknown
            $table->boolean('send_notification')->default(true);
            $table->integer('notification_days_before')->default(7);
            $table->text('gift_ideas')->nullable();
            $table->timestamps();

            $table->index('contact_id');
            $table->index('date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('birthdays');
    }
};
