<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('balance_logs', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->string('description');
            $table->dateTime('processed_at')->nullable();
            $table->timestamps();
            
            $table->foreignId('account_id')->constrained()->onDelete('restrict')->index();
            $table->foreignId('operation_id')->constrained()->onDelete('restrict')->index();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('balance_logs');
    }
};
