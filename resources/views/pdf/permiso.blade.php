<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Solicitud de Permiso</title>
    <style>

        @font-face {    
            font-family: 'Helvetica';
            font-style: normal;
            font-weight: normal;
            src: local('Helvetica'), local('Helvetica-Regular');
        }

        @page {
            margin: 25px;
            padding: 0;
        }
        body {
            font-family: 'Helvetica', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            margin: 0;
            padding: 0;
            color: #333;
            background-color: #fff;
        }
        .page-container {
            position: relative;
            width: 100%;
            padding: 0;
        }
        /* Header con logo y título */
        .header {
            display: flex;
            align-items: center; /* Alinear verticalmente */
            margin-bottom: 20px;
        }
        .logo-container {
            width: 100px;
            margin-right: 20px;
        }
        .logo {
            width: 100%;
            height: auto;
        }
        .title-container {
            flex: 1;
            text-align: center;
        }
        .title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin: 0 0 5px 0;
        }
        .subtitle {
            font-size: 14px;
            color: #376e37;
            font-weight: bold;
            margin: 0;
        }
        /* Secciones con estilo de tarjeta */
        .info-card {
            border: 1px solid #ddd;
            border-radius: 6px;
            overflow: hidden;
            margin-bottom: 15px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
        }
        .card-header {
            background-color: #376e37;
            color: white;
            padding: 5px 10px;
            font-size: 13px;
            font-weight: bold;
        }
        .card-body {
            padding: 8px;
        }
        /* Tabla de información */
        .info-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0;
            margin-bottom: 0;
        }
        .info-table tr td {
            padding: 5px 8px;
            vertical-align: top;
        }
        .info-table tr:nth-child(even) {
            background-color: #f8f8f8;
        }
        .label {
            font-weight: bold;
            color: #2c3e50;
            width: 22%;
        }
        .value {
            width: 28%;
        }
        /* Tabla para la forma de reposición */
        .reposition-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0;
        }
        .reposition-table th, .reposition-table td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: center;
            font-size: 11px;
        }
        .reposition-table th {
            background-color: #376e37;
            color: white;
            font-size: 10px;
            font-weight: normal;
        }
        .reposition-table tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        /* Checkbox personalizado */
        .checkbox-container {
            display: flex;
            align-items: center;
            justify-content: flex-start;
        }
        .checkbox {
            display: inline-block;
            width: 14px;
            height: 14px;
            border: 1px solid #333;
            text-align: center;
            line-height: 14px;
            font-weight: bold;
            margin-right: 6px;
        }
        /* Sección de firmas */
        .signature-section {
            margin-top: 60px; /* Aumentado para dar más espacio */
            width: 100%;
            display: table;
        }
        .signature-row {
            display: table-row;
        }
        .signature-col {
            display: table-cell;
            width: 33.33%;
            text-align: center;
            padding: 0 8px;
        }
        .signature-line {
            border-top: 1px solid #333;
            margin: 20px auto 5px auto;
            width: 90%; /* Ampliado para dar más espacio */
        }
        .signature-name {
            font-weight: bold;
            margin-bottom: 2px;
            font-size: 11px;
        }
        .signature-title {
            font-size: 10px;
            color: #555;
        }
        /* Elementos decorativos y optimización de espacio */
        .motivo-cell {
            min-height: 40px;
        }
    </style>
