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
        Schema::create('employees', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('enterprise_id');
            $table->string('name');
            $table->string('phone', 20);
            $table->string('email');
            $table->date('birth_date')->nullable();

            $table->timestamps();
        });
        Schema::table('employees', function (Blueprint $table) {
            $table->foreign('enterprise_id', 'fk_enterprise_id')
                ->references('id')
                ->on('enterprises')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('employees', function (Blueprint $table) {
            $table->dropForeign('fk_enterprise_id');
        });
        Schema::dropIfExists('employees');
    }
};
