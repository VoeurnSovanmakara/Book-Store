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
        Schema::create('customer_addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('phone');
            $table->string('note')->nullable();
            $table->enum('type', ['HOME', 'WORK', 'SCHOOL', 'OTHER']);
            $table->decimal('lat', 10, 8);
            $table->decimal('lng', 11, 8);
            $table->string('detail')->nullable();
            $table->timestamps();

            $table->index('customer_id'); // fast lookup of "all addresses for customer X"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_addresses');
    }
};
