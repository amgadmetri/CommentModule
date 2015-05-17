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
		\CMS::coreModuleSettings()->insert([
				[
				'key'           => 'Allow Unregisterd User To Comment',
				'value'         => serialize(['True']),
				'input_type'    => 'select',
				'select_values' => serialize(['True', 'False']),
				'module_key'    => 'comment',
				],
				[
				'key'           => 'Allow Comment Approval',
				'value'         => serialize(['True']),
				'input_type'    => 'select',
				'select_values' => serialize(['True', 'False']),
				'module_key'    => 'comment',
				],
			]);
	}

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function down()
	{
		\CMS::coreModuleSettings()->delete('comment', 'module_key');
	}
}