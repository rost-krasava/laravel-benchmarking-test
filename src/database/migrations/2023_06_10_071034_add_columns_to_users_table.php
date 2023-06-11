<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToUsersTable extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('first_name')->default('');
            $table->string('last_name')->default('');
            $table->string('profile')->default('');
            $table->tinyInteger('status')->default(1);
            $table->json('location')->nullable();
            $table->string('provider')->nullable();
            $table->string('provider_id')->nullable();
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('first_name');
            $table->dropColumn('last_name');
            $table->dropColumn('profile');
            $table->dropColumn('status');
            $table->dropColumn('location');
            $table->dropColumn('provider');
            $table->dropColumn('provider_id');
        });
    }
}
