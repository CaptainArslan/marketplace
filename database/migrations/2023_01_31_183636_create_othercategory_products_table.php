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
        Schema::create('othercategory_products', function (Blueprint $table) {
            $table->id();
            $table->foreignId("othercategory_id")->constrained("othercategories");
            $table->foreignId("product_id")->constrained("products");





        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('othercategory_products');
    }
};
