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
        Schema::create('whatsapp_draft_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('whatsapp_draft_id')->constrained();
            $table->foreignId('product_id')->constrained();
            $table->integer('quantity');
            $table->integer('item_price');
            $table->string('item_name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('whatsapp_draft_items');
    }
};
