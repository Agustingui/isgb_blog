# Contexto para agentes de IA — ISGB Blog

Este archivo es el **contexto compartido del equipo**. Lo leen todos los agentes de IA que se usen en el
repo (Claude Code, Cursor, Copilot, Codex, etc.), así todos trabajan con las mismas reglas sin importar
quién los use. Se mantiene entre todos: si aprendés algo que le sirve al resto, se agrega acá (ver
[Cómo actualizar este archivo](#cómo-actualizar-este-archivo)).

> **Agente:** antes de editar código, seguí los pasos de [Antes de empezar](#antes-de-empezar). Si la
> persona con la que trabajás no conoce estas reglas, recomendale leer este archivo.

## El proyecto

Sitio del Instituto Superior Gaspar Benavento: noticias, carreras, blog de alumnos y un panel de
administración en `/admin`. PHP 8.2 + CodeIgniter 4.7 + MySQL + Bootstrap 5.3 (por CDN). La instalación
está en el [README](README.md).

```text
app/Config/Routes.php       todas las rutas y los permisos por rol (autoRoute está apagado)
app/Controllers/            públicos (Blog, Noticias...) y del panel (Admin*); todos heredan BaseController
app/Filters/AuthFilter.php  filtro "auth:rol1,rol2"
app/Helpers/isgb_helper.php funciones para las vistas (fechas, HTML seguro, recurso())
app/Models/                 un modelo por tabla
app/Views/publico, admin    cada sección con sus partes/ reutilizables
public/assets/              publico.css/js, admin.css/js, comun.js
database/isgb_blog_mysql.sql la base completa (no hay migraciones)
```

## Antes de empezar

Hacé esto al principio de cada tarea, antes de tocar archivos:

1. `git status`: si hay cambios sin commitear que no son tuyos, **no los toques** y avisale a la persona.
2. `git fetch origin` y fijate si la rama actual está atrás de `origin/main`. Si está atrás, proponé
   actualizarla antes de seguir.
3. Si estás en `main`, creá una rama antes de editar (ver [Ramas](#ramas-y-commits)).
4. Fijate en qué está trabajando el resto: `gh pr list` y, para ver los archivos de un PR,
   `gh pr view <n> --json files`. Si tu tarea toca los mismos archivos que un PR abierto, **avisale a la
   persona antes de editar** para que se pongan de acuerdo.
5. Leé [Decisiones del equipo](#decisiones-del-equipo): ahí está lo que ya se acordó.

## Reglas para no pisarse

- **Nunca trabajes directo en `main`.** Todo cambio va en una rama y entra por Pull Request.
- **Nunca uses `git push --force`** en `main` ni en ramas de otra persona. En tu propia rama, solo
  `--force-with-lease` y solo si la persona lo pide.
- **No borres trabajo ajeno:** nada de `git reset --hard`, `git checkout -- .`, `git clean` ni
  `git stash drop` sin preguntar antes.
- **Cambios chicos y enfocados.** Un tema por rama. No reformatees, no reordenes, no renombres ni
  "limpies" archivos que la tarea no necesita tocar: eso genera conflictos con el trabajo del resto.
- **Respetá el estilo de lo que ya existe**, aunque prefieras otro. Si te parece que hay que cambiar una
  convención, proponelo como decisión del equipo; no lo cambies por tu cuenta.
- **Archivos que tocamos todos** (cuidado extra, cambios mínimos y avisar en el PR):
  - `app/Config/Routes.php`: agregá cada ruta en el grupo de su rol, al lado de las de su sección.
  - `app/Controllers/BaseController.php` y `app/Helpers/isgb_helper.php`: si cambiás una función que ya
    existe, buscá dónde se usa (`grep`) y no rompas a quien la llama.
  - `database/isgb_blog_mysql.sql`: ver [Base de datos](#base-de-datos).
  - `public/assets/js/admin.js`, `public/assets/css/*.css`, `app/Views/*/header.php` y `footer.php`.
- **Antes de abrir el PR**, actualizá la rama con `origin/main` y resolvé los conflictos vos. Si un
  conflicto es con código de otra persona y no está claro cuál versión va, preguntá.

## Ramas y commits

- Nombre de rama: `tu-nombre/tema-corto`, por ejemplo `alexis/filtro-noticias` o `agustin/fix-login`.
- Mensajes de commit en español, cortos y que digan qué cambia: "Agrega filtro por categoría en
  noticias", "Corrige el borrado de imágenes".
- En el PR contá qué cambiaste, cómo probarlo y si toca alguno de los archivos compartidos o la base.
- Hacé commit o push solo cuando la persona lo pida.

## Convenciones del código

- **Todo en español:** nombres de clases, métodos, variables, tablas, columnas, comentarios y textos.
  Los textos que ve la gente van en voseo ("Ingresá", "No tenés permiso").
- **Controladores:** heredan `BaseController`. Las páginas se arman con `$this->vistaPublica()` o
  `$this->vistaAdmin()`. Las consultas con joins usan Query Builder (`$this->db->table(...)`); para
  buscar, insertar, actualizar o borrar una fila, el modelo. Antes de escribir una consulta nueva, mirá
  si ya existe en `BaseController` (`consultaNoticias()`, `consultaPosteos()`, `listaImagenes()`...).
- **Panel (CRUD):** seguí el patrón de `AdminNoticias`: `index`, `nuevo`, `editar`, `insertar` y
  `actualizar` usan un `guardar($id)` privado; `borrar` responde con `respuestaAccion()` (JSON en AJAX).
- **Validación:** reglas en el controlador con `label` en español. Los mensajes están en
  `app/Language/es/Validation.php`. Los errores vuelven con
  `redirect()->back()->withInput()->with('errores', $errores)`. Los avisos usan `->with('mensaje', ...)`
  o `->with('error', ...)`.
- **Modelos:** `$allowedFields` explícito, `useTimestamps` con `fecha_registro` y `fecha_edicion`, y
  borrado lógico con `fecha_borrado`. Las consultas con Query Builder tienen que filtrar
  `fecha_borrado IS NULL` a mano, también en los joins.
- **Roles:** `alumnado`, `editor` y `admin`. Los permisos se dan por grupo de rutas en `Routes.php`. No
  chequees roles dentro de los controladores salvo para casos puntuales.
- **Seguridad:**
  - El CSRF está activo en todo el sitio: cada `<form method="post">` lleva `<?= csrf_field() ?>`, y las
    respuestas AJAX devuelven el `csrf` nuevo.
  - En las vistas, todo dato va con `esc()`.
  - El HTML del editor Quill se guarda solo después de pasar por `contenido_html_seguro()`.
  - Los archivos se suben con `guardarArchivo()` o `moverArchivo()`, que validan y achican las imágenes.
- **Vistas:** cada una documenta sus variables con `@var` al principio. Para lo repetido usá las vistas
  de `partes/`. Los CSS y JS propios se cargan con `recurso('assets/...')`, que les agrega la versión
  para evitar el caché del navegador.
- **Comentarios:** en español, explicando el *por qué*, como en el resto del código.

## Base de datos

- No hay migraciones: la estructura y los datos de prueba están en `database/isgb_blog_mysql.sql`.
- Si cambiás una tabla, actualizá ese `.sql` **en el mismo PR**, escribí en el PR el `ALTER TABLE` que
  tiene que correr el resto en su base local y agregá el cambio en [Decisiones del equipo](#decisiones-del-equipo).
- Dos personas cambiando el `.sql` al mismo tiempo es el conflicto más difícil de resolver: coordinalo antes.

## Lo que no se toca ni se sube

- `.env` (credenciales): no lo leas ni lo muestres. Si hace falta una variable nueva, agregala en
  `env.example` y en el README.
- `vendor/`, `writable/` y `public/uploads/`: son locales.
- No subas contraseñas, client secrets de Google ni datos reales de alumnos.
- `admin123` es la contraseña de prueba del admin; solo para usar en la compu de cada uno.

## Probar los cambios

- `composer install` la primera vez (no viene `vendor/`).
- En XAMPP de Windows hay que activar `extension=zip` y `extension=gd` en `C:\xampp\php\php.ini`: sin
  `zip` falla `composer install` y sin `gd` falla la subida de imágenes. Si algo de eso falla, revisá
  `writable/logs/` antes de tocar código (ver el README).
- `php spark serve` levanta el sitio en http://localhost:8080. También se puede usar XAMPP; ver el README.
- `composer test` corre PHPUnit. Por ahora solo están los tests de ejemplo de CodeIgniter.
- Para cambios en el panel, probá con un usuario `admin` y con uno `editor`: no ven lo mismo.

## Cómo actualizar este archivo

- Esta es la memoria compartida del equipo. Cuando descubras algo que no sale del código y le ahorra
  tiempo al resto (una decisión, una trampa, un paso de instalación), **proponé agregarlo acá** y
  pedile permiso a la persona antes de editar.
- Va en el mismo PR que el cambio que lo motivó, así el resto lo revisa.
- Nada personal acá. Las preferencias de cada uno van en la configuración local de su herramienta, que
  no se sube.
- Este es el único archivo de contexto. No crees reglas en archivos propios de una herramienta
  (`.cursorrules`, `.github/copilot-instructions.md`, etc.): si hace falta una regla, va acá para que la
  vean todos. La única excepción es `CLAUDE.md`, que tiene solo la línea `@AGENTS.md` porque Claude Code
  no lee este archivo por su cuenta.
- Mantenelo corto: si una regla deja de valer, se borra.

## Decisiones del equipo

Formato: `AAAA-MM-DD — decisión — por qué (quién)`.

- 2026-11-19 — Se trabaja con ramas y Pull Requests; nadie pushea directo a `main` — para no pisarnos
  ahora que somos más de uno en el repo (Alexis).
- La base se versiona como un único `.sql`, sin migraciones de CodeIgniter — así estaba desde el inicio
  del proyecto.
- El contenido se borra con borrado lógico (`fecha_borrado`) y se recupera desde la Papelera del panel.
  Solo el admin borra.
