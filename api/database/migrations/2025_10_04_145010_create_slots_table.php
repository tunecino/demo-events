<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create("slots", function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->uuid("event_id");
            /**
             * w'll just go with random hash for now.
             * TODO: use proper fk for user_id once auth is implemented.
             */
            $table->string("user_id")->nullable();
            $table->timestamp("start_at");
            $table->timestamp("end_at");
            $table->unsignedTinyInteger("status");
            $table->timestamps();
            // SQLite compatible foreign keys
            $table
                ->foreign("event_id")
                ->references("id")
                ->on("events")
                ->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists("slots");
    }
};
