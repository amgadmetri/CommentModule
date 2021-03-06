@extends('core::app')
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

    <div class="container" >
      <div class="row">
        <div class="panel panel-default">
        
          <div class="panel-heading" >
            <h3 class="panel-title"> Recent Comments</h3>
          </div>

          <div class="panel-body" class="col-md-7">
            <ul class="list-group">
             @foreach ($comments as $comment)
               <li class="list-group-item">
                <div class="row">
                  <div class="col-xs-2 col-md-1">
                    <img src="http://placehold.it/80" class="img-circle img-responsive" alt="" /></div>
                    <div class="col-xs-6 col-md-7">
                      <div>
                        <h4>{{ $comment->comment_title }}</h4>
                        <div class="mic-info">
                          By: {{ $comment->name }} on {{ $comment->created_at }}
                        </div>
                      </div>
                      <div class="comment-text">
                        {{ $comment->comment_content }}
                      </div>
                      Status: {{ $comment->status }}
                      <div class="action">
                        @if ($comment->status === 'pending')
                          <a class="btn btn-success btn-xs" href='{{ url("admin/comment/approve/$comment->id") }}'>
                            Approve
                          </a> 
                        @endif
                        <a class="btn btn-danger btn-xs" href='{{ url("admin/comment/deletecomment/$comment->id") }}'>
                          Delete
                        </a> 
                      </div>
                    </div>
                  </div>
                </li>
              @endforeach
            </ul>
            <a class="btn btn-success btn-xs" href='{{ url("admin/comment/approveall") }}'>
              approve all comments
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>  
</div>
@stop

