@extends('comment::master')
<style type="text/css"> 
  li {
    list-style: none;
  }
</style>
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
    <h1>"Google" launches new "robot" that can perform surgeries</h1>
    <p>Google this period, the company is working in collaboration with 
      the famous developed a new robot that can performs accurate surgeries 
      completely without errors Johnson and Johnson company, and the company says
      that the new robots are supposed to make it easier for doctors operations and 
      help them during surgery has developed is unprecedented in any robot and another 
      decrease the risk that task surgeries over safety ratio, according to the US site mashable. 
      Steps making new Android today it was announced this step is preparation with a group of 
      scientists and developers specializing in the fields of robotics for the integration of advanced technologies 
    </p><br>
    
    <!-- comments -->
    <div class="container">
      <div class="row">
        <div class="col-md-7">
          <h2 class="page-header">Comments</h2>
          <section class="comment-list">
            <br>
            <div class="well">
              {!! $html !!}
            </div>
            @include('comment::comments.addcommentform', ['commentData', $commentData])
          </section>
        </div>
      </div>
    </div>


  </div>  
</div>
@stop



