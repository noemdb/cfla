<div class="position-fixed w-100 h-100 d-flex align-items-center justify-content-center"
    style="top: 0; left: 0; z-index: 1050; @if(!$showModal) display: none; @endif">
    {{-- Backdrop --}}
    <div class="position-absolute w-100 h-100 bg-dark"
        style="opacity: 0.5; top: 0; left: 0;"></div>

    {{-- Modal Content --}}
    <div class="position-relative bg-white rounded shadow-lg"
        style="width: 90%; max-width: 1200px; max-height: 90vh; overflow-y: auto;">
        {{-- Header --}}
        <div class="d-flex justify-content-between align-items-center p-3 border-bottom">
            <h5 class="mb-0">
                {{ $selected_id ? 'Editar Informe Final' : 'Nuevo Informe Final' }}
            </h5>
            <button type="button" class="btn btn-link text-dark p-0" wire:click="closeModal">
                <i class="fas fa-times"></i>
            </button>
        </div>

        {{-- Body --}}
        <div class="p-3">
            <form wire:submit.prevent="{{ $selected_id ? 'update' : 'save' }}">
                {{-- Tabs Navigation --}}
                <ul class="nav nav-tabs nav-fill mb-3" id="formTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="basic-tab" data-toggle="tab" href="#basic" role="tab">
                            <i class="fas fa-info-circle"></i> Información Básica y Logros
                        </a>
                    </li>
                    {{-- <li class="nav-item">
                        <a class="nav-link" id="context-tab" data-toggle="tab" href="#context" role="tab">
                            <i class="fas fa-users"></i> Contexto y Planificación
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="activities-tab" data-toggle="tab" href="#activities" role="tab">
                            <i class="fas fa-tasks"></i> Actividades
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="observations-tab" data-toggle="tab" href="#observations" role="tab">
                            <i class="fas fa-clipboard-list"></i> Observaciones
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="expectativas-tab" data-toggle="tab" href="#expectativas" role="tab" aria-controls="expectativas" aria-selected="false">
                            <i class="fas fa-book-open mr-2"></i>Aprendizajes Esperados
                        </a>
                    </li> --}}
                </ul>

                {{-- Tabs Content --}}
                <div class="tab-content" id="formTabsContent">
                    {{-- Tab 1: Información Básica --}}
                    <div class="tab-pane fade show active" id="basic" role="tabpanel">
                        <div class="form-row">

                            {{-- Orden --}}
                            <div class="form-group col-md-2">
                                <label for="order">{{ $list_comment['order'] ?? 'Orden' }}</label>
                                <select wire:model="order" class="form-control">
                                    <option value="">Seleccione...</option>
                                    @php $i = 1; @endphp
                                    @for ($i = 1; $i < 10; $i++)
                                        <option value="{{ $i }}">{{ $i }}</option>
                                    @endfor
                                </select>
                                @error('order') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Evaluación --}}
                            <div class="form-group col-md-5">
                                <label for="pevaluacion_id">{{ $list_comment['pevaluacion_id'] ?? 'Evaluación' }}</label>
                                <select wire:model="pevaluacion_id" class="form-control">
                                    <option value="">Seleccione...</option>
                                    @foreach($list_pevaluacion as $id => $name)
                                        <option value="{{ $id }}">{{ $name ?? 'Sin objetivo' }}</option>
                                    @endforeach
                                </select>
                                @error('pevaluacion_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            {{-- Estudiante --}}
                            <div class="form-group col-md-5">
                                <label for="estudiant_id">{{ $list_comment['estudiant_id'] ?? 'Estudiante' }}</label>
                                <select wire:model.defer="estudiant_id" class="form-control">
                                    <option value="">Seleccione...</option>
                                    @if($pevaluacion_id)
                                        @php
                                            $pevaluacion = $pevaluacions->firstWhere('id', $pevaluacion_id);
                                            $estudiants = $pevaluacion ? $pevaluacion->estudiants : collect();
                                        @endphp
                                        @foreach($estudiants as $estudiant)
                                            <option value="{{ $estudiant->id }}">{{ $estudiant->ci_estudiant }} - {{ $estudiant->fullname2 }}</option>
                                        @endforeach
                                    @endif
                                </select>
                                @error('estudiant_id') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            {{-- Título --}}
                            <div class="form-group col-md-12">
                                <label for="title">{{ $list_comment['title'] ?? 'Título del Informe' }}</label>
                                <input wire:model.defer="title" type="text" class="form-control">
                                @error('title') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>

                        @if ($pevaluacion->status_official)
                            <div class="form-row pt-2">
                                <label>{{ $list_comment['expected_learnings'] ?? 'Aprendizajes Esperados' }}</label>
                                <textarea wire:model.defer="expected_learnings" class="form-control" rows="3"></textarea>
                                @error('expected_learnings') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="form-row pt-2">
                                <label>{{ $list_comment['achievements'] ?? 'Logros del estudiante' }}</label>
                                <textarea wire:model.defer="achievements" class="form-control" rows="3"></textarea>
                                @error('achievements') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="form-row pt-2">
                                <label>Observaciones</label>
                                <textarea wire:model.defer="individual_observations" class="form-control" rows="3"></textarea>
                                @error('individual_observations') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        @else
                            <div class="form-row pt-2">
                                <label>{{ $list_comment['specialist_observation'] ?? 'Observación de los Especialistas' }}</label>
                                <textarea wire:model.defer="specialist_observation" class="form-control" rows="3"></textarea>
                                @error('specialist_observation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        @endif

                    </div>

                    {{-- Tab 2: Contexto y Planificación --}}
                    {{-- <div class="tab-pane fade" id="context" role="tabpanel">
                        <div class="form-group">
                            <label>{{ $list_comment['context_group'] ?? 'Apreciación del estudiante, características, necesidades' }}</label>
                            <textarea wire:model.defer="context_group" class="form-control" rows="3"></textarea>
                            @error('context_group') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ $list_comment['planing_eject'] ?? 'Resumen de la planificación ejecutada' }}</label>
                            <textarea wire:model.defer="planing_eject" class="form-control" rows="3"></textarea>
                            @error('planing_eject') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ $list_comment['featured_project'] ?? 'Descripción del proyecto más significativo' }}</label>
                            <textarea wire:model.defer="featured_project" class="form-control" rows="3"></textarea>
                            @error('featured_project') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div> --}}

                    {{-- Tab 3: Actividades y Logros --}}
                    {{-- <div class="tab-pane fade" id="activities" role="tabpanel">
                        <div class="form-group">
                            <label>{{ $list_comment['special_activities'] ?? 'Eventos especiales' }}</label>
                            <textarea wire:model.defer="special_activities" class="form-control" rows="3"></textarea>
                            @error('special_activities') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ $list_comment['family_participation'] ?? 'Participación familiar' }}</label>
                            <textarea wire:model.defer="family_participation" class="form-control" rows="3"></textarea>
                            @error('family_participation') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>
                    </div> --}}

                    {{-- Tab 4: Observaciones --}}
                    {{-- <div class="tab-pane fade" id="observations" role="tabpanel">
                        <div class="form-group">
                            <label>{{ $list_comment['individual_observations'] ?? 'Observaciones socioafectivas' }}</label>
                            <textarea wire:model.defer="individual_observations" class="form-control" rows="3"></textarea>
                            @error('individual_observations') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ $list_comment['conclusions'] ?? 'Reflexión final del docente' }}</label>
                            <textarea wire:model.defer="conclusions" class="form-control" rows="3"></textarea>
                            @error('conclusions') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                        <div class="form-group">
                            <label>{{ $list_comment['recommendations'] ?? 'Sugerencias a la familia y equipo docente' }}</label>
                            <textarea wire:model.defer="recommendations" class="form-control" rows="3"></textarea>
                            @error('recommendations') <small class="text-danger">{{ $message }}</small> @enderror
                        </div>

                    </div> --}}

                    {{-- Tab 6: Aprendizajes Esperados --}}
                    {{-- <div class="tab-pane fade" id="expectativas" role="tabpanel" aria-labelledby="expectativas-tab">
                        <div class="accordion" id="areasAccordion">
                            @foreach($learning_areas ?? [] as $area)
                                <div class="card mb-2">
                                    <div class="card-header bg-light" id="heading{{ $area->id }}">
                                        <h2 class="mb-0">
                                            <button class="btn btn-link btn-block text-left text-primary font-weight-bold" type="button" data-toggle="collapse" data-target="#collapse{{ $area->id }}" aria-expanded="true" aria-controls="collapse{{ $area->id }}">
                                                <i class="fas fa-check-double"></i>
                                                {{ $area->name }}
                                            </button>
                                        </h2>
                                    </div>
                                    <div id="collapse{{ $area->id }}" class="collapse" aria-labelledby="heading{{ $area->id }}" data-parent="#areasAccordion">
                                        <div class="card-body">
                                            <div class="row">
                                                @foreach($area->expectations as $expectation)
                                                    <div class="col-md-6 mb-2">
                                                        <div class="custom-control custom-checkbox">
                                                            <input type="checkbox"
                                                                class="custom-control-input"
                                                                id="expectation_{{ $expectation->id }}"
                                                                wire:model.defer="selected_expectations"
                                                                value="{{ $expectation->id }}">
                                                            <label class="custom-control-label" for="expectation_{{ $expectation->id }}">
                                                                <span class="text-muted small">{{ $expectation->description }}</span>
                                                            </label>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div> --}}
                </div>

                {{-- Form Actions --}}
                <div class="form-group text-right mt-3 border-top pt-3">
                    <button type="submit" class="btn btn-success">
                        {{ $selected_id ? 'Actualizar' : 'Guardar' }} Informe
                    </button>
                    <button type="button" class="btn btn-secondary" wire:click="closeModal">Cancelar</button>
                </div>

            </form>
        </div>
    </div>
</div>



