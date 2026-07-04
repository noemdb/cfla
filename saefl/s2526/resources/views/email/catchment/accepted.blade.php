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

        @if ($interview)
            <!-- Main Content -->
            <div style="margin-bottom: 2rem;">
                <h1 style="color: #2c5282; text-align: center; margin-bottom: 1rem;">¡Felicitaciones! Su solicitud ha sido aceptada</h1>
                <h3 style="color: #4a5568; text-align: center; margin-bottom: 1.5rem;">Proceso de Matriculación Escolar 2026 - 2027</h3>

                <p style="text-align: justify; margin-bottom: 1rem;">
                    Estimado(a) Sr(a). <strong>{{$interview->full_name}}</strong>, CI: {{$interview->identification_number}},
                </p>

                <div style="background-color: #f7fafc; padding: 1rem; border-radius: 4px; margin-bottom: 1.5rem;">

                    <div style="margin-bottom: 1rem;">
                        <p>Nos complace informarle que su solicitud de admisión al Colegio Fray Luis Amigó ha sido <strong>ACEPTADA</strong>. Después de una cuidadosa revisión de su expediente académico, estamos convencidos de que su representado y usted tienen el potencial para prosperar en nuestro programa académico, el cual se distingue por ofrecer una formación integral de alta calidad en un ambiente de aprendizaje que promueve valores fundamentales de nuestra espiritualidad amigoniana.</p>
                    </div>

                    <div style="background-color: #e9f2ff; padding: 15px; border-left: 4px solid #2c5aa0; margin-bottom: 25px;">
                        <p>Para confirmar formalmente el cupo de su representado en nuestro colegio, le solicitamos:</p>
                        <ol>
                            <li>Iniciar el proceso de admisión en la administración</li>
                            <li>Presentar la <strong>Constancia de Aceptación adjunta</strong></li>
                            {{-- <li>Cancelar <strong>120 USD</strong></li> --}}
                        </ol>
                        <p>Este paso es indispensable y debe realizarse dentro de los <strong>30 días hábiles</strong> siguientes a la recepción de esta comunicación.</p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <p>La presentación de esta constancia también le permitirá gestionar el retiro formal de su hijo(a) de la institución de procedencia.</p>
                    </div>

                    <div style="background-color: #fff8e6; padding: 15px; border-left: 4px solid #ffc107; margin-bottom: 25px;">
                        <p><strong>Posteriormente, durante el mes de julio, le contactaremos para formalizar la inscripción, momento en el cual debe presentar los documentos anexos requeridos.</strong></p>
                    </div>

                    <div style="margin-bottom: 20px;">
                        <p>Bienvenido a la comunidad del Colegio Fray Luis Amigó y a nuestra familia Amigoniana. No dude en comunicarse con nosotros ante cualquier inquietud.</p>
                    </div>

                </div>

                <!-- Student Information -->
                <div style="margin-bottom: 2rem;">
                    <h3 style="color: #2c5282; margin-bottom: 1rem;">Información del Estudiante</h3>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div>
                            <h4 style="color: #4a5568; margin-bottom: 0.5rem;">Datos del Representante</h4>
                            <ul style="list-style: none; padding: 0;">
                                <li><strong>Nombre:</strong> {{$interview->full_name ?? null}}</li>
                                <li><strong>CI:</strong> {{$interview->identification_number ?? null}}</li>
                                <li><strong>Correo electrónico:</strong> {{$interview->email ?? null}}</li>
                                <li><strong>Parentesco:</strong> {{$interview->relationship ?? null}}</li>
                            </ul>
                        </div>
                        <div>
                            <h4 style="color: #4a5568; margin-bottom: 0.5rem;">Datos del Estudiante</h4>
                            <ul style="list-style: none; padding: 0;">
                                <li><strong>Nombre:</strong> {{$interview->student_full_name}}</li>
                                <li><strong>Grado:</strong> {{$interview->grado->name ?? null}}</li>
                                <li><strong>Fecha de nacimiento:</strong> {{ f_date($interview->date_of_birth) }}</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Call to Action -->
                <div style="text-align: center; background-color: #ebf8ff; padding: 1.5rem; border-radius: 4px; margin-bottom: 2rem;">
                    <h3 style="color: #2c5282; margin-bottom: 1rem;">Obtenga su Carta de Aceptación digital</h3>
                    <p style="margin-bottom: 1rem;">
                        Descarguela haciendo click en el siguiente enlace:
                    </p>
                    @php $link = route('catchments.accepted',$interview->token); @endphp
                    <div style="margin: 1rem 0;">
                        <a href="{{ $link }}" style="display: inline-block; background-color: #2c5282; color: white; padding: 0.75rem 1.5rem; text-decoration: none; border-radius: 4px; font-weight: bold;">
                            Descargar Carta de Aceptación
                        </a>
                    </div>

                    <div style="margin: 1rem 0;">
                        <p style="margin-bottom: 0.5rem; font-size: 0.9rem;">O escanee el siguiente código QR:</p>
                        @php
                            $qrUrl = route('catchments.accepted', $interview->token);
                            $qrImageUrl = env('QRSERVER_URL',null) . urlencode($qrUrl) . '&chld=L|1';
                        @endphp
                        <img src="{{ $qrImageUrl }}"
                            alt="Código QR para descargar carta de aceptación"
                            style="max-width: 200px; height: auto; display: block; margin: 0 auto;">
                    </div>

                    <p style="margin-top: 1rem; font-size: 0.9rem;">
                        Esté atento a nuestro próximo correo con el cronograma de inscripción, que también publicaremos en nuestra red social oficial @cflasf.oficial | Original audio on Instagram.
                    </p>

                </div>
            </div>
        @endif

        <!-- Footer -->
        <div style="text-align: center; margin-top: 2rem;">
            <p style="margin-bottom: 1rem;">Agradecemos su interés en nuestra institución.</p>

            <div style="margin-bottom: 1rem;">
                <div style="font-weight: bold;">{{$autoridad->profile_professional}} {{$autoridad->fullname}}</div>
                <div>{{$autoridad->position}}</div>
            </div>

            <div>
                <div style="font-weight: bold;">{{$director->profile_professional}} {{$director->fullname}}</div>
                <div>{{$director->position}}</div>
            </div>
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
