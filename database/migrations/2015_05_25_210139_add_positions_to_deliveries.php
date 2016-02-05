<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPositionsToDeliveries extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('deliveries', function(Blueprint $table)
		{
			$table->string('sender_position');
			$table->string('recipient_position');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('deliveries', function(Blueprint $table)
		{
	    $table->dropColumn('sender_position');
	    $table->dropColumn('recipient_position');
		});
	}

}
