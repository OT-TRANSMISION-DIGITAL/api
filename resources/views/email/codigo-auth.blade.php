<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>

<body>

    <table width="100%" cellpadding="0" cellspacing="0" style="background-color: #f8f9fa; padding: 3rem 1rem;">
        <tr>
          <td align="center">
            <table width="100%" cellpadding="0" cellspacing="0" style="max-width: 28rem;">
              <tr>
                <td align="center" style="padding-bottom: 1.5rem;">
                </td>
              </tr>
              <tr>
                <td align="center" style="text-align: center;">
                  <h1 style="font-size: 1.875rem; font-weight: bold; color: #333;">Verifica tu cuenta</h1>
                  <p style="margin-top: 0.5rem; color: #6c757d;">
                    Ingresa el siguiente código para completar la verificación de tu cuenta.
                  </p>
                </td>
              </tr>
              <tr>
                <td align="center" style="background-color: #e9ecef; border-radius: 6px; padding: 2rem 1.5rem; text-align: center;">
                  <div style="font-size: 3.75rem; font-weight: bold; color: #3E4095;">{{$codigo}}</div>
                  <p style="margin-top: 0.5rem; font-size: 0.875rem; color: #6c757d;">Este código es válido por 10 minutos.</p>
                </td>
              </tr>
              <tr>
                <td align="center" style="text-align: center; font-size: 0.875rem; color: #6c757d;">
                  Si no solicitaste este código, puedes ignorar este mensaje.
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>

    
</body>
</html>