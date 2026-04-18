<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            $table->string('modifier')->default('na')->change();
        });
    }

    public function down(): void
    {
        Schema::table('transaction_items', function (Blueprint $table) {
            // Reverting back to enum might be tricky depending on the DB driver
            // but for SQLite/MySQL it should be fine to leave as string or try to change back.
            $table->enum('modifier', ['premium', 'standar', 'na'])->default('na')->change();
        });
    }
};
