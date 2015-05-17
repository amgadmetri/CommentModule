<!-- comments -->
<h2 class="page-header">Comments</h2>
<section class="comment-list">
  <br>
  <div class="well" id="{{ $commentTemplateName }}CommentContent">
    {!! $commentTree !!}
  </div>

  <div class="alert alert-danger hidden" id="{{ $commentTemplateName }}addErrormessageContainer">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
    </ul>
  </div>

  <div class="alert alert-success hidden" id="{{ $commentTemplateName }}addMessageContainer">
    <ul>
    </ul>
  </div>
  
  @if($unrigesteredUserCanComment === 'True' || Auth::check())
    @include('comment::comments.parts.addcommentform', ['parent_id' => 0])
  @endif
</section>


<style type="text/css"> 
  #comment-tree
  {
    list-style: none;
  }
</style>
@include('comment::comments.assets.ajax-handlers.addcommentajaxhandler')
@include('comment::comments.assets.ajax-handlers.editcommentajaxhandler')
@include('comment::comments.assets.ajax-handlers.deletecommentajaxhandler')
@include('comment::comments.assets.ajax-handlers.paginationcommentajaxhandler')