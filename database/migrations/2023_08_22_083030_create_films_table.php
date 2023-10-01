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
        Schema::create('films', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('poster_image')->nullable();
            $table->string('preview_image')->nullable();
            $table->string('background_image')->nullable();
            $table->string('background_color')->nullable();
            $table->string('video_link')->nullable();
            $table->string('preview_video_link')->nullable();
            $table->text('description')->nullable();
            $table->string('director')->nullable();
            $table->json('starring')->nullable();
            $table->unsignedInteger('run_time')->nullable();
            $table->unsignedInteger('released')->nullable();
            $table->boolean('promo')->default(false);
            $table->string('status')->default(\App\Models\Film::STATUS_PENDING);
            $table->string('imdb_id');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('films');
    }
};
