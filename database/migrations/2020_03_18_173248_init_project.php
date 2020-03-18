<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class InitProject extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		$sql = __DIR__.'/_data/asum.sql';
		$db = config('database.connections.mysql');
		exec("mysql -u {$db['username']} -p{$db['password']} {$db['database']} < {$sql}");

		$sql = __DIR__.'/_data/meters_value.sql';
		$db = config('database.connections.db_meters_value');
		exec("mysql -u {$db['username']} -p{$db['password']} {$db['database']} < {$sql}");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        //
    }
}
