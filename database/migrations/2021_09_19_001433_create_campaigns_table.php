<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('code');
            $table->string('status')->default('pending_payment');
            $table->string('email');
            $table->unsignedBigInteger('video_id');
            $table->json('screens')->nullable();
            $table->json('video_details')->nullable();
            $table->float('total_price')->default(0);
            $table->timestamp('campaign_from', 0)->nullable();
            $table->timestamp('campaign_to', 0)->nullable();
            $table->integer('campaign_length')->nullable();
            $table->timestamps();
            $table->foreign('video_id')->references('id')->on('videos');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
