<nav>
	<ul class="pagination">
		<li class="previous">
			<a 
			href = "{{ $comments->previousPageUrl() }}" 
			id   = "{{ $commentTemplateName }}commentmodulePrevious"
			@if($comments->previousPageUrl() == null)
				class="btn disabled" role="button"
			@endif
			>
			<span aria-hidden="true">&larr;</span> Previous
			</a>
		</li>

		@for($i = 1 ; $i <= $comments->lastPage() ; $i++)
			<li 
			@if($comments->currentPage() == $i)
				class="active"
			@endif
			>
				<a 
				href ="{{ $comments->url($i) }}"
				id   ="{{ $commentTemplateName }}commentmoduleLinks"
				>
				{{ $i }}
				</a>
			</li>
		@endfor

		<li class="next">
			<a 
			href = "{{ $comments->nextPageUrl() }}" 
			id   = "{{ $commentTemplateName }}commentmoduleNext"
			@if($comments->nextPageUrl() == null)
				class="btn disabled" role="button"
			@endif
			>
			Next <span aria-hidden="true">&rarr;</span>
			</a>
		</li>
	</ul>
</nav>