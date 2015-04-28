<?php namespace App\Modules\Comment\Repositories;

use App\Modules\Comment\Traits\CommentTrait;


class CommentRepository
{
	use CommentTrait;

	public function getCommentTemplate($item, $itemId, $commentModuleName = 'comment_module')
	{
		$commentOwner = \Auth::check() ? \Auth::user() : $this->checkIpToken();
		$commentTree  = $this->paginateCommentTree($commentOwner, $item, $itemId, $commentModuleName);

		return view('comment::comments.parts.commentmodule', compact('comments', 'commentTree', 'commentOwner', 'itemId', 'item', 'commentModuleName'))->render();
	}
}
