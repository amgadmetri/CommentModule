<?php namespace App\Modules\Comment\Traits;

use App\Modules\Comment\Comment;
use Request;

trait CommentTrait{

	public function getAllComments($item = false, $itemId = false)
	{
		if($item && $itemId)
		{
			return Comment::where('item_type', '=', $item)->where('item_id', '=', $itemId)->paginate('1');
		}
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

	public function approveComment($id)
	{
		$comment           = $this->getComment($id);
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
	
	public function paginateCommentTree($commentOwner, $item, $itemId, $commentModuleName, $parent_id = 0)
	{
		$comments     = $this->getAllComments($item, $itemId);
		$comments->setPath(url('comment/paginate', [$commentOwner, $item, $itemId, $commentModuleName]));

		$commentTree  = $this->getCommentTree($comments, $commentOwner, $item, $itemId, $commentModuleName, $parent_id = 0);
		$commentTree .= view('comment::comments.parts.paginationscommentmodule', compact('comments', 'commentModuleName'))->render();

		return$commentTree;
	}
	public function getCommentTree($comments, $commentOwner, $item, $itemId, $commentModuleName, $parent_id = 0)
	{
		$commentTree = '<li id="comment-tree">';
		if(is_object($comments))
		{
			if( ! $comments->count())
			{
				$commentTree .= '<h3><p>No Comments.</p></h3>';
			}

			foreach ($comments as $comment)
			{
				if (is_object($comment) && $comment->parent_id == $parent_id)
				{
					$commentTree .= '<div id="' .$commentModuleName . 'singleComment">' . view('comment::comments.parts.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'commentModuleName'))->render() . '</div>';
					$commentTree .= '<ul>' . $this->getCommentTree($commentOwner, $item, $itemId, $commentModuleName, $comment->id) . '</ul>';
				}
			}
		}
		return $commentTree;
	}
}