</head>
<body>
    <div class="page-container">
        <div class="content-container">
            <!-- Encabezado con logo a la izquierda y título a la derecha -->
            <div class="header">
                <div class="logo-container">
                    <img src="{{ public_path('images/BCR.png') }}" alt="Banco Central de Reserva" class="logo">
                </div>
                <div class="title-container">
                    <h1 class="title">SOLICITUD DE PERMISO Y CONTROL DE REPOSICIÓN</h1>
                    <h2 class="subtitle">Censo Agropecuario y Pesca 2025</h2>
                </div>
            </div>
            
            <!-- Información personal -->
            <div class="info-card">
                <div class="card-header">INFORMACIÓN PERSONAL</div>
                <div class="card-body">
                    <table class="info-table">
                        <tr>
                            <td class="label">DUI:</td>
                            <td class="value">{{ $dui }}</td>
                            <td class="label">Fecha de solicitud:</td>
                            <td class="value">{{ $fecha }}</td>
                        </tr>
                        <tr>
                            <td class="label">Nombre completo:</td>
                            <td class="value" colspan="3">{{ strtoupper($nombre) }}</td>
                        </tr>
                        <tr>
                            <td class="label">Sede:</td>
                            <td class="value">{{ $sede }}</td>
                            <td class="label">Cargo:</td>
                            <td class="value">{{ $cargo }}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Información del permiso -->
            <div class="info-card">
                <div class="card-header">DETALLES DEL PERMISO</div>
                <div class="card-body">
                    <table class="info-table">
                        <tr>
                            <td class="label">Fecha del permiso:</td>
                            <td class="value">{{ $fechaPermiso }}</td>
                            <td class="label">Tiempo solicitado:</td>
                            <td class="value">{{ $tiempoSolicitado }}</td>
                        </tr>
                        <tr>
                            <td class="label">Horario:</td>
                            <td class="value" colspan="3">{{ $horaInicio }} a {{ $horaFin }}</td>
                        </tr>
                        <tr>
                            <td class="label">Motivo:</td>
                            <td class="value motivo-cell" colspan="3">{{ $motivo }}</td>
                        </tr>
                        <tr>
                            <td class="label">Tipo de permiso:</td>
                            <td class="value">
                                <div class="checkbox-container">
                                    <div class="checkbox">{{ $conDescuento ? 'X' : '' }}</div> Con descuento
                                </div>
                            </td>
                            <td class="value" colspan="2">
                                <div class="checkbox-container">
                                    <div class="checkbox">{{ !$conDescuento ? 'X' : '' }}</div> Sin descuento
                                </div>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <!-- Forma de reposición -->
            <div class="info-card">
                <div class="card-header">FORMA DE REPOSICIÓN</div>
                <div class="card-body">
                    <table class="reposition-table">
                        <thead>
                            <tr>
                                <th colspan="2">DESDE</th>
                                <th colspan="2">HASTA</th>
                                <th rowspan="2">TOTAL DE MINUTOS</th>
                            </tr>
                            <tr>
                                <th>DÍA</th>
                                <th>HORA</th>
                                <th>DÍA</th>
                                <th>HORA</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if(is_array($diasReposicion) && count($diasReposicion) > 0)
                                @foreach($diasReposicion as $dia)
                                    @php
                                        if(!isset($dia['fecha']) || !isset($dia['horaInicio']) || !isset($dia['horaFin'])) {
                                            continue;
                                        }
                                        
                                        $fechaFormateada = '';
                                        if(!empty($dia['fecha'])) {
                                            try {
                                                $fechaFormateada = date('d/m/Y', strtotime($dia['fecha']));
                                            } catch(\Exception $e) {
                                                $fechaFormateada = $dia['fecha'];
                                            }
                                        }
                                        
                                        // Calcular minutos
                                        $minutos = 0;
                                        if(!empty($dia['horaInicio']) && !empty($dia['horaFin'])) {
                                            try {
                                                $inicio = strtotime("1970-01-01 " . $dia['horaInicio']);
                                                $fin = strtotime("1970-01-01 " . $dia['horaFin']);
                                                
                                                if($inicio && $fin) {
                                                    $diff = $fin - $inicio;
                                                    // Si fin es menor que inicio, asumimos que es al día siguiente
                                                    if($diff < 0) {
                                                        $diff += 86400; // Añadir 24 horas en segundos
                                                    }
                                                    
                                                    $minutos = round($diff / 60);
                                                }
                                            } catch(\Exception $e) {
                                                $minutos = 0;
                                            }
                                        }
                                    @endphp
                                    <tr>
                                        <td>{{ $fechaFormateada }}</td>
                                        <td>{{ $dia['horaInicio'] ?? '' }}</td>
                                        <td>{{ $fechaFormateada }}</td>
                                        <td>{{ $dia['horaFin'] ?? '' }}</td>
                                        <td>{{ $minutos }}</td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td></td>
                                    <td>0</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Sección de firmas -->
            <div class="signature-section">
                <div class="signature-row">
                    <div class="signature-col">
                        <div class="signature-line"></div>
                        <div class="signature-name">F.</div>
                        <div class="signature-title">Solicitante</div>
                    </div>
                    <div class="signature-col">
                        <div class="signature-line"></div>
                        <div class="signature-name">F.</div>
                        <div class="signature-title">Revisado Especialista/Especialista Líder</div>
                    </div>
                    <div class="signature-col">
                        <div class="signature-line"></div>
                        <div class="signature-name">F.</div>
                        <div class="signature-title">Responsable Delegado BCR</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>