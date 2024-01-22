{{-- <x-errors /> --}}

@switch($step)
    
    @case(1) @include('livewire.app.enrollment.steper.estudiant') @break

    @default
        
@endswitch

{{-- 
"user_id" => 34
--------------------------------------
"ci_estudiant" => "32446229"
"lastname" => "GOMEZ SANCHEZ"
"name" => "ANGELES TRINIDAD"
"gender" => "Femenino"
"date_birth" => "2008-04-11"
"town_hall_birth" => "INDEPENDENCIA"
"state_birth" => "YARACUY"
"country_birth" => "VENEZUELA"
"dir_address" => "URB. COLINAS DEL NORTE, AV 1 ENTRE CALLES 8 Y 10, CASA # 055, PRADOS DEL NORTE."
"grado_id" => "9"
"pestudio_id" => "1"
"institution" => "U.E.C. LOS ANGELES"
"pending_matter" => null
"literal" => "A"
"grupo_estable_id" => 0
--------------------------------------
"age" => 0
"blood_type" => "A"
"weight" => 0
"height" => 0
"laterality" => "IZQUIERDA"
"order_born" => "1"
"group_family" => 0
"status_brother" => "true"
--------------------------------------
"ci_representant" => "12079224"
"name_representant" => "ANA YOMAIRA SANCHEZ ORDOÑEZ"
"relationship" => "Madre"
"profession_representant" => ""
"phone_representant" => "04125081606"
"email_representant" => "anayso1306@gmail.com"
"recommended_by" => null
--------------------------------------
"coexistence" => null
"status_transport_private_vehicle" => null
"status_transport_public_vehicle" => null
"status_transport_walking" => null
"status_transport_other" => null
"transport_other" => null
--------------------------------------
"status_vaccination_schedule" => null
"status_sports_potential" => null
"sports_potential" => null
"place_where_he_practices" => null
--------------------------------------
"status_illness_cardiovascular" => null
"status_illness_cancer" => null
"status_illness_lupus" => null
"status_illness_diabetes" => null
"status_illness_renal_problems" => null
"status_illness_overweight" => null
"status_illness_other" => null
"illness_other" => null
--------------------------------------
"status_conditions_intellectual_disability" => null
"status_conditions_motor_disability" => null
"status_conditions_visual_disability" => null
"status_conditions_hearing_impairment" => null
"status_conditions_outstanding_attitudes" => null
"status_conditions_autism" => null
"status_conditions_other" => null
"conditions_other" => null
--------------------------------------
"status_treated_by_specialist" => null
"specialist" => null
"status_take_medication" => null
"medication" => null
--------------------------------------
"mother_name" => null
"mother_lastname" => null
"mother_ci" => null
"mother_profession" => null
"mother_phones" => null
"mother_address" => null
"father_name" => null
"father_lastname" => null
"father_ci" => null
"father_profession" => null
"father_phones" => null
"father_address" => null

--------------------------------------
"cellphone" => null
"cellphone_representant" => ""
"twitter" => null
"instagram" => null
--}}