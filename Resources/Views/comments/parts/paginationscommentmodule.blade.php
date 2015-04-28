<nav>
	<ul class="pager">
		<li class="previous">
			<a 
			href = "{{ $comments->previousPageUrl() }}" 
			id   = "{{ $commentModuleName }}commentmodulePrevious"
			@if($comments->previousPageUrl() == null)
			class="btn disabled" role="button"
			@endif
			>
			<span aria-hidden="true">&larr;</span> Previous
			</a>
		</li>
		<li class="next">
			<a 
			href = "{{ $comments->nextPageUrl() }}" 
			id   = "{{ $commentModuleName }}commentmoduleNext"
			@if($comments->nextPageUrl() == null)
			class="btn disabled" role="button"
			@endif
			>
			Next <span aria-hidden="true">&rarr;</span>
			</a>
		</li>
	</ul>
</nav>