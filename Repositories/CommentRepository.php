<?php namespace App\Modules\Comment\Repositories;

use App\Modules\Core\AbstractRepositories\AbstractRepository;

class CommentRepository extends AbstractRepository
{
	/**
	 * Return the model full namespace.
	 * 
	 * @return string
	 */
	protected function getModel()
	{
		return 'App\Modules\Comment\Comment';
	}

	/**
	 * Return the module relations.
	 * 
	 * @return array
	 */
	protected function getRelations()
	{
		return ['replies'];
	}

	/**
	 * Return comments belongs to a specific item.
	 * 
	 * @param  string  $item
	 * @param  integer $itemId
	 * @param  integer $perPage
	 * @return collection
	 */
	public function getAllItemCommentsCount($item = false, $itemId = false)
	{
		if($item && $itemId)
		{
			return $this->model->with($this->getRelations())->
						    where('item_type', '=', $item)->
			                where('item_id', '=', $itemId)->
			                where('parent_id', '=', 0)->
			                count();    
		}
	}

	/**
	 * Return comments belongs to a specific item.
	 * 
	 * @param  string $item
	 * @param  integer $itemId
	 * @param  integer $perPage
	 * @return collection
	 */
	public function getAllItemComments($item = false, $itemId = false, $perPage = 6)
	{
		if($item && $itemId)
		{
			return $this->model->with($this->getRelations())->
						    where('item_type', '=', $item)->
			                where('item_id', '=', $itemId)->
			                where('parent_id', '=', 0)->
			                paginate($perPage);    
		}
	}

	/**
	 * Return a list of comment based on the
	 * given ids.
	 * 
	 * @param  array $ids
	 * @return collection
	 */
	public function getComments($ids)
	{
		return $this->model->whereIn('id', $ids)->get();
	}
	
	/**
	 * Get the stored cookie token or create new one.
	 * 
	 * @return string
	 */
	public function createIpToken()
	{	
		return \Request::cookie('ip_token') ?: bcrypt(str_random(40) . uniqid() . time());
	}

	/**
	 * If the ip address or the token exists in 
	 * the storage then return the first comment 
	 * id with that ip address or token else 
	 * return 0.
	 * 
	 * @return integer
	 */
	public function checkIpToken()
	{
		$comment = $this->first('ip_address', \Request::getClientIp()) ?: $this->first('ip_token', $this->createIpToken());
		return $comment ? $comment->id : 0;
	}

	/**
	 * Change the status of the comment
	 * to approved.
	 * 
	 * @param  integer $id
	 * @return integer affected rows
	 */
	public function approveComment($id)
	{
		return $this->update($id, ['status' => 'accepted']);
	}

	/**
	 * Change the status of all pending comments to approved.
	 * 
	 * @return integer affected rows
	 */
	public function approveAllComments()
	{
		return $this->update('pending', ['status' => 'accepted'], 'status');	
	}

	/**
	 * Return the comment template for handling add , edit , delete
	 * show and reply comments.
	 * 
	 * @param  string  $item                The name of the item the 
	 *                                      comment belongs to. 
	 *                                      ex: 'user', 'content' ....
	 * @param  integer $itemId              The id of the item the 
	 *                                      comment belongs to. 
	 *                                      ex: 'user', 'content' ....
	 * @param  string  $path 			    The path to the custom comment
	 *                            		    html template.
	 * @param  integer $perPage 
	 * @param  string  $commentTemplateName 
	 * @return string
	 */
	public function getCommentTemplate($item, $itemId, $path = false, $perPage = 6, $commentTemplateName = 'comment_template')
	{
		$themeName                  = \CMS::CoreModules()->getActiveTheme()->module_key;
		$path                       = $path ? $themeName . "::" . $path : 'comment::comments.parts';
		$commentOwnerId             = $this->getCommentOwnerId();
		$commentOwner               = $this->getCommentOwnerIdData($commentOwnerId);
		$unrigesteredUserCanComment = \CMS::coreModuleSettings()->getSettingValuByKey('Allow Unregisterd User To Comment', 'comment')[0];
		$commentTree                = $this->paginateCommentTree($commentOwnerId, $item, $itemId, $path, $commentTemplateName, $perPage);

		return view($path . '.commentmodule', compact('commentTree', 'commentOwner', 'itemId', 'item', 'commentTemplateName', 'unrigesteredUserCanComment', 'perPage'))->render();
	}

	/**
	 * Prepare the comment data for pagination and call
	 * a function to draw the comment tree.
	 * 
	 * @param  integer $commentOwnerId
	 * @param  string  $item                The name of the item the 
	 *                                      comment belongs to. 
	 *                                      ex: 'user', 'content' ....
	 * @param  integer $itemId              The id of the item the 
	 *                                      comment belongs to. 
	 *                                      ex: 'user', 'content' ....
	 * @param  string  $path 			    The path to the custom comment
	 *                            		    html template.
	 * @param  string  $commentTemplateName 
	 * @param  integer $perPage 
	 * @param  integer $parent_id
	 * @return string
	 */
	public function paginateCommentTree($commentOwnerId, $item, $itemId, $path, $commentTemplateName, $perPage, $parent_id = 0)
	{
		$commentOwner = $this->getCommentOwnerIdData($commentOwnerId);
		$comments     = $this->getAllItemComments($item, $itemId, $perPage);
		$comments->setPath(url('admin/comment/paginate', [$commentOwner, $item, $itemId, $path, $perPage, $commentTemplateName]));

		$commentTree  = $this->getCommentTree($comments, $commentOwner, $item, $itemId, $path, $commentTemplateName, $parent_id = 0);
		$commentTree .= view($path . '.paginationscommentmodule', compact('comments', 'commentTemplateName'))->render();

		return $commentTree;
	}

	/**
	 * Recursive function that build the comment tree.
	 * 
	 * @param  collection  $comments
	 * @param  integer  $commentOwner
	 * @param  string $item                The name of the item the 
	 *                                     comment belongs to. 
	 *                                     ex: 'user', 'content' ....
	 * @param  integer $itemId             The id of the item the 
	 *                                     comment belongs to. 
	 *                                     ex: 'user', 'content' ....
	 * @param  string  $path 			    The path to the custom comment
	 *                            		    html template.
	 * @param  string  $commentTemplateName 
	 * @param  integer $parent_id
	 * @return string
	 */
	public function getCommentTree($comments, $commentOwner, $item, $itemId, $path, $commentTemplateName, $parent_id = 0)
	{
		$commentTree = '';
		if( ! $comments->count() && $parent_id == 0)
		{
			$commentTree .= '<h3><p>No Comments.</p></h3>';
		}

		foreach ($comments as $comment)
		{
			if ($comment->parent_id == $parent_id)
			{
				$commentTree .= view($path . '.commenttemplate', compact('comment', 'commentOwner', 'item', 'itemId', 'commentTemplateName'))->render();
			}
		}
		return $commentTree;
	}

	/**
	 * Return the registerd user id or check for
	 * the ip token cookie and return the comment
	 * id belongs to that token.
	 * 
	 * @return integer
	 */
	public function getCommentOwnerId()
	{
		return \Auth::check() ? \Auth::user()->id : $this->checkIpToken();
	}

	/**
	 * Return the registered user id or the comment id.
	 * 
	 * @param  integer $id
	 * @return object
	 */
	public function getCommentOwnerIdData($id)
	{
		return \Auth::check() ? \CMS::users()->find($id) : $this->find($id);
	}
}
