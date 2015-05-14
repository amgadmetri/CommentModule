<?php namespace App\Modules\Comment\Http\Controllers;

use App\Http\Controllers\BaseController;
use App\Modules\Comment\Http\Requests\CommentFormRequest;
use App\Modules\Comment\Http\Requests\EditCommentFormRequest;

class CommentController extends BaseController {
	
	/**
	 * Specify a list of extra permissions.
	 * 
	 * @var permissions
	 */
	protected $permissions = [
	'Index'      => 'ApproveComments', 
	'Approve'    => 'ApproveComments' , 
	'Approveall' => 'ApproveComments'
	];

	public function __construct()
	{
		parent::__construct('Comments');
	}

	public function getIndex()
	{
		$comments = \CMS::comments()->all();
		return view('comment::comments.comment' , compact('comments'));
	}
	
	public function getApprove($id)
	{
		\CMS::comments()->approveComment($id);
		return redirect()->back();
	}

	public function getApproveall()
	{
		\CMS::comments()->approveAllComments();
		return redirect()->back();
	}

	public function postAddcomment(CommentFormRequest $request)
	{
		$allowCommentApproval = \CMS::coreModuleSettings()->getSettingValuByKey('Allow Comment Approval', 'comment')[0];

		if ( ! \Auth::check())
		{
			$token                = \CMS::comments()->createIpToken();
			$data['ip_token']     = $token;
			$data['approved']     = $allowCommentApproval == 'False' ? 'accepted' : 'pending';
			\CMS::comments()->create(array_merge($request->all(),  $data));

			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentModuleName = $request->get('commentModuleName');
				$commentOwnerId    = \Auth::check() ? \Auth::user()->id : \CMS::comments()->checkIpToken();

				return response(\CMS::comments()->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName))->withCookie(\Cookie::forever('ip_token', $token));
			}

			return redirect()->back()->
			                   withCookie(\Cookie::forever('ip_token', $token))->
			                   with('message', 'Comment sent and waiting for approval');
		}
		else
		{
			$data['name']     = \Auth::user()->name ;
			$data['email']    = \Auth::user()->email;
			$data['approved'] = $allowCommentApproval == 'False' || \CMS::users()->userHasGroup(\Auth::user()->id, 'admin') ? 'accepted' : 'pending';

			\CMS::comments()->create(array_merge($request->all(),  $data));

			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentModuleName = $request->get('commentModuleName');
				$commentOwnerId    = \Auth::check() ? \Auth::user()->id : \CMS::comments()->checkIpToken();

				return response(\CMS::comments()->paginateCommentTree($commentOwnerId, $item, $itemId, $commentModuleName));
			}

			return redirect()->back()->with('message', 'Comment sent and waiting for approval');

		}
		
	}

	public function postEditcomment(EditCommentFormRequest $request, $id)
	{
		$comment = \CMS::comments()->find($id);

		if (\Request::cookie('ip_token') !== $comment->ip_token && \Auth::user()->id !== $comment->user_id) 
		{
			return redirect('admin/comment/addcomment');
		}

		\CMS::comments()->update($id, array_merge($request->all()));

		if($request->ajax())
		{	
			$item              = $request->get('item_type');
			$itemId            = $request->get('item_id');
			$commentModuleName = $request->get('commentModuleName');
			$commentOwner      = \Auth::check() ? \Auth::user() : \CMS::comments()->checkIpToken();
			
			return view('comment::comments.parts.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'commentModuleName'))->render();
		}

		return redirect()->back()->with('message', 'Comment edited successfully.');
	}

	public function getDelete($id, \Illuminate\Http\Request $request)
	{
		\CMS::comments()->delete($id);

		if($request->ajax())
		{	
			return 'done';
		}

		return redirect()->back()->with('message', 'comment Deleted succssefuly');
	}

	public function getPaginate($commentOwner, $item, $itemId, $commentModuleName = 'mediaLibrary')
	{
		return \CMS::comments()->paginateCommentTree($commentOwner, $item, $itemId, $commentModuleName);
	}
}