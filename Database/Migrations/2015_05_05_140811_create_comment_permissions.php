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
		foreach (\InstallationRepository::getModuleParts('comment') as $modulePart) 
		{
			\AclRepository::insertDefaultItemPermissions(
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
		foreach (\InstallationRepository::getModuleParts('comment') as $modulePart) 
		{
			\AclRepository::deleteItemPermissions($modulePart->part_key);
		}
	}
}