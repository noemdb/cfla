<ul class="list-group">
	@foreach($alerts as $alert)
	    <li class="list-group-item text-overflow" title="{{ $alert->mensaje ?? 'default' }}">
	        <span class="text-{{ $alert->tipo ?? 'default' }}">
	            <i class="fa {{$icon_menus['alert'] ?? ''}} fa-fw"></i> {{ $alert->user->username ?? 'default' }}
	            <span class="pull-right text-muted small"> <em>{{ $alert->created_at->diffForHumans() }}</em></span>
	        </span>
	        <div class="text-{{ $alert->tipo ?? 'default' }} text-overflow">
	        	{{ (isset($show_alert)) ? $alert->mensaje : '' }}
	        </div>
		</li>
	@endforeach
</ul>
<a href="{{ route('alerts.index') }}">Mas...</a>