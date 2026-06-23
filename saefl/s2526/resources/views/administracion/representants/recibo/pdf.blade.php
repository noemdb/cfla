@php
    function money($b, $d, $complete = false)
    {
        $text = '<span style="color:#242424;"> $ ' . f_float($d) . '</span>';
        $text = $complete ? str_pad(f_float($b), 7, '_', STR_PAD_LEFT) . ' Bs.  || ' . $text : $text;
        return $text;
    }

    function printLine($char = '=', $length = 44)
    {
        return str_repeat($char, $length);
    }

    function centerText($text, $width = 44)
    {
        $textLength = strlen(strip_tags($text));
        $padding = max(0, ($width - $textLength) / 2);
        return str_repeat(' ', floor($padding)) . $text;
    }

    function formatCurrency($amount, $symbol = 'Bs.')
    {
        return $symbol . ' ' . number_format($amount, 2, ',', '.');
    }
@endphp

<!DOCTYPE html>
<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Ticket Fiscal - {{ $registro_pago_combinado->correlative }}</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Courier+Prime:wght@400;700&display=swap');

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html,
        body {
            font-family: 'Courier Prime', 'Courier New', monospace;
            font-size: 11px;
            line-height: 1.2;
            color: #000;
            background: #fff;
            margin: 0;
            padding: 8px;
        }

        .ticket {
            width: 100%;
            max-width: 100%;
            background: white;
            border: 1px solid #ddd;
            padding: 6px;
            margin: 0;
        }

        .header {
            text-align: center;
            margin-bottom: 6px;
        }

        .logo {
            width: 30px;
            height: 30px;
            margin: 0 auto 3px;
        }

        .institution-name {
            font-weight: bold;
            font-size: 9px;
            text-transform: uppercase;
            margin-bottom: 2px;
        }

        .ministry {
            font-size: 7px;
            margin-bottom: 1px;
        }

        .separator {
            text-align: center;
            margin: 3px 0;
            font-family: monospace;
            font-size: 10px;
        }

        .ticket-info {
            text-align: center;
            font-size: 8px;
            margin-bottom: 6px;
        }

        .section {
            margin-bottom: 4px;
        }

        .section-title {
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 2px;
            border-bottom: 1px dashed #666;
            padding-bottom: 1px;
            font-size: 8px;
        }

        .customer-info {
            font-size: 8px;
            margin-bottom: 3px;
        }

        .student-list {
            font-size: 7px;
            margin-bottom: 3px;
        }

        .items-table {
            width: 100%;
            font-size: 8px;
            margin-bottom: 4px;
        }

        .items-table th {
            text-align: left;
            font-weight: bold;
            border-bottom: 1px solid #666;
            padding: 1px 0;
            font-size: 7px;
        }

        .items-table td {
            padding: 1px 0;
            vertical-align: top;
            font-size: 7px;
        }

        .item-desc {
            width: 60%;
        }

        .item-amount {
            width: 40%;
            text-align: right;
        }

        .total-line {
            border-top: 1px solid #666;
            border-bottom: 1px double #666;
            font-weight: bold;
            background-color: #f5f5f5;
        }

        .payment-details {
            font-size: 7px;
            margin-bottom: 4px;
        }

        .payment-method {
            margin-bottom: 1px;
            padding: 1px 0;
        }

        .footer {
            text-align: center;
            font-size: 7px;
            margin-top: 6px;
            border-top: 1px dashed #666;
            padding-top: 3px;
        }

        .signature-section {
            margin-top: 8px;
            text-align: center;
        }

        .signature-line {
            border-bottom: 1px solid #666;
            width: 120px;
            margin: 4px auto 1px;
            height: 15px;
        }

        .no-print {
            display: block;
        }

        @media print {
            body {
                margin: 0;
                padding: 0;
            }

            .ticket {
                border: 1px solid #000;
                box-shadow: none;
                margin: 0;
                padding: 4px;
            }

            .no-print {
                display: none !important;
            }

            /* Ajustes para impresión */
            .logo {
                width: 25px;
                height: 25px;
            }

            .signature-line {
                width: 100px;
                height: 12px;
            }
        }

        .highlight {
            background-color: #f0f0f0;
            padding: 2px;
        }

        .text-right {
            text-align: right;
        }

        .text-center {
            text-align: center;
        }

        .bold {
            font-weight: bold;
        }

        .small {
            font-size: 7px;
        }

        /* Estilos específicos para el layout duplicado */
        .duplicate-container {
            width: 100%;
            max-width: 210mm;
            /* Tamaño A4 */
            margin: 0 auto;
        }

        .duplicate-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 4px;
        }

        .duplicate-table td {
            width: 50%;
            vertical-align: top;
            /* border: 1px solid #ccc; */
            padding: 4px;
        }

        /* Separador entre tickets */
        .ticket-separator {
            border-right: 2px dashed #999;
            margin-right: 4px;
            padding-right: 4px;
        }
    </style>
</head>

<body>
    <div class="duplicate-container">
        <table class="duplicate-table" cellpadding="4" cellspacing="4"
            style="font-size:0.7rem;margin-bottom:0.2rem; padding-bottom:0.2rem;">
            <tr>
                <td width="50%" class="ticket-separator">
                    @include('administracion.representants.recibo.partials.main')
                </td>
                <td width="50%">
                    @include('administracion.representants.recibo.partials.main')
                </td>
            </tr>
        </table>
    </div>

    <!-- Print Button (No Print) -->
    <div class="no-print" style="text-align: center; margin-top: 20px;">
        <button onclick="window.print()"
            style="padding: 10px 20px; background: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer;">
            Imprimir Tickets Duplicados
        </button>
        <p style="margin-top: 10px; font-size: 12px; color: #666;">
            Este formato muestra el ticket duplicado para el representante y la institución
        </p>
    </div>
</body>

</html>
