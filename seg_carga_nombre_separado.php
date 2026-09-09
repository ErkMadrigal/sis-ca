<?php
	session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>CAROBRA Consultores Especializados</title>
		<link rel="shortcut icon" href="./img/favicon.ico">
		<script type="text/javascript" src="./js/carobra.js"></script>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">

		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/>
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

		<style>
			#contenido, #contenido * { text-align: left; }

			#loader {
				display:none; position:fixed; top:0; left:0; width:100%; height:100%;
				background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;
			}

			.rzc-card {
				background:#fff; border:1px solid #e0e8ec; border-radius:10px;
				padding:18px 20px; margin-bottom:18px;
			}

			.rzc-titulo { font-size:15px; font-weight:700; color:#14507a; margin-bottom:6px; }
			.rzc-subtitulo { font-size:12.5px; color:#888; margin-bottom:14px; }

			.rzc-drop-zone {
				border:2px dashed #b0d0e0; border-radius:10px; padding:34px 16px;
				text-align:center; cursor:pointer; transition:border-color .15s, background .15s;
				background:#f7fbfd;
			}
			.rzc-drop-zone:hover { border-color:#2B73B6; background:#eef6fb; }

			.rzc-drop-icono { font-size:32px; margin-bottom:8px; }
			.rzc-drop-texto { font-size:13px; color:#555; }
			.rzc-drop-archivo { font-size:12.5px; color:#2B73B6; font-weight:600; margin-top:8px; }

			.rzc-resumen { display:flex; flex-wrap:wrap; gap:12px; margin:18px 0; }

			.rzc-resumen-item {
				flex:1; min-width:130px; border-radius:10px; padding:14px 16px;
				border:1px solid; text-align:center;
			}

			.rzc-resumen-num { font-size:24px; font-weight:700; line-height:1; }
			.rzc-resumen-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-top:4px; }

			.rzc-resumen-nuevo       { background:#eafaf1; border-color:#b8e8cd; }
			.rzc-resumen-nuevo       .rzc-resumen-num, .rzc-resumen-nuevo .rzc-resumen-label { color:#1e8e58; }
			.rzc-resumen-actualizado { background:#e8f0fb; border-color:#b9d3f0; }
			.rzc-resumen-actualizado .rzc-resumen-num, .rzc-resumen-actualizado .rzc-resumen-label { color:#1a5276; }
			.rzc-resumen-duplicado   { background:#fff8e6; border-color:#f5d97a; }
			.rzc-resumen-duplicado   .rzc-resumen-num, .rzc-resumen-duplicado .rzc-resumen-label { color:#a06b00; }
			.rzc-resumen-error       { background:#fdeeee; border-color:#f3c4c4; }
			.rzc-resumen-error       .rzc-resumen-num, .rzc-resumen-error .rzc-resumen-label { color:#c0392b; }

			.rzc-tabla-wrap { max-height:340px; overflow-y:auto; border:1px solid #e0e8ec; border-radius:8px; }
			.rzc-tabla { width:100%; border-collapse:collapse; font-size:12.5px; }

			.rzc-tabla thead th {
				background:#f0f6f9; color:#2B73B6; font-weight:700; font-size:11px;
				text-transform:uppercase; letter-spacing:.04em; padding:9px 10px; text-align:left;
				position:sticky; top:0; z-index:1;
			}

			.rzc-tabla tbody td { padding:8px 10px; border-bottom:1px solid #eef2f4; color:#333; }
			.rzc-tabla tbody tr:hover { background:#f7fbfc; }

			.btn-rzc-guardar {
				background:#2B73B6; color:#fff; border:none; padding:9px 22px; border-radius:6px;
				font-size:13px; font-weight:600; cursor:pointer; transition:background .2s;
			}
			.btn-rzc-guardar:hover:not(:disabled) { background:#145670; }
			.btn-rzc-guardar:disabled { opacity:.45; cursor:not-allowed; }

			.btn-rzc-limpiar {
				background:#fff; color:#555; border:1px solid #bbb; padding:9px 18px; border-radius:6px;
				font-size:13px; cursor:pointer; transition:background .2s;
			}
			.btn-rzc-limpiar:hover { background:#f5f5f5; }

			.btn-rzc-peligro {
				background:#fff; color:#c0392b; border:1px solid #e6a5a0; padding:9px 18px; border-radius:6px;
				font-size:13px; font-weight:600; cursor:pointer; transition:background .2s;
			}
			.btn-rzc-peligro:hover { background:#fdeeee; }

			.rzc-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:16px; flex-wrap:wrap; }

			.rzc-vacio { text-align:center; padding:30px; color:#aaa; font-size:13px; }

			/* Barra de progreso de la carga en lotes */
			.rzc-progreso-wrap {
				background:#eef2f4; border-radius:8px; height:20px; overflow:hidden; margin:14px 0 6px;
			}
			.rzc-progreso-bar {
				background:linear-gradient(135deg,#2384c5,#0072bc); height:100%; width:0%;
				transition:width .25s ease; display:flex; align-items:center; justify-content:center;
				color:#fff; font-size:10.5px; font-weight:700;
			}
			.rzc-progreso-texto { font-size:12.5px; color:#555; text-align:center; }
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>

		<div id="loader">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div>Procesando...</div>
			</div>
		</div>

	<?php

		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo = (strtotime($fa)-strtotime($fg));

		if($periodo > 600){
			session_destroy();
			session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"] = "p";
		}

		if ($_SESSION["autentifica"] != "p"){
			session_destroy();
			session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {

			$divpie       = new div("pie");
			$divopcion    = new div("opcion");
			$divcabecera  = new div("cabecera");
			$divcontenido = new div("contenido");
			$divcontenedor= new div("contenedor");
			$imgpie       = new img("imgpie","./img/pie.png");
			$divusuario   = new div("usuario",'',"absolute","97%");
			$imgcabecera  = new img("imgcabecera","./img/cabecera.png");

			$divcabecera->crear();
				$imgcabecera->crear();
				$imgcabecera->css("width","100%");
				titulo("Carga masiva de nombre separado");
				$pag="PRODUCCION";
				menu();
			$divcabecera->close();

			$divpie->crear();
				$imgpie->crear();
				$imgpie->css("width","100%");
				$divusuario->crear();
					echo "<br><br>";
					$divusuario->css("top","10px");
					$divusuario->css("text-align","left");
	?>
				<div id="vinculos" style="margin-top:10px; margin-left:25px;">
					<?php include("vinculos.php"); ?>
				</div>
	<?php
					echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: " . $_SESSION["nombre"] . "</spam>";
					echo "<div style='width:100%; text-align:right; margin-top:-25px;'>";
						echo "<spam id='email'>Email</spam>";
						echo "&nbsp;&nbsp;&nbsp;<spam id='salir'>Salir</spam>";
					echo "</div>";
				$divusuario->close();
				$divpie->close();
				$divcontenedor->crear();
				$divopcion->crear();
					$divopcion->css("background","#FFFFFF");
				$divopcion->close();
				$divcontenido->crear();
	?>

	<div class="container-fluid" style="padding:16px 18px; text-align:left;">

		<!-- Carga de archivo -->
		<div class="rzc-card">
			<div class="rzc-titulo">📤 Carga masiva de nombre separado (usuarios_nombre_separado)</div>
			<div class="rzc-subtitulo">
				Sube el xlsx con las columnas <strong>Clave</strong>, <strong>Nombre</strong>, <strong>Apellido paterno</strong> y
				<strong>Apellido materno</strong>. Es una carga temporal mientras se corrige de fondo: <code>carobra.usuarios</code> no se toca,
				solo se llena <code>carobra.usuarios_nombre_separado</code>: si la <strong>clave ya existe</strong> esa fila se omite (no se
				sobreescribe), y si no existe se inserta. Pensado para archivos grandes (18 mil+ filas): se manda en lotes, no todo de un
				jalón, para que no truene el navegador ni el servidor.
			</div>

			<div class="rzc-drop-zone" id="rzc-drop-zone">
				<input type="file" id="rzc-file-input" accept=".xlsx,.xls" style="display:none;" onchange="onArchivoSeleccionado(this)">
				<div class="rzc-drop-icono">📄</div>
				<div class="rzc-drop-texto">Haz clic para elegir el archivo xlsx</div>
				<div class="rzc-drop-archivo" id="rzc-nombre-archivo"></div>
			</div>

			<div style="display:flex; gap:10px; margin-top:14px;">
				<button type="button" class="btn-rzc-limpiar" onclick="document.getElementById('rzc-file-input').click()">📁 Cargar archivo</button>
				<button type="button" class="btn-rzc-guardar" id="rzc-btn-procesar" onclick="procesarArchivoSeleccionado()" disabled>⚙️ Leer archivo</button>
			</div>
		</div>

		<!-- Vista previa (muestra, no las 18 mil filas completas) -->
		<div class="rzc-card" id="rzc-preview-wrap" style="display:none;">
			<div class="rzc-titulo">Vista previa</div>
			<div class="rzc-subtitulo">
				Por rendimiento solo se muestran las primeras <strong id="rzc-preview-limite">30</strong> filas como muestra;
				la carga completa sí procesa todas las filas válidas.
			</div>

			<div class="rzc-resumen">
				<div class="rzc-resumen-item rzc-resumen-nuevo">
					<div class="rzc-resumen-num" id="rzc-total-leidas">0</div>
					<div class="rzc-resumen-label">Filas leídas</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-actualizado">
					<div class="rzc-resumen-num" id="rzc-total-validas">0</div>
					<div class="rzc-resumen-label">Con clave válida</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-duplicado">
					<div class="rzc-resumen-num" id="rzc-total-duplicadas">0</div>
					<div class="rzc-resumen-label">Claves repetidas en archivo</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-error">
					<div class="rzc-resumen-num" id="rzc-total-sin-clave">0</div>
					<div class="rzc-resumen-label">Sin clave (se omiten)</div>
				</div>
			</div>

			<div class="rzc-tabla-wrap">
				<table class="rzc-tabla">
					<thead>
						<tr>
							<th>Clave</th>
							<th>Nombre(s)</th>
							<th>Apellido paterno</th>
							<th>Apellido materno</th>
						</tr>
					</thead>
					<tbody id="rzc-preview-tbody">
						<tr><td colspan="4" class="rzc-vacio">Sube un archivo para ver la vista previa.</td></tr>
					</tbody>
				</table>
			</div>

			<!-- Progreso de la carga en lotes -->
			<div id="rzc-progreso-wrap-ext" style="display:none; margin-top:16px;">
				<div class="rzc-progreso-wrap">
					<div class="rzc-progreso-bar" id="rzc-progreso-bar">0%</div>
				</div>
				<div class="rzc-progreso-texto" id="rzc-progreso-texto">Preparando...</div>
			</div>

			<!-- Resultado final -->
			<div class="rzc-resumen" id="rzc-resultado-wrap" style="display:none;">
				<div class="rzc-resumen-item rzc-resumen-nuevo">
					<div class="rzc-resumen-num" id="rzc-res-insertadas">0</div>
					<div class="rzc-resumen-label">Insertadas (nuevas)</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-duplicado">
					<div class="rzc-resumen-num" id="rzc-res-omitidas">0</div>
					<div class="rzc-resumen-label">Omitidas (clave ya existía)</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-error">
					<div class="rzc-resumen-num" id="rzc-res-fallidas">0</div>
					<div class="rzc-resumen-label">Filas en lotes fallidos</div>
				</div>
			</div>

			<div class="rzc-footer">
				<button type="button" class="btn-rzc-limpiar" onclick="limpiarCarga()">Cancelar / limpiar</button>
				<button type="button" class="btn-rzc-peligro" id="rzc-btn-detener" onclick="detenerCarga()" style="display:none;">⏸ Detener carga</button>
				<button type="button" class="btn-rzc-limpiar" id="rzc-btn-reintentar" onclick="reintentarFallidas()" style="display:none;">🔁 Reintentar filas fallidas</button>
				<button type="button" class="btn-rzc-guardar" id="rzc-btn-aplicar" onclick="confirmarAplicarCargaMasiva()" disabled>🚀 Aplicar carga masiva</button>
			</div>
		</div>

	</div>

	<?php
		$divcontenido->close();
		$divcontenedor->close();
		$divpie->close();
	?>

	<script language='javascript'>
		var hc = calcula_h(125);
		var h  = $(window).outerHeight() - hc;
		$('#pie').css("top", h+5);
		hc = calcula_h(85);
		h  = $(window).outerHeight() - (hc*2);
		$('#contenedor').css("top", hc-15);
		$('#contenedor').css("height", h);
		hc = calcula_h(45);
		$('#menu').css("top", hc-20);
		$('#opcion').load('menu.php?modulo=<?php echo $pag?>');
	</script>

	<?php } ?>

	</body>

	<script type="text/javascript">

	const API = 'seg_carga_nombre_separado_api.php';

	/* Filas por lote. 500 es un buen punto medio: pocas peticiones
	   (18,000 / 500 ≈ 36) pero cada INSERT multi-fila sigue siendo
	   chico y rápido. Si algún día el servidor se ve muy lento,
	   basta con bajar este número. */
	const TAMANO_LOTE = 500;
	const PREVIEW_LIMITE = 30;

	async function api(payload) {
		const resp = await fetch(API, {
			method : 'POST',
			headers: { 'Content-Type': 'application/json' },
			body   : JSON.stringify(payload)
		});
		return resp.json();
	}

	function mostrarLoader() { document.getElementById('loader').style.display = 'flex'; }
	function ocultarLoader() { document.getElementById('loader').style.display = 'none'; }

	document.getElementById('rzc-drop-zone').addEventListener('click', () => {
		document.getElementById('rzc-file-input').click();
	});

	let archivoSeleccionado = null;   /* File elegido, pendiente de leer */
	let filasParaAplicar   = [];      /* {clave, nombres, apellido_paterno, apellido_materno}, ya validadas (con clave) */
	let filasFallidas      = [];      /* filas de lotes que fallaron incluso tras reintentar */
	let cargaEnProgreso    = false;
	let detenerSolicitado  = false;

	/* Encuentra, sin importar mayúsculas/acentos/espacios extra, la
	   columna del xlsx que corresponde a cada dato, para no depender
	   de que el encabezado venga exactamente idéntico letra por letra. */
	function encontrarColumna(headers, palabrasClave) {
		const norm = s => (s || '').toString().toLowerCase()
			.normalize('NFD').replace(/[̀-ͯ]/g, '')
			.replace(/\s+/g, ' ').trim();

		for (const h of headers) {
			const hn = norm(h);
			if (palabrasClave.every(p => hn.includes(p))) return h;
		}
		return null;
	}

	function onArchivoSeleccionado(input) {
		const file = input.files[0];
		if (!file) return;

		archivoSeleccionado = file;
		document.getElementById('rzc-nombre-archivo').textContent = '📎 ' + file.name;
		document.getElementById('rzc-btn-procesar').disabled = false;

		document.getElementById('rzc-preview-wrap').style.display = 'none';
		resetearEstadoCarga();
	}

	function resetearEstadoCarga() {
		filasFallidas = [];
		document.getElementById('rzc-progreso-wrap-ext').style.display = 'none';
		document.getElementById('rzc-resultado-wrap').style.display = 'none';
		document.getElementById('rzc-btn-detener').style.display = 'none';
		document.getElementById('rzc-btn-reintentar').style.display = 'none';
		document.getElementById('rzc-btn-aplicar').textContent = '🚀 Aplicar carga masiva';
		document.getElementById('rzc-btn-aplicar').disabled = true;
	}

	async function procesarArchivoSeleccionado() {
		if (!archivoSeleccionado) {
			Swal.fire('Elige un archivo', 'Primero carga el xlsx con el botón "Cargar archivo".', 'warning');
			return;
		}

		mostrarLoader();
		try {
			const arrayBuffer = await archivoSeleccionado.arrayBuffer();
			const wb = XLSX.read(arrayBuffer, { type: 'array' });
			const primeraHoja = wb.Sheets[wb.SheetNames[0]];
			const filas = XLSX.utils.sheet_to_json(primeraHoja, { defval: '' });

			if (!filas.length) {
				ocultarLoader();
				Swal.fire('Archivo vacío', 'El xlsx no tiene filas de datos.', 'warning');
				return;
			}

			const headers = Object.keys(filas[0]);
			const colClave    = encontrarColumna(headers, ['clave']);
			const colNombre   = encontrarColumna(headers, ['nombre']);
			const colApPat    = encontrarColumna(headers, ['apellido', 'paterno']);
			const colApMat    = encontrarColumna(headers, ['apellido', 'materno']);

			if (!colClave || !colNombre || !colApPat || !colApMat) {
				ocultarLoader();
				Swal.fire('Columnas no encontradas', 'No pude identificar Clave, Nombre, Apellido paterno y/o Apellido materno en el xlsx. Encabezados encontrados: ' + headers.join(', '), 'error');
				return;
			}

			const filasCrudas = filas.map(f => ({
				clave            : (f[colClave]  ?? '').toString().trim().toUpperCase(),
				nombres          : (f[colNombre] ?? '').toString().trim().toUpperCase(),
				apellido_paterno : (f[colApPat]  ?? '').toString().trim().toUpperCase(),
				apellido_materno : (f[colApMat]  ?? '').toString().trim().toUpperCase(),
			}));

			const totalLeidas = filasCrudas.length;
			const sinClave    = filasCrudas.filter(f => f.clave === '').length;

			/* Dedup por clave dentro del archivo: se queda la ÚLTIMA
			   ocurrencia (igual criterio que usa el backend dentro de
			   un mismo lote, con ON DUPLICATE KEY UPDATE). */
			const porClave = new Map();
			let duplicadas = 0;
			for (const f of filasCrudas) {
				if (f.clave === '') continue;
				if (porClave.has(f.clave)) duplicadas++;
				porClave.set(f.clave, f);
			}

			filasParaAplicar = Array.from(porClave.values());

			if (!filasParaAplicar.length) {
				ocultarLoader();
				Swal.fire('Sin filas útiles', 'Ninguna fila tiene clave.', 'warning');
				return;
			}

			document.getElementById('rzc-total-leidas').textContent     = totalLeidas.toLocaleString('es-MX');
			document.getElementById('rzc-total-validas').textContent    = filasParaAplicar.length.toLocaleString('es-MX');
			document.getElementById('rzc-total-duplicadas').textContent = duplicadas.toLocaleString('es-MX');
			document.getElementById('rzc-total-sin-clave').textContent  = sinClave.toLocaleString('es-MX');

			const muestra = filasParaAplicar.slice(0, PREVIEW_LIMITE);
			document.getElementById('rzc-preview-limite').textContent = String(Math.min(PREVIEW_LIMITE, filasParaAplicar.length));
			document.getElementById('rzc-preview-tbody').innerHTML = muestra.map(f => `
				<tr>
					<td>${escaparHtml(f.clave)}</td>
					<td>${escaparHtml(f.nombres)}</td>
					<td>${escaparHtml(f.apellido_paterno)}</td>
					<td>${escaparHtml(f.apellido_materno)}</td>
				</tr>
			`).join('');

			resetearEstadoCarga();
			document.getElementById('rzc-btn-aplicar').disabled = false;
			document.getElementById('rzc-preview-wrap').style.display = 'block';

			ocultarLoader();
			document.getElementById('rzc-preview-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });

		} catch (e) {
			ocultarLoader();
			console.error(e);
			Swal.fire('Error', 'No se pudo leer el archivo. Verifica que sea un xlsx válido.', 'error');
		}
	}

	function escaparHtml(txt) {
		const div = document.createElement('div');
		div.textContent = txt == null ? '' : String(txt);
		return div.innerHTML;
	}

	function partirEnLotes(arr, tam) {
		const lotes = [];
		for (let i = 0; i < arr.length; i += tam) {
			lotes.push(arr.slice(i, i + tam));
		}
		return lotes;
	}

	/* Manda UN lote al backend, con hasta 2 reintentos si falla por
	   red o el backend responde ok:false (por ejemplo, un candado o
	   error de conexión pasajero en la BD). No lanza excepción hacia
	   afuera: si tras los reintentos sigue fallando, regresa ok:false
	   para que el llamador decida seguir con el siguiente lote. */
	async function enviarLoteConReintentos(lote, maxIntentos = 3) {
		let ultimoError = null;

		for (let intento = 1; intento <= maxIntentos; intento++) {
			try {
				const resp = await api({ action: 'cargarLoteNombreSeparado', filas: lote });
				if (resp.ok) return resp;
				ultimoError = resp.msg || 'Error desconocido del servidor.';
			} catch (e) {
				ultimoError = 'Error de comunicación con el servidor.';
			}
			if (intento < maxIntentos) {
				await new Promise(r => setTimeout(r, 600 * intento)); /* backoff simple */
			}
		}

		console.error('Lote fallido tras reintentos:', ultimoError);
		return { ok: false, msg: ultimoError };
	}

	function actualizarProgreso(lotesHechos, totalLotes, filasHechas, totalFilas, insertadas, omitidas, fallidas) {
		const pct = totalLotes ? Math.round((lotesHechos / totalLotes) * 100) : 0;
		const bar = document.getElementById('rzc-progreso-bar');
		bar.style.width = pct + '%';
		bar.textContent = pct + '%';
		document.getElementById('rzc-progreso-texto').textContent =
			`Lote ${lotesHechos} de ${totalLotes} — ${filasHechas.toLocaleString('es-MX')} / ${totalFilas.toLocaleString('es-MX')} filas · ` +
			`insertadas: ${insertadas.toLocaleString('es-MX')} · omitidas (ya existían): ${omitidas.toLocaleString('es-MX')}` +
			`${fallidas ? ' · fallidas: ' + fallidas.toLocaleString('es-MX') : ''}`;
	}

	/* Corre la carga masiva sobre un arreglo de filas dado (se usa
	   tanto para la carga inicial completa como para "Reintentar
	   filas fallidas", que vuelve a correr esto pero solo con las
	   que quedaron pendientes).

	   Regla: si la clave ya existe en la tabla, esa fila se OMITE
	   (no se sobreescribe nada); si no existe, se inserta. Eso lo
	   resuelve el backend con INSERT IGNORE. */
	async function correrCargaMasiva(filas) {
		cargaEnProgreso   = true;
		detenerSolicitado = false;

		const lotes = partirEnLotes(filas, TAMANO_LOTE);
		const totalFilas = filas.length;

		let filasHechas = 0;
		let insertadas  = 0;
		let omitidas    = 0;
		filasFallidas   = [];

		document.getElementById('rzc-progreso-wrap-ext').style.display = 'block';
		document.getElementById('rzc-resultado-wrap').style.display = 'none';
		document.getElementById('rzc-btn-detener').style.display = 'inline-block';
		document.getElementById('rzc-btn-reintentar').style.display = 'none';
		document.getElementById('rzc-btn-aplicar').disabled = true;

		actualizarProgreso(0, lotes.length, 0, totalFilas, 0, 0, 0);

		for (let i = 0; i < lotes.length; i++) {
			if (detenerSolicitado) break;

			const lote = lotes[i];
			const resp = await enviarLoteConReintentos(lote);

			if (resp.ok) {
				insertadas += resp.insertadas || 0;
				omitidas   += resp.omitidas   || 0;
			} else {
				/* Este lote no se pudo aplicar ni con reintentos: se
				   guardan sus filas para poder reintentarlas después
				   sin tener que repetir TODA la carga desde cero. */
				filasFallidas.push(...lote);
			}

			filasHechas += lote.length;
			actualizarProgreso(i + 1, lotes.length, filasHechas, totalFilas, insertadas, omitidas, filasFallidas.length);
		}

		cargaEnProgreso = false;
		document.getElementById('rzc-btn-detener').style.display = 'none';

		document.getElementById('rzc-res-insertadas').textContent = insertadas.toLocaleString('es-MX');
		document.getElementById('rzc-res-omitidas').textContent   = omitidas.toLocaleString('es-MX');
		document.getElementById('rzc-res-fallidas').textContent   = filasFallidas.length.toLocaleString('es-MX');
		document.getElementById('rzc-resultado-wrap').style.display = 'flex';

		if (filasFallidas.length) {
			document.getElementById('rzc-btn-reintentar').style.display = 'inline-block';
		}

		document.getElementById('rzc-btn-aplicar').textContent = '🚀 Aplicar carga masiva';
		document.getElementById('rzc-btn-aplicar').disabled = true; /* ya se aplicó; usar "reintentar" para lo fallido */

		if (detenerSolicitado) {
			Swal.fire('Carga detenida', `Se detuvo la carga. Se insertaron ${insertadas.toLocaleString('es-MX')} filas nuevas antes de parar (${omitidas.toLocaleString('es-MX')} ya existían).`, 'info');
		} else if (filasFallidas.length) {
			Swal.fire('Carga terminada con pendientes', `Se insertaron ${insertadas.toLocaleString('es-MX')} filas nuevas (${omitidas.toLocaleString('es-MX')} ya existían y se omitieron). ${filasFallidas.length.toLocaleString('es-MX')} quedaron en lotes fallidos — usa "Reintentar filas fallidas".`, 'warning');
		} else {
			Swal.fire({ icon:'success', title:'Carga completa', text: `Se insertaron ${insertadas.toLocaleString('es-MX')} filas nuevas. ${omitidas.toLocaleString('es-MX')} ya existían y se omitieron (no se modificaron).` });
		}
	}

	async function confirmarAplicarCargaMasiva() {
		if (!filasParaAplicar.length) return;

		const result = await Swal.fire({
			title: '¿Aplicar la carga masiva?',
			html: `Se van a intentar insertar hasta <strong>${filasParaAplicar.length.toLocaleString('es-MX')}</strong> filas en
				   <code>carobra.usuarios_nombre_separado</code>, en lotes de ${TAMANO_LOTE}. Las claves que <strong>ya existan</strong>
				   se omiten (no se sobreescriben). No se toca <code>carobra.usuarios</code>.`,
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, aplicar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		await correrCargaMasiva(filasParaAplicar);
	}

	async function reintentarFallidas() {
		if (!filasFallidas.length) return;
		const pendientes = filasFallidas.slice();
		await correrCargaMasiva(pendientes);
	}

	function detenerCarga() {
		if (!cargaEnProgreso) return;
		detenerSolicitado = true;
	}

	function limpiarCarga() {
		archivoSeleccionado = null;
		filasParaAplicar = [];
		filasFallidas = [];
		document.getElementById('rzc-file-input').value = '';
		document.getElementById('rzc-nombre-archivo').textContent = '';
		document.getElementById('rzc-btn-procesar').disabled = true;
		document.getElementById('rzc-preview-wrap').style.display = 'none';
		document.getElementById('rzc-preview-tbody').innerHTML = '<tr><td colspan="4" class="rzc-vacio">Sube un archivo para ver la vista previa.</td></tr>';
		resetearEstadoCarga();
	}

	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		document.getElementById('salir').addEventListener('click', () => {
			window.location = 'https://www.sis-ca.com.mx:7863/';
		});
		document.getElementById('email').addEventListener('click', () => {
			window.open('http://webmail.carobra.com.mx/imp/login.php');
		});
	});

	</script>
</html>