<div class="alert alert-danger hidden" id="{{ $commentModuleName }}editErrormessageContainer">
  <strong>Whoops!</strong> There were some problems with your input.<br><br>
  <ul>
  </ul>
</div>

<div class="alert alert-success hidden" id="{{ $commentModuleName }}editMessageContainer">
  <ul>
  </ul>
</div>

<form method="post" id="{{ $commentModuleName }}edit_comment_form" action="{{ url('comment/editcomment', $comment->id) }}">
  <input name="_token" type="hidden" value="{{ csrf_token() }}">

  <div class="form-group">
    <label for="comment_title">Comment title:</label>
    <input 
    type             ="text" 
    class            ="form-control" 
    name             ="comment_title" 
    value            ="{{ $comment->comment_title }}" 
    placeholder      ="edit comment title here .." 
    aria-describedby ="sizing-editon2"
    >
  </div>
  <div class="form-group">
    <label for="comment_content">Content:</label>
    <textarea 
    class            ="form-control" 
    rows             ="3" 
    name             ="comment_content" 
    placeholder      ="edit comment here .."
    aria-describedby ="sizing-editon2">{{ $comment->comment_content }}</textarea>
  </div>


  <input name="user_id" type="hidden" value="{{ $comment->user_id }}">
  <input name="edited" type="hidden" value="1">
  <input name="parent_id" type="hidden" value="{{ $comment->parent_id }}">
  <input name="item_id" type="hidden" value="{{ $comment->item_id }}">
  <input name="item_type" type="hidden" value="{{ $comment->item_type }}">
  <input name="approved" type="hidden" value="{{ $comment->approved }}">
  <input name="ip_address" type="hidden" value='{{ $comment->ip_address }}'>
  <input name="commentModuleName" type="hidden" value='{{ $commentModuleName }}'>
  
  <button type="submit" class="btn btn-default form-control">Update</button>
</form>