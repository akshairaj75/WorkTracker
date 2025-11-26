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
        Schema::create('nested_subtasks', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('subtask_id')->constrained('subtasks')->onDelete('cascade');
            $table->string('sub_work_type');
            $table->string('sub_work_result');
            $table->text('sub_work_description')->nullable();
            $table->date('sub_work_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nested_subtasks');
    }
};
