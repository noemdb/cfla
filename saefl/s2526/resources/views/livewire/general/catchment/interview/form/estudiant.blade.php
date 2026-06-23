<fieldset class="border rounded p-2 my-2">
    <legend>II.- Datos del Estudiante Aspirante a Cupo:</legend>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'student_full_name' ; $model = 'catchment_interview.'.$name @endphp            
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="student_full_name" name="student_full_name" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'date_of_birth' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'student_age' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="number" class="form-control" id="student_age" name="student_age" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'grade_year_aspiring' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <select class="form-select" id="{{$model}}" name="{{$model}}" wire:model="{{$model}}">
                <option selected="selected" value="">Seleccione</option>
                <optgroup label="20000-EDUCACION INICIAL">
                    <option value="22">1ER NIVEL</option>
                    <option value="23">2DO NIVEL</option>
                    <option value="24">3ER NIVEL</option>
                </optgroup>
                <optgroup label="21000-EDUCACION PRIMARIA">
                    <option value="1" >1ER GRADO</option>
                    <option value="2">2DO GRADO</option>
                    <option value="3">3ER GRADO</option>
                    <option value="4">4TO GRADO</option>
                    <option value="5">5TO GRADO</option>
                    <option value="6">6TO GRADO</option>
                </optgroup>
                <optgroup label="31059B-EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA">
                    <option value="12">PRIMER AÑO</option>
                    <option value="13">SEGUNDO AÑO</option>
                    <option value="14">TERCER AÑO</option>
                </optgroup>
                <optgroup label="31059-EDUCACION MEDIA GENERAL">
                    <option value="10">CUARTO AÑO</option>
                </optgroup>
            </select>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="row my-2">
        <div class="form-group col-md-6">
            <div class="form-check">
                @php $name = 'has_siblings' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="has_siblings" name="has_siblings"  wire:model="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>

    @if ($catchment_interview->has_siblings)      
    
        <div class="row my-2">
            <div class="form-group col-md-6">
                <label for="sibling_name">Nombre del hermano/a</label>
                @php $name = 'sibling_name' ; $model = 'catchment_interview.'.$name @endphp
                <input type="text" class="form-control" id="sibling_name" name="sibling_name" wire:model.defer="{{$model}}">
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>
            <div class="form-group col-md-6">
                @php $name = 'sibling_grade_section' ; $model = 'catchment_interview.'.$name @endphp
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
                <select class="form-select" id="{{$model}}" name="{{$model}}" wire:model="{{$model}}">
                    <option selected="selected" value="">Seleccione</option>
                    <optgroup label="20000-EDUCACION INICIAL">
                        <option value="22">1ER NIVEL</option>
                        <option value="23">2DO NIVEL</option>
                        <option value="24">3ER NIVEL</option>
                    </optgroup>
                    <optgroup label="21000-EDUCACION PRIMARIA">
                        <option value="1" >1ER GRADO</option>
                        <option value="2">2DO GRADO</option>
                        <option value="3">3ER GRADO</option>
                        <option value="4">4TO GRADO</option>
                        <option value="5">5TO GRADO</option>
                        <option value="6">6TO GRADO</option>
                    </optgroup>
                    <optgroup label="31059B-EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA">
                        <option value="12">PRIMER AÑO</option>
                        <option value="13">SEGUNDO AÑO</option>
                        <option value="14">TERCER AÑO</option>
                    </optgroup>
                    <optgroup label="31059-EDUCACION MEDIA GENERAL">
                        <option value="10">CUARTO AÑO</option>
                    </optgroup>

                </select>
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>        

        </div>

        <div class="row my-2">

            <div class="form-group col-md-6">
                @php $name = 'sibling_name_2' ; $model = 'catchment_interview.'.$name @endphp
                <label for="{{$name}}" class="m-0">{{ $list_comment[$name] ?? 'Nombre del segundo hermano/a' }}</label>
                {!! Form::text($name, old($name), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name] ?? 'Nombre del segundo hermano/a',
                    'id' => $name,
                    'wire:model.defer' => $model,
                    'name' => $name,
                    'autocomplete' => 'off',
                ]) !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>
            

            <div class="form-group col-md-6">
                @php $name = 'sibling_name_3' ; $model = 'catchment_interview.'.$name @endphp
                <label for="{{$name}}" class="m-0">{{ $list_comment[$name] ?? 'Nombre del tercer hermano/a' }}</label>
                {!! Form::text($name, old($name), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name] ?? 'Nombre del tercer hermano/a',
                    'id' => $name,
                    'wire:model.defer' => $model,
                    'name' => $name,
                    'autocomplete' => 'off',
                ]) !!}
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>

        </div>
        
    @endif

    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'tutor_teacher_name' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="tutor_teacher_name" name="tutor_teacher_name" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'tutor_teacher_phone' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="tutor_teacher_phone" name="tutor_teacher_phone" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>

</fieldset>