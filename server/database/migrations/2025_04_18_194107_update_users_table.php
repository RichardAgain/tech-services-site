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
        Schema::create('roles', function (Blueprint $table) {
            $table->id('id');
            $table->string('name')->unique();
        });

        Schema::table('users', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained('roles')->default(2);

            $table->renameColumn('name', 'username');
            $table->string('firstName');
            $table->string('lastName')->nullable();
            $table->string('address');
            $table->string('phone');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
