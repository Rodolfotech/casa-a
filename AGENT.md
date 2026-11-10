#CASA A 
Es un espacio boutique multifuncional y hub creativo multidisciplinario. Diseñado como estudio para
realizar campañas fotográficas, producción de video, activaciones de marcas, grabación de podcast, eventos
pequeños. Además CASA A cuenta con un Rental de mobiliario y Props de arte.

SITIO WEB
simple y fácil de navegar. La idea es que el indice este a la vista e identifique
con claridad cada espacio.
Contenido :
Home / pagina de inicio con una portada lo mas probable que sea en imágenes
Casa A / breve introducción explicativa de la marca, lo que ofrecemos.
Estudio / toda la parte tecnica del lugar con planos, fotos del espacio físico
Contacto / links a rrss, ficha con calendario de reserva, valores, teléfono etc.


# un theme
visual

Nombre del theme: casa-a
Estado: archivos creados en wp-content/themes/casa-a/ (pendiente de activar en el admin y crear paginas Home, Casa A, Estudio y Contacto)

## Paleta de color
Paleta de referencia (imagen de referencia, valores medidos de los círculos). Reemplaza la paleta anterior.

| Rol | Nombre | Hex | Variable CSS | Uso |
|---|---|---|---|---|
| Fondo principal | Blanco | #FFFFFF | --casa-a-blanco | Fondo general de página |
| Fondo alterno | Gris claro | #EBEBEB | --casa-a-gris-claro | Buscador, formularios, footer |
| Texto principal | Negro | #000000 | --casa-a-negro | Títulos, cuerpo de texto, hero |
| Texto secundario | Gris | #919191 | --casa-a-gris | Descripciones, captions, bordes |
| Acento | Taupe | #B1A28F | --casa-a-taupe | Botones y llamadas a la acción |
| Acento secundario | Gris taupe | #A49A91 | --casa-a-gris-taupe | Contador de "Mi selección", etiquetas, íconos |

Criterio: paleta neutra y cálida para que las fotos de productos sean el protagonista.
Theme: los valores están en `:root` de style.css y en theme.json (paleta del editor).

#UN PLUGIN 
que permita agregar los productos al catologo
- debe permitir agregar categorias
- eliminar y editar cada catoria
- agregar, eliminar y editar cada producto

Estado: archivos creados en wp-content/plugins/casa-a-catalogo/ (pendiente de activar en el admin). Usa el tipo "producto" y la taxonomia "categoria_producto" de WordPress, con dimensiones y disponibilidad (renta/venta/ambos). Plantillas archive-producto.php y single-producto.php en el theme.


