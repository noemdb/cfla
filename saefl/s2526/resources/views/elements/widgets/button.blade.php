<button 
type="{{{ isset($type) ? $class : 'button' }}}" 
class="btn btn-{{{ isset($class) ? $class : 'default' }}} {{{ isset($rounded) ? 'btn-rounded' : ''}}} {{{ isset($bordered) ? 'btn-bordered' : ''}}} @if (isset($size)) btn-{{$size}} @endif  {{{ isset($disabled) ? 'disabled' : '' }}}">
{{ $value }}
</button> 