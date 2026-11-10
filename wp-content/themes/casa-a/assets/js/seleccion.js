/**
 * Mi selección: guarda los productos en el navegador del visitante, dibuja el panel
 * lateral (productos, proyectos y cotización) y mantiene el contador del nav.
 *
 * Botones del catálogo: .casa-a-agregar con data-id, data-codigo, data-nombre,
 * data-precio, data-imagen y data-disponible ("1" o "0").
 */
(function () {
	'use strict';

	var CLAVE = 'casa_a_seleccion';
	var MAX_CANTIDAD = 99;

	var drawer = null;
	var fondo = null;

	function $(selector) {
		return document.querySelector(selector);
	}

	function $$(selector) {
		return Array.prototype.slice.call(document.querySelectorAll(selector));
	}

	function leer() {
		try {
			var datos = JSON.parse(window.localStorage.getItem(CLAVE));
			return Array.isArray(datos) ? datos.filter(function (p) { return p && p.id && p.nombre; }) : [];
		} catch (e) {
			return [];
		}
	}

	function guardar(lista) {
		try {
			window.localStorage.setItem(CLAVE, JSON.stringify(lista));
		} catch (e) {
			// Sin almacenamiento disponible, la selección dura solo en esta página.
		}
	}

	function formato(numero) {
		return String(Math.round(numero)).replace(/\B(?=(\d{3})+(?!\d))/g, '.');
	}

	/**
	 * Máximo de unidades que se pueden llevar de un producto: su stock, sin pasar de 99.
	 * Si el producto no tiene stock cargado, se usa el límite general.
	 */
	function maximoPara(producto) {
		var stock = parseInt(producto.stock, 10);
		return isNaN(stock) ? MAX_CANTIDAD : Math.min(MAX_CANTIDAD, stock);
	}

	function total(lista) {
		return lista.reduce(function (suma, p) {
			return suma + (parseInt(p.precio, 10) || 0) * (p.cantidad || 1);
		}, 0);
	}

	function estaEnSeleccion(lista, id) {
		return lista.some(function (p) { return String(p.id) === String(id); });
	}

	function rutValido(rut) {
		var limpio = String(rut).replace(/[^0-9kK]/g, '').toUpperCase();

		if (limpio.length < 2) {
			return false;
		}

		var cuerpo = limpio.slice(0, -1);
		var dv = limpio.slice(-1);
		var suma = 0;
		var multiplo = 2;

		for (var i = cuerpo.length - 1; i >= 0; i--) {
			suma += parseInt(cuerpo.charAt(i), 10) * multiplo;
			multiplo = multiplo === 7 ? 2 : multiplo + 1;
		}

		var resto = 11 - (suma % 11);
		var esperado = resto === 11 ? '0' : (resto === 10 ? 'K' : String(resto));

		return dv === esperado;
	}

	function activarTab(nombre) {
		$$('[role="tab"]').forEach(function (tab) {
			var activa = tab.dataset.tab === nombre;
			tab.setAttribute('aria-selected', activa ? 'true' : 'false');
			tab.tabIndex = activa ? 0 : -1;
		});

		$$('[data-panel]').forEach(function (panel) {
			panel.hidden = panel.dataset.panel !== nombre;
		});

		if (nombre === 'cotizar') {
			sincronizarItems();
		}
	}

	function abrir(tab) {
		drawer.hidden = false;
		fondo.hidden = false;
		activarTab(tab || 'productos');

		requestAnimationFrame(function () {
			drawer.classList.add('is-open');
			fondo.classList.add('is-open');
		});

		drawer.setAttribute('aria-hidden', 'false');
		document.body.classList.add('casa-a-drawer-abierto');
		drawer.querySelector('[role="tab"][aria-selected="true"]').focus();
	}

	function cerrar() {
		drawer.classList.remove('is-open');
		fondo.classList.remove('is-open');
		drawer.setAttribute('aria-hidden', 'true');
		document.body.classList.remove('casa-a-drawer-abierto');

		window.setTimeout(function () {
			if (!drawer.classList.contains('is-open')) {
				drawer.hidden = true;
				fondo.hidden = true;
			}
		}, 300);
	}

	function filaLista(producto) {
		var li = document.createElement('li');
		li.className = 'casa-a-drawer-item';

		if (producto.imagen) {
			var img = document.createElement('img');
			img.src = producto.imagen;
			img.alt = '';
			img.width = 72;
			img.height = 72;
			li.appendChild(img);
		} else {
			var vacia = document.createElement('span');
			vacia.className = 'casa-a-drawer-imagen-vacia';
			li.appendChild(vacia);
		}

		var info = document.createElement('div');
		info.className = 'casa-a-drawer-info';

		var nombre = document.createElement('p');
		nombre.className = 'casa-a-drawer-nombre';
		nombre.textContent = producto.nombre;
		info.appendChild(nombre);

		var meta = document.createElement('p');
		meta.className = 'casa-a-drawer-meta';
		meta.textContent = (producto.codigo ? producto.codigo + ' · ' : '') + '$' + formato(parseInt(producto.precio, 10) || 0) + ' / día';
		info.appendChild(meta);

		var controles = document.createElement('div');
		controles.className = 'casa-a-cantidad';

		var menos = document.createElement('button');
		menos.type = 'button';
		menos.dataset.accion = 'menos';
		menos.dataset.id = producto.id;
		menos.setAttribute('aria-label', 'Quitar una unidad');
		menos.textContent = '−';

		var maximo = maximoPara(producto);
		var cantidad = document.createElement('input');
		cantidad.type = 'text';
		cantidad.inputMode = 'numeric';
		cantidad.pattern = '[0-9]*';
		cantidad.maxLength = 2;
		cantidad.className = 'casa-a-cantidad-input';
		cantidad.value = producto.cantidad || 1;
		cantidad.dataset.cantidadId = producto.id;
		cantidad.setAttribute('aria-label', 'Cantidad de ' + producto.nombre);
		cantidad.max = maximo;

		var mas = document.createElement('button');
		mas.type = 'button';
		mas.dataset.accion = 'mas';
		mas.dataset.id = producto.id;
		mas.setAttribute('aria-label', 'Agregar una unidad');
		mas.textContent = '+';

		controles.appendChild(menos);
		controles.appendChild(cantidad);
		controles.appendChild(mas);
		info.appendChild(controles);
		li.appendChild(info);

		var quitar = document.createElement('button');
		quitar.type = 'button';
		quitar.className = 'casa-a-drawer-quitar';
		quitar.dataset.accion = 'quitar';
		quitar.dataset.id = producto.id;
		quitar.setAttribute('aria-label', 'Quitar ' + producto.nombre);
		quitar.innerHTML = '<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true"><path d="M4 7h16M9 7V4h6v3M6 7l1 13h10l1-13"/></svg>';
		li.appendChild(quitar);

		return li;
	}

	function sincronizarItems() {
		var campo = $('[data-cotizacion-items]');

		if (!campo) {
			return;
		}

		campo.value = JSON.stringify(leer().map(function (p) {
			return { id: p.id, cantidad: p.cantidad || 1 };
		}));
	}

	function pintar() {
		var lista = leer();
		var n = lista.length;

		$$('.casa-a-seleccion-contador').forEach(function (el) {
			el.textContent = n;
		});

		$$('.casa-a-seleccion').forEach(function (el) {
			el.setAttribute('aria-label', 'Mi selección: ' + n);
		});

		$('[data-seleccion-resumen]').textContent = n + (n === 1 ? ' producto' : ' productos') + ' · $' + formato(total(lista)) + ' / día';

		var ul = $('[data-seleccion-lista]');
		ul.textContent = '';
		lista.forEach(function (p) {
			ul.appendChild(filaLista(p));
		});

		$('[data-seleccion-vacio]').hidden = n > 0;
		$('[data-seleccion-continuar]').disabled = n === 0;

		$$('.casa-a-agregar').forEach(function (boton) {
			var agregado = estaEnSeleccion(lista, boton.dataset.id);
			boton.classList.toggle('is-added', agregado);
			boton.disabled = agregado || boton.dataset.disponible !== '1';
			boton.textContent = agregado ? 'Agregado' : 'Agregar';
		});

		sincronizarItems();
	}

	function agregar(boton) {
		var lista = leer();

		if (!estaEnSeleccion(lista, boton.dataset.id)) {
			lista.push({
				id: boton.dataset.id,
				codigo: boton.dataset.codigo,
				nombre: boton.dataset.nombre,
				precio: boton.dataset.precio,
				imagen: boton.dataset.imagen,
				stock: boton.dataset.stock,
				cantidad: 1
			});
			guardar(lista);
		}

		pintar();
	}

	function cambiarCantidad(id, accion) {
		var lista = leer();

		lista = lista.reduce(function (nueva, p) {
			if (String(p.id) !== String(id)) {
				nueva.push(p);
				return nueva;
			}

			if (accion === 'quitar') {
				return nueva;
			}

			var cantidad = p.cantidad || 1;
			cantidad = accion === 'mas' ? Math.min(maximoPara(p), cantidad + 1) : Math.max(1, cantidad - 1);
			p.cantidad = cantidad;
			nueva.push(p);
			return nueva;
		}, []);

		guardar(lista);
		pintar();
	}

	function mostrarAviso(texto, tipo) {
		var previo = $('[data-cotizacion-aviso-js]');

		if (previo) {
			previo.remove();
		}

		var aviso = document.createElement('p');
		aviso.className = 'casa-a-aviso ' + (tipo === 'ok' ? 'casa-a-aviso-ok' : 'casa-a-aviso-error');
		aviso.setAttribute('role', 'status');
		aviso.setAttribute('data-cotizacion-aviso-js', '');
		aviso.textContent = texto;
		$('[data-panel="cotizar"]').insertBefore(aviso, $('[data-cotizacion-form]'));
	}

	function enviarCotizacion(evento) {
		var formulario = evento.target;
		var lista = leer();

		if (lista.length === 0) {
			evento.preventDefault();
			mostrarAviso('Agrega al menos un producto antes de solicitar la cotización.', 'error');
			return;
		}

		if (!rutValido(formulario.rut.value)) {
			evento.preventDefault();
			mostrarAviso('El RUT no es válido. Revisa el dígito verificador.', 'error');
			return;
		}

		if (formulario.fecha_devolucion.value < formulario.fecha_retiro.value) {
			evento.preventDefault();
			mostrarAviso('La fecha de devolución no puede ser anterior a la de retiro.', 'error');
			return;
		}

		sincronizarItems();
	}

	function revisarRespuesta() {
		var parametros = new URLSearchParams(window.location.search);
		var estado = parametros.get('casa_a_cotizacion');

		if (!estado) {
			return;
		}

		if (estado === 'ok') {
			guardar([]);
			pintar();
			abrir('cotizar');
			mostrarAviso('Gracias. Recibimos tu solicitud de cotización y te responderemos pronto.', 'ok');
		} else {
			abrir('cotizar');
		}

		parametros.delete('casa_a_cotizacion');
		var query = parametros.toString();
		window.history.replaceState({}, '', window.location.pathname + (query ? '?' + query : '') + window.location.hash);
	}

	document.addEventListener('click', function (evento) {
		var objetivo = evento.target;

		var agregar_btn = objetivo.closest('.casa-a-agregar');
		if (agregar_btn && !agregar_btn.disabled) {
			agregar(agregar_btn);
			return;
		}

		var nav_link = objetivo.closest('.casa-a-seleccion');
		if (nav_link) {
			evento.preventDefault();
			abrir('productos');
			return;
		}

		if (objetivo.closest('[data-seleccion-cerrar]')) {
			cerrar();
			return;
		}

		var tab = objetivo.closest('[role="tab"]');
		if (tab) {
			activarTab(tab.dataset.tab);
			return;
		}

		if (objetivo.closest('[data-seleccion-continuar]')) {
			activarTab('cotizar');
			return;
		}

		var accion = objetivo.closest('[data-accion]');
		if (accion) {
			cambiarCantidad(accion.dataset.id, accion.dataset.accion);
		}
	});

	document.addEventListener('keydown', function (evento) {
		if (evento.key === 'Escape' && drawer && drawer.classList.contains('is-open')) {
			cerrar();
		}
	});

	// Cantidad escrita en la lista: solo dígitos; al salir del campo se ajusta entre 1 y 99.
	document.addEventListener('input', function (evento) {
		if (evento.target.matches('[data-cantidad-id]')) {
			evento.target.value = evento.target.value.replace(/\D/g, '');
		}
	});

	document.addEventListener('change', function (evento) {
		var campo = evento.target;

		if (!campo.matches('[data-cantidad-id]')) {
			return;
		}

		var valor = parseInt(campo.value, 10);
		var lista = leer();

		lista.forEach(function (p) {
			if (String(p.id) === String(campo.dataset.cantidadId)) {
				p.cantidad = isNaN(valor) ? 1 : Math.min(maximoPara(p), Math.max(1, valor));
			}
		});

		guardar(lista);
		pintar();
	});

	document.addEventListener('submit', function (evento) {
		if (evento.target.matches('[data-cotizacion-form]')) {
			enviarCotizacion(evento);
		}
	});

	function iniciar() {
		drawer = $('.casa-a-drawer');
		fondo = $('.casa-a-drawer-fondo');

		if (!drawer || !fondo) {
			return;
		}

		pintar();
		revisarRespuesta();
	}

	/**
	 * Agrega un producto con la cantidad indicada (lo usa el modal de producto).
	 *
	 * @param {object} datos    id, codigo, nombre, precio e imagen del producto.
	 * @param {number} cantidad Unidades a agregar.
	 */
	function agregarProducto(datos, cantidad) {
		var lista = leer();
		var existente = lista.filter(function (p) { return String(p.id) === String(datos.id); })[0];

		if (existente) {
			existente.cantidad = Math.min(maximoPara(existente), (existente.cantidad || 1) + cantidad);
		} else {
			var nuevo = {
				id: String(datos.id),
				codigo: datos.codigo,
				nombre: datos.nombre,
				precio: String(datos.precio || 0),
				imagen: datos.imagen || '',
				stock: datos.stock,
				cantidad: 1
			};
			nuevo.cantidad = Math.min(maximoPara(nuevo), cantidad);
			lista.push(nuevo);
		}

		guardar(lista);
		pintar();
	}

	window.casaASeleccion = { agregar: agregarProducto };

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', iniciar);
	} else {
		iniciar();
	}
})();
