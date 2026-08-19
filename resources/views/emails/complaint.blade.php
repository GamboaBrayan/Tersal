<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; background-color: #f4f4f4; margin: 0; padding: 20px; }
        .container { background-color: #ffffff; max-width: 600px; margin: 0 auto; padding: 20px; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); }
        h1 { color: #1B3BE3; font-size: 24px; border-bottom: 2px solid #f4f4f4; padding-bottom: 10px; }
        .section { margin-bottom: 20px; }
        .section h2 { font-size: 18px; color: #333; margin-bottom: 10px; background-color: #f9f9f9; padding: 8px; border-left: 4px solid #1B3BE3; }
        .row { margin-bottom: 8px; }
        .label { font-weight: bold; color: #555; }
        .value { color: #000; }
        .highlight { font-size: 20px; color: #e53e3e; font-weight: bold; text-align: center; padding: 10px; background-color: #fee2e2; border-radius: 4px; margin-bottom: 20px;}
    </style>
</head>
<body>
    <div class="container">
        <h1>Nuevo Registro: Libro de Reclamaciones</h1>
        
        <div class="highlight">
            CÓDIGO: {{ $generated_id }}
        </div>

        <div class="section">
            <h2>1. Identificación del Consumidor</h2>
            @if($es_menor)
                <div class="row"><span class="label">Es menor de edad:</span> <span class="value">Sí</span></div>
                <div class="row"><span class="label">Nombre del Apoderado:</span> <span class="value">{{ $nombre_apoderado }}</span></div>
            @endif
            <div class="row"><span class="label">Nombres y Apellidos:</span> <span class="value">{{ $nombres }} {{ $apellidos }}</span></div>
            <div class="row"><span class="label">Documento:</span> <span class="value">{{ $tipo_documento }} - {{ $numero_documento }}</span></div>
            <div class="row"><span class="label">Domicilio:</span> <span class="value">{{ $direccion }}, {{ $distrito }}</span></div>
            <div class="row"><span class="label">Teléfono:</span> <span class="value">{{ $telefono }}</span></div>
            <div class="row"><span class="label">Email:</span> <span class="value">{{ $email }}</span></div>
        </div>

        <div class="section">
            <h2>2. Identificación del Bien Contratado</h2>
            <div class="row"><span class="label">Tipo:</span> <span class="value">{{ strtoupper($tipo_bien) }}</span></div>
            <div class="row"><span class="label">Monto Reclamado:</span> <span class="value">S/ {{ $monto_reclamado }}</span></div>
            <div class="row"><span class="label">Descripción:</span> <span class="value">{{ $descripcion_bien }}</span></div>
        </div>

        <div class="section">
            <h2>3. Detalle de la Reclamación</h2>
            <div class="row"><span class="label">Tipo:</span> <span class="value">{{ strtoupper($tipo_reclamo) }}</span></div>
            <div class="row"><span class="label">Detalle:</span></div>
            <div class="row value" style="padding: 10px; background-color: #f4f4f4; border-radius: 4px;">{{ $detalle }}</div>
            
            @if(!empty($pedido))
                <div class="row" style="margin-top:10px;"><span class="label">Pedido/Solución esperada:</span></div>
                <div class="row value" style="padding: 10px; background-color: #f4f4f4; border-radius: 4px;">{{ $pedido }}</div>
            @endif
        </div>
        
        <div style="text-align: center; margin-top: 30px; font-size: 12px; color: #888;">
            Este correo fue enviado automáticamente desde el sistema de Libro de Reclamaciones de Tersal.
        </div>
    </div>
</body>
</html>
