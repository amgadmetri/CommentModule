<?php namespace App\Modules\Comment;

use Illuminate\Database\Eloquent\Model;

class Comment extends Model {

	/**
	 * Spescify the storage table.
	 * 
	 * @var table
	 */
	protected $table    = 'comments';

	/**
	 * Specify the fields allowed for the mass assignment.
	 * 
	 * @var fillable
	 */
	protected $fillable = ['item_id', 'item_type', 'name', 'email', 'comment_title', 'comment_content', 'status', 'parent_id', 'user_id' ,'ip_address', 'edited', 'ip_token'];


	/**
	 * Get the comment replies.
	 * 
	 * @return collection
	 */
	public function replies()
    {
        return $this->hasMany('App\Modules\Comment\Comment', 'parent_id');
    }


	public static function boot()
	{
		parent::boot();

		Comment::deleting(function($comment)
		{
			/**
			 * Remove the replies related to the deleted comment.
			 */
			$comment->replies()->delete();
		});
	}
}
