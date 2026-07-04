<div class="d-block">
    <div class="container">

        <div class="row">
            <div class="col-6">

                <label for="pestudio_id" class="font-weight-bold text-secondary m-0">{{$list_comment['pestudio_id'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->pestudio->name ?? ''}}
                </div>
                <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->name ?? ''}}
                </div>
                <label for="code" class="font-weight-bold text-secondary m-0">{{$list_comment['code'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->code ?? ''}}
                </div>
                <label for="code_sm" class="font-weight-bold text-secondary m-0">{{$list_comment['code_sm'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->code_sm ?? ''}}
                </div>
                <label for="name" class="font-weight-bold text-secondary m-0">{{$list_comment['name'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->name ?? ''}}
                </div>
                <label for="tescala" class="font-weight-bold text-secondary m-0">{{$list_comment['tescala'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->tescala ?? ''}}
                </div>
                <label for="order" class="font-weight-bold text-secondary m-0">{{$list_comment['order'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->order ?? ''}}
                </div>
                <label for="hour_t_week" class="font-weight-bold text-secondary m-0">{{$list_comment['hour_t_week'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->hour_t_week ?? ''}}
                </div>
                <label for="hour_p_week" class="font-weight-bold text-secondary m-0">{{$list_comment['hour_p_week'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->hour_p_week ?? ''}}
                </div>

            </div>

            <div class="col-6">

                <label for="unid_credit" class="font-weight-bold text-secondary m-0">{{$list_comment['unid_credit'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->unid_credit ?? ''}}
                </div>
                <label for="approved_credit_unir" class="font-weight-bold text-secondary m-0">{{$list_comment['approved_credit_unir'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->approved_credit_unir ?? ''}}
                </div>
                <label for="enable_lost_regulation" class="font-weight-bold text-secondary m-0">{{$list_comment['enable_lost_regulation'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{($asignatura->enable_lost_regulation=='true') ? 'SI':'NO'}}
                </div>
                <label for="enable_official_doc" class="font-weight-bold text-secondary m-0">{{$list_comment['enable_official_doc'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{($asignatura->enable_official_doc=='true') ? 'SI':'NO'}}
                </div>
                <label for="enable_repairable" class="font-weight-bold text-secondary m-0">{{$list_comment['enable_repairable'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{($asignatura->enable_repairable=='true') ? 'SI':'NO'}}
                </div>
                <label for="enable_grupo_estable" class="font-weight-bold text-secondary m-0">{{$list_comment['enable_grupo_estable'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{($asignatura->enable_grupo_estable=='true') ? 'SI':'NO'}}
                </div>
                <label for="observations" class="font-weight-bold text-secondary m-0">{{$list_comment['observations'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->observations ?? ''}}
                </div>
                <label for="prelacions" class="font-weight-bold text-secondary m-0">{{$list_comment['prelacions'] ?? ''}}</label>
                <div class="alert alert-secondary">
                    {{$asignatura->prelacions ?? ''}}
                </div>
            </div>

        </div>

    </div>

</div>

