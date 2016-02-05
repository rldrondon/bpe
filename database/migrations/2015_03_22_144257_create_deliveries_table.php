<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDeliveriesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('deliveries', function(Blueprint $table)
		{
			$table->increments('id');
			$table->string('tracking_code');
			$table->string('delivery_code');
			$table->string('sender_address');
			$table->string('sender_info')->nullable();
			$table->string('sender_email');
			$table->string('recipient_address');
			$table->string('recipient_info')->nullable();
			$table->string('recipient_email');
			$table->tinyInteger('state')->default(0);
			$table->integer('agent_id')->nullable();
			$table->timestamp('submission_time')->nullable();
			$table->timestamp('pickup_time')->nullable();
			$table->timestamp('delivery_time')->nullable();
			$table->dateTime('estimated_pickup')->nullable();
			$table->dateTime('estimated_delivery')->nullable();

			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::drop('deliveries');
	}

}
