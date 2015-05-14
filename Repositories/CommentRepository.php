<?php namespace App\Modules\Comment\Repositories;

use App\AbstractRepositories\AbstractRepository;
use App\Modules\Comment\Comment;

class CommentRepository extends AbstractRepository
{
	protected function getModel()
	{
		return 'App\Modules\Comment\Comment';
	}

	protected function getRelations()
	{
		return ['replies'];
	}

	public function getAllItemComments($item = false, $itemId = false, $perPage = 6)
	{
		if($item && $itemId)
		{
			return Comment::with($this->getRelations())->
							where('item_type', '=', $item)->
			                where('item_id', '=', $itemId)->
			                where('parent_id', '=', 0)->
			                paginate($perPage);
		}
	}

	public function getComments($ids)
	{
		return Comment::whereIn('id', $ids)->get();
	}
	
	public function createIpToken()
	{	
		return \Request::cookie('ip_token') ?: bcrypt(str_random(40) . uniqid() . time());
	}

	public function checkIpToken()
	{
		$comment = $this->findBy('ip_token', $this->createIpToken())[0];
		return $comment !== null ? $comment->id : 0;
	}

	public function approveComment($id)
	{
		$comment           = $this->find($id);
		$comment->approved = 'accepted';
		return $comment->save();
	}

	public function approveAllComments()
	{
		return $this->update('pending', ['approved' => 'accepted'], 'approved');	
	}

	public function paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName, $parent_id = 0)
	{
		$commentOwner = \Auth::check() ? \CMS::users()->find($commentOwnerId) : $this->find($commentOwnerId);
		$comments     = $this->getAllItemComments($item, $itemId, 6);
		$comments->setPath(url('comment/paginate', [$commentOwner, $item, $itemId, $commentModuleName]));

		$commentTree  = $this->getCommentTree($comments, $commentOwner, $item, $itemId, $commentModuleName, $parent_id = 0);
		$commentTree .= view('comment::comments.parts.paginationscommentmodule', compact('comments', 'commentModuleName'))->render();

		return $commentTree;
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

	public function getCommentTemplate($item, $itemId, $commentModuleName = 'comment_module')
	{
		$commentOwnerId             = \Auth::check() ? \Auth::user()->id : $this->checkIpToken();
		$commentOwner               = \Auth::check() ? \CMS::users()->find($commentOwnerId) : $this->find($commentOwnerId);
		$unrigesteredUserCanComment = \CMS::coreModuleSettings()->getSettingValuByKey('Allow Unregisterd User To Comment', 'comment')[0];
		$commentTree                = $this->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName);
		
		return view('comment::comments.parts.commentmodule', compact('commentTree', 'commentOwner', 'itemId', 'item', 'commentModuleName', 'unrigesteredUserCanComment'))->render();
	}
}
