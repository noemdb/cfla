{{-- <div class="container"> --}}
    <div class="row">
        <div class=" col-sm-12">
            <div class=" p-2 mb-2">

                <div class=" p-2 mb-2 mx-2 border rounded">

                    {{-- @include('welcome.payment.form.fields.prepago') --}}

                    <fieldset class="form_fieldset">
                        @php $i = 1 @endphp
                        <h4 class=" text-right alert alert-primary"><span class=" badge badge-primary"> Transferencia {{$i}}</span></h4>
                        <div class="p-2">
                            @include('welcome.payment.form.fields.prepago')
                        </div>
                        <input type="button" name="previous" class="previous-form btn btn-light w-50 p-0 btn-lg" value="Anterior" disabled/>
                        <input type="button" class="next-form btn btn-info w-50 p-0 float-right btn-lg" value="Siguiente" />
                    </fieldset>
                    <fieldset class="form_fieldset">
                        @php $i = 2 @endphp
                        <h4 class=" text-right alert alert-success"><span class=" badge badge-success"> Transferencia {{$i}}</span></h4>
                        <div class="p-2">
                            @include('welcome.payment.form.fields.prepago')
                        </div>
                        <input type="button" name="previous" class="previous-form btn btn-light  btn-lg  w-50 p-0" value="Anterior"/>
                        <input type="button" class="next-form btn btn-info  btn-lg w-50 p-0 float-right" value="Siguiente" />
                    </fieldset>
                    <fieldset class="form_fieldset">
                        @php $i = 3 @endphp
                        <h4 class=" text-right alert alert-info"><span class=" badge badge-info"> Transferencia {{$i}}</span></h4>
                        <div class="p-2">
                            @include('welcome.payment.form.fields.prepago')
                        </div>
                        <input type="button" name="previous" class="previous-form btn btn-light  btn-lg  w-50 p-0" value="Anterior"/>
                        <input type="button" class="next-form btn btn-info  btn-lg w-50 p-0 float-right" value="Siguiente" />
                    </fieldset>
                    <fieldset class="form_fieldset">
                        @php $i = 4 @endphp
                        <h4 class=" text-right alert alert-secondary"><span class=" badge badge-secondary"> Transferencia {{$i}}</span></h4>
                        <div class="p-2">
                            @include('welcome.payment.form.fields.prepago')
                        </div>
                        <input type="button" name="previous" class="previous-form btn btn-light  btn-lg w-50 p-0" value="Anterior" />
                        <input type="button" class="next-form btn btn-info  btn-lg w-50 p-0 float-right" value="Siguiente" disabled/>
                    </fieldset>

                    <div class=" p-4">
                        <div class="progress">
                            <div class="progress-bar progress-bar-striped" role="progressbar" aria-valuemin="0" aria-valuemax="100"></div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
{{-- </div> --}}



{{-- <form>
    <select id="dropdown" name="dropdown">
        <option value="0">Choose</option>
        <option value="area1">DIV Área 1</option>
        <option value="area2">DIV Área 2</option>
        <option value="area3">DIV Área 3</option>
    </select>
</form>
<div id="divarea1" class="box">DIV Área 1</div>
<div id="divarea2" class="box">DIV Área 2</div>
<div id="divarea3" class="box">DIV Área 3</div> --}}
