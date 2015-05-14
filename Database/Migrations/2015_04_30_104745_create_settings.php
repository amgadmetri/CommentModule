<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettings extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		DB::table('core_settings')->insert([
				array(
					'key'           => 'Allow Unregisterd User To Comment',
					'value'         => serialize(['True']),
					'input_type'    => 'select',
					'select_values' => serialize(['True', 'False']),
					'module_key'    => 'comment',
					),
				array(
					'key'           => 'Allow Comment Approval',
					'value'         => serialize(['True']),
					'input_type'    => 'select',
					'select_values' => serialize(['True', 'False']),
					'module_key'    => 'comment',
					),
				]
				);
	}

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function down()
	{
		DB::table('core_settings')->where('module_key', '=', 'comment')->delete();
	}
}