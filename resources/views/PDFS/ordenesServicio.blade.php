<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recibo</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
    </style>
</head>
<body>
    <div style="display: table; width: 100%;">
        <div style="display: table-cell;">
            <img src="{{ public_path('logo-OT-PDF.png') }}" alt="Logo" width="180">
        </div>
        <div style="display: table-cell; vertical-align: middle; text-align: center;">
            <div style="display: table; margin: 0 auto;">
                <h1 style="margin: 0; padding: 0; color: #26227B; font-size: 32px; font-family: 'Arial Narrow Bold', Arial, Helvetica, sans-serif; font-weight: bold;">TRANSMISION DIGITAL</h1>
                <p style="margin: 0; padding: 0; color: #26227B; font-size: 13px; font-weight: 600;">R.F.C. MAQR-621231-84A</p>
                <p style="margin: 0; padding: 0; color: #26227B; font-size: 13px; font-weight: 600;">CALLE LOBO No. 11 FRACC. LOS VIÑEDOS</p>
                <p style="margin: 0; padding: 0; color: #26227B; font-size: 13px; font-weight: 600;">TEL. OFICINA: 750-54-77</p>
                <p style="margin: 0; padding: 0; color: #26227B; font-size: 13px; font-weight: 600;">C.P. 27019 TORREON, COAH.</p>
                <h3 style="margin: 0; padding: 0; color: #26227B; font-size: 18px; font-weight: bold; margin-top: 10px;">Recibo de Equipo, Material o Servicio</h3>
            </div>
        </div>
        <div style="display: inline-block; border: 2px solid #26227B; border-radius: 6px; overflow: hidden; margin: 0; padding: 0; width: 100%;">
            <div style="">
                <p style="margin: 0; padding: 1px; font-size: 14px; font-weight: bold; color: #26227B; text-align: center; letter-spacing: 4px; border-bottom: 1.5px solid #26227B; background-color: #b7b7d0;">RECIBO</p>
                <p style="margin: 10px 0 0 12px; font-weight: bold; color: #bb0c0c; text-align: left;">#  <span style="font-size: 20px; font-weight: bold; color: #bb0c0c; margin-left: 18px;">{{$data['folio']}}</span></p>
            </div>
            <div style="position: relative;">
                <p style="margin: 0; padding: 1px; font-size: 14px; font-weight: bold; color: #26227B; text-align: center; letter-spacing: 4px; border-bottom: 1.5px solid #26227B; border-top: 1.5px solid #26227B; background-color: #b7b7d0;">FECHA</p>
                <div style="display: table; width: 100%;">
                    <div style="display: table-row;">
                        <div style="display: table-cell;  padding-left: 6px;">
                            <span style="font-size: 10px; font-weight: bold; position: absolute; top: 20px;">DIA</span>
                            <p style="margin: 10px 0 0; padding: 0;">{{ $data['fecha'][2] }} /</p>
                        </div>
                        <div style="display: table-cell;">
                            <span style="font-size: 10px; font-weight: bold; position: absolute; top: 20px;">MES</span>
                            <p style="margin: 10px 0 0; padding: 0;">{{$data['fecha'][1]}} /</p>
                        </div>
                        <div style="display: table-cell;">
                            <span style="font-size: 10px; font-weight: bold; position: absolute; top: 20px;">AÑO</span>
                            <p style="margin: 10px 0 0; padding: 0;">{{$data['fecha'][0]}}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <main style="margin-top: 25px; margin-left: 5px; display: table; width: 100%;">
        <div style="width: 100%;
    display: table-caption; margin-bottom: 8px">
            <div style="display: inline-block; padding-right: 10px;">
                <label style="font-size: 14px; font-weight: 700; color: #26227B; white-space: nowrap;">CLIENTE: </label>
            </div>
            <div style="display: inline-block; width: 87%;">
                <input style="font-size: 14px; font-weight: bold; color: #000; margin: 0; padding: 0; border: none; border-bottom: #26227B 1px solid; margin-right: 2px; padding-left: 5px; width: 100%;" 
                value="{{$data['cliente']}}" type="text"/>
            </div>
        </div>
        <div style="width: 100%;
    display: table-caption; margin-bottom: 8px">
            <div style="display: inline-block; padding-right: 10px;">
                <label style="font-size: 14px; font-weight: 700; color: #26227B; white-space: nowrap;">SUCURSAL: </label>
            </div>
            <div style="display: inline-block; width: 72.7%;">
                <input style="font-size: 14px; font-weight: bold; color: #000; margin: 0; padding: 0; border: none; border-bottom: #26227B 1px solid; margin-right: 2px; padding-left: 5px; width: 100%;" 
                value="{{$data['sucursal']}}" type="text"/>
            </div>
        </div>
        <div style="width: 100%;
    display: table-caption; margin-bottom: 10px">
            <div style="display: inline-block; padding-right: 10px;">
                <label style="font-size: 14px; font-weight: 700; color: #26227B; white-space: nowrap;">DIRECCIÓN DE ENTREGA: </label>
            </div>
            <div style="display: inline-block; width: 71%;">
                <input style="font-size: 14px; font-weight: bold; color: #000; margin: 0; padding: 0; border: none; border-bottom: #26227B 1px solid; margin-right: 2px; padding-left: 5px; width: 100%;" 
                value="{{$data['direccion']}}" type="text"/>
            </div>
        </div>
        <div style="width: 100%;display: inline-block; margin-top: 3px">

            <div style="width: 45%;
            display: inline-block; margin-right: 10px;">
                <div style="display: inline-block; padding-right: 10px;">
                    <label style="font-size: 14px; font-weight: 700; color: #26227B; white-space: nowrap;">TEL: </label>
                </div>
                <div style="display: inline-block; width: 73.2%;">
                    <input style="font-size: 14px; font-weight: bold; color: #000; margin: 0; padding: 0; border: none; border-bottom: #26227B 1px solid; margin-right: 2px; padding-left: 5px; width: 100%;" value="{{$data['telefono']}}" type="text"/>
                </div>
            </div>

            <div style="width: 50%;
            display: inline-block;">
                <div style="display: inline-block;; padding-right: 10px;">
                    <label style="font-size: 14px; font-weight: 700; color: #26227B; white-space: nowrap;">TECNICO: </label>
                </div>
                <div style="display: inline-block; width: 73.2%;">
                    <input style="font-size: 14px; font-weight: bold; color: #000; margin: 0; padding: 0; border: none; border-bottom: #26227B 1px solid; margin-right: 2px; padding-left: 5px; width: 100%;" value="{{$data['tecnico']}}" type="text"/>
                </div>
            </div>
        </div>
    </main>    
    
    <div style="width: 100%; margin-top: 15px; overflow: hidden;">
        <table style="font-size: 14px; border-collapse: collapse; width: 100%; overflow: hidden; margin: 0; padding: 0;">
            <thead style="color: #26227B; background-color: #b7b7d0;">
                <tr>
                    <th style="width: 10%; border: 1px solid #26227B; padding: 5px;">Cantidad</th>
                    <th style="width: 10%; border: 1px solid #26227B; padding: 5px;">Unidad</th>
                    <th style="width: 50%; border: 1px solid #26227B; padding: 5px;">Equipo / Servicio / Material</th>
                    <th style="width: 15%; border: 1px solid #26227B; padding: 5px;">Precio Unitario</th>
                    <th style="width: 15%; border: 1px solid #26227B; padding: 5px;">Total</th>
                </tr>
            </thead>
            <tbody style="text-align: center;">
                @foreach ($data['orden'] as $item)
                    <tr>
                        <td style="border: 1px solid #26227B; padding: 5px; height:15px;">{{$item['cantidad']}}</td>
                        <td style="border: 1px solid #26227B; padding: 5px;">{{$item['unidad']}}</td>
                        <td style="font-size: 12px; text-align: left; border: 1px solid #26227B; padding: 5px;">{{$item['producto']}}</td>
                        <td style="border: 1px solid #26227B; padding: 5px;">{{$item['precio']}}</td>
                        <td style="border: 1px solid #26227B; padding: 5px;">{{$item['total']}}</td>
                    </tr>
                @endforeach
                <!-- Repetir filas vacías -->
                <tr>
                    <td colspan="3" rowspan="3" style="border: none;">
                        <div style="width: 100%; border: #26227B 2px solid; border-radius: 6px; overflow: hidden; margin: 5px; text-align: left;">
                                <p style="color: #26227B; text-align: left;"></p>
                        </div>
                    </td>
                    <td style="border: none; text-align: right; padding-right: 25px;">Subtotal:</td>
                    <td style="border: 1px solid #26227B; padding: 5px;">{{$data['subtotal']}}</td>
                </tr>
                <tr>
                    <td style="border: none; text-align: right; padding-right: 25px;">IVA:</td>
                    <td style="border: 1px solid #26227B; padding: 5px;">{{$data['iva']}}</td>
                </tr>
                <tr>
                    <td style="border: none; text-align: right; padding-right: 25px; border-radius: 0 0 10px 0;">Total:</td>
                    <td style="border: 1px solid #26227B; padding: 5px;">{{$data['total']}}</td>
                </tr>
            </tbody>
        </table>
    </div>
    
    <footer style="display: table; margin-top: 40px; width: 100%;">
        <div style="margin-bottom: 0px;">
            <!-- <div style="display: inline-block; width: auto;">
                <hr style="width: 280px; margin: 0; padding: 0;"/>
                <p style="text-align: center; margin: 0; padding: 0; font-size: 15px;">Firma de recibido</p>
            </div>
            <div style="display: inline-block; width: auto; margin-left: 17%;">
                <hr style="width: 280px; margin: 0; padding: 0;"/>
                <p style="text-align: center; margin: 0; padding: 0; font-size: 15px;">Firma del Técnico</p>
            </div> -->
        </div>
        <div style="display: inline-block; margin-left: 30%;">
            <div style=" width: 100%;">
                <div style="margin-left: 40%; ">
                    @if(!is_null($data['firma']))
                        <img src="{{ public_path('imagenes/firmas/' . $data['firma']) }}" alt="Logo" width="180">
                    @else
                        <p>Sin firma</p>
                    @endif
                </div>
                <hr style="width: 280px; margin: 0; padding: 0;"/>
                <p style="text-align: center; margin: 0; padding: 0; font-size: 15px;">Firma de recibido</p>
            </div>
        </div>
    </footer>
</body>
</html>
