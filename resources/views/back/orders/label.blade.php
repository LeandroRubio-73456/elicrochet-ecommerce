<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Etiqueta de Envío - #{{ $order->order_number }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0px;
            padding: 20px;
        }   
        .label-container {
            border: 2px solid #000;
            padding: 25px;
            max-width: 600px; /* Aprox media hoja A4 */
            margin: 0 auto;
        }
        .section strong{
            margin-bottom: 25px; 
            font-style: italic;
        }
        .remitente {
            display: flex;
            margin-left: auto;
            flex-direction: column;
            align-items: flex-end;
            text-align: right;
        }
        .section-title {
            font-weight: bold;
            font-size: 36px;
            margin-bottom: 10px;
            border: 2px solid #000;
            padding: 10px;
            display: inline-block;
        }
        .content {
            font-size: 28px;
            line-height: 1.5;
            margin-left: 10px;
        }
        .footer-warning {
            text-align: center;
            font-weight: bold;
            font-size: 64px;
            margin-top: 30px;
            padding: 10px;
            font-style: italic;
        }

        .city  {
            font-weight: bold;
            font-size: 38px;
        }
        @media print {
            .no-print {
                display: none;
            }
            body {
                padding: 0;
            }
            .label-container {
                border: none;
                margin: 0;
                width: 95%;
                max-width: 100%;
            }
        }
    </style>
</head>
<body>

    <div class="no-print" style="text-align: center; margin-bottom: 20px;">
        <button onclick="window.print()" style="padding: 10px 20px; font-size: 16px; cursor: pointer; background-color: #007bff; color: white; border: none; border-radius: 5px;">
            Imprimir Etiqueta
        </button>
    </div>

    <div class="label-container">
        <!-- DESTINATARIO -->
        <div class="section">
            <div class="section-title">DESTINATARIO:</div>
            <div class="content">
                <strong>{{ $order->customer_name }}</strong><br>
                <strong>CI: {{ $order->user->cedula ?? 'N/A' }}</strong><br>
                Dir: {{ $order->address->street ?? $order->shipping_address ?? 'Dirección No Disponible' }}<br>
                Ref: {{ $order->address->reference ?? $order->shipping_reference ?? '' }}<br>
                Cel: {{ $order->customer_phone }} <br>
                <strong class="city"> {{ $order->address->city ?? $order->shipping_city ?? '' }} - {{ $order->address->province ?? $order->shipping_province ?? '' }} </strong>
            </div>
        </div>

        <!-- REMITENTE -->
        <div class="section remitente">
            <div class="section-title">REMITENTE:</div>
            <div class="content">
                <strong>Estefania Elizabeth</strong><br>
                <strong>CI: 1754989877</strong><br>
                Cel:0967561212<br>
                Quito – Pichincha
            </div>
        </div>

        <!-- WARNING -->
        <div class="footer-warning">
            FRAGIL - FRAGIL
        </div>

    </div>

</body>
</html>
