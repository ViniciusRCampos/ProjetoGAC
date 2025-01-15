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
            $table->dateTime('fulfilled_at');
            $table->timestamps();
        
            $table->foreignId('action_id')->constrained()->onDelete('restrict')->index();
            $table->foreignId('from_account_id')->nullable()->constrained('accounts')->onDelete('restrict')->index();
            $table->foreignId('to_account_id')->constrained('accounts')->onDelete('restrict')->index();
            $table->unsignedBigInteger('origin_operation_id')->nullable();
            $table->foreign('origin_operation_id')
                ->references('id')
                ->on('operation_logs')
                ->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('transfer_logs');
    }
};
