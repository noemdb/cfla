@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
    $user = Auth::user();
    // $rol = ($user) ? $user->rols->where('area',$area_active)->first() : null; //dd($rols);
    // $group = ($rol) ?$rol->group : null; //dd($group);
@endphp

    <table width="100%" class="table table-striped table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_asignatura }}">Asignatura</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_lapso }}">Lapso</th>
                <th class="{{ $class_lapso }}">Actividades</th>
                {{-- <th class="{{ $class_lapso }}">Obs.[Coord.Eval.]</th> --}}
                <th class="{{ $class_action }}">Acciones</th>               
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($pevaluacions as $pevaluacion)

            @php 
            $profesor = $pevaluacion->profesor; 
            $grupo_estable = $pevaluacion->grupo_estable; 
            $pensum = $pevaluacion->pensum; 
            $grado = $pevaluacion->pensum->grado; 
            $pensum = $pevaluacion->pensum; 
            @endphp

            <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}" class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
                <td>
                    {{$loop->iteration}}
                </td>
                <td>
                    {{ $pensum->fullname ?? ''}}

                    <hr class="b-1 m-1">
                    
                    <div class="text-muted">{{ $grupo_estable->name ?? ''}}</div>

                    <hr class="b-1 m-1">

                    <div class="text-muted">{{ $profesor->fullname ?? ''}}</div>
                    
                </td>
                <td class="text-nowrap">
                    {{ $grado->name ?? ''}} {{ $pevaluacion->seccion->name ?? ''}}
                </td>
                <td class="text-nowrap">
                    {{ $pevaluacion->lapso->name ?? ''}}
                </td>

                <td>
                    @if ($pevaluacion->observations) <div>Obs.[Coord.Eval.]: {{ $pevaluacion->observations ?? ''}}</div> @endif

                    @php $activities = $pevaluacion->activities; @endphp

                    @if ($activities->count())

                    <ul class="nav nav-tabs nav-fill" id="myTab{{$pevaluacion->id}}" role="tablist">
                        @foreach ($activities as $item) 
                            <li class="nav-item">
                                <a class="nav-link px-2 {{($item->status) ? 'text-success' : 'text-warning'}} {{ ($loop->first) ? 'active':null}}" id="nav-tab-{{$item->id}}" data-toggle="tab" href="#contentTab{{$item->id}}" role="tab" aria-controls="nav-tab" aria-selected="true">
                                    <div class="d-flex justify-content-center">
                                        <div>{{$loop->iteration}}</div>
                                        <div class="px-2">
                                            <span class="font-weight-light text-right">
                                                @if ($item->status) <i class="fa fa-check text-success" aria-hidden="true"></i> @else <i class="fa fa-info text-warning" aria-hidden="true"></i> @endif 
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </li>                               
                        @endforeach
                    </ul>
                    
                    <div class="tab-content border border-top-0" id="myTabContent">
                
                        @foreach ($activities as $item)
                
                            @php $key = Str::random().$item->id @endphp
                
                            <div class="tab-pane pt-2 px-2 fade table-light {{ ($loop->first) ? 'show active':null}}" id="contentTab{{$item->id}}" role="tabpanel" aria-labelledby="nav-tab-{{$item->id}}">
                                <div class="p-2 bd-callout {{ ($item->status_resume) ? 'bd-callout-success' : 'bd-callout-warning'}} p-2">
                                    <div><strong>T.Generador/Énfasis:</strong> {{$item->topic}}</div>
                                    <div><strong>T.Temático/T.Indispensable:</strong> {{$item->thematic}}</div>
                                    <div><strong>Referentes/Ético:</strong> {{$item->references}}</div>

                                    <div class="{{($item->status) ? 'table-success' : 'table-warning'}} mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div class="flex-grow-1">
                                                <strong>Comentario:</strong>
                                                {{$item->comments}}
                                                <span class="font-weight-bold {{ ($item->status) ? 'text-success' : 'text-warning' }}">
                                                    {{ ($item->status) ? 'Aprobado' : 'En revisión' }}
                                                </span>
                                            </div>

                                            {{-- ✏️ BOTÓN EDITAR --}}
                                            <button wire:click="openModal({{ $item->id }})" class="btn btn-sm btn-outline-info ml-2">
                                                <i class="fa fa-edit"></i> Editar
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                
                        @endforeach
                
                    </div>

                    @endif

                </td>

                <td>
                    <div class="btn-group-vertical">

                        <a title="Resumen del Plan de Actividades" class="btn btn-info" href="{{route('bienestars.activities.resume',$pevaluacion->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a>
                        <a title="Plan de Actividades" class="btn btn-success" href="{{route('bienestars.activities.format',$pevaluacion->id)}}" role="button" target="_BLANK">
                            <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                        </a> 

                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

    <div class="mt-2">
        {{ $pevaluacions->links() }}
    </div>

<!-- 🧩 MODAL EDITAR COMENTARIO -->
<div wire:ignore.self class="modal fade" id="activityModal" tabindex="-1" role="dialog" aria-labelledby="activityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">

      <div class="modal-header bg-info text-white">
        <h5 class="modal-title" id="activityModalLabel">Editar Comentario de Actividad</h5>
        <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>

      <div class="modal-body">
          <div class="form-group">
              <label><strong>Tema Generador / Énfasis</strong></label>
              <input type="text" class="form-control" wire:model="activity_topic" readonly>
          </div>

          <div class="form-group">
              <label><strong>Tema Temático / Indispensable</strong></label>
              <input type="text" class="form-control" wire:model="activity_thematic" readonly>
          </div>

          <div class="form-group">
              <label class="font-weight-bold m-0 small">Comentario</label>
              <textarea class="form-control" rows="4" wire:model.defer="activity_comments"></textarea>
              @error('activity_comments')
                  <small class="text-danger">{{ $message }}</small>
              @enderror
          </div>

          <!-- ✅ NUEVO CAMPO DE ESTADO -->
          <div class="form-group mb-3">
            <div class="custom-control custom-switch">
                <input 
                    type="checkbox" 
                    wire:model="activity_status" 
                    class="custom-control-input" 
                    id="activity_status" 
                >
                <label class="custom-control-label font-weight-bold m-0 small" for="activity_status">
                    Estado de aprobación: 
                    <span class="text-success" wire:loading.remove wire:target="activity_status">
                        {{ $activity_status ? 'Aprobado' : 'En revisión' }}
                    </span>
                </label>
            </div>
            @error('activity_status')
                <small class="text-danger">{{ $message }}</small>
            @enderror
        </div>

      </div>

      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
        <button type="button" wire:click="saveComment" class="btn btn-primary">
            <i class="fa fa-save"></i> Guardar
        </button>
      </div>

    </div>
  </div>
</div>

