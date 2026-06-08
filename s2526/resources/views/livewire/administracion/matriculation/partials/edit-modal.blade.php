<div class="modal fade show" style="display: block; background-color: rgba(0,0,0,0.5);" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-edit"></i> Editar Entrevista - {{ $selectedInterview->student_full_name }}
                </h5>
                <button type="button" class="close" wire:click="closeModal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">

                {{-- Sistema de Tabs --}}
                <ul class="nav nav-tabs" id="interviewTabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active" id="status-tab" data-toggle="tab" href="#status" role="tab">
                            <i class="fas fa-check-circle"></i> Estado
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="student-tab" data-toggle="tab" href="#student" role="tab">
                            <i class="fas fa-user-graduate"></i> Estudiante
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="representative-tab" data-toggle="tab" href="#representative" role="tab">
                            <i class="fas fa-user"></i> Representante
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="family-tab" data-toggle="tab" href="#family" role="tab">
                            <i class="fas fa-users"></i> Familia
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="economic-tab" data-toggle="tab" href="#economic" role="tab">
                            <i class="fas fa-dollar-sign"></i> Económico
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="school-tab" data-toggle="tab" href="#school" role="tab">
                            <i class="fas fa-school"></i> Colegio
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="agreements-tab" data-toggle="tab" href="#agreements" role="tab">
                            <i class="fas fa-handshake"></i> Acuerdos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" id="religion-tab" data-toggle="tab" href="#religion" role="tab">
                            <i class="fas fa-cross"></i> Religión
                        </a>
                    </li>
                </ul>

                <form wire:submit.prevent="updateInterview">
                    <div class="tab-content mt-3" id="interviewTabsContent">

                        {{-- TAB 1: ESTADO Y CALIFICACIÓN --}}
                        <div class="tab-pane fade show active" id="status" role="tabpanel">
                            <div class="alert alert-warning">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-star"></i> Datos para la aceptación o lista de espera
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['rating'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.rating">
                                                <option value="">Seleccione</option>
                                                @for($i = 1; $i <= 5; $i++)
                                                    <option value="{{ $i }}">{{ $i }}</option>
                                                @endfor
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['accepted'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.accepted">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                            <small class="text-muted">Al aceptar la solicitud de matrícula, se envía una carta de aceptación.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['status_standby'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.status_standby">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                            <small class="text-muted">Al asignar en lista de espera, se envía una carta digital de notificación.</small>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>Notificación al representante</label>
                                            <select class="form-control" wire:model.defer="statusNotify">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                            <small class="text-muted">Indica si el representante ha sido notificado sobre el estado.</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['observations'] }}</label>
                                    <textarea class="form-control" rows="4" wire:model.defer="interviewData.observations"
                                              placeholder="{{ $list_comment['observations'] }}"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 2: INFORMACIÓN DEL ESTUDIANTE --}}
                        <div class="tab-pane fade" id="student" role="tabpanel">
                            <div class="alert alert-info">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-user-graduate"></i> Información del Estudiante
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['student_full_name'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.student_full_name"
                                                   placeholder="{{ $list_comment['student_full_name'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ $list_comment['date_of_birth'] }}</label>
                                            <input type="date" class="form-control" wire:model.defer="interviewData.date_of_birth">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ $list_comment['student_age'] }}</label>
                                            <input type="number" class="form-control" wire:model.defer="interviewData.student_age"
                                                   placeholder="{{ $list_comment['student_age'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['grade_year_aspiring'] }}</label>
                                    <select class="form-control" wire:model.defer="interviewData.grade_year_aspiring">
                                        <option value="">Seleccione</option>
                                        @foreach($list_grade as $group => $grades)
                                            <optgroup label="{{ $group }}">
                                                @foreach($grades as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </optgroup>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Información de hermanos --}}
                                <hr>
                                <h6 class="text-primary">Información de Hermanos</h6>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['has_siblings'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.has_siblings">
                                                <option value="0">NO</option>
                                                <option value="1">SI</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['sibling_name'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.sibling_name"
                                                   placeholder="{{ $list_comment['sibling_name'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['sibling_grade_section'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.sibling_grade_section">
                                                <option value="">Seleccione</option>
                                                @foreach($list_grade as $group => $grades)
                                                    <optgroup label="{{ $group }}">
                                                        @foreach($grades as $id => $name)
                                                            <option value="{{ $id }}">{{ $name }}</option>
                                                        @endforeach
                                                    </optgroup>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['sibling_name_2'] ?? 'Nombre del segundo hermano/a' }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.sibling_name_2"
                                                   placeholder="Nombre del segundo hermano/a">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['sibling_name_3'] ?? 'Nombre del tercer hermano/a' }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.sibling_name_3"
                                                   placeholder="Nombre del tercer hermano/a">
                                        </div>
                                    </div>
                                </div>

                                {{-- Información del tutor/docente --}}
                                <hr>
                                <h6 class="text-primary">Tutor/Docente</h6>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['tutor_teacher_name'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.tutor_teacher_name"
                                                   placeholder="{{ $list_comment['tutor_teacher_name'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['tutor_teacher_phone'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.tutor_teacher_phone"
                                                   placeholder="{{ $list_comment['tutor_teacher_phone'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 3: INFORMACIÓN DEL REPRESENTANTE --}}
                        <div class="tab-pane fade" id="representative" role="tabpanel">
                            <div class="alert alert-success">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-user"></i> Información del Representante
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['full_name'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.full_name"
                                                   placeholder="{{ $list_comment['full_name'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ $list_comment['identification_number'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.identification_number"
                                                   placeholder="{{ $list_comment['identification_number'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>{{ $list_comment['age'] }}</label>
                                            <input type="number" class="form-control" wire:model.defer="interviewData.age"
                                                   placeholder="{{ $list_comment['age'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['relationship'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.relationship"
                                                   placeholder="{{ $list_comment['relationship'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['phone_numbers'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.phone_numbers"
                                                   placeholder="{{ $list_comment['phone_numbers'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['email'] }}</label>
                                            <input type="email" class="form-control" wire:model.defer="interviewData.email"
                                                   placeholder="{{ $list_comment['email'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['email_alternate'] }}</label>
                                            <input type="email" class="form-control" wire:model.defer="interviewData.email_alternate"
                                                   placeholder="{{ $list_comment['email_alternate'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['profession_occupation'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.profession_occupation"
                                                   placeholder="{{ $list_comment['profession_occupation'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 4: INFORMACIÓN FAMILIAR --}}
                        <div class="tab-pane fade" id="family" role="tabpanel">
                            <div class="alert alert-secondary">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-users"></i> Información Familiar
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['living_with'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.living_with">
                                                <option value="">Seleccione</option>
                                                <option value="Madre">Madre</option>
                                                <option value="Padre">Padre</option>
                                                <option value="Ambos">Ambos</option>
                                                <option value="Hermano(a)">Hermano(a)</option>
                                                <option value="Otros">Otros</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['other_person_origin'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.other_person_origin"
                                                   placeholder="{{ $list_comment['other_person_origin'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['reason_for_living_with_other'] }}</label>
                                    <textarea class="form-control" rows="3" wire:model.defer="interviewData.reason_for_living_with_other"
                                              placeholder="{{ $list_comment['reason_for_living_with_other'] }}"></textarea>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['num_family_group_members'] }}</label>
                                            <input type="number" class="form-control" wire:model.defer="interviewData.num_family_group_members"
                                                   placeholder="{{ $list_comment['num_family_group_members'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['num_people_financially_dependent'] }}</label>
                                            <input type="number" class="form-control" wire:model.defer="interviewData.num_people_financially_dependent"
                                                   placeholder="{{ $list_comment['num_people_financially_dependent'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['family_member_studied_worked_at_school'] }}</label>
                                    <input type="text" class="form-control" wire:model.defer="interviewData.family_member_studied_worked_at_school"
                                           placeholder="{{ $list_comment['family_member_studied_worked_at_school'] }}">
                                </div>

                                {{-- Persona responsable --}}
                                <hr>
                                <h6 class="text-primary">Persona Responsable de Asistir al Colegio</h6>

                                <div class="form-group">
                                    <label>{{ $list_comment['person_responsible_attending'] }}</label>
                                    <input type="text" class="form-control" wire:model.defer="interviewData.person_responsible_attending"
                                           placeholder="{{ $list_comment['person_responsible_attending'] }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['place_person_responsible_attending'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.place_person_responsible_attending"
                                                   placeholder="{{ $list_comment['place_person_responsible_attending'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['position_person_responsible_attending'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.position_person_responsible_attending"
                                                   placeholder="{{ $list_comment['position_person_responsible_attending'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['work_person_responsible_attending'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.work_person_responsible_attending"
                                                   placeholder="{{ $list_comment['work_person_responsible_attending'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 5: INFORMACIÓN ECONÓMICA --}}
                        <div class="tab-pane fade" id="economic" role="tabpanel">
                            <div class="alert alert-warning">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-dollar-sign"></i> Información Económica
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['monthly_income'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.monthly_income">
                                                <option value="">Seleccione</option>
                                                <option value="Salario minimo">Salario mínimo</option>
                                                <option value="Entre 10 y 70$">Entre 10 y 70$</option>
                                                <option value="Entre  70 y 150$">Entre 70 y 150$</option>
                                                <option value="150$ o más">150$ o más</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['num_people_contributing'] }}</label>
                                            <input type="number" class="form-control" wire:model.defer="interviewData.num_people_contributing"
                                                   placeholder="{{ $list_comment['num_people_contributing'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['income_source'] }}</label>
                                    <input type="text" class="form-control" wire:model.defer="interviewData.income_source"
                                           placeholder="{{ $list_comment['income_source'] }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['able_to_pay_dollars'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.able_to_pay_dollars">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['able_to_pay_bolivars'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.able_to_pay_bolivars">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['has_payment_responsible'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.has_payment_responsible">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['person_guarantor_name_phone'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.person_guarantor_name_phone"
                                                   placeholder="{{ $list_comment['person_guarantor_name_phone'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 6: INFORMACIÓN DEL COLEGIO --}}
                        <div class="tab-pane fade" id="school" role="tabpanel">
                            <div class="alert alert-primary">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-school"></i> Información sobre el Colegio
                                </h5>

                                <div class="form-group">
                                    <label>{{ $list_comment['knowledge_of_school'] }}</label>
                                    <input type="text" class="form-control" wire:model.defer="interviewData.knowledge_of_school"
                                           placeholder="{{ $list_comment['knowledge_of_school'] }}">
                                </div>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['studied_at_school'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.studied_at_school">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['year_of_graduation'] }}</label>
                                            <input type="number" class="form-control" wire:model.defer="interviewData.year_of_graduation"
                                                   placeholder="{{ $list_comment['year_of_graduation'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['recommendation_from_school'] }}</label>
                                            <select class="form-control" wire:model.defer="interviewData.recommendation_from_school">
                                                <option value="">Seleccione</option>
                                                <option value="1">SI</option>
                                                <option value="0">NO</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['academic_director'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.academic_director"
                                                   placeholder="{{ $list_comment['academic_director'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>{{ $list_comment['school_director'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.school_director"
                                                   placeholder="{{ $list_comment['school_director'] }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['teachers_worked_at_school'] }}</label>
                                    <input type="text" class="form-control" wire:model.defer="interviewData.teachers_worked_at_school"
                                           placeholder="{{ $list_comment['teachers_worked_at_school'] }}">
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['reason_for_choosing_institution'] }}</label>
                                    <textarea class="form-control" rows="4" wire:model.defer="interviewData.reason_for_choosing_institution"
                                              placeholder="{{ $list_comment['reason_for_choosing_institution'] }}"></textarea>
                                </div>

                                {{-- Información del recomendante --}}
                                <hr>
                                <h6 class="text-primary">Información del Recomendante</h6>

                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['recommender_name'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.recommender_name"
                                                   placeholder="{{ $list_comment['recommender_name'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['recommender_affinity'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.recommender_affinity"
                                                   placeholder="{{ $list_comment['recommender_affinity'] }}">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>{{ $list_comment['recommender_phone'] }}</label>
                                            <input type="text" class="form-control" wire:model.defer="interviewData.recommender_phone"
                                                   placeholder="{{ $list_comment['recommender_phone'] }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 7: ACUERDOS Y COMPROMISOS --}}
                        <div class="tab-pane fade" id="agreements" role="tabpanel">
                            <div class="alert alert-info">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-handshake"></i> Acuerdos y Compromisos
                                </h5>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.agreement_to_code_of_conduct" value="1">
                                                <label class="form-check-label">{{ $list_comment['agreement_to_code_of_conduct'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.respect_communication_channels" value="1">
                                                <label class="form-check-label">{{ $list_comment['respect_communication_channels'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.ensure_compliance_with_school_activities" value="1">
                                                <label class="form-check-label">{{ $list_comment['ensure_compliance_with_school_activities'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.comply_with_school_uniform" value="1">
                                                <label class="form-check-label">{{ $list_comment['comply_with_school_uniform'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.respect_authorities_directives" value="1">
                                                <label class="form-check-label">{{ $list_comment['respect_authorities_directives'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.pay_installments_on_time" value="1">
                                                <label class="form-check-label">{{ $list_comment['pay_installments_on_time'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.respect_parent_assembly_agreements" value="1">
                                                <label class="form-check-label">{{ $list_comment['respect_parent_assembly_agreements'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.pay_overdue_installments" value="1">
                                                <label class="form-check-label">{{ $list_comment['pay_overdue_installments'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- TAB 8: INFORMACIÓN RELIGIOSA --}}
                        <div class="tab-pane fade" id="religion" role="tabpanel">
                            <div class="alert alert-light">
                                <h5 class="font-weight-bold">
                                    <i class="fas fa-cross"></i> Información Religiosa
                                </h5>

                                <div class="form-group">
                                    <label>{{ $list_comment['religion'] }}</label>
                                    <select class="form-control" wire:model.defer="interviewData.religion">
                                        <option value="">Seleccione</option>
                                        @foreach($list_religions as $key => $religion)
                                            <option value="{{ $key }}">{{ $religion }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.awareness_of_catholic_school_affiliation" value="1">
                                                <label class="form-check-label">{{ $list_comment['awareness_of_catholic_school_affiliation'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <div class="form-check">
                                                <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.agreement_to_catholic_formation" value="1">
                                                <label class="form-check-label">{{ $list_comment['agreement_to_catholic_formation'] }}</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="form-check">
                                        <input type="checkbox" class="form-check-input" wire:model.defer="interviewData.agreement_to_participate_in_catholic_activities" value="1">
                                        <label class="form-check-label">{{ $list_comment['agreement_to_participate_in_catholic_activities'] }}</label>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label>{{ $list_comment['justification_for_not_participating_in_catholic_activities'] }}</label>
                                    <textarea class="form-control" rows="3" wire:model.defer="interviewData.justification_for_not_participating_in_catholic_activities"
                                              placeholder="{{ $list_comment['justification_for_not_participating_in_catholic_activities'] }}"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Errores de validación --}}
                    @if ($errors->any())
                        <div class="alert alert-danger mt-3">
                            <h6><i class="fas fa-exclamation-triangle"></i> Errores de validación:</h6>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" wire:click="closeModal">
                    <i class="fas fa-times"></i> Cancelar
                </button>
                <button type="button" class="btn btn-primary" wire:click="updateInterview" wire:loading.attr="disabled">
                    <span wire:loading.remove>
                        <i class="fas fa-save"></i> Guardar Cambios
                    </span>
                    <span wire:loading>
                        <i class="fas fa-spinner fa-spin"></i> Guardando...
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>

@section('livewireScriptsCustom')
@parent
{{-- Script para manejar los tabs --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Activar el primer tab por defecto
        const firstTab = document.querySelector('#status-tab');
        if (firstTab) {
            firstTab.classList.add('active');
        }
    });
</script>
@endsection


