<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    
    public function up()
    {
        Schema::create('tasks', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('assignee')->nullable();
            $table->date('due_date');
            $table->decimal('time_tracked', 10, 2)->default(0);
            $table->enum('status', ['pending', 'open', 'in_progress', 'completed'])->default('pending');
            $table->enum('priority', ['low', 'medium', 'high'])->nullable();
            $table->timestamps();
    });
}


 
    public function down(): void
    {
        Schema::dropIfExists('tasks');
    }
};
