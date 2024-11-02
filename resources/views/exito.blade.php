<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html dir="ltr" xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta name="viewport" content="width=device-width" />
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <link rel="apple-touch-icon" href="{{asset('/images/logo_carpio.png')}}">
    <link rel="shortcut icon" type="image/x-icon" href="{{asset('/images/logo_carpio.png')}}">
      <title>Confirmación de Cita | Carpio SAC</title>
</head>

<body style="margin:0px; background: #f8f8f8; ">
    <div width="100%" style="background: #f8f8f8; padding: 0px 0px; font-family:arial; line-height:28px; height:100%;  width: 100%; color: #514d6a;">
        <div style="max-width: 700px; padding:50px 0;  margin: 0px auto; font-size: 14px">
            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%; padding-bottom: 10px; background-color:#fff;">
                <tbody>
                    <tr>
                        <td style="vertical-align: middle; padding-bottom:2px;padding-top:5px;" align="center">
                            <img src="http://carpio.ayluby.com/images/logo_carpio.png" alt="Logo Carpio" width="95px" style="border:none">
                        </td>
                    </tr>
                </tbody>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                <tbody>
                    <tr>
                        <td style="background:#2962FF; padding:20px; color:#fff; text-align:center;"> <strong>NOTIFICACIÓN DEL SISTEMA </strong></td>
                    </tr>
                </tbody>
            </table>
            <div style="padding: 40px; background: #fff;">
                <table border="0" cellpadding="0" cellspacing="0" style="width: 100%;">
                    <tbody>
                        <tr>
                            <td>
                                @if($msje == 'Exito')
                                    <p>Gracias <b> por Agendar tu Cita con Nosotros </b></p>
                                    <small>En Breve un representante de nuestro equipo se pondrá en contacto con usted.</small>
                                    <br>
                                    <!-- <p><strong>Te damos la bienvenida, </strong> para completar el registro, te hemos enviado un correo Electrónico, para confirmar la veracidad de tu información.</p> -->
                                @else
                                    <p>
                                        Se notifica que el Cliente: <strong>{{$nombrePersona}}, con Documento: {{$documento}}</strong>
                                        ha agendado una cita para el <mark>{{$fecha}}</mark> a horas <mark>{{$hora}}</mark>
                                    </p>

                                    <p>
                                        Teléfono: <strong> {{$telefono}} </strong>
                                        <br/>
                                        Correo Electrónico: <strong>{{$correoElectronico}}</strong>
                                    </p>
                                    <!-- <p>Gracias por confirmar tu Correo Electrónico, <b>Ya eres parte de nuestro EQUIPO.</b></p> 
                                    <p>te damos la bienvenida y a la vez te hemos enviado un Correo Electrónico con tus credenciales de Acceso a nuestra plataforma.</p> -->
                                @endif                                
                                <b> Gracias - La Administración.</b> 
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            <div style="text-align: center; font-size: 12px; color: #9b9b9b; padding-top: 20px;padding-bottom:12px;background-color:#fff;">
                Realizado por Carpio SAC
                    <!--<a href="javascript:void(0);" style="color: #b2b2b5; text-decoration: underline;">Iniciar Sesión</a> </p>-->
            </div>
        </div>
    </div>

</body>
</html>