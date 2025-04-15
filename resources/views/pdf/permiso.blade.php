<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Solicitud de Permiso</title>
    <style>
        @page {
            margin: 1.5cm 2cm;
            padding: 0;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 0;
            color: #000;
            background-color: #fff;
        }
        .container {
            width: 100%;
            max-width: 800px;
            margin: 0 auto;
        }
        .header {
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            position: relative;
            height: 90px;
        }
        .header img {
            max-height: 80px;
            width: auto;
            display: block;
            margin: 0 auto;
        }
        .title-container {
            margin-bottom: 20px;
        }
        .title {
            text-align: center;
            font-size: 16px;
            font-weight: bold;
            margin: 0 0 10px 0;
            border: 1px solid #000;
            padding: 8px;
            background-color: #f5f5f5;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .subtitle {
            text-align: center;
            font-size: 14px;
            margin: 0;
            border: 1px solid #000;
            padding: 6px;
            background-color: #f9f9f9;
        }
        .main-content {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 25px;
            page-break-inside: avoid;
        }
        table, th, td {
            border: 1px solid #000;
        }
        th {
            background-color: #e6e6e6;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            font-size: 11px;
        }
        td {
            padding: 8px;
            text-align: left;
            font-size: 12px;
        }
        td.centered {
            text-align: center;
        }
        .section-title {
            font-weight: bold;
            text-align: center;
            margin: 25px 0 15px 0;
            padding: 5px 0;
            border-bottom: 2px solid #000;
            font-size: 14px;
        }
        .signature-section {
            margin-top: 80px;
            width: 100%;
            display: block;
            position: relative;
            page-break-inside: avoid;
            clear: both;
        }
        .signature-row {
            width: 100%;
            display: block;
            height: 70px;
        }
        .signature-col {
            width: 31%;
            float: left;
            margin: 0 1%;
            text-align: center;
        }
        .signature-line {
            width: 90%;
            margin: 0 auto 5px auto;
            border-top: 1px solid #000;
        }
        .signature-f {
            margin: 3px 0;
        }
        .signature-title {
            font-size: 10px;
            margin-top: 3px;
        }
        .text-bold {
            font-weight: bold;
        }
        .text-center {
            text-align: center;
        }
        .clearfix:after {
            content: "";
            display: table;
            clear: both;
        }
        .page-break {
            page-break-before: always;
        }
        .bg-gray {
            background-color: #f5f5f5;
        }
        .align-top {
            vertical-align: top;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ public_path('images/BCR.png') }}" alt="Banco Central de Reserva de El Salvador">
        </div>
        
        <div class="title-container">
            <div class="title">Solicitud de Permiso y Control de Reposición</div>
            <div class="subtitle">Censo Agropecuario y Pesca 2025</div>
        </div>
        
        <div class="main-content">
            <table>
                <tr>
                    <td width="15%" class="text-bold bg-gray">DUI:</td>
                    <td width="35%">{{ $dui }}</td>
                    <td width="15%" class="text-bold bg-gray">Fecha:</td>
                    <td width="35%">{{ $fecha }}</td>
                </tr>
                <tr>
                    <td class="text-bold bg-gray">Nombre</td>
                    <td colspan="3">{{ strtoupper($nombre) }}</td>
                </tr>
                <tr>
                    <td class="text-bold bg-gray">Fecha del Permiso</td>
                    <td>{{ $fechaPermiso }}</td>
                    <td class="text-bold bg-gray">Tiempo Solicitado</td>
                    <td>{{ $tiempoSolicitado }}</td>
                </tr>
                <tr>
                    <td class="text-bold bg-gray">Desde:</td>
                    <td colspan="3">{{ $horaInicio }} a {{ $horaFin }}</td>
                </tr>
                <tr>
                    <td class="text-bold bg-gray align-top" rowspan="2">Motivo</td>
                    <td class="align-top" rowspan="2">{{ $motivo }}</td>
                    <td class="text-bold bg-gray text-center">Con descuento</td>
                    <td class="text-bold bg-gray text-center">Sin descuento</td>
                </tr>
                <tr>
                    <td class="text-center" style="font-size: 14px; font-weight: bold;">{{ $conDescuento ? 'X' : '' }}</td>
                    <td class="text-center" style="font-size: 14px; font-weight: bold;">{{ !$conDescuento ? 'X' : '' }}</td>
                </tr>
                <tr>
                    <td class="text-bold bg-gray">Sede:</td>
                    <td>{{ $sede }}</td>
                    <td class="text-bold bg-gray">Cargo:</td>
                    <td>{{ $cargo }}</td>
                </tr>
            </table>
            
            <div class="section-title">Forma de Reposición</div>
            
            <table>
                <thead>
                    <tr>
                        <th colspan="2" class="text-center">Desde</th>
                        <th colspan="2" class="text-center">Hasta</th>
                        <th rowspan="2" class="text-center">Total de Minutos</th>
                    </tr>
                    <tr>
                        <th class="text-center">Día</th>
                        <th class="text-center">Hora</th>
                        <th class="text-center">Día</th>
                        <th class="text-center">Hora</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($diasReposicion as $dia)
                    <tr>
                        <td class="text-center">{{ isset($dia['fecha']) ? date('d/m/Y', strtotime($dia['fecha'])) : '' }}</td>
                        <td class="text-center">{{ $dia['horaInicio'] ?? '' }}</td>
                        <td class="text-center">{{ isset($dia['fecha']) ? date('d/m/Y', strtotime($dia['fecha'])) : '' }}</td>
                        <td class="text-center">{{ $dia['horaFin'] ?? '' }}</td>
                        <td class="text-center">
                            @php
                                $minutos = 0;
                                if(isset($dia['horaInicio']) && isset($dia['horaFin'])) {
                                    $inicio = strtotime($dia['horaInicio']);
                                    $fin = strtotime($dia['horaFin']);
                                    if($inicio && $fin) {
                                        $diff = $fin - $inicio;
                                        if($diff < 0) $diff += 86400;
                                        $minutos = $diff / 60;
                                    }
                                }
                                echo round($minutos);
                            @endphp
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        <div class="signature-section">
            <div class="signature-row clearfix">
                <div class="signature-col">
                    <div class="signature-line"></div>
                    <div class="signature-f">F.</div>
                    <div class="signature-title">Solicitante</div>
                </div>
                <div class="signature-col">
                    <div class="signature-line"></div>
                    <div class="signature-f">F.</div>
                    <div class="signature-title">Revisado Especialista/Especialista Líder</div>
                </div>
                <div class="signature-col">
                    <div class="signature-line"></div>
                    <div class="signature-f">F.</div>
                    <div class="signature-title">Responsable Delegado BCR</div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>