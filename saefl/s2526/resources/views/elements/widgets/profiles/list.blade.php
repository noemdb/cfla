<ul class="list-group">
	@foreach($profiles as $profile)
	    <li class="list-group-item text-overflow" title="{{ $profile->fullname ?? 'default' }}">
	        <span class="text-{{ $profile->user->class ?? 'default' }}">
	            <b>
	            	<i class="{{$icon_menus['profile'] ?? ''}}"></i>
	            	{{ $profile->fullname ?? '' }} 	            	
	            </b>
	            <span class="pull-right text-muted small"> <em>{{ $profile->created_at->diffForHumans() }}</em></span>
	        </span>
	        <div class="text-{{ $profile->user->class ?? 'default' }} text-overflow">
	        	{{$profile->user->username ?? ''}}
	        	{{-- {{ (isset($show_task)) ? $profile->email : '' }} --}}
	        </div>
	    </li>
	@endforeach
</ul>

<a href="{{ route('profiles.index') }}" class="btn btn-link">Mas...</a>
