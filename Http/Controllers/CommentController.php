<?php namespace App\Modules\Comment\Http\Controllers;

use App\Modules\Core\Http\Controllers\BaseController;
use App\Modules\Comment\Http\Requests\AddCommentFormRequest;
use App\Modules\Comment\Http\Requests\EditCommentFormRequest;

class CommentController extends BaseController {
	
	/**
	 * Specify a list of extra permissions.
	 * 
	 * @var permissions
	 */
	protected $permissions = [
	'getIndex'      => 'ApproveComments', 
	'getApprove'    => 'ApproveComments' , 
	'getApproveall' => 'ApproveComments'
	];

	/**
	 * Create new CommentController instance.
	 */
	public function __construct()
	{
		parent::__construct('Comments');
	}

	/**
	 * Display a listing of the comments.
	 * 
	 * @return Response
	 */
	public function getIndex()
	{
		$comments = \CMS::comments()->all();
		return view('comment::comments.comment' , compact('comments'));
	}
	
	/**
	 * Change the status of the comment to approved.
	 * 
	 * @param  integer $id
	 * @return response
	 */
	public function getApprove($id)
	{
		\CMS::comments()->approveComment($id);
		return redirect()->back();
	}

	/**
	 * Change the status of all pending comments to approved.
	 * 
	 * @return response
	 */
	public function getApproveall()
	{
		\CMS::comments()->approveAllComments();
		return redirect()->back();
	}

	/**
	 * Store a newly created comment in storage.
	 * 
	 * @param  AddCommentFormRequest $request 
	 * @return response
	 */
	public function postAddcomment(AddCommentFormRequest $request)
	{	
		/**
		 * Get comment settings from the core module settings.
		 */
		$allowCommentApproval       = \CMS::coreModuleSettings()->getSettingValuByKey('Allow Comment Approval', 'comment')[0];
		$unrigesteredUserCanComment = \CMS::coreModuleSettings()->getSettingValuByKey('Allow Unregisterd User To Comment', 'comment')[0];

		if ( ! \Auth::check() && $unrigesteredUserCanComment === 'True')
		{
			/**
			 * Get the stored cookie token or create new one.
			 * 
			 * @var token
			 */
			$token            = \CMS::comments()->createIpToken();
			$data['ip_token'] = $token;
			$data['status']   = $allowCommentApproval == 'False' ? 'accepted' : 'pending';
			\CMS::comments()->create(array_merge($request->all(),  $data));

			/**
			 * If the request is ajax then get the necessary data 
			 * for returning the comment template html and create
			 * a cookie with the created token.
			 */
			if ($request->ajax())
			{
				$item                = $request->get('item_type');
				$itemId              = $request->get('item_id');
				$commentTemplateName = $request->get('commentTemplateName');
				$perPage             = $request->get('per_page');
				$path                = $request->get('path');
				$commentOwnerId      = \CMS::comments()->getCommentOwnerId();

				return response(\CMS::comments()->
					                  paginateCommentTree($commentOwnerId, $item, $itemId, $path, $commentTemplateName, $perPage))->
				                      withCookie(\Cookie::forever('ip_token', $token));
			}

			/**
			 * Redirect back after the comment had been saved 
			 * and create a cookie with the created token.
			 */
			return redirect()->back()->
			                   withCookie(\Cookie::forever('ip_token', $token))->
			                   with('message', 'Comment sent');
		}
		else
		{
			$data['name']   = \Auth::user()->name ;
			$data['email']  = \Auth::user()->email;
			$data['status'] = $allowCommentApproval == 'False' || \CMS::users()->userHasGroup(\Auth::user()->id, 'admin') ? 'accepted' : 'pending';
			\CMS::comments()->create(array_merge($request->all(),  $data));

			/**
			 * If the request is ajax then get the necessary data 
			 * for returning the comment template html and create
			 * a cookie with the created token.
			 */
			if($request->ajax())
			{
				$item              = $request->get('item_type');
				$itemId            = $request->get('item_id');
				$commentTemplateName = $request->get('commentTemplateName');
				$perPage             = $request->get('per_page');
				$path                = $request->get('path');
				$commentOwnerId    = \CMS::comments()->getCommentOwnerId();

				return response(\CMS::comments()->paginateCommentTree($commentOwnerId, $item, $itemId, $path, $commentTemplateName, $perPage));
			}

			return redirect()->back()->with('message', 'Comment sent and waiting for approval');
		}
		
	}

	/**
	 * Update the specified comment in storage.
	 * 
	 * @param  EditCommentFormRequest $request 
	 * @param  integer                $id
	 * @return response
	 */
	public function postEditcomment(EditCommentFormRequest $request, $id)
	{	
		/**
		 * If the user isn't the owner of the comment then redirect back.
		 */
		$comment = \CMS::comments()->find($id);
		if (\Request::cookie('ip_token') !== $comment->ip_token && \Auth::user()->id !== $comment->user_id) 
		{
			return redirect('admin/comment/addcomment');
		}

		\CMS::comments()->update($id, $request->all());

		/**
		 * If the request is ajax then get the necessary data 
		 * for returning the comment template html and create
		 * a cookie with the created token.
		 */
		if($request->ajax())
		{	
			$item                = $request->get('item_type');
			$itemId              = $request->get('item_id');
			$perPage             = $request->get('per_page');
			$path                = $request->get('path');
			$commentTemplateName = $request->get('commentTemplateName');
			$commentOwner        =  \CMS::comments()->getCommentOwnerIdData(\CMS::comments()->getCommentOwnerId());
			$comment             = \CMS::comments()->find($id);
			
			return view($path . '.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'path', 'perPage', 'commentTemplateName'))->render();
		}

		return redirect()->back()->with('message', 'Comment edited successfully.');
	}

	/**
	 * Remove the specified comment from storage.
	 * 
	 * @param  integer                  $id     
	 * @param  \Illuminate\Http\Request $request 
	 * @return response
	 */
	public function getDeletecomment($id, \Illuminate\Http\Request $request)
	{
		\CMS::comments()->delete($id);
		if($request->ajax())
		{	
			return 'done';
		}

		return redirect()->back()->with('message', 'Comment Deleted succssefuly');
	}

	/**
	 * Handle the pagination request.
	 * 
	 * @param  integer $commentOwnerId   The id of the registerd user 
	 *                                   or the user's comment id in 
	 *                                   case of unregisterd users.
	 * @param  string $item              The name of the item the 
	 *                                   comment belongs to. 
	 *                                   ex: 'user', 'content' ....
	 * @param  integer $itemId           The id of the item the 
	 *                                   comment belongs to. 
	 *                                   ex: 'user', 'content' ....
	 * @param  string  $path 			 The path to the custom comment
	 *                            		 html template.
	 * @param  string $commentTemplateName
	 * @return response
	 */
	public function paginate($commentOwnerId, $item, $itemId, $path, $perPage, $commentTemplateName)
	{
		return \CMS::comments()->paginateCommentTree($commentOwnerId, $item, $itemId, $path, $commentTemplateName, $perPage);
	}
}