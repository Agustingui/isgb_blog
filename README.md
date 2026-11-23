<p align="center">
  <img src="public/img/logo.png" alt="ISGB" width="120">
</p>

# ISGB Blog

Es la página web del Instituto Superior Gaspar Benavento. Ahí se publican las noticias del instituto, la info de cada carrera y un blog para los alumnos. También tiene un panel para administrar todo.

Está hecha en PHP 8.2 con CodeIgniter 4, MySQL y Bootstrap 5.

## Qué se puede hacer

**En la parte pública**

- Leer las noticias y el blog (los posteos tienen me gusta y contador de visitas)
- Ver cada carrera con sus materias, el plan de estudios y la resolución
- Buscar en todo el sitio

**Con una cuenta**

- Registrarte con tu mail o entrar directo con Google
- Recuperar la contraseña si te la olvidaste
- Cambiar tus datos o tu contraseña, o borrar la cuenta desde "Mi cuenta"

**En el panel** (`/admin`)

- Cargar, editar y borrar noticias, posteos, carreras, imágenes y usuarios
- Escribir con un editor de texto (Quill) y buscar en las tablas (DataTables)
- Recuperar desde la papelera lo que se borró sin querer
- Cambiar la configuración del sitio

## Quién puede hacer qué

- **alumnado**: entra a su cuenta y puede dar me gusta en el blog.
- **editor**: lo mismo, y además carga y edita noticias, posteos e imágenes.
- **admin**: puede hacer todo. Es el único que borra contenido y maneja carreras, usuarios, papelera y configuración.

Si querés ver o cambiar los permisos, están en [app/Config/Routes.php](app/Config/Routes.php) y en el filtro [AuthFilter](app/Filters/AuthFilter.php).

## Cómo instalarlo (XAMPP en Linux)

Necesitás tener instalados XAMPP (con PHP 8.2 o más nuevo), Composer y Git.

Primero levantá XAMPP:

```bash
sudo /opt/lampp/lampp start
```

Cloná el repo adentro de `htdocs` e instalá las dependencias (la carpeta `vendor/` no viene en el repo):

```bash
cd /opt/lampp/htdocs
git clone https://github.com/Agustingui/isgb_blog.git
cd isgb_blog
composer install
```

Importá la base. El archivo `.sql` ya crea la base `isgb_blog` solo, así que no hace falta crearla antes:

```bash
/opt/lampp/bin/mysql -u root < database/isgb_blog_mysql.sql
```

Usá el `mysql` de XAMPP (`/opt/lampp/bin/mysql`) y no el del sistema, porque si no puede conectarse a otro servidor. También lo podés importar desde phpMyAdmin (http://localhost/phpmyadmin, pestaña _Importar_).

Copiá `env.example` a `.env` (`cp env.example .env`) y completá los datos de la base:

```ini
CI_ENVIRONMENT = development
app.baseURL = 'http://localhost/isgb_blog/public/'

database.default.hostname = localhost
database.default.database = isgb_blog
database.default.username = root
database.default.password =
```

Con eso el sitio ya anda. Lo que sigue es opcional.

**Ingreso con Google.** Creá unas credenciales OAuth en Google Cloud Console y agregá como URI de redirección `http://localhost/isgb_blog/public/login/google/volver`. Después poné esto en el `.env`:

```ini
google.clientId = ...
google.clientSecret = ...
google.dominio = ...          # opcional: para que solo entren mails de ese dominio
```

**Cerrar el registro.** Por defecto cualquiera se puede registrar. Si no querés eso:

```ini
auth.registroAbierto = false
```

**Envío de mails** (para recuperar la contraseña). Si no configurás un remitente, el sitio no intenta mandar nada. Con un servidor SMTP sería así:

```ini
email.fromEmail = no-responder@tu-dominio
email.protocol = smtp
email.SMTPHost = smtp.tu-dominio
email.SMTPUser = ...
email.SMTPPass = ...
email.SMTPPort = 587
email.SMTPCrypto = tls
```

Con eso ya está. Entrá a http://localhost/isgb_blog/public/ para ver el sitio, o a `/admin` para el panel.

Para entrar al panel, la base ya trae un usuario admin:

- **Mail:** `admin@isgbenavento.edu.ar`
- **Contraseña:** `admin123`

**Ojo:** esta contraseña es solo para probar en tu compu. Antes de subir el sitio a un servidor, cambiala.

## Problemas comunes en Linux

### `mkdir(): Permiso denegado` al subir una imagen

Apache corre con su propio usuario, y ese usuario necesita poder escribir en `writable/` y en `public/uploads/`. Pero cuando clonás el repo, esas carpetas quedan a tu nombre. Por eso, la primera vez que subís una imagen, Apache intenta crear `public/uploads/` y no puede.

El usuario de Apache no es el mismo en todas las compus. En XAMPP casi siempre es `daemon`, pero si usás el Apache de tu distro puede ser `www-data` (Ubuntu/Debian), `apache` (Fedora) o `http` (Arch). Para ver cuál es el tuyo:

```bash
grep -E "^(User|Group)" /opt/lampp/etc/httpd.conf
```

Te va a mostrar algo como `User daemon` y `Group daemon`. Si no usás XAMPP, buscalo así con Apache andando:

```bash
ps -eo user,comm | grep -E "httpd|apache2" | grep -v root | sort -u
```

Después dale las carpetas a ese usuario. Cambiá `daemon` por el que te salió:

```bash
APACHE_USER=daemon
sudo mkdir -p public/uploads
sudo chown -R $APACHE_USER:$APACHE_USER writable public/uploads
sudo chmod -R 775 writable public/uploads
```

## Problemas comunes en Windows (XAMPP)

El XAMPP de Windows trae apagadas dos extensiones de PHP que el proyecto necesita:

- **zip**: sin ella, `composer install` falla con *"The zip extension and unzip/7z commands are both missing"*.
- **gd**: sin ella, no se pueden subir imágenes (el sitio las achica al guardarlas). En `writable/logs/` aparece *"The framework needs the following extension(s) installed and loaded: GD"*.

Para activarlas, abrí `C:\xampp\php\php.ini`, buscá estas dos líneas y sacales el `;` del principio:

```ini
extension=gd
extension=zip
```

Después reiniciá el servidor (Apache desde el panel de XAMPP, o `php spark serve`). Con `php -m` podés comprobar que aparezcan `gd` y `zip`.

## Cómo está organizado

```text
app/
  Config/        rutas y configuración
  Controllers/   controladores del sitio y del panel (los Admin*)
  Filters/       control de acceso según el rol
  Models/
  Views/         publico/, admin/, emails/, etc.
database/        archivo .sql de la base
public/          raíz web (assets, img, uploads)
tests/
writable/        caché, logs y sesiones
```

## Licencia

El proyecto es de código abierto bajo la licencia MIT: lo podés usar, copiar y modificar libremente, siempre que mantengas el aviso de copyright. El texto completo está en [LICENSE](LICENSE).
