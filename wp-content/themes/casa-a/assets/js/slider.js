/**
 * Carrusel de la portada: avanza solo cada pocos segundos y el visitante
 * puede cambiar de diapositiva con los puntos, las flechas del teclado o deslizando.
 */
(function () {
	'use strict';

	var INTERVALO = 6000;

	var slider = document.querySelector('.casa-a-slider');

	if (!slider) {
		return;
	}

	var diapositivas = Array.prototype.slice.call(slider.querySelectorAll('.casa-a-slide'));
	var puntos = Array.prototype.slice.call(slider.querySelectorAll('.casa-a-slider-punto'));
	var actual = 0;
	var temporizador = null;
	var reducirMovimiento = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

	function mostrar(indice) {
		actual = (indice + diapositivas.length) % diapositivas.length;

		diapositivas.forEach(function (slide, i) {
			var activa = i === actual;
			slide.classList.toggle('is-active', activa);
			slide.setAttribute('aria-hidden', activa ? 'false' : 'true');

			var enlace = slide.querySelector('.casa-a-slide-boton');
			if (enlace) {
				enlace.tabIndex = activa ? 0 : -1;
			}
		});

		puntos.forEach(function (punto, i) {
			var activo = i === actual;
			punto.classList.toggle('is-active', activo);
			punto.setAttribute('aria-selected', activo ? 'true' : 'false');
			punto.tabIndex = activo ? 0 : -1;
		});
	}

	function detener() {
		window.clearInterval(temporizador);
		temporizador = null;
	}

	function iniciar() {
		if (reducirMovimiento || temporizador) {
			return;
		}

		temporizador = window.setInterval(function () {
			mostrar(actual + 1);
		}, INTERVALO);
	}

	function reiniciar() {
		detener();
		iniciar();
	}

	puntos.forEach(function (punto) {
		punto.addEventListener('click', function () {
			mostrar(parseInt(punto.dataset.slide, 10));
			reiniciar();
		});
	});

	slider.addEventListener('keydown', function (evento) {
		if (evento.key === 'ArrowRight') {
			mostrar(actual + 1);
			reiniciar();
		} else if (evento.key === 'ArrowLeft') {
			mostrar(actual - 1);
			reiniciar();
		}
	});

	// Pausa mientras el puntero o el foco están sobre el carrusel.
	slider.addEventListener('mouseenter', detener);
	slider.addEventListener('mouseleave', iniciar);
	slider.addEventListener('focusin', detener);
	slider.addEventListener('focusout', iniciar);

	// Deslizar con el dedo o el mouse.
	var inicioX = null;

	slider.addEventListener('pointerdown', function (evento) {
		inicioX = evento.clientX;
	});

	slider.addEventListener('pointerup', function (evento) {
		if (inicioX === null) {
			return;
		}

		var distancia = evento.clientX - inicioX;
		inicioX = null;

		if (Math.abs(distancia) > 40) {
			mostrar(distancia < 0 ? actual + 1 : actual - 1);
			reiniciar();
		}
	});

	mostrar(0);
	iniciar();
})();
