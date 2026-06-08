<div class="container d-block">
    <div class="row">
        <div class="col-6">
            <label for="ti_teacher" class="font-weight-bold text-secondary m-0">{{$list_comment['ti_teacher'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->ti_teacher ?? ''}}
            </div>
            <label for="user_id" class="font-weight-bold text-secondary m-0">{{$list_comment['user_id'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->user->username ?? ''}}
            </div>
            <label for="ci_profesor" class="font-weight-bold text-secondary m-0">{{$list_comment['ci_profesor'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->ci_profesor ?? ''}}
            </div>
            <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->fullname ?? ''}}
            </div>
            <label for="gender" class="font-weight-bold text-secondary m-0">{{$list_comment['gender'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->gender ?? ''}}
            </div>
            <label for="date_birth" class="font-weight-bold text-secondary m-0">{{$list_comment['date_birth'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{ (!empty($profesor->date_birth)) ? f_date($profesor->date_birth) : null}}
            </div>
            <label for="city_birth" class="font-weight-bold text-secondary m-0">{{$list_comment['city_birth'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->city_birth ?? ''}}
            </div>
        </div>
        <div class="col-6">
            <label for="town_hall_birth" class="font-weight-bold text-secondary m-0">{{$list_comment['town_hall_birth'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->town_hall_birth ?? ''}}
            </div>
            <label for="state_birth" class="font-weight-bold text-secondary m-0">{{$list_comment['state_birth'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->state_birth ?? ''}}
            </div>
            <label for="country_birth" class="font-weight-bold text-secondary m-0">{{$list_comment['country_birth'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->country_birth ?? ''}}
            </div>
            <label for="dir_address" class="font-weight-bold text-secondary m-0">{{$list_comment['dir_address'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->dir_address ?? ''}}
            </div>
            <label for="phone" class="font-weight-bold text-secondary m-0">{{$list_comment['phone'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->phone ?? ''}}
            </div>
            <label for="email" class="font-weight-bold text-secondary m-0">{{$list_comment['phone'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->email ?? ''}} / {{$profesor->email ?? ''}}
            </div>
            <label for="status_active" class="font-weight-bold text-secondary m-0">{{$list_comment['status_active'] ?? ''}}</label>
            <div class="alert alert-secondary">
                {{$profesor->status_active ?? ''}}
            </div>
        </div>
        
    </div>
</div>
