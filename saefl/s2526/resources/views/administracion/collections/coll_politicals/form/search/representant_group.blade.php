{!! Form::open(['route'=>'administracion.collections.coll_politicals.representant.group','method'=>'GET','class'=>'pb-2', 'role'=>'search']) !!}

    <div class="container-fluid">
        <div class="row">
            <div class="col-sm-10">
                <div class="form-group">
                    <label for="canon" class="font-weight-bold text-secondary m-0">{{$list_comment['canon'] ?? ''}}</label>
                    {!! Form::select('canon',$list_coll_politicals_canon,$canon,['class'=>'form-control','required','placeholder'=>'Seleccione']);!!}
                </div>
            </div>
            <div class="col-sm-2">
                <div class="form-group">
                    <br>
                    <button class="btn btn-primary btn-block" type="submit">
                        <i class="fa fa-search" aria-hidden="true"></i>
                        Buscar
                    </button>
                </div>
            </div>
        </div>
    </div>

{!! Form::close() !!}
