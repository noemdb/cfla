<div class="px-2">
    <table class="table table-sm table-striped table-condensed table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Título</th>
                <th>Descripción</th>
                <th>Icon/Categoría</th>
                <th>Image</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($posts as $item)
            <tr>
                <td>{{ $item->id }}</td>
                <td>{{ $item->title }}</td>
                <td>

                    <div>{{ $item->description }}</div>

                    <div class="text-muted small font-weight-bold">
                        @if ($item->status_priority) [status_priority] @endif
                        @if ($item->status_feature) [status_feature] @endif
                        @if ($item->status_coverPage) [status_coverPage] @endif
                        @if ($item->status_pinned) [status_pinned] @endif
                        @if ($item->status_active) [status_active] @endif
                        @if ($item->status_published) [status_published] @endif
                    </div>

                </td>
                <td>
                    <div class="text-{{ $item->icon }} font-weight-bold text-uppercase">{{ $item->icon }}</div>
                    <div class="text-muted font-weight-bold">{{ $item->category->name }}</div>
                </td>
                <td>
                    @if ($item->file_url)
                    <center>
                        <div class="">
                            <div class="card" style="width: 5rem;">
                                <img src="{{ asset($item->file_url) }}" class="card-img-top" alt="...">
                            </div>
                        </div>
                    </center>
                    @endif
                </td>
                <td>
                    <div class="btn-group " role="group" aria-label="">
                        {{-- <a href="#" class="btn btn-primary">Ver</a> --}}
                        <a href="#" class="btn btn-warning btn-sm" wire:click="edit({{$item->id}})">
                            <i class="{{ $icon_menus['edit'] }}"></i>
                        </a>
                        <a href="#" class="btn btn-danger btn-sm" wire:click="delete({{$item->id}})">
                            <i class="{{ $icon_menus['eliminar'] }}"></i>
                        </a>
                    </div>

                    <!-- Agrega más acciones según tus necesidades (editar, eliminar, etc.) -->
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{ $posts->links() }}
</div>