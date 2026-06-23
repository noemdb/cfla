<div>
    <!-- Botón para abrir el modal -->
    <button type="button" class="btn btn-primary mb-3" wire:click="createProfesor">
        <i class="fas fa-plus"></i> Nuevo Profesor
    </button>

    <!-- Modal del Wizard -->
    <div wire:ignore.self class="modal fade" id="profesorWizardModal" tabindex="-1" role="dialog" aria-labelledby="profesorWizardModalLabel" aria-hidden="true" data-backdrop="static">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="profesorWizardModalLabel">
                        {{ $profesor_id ? 'Editar Profesor' : 'Registrar Nuevo Profesor' }}
                    </h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" wire:click="resetForm">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                
                <div class="modal-body">
                    <!-- Progress Bar / Pasos -->
                    <div class="d-flex justify-content-between mb-4">
                        <div class="text-center w-100 {{ $wizardStep >= 1 ? 'text-primary fw-bold' : 'text-muted' }}">
                            <div class="mb-1"><i class="fas fa-user-circle fa-2x"></i></div>
                            <small>1. Datos Personales</small>
                        </div>
                        <div class="text-center w-100 {{ $wizardStep >= 2 ? 'text-primary fw-bold' : 'text-muted' }}">
                            <div class="mb-1"><i class="fas fa-address-book fa-2x"></i></div>
                            <small>2. Contacto</small>
                        </div>
                        <div class="text-center w-100 {{ $wizardStep >= 3 ? 'text-primary fw-bold' : 'text-muted' }}">
                            <div class="mb-1"><i class="fas fa-user-cog fa-2x"></i></div>
                            <small>3. Cuenta y Rol</small>
                        </div>
                    </div>
                    <div class="progress mb-4" style="height: 5px;">
                        <div class="progress-bar" role="progressbar" style="width: {{ ($wizardStep / 3) * 100 }}%;" aria-valuenow="{{ ($wizardStep / 3) * 100 }}" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>

                    <!-- Paso 1: Datos Personales -->
                    @if ($wizardStep == 1)
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="ci_profesor">Cédula de Identidad <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('ci_profesor') is-invalid @enderror" id="ci_profesor" wire:model.defer="ci_profesor" placeholder="V-12345678">
                                @error('ci_profesor') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="ti_teacher">Tipo de Facilitador</label>
                                <input type="text" class="form-control @error('ti_teacher') is-invalid @enderror" id="ti_teacher" wire:model.defer="ti_teacher" placeholder="Ej. Titular, Suplente">
                                @error('ti_teacher') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="name">Nombres <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" wire:model.defer="name">
                                @error('name') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="lastname">Apellidos <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('lastname') is-invalid @enderror" id="lastname" wire:model.defer="lastname">
                                @error('lastname') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gender">Género <span class="text-danger">*</span></label>
                                <select class="form-control @error('gender') is-invalid @enderror" id="gender" wire:model.defer="gender">
                                    <option value="">Seleccione...</option>
                                    <option value="M">Masculino</option>
                                    <option value="F">Femenino</option>
                                </select>
                                @error('gender') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="date_birth">Fecha de Nacimiento</label>
                                <input type="date" class="form-control @error('date_birth') is-invalid @enderror" id="date_birth" wire:model.defer="date_birth">
                                @error('date_birth') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Paso 2: Datos de Contacto -->
                    @if ($wizardStep == 2)
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label for="email">Correo Electrónico <span class="text-danger">*</span></label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" wire:model.defer="email" placeholder="ejemplo@correo.com">
                                @error('email') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="gsemail">Correo GSuite</label>
                                <input type="email" class="form-control @error('gsemail') is-invalid @enderror" id="gsemail" wire:model.defer="gsemail">
                                @error('gsemail') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="phone">Teléfono Fijo</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" id="phone" wire:model.defer="phone">
                                @error('phone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="cellphone">Teléfono Celular</label>
                                <input type="text" class="form-control @error('cellphone') is-invalid @enderror" id="cellphone" wire:model.defer="cellphone">
                                @error('cellphone') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="whatsapp">WhatsApp</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" id="whatsapp" wire:model.defer="whatsapp">
                                @error('whatsapp') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-12 form-group">
                                <label for="dir_address">Dirección de Residencia</label>
                                <textarea class="form-control @error('dir_address') is-invalid @enderror" id="dir_address" wire:model.defer="dir_address" rows="2"></textarea>
                                @error('dir_address') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    @endif

                    <!-- Paso 3: Cuenta y Rol -->
                    @if ($wizardStep == 3)
                        <div class="row">
                            <div class="col-md-12">
                                <h6 class="border-bottom pb-2 mb-3 text-primary"><i class="fas fa-user-lock"></i> Datos de la Cuenta</h6>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="user_username">Nombre de Usuario <span class="text-danger">*</span></label>
                                <input type="text" class="form-control @error('user_username') is-invalid @enderror" id="user_username" wire:model.defer="user_username">
                                @error('user_username') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="user_password">Contraseña</label>
                                <input type="password" class="form-control @error('user_password') is-invalid @enderror" id="user_password" wire:model.defer="user_password" placeholder="Dejar vacío para no cambiar (Por defecto CI)">
                                @error('user_password') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12 mt-2">
                                <h6 class="border-bottom pb-2 mb-3 text-primary"><i class="fas fa-id-badge"></i> Rol del Profesor</h6>
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="rol_finicial">Fecha Inicial del Rol <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('rol_finicial') is-invalid @enderror" id="rol_finicial" wire:model.defer="rol_finicial">
                                @error('rol_finicial') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>
                            <div class="col-md-6 form-group">
                                <label for="rol_ffinal">Fecha Final del Rol <span class="text-danger">*</span></label>
                                <input type="date" class="form-control @error('rol_ffinal') is-invalid @enderror" id="rol_ffinal" wire:model.defer="rol_ffinal">
                                @error('rol_ffinal') <span class="invalid-feedback">{{ $message }}</span> @enderror
                            </div>

                            <div class="col-md-12 form-group mt-2">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" class="custom-control-input" id="status_active" wire:model.defer="status_active">
                                    <label class="custom-control-label" for="status_active">El profesor se encuentra actualmente activo</label>
                                </div>
                            </div>
                        </div>
                    @endif

                </div>
                
                <div class="modal-footer bg-light">
                    @if ($wizardStep > 1)
                        <button type="button" class="btn btn-secondary" wire:click="prevStep">
                            <i class="fas fa-arrow-left"></i> Anterior
                        </button>
                    @endif
                    
                    @if ($wizardStep < 3)
                        <button type="button" class="btn btn-primary" wire:click="nextStep">
                            Siguiente <i class="fas fa-arrow-right"></i>
                        </button>
                    @else
                        <button type="button" class="btn btn-success" wire:click="submit">
                            <i class="fas fa-save"></i> Guardar Profesor
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <script>
        window.addEventListener('show-profesor-modal', event => {
            $('#profesorWizardModal').modal('show');
        });
        
        window.addEventListener('hide-profesor-modal', event => {
            $('#profesorWizardModal').modal('hide');
        });
    </script>
</div>
