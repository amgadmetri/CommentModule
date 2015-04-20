<?php
namespace App\Modules\Comment\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EditCommentFormRequest extends FormRequest
{
	/**
	 * Get the validation rules that apply to the request.
	 *
	 * @return array
	 */
	public function rules()
	{
		return [
		'comment_title'   => 'required|max:150',
		'comment_content' => 'required',
		'edited'          => 'required',
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