<?php namespace App\Modules\Comment;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	protected $table    = 'comments';
	protected $fillable = ['item_id', 'item_type', 'name', 'email', 'comment_title', 'comment_content', 'approved', 'parent_id', 'user_id' ,'ip_address', 'edited', 'ip_token'];


	public function replies()
    {
        return $this->hasMany('App\Modules\Comment\Comment', 'parent_id');
    }


	public static function boot()
	{
		parent::boot();

		Comment::deleting(function($comments)
		{
			$comments->replies()->delete();
		});
	}
}
