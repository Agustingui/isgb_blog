<?php
/**
 * Política de privacidad.
 *
 * @var array $instituto Datos del instituto (email de contacto).
 */
?>
<article class="container detalle py-5">
    <a href="<?= base_url() ?>" class="volver"><span class="material-symbols-outlined" aria-hidden="true">arrow_back</span> Volver al inicio</a>

    <header>
        <h1 class="h2 mt-4">Política de privacidad</h1>
        <p class="texto-suave">Última actualización: <?= formatear_fecha(date('Y-m-d')) ?></p>
    </header>

    <div class="contenido-enriquecido">
        <h2>Qué datos guardamos</h2>
        <ul>
            <li><strong>Si creás una cuenta o ingresás al sitio</strong> (con email y contraseña o con Google): tu nombre, tu email y tu contraseña, que se guarda cifrada y no podemos leerla.</li>
            <li><strong>Si usás el blog estando ingresado:</strong> tus “me gusta” y las lecturas de posteos.</li>
        </ul>

        <h2>Para qué los usamos</h2>
        <ul>
            <li>Identificarte cuando ingresás y guardar tus “me gusta”.</li>
        </ul>
        <p>No vendemos ni compartimos tus datos con terceros. Los mails los enviamos con el servidor de correo del instituto, y si elegís ingresar con Google, Google nos informa tu nombre y tu email.</p>

        <h2>Menores de edad</h2>
        <p>Si tenés menos de 18 años, pedile permiso a tu madre, padre o tutor antes de crear una cuenta. El instituto puede pedir esa autorización cuando lo considere necesario.</p>

        <h2>Tus derechos</h2>
        <ul>
            <li>Podés cambiar tu nombre y tu contraseña desde <a href="<?= base_url('cuenta') ?>">Mi cuenta</a>.</li>
            <li>Podés borrar tu cuenta y tus datos cuando quieras desde la misma pantalla.</li>
            <li>
                También podés pedir acceso, corrección o eliminación de tus datos escribiendo a
                <?php if ($instituto['email_contacto']): ?>
                    <a href="mailto:<?= esc($instituto['email_contacto']) ?>"><?= esc($instituto['email_contacto']) ?></a>.
                <?php else: ?>
                    la secretaría del instituto.
                <?php endif ?>
            </li>
        </ul>
        <p>Como titular de los datos tenés los derechos que reconoce la Ley 25.326 de Protección de los Datos Personales. La Agencia de Acceso a la Información Pública es el organismo de control.</p>

        <h2>Cookies</h2>
        <p>Usamos solo las cookies necesarias para que funcione el ingreso y la seguridad de los formularios. No usamos cookies de publicidad.</p>
    </div>
</article>
