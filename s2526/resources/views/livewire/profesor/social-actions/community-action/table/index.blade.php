<table class="table table-striped table-inverse table-sm small">    
    <thead class="thead-inverse">
        <tr>
            <th>N</th>
            <th>Título</th>
            <th>Descripcción/Lugar</th>
            <th>Fecha : Duración (Hrs)</th>
            <th>Estado</th>
            <th>Imagen</th>
            <th>Acción</th>
        </tr>
    </thead>
    <tbody>
        @forelse ($community_actions as $item)
            @php
            
            @endphp
            
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $item->title }}</td>
                <td>{{ $item->description }} <br> {{ $item->location }}</td>
                <td>{{ $item->date }} : {{ $item->duration }}</td>
                <td class="text-lefl">{{ ($item->status) ? '-ACTIVA-' : '-DESACTIVA-' }}</td>
                <td>
                    @if ($item->image)
                        <button type="button" class="btn btn-light btn-sm">
                            <i class="fa fa-file-image" aria-hidden="true" wire:click="showImagen({{$item->id}})"></i>
                        </button>
                    @endif
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group" aria-label="Small button group" wire:key="btn-group-crud-{{$item->id}}">

                        <a name="" id="" class="btn btn-warning {{ ($item->status_edit) ? null : ' disabled ' }}" href="#" role="button" wire:click="edit({{$item->id}})">
                            <i class="fas fa-pen" aria-hidden="true"></i>
                        </a>

                        {{-- <a name="" id="" class="btn btn-dark" href="{{route('profesors.social_actions.community_actions.pdf',$item->id)}}" role="button" target="_blank">
                            <i class="far fa-file-pdf"></i>
                        </a> --}}

                        <a name="" id="" class="btn btn-danger {{ ($item->status_delete) ? null : ' disabled ' }}" href="#" role="button" wire:click="alertQuestion({{$item->id}},'remove')">
                            <i class="fa fa-trash" aria-hidden="true"></i>
                        </a>                        

                    </div>    
                </td>
            </tr>
        @empty

            <tr>
                <td colspan="7">No hay datos</td>
            </tr>
        @endforelse
    </tbody>
</table>

{{-- 
status
observations
author_id --}}