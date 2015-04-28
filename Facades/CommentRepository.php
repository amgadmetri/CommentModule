<?php namespace App\Modules\Comment\Facades;

use Illuminate\Support\Facades\Facade;

class CommentRepository extends Facade
{
	protected static function getFacadeAccessor() { return 'CommentRepository'; }
}