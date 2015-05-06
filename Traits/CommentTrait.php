<?php namespace App\Modules\Comment\Traits;

use App\Modules\Comment\Comment;
use Request;

trait CommentTrait{

	public function getAllComments($item = false, $itemId = false)
	{
		if($item && $itemId)
		{
			return Comment::where('item_type', '=', $item)->
			                where('item_id', '=', $itemId)->
			                where('parent_id', '=', 0)->
			                paginate('6');
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
		$ipToken = Request::cookie('ip_token');
		if ($ipToken) 
		{
			return $ipToken;
		}
		else
		{
			return bcrypt(str_random(40) . uniqid() . time());
		}
	}

	public function checkIpToken()
	{
		$comment = Comment::where('ip_token', '=', $this->createIpToken())->first();
		if ($comment !== null)
		{
			return $comment->id;
		}
		else
		{
			return 0;
		}
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
	
	public function paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName, $parent_id = 0)
	{
		$commentOwner = \Auth::check() ? \AclRepository::getUser($commentOwnerId) : $this->getComment($commentOwnerId);
		$comments     = $this->getAllComments($item, $itemId);
		$comments->setPath(url('comment/paginate', [$commentOwner, $item, $itemId, $commentModuleName]));

		$commentTree  = $this->getCommentTree($comments, $commentOwner, $item, $itemId, $commentModuleName, $parent_id = 0);
		$commentTree .= view('comment::comments.parts.paginationscommentmodule', compact('comments', 'commentModuleName'))->render();

		return$commentTree;
	}

	public function getCommentTree($comments, $commentOwner, $item, $itemId, $commentModuleName, $parent_id = 0)
	{
		$commentTree = '<li id="comment-tree">';
		if( ! $comments->count() && $parent_id == 0)
		{
			$commentTree .= '<h3><p>No Comments.</p></h3>';
		}

		foreach ($comments as $comment)
		{
			if ($comment->parent_id == $parent_id)
			{
				$commentTree .= '<div id="' .$commentModuleName . 'singleComment">' . view('comment::comments.parts.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'commentModuleName'))->render() . '</div>';
				$commentTree .= '<ul>' . $this->getCommentTree($comment->replies, $commentOwner, $item, $itemId, $commentModuleName, $comment->id) . '</ul>';
			}
		}
		return $commentTree . '</li>';
	}
}