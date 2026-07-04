@extends('layouts.activity')

@section('header')
    <div class="d-flex justify-content-between align-items-center bg-light rounded p-3 mb-3 shadow-sm">
        <h2 class="h4 mb-0 text-primary">
            <i class="fas fa-history me-2"></i>Registro de Actividades
        </h2>
    </div>
@endsection

@section('content')
    <div class="card">
        <div class="card-body">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs nav-fill" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" data-bs-toggle="tab" href="#table" role="tab">
                        <i class="fas fa-table me-1"></i>Tabla de Actividades
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" data-bs-toggle="tab" href="#dashboard" role="tab">
                        <i class="fas fa-chart-line me-1"></i>Dashboard
                    </a>
                </li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content mt-3">
                <div class="tab-pane fade show active" id="table" role="tabpanel">
                    <livewire:activity-logs.table />
                </div>
                <div class="tab-pane fade" id="dashboard" role="tabpanel">
                    <livewire:activity-logs.dashboard />
                </div>
            </div>
        </div>
    </div>
@endsection
