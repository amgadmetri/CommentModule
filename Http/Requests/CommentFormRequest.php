<?php
namespace App\Modules\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Request;

class CommentFormRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'item_id'         => 'required',
			'item_type'       => 'required|max:150',
			'name'        => 'required|max:150',
			'email'           => 'required|email',
			'comment_title'   => 'required|max:150',
			'comment_content' => 'required',
			'parent_id'       => 'required',
			'user_id'         => 'required',
			'ip_address'      => 'required'
			
		];
	}

	/**                             
	 * Determine if the user is authorized to make this request.
	 *
	 * @return bool
	 */
	public function authorize()
	{
		return true;
	}
}