<?php namespace App\Modules\Comment\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Comment\Http\Requests\CommentFormRequest;
use App\Modules\Comment\Http\Requests\EditCommentFormRequest;
use App\Modules\Comment\Repositories\CommentRepository;

use Request;
use Cookie;
use Auth;

class CommentController extends BaseController {
	
	public function __construct(CommentRepository $comment)
	{
		parent::__construct($comment, 'Comments');
	}

	public function getIndex()
	{
		$this->hasPermission('ApproveComments');
		$comments = $this->repository->getAllComments();

		return view('comment::comments.comment' , compact('comments'));
	}
	
	public function getApprove($id)
	{
		$this->hasPermission('ApproveComments');
		$this->repository->approveComment($id);

		return redirect()->back();
	}

	public function getApproveall()
	{
		$this->hasPermission('ApproveComments');
		$this->repository->approveAllComments();

		return redirect()->back();
	}

	public function postAddcomment(CommentFormRequest $request)
	{
		$allowCommentApproval = \InstallationRepository::getSettingValuByKey('Allow Comment Approval', 'comment')[0];

		if ( ! Auth::check())
		{
			$token                = $this->repository->createIpToken();
			$data['ip_token']     = $token;
			$data['approved']     = $allowCommentApproval == 'False' ? 'accepted' : 'pending';
			$this->repository->createComment(array_merge($request->all(),  $data));

			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentModuleName = $request->get('commentModuleName');
				$commentOwnerId    = \Auth::check() ? \Auth::user()->id : $this->repository->checkIpToken();

				return response($this->repository->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName))->withCookie(Cookie::forever('ip_token', $token));
			}

			return redirect()->back()->
			       withCookie(Cookie::forever('ip_token', $token))->
			       with('message', 'Comment sent and waiting for approval');
		}
		else
		{
			$data['name']     = Auth::user()->name ;
			$data['email']    = Auth::user()->email;
			$data['approved'] = $allowCommentApproval == 'False' || \AclRepository::userHasGroup(\Auth::user()->id, 'admin') ? 'accepted' : 'pending';

			$this->repository->createComment(array_merge($request->all(),  $data));

			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentModuleName = $request->get('commentModuleName');
				$commentOwnerId    = \Auth::check() ? \Auth::user()->id : $this->repository->checkIpToken();

				return response($this->repository->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName));
			}

			return redirect()->back()->with('message', 'Comment sent and waiting for approval');

		}
		
	}

	public function postEditcomment(EditCommentFormRequest $request, $id)
	{
		$comment = $this->repository->getComment($id);

		if (Request::cookie('ip_token') !== $comment->ip_token && Auth::user()->id !== $comment->user_id) 
		{
			return redirect('comment/addcomment');
		}

		$this->repository->updateComment($id, array_merge($request->all()));

		if($request->ajax())
		{	
			$comment           = $this->repository->getComment($id);
			$item              = $request->get('item_type');
			$itemId            = $request->get('item_id');
			$commentModuleName = $request->get('commentModuleName');
			$commentOwner      = \Auth::check() ? \Auth::user() : $this->repository->checkIpToken();
			
			return view('comment::comments.parts.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'commentModuleName'))->render();
		}

		return redirect()->back()->with('message', 'Comment edited successfully.');
	}

	public function getDelete($id, \Illuminate\Http\Request $request)
	{
		$this->repository->deleteComment($id);

		if($request->ajax())
		{	
			return 'done';
		}

		return redirect()->back()->with('message', 'comment Deleted succssefuly');
	}

	public function getPaginate($commentOwner, $item, $itemId, $commentModuleName = 'mediaLibrary')
	{
		return $this->repository->paginateCommentTree($commentOwner, $item, $itemId, $commentModuleName);
	}
}