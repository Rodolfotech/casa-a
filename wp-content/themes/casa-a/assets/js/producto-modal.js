/**
 * Modal de producto: se abre al hacer clic en la foto de una tarjeta del catálogo,
 * muestra las imágenes del producto y permite agregarlo a "Mi selección".
 */
(function () {
	'use strict';

	var modal = null;
	var fondo = null;
	var producto = null;
	var cantidad = 1;
	var ultimoFoco = null;

	function $(selector) {
		return modal.querySelector(selector);
	}

	function formato(numero) {
		return String(Math.round(numero)).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
	}

	function maximo() {
		return producto && producto.stock !== null ? Math.min(99, producto.stock) : 99;
	}

	function mostrarPrincipal(indice) {
		var imagenes = producto.imagenes;
		var principal = $('[data-modal-principal]');

		principal.src = imagenes[indice].grande;
		principal.alt = producto.nombre;

		$$miniaturas().forEach(function (boton, i) {
			boton.setAttribute('aria-pressed', i === indice ? 'true' : 'false');
		});
	}

	function $$miniaturas() {
		return Array.prototype.slice.call(modal.querySelectorAll('[data-modal-miniatura]'));
	}

	function pintarCantidad() {
		$('[data-modal-cantidad]').value = cantidad;
		$('[data-modal-menos]').disabled = cantidad <= 1;
		$('[data-modal-mas]').disabled = cantidad >= maximo();
	}

	function llenar(datos) {
		producto = datos;
		cantidad = 1;

		$('[data-modal-etiqueta]').textContent = (datos.categorias.join(' · ') || 'Catálogo') + ' · ' + datos.codigo;
		$('[data-modal-titulo]').textContent = datos.nombre;
		$('[data-modal-atributos]').textContent = datos.atributos.join(' · ');
		$('[data-modal-medidas]').textContent = datos.medidas || '—';
		$('[data-modal-precio]').textContent = datos.precio !== null ? '$' + formato(datos.precio) + ' / día' : '—';
		$('[data-modal-stock]').textContent = datos.stock !== null ? datos.stock + ' unidades' : '—';

		var miniaturas = $('[data-modal-miniaturas]');
		miniaturas.textContent = '';

		datos.imagenes.forEach(function (imagen, indice) {
			var boton = document.createElement('button');
			boton.type = 'button';
			boton.className = 'casa-a-modal-miniatura';
			boton.setAttribute('data-modal-miniatura', '');
			boton.setAttribute('aria-label', 'Imagen ' + (indice + 1));

			var img = document.createElement('img');
			img.src = imagen.miniatura || imagen.grande;
			img.alt = '';
			boton.appendChild(img);

			boton.addEventListener('click', function () {
				mostrarPrincipal(indice);
			});

			miniaturas.appendChild(boton);
		});

		miniaturas.hidden = datos.imagenes.length < 2;

		if (datos.imagenes.length) {
			mostrarPrincipal(0);
		}

		var agregar = $('[data-modal-agregar]');
		var disponible = datos.stock === null || datos.stock > 0;
		agregar.disabled = !disponible;
		agregar.textContent = disponible ? 'Agregar a mi selección' : 'Sin stock';

		$('[data-modal-aviso]').hidden = true;
		pintarCantidad();
	}

	function abrir(datos, origen) {
		ultimoFoco = origen;
		llenar(datos);

		modal.hidden = false;
		fondo.hidden = false;
		modal.setAttribute('aria-hidden', 'false');

		requestAnimationFrame(function () {
			modal.classList.add('is-open');
			fondo.classList.add('is-open');
		});

		document.body.classList.add('casa-a-modal-abierto');
		$('.casa-a-modal-cerrar').focus();
	}

	function cerrar() {
		modal.classList.remove('is-open');
		fondo.classList.remove('is-open');
		modal.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('casa-a-modal-abierto');

		window.setTimeout(function () {
			if (!modal.classList.contains('is-open')) {
				modal.hidden = true;
				fondo.hidden = true;
			}
		}, 300);

		if (ultimoFoco) {
			ultimoFoco.focus();
		}
	}

	function agregarAlCarrito() {
		if (!window.casaASeleccion || !producto) {
			return;
		}

		window.casaASeleccion.agregar({
			id: producto.id,
			codigo: producto.codigo,
			nombre: producto.nombre,
			precio: producto.precio || 0,
			imagen: producto.imagenes.length ? producto.imagenes[0].miniatura : '',
			stock: producto.stock
		}, cantidad);

		var aviso = $('[data-modal-aviso]');
		aviso.textContent = cantidad + (cantidad === 1 ? ' unidad agregada' : ' unidades agregadas') + ' a Mi selección.';
		aviso.hidden = false;
	}

	document.addEventListener('click', function (evento) {
		var abrirBoton = evento.target.closest('[data-modal-producto]');

		if (abrirBoton) {
			var datos;

			try {
				datos = JSON.parse(abrirBoton.getAttribute('data-modal-producto'));
			} catch (e) {
				return;
			}

			abrir(datos, abrirBoton);
			return;
		}

		if (!modal || modal.hidden) {
			return;
		}

		if (evento.target.closest('[data-modal-cerrar]')) {
			cerrar();
		} else if (evento.target.closest('[data-modal-menos]')) {
			cantidad = Math.max(1, cantidad - 1);
			pintarCantidad();
		} else if (evento.target.closest('[data-modal-mas]')) {
			cantidad = Math.min(maximo(), cantidad + 1);
			pintarCantidad();
		} else if (evento.target.closest('[data-modal-agregar]')) {
			agregarAlCarrito();
		}
	});

	document.addEventListener('keydown', function (evento) {
		if (evento.key === 'Escape' && modal && modal.classList.contains('is-open')) {
			cerrar();
		}
	});

	// Cantidad escrita: solo dígitos; al salir del campo se ajusta entre 1 y el máximo.
	document.addEventListener('input', function (evento) {
		if (evento.target.matches('[data-modal-cantidad]')) {
			evento.target.value = evento.target.value.replace(/\D/g, '');
		}
	});

	document.addEventListener('change', function (evento) {
		if (evento.target.matches('[data-modal-cantidad]')) {
			var valor = parseInt(evento.target.value, 10);
			cantidad = isNaN(valor) ? 1 : Math.min(maximo(), Math.max(1, valor));
			pintarCantidad();
		}
	});

	function iniciar() {
		modal = document.querySelector('.casa-a-modal');
		fondo = document.querySelector('.casa-a-modal-fondo');
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', iniciar);
	} else {
		iniciar();
	}
})();
