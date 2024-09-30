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
        Schema::create('message_attachments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger("message_id")->nullable();
            $table->foreign("message_id")->references("id")->on("messages")->onUpdate("CASCADE")->onDelete("CASCADE");
            $table->text("name")->nullable();
            $table->string("type", 255)->nullable();
            $table->text("path")->nullable();
            $table->double("size")->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('message_attachments');
    }
};
