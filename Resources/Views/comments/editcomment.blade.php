@extends('app')
@section('content')
<div class="container">
	<div class="col-sm-9">
		@if (count($errors) > 0)
		<div class="alert alert-danger">
			<strong>Whoops!</strong> There were some problems with your input.<br><br>
			<ul>
				@foreach ($errors->all() as $error)
				<li>{{ $error }}</li>
				@endforeach
			</ul>
		</div>
		@endif

		@if (Session::has('message'))
		<div class="alert alert-success">
			<ul>
				<li>{{ Session::get('message') }}</li>
			</ul>
		</div>
		@endif

		<form method="post">
			<input name="_token" type="hidden" value="{{ csrf_token() }}">

			<div class="form-group">
				<label for="comment_title">Comment title</label>
				<input 
				type="text" 
				class="form-control" 
				name="comment_title" 
				value="{{$comment->comment_title }}" 
				placeholder="Album name.." 
				aria-describedby="sizing-addon2"
				>
			</div>
			<div class="form-group">
				<label for="comment_content">Comment</label>
				<input 
				type="text" 
				class="form-control" 
				name="comment_content" 
				value="{{$comment->comment_content }}" 
				placeholder="Album name.." 
				aria-describedby="sizing-addon2"
				>
			</div>
			<input type="hidden" name="edited" value="1">
			<div class="form-group">
				<button type="submit" class="btn btn-primary form-control">update comment</button>
			</div>
			
		</form>

	</div>
</div>
@stop