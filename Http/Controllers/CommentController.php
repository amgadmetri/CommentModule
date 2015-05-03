<?php namespace App\Modules\Comment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Comment\Http\Requests\CommentFormRequest;
use App\Modules\Comment\Http\Requests\EditCommentFormRequest;
use App\Modules\Comment\Repositories\CommentRepository;

use Request;
use Cookie;
use Auth;

class CommentController extends Controller {

	private $comment;
	
	public function __construct(CommentRepository $comment)
	{
		$this->comment = $comment;
	}

	public function getIndex()
	{
		$comments = $this->comment->getAllComments();
		return view('comment::comments.comment' , compact('comments'));
	}

	public function postAddcomment(CommentFormRequest $request)
	{
		$allowCommentApproval = \InstallationRepository::getSettingValuByKey('Allow Comment Approval', 'comment')[0];

		if ( ! Auth::check())
		{
			$token                = $this->comment->createIpToken();
			$data['ip_token']     = $token;
			$data['approved']     = $allowCommentApproval == 'False' ? 'accepted' : 'pending';
			$this->comment->createComment(array_merge($request->all(),  $data));

			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentModuleName = $request->get('commentModuleName');
				$commentOwnerId    = \Auth::check() ? \Auth::user()->id : $this->comment->checkIpToken();

				return response($this->comment->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName))->withCookie(Cookie::forever('ip_token', $token));
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

			$this->comment->createComment(array_merge($request->all(),  $data));

			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentModuleName = $request->get('commentModuleName');
				$commentOwnerId    = \Auth::check() ? \Auth::user()->id : $this->comment->checkIpToken();

				return response($this->comment->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName));
			}

			return redirect()->back()->with('message', 'Comment sent and waiting for approval');

		}
		
	}

	public function postEditcomment(EditCommentFormRequest $request, $id)
	{
		$comment = $this->comment->getComment($id);

		if (Request::cookie('ip_token') !== $comment->ip_token && Auth::user()->id !== $comment->user_id) 
		{
			return redirect('comment/addcomment');
		}

		$this->comment->updateComment($id, array_merge($request->all()));

		if($request->ajax())
		{	
			$comment           = $this->comment->getComment($id);
			$item              = $request->get('item_type');
			$itemId            = $request->get('item_id');
			$commentModuleName = $request->get('commentModuleName');
			$commentOwner      = \Auth::check() ? \Auth::user() : $this->comment->checkIpToken();
			
			return view('comment::comments.parts.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'commentModuleName'))->render();
		}

		return redirect()->back()->with('message', 'Comment edited successfully.');
	}

	public function getDelete($id, \Illuminate\Http\Request $request)
	{
		$this->comment->deleteComment($id);

		if($request->ajax())
		{	
			return 'done';
		}

		return redirect()->back()->with('message', 'comment Deleted succssefuly');
	}

	public function getApprove($id)
	{
		$this->comment->approveComment($id);
		return redirect()->back();
	}

	public function getApproveall()
	{
		$this->comment->approveAllComments();
		return redirect()->back();
	}

	public function getPaginate($commentOwner, $item, $itemId, $commentModuleName = 'mediaLibrary')
	{
		return $this->comment->paginateCommentTree($commentOwner, $item, $itemId, $commentModuleName);
	}
}