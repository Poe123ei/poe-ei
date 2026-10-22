<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Creates `events`, one row per planned worship session.
 *
 * `dorm` is a free-text name and `session` is 'morning' or 'evening'.
 * The "one event per dorm per day" rule is enforced in StoreEventRequest,
 * not by a database index.
 *
 * TODO: In a new migration, replace `dorm` with a dorm_id foreign key and
 *       consider a unique index on (dorm_id, date).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('events', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('dorm');
            $table->date('date');
            $table->string('session');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('events');
    }
};
