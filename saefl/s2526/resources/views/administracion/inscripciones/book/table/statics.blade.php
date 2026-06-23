<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        @foreach ($arrMonths as $index => $name)
            @php $id = 'statics-month-'.$index; @endphp
            <a class="nav-item nav-link {{ $loop->first ? 'active ' : ' border-bottom-1 ' }}  text-capitalize"
                id="nav-{{ $index }}-tab" data-toggle="tab" href="#nav-{{ $index }}" role="tab"
                aria-controls="nav-{{ $index }}" aria-selected="true">{{ $name }}</a>
        @endforeach
    </div>
</nav>

<div class="tab-content border rounded border-top-0" id="nav-tabContent">
    @foreach ($arrMonths as $index => $name)

        @php 
          $id = 'statics-month-'.$index;
          $date  = date("Y").'-'.$index.'-'.'01';
          $start = Carbon\Carbon::createFromFormat('Y-m-d', $date);
          $date_end   = $start->copy()->endOfMonth();
        @endphp        

        <div class="tab-pane fade {{ $loop->first ? ' show active' : null }}" id="nav-{{ $index }}" role="tabpanel" aria-labelledby="nav-{{ $index }}-tab">

          <div class="p-2">   

            <div class="alert font-weight-bold pb-0 border-bottom">
                Para estudiantes inscritos del <span class="text-muted">{{$start->format('d-m-Y')}} hasta {{$date_end->format('d-m-Y')}} </span>.
            </div>

            <div class="container-fluid pt-2">

              <div class="row">

                @foreach ($pestudios as $pestudio)
                  <div class="col-6">                             

                    <table class="table table-sm table-striped small ">
                      <head>
                        <tr>
                            <th colspan="4" class="alert alert-secondary"  valign="center">{{ $pestudio->name }}</th>
                        </tr>

                        <tr>
                            <th class="pl-2" style="vertical-align: middle;">Nivel</th>
                            <th class="text-center table-primary" valign="center">
                              <div class="container-fluid">
                                <div class="row">
                                  <div class="col-12">
                                    Varones
                                  </div>                              
                                </div>
                                <div class="row">
                                  <div class="col-5 text-right">
                                    Edad <div class="small d-block">[Años]</div>
                                  </div>
                                  <div class="col-7 text-left">
                                    Cantidad <div class="small d-block">[Estudiantes]</div>
                                  </div> 
                              </div>
                            </th>
                            <th class="text-center table-danger" valign="center">
                              <div class="container-fluid">
                                <div class="row">
                                  <div class="col-12">
                                    Hembras
                                  </div>                              
                                </div>
                                <div class="row">
                                  <div class="col-5 text-right">
                                    Edad <small class="small d-block">[Años]</small>
                                  </div>
                                  <div class="col-7 text-left">
                                    Cantidad <div class="small d-block">[Estudiantes]</div>
                                  </div>                              
                                </div>
                              </div>
                            </th>
                            <th class="table-success" style="vertical-align: bottom;">
                              Total <div class="small d-block">[Estudiantes]</div>
                            </th>
                        </tr>
                      </head>
                      
                          @php 
                            $grados = $pestudio->grados;
                            $sum_varones = 0;
                            $sum_hembras = 0;
                            $sum_total = 0;
                          @endphp
                          
                          <tbody>
                              @foreach ($grados as $grado)   
                                @php 
                                  $dataM = $grado->getArrEstudiantGender('Masculino',$date_end);
                                  $dataF = $grado->getArrEstudiantGender('Femenino',$date_end);                                
                                  $total = 0;
                                @endphp  
                                <tr >
                                  <td class="w-25" style="vertical-align: middle;">
                                    <div style="display: flex; justify-content: start; align-items: center; height: 100%;">
                                      {{$grado->name}}
                                    </div>
                                  </td>
                                  <td class="text-center table-primary" style="vertical-align: middle;">
                                    <div class="container-fluid">
                                      @foreach ($dataM as $age=>$count) 

                                        @php 
                                          $total = $total + $count; 
                                          $sum_varones = $sum_varones + $count; 
                                          $edad = str_pad($age, 2,'0', STR_PAD_LEFT);
                                          $cantidad = str_pad($count, 2,'0', STR_PAD_LEFT);
                                        @endphp

                                        <div class="row">
                                          <div class="col-5 text-right">
                                            {{$edad}}
                                          </div>
                                          <div class="col-7 text-left">
                                            {{$cantidad}}
                                          </div>                              
                                        </div>

                                      @endforeach
                                    </div>

                                  </td>
                                  <td class="text-center table-danger" style="vertical-align: middle;">

                                    <div class="container-fluid">
                                      @foreach ($dataF as $age=>$count) 

                                        @php 
                                          $total = $total + $count; 
                                          $sum_hembras = $sum_hembras + $count; 
                                          $edad = str_pad($age, 2,'0', STR_PAD_LEFT);
                                          $cantidad = str_pad($count, 2,'0', STR_PAD_LEFT);
                                        @endphp

                                        <div class="row">
                                          <div class="col-5 text-right">
                                            {{$edad}}
                                          </div>
                                          <div class="col-7 text-left">
                                            {{$cantidad}}
                                          </div>                              
                                        </div>

                                      @endforeach
                                    </div>
                                    
                                  </td>
                                  <td class="table-success" style="vertical-align: middle;">
                                    {{$total}}
                                  </td>
                                </tr>                       

                              @endforeach
                              <tr class="table-info">
                                <th> Totales </th>
                                <th class="text-center"> {{$sum_varones}} </th>
                                <th class="text-center"> {{$sum_hembras}} </th>
                                <th class="text-center"> {{$sum_varones + $sum_hembras}} </th>
                              </tr>
                          </tbody>
                    </table>

                  </div>
                @endforeach 

              </div>
            </div>

          </div>

        </div>
    @endforeach
</div>
