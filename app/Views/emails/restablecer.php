<?php
/**
 * Email HTML de recuperación de contraseña. Los estilos van en línea porque los clientes de correo ignoran las hojas de estilo.
 *
 * @var string $nombre Nombre del destinatario.
 * @var string $enlace URL con el token de recuperación.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Restablecer tu contraseña</title>
</head>
<body style="margin:0;padding:0;background:#f3f7fa;font-family:Arial,Helvetica,sans-serif;color:#0f2a43;">
    <table width="100%" cellpadding="0" cellspacing="0" style="background:#f3f7fa;padding:24px 0;">
        <tr><td align="center">
            <table width="560" cellpadding="0" cellspacing="0" style="background:#ffffff;border-radius:8px;overflow:hidden;">
                <tr><td style="background:#0b4c7c;color:#ffffff;padding:20px 28px;font-size:18px;">Instituto Superior Gaspar Benavento</td></tr>
                <tr><td style="padding:28px;">
                    <h1 style="margin:0 0 12px;font-size:20px;">Hola, <?= esc($nombre) ?></h1>
                    <p style="margin:0 0 20px;font-size:15px;line-height:1.5;">Pediste crear una contraseña nueva. El enlace dura 1 hora.</p>
                    <a href="<?= esc($enlace) ?>" style="display:inline-block;background:#1470a6;color:#ffffff;text-decoration:none;padding:10px 20px;border-radius:6px;font-size:14px;">Crear contraseña nueva</a>
                    <p style="margin:20px 0 0;font-size:12px;color:#4c6c86;">Si no lo pediste vos, ignorá este mail: tu contraseña sigue igual.</p>
                </td></tr>
            </table>
        </td></tr>
    </table>
</body>
</html>
