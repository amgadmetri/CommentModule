<!-- comments -->
<h2 class="page-header">Comments</h2>
<section class="comment-list">
  <br>
  <div class="well" id="{{ $commentModuleName }}CommentContent">
    {!! $commentTree !!}
  </div>

  <div class="alert alert-danger" id="{{ $commentModuleName }}errormessageContainer">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
    </ul>
  </div>

  <div class="alert alert-success" id="{{ $commentModuleName }}messageContainer">
    <ul>
    </ul>
  </div>

  @include('comment::comments.parts.addcommentform', ['parent_id' => 0])
</section>


<style type="text/css"> 
  #comment-tree
  {
    list-style: none;
  }
</style>
@include('comment::comments.assets.ajax-handlers.commentajaxhandler')
@include('comment::comments.assets.ajax-handlers.paginationcommentajaxhandler')