<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('purchase_analyses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('item_name');
            $table->decimal('monthly_allowance', 14, 2);
            $table->decimal('current_money', 14, 2);
            $table->decimal('item_price', 14, 2);
            $table->unsignedTinyInteger('need_level');
            $table->unsignedTinyInteger('days_until_allowance');
            $table->decimal('remaining_percentage', 5, 2);
            $table->decimal('score', 5, 2);
            $table->string('category');
            $table->text('decision');
            $table->text('recommendation');
            $table->json('result_payload')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_analyses');
    }
};
