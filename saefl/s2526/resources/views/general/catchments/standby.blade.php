<html>
   <head>
      <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
      <meta http-equiv="X-UA-Compatible" content="IE=8">
      <title>CARTA DIGITAL DE ACEPTACIÓN, MATRICULA ESCOLAR 2026 - 2027</title>
      <link href="{{ asset('css/table_pdf.css') ??  null}}" rel="stylesheet">
      <style type="text/css" media="print">
         div.page {
         page-break-after: always;
         page-break-inside: avoid;
         }
         .title {
         font-weight: bold;
         font-size: 14px;
         }
         footer {
         position: fixed;
         top: -60px;
         left: 0px;
         right: 0px;
         height: 50px;
         }
         body {
         text-transform: uppercase;
         }
      </style>
      <style>
         .field-label {
         /* width: 20rem; */
         }
         .field-label {
         /* max-width: 5rem; */
         margin-top: 1rem;
         padding-top: 1rem;
         white-space: wrap !important;
         font-weight: bold;
         }
         .field-value {
         white-space: nowrap !important;
         }
         .box {
         display: flex;
         /* align-items: center; */
         justify-content: center;
         }
         /* .box div {
         width: 100px;
         height: 100px;
         } */
      </style>
   </head>
   <body style="display: flex; justify-content: center; border: 1px #ccc solid; align-content: center;max-width: 18cm">
      <div style="max-width: 18cm">
         @php $pescolar = $institucion->pescolar; @endphp
         @php $catchment_interview = $catchment; @endphp
         <table width="100%" cellpadding="0" cellspacing="0"
            style=" font-size:0.8rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
            <thead>
               <tr>
                  <th scope="row" width="70px">
                     <img width="70px" height="70px" class="card-img-top" src="{{asset('images/avatar/uecfla.jpg')}}">
                  </th>
                  <th align="center" style="text-align: center;">
                     <div class="title"><b>{{ $institucion->name }}</b></div>
                     <div class="title"><b>DIRECCIÓN ACADÉMICA</b></div>
                  </th>
                  <th scope="row" width="70px">
                     <img width="100px" height="70px" class="card-img-top" src="{{asset('images/avatar/amigoniano.png')}}">
                  </th>
               </tr>
            </thead>
         </table>
         <hr>
         <table cellpadding="2" cellspacing="2" style="border-collapse: collapse; width: 100%;" border="0">
            <tbody>
               <tr>
                  <td style="width: 100%;">
                     <p style="text-align: right;">En San Felipe - Edo. Yaracuy a {{$toDate ?? null}}</p>
                     @if ($catchment)
                     <h1>Su representado ha sido incluido en la LISTA DE ESPERA.</h1>
                     <h3>Proceso de Matriculación Escolar 2026 - 2027</h3>
                     <hr>
                     <p style="text-align: justify;">
                        Estimado(a) Sr(a).&nbsp;<em><strong>{{$catchment_interview->full_name}}</strong>, CI;
                        {{$catchment_interview->identification_number}},</em>
                     </p>
                     <hr>
                     <p style="font-size: 0.8rem;">
                     <div class="container">
                        <p>Nos complace saludarle e informarle que la solicitud de admisión de {{$catchment_interview->student_full_name}} ha sido evaluada con detenimiento, considerando los procesos realizados tanto por usted como por su representado.</p>
                        <p>Nos complace comunicarles que ha cumplido satisfactoriamente con nuestras expectativas y demuestra un gran potencial para prosperar en nuestro riguroso y enriquecedor programa académico.</p>
                        <p>Sin embargo, como se le informó al inicio del proceso de admisión, una de las limitaciones para la asignación de cupos es la disponibilidad de los mismos. Por esta razón, ha sido incluido(a) en la **LISTA DE ESPERA**, en espera de que alguna vacante pueda ser liberada.</p>
                        <p>Le invitamos a mantenerse atento(a) a su correo electrónico o a comunicarse directamente con la Coordinación de Registro y Control de Estudios al siguiente número: 0424-5027880, ya sea por llamada o vía WhatsApp. Con gusto atenderemos cualquier duda o inquietud que pueda tener.</p>
                     </div>
                     </p>
                     <div>
                        <p>Sus datos registrados:</p>
                        <ul>
                           <li style="background-color: darkgray">Información del Representante</li>
                           <li><b>Nombre:</b> {{$catchment_interview->full_name ?? null}}</li>
                           <li><b>CI:</b> {{$catchment->identification_number ?? null}}</li>
                           <li><b>Correo electrónico:</b> {{$catchment_interview->email ?? null}}</li>
                           <li><b>Parentesco con el estudiante:</b> {{$catchment_interview->relationship ?? null}}</li>
                        </ul>
                        <ul>
                           <li style="background-color: darkgray">Información General del estudiante</li>
                           <li><b>Nombre:</b> {{$catchment_interview->student_full_name}}</li>
                           <li><b>Grado:</b> {{$catchment_interview->grado->name ?? null}}</li>
                           <li><b>Fecha de nacimiento:</b> {{ f_date($catchment_interview->date_of_birth) }}</li>
                        </ul>
                     </div>
                     @endif
                     <hr>
                     <strong>Agradecemos su interés en nuestra institución.</strong>
                     <hr>
                     <p style="text-align: center;">Atte.</p>
                     <div style="text-align: center;">
                        <div>{{$autoridad->profile_professional}} {{$autoridad->fullname}}</div>
                        <div>{{$autoridad->position}}</div>
                     </div>
                     <hr>
                     <p>
                     <div>{{$director->profile_professional}} {{$director->fullname}}</div>
                     <div>{{$director->position}}</div>
                     </p>
                  </td>
               </tr>
            </tbody>
         </table>
         <hr>
         <footer class="text-muted" style="font-size:0.8rem">
            <span>
            AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA. SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
            Teléfonos: 0424-5891682 / 0414-5442298. Correo electrónico: frayluisamigoyara@hotmail.com
            </span>
         </footer>
      </div>
   </body>
</html>
