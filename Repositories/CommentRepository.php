<?php namespace App\Modules\Comment\Repositories;

use App\Modules\Comment\Traits\CommentTrait;


class CommentRepository
{
	use CommentTrait;

	public function getCommentTemplate($item, $itemId, $commentModuleName = 'comment_module')
	{
		$commentOwnerId             = \Auth::check() ? \Auth::user()->id : $this->checkIpToken();
		$commentOwner               = \Auth::check() ? \AclRepository::getUser($commentOwnerId) : $this->getComment($commentOwnerId);
		$unrigesteredUserCanComment = \InstallationRepository::getSettingValuByKey('Allow Unregisterd User To Comment', 'comment')[0];
		$commentTree                = $this->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName);
		
		return view('comment::comments.parts.commentmodule', compact('commentTree', 'commentOwner', 'itemId', 'item', 'commentModuleName', 'unrigesteredUserCanComment'))->render();
	}
}
