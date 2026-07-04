@isset($tasks)

<h4>Últimas 5 </h4>
@php ($tasks = $tasks->take(5))

    <div id="accordion">

        @foreach($tasks as $task)

            {{-- <div class="card"> --}}
            <div class="card border-{{$task->class ?? 'secondary'}} border-top-0 border-right-0 border-bottom-0">
                <div class="card-header" id="heading_task_{{ $task->id ?? '' }}">
                    <h5 class="mb-0">
                        <button class="btn btn-link btn-sm" data-toggle="collapse" data-target="#accordion_task_{{ $task->id ?? '' }}" aria-expanded="true" aria-conttasks="accordion_task_{{ $task->id ?? '' }}">
                            <span class="text text-{{ $task->tipo ?? '' }}">{{ $task->truncdescripcion }}</span>
                        </button>
                    </h5>
                </div>

                <div id="accordion_task_{{ $task->id ?? '' }}" class="collapse" aria-labelledby="heading_task_{{ $task->id ?? '' }}" data-parent="#accordion">
                    <div class="card-body">

                        @include('admin.tasks.show.simple')

                    </div>
                </div>
            </div>

        @endforeach

    </div>

@endisset