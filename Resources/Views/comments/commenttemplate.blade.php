  <article class="row">
    <div class="col-md-2 col-sm-2 col-md-offset-1 col-sm-offset-0 hidden-xs">
      <figure class="thumbnail">
        <img class="img-responsive" src="https://ssl.gstatic.com/accounts/ui/avatar_2x.png" alt='{{ $comment->name }}'/>
        <figcaption class="text-center">{{ $comment->name }}</figcaption>
      </figure>
    </div>
    <div class="col-md-9 col-sm-9">
      <div class="panel panel-default arrow left">
        <div class="panel-heading">
          <strong> {{ $comment->name }} </strong>
          {{ $comment->comment_title }}  
          <i><span class="text-muted" 
            data-toggle="tooltip" 
            data-placement="left" 
            title=" {{ $comment->created_at }}" >
            
            @if ($comment->edited === '1')
            Edited
            {{ $comment->updated_at->diffForHumans() }} 
            @else
            {{ $comment->created_at->diffForHumans() }}
            @endif 
          </span></i>
        </div>
        <div class="panel-body">

          <div class="comment-post">
            <p> {{ $comment->comment_content }} </p><br>
            @if (Auth::check() && Auth::user()->id === $comment->user_id )
            
            
            <p class="text-left"> 
              <a href='{{ url("/comment/update/$comment->id") }}' data-toggle="tooltip" data-placement="left" title="Edit">
                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
              </a>
              <a href='{{ url("/comment/delete/$comment->id") }}' data-toggle="tooltip" data-placement="left" title="Delete">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
              </a> 
            </p>
            @elseif (Auth::guest() && Request::cookie('ip_token') !== null && Request::cookie('ip_token') === $comment->ip_token)

            <p class="text-left"> 
              <a href='{{ url("/comment/update/$comment->id") }}' data-toggle="tooltip" data-placement="left" title="Edit">
                <span class="glyphicon glyphicon-edit" aria-hidden="true"></span>
              </a>
              <a href='{{ url("/comment/delete/$comment->id") }}' data-toggle="tooltip" data-placement="left" title="Delete">
                <span class="glyphicon glyphicon-remove" aria-hidden="true"></span>
              </a> 
            </p>
            
            @endif

          </div>
          <div class="collapse" id="{{$comment->id}}reply">
            <div class="well">
              @include('comment::comments.replyform', ['commentData', $commentData])
            </div>
          </div>
          <p class="text-right">
            <a  data-toggle="collapse" href="#{{$comment->id}}reply" aria-expanded="false" aria-controls="{{$comment->id}}" data-toggle="tooltip" data-placement="left" title="Reply">
              <i class="fa fa-reply"></i>
              <span class="glyphicon glyphicon-share-alt" aria-hidden="true"></span>
            </a>
          </p>
        </div>
      </div>
    </div>
  </article>