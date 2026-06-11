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
        Schema::create('engineers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->string('name');
            $table->string('company_name');
            $table->integer('age');
            $table->string('gender');
            $table->string('nearest_station');
            $table->integer('desired_unit_price');
            $table->integer('experience_years');
            $table->date('available_date');
            $table->string('desired_location');
            $table->text('desired_conditions');
            $table->text('career_summary');
            $table->string('status');
            $table->timestamp('deleted_at')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('engineers');
    }
};