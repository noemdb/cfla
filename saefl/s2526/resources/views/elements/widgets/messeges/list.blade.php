<ul class="list-group">
	@foreach($messeges as $messege)
	    <li class="list-group-item text-overflow" title="{{ $messege->mensaje ?? 'default' }}">
	        <span class="text-{{ $messege->class ?? 'default' }}">
	            <i class="fa {{$icon_menus['messege'] ?? ''}} fa-fw"></i> {{ $messege->user->username ?? 'default' }}
	            <span class="pull-right text-muted small"> <em>{{ $messege->created_at->diffForHumans() }}</em></span>
	        </span>
	        <div class="text-{{ $messege->TipClass ?? 'default' }} text-overflow">
	        	{{ (isset($show_messege)) ? $messege->mensaje : '' }}
	        </div>
		</li>
	@endforeach
</ul>
<a href="{{ route('messeges.index') }}">Mas...</a>