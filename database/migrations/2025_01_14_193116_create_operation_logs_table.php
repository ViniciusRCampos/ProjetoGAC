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
        Schema::create('operation_logs', function (Blueprint $table) {
            $table->id();
            $table->decimal('amount', 15, 2);
            $table->boolean('fulfilled')->default(false);
            $table->dateTime('fulfilled_at')->nullable();
            $table->timestamps();
        
            $table->foreignId('action_id')->constrained('operation_actions')->onDelete('restrict')->index();
            $table->foreignId('from_account_id')
                  ->nullable()
                  ->constrained('accounts', 'id')
                  ->onDelete('restrict')
                  ->index()
                  ->name('fk_from_account');
            $table->foreignId('to_account_id')
                  ->constrained('accounts', 'id')
                  ->onDelete('restrict')
                  ->index()
                  ->name('fk_to_account');
            $table->unsignedBigInteger('origin_operation_id')->nullable();
            $table->foreign('origin_operation_id')
                  ->references('id')
                  ->on('operation_logs')
                  ->onDelete('restrict')
                  ->name('fk_origin_operation');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operation_logs');
    }
};
