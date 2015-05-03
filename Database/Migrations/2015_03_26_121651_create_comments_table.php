<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		if ( ! Schema::hasTable('comments'))
		{
			Schema::create('comments', function(Blueprint $table) {
				$table->bigIncrements('id');				
				$table->bigInteger('item_id');
				$table->string('item_type', 150)->index();
				$table->string('name', 150)->index();
				$table->string('email', 150)->index();
				$table->string('comment_title', 150)->index();
				$table->text('comment_content');
				$table->enum('approved', ['accepted', 'pending', 'rejected'])->default('pending')->index();
				$table->bigInteger('parent_id');
				$table->bigInteger('user_id');
				$table->string('ip_address', 45)->index();
				$table->string('ip_token', 150)->nullable()->index();
				$table->boolean('edited')->default(0);
				$table->timestamps();
			});
		}
	}

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function down()
	{
		if (Schema::hasTable('comments'))
		{
			Schema::drop('comments');
		}
	}
}