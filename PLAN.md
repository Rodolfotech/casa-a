# Plan de trabajo: sitio CASA A (según AGENT.md)

## Context
`AGENT.md` define el sitio web de CASA A (estudio boutique, hub creativo, rental de mobiliario y props). Hoy WordPress 7.1.3 corre en local (`/var/www/html/wordpress/proyecto4`, BD `proyecto4`, URL `http://localhost/wordpress/proyecto4/`) solo con temas de fábrica y plugins Akismet/Hello Dolly. No existe theme ni plugin propio, por eso "no se ve nada". Objetivo: theme visual `casa-a` con la paleta ya definida + plugin de catálogo (categorías y productos con CRUD).

Estado ya hecho: paleta y nombre del theme documentados en `AGENT.md`. `SPEC.md` y `CLAUDE.md` están vacíos.

## Fases

### Fase 1 — Theme `casa-a` (visible primero)
- Crear `wp-content/themes/casa-a/` con `style.css` (cabecera del theme), `theme.json` (paleta de AGENT.md como colores y tipografías), `functions.php`, `index.php`, `header.php`, `footer.php`, `front-page.php`, `page.php`.
- Paleta como variables CSS / `theme.json`: #F7F4EF, #E9E2D6, #1E1D1B, #B5573A, #8A9A84, #6F6A63, #141311.
- Menú principal visible en el header con las 4 secciones (registrar con `register_nav_menus`).
- Responsive y mobile-first; navegación simple.

### Fase 2 — Páginas y contenido base
- Crear páginas: Home, Casa A, Estudio, Contacto (vía WP-CLI si existe, si no desde el admin) y asignar Home como página de inicio.
- Home: portada con imágenes (hero + galería).
- Casa A: introducción de la marca y oferta (texto de AGENT.md).
- Estudio: plantilla `page-estudio.php` con bloques para planos, fotos y datos técnicos.
- Contacto: plantilla `page-contacto.php` con links a RRSS, valores, teléfono y ficha de reserva.
- Asignar menú a la ubicación del header.

### Fase 3 — Activación y verificación del theme
- Activar con `wp theme activate casa-a` (o *Apariencia → Temas*).
- Revisar las 4 páginas en navegador, escritorio y móvil.

### Fase 4 — Plugin de catálogo `casa-a-catalogo`
- Crear `wp-content/plugins/casa-a-catalogo/casa-a-catalogo.php`.
- Registrar CPT `producto` y taxonomía `categoria_producto` (jerárquica), ambos con `show_in_rest` y menú en el admin.
- Con eso WordPress ya da agregar, editar y eliminar categorías y productos (requisito de AGENT.md) sin UI propia.
- Metadatos del producto: imagen destacada, descripción, y campos opcionales (dimensiones, disponibilidad para rental).
- Plantillas `archive-producto.php` / `single-producto.php` en el theme, con estilos de la paleta.
- Activar el plugin.

### Fase 5 — Reservas (pendiente de decisión)
- AGENT.md pide "ficha con calendario de reserva" en Contacto. Decidir: embed de calendario externo (Google Calendar/Calendly) o plugin de reservas aparte. Por defecto: embed externo, para no inflar el plugin de catálogo.

### Fase 6 — Documentación
- Actualizar `AGENT.md` marcando el estado de cada fase.
- Volcar el brief técnico en `SPEC.md` si el usuario lo confirma.

## Archivos críticos
- `AGENT.md` (fuente de requisitos; se actualiza al final)
- `wp-content/themes/casa-a/*` (nuevo)
- `wp-content/plugins/casa-a-catalogo/casa-a-catalogo.php` (nuevo)
- Referencia de estructura de theme: `wp-content/themes/twentytwentyfive/` (theme.json y estilos)

## Verificación
- `wp theme list` / `wp plugin list` muestran `casa-a` y `casa-a-catalogo` activos (o revisar en el admin).
- Abrir `http://localhost/wordpress/proyecto4/` y confirmar que el menú con las 4 secciones es visible y los colores corresponden a la paleta.
- Crear una categoría y un producto desde el admin, editarlos y eliminarlos; confirmar que aparecen en `/producto/` y en la página individual.
- Probar en ancho móvil (~375 px) sin scroll horizontal.
- `php -l` sobre cada archivo PHP nuevo para detectar errores de sintaxis.

## Decisiones tomadas por defecto (redirigibles)
- Theme clásico PHP con `theme.json`, no block theme completo.
- Plugin usa CPT + taxonomía nativos en lugar de UI de administración propia.
- Reservas con embed externo.
