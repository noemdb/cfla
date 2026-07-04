<ul class="list-group">
	@foreach($tasks as $task)
	    <li class="list-group-item text-overflow" title="{{ $task->descripcion ?? 'default' }}">
	        <span class="text-{{ $task->tipo ?? 'default' }}">
	            <b><i class="fa fa-tasks fa-fw"></i> {{ $task->codigo ?? 'default' }}</b>
	            <span class="pull-right text-muted small"> <em>{{ $task->created_at->diffForHumans() }}</em></span>
	        </span>
	        <div class="text-{{ $task->tipo ?? 'default' }} text-overflow">
	        	{{ (isset($show_task)) ? $task->descripcion : '' }}
	        </div>
	    </li>
	@endforeach
</ul>

<a href="{{ route('tasks.index') }}" class="btn btn-link">Mas...</a>
