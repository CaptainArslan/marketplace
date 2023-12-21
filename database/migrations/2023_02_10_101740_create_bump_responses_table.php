<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bump_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId("bump_id")->constrained("product_bumps");
            $table->integer("pages")->default(0);
            $table->decimal('price', 16, 2);
            $table->integer("order_id");
            $table->foreignId("sell_id")->constrained("sells")->nullable();

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bump_responses');
    }
};
