@extends('bienestars.layouts.dashboard.app')

@section('main')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">Detalles del Mensaje</h4>
                    <a href="{{ route('bienestar.resend.index') }}" class="btn btn-secondary">
                        Volver al listado
                    </a>
                </div>

                <div class="card-body">
                    @if(session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>ID del Mensaje:</strong></label>
                                <p>{{ $email['id'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Estado:</strong></label>
                                <p>
                                    <span class="badge badge-{{ $email['status'] === 'delivered' ? 'success' : 'warning' }}">
                                        {{ $email['status'] }}
                                    </span>
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Asunto:</strong></label>
                                <p>{{ $email['subject'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Fecha de Envío:</strong></label>
                                <p>{{ \Carbon\Carbon::parse($email['created_at'])->format('d/m/Y H:i:s') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Destinatario:</strong></label>
                                <p>{{ $email['to'] }}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label><strong>Remitente:</strong></label>
                                <p>{{ $email['from'] }}</p>
                            </div>
                        </div>
                    </div>

                    @if(isset($email['cc']) && !empty($email['cc']))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>CC:</strong></label>
                                    <p>{{ is_array($email['cc']) ? implode(', ', $email['cc']) : $email['cc'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    @if(isset($email['bcc']) && !empty($email['bcc']))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>BCC:</strong></label>
                                    <p>{{ is_array($email['bcc']) ? implode(', ', $email['bcc']) : $email['bcc'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label><strong>Contenido:</strong></label>
                                <div class="border p-3 bg-light">
                                    {!! $email['html'] ?? $email['text'] ?? 'No hay contenido disponible' !!}
                                </div>
                            </div>
                        </div>
                    </div>

                    @if(isset($email['last_event']))
                        <div class="row">
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label><strong>Último Evento:</strong></label>
                                    <p>{{ $email['last_event'] }}</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
