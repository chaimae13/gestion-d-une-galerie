<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up()
   {
       Schema::create('photos', function (Blueprint $table) {
           $table->id();
           $table->string('filename');
           $table->string('path');
           $table->string('cloud_url');
           $table->unsignedBigInteger('user_id');
           $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');

            $table->unsignedBigInteger('theme_id');
           $table->foreign('theme_id')->references('id')->on('themes')->onDelete('cascade');
           $table->timestamps();
       });
   }

   public function down()
   {
       Schema::dropIfExists('photos');
   }
};
