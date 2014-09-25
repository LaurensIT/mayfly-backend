<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPregenTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::Create('pregen',function($t)
		{
                        $t->engine = "memory";
			$t->increments('id');
			$t->string('public',5120);
                        $t->string('password',20);
			$t->string('private',5120);
			$t->datetime('valid_untill');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::Drop('pregen');
	}

}
