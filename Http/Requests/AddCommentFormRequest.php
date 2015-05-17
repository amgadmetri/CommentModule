<?php
namespace App\Modules\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AddCommentFormRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
			'name'            => 'required|max:150',
			'email'           => 'required|max:150|email',
			'comment_title'   => 'required|max:150',
			'comment_content' => 'required',
			'parent_id'       => 'required',
			'user_id'         => 'required',
			'ip_address'      => 'required|max:45'
			
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