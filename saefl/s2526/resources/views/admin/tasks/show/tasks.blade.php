@isset($tasks)

    <div id="accordion">

        @foreach($tasks as $task)

            <div class="card">
                <div class="card-header" id="heading_task_{{ $task->id ?? '' }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link btn-sm" data-toggle="collapse" data-target="#accordion_task_{{ $task->id ?? '' }}" aria-expanded="true" aria-conttasks="accordion_task_{{ $task->id ?? '' }}">
                            <span class="text text-{{ $task->tipo ?? '' }}">{{ $task->truncdescripcion }}</span>
                        </button>
                    </h5>
                </div>

                <div id="accordion_task_{{ $task->id ?? '' }}" class="collapse" aria-labelledby="heading_task_{{ $task->id ?? '' }}" data-parent="#accordion">
                    <div class="card-body">

                        @includeIf('admin.tasks.show.task')

                        <a class="btn btn-warning w-100" href="{{ route('tasks.edit',$task->id)}}" taske="button">
                            Actualizar
                            <i class="{{$icon_menus['task'] ?? ''}}"></i>
                        </a>                       

                    </div>
                </div>
            </div>

        @endforeach

    </div>

@endisset