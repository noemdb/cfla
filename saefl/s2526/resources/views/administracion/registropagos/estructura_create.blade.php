@extends('administracion.layouts.dashboard.app')

@section('title')
    Asistente Interactivo para Generar estructura de cobranza - {{ Auth::user()->rol ?? '' }}
@endsection

@section('main')
    <main role="main" class="col-md-10 col-lg-10">

        <div class="pt-2">

            @livewire('administracion.registro-pago.asistente.estructura')

        </div>
    </main>
@endsection

@section('livewireCustomeScripts')
    @parent
    <script>
        window.addEventListener('swal', function(e) {
            Swal.fire(e.detail);
        });

        window.addEventListener('swal:confirm', function(e) {

            Swal.fire({
                    title: e.detail.message,
                    text: e.detail.text,
                    icon: e.detail.type,
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.value) {
                        window.livewire.emit('remove', e.detail.id);
                    }
                });
        });

        window.addEventListener('swal:question', function(e) {
            Swal.fire({
                    title: e.detail.message,
                    text: e.detail.text,
                    icon: e.detail.type,
                    buttons: true,
                    dangerMode: true,
                    showCancelButton: true,
                })
                .then((result) => {
                    if (result.value) {
                        window.livewire.emit(e.detail.method, e.detail.id);
                    }
                });
        });
    </script>
@endsection


@section('stylesheet')
    @parent
    <style>
        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2rem;
        }

        .step {
            flex: 1;
            text-align: center;
            position: relative;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 15px;
            right: -50%;
            width: 100%;
            height: 2px;
            background-color: #dee2e6;
            z-index: 1;
        }

        .step.active:not(:last-child)::after,
        .step.completed:not(:last-child)::after {
            background-color: #28a745;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #dee2e6;
            color: #6c757d;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            position: relative;
            z-index: 2;
            margin-bottom: 0.5rem;
        }

        .step.active .step-circle {
            background-color: #007bff;
            color: white;
        }

        .step.completed .step-circle {
            background-color: #28a745;
            color: white;
        }

        .payment-summary {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 1rem;
        }

        .ticket-preview {
            background-color: white;
            border: 2px dashed #dee2e6;
            border-radius: 0.375rem;
            padding: 1.5rem;
            font-family: 'Courier New', monospace;
            font-size: 0.9rem;
        }

        .resource-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .resource-item:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .resource-item.selected {
            border-color: #28a745;
            background-color: #d4edda;
        }

        .quota-item {
            border: 1px solid #dee2e6;
            border-radius: 0.375rem;
            padding: 0.75rem;
            margin-bottom: 0.5rem;
            transition: all 0.2s;
        }

        .quota-item.overdue {
            border-left: 4px solid #dc3545;
        }

        .quota-item.not-due {
            border-left: 4px solid #28a745;
        }

        .quota-item:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        .quota-item.selected {
            border-color: #28a745;
            background-color: #d4edda;
        }
    </style>

    <style>
        .resource-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .resource-item:hover {
            background-color: #f9f9f9;
        }

        .resource-item.selected {
            background-color: #e9ecef;
            border-color: #bbb;
        }

        .quota-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .quota-item:hover {
            background-color: #f9f9f9;
        }

        .quota-item.selected {
            background-color: #e9ecef;
            border-color: #bbb;
        }

        .quota-item.overdue {
            border-left: 5px solid #dc3545;
        }

        .quota-item.not-due {
            border-left: 5px solid #28a745;
        }

        .step-indicator {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .step {
            text-align: center;
            flex-grow: 1;
            position: relative;
            padding-top: 25px;
        }

        .step:not(:last-child)::after {
            content: '';
            position: absolute;
            top: 14px;
            left: 50%;
            width: 100%;
            height: 2px;
            background-color: #ccc;
            transform: translateX(-50%);
            z-index: 0;
        }

        .step.completed::after {
            background-color: #5cb85c;
        }

        .step-circle {
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background-color: #fff;
            border: 2px solid #ccc;
            color: #777;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 14px;
            font-weight: bold;
            position: absolute;
            top: 0;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
        }

        .step.active .step-circle {
            border-color: #007bff;
            color: #007bff;
        }

        .step.completed .step-circle {
            border-color: #5cb85c;
            color: #fff;
            background-color: #5cb85c;
        }

        .step-label {
            margin-top: 5px;
            color: #555;
        }

        .step.active .step-label {
            color: #007bff;
        }

        .step.completed .step-label {
            color: #5cb85c;
        }

        .payment-summary {
            border: 1px solid #ddd;
            padding: 20px;
            border-radius: 5px;
            background-color: #f9f9f9;
        }

        .ticket-preview {
            border: 1px dashed #000;
            padding: 10px;
            font-family: monospace;
            font-size: 12px;
            line-height: 1.4;
        }

        .paid-quota {
            background-color: #f8fff8 !important;
            opacity: 0.7;
        }

        .paid-quota .form-check-input:disabled {
            cursor: not-allowed;
        }

        .paid-quota .form-check-label {
            color: #6c757d;
        }
    </style>

    <style>
        /* Estilos para el recibo de pago */
        .ticket {
            width: 100%;
            max-width: 300px;
            /* Ancho típico de un recibo de caja */
            margin: 0 auto;
            padding: 8px;
            /* Reduced padding */
            font-family: 'Courier New', monospace;
            font-size: 10px;
            line-height: 1.4;
            color: #333;
            border: 1px solid #eee;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            background-color: #fff;
        }

        .header {
            text-align: center;
            margin-bottom: 5px;
            /* Reduced margin */
        }

        .header .logo {
            max-width: 60px;
            height: auto;
            margin-bottom: 3px;
            /* Reduced margin */
        }

        .header .institution-name {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 10px;
            margin-bottom: 1px;
            /* Reduced margin */
        }

        .header .ministry {
            font-size: 9px;
            color: #555;
        }

        .separator {
            margin: 5px 0;
            /* Reduced margin */
            text-align: center;
            font-size: 8px;
            color: #666;
        }

        .ticket-info {
            text-align: center;
            margin-bottom: 5px;
            /* Reduced margin */
        }

        .ticket-info div {
            margin-bottom: 1px;
            /* Reduced margin */
        }

        .section {
            margin-bottom: 5px;
            /* Reduced margin */
        }

        .section-title {
            font-weight: bold;
            text-align: center;
            margin-bottom: 3px;
            /* Reduced margin */
            border-bottom: 1px dashed #ccc;
            padding-bottom: 2px;
            /* Reduced padding */
        }

        .customer-info div,
        .student-list div,
        .payment-details div {
            margin-bottom: 1px;
            /* Reduced margin */
        }

        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 3px;
            /* Reduced margin */
        }

        .items-table th,
        .items-table td {
            padding: 2px 0;
            /* Reduced padding */
            text-align: left;
        }

        .items-table .item-desc {
            width: 70%;
        }

        .items-table .item-amount {
            width: 30%;
            text-align: right;
        }

        .items-table tfoot .total-line {
            border-top: 1px dashed #000;
            padding-top: 3px;
            /* Reduced padding */
        }

        .payment-method {
            display: flex;
            justify-content: space-between;
            margin-bottom: 1px;
            /* Reduced margin */
        }

        .payment-method .text-right {
            text-align: right;
        }

        .signature-section {
            margin-top: 10px;
            /* Reduced margin */
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #000;
            width: 80%;
            margin: 8px auto 3px auto;
            /* Reduced margin */
        }

        .footer {
            margin-top: 10px;
            /* Reduced margin */
            text-align: center;
            font-size: 9px;
            color: #555;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 9px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        .highlight {
            background-color: #f0f0f0;
            padding: 5px;
            /* Reduced padding */
            border-radius: 3px;
        }

        /* New styles for receipt title and number */
        .receipt-title {
            font-size: 14px;
            /* Larger font size */
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .receipt-number {
            font-size: 12px;
            /* Slightly larger for the label */
            font-weight: bold;
            margin-bottom: 2px;
        }

        .correlative-value {
            font-size: 18px;
            /* Significantly larger for the number itself */
            font-weight: bolder;
            color: #ff0000;
            /* Ensure it stands out */
        }

        /* Print button styles */
        .print-button {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            text-align: center;
        }

        .print-button:hover {
            background-color: #0056b3;
        }

        /* Hide print button when printing */
        @media print {
            body * {
                visibility: hidden;
            }

            #printable-ticket-area,
            #printable-ticket-area * {
                visibility: visible;
            }

            #printable-ticket-area {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                margin: 0;
                padding: 0;
            }

            .ticket {
                width: 100% !important;
                max-width: 300px !important;
                margin: 0 auto !important;
                box-shadow: none !important;
                border: none !important;
                padding: 0 !important;
            }

            .print-button {
                display: none !important;
            }

            html,
            body {
                margin: 0;
                padding: 0;
                height: auto;
                overflow: visible;
            }
        }
    </style>
@endsection
