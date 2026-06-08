<div class="card">
    <div class="card-body">

        <div class="alert alert-warning" role="alert">

            <div class="card-title">
                <h5 class="card-title font-weight-bold">Datos para la aceptación o lista de espera.</h5>
            </div>

            <div class="form-group">
                <label for="rating" class="m-0">{{ $list_comment['rating'] }}</label>
                {!! Form::selectRange('rating', 1, 5, old('rating'), [
                    'class' => 'form-control',
                    'id' => 'rating',
                    'placeholder' => 'Seleccione',
                    // 'required',
                ]) !!}

            </div>

            <div class="form-group">
                <label for="accepted" class="m-0">{{ $list_comment['accepted'] ?? null }}</label>
                {!! Form::select('accepted', [true => 'SI', false => 'NO'], old('accepted'), [
                    'class' => 'form-control',
                    'id' => 'accepted',
                    'placeholder' => 'Seleccione',
                    // 'required',
                ]) !!}
                <small class="text-muted">Al aceptar la solicitud de matrícula, se envía una carta de aceptación a la dirección de correo electrónico del representante.</small>
            </div>

            <div class="form-group">
                <label for="status_standby" class="m-0">{{ $list_comment['status_standby'] ?? null }}. <small class="font-weight-bold">[Mensaje de notificación por email]</small></label>
                {!! Form::select('status_standby', [true => 'SI', false => 'NO'], old('status_standby'), [
                    'class' => 'form-control',
                    'id' => 'accepted',
                    'placeholder' => 'Seleccione',
                    // 'required',
                ]) !!}
                <small class="text-muted">Al asignar en lista de espera, se envía una carta digital de notificación indicando que la solicitud de matrícula queda en lista de espera, a la dirección de correo electrónico del representante.</small>
            </div>

            <div class="form-group">
                <label for="status_notify" class="m-0">Notificación al representante <small class="font-weight-bold">[Mensaje de notificación por email]</small></label>
                {!! Form::select('status_notify', [true => 'SI', false => 'NO'], old('status_notify'), [
                    'class' => 'form-control',
                    'id' => 'status_notify',
                    'placeholder' => 'Seleccione',
                ]) !!}
                <small class="text-muted">Indica si el representante ha sido notificado sobre el estado de su solicitud.</small>
            </div>

            <div class="form-group">
                <label for="observations" class="m-0">{{ $list_comment['observations'] ?? null }}</label>
                {!! Form::textarea('observations', old('observations'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['observations'],
                    'id' => 'observations',
                    'rows' => '4',
                ]) !!}
            </div>
        </div>

        <hr>

        <div class="alert alert-secondary" role="alert">

            <div class="card-title">
                <h5 class="card-title font-weight-bold">Datos de la entrevista.</h5>
            </div>

            <div class="form-group mb-3">
                <label for="full_name" class="m-0">{{ $list_comment['full_name'] }}</label>
                {!! Form::text('full_name', old('full_name'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['full_name'],
                    'id' => 'full_name',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="identification_number" class="m-0">{{ $list_comment['identification_number'] }}</label>
                {!! Form::text('identification_number', old('identification_number'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['identification_number'],
                    'id' => 'identification_number',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="age" class="m-0">{{ $list_comment['age'] }}</label>
                {!! Form::number('age', old('age'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['age'],
                    'id' => 'age',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="relationship" class="m-0">{{ $list_comment['relationship'] }}</label>
                {!! Form::text('relationship', old('relationship'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['relationship'],
                    'id' => 'relationship',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="phone_numbers" class="m-0">{{ $list_comment['phone_numbers'] }}</label>
                {!! Form::text('phone_numbers', old('phone_numbers'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['phone_numbers'],
                    'id' => 'phone_numbers',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="email" class="m-0">{{ $list_comment['email'] }}</label>
                {!! Form::email('email', old('email'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['email'],
                    'id' => 'email',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="profession_occupation" class="m-0">{{ $list_comment['profession_occupation'] }}</label>
                {!! Form::text('profession_occupation', old('profession_occupation'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['profession_occupation'],
                    'id' => 'profession_occupation',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="student_full_name" class="m-0">{{ $list_comment['student_full_name'] }}</label>
                {!! Form::text('student_full_name', old('student_full_name'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['student_full_name'],
                    'id' => 'student_full_name',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="date_of_birth" class="m-0">{{ $list_comment['date_of_birth'] }}</label>
                {!! Form::date('date_of_birth', old('date_of_birth'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['date_of_birth'],
                    'id' => 'date_of_birth',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="student_age" class="m-0">{{ $list_comment['student_age'] }}</label>
                {!! Form::number('student_age', old('student_age'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['student_age'],
                    'id' => 'student_age',
                ]) !!}
            </div>

            <div class="form-group mb-3">

                <label for="grade_year_aspiring" class="m-0">{{ $list_comment['grade_year_aspiring'] }} </label>
                {!! Form::select('grade_year_aspiring', $list_grade, old('grade_year_aspiring'), [
                    'class' => 'form-control',
                    'id' => 'grade_year_aspiring',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="has_siblings" class="m-0">{{ $list_comment['has_siblings'] }}</label>
                {!! Form::select('has_siblings', [false => 'NO',true => 'SI'], old('has_siblings'), [
                    'class' => 'form-control',
                    'id' => 'has_siblings',
                    // 'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="sibling_name" class="m-0">{{ $list_comment['sibling_name'] }}</label>
                {!! Form::text('sibling_name', old('sibling_name'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['sibling_name'],
                    'id' => 'sibling_name',
                ]) !!}
            </div>

            <div class="form-group mb-3">

                <label for="sibling_grade_section" class="m-0">{{ $list_comment['sibling_grade_section'] }} </label>
                {!! Form::select('sibling_grade_section', $list_grade, old('sibling_grade_section'), [
                    'class' => 'form-control',
                    'id' => 'sibling_grade_section',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="sibling_name_2" class="m-0">{{ $list_comment['sibling_name_2'] ?? 'Nombre del segundo hermano/a' }}</label>
                {!! Form::text('sibling_name_2', old('sibling_name_2'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['sibling_name_2'] ?? 'Nombre del segundo hermano/a',
                    'id' => 'sibling_name_2',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="sibling_name_3" class="m-0">{{ $list_comment['sibling_name_3'] ?? 'Nombre del tercer hermano/a' }}</label>
                {!! Form::text('sibling_name_3', old('sibling_name_3'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['sibling_name_3'] ?? 'Nombre del tercer hermano/a',
                    'id' => 'sibling_name_3',
                ]) !!}
            </div>


            <div class="form-group mb-3">
                <label for="tutor_teacher_name" class="m-0">{{ $list_comment['tutor_teacher_name'] }}</label>
                {!! Form::text('tutor_teacher_name', old('tutor_teacher_name'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['tutor_teacher_name'],
                    'id' => 'tutor_teacher_name',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="tutor_teacher_phone" class="m-0">{{ $list_comment['tutor_teacher_phone'] }}</label>
                {!! Form::text('tutor_teacher_phone', old('tutor_teacher_phone'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['tutor_teacher_phone'],
                    'id' => 'tutor_teacher_phone',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="religion" class="m-0">{{ $list_comment['religion'] }}</label>
                {!! Form::select('religion', $list_religions, old('religion'), [
                    'class' => 'form-control',
                    'id' => 'religion',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="living_with" class="m-0">{{ $list_comment['living_with'] }}</label>
                {!! Form::text('living_with', old('living_with'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['living_with'],
                    'id' => 'living_with',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="other_person_origin" class="m-0">{{ $list_comment['other_person_origin'] }}</label>
                {!! Form::text('other_person_origin', old('other_person_origin'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['other_person_origin'],
                    'id' => 'other_person_origin',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="reason_for_living_with_other" class="m-0">{{ $list_comment['reason_for_living_with_other'] }}</label>
                {!! Form::textarea('reason_for_living_with_other', old('reason_for_living_with_other'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['reason_for_living_with_other'],
                    'id' => 'reason_for_living_with_other',
                    'rows' => '3',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="num_family_group_members" class="m-0">{{ $list_comment['num_family_group_members'] }}</label>
                {!! Form::number('num_family_group_members', old('num_family_group_members'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['num_family_group_members'],
                    'id' => 'num_family_group_members',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="num_people_financially_dependent" class="m-0">{{ $list_comment['num_people_financially_dependent'] }}</label>
                {!! Form::number('num_people_financially_dependent', old('num_people_financially_dependent'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['num_people_financially_dependent'],
                    'id' => 'num_people_financially_dependent',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="person_responsible_attending" class="m-0">{{ $list_comment['person_responsible_attending'] }}</label>
                {!! Form::text('person_responsible_attending', old('person_responsible_attending'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['person_responsible_attending'],
                    'id' => 'person_responsible_attending',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="place_person_responsible_attending" class="m-0">{{ $list_comment['place_person_responsible_attending'] }}</label>
                {!! Form::text('place_person_responsible_attending', old('place_person_responsible_attending'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['place_person_responsible_attending'],
                    'id' => 'place_person_responsible_attending',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="position_person_responsible_attending" class="m-0">{{ $list_comment['position_person_responsible_attending'] }}</label>
                {!! Form::text('position_person_responsible_attending', old('position_person_responsible_attending'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['position_person_responsible_attending'],
                    'id' => 'position_person_responsible_attending',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="work_person_responsible_attending" class="m-0">{{ $list_comment['work_person_responsible_attending'] }}</label>
                {!! Form::text('work_person_responsible_attending', old('work_person_responsible_attending'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['work_person_responsible_attending'],
                    'id' => 'work_person_responsible_attending',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="monthly_income" class="m-0">{{ $list_comment['monthly_income'] }}</label>
                {!! Form::select('monthly_income', \App\Models\app\Enrollment\CatchmentInterview::list_monthly_income(), old('monthly_income'), [
                    'class' => 'form-control',
                    'id' => 'monthly_income',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="num_people_contributing" class="m-0">{{ $list_comment['num_people_contributing'] }}</label>
                {!! Form::number('num_people_contributing', old('num_people_contributing'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['num_people_contributing'],
                    'id' => 'num_people_contributing',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="income_source" class="m-0">{{ $list_comment['income_source'] }}</label>
                {!! Form::text('income_source', old('income_source'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['income_source'],
                    'id' => 'income_source',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="able_to_pay_dollars" class="m-0">{{ $list_comment['able_to_pay_dollars'] }}</label>
                {!! Form::select('able_to_pay_dollars', [true => 'SI', false => 'NO'], old('able_to_pay_dollars'), [
                    'class' => 'form-control',
                    'id' => 'able_to_pay_dollars',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="able_to_pay_bolivars" class="m-0">{{ $list_comment['able_to_pay_bolivars'] }}</label>
                {!! Form::select('able_to_pay_bolivars', [true => 'SI', false => 'NO'], old('able_to_pay_bolivars'), [
                    'class' => 'form-control',
                    'id' => 'able_to_pay_bolivars',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="has_payment_responsible" class="m-0">{{ $list_comment['has_payment_responsible'] }}</label>
                {!! Form::select('has_payment_responsible', [true => 'SI', false => 'NO'], old('has_payment_responsible'), [
                    'class' => 'form-control',
                    'id' => 'has_payment_responsible',
                    'placeholder' => 'Seleccione',
                ]) !!}
            </div>

            <div class="form-group mb-3">
                <label for="person_guarantor_name_phone" class="m-0">{{ $list_comment['person_guarantor_name_phone'] }}</label>
                {!! Form::text('person_guarantor_name_phone', old('person_guarantor_name_phone'), [
                    'class' => 'form-control',
                    'placeholder' => $list_comment['person_guarantor_name_phone'],
                    'id' => 'person_guarantor_name_phone',
                ]) !!}
            </div>
        </div>



    </div>
</div>
