@extends('layouts.activity')

@section('header')
    <div class="d-flex justify-content-between align-items-center">
        <h2 class="mb-0">
            <i class="fas fa-history me-2"></i>Detalle de Actividad
        </h2>
        <a href="{{ route('activity-logs.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left me-1"></i>Volver
        </a>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h5 class="mb-3">Información General</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th class="w-25">ID</th>
                            <td>{{ $activity->id }}</td>
                        </tr>
                        <tr>
                            <th>Fecha</th>
                            <td>{{ $activity->created_at->format('d/m/Y H:i:s') }}</td>
                        </tr>
                        <tr>
                            <th>Usuario</th>
                            <td>
                                @if($activity->causer)
                                    {{ $activity->causer->name }}
                                @else
                                    Sistema
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <th>Descripción</th>
                            <td>{{ $activity->description }}</td>
                        </tr>
                    </table>
                </div>
                <div class="col-md-6">
                    <h5 class="mb-3">Información Técnica</h5>
                    <table class="table table-bordered">
                        <tr>
                            <th class="w-25">Ruta</th>
                            <td>{{ $activity->properties->get('route') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>Método</th>
                            <td>{{ $activity->properties->get('method') ?? '-' }}</td>
                        </tr>
                        <tr>
                            <th>IP</th>
                            <td>{{ $activity->properties->get('ip_address') }}</td>
                        </tr>
                        <tr>
                            <th>User Agent</th>
                            <td>{{ $activity->properties->get('user_agent') ?? '-' }}</td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="mt-4">
                <h5 class="mb-3">Propiedades</h5>
                <div class="card">
                    <div class="card-body">
                        <pre class="mb-0">{{ json_encode($activity->properties, JSON_PRETTY_PRINT) }}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
