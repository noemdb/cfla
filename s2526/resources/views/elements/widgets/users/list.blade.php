<ul class="list-group">
	@foreach($users as $user)
	    <li class="list-group-item text-overflow" title="{{ $user->username ?? 'default' }}">
	        <span class="text-{{ $user->class ?? 'default' }}">
	            <b><i class="{{$icon_menus['user'] ?? ''}}"></i> {{ $user->username ?? 'default' }}</b>
	            <span class="pull-right text-muted small"> <em>{{ $user->created_at->diffForHumans() }}</em></span>
	        </span>
	        <div class="text-{{ $user->class ?? 'default' }} text-overflow">
	        	{{$user->email ?? ''}}
	        	{{-- {{ (isset($show_task)) ? $user->email : '' }} --}}
	        </div>
	    </li>
	@endforeach
</ul>

<a href="{{ route('users.index') }}" class="btn btn-link">Mas...</a>
