@php
$coll_mensseges = $coll_nivel->coll_mensseges;
$coll_mensseges_active = $coll_nivel->coll_mensseges->where('status','true');
@endphp

<div class="continer-fluid border-bottom px-2 py-3">
    <div class="row">
        <div class="col-1 text-right h-auto">
            <span class=" font-weight-bold">{{$loop->iteration}}</span>
        </div>
        <div class="col-12">
            <dl class="mb-1 ">
                <dt>{{$list_comment['name']}}</dt>
                <dd class="text-secondary">{{ $coll_nivel->name ?? '' }}</dd>
            </dl>
            {{-- <dl class="mb-1 ">
                <dt>
                    {{$list_comment['order']}}/{{$list_comment['weighing']}}
                    <span class="text-secondary font-weight-normal">{{ $coll_nivel->order ?? '' }}/{{ $coll_nivel->weighing ?? '' }}</span>
                </dt>
            </dl> --}}
            <dl class="mb-1 ">
                <dt>{{$list_comment['description']}}</dt>
                <dd class="text-secondary font-weight-normal">{{ $coll_nivel->description ?? '' }}</dd>
            </dl>
            <dl class="mb-1 ">
                <dt>
                    {{$list_comment['status']}}
                    <span class="text-secondary">{{ ($coll_nivel->status=='true') ? 'Activo':'Desactivo' }}</span>
                </dt>
            </dl>
        </div>

    </div>
    <hr>
    <div class="row">
        <div class="col-12">

            <div class="pl-2 border rounded shadow-sm">
                <div class=" font-weight-bold pb-1">Mensajes</div>

                <div class="">

                    @forelse ($coll_mensseges as $coll_messege)
                        <dl class=" px-1 mx-1 mb-1 rounded {{($coll_messege->status == 'false') ? 'alert-danger' : null}}">
                            <dt>{{$loop->iteration}}. {{$coll_messege->title}} <span class="small ">{{($coll_messege->status == 'false') ? 'Desactivo' : null}}</span></dt>
                            {{-- <dd class="text-secondary font-weight-normal" title="{{$coll_messege->sentence}}">{{ Str::limit($coll_messege->sentence,98)}}</dd> --}}
                            <dd class="text-secondary font-weight-normal" title="{{$coll_messege->sentence}}">{!!$coll_messege->sentence!!}</dd>
                        </dl>
                        <div class="btn-group py-1" role="group" aria-label="Basic example">
                            @php $disabled = ($coll_messege->status == 'false' || !$coll_political->status_send_mail) ? 'disabled' : null @endphp
                            <a title="Enviar Mensaje de Cobranza" class="btn btn-success btn-sm {{ $disabled ?? null}}" href="{{ route('email.collections.coll_messeges.bacth.send.mails',$coll_messege->id)}}" role="button">
                                {{-- <i class="fa fa-envelope" aria-hidden="true"></i> --}}
                                <i class="{{ $icon_menus['mail'] ?? ''}} fa-1x"></i>
                            </a>
                            <a title="Editar Mensaje de Cobranza" class="btn btn-warning btn-sm"  href="{{route('administracion.collections.coll_messeges.edit',$coll_messege->id)}}" role="button">
                                <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                            </a>


                        </div>
                        @include('administracion.collections.coll_messeges.modals.emailPreview')
                        <hr>
                    @empty
                        <div class=" text-muted font-weight-light"> No hay mensajes de cobro registrados </div class=" text-muted font-weight-bold">
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col text-center">
            <div class="btn-group py-1" role="group" aria-label="Basic example">
                <a name="" id="" class="btn btn-info {{ ($coll_mensseges_active->isEmpty() || !$coll_political->status_send_mail) ? ' disabled ' : null }}" href="{{ route('email.collections.coll_nivels.bacth.send.mails',$coll_nivel->id)}}" role="button">
                    <i class="fa fa-envelope" aria-hidden="true"></i>
                    Enviar mensajes
                </a>
                <a title="Editar Nivel de Cobranza" class="btn btn-warning"  href="{{route('administracion.collections.coll_nivels.edit',$coll_nivel->id)}}" role="button">
                    <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                </a>
            </div>
        </div>
    </div>
</div>



