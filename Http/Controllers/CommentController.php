<?php namespace App\Modules\Comment\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Comment\Http\Requests\CommentFormRequest;
use App\Modules\Comment\Http\Requests\EditCommentFormRequest;
use App\Modules\Comment\Repositories\CommentRepository;
use Carbon\Carbon;
use Request;
use Cookie;
use Auth;

class CommentController extends Controller {

	private $comment;
	
	public function __construct(CommentRepository $comment)
	{
		$this->comment = $comment;
	}

 	//display all the comments
	public function getIndex()
	{
		$comments = $this->comment->getAllComments();
		return view('comment::comments.comment' , compact('comments'));
	}

	public function getAddcomment()
	{
		$commentData = Auth::check() ? Auth::user() : $this->comment->checkIpToken();
		$comments    = $this->comment->getAllComments(); 
		$html        = $this->getCommentTree($commentData);

		return view('comment::comments.addcomment', compact('comments', 'html', 'commentData'));
	}

	public function postAddcomment(CommentFormRequest $request)
	{
		if ( ! Auth::check())
		{
			$token            = $this->comment->createIpToken();
			$data['ip_token'] = $token;
			$data['item_id'] = 1;
			$data['item_type'] = "content";

			$this->comment->createComment(array_merge($request->all(),  $data));

			return redirect()->back()->
			withCookie(Cookie::forever('ip_token', $token))->
			with('message', 'Comment sent and waiting for approval');
		}
		else
		{
			$token             = $this->comment->createIpToken();
			$data['ip_token']  = $token;
			$data['item_id']   = 1;
			$data['item_type'] = "content";
			$data['name']      = Auth::user()->name ;
			$data['email']     = Auth::user()->email;

			$this->comment->createComment(array_merge($request->all(),  $data));
			return redirect()->back()->
			with('message', 'Comment sent and waiting for approval');

		}
		
	}

	public function getDelete($id)
	{
		$this->comment->deleteComment($id);
		return redirect()->back()->with('message', 'comment Deleted succssefuly');
	}

	public function postAddreply(CommentFormRequest $request)
	{
		$token            = $this->comment->createIpToken();
		$data['ip_token'] = $token;
		$data['item_id'] = 1;
		$data['item_type'] = "content";

		$this->comment->createComment(array_merge($request->all(),  $data));

		return redirect()->back()->withCookie(Cookie::forever('ip_token', $token))->
		with('message', 'Reply sent and waiting for approval');
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

	public function getCommentTree($commentData, $parent_id = 0)
	{
		$comments = $this->comment->getAllComments();
		$html = '<li>';
		foreach ($comments as $comment)
		{
			if ($comment->parent_id == $parent_id)
			{
				$html .= view('comment::comments.commenttemplate', compact('comment', 'commentData'))->render();
				$html .= '<ul>' . $this->getCommentTree($commentData, $comment->id) . '</ul>';
			}
		}
		$html .= '</li>';
		return $html;
	}

	public function getUpdate($id)
	{
		$comment = $this->comment->getComment($id);

		if (Request::cookie('ip_token') !== $comment->ip_token && Auth::user()->id !== $comment->user_id) 
		{
			return redirect('comment/addcomment');
		}
		
		return view('comment::comments.editcomment', compact('comment'));
	}

	public function postUpdate(EditCommentFormRequest $request, $id)
	{

		$this->comment->updateComment($id, array_merge($request->all()));
		return redirect('comment/addcomment');
	}
}