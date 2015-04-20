<form method="post" action="{{ url('comment/addreply') }}">
  <input name="_token" type="hidden" value="{{ csrf_token() }}">

  <div class="form-group">
    @if(empty($commentData))
    <label for="name">Username:</label>
    <input 
    type="text" 
    class="form-control" 
    name="name" 
    value="{{ old('name') }}" 
    placeholder="Add name here .." 
    aria-describedby="sizing-addon2"
    >
    @else
    <input type="hidden" name="name" value="{{ $commentData->name }}">
    @endif
  </div>
  <div class="form-group">
    @if(empty($commentData))
    <label for="email">Email:</label>
    <input 
    type="email" 
    class="form-control" 
    name="email" 
    value="{{ old('email') }}" 
    placeholder="Add email here .." 
    aria-describedby="sizing-addon2"
    >
    @else
    <input type="hidden" name="email" value="{{ $commentData->email }}">
    @endif
  </div>
  <div class="form-group">
    <label for="comment_title">Comment title:</label>
    <input 
    type="text" 
    class="form-control" 
    name="comment_title" 
    value="{{ old('comment_title') }}" 
    placeholder="Add comment title here .." 
    aria-describedby="sizing-addon2"
    >
  </div>
  <div class="form-group">
    <label for="comment_content">Content:</label>
    <textarea 
    class="form-control" 
    rows="3" name="comment_content" 
    value="{{ old('comment_content') }}" 
    placeholder="Add comment here .."
    aria-describedby="sizing-addon2">{{ old('comment_content') }}</textarea>
  </div>

  <input name="parent_id" type="hidden" value='{{ $comment->id }}'>
  @if( ! Auth::check())
  <input name="user_id" type="hidden" value="0">
  @else
  <input name="user_id" type="hidden" value="{{ Auth::user()->id }}">
  @endif
  <input name="ip_address" type="hidden" value='{{ Request::getClientIp() }}'>


  <input type="submit" class="btn btn-default form-control" value="Comment">

</form>
