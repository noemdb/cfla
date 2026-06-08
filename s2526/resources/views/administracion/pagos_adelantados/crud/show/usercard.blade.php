<div class="card bd-callout bd-callout-{{{ $user->is_active=="enable" ? 'primary' : 'default' }}}" style="margin-top: 0rem !important;">

        <img class="card-img-top" style="width: 6rem; margin-left: auto; margin-right: auto;" src="{{ (isset($profile->url_img)) ? asset($profile->url_img) : asset('images/avatar/user_default.png') }}" alt="Card image cap">
    
        <div class="card-body">
            <h5 class="card-title text-center">
                <b>{{ $user->username }}</b>            
            </h5>    
            
            <div class="card-text bd-callout bd-callout-{{$user->class ?? 'deafult'}}">
                <small>
                    <table class="table table-sm mb-1">                
                        <tr>
                            <th scope="row"><i class="fas fa-user"></i></th>
                            <td class="text-muted">{{ $user->profile->full_name  ?? 'deafult'}}</td>
                        </tr>
                        <tr>
                            <th scope="row"><i class="far fa-envelope"></i></th>
                            <td class="text-muted">{{ $user->email  ?? 'deafult'}}</td>
                        </tr>
                        <tr>
                            <th scope="row"><i class="far fa-address-book"></i></th>
                            <td class="text-muted">
                                {{ $user->getUserRol()  ?? 'deafult'}}                          
                            </td>
                        </tr>
                    </table>
                </small>            
            </div>
            
        </div>
    
    </div>