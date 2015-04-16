<?php namespace App\Modules\Comment\Traits;

use App\Modules\Comment\Comment;
use Request;
trait CommentTrait{

	public function getAllComments()
	{
		return Comment::all();
	}

	public function getComments($ids)
	{
		return Comment::whereIn('id', $ids)->get();
	}

	public function getComment($id)
	{
		return Comment::find($id);
	}

	public function createComment($data)
	{
		return Comment::create($data);
	}

	public function createIpToken()
	{
		if (Request::cookie('ip_token')) 
		{
			return Request::cookie('ip_token');
		}
		else
		{
			return bcrypt(str_random(40) . uniqid() . time());
		}
	}

	public function checkIpToken()
	{
		return Comment::where('ip_token', '=', $this->createIpToken())->first();
	}

	public function createReply($data)
	{
		return Comment::create($data);
	}

	public function approveComment($id)
	{
		$comment = $this->getComment($id);
		$comment->approved = 'accepted';
		return $comment->save();
	}

	public function approveAllComments()
	{
		Comment::where('approved', '=', 'pending')->update(['approved' => 'accepted']);		
	}
	
	public function updateComment($id, $data)
	{
		$comment = $this->getComment($id);
		return $comment->update($data);
	}

	public function deleteComment($id)
	{	
		$comment = $this->getComment($id);
		return $comment->delete();
	}
	
	
}