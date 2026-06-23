<div style="max-width: 18cm; font-family: Arial, sans-serif; line-height: 1.6;">

    @php $pescolar = $institucion->pescolar; @endphp

    <!-- Header with Institution Information -->
    <table width="100%" cellpadding="0" cellspacing="0" style="margin-bottom: 1rem;">
        <tr>
            <td width="70px" style="vertical-align: middle;">
                <img width="70px" height="70px" src="{{asset('images/avatar/uecfla.jpg')}}" alt="Logo UEC">
            </td>
            <td style="text-align: center; vertical-align: middle;">
                <div style="font-size: 1.2rem; font-weight: bold;">{{ $institucion->name }}</div>
                <div style="font-size: 1rem; font-weight: bold;">DIRECCIÓN ACADÉMICA</div>
            </td>
            <td width="70px" style="vertical-align: middle;">
                <img width="100px" height="70px" src="{{asset('images/avatar/amigoniano.png')}}" alt="Logo Amigoniano">
            </td>
        </tr>
    </table>

    <hr style="border: 1px solid #ddd; margin: 1rem 0;">

    <div style="padding: 0 1rem;">
        <!-- Date and Greeting -->
        <p style="text-align: right; color: #666;">San Felipe, Edo. Yaracuy, {{$toDate ?? null}}</p>

        <!-- Main Content -->
        <div style="margin-bottom: 2rem;">
            <h1 style="color: #2c5282; text-align: center; margin-bottom: 1rem;">Confirmación de Continuidad Escolar</h1>
            <h3 style="color: #4a5568; text-align: center; margin-bottom: 1.5rem;">Proceso de Prosecución 2025 - 2026</h3>

            <p style="text-align: justify; margin-bottom: 1rem;">
                Estimado(a) Sr(a). <strong>{{$representant->full_name}}</strong>,
            </p>

            <div style="background-color: #f7fafc; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">

                <div style="margin-bottom: 1rem;">
                    <p>Le informamos que se ha registrado exitosamente la <strong>Confirmación de Continuidad Escolar</strong> (Prosecución) para su representado en el Colegio Fray Luis Amigó para el próximo periodo escolar.</p>
                </div>

                <div style="background-color: #e9f2ff; padding: 15px; border-left: 4px solid #2c5aa0; margin-bottom: 25px;">
                    <p>Detalles del Registro:</p>
                    <ul>
                        <li><strong>Estudiante:</strong> {{$estudiant->fullname}}</li>
                        <li><strong>Fecha de Confirmación:</strong> {{ \Carbon\Carbon::parse($estudiant->date_prosecution)->format('d/m/Y h:i A') }}</li>
                    </ul>
                </div>

                <div style="margin-bottom: 20px;">
                    <p>Agradecemos su confianza depositada en nuestra institución para continuar con la formación integral de su representado bajo nuestros valores amigonianos.</p>
                </div>

                <div style="background-color: #fff8e6; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 25px;">
                    <p><strong>Recuerde estar atento a las próximas comunicaciones relacionadas con el proceso de formalización de inscripción.</strong></p>
                </div>

            </div>

            <!-- Student Information -->
            <div style="margin-bottom: 2rem;">
                <h3 style="color: #2c5282; margin-bottom: 1rem;">Información Adicional</h3>
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                    <div>
                        <h4 style="color: #4a5568; margin-bottom: 0.5rem;">Representante</h4>
                        <ul style="list-style: none; padding: 0;">
                            <li><strong>Nombre:</strong> {{$representant->full_name ?? null}}</li>
                            <li><strong>Identificación:</strong> {{$representant->identification_number ?? null}}</li>
                        </ul>
                    </div>
                    <div>
                        <h4 style="color: #4a5568; margin-bottom: 0.5rem;">Estudiante</h4>
                        <ul style="list-style: none; padding: 0;">
                            <li><strong>Nombre:</strong> {{$estudiant->fullname}}</li>
                            <li><strong>Grado Actual:</strong> {{$estudiant->grado->name ?? 'N/A'}}</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 2rem;">
            <p style="margin-bottom: 1rem;">"Un hogar para crecer, una familia para amar"</p>

            <div style="margin-bottom: 1rem;">
                <div style="font-weight: bold;">{{$autoridad->profile_professional ?? ''}} {{$autoridad->fullname ?? ''}}</div>
                <div>{{$autoridad->position ?? ''}}</div>
            </div>

            @if(isset($director))
                <div>
                    <div style="font-weight: bold;">{{$director->profile_professional ?? ''}} {{$director->fullname ?? ''}}</div>
                    <div>{{$director->position ?? ''}}</div>
                </div>
            @endif
        </div>

        <hr style="border: 1px solid #ddd; margin: 1.5rem 0;">

        <footer style="font-size: 0.8rem; color: #666; text-align: center;">
            <p>
                AV. LA PAZ CON AV. CEDEÑO FRENTE A LA PLAZA JUAN JOSE DE MAYA<br>
                SAN FELIPE ESTADO YARACUY. VENEZUELA<br>
                Teléfonos: 0424-5891682 / 0414-5442298<br>
                Correo electrónico: frayluisamigoyara@hotmail.com
            </p>
        </footer>
    </div>
</div>
