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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->foreignId("author_id")->constrained("authors")->onDelete("cascade");
            $table->foreignId("genre_id")->nullable()->constrained("genres")->onDelete("set null");
            $table->date("publish_date");
            $table->string("cover_image")->nullable();
            $table->longText("summury")->nullable();
            $table->double("price");
            $table->double("discount")->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
