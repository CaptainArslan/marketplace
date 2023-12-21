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
        Schema::create('buyer_seller_meetings', function (Blueprint $table) {
            $table->id();
            $table->foreignId("buyer_id")->constrained("users");
            $table->foreignId("product_id")->constrained("products");
            $table->bigInteger('author_id');
            $table->date('meeting_date');
            $table->time('meeting_time');
            $table->string('meeting_link')->default('wait for approval');
            $table->string('agenda');
            $table->tinyInteger('status');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('buyer_seller_meetings');
    }
};
