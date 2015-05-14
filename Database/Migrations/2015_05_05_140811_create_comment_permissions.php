<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCommentPermissions extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		foreach (\CMS::CoreModuleParts()->getModuleParts('comment') as $modulePart) 
		{
			\CMS::permissions()->insertDefaultItemPermissions(
				                 $modulePart->part_key, 
				                 $modulePart->id, 
				                 [
					                 'admin'   => ['ApproveComments'],
					                 'manager' => ['ApproveComments']
				                 ]);
		}
	}

	/**
	 * Reverse the migration.
	 *
	 * @return void
	 */
	public function down()
	{
		foreach (\CMS::CoreModuleParts()->getModuleParts('comment') as $modulePart) 
		{
			\CMS::deleteItemPermissions($modulePart->part_key);
		}
	}
}