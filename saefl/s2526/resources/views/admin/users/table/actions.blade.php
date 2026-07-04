<div class="btn-group btn-group-sm">
    <a title="Mostrar detalles" class="btn btn-info btn-xs" href="{{ route('users.show',$id) }}">
        <i class="fas fa-info"></i>
    </a>

    <a title="Editar resgistro" class="btn btn-warning btn-xs btn-action-group-{{ $id }}" href="{{ route('users.edit',$id) }}" id="btn-edituser_{{$id}}">
        <i class="fas fa-pencil-alt"></i>
    </a>

    <a title="Eliminar {{(isset($deleted_at) ? 'DEFINITIVAMENTE':'')}}" class="btn-delete btn btn-danger btn-xs" href="{{ route('users.destroy',$id) }}" id="btn-delete-userid_{{$id}}">
        <i class="fas fa-trash"></i>
    </a>
</div>
