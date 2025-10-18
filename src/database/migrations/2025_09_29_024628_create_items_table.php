<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('condition_id')->constrained();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->text('description');
            $table->decimal('price', 10, 0);
            $table->string('image');
            $table->boolean('is_sold')->default(false);
            $table->timestamps();
        
            $table->index(['is_sold', 'created_at']);
            $table->index('user_id');
            $table->index('condition_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('items');
    }
}
