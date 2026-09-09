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

			.rzc-titulo {
				font-size:15px; font-weight:700; color:#14507a; margin-bottom:6px;
			}

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

			.rzc-resumen {
				display:flex; flex-wrap:wrap; gap:12px; margin:18px 0;
			}

			.rzc-resumen-item {
				flex:1; min-width:130px; border-radius:10px; padding:14px 16px;
				border:1px solid; text-align:center;
			}

			.rzc-resumen-num { font-size:24px; font-weight:700; line-height:1; }
			.rzc-resumen-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-top:4px; }

			.rzc-resumen-nuevo         { background:#eafaf1; border-color:#b8e8cd; }
			.rzc-resumen-nuevo         .rzc-resumen-num, .rzc-resumen-nuevo .rzc-resumen-label { color:#1e8e58; }
			.rzc-resumen-actualizado   { background:#e8f0fb; border-color:#b9d3f0; }
			.rzc-resumen-actualizado   .rzc-resumen-num, .rzc-resumen-actualizado .rzc-resumen-label { color:#1a5276; }
			.rzc-resumen-duplicado     { background:#fff8e6; border-color:#f5d97a; }
			.rzc-resumen-duplicado     .rzc-resumen-num, .rzc-resumen-duplicado .rzc-resumen-label { color:#a06b00; }
			.rzc-resumen-no_encontrado { background:#fff8e6; border-color:#f5d97a; }
			.rzc-resumen-no_encontrado .rzc-resumen-num, .rzc-resumen-no_encontrado .rzc-resumen-label { color:#a06b00; }
			.rzc-resumen-error         { background:#fdeeee; border-color:#f3c4c4; }
			.rzc-resumen-error         .rzc-resumen-num, .rzc-resumen-error .rzc-resumen-label { color:#c0392b; }

			.rzc-tabla-wrap {
				max-height:420px; overflow-y:auto; border:1px solid #e0e8ec; border-radius:8px;
			}

			.rzc-tabla { width:100%; border-collapse:collapse; font-size:12.5px; }

			.rzc-tabla thead th {
				background:#f0f6f9; color:#2B73B6; font-weight:700; font-size:11px;
				text-transform:uppercase; letter-spacing:.04em; padding:9px 10px; text-align:left;
				position:sticky; top:0; z-index:1;
			}

			.rzc-tabla tbody td { padding:8px 10px; border-bottom:1px solid #eef2f4; color:#333; }
			.rzc-tabla tbody tr:hover { background:#f7fbfc; }

			.rzc-badge {
				display:inline-block; font-size:10.5px; font-weight:700; text-transform:uppercase;
				letter-spacing:.03em; border-radius:10px; padding:2px 9px;
			}
			.rzc-badge-nuevo         { background:#eafaf1; color:#1e8e58; }
			.rzc-badge-actualizado   { background:#e8f0fb; color:#1a5276; }
			.rzc-badge-no_encontrado { background:#fff8e6; color:#a06b00; }
			.rzc-badge-error         { background:#fdeeee; color:#c0392b; }

			.rzc-motivo { font-size:11px; color:#888; }

			.rzc-paso {
				display:inline-flex; align-items:center; justify-content:center;
				width:20px; height:20px; border-radius:50%; background:#2B73B6; color:#fff;
				font-size:11px; font-weight:700; margin-right:8px;
			}

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

			.rzc-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:16px; }

			.rzc-vacio { text-align:center; padding:30px; color:#aaa; font-size:13px; }
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
				titulo("Interface Rechazos");
				$pag="INTERFACES";
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
			<div class="rzc-titulo">📤 Cargar Rechazos Comparador desde xlsx</div>

			<div class="rzc-drop-zone" id="rzc-drop-zone">
				<input type="file" id="rzc-file-input" accept=".xlsx,.xls" style="display:none;" onchange="onArchivoSeleccionado(this)">
				<div class="rzc-drop-icono">📄</div>
				<div class="rzc-drop-texto">Haz clic para elegir el archivo xlsx</div>
				<div class="rzc-drop-archivo" id="rzc-nombre-archivo"></div>
			</div>

			<div style="display:flex; gap:10px; margin-top:14px;">
				<button type="button" class="btn-rzc-limpiar" onclick="document.getElementById('rzc-file-input').click()">📁 Cargar archivo</button>
				<button type="button" class="btn-rzc-guardar" id="rzc-btn-procesar" onclick="procesarArchivoSeleccionado()" disabled>⚙️ Procesar archivo</button>
			</div>
		</div>

		<!-- Vista previa: catálogo -->
		<div class="rzc-card" id="rzc-catalogo-wrap" style="display:none;">
			<div class="rzc-titulo"><span class="rzc-paso">1</span>Catálogo rechazo_comp</div>
			<div class="rzc-subtitulo">Un renglón por id (deduplicado). Se guardarán los marcados como <strong>Nuevo</strong> o <strong>Actualizado</strong>.</div>

			<div class="rzc-resumen">
				<div class="rzc-resumen-item rzc-resumen-nuevo">
					<div class="rzc-resumen-num" id="rzc-cat-nuevo">0</div>
					<div class="rzc-resumen-label">Nuevos</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-actualizado">
					<div class="rzc-resumen-num" id="rzc-cat-actualizado">0</div>
					<div class="rzc-resumen-label">A actualizar</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-duplicado">
					<div class="rzc-resumen-num" id="rzc-cat-duplicado">0</div>
					<div class="rzc-resumen-label">Duplicados en archivo</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-error">
					<div class="rzc-resumen-num" id="rzc-cat-error">0</div>
					<div class="rzc-resumen-label">Errores</div>
				</div>
			</div>

			<div class="rzc-tabla-wrap">
				<table class="rzc-tabla">
					<thead>
						<tr>
							<th>Id</th>
							<th>Descripción</th>
							<th>Estatus</th>
							<th>Detalle</th>
						</tr>
					</thead>
					<tbody id="rzc-cat-tbody">
						<tr><td colspan="4" class="rzc-vacio">Sube un archivo para ver la vista previa.</td></tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Vista previa: folios -->
		<div class="rzc-card d-none" id="rzc-folios-wrap" style="display:none;">
			<div class="rzc-titulo"><span class="rzc-paso">2</span>Actualización de prod_rechazos_v2 (por folio)</div>
			<div class="rzc-subtitulo">Un renglón por fila del xlsx. Se actualizará <code>id_rechazo_comp</code> en los folios marcados como <strong>Actualizado</strong>.</div>

			<div class="rzc-resumen">
				<div class="rzc-resumen-item rzc-resumen-actualizado">
					<div class="rzc-resumen-num" id="rzc-fol-actualizado">0</div>
					<div class="rzc-resumen-label">Folios a actualizar</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-no_encontrado">
					<div class="rzc-resumen-num" id="rzc-fol-no_encontrado">0</div>
					<div class="rzc-resumen-label">Folio no encontrado</div>
				</div>
				<div class="rzc-resumen-item rzc-resumen-error">
					<div class="rzc-resumen-num" id="rzc-fol-error">0</div>
					<div class="rzc-resumen-label">Errores</div>
				</div>
			</div>

			<div class="rzc-tabla-wrap">
				<table class="rzc-tabla">
					<thead>
						<tr>
							<th>Folio</th>
							<th>Id rechazo</th>
							<th>Estatus</th>
							<th>Detalle</th>
						</tr>
					</thead>
					<tbody id="rzc-fol-tbody">
						<tr><td colspan="4" class="rzc-vacio">Sube un archivo para ver la vista previa.</td></tr>
					</tbody>
				</table>
			</div>

			<div class="rzc-footer">
				<button type="button" class="btn-rzc-limpiar" onclick="limpiarCarga()">Cancelar / limpiar</button>
				<button type="button" class="btn-rzc-guardar" id="rzc-btn-aplicar" onclick="confirmarAplicarCambios()" disabled>Aplicar cambios</button>
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

	const API = 'interface_rechazo_api.php';

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

	let filasParaAplicar = []; /* último set de filas {folio, id, descripcion} parseado del xlsx (una por renglón, sin deduplicar) */
	let archivoSeleccionado = null; /* File elegido, pendiente de procesar */
	let duplicadosIdEnArchivo = 0; /* solo informativo: cuántos ids se repiten dentro del archivo */

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

	/* Solo guarda el archivo elegido y habilita el botón "Procesar
	   archivo"; NO lee ni sube nada todavía. Leer/parsear se hace
	   aparte, al presionar ese botón. */
	function onArchivoSeleccionado(input) {
		const file = input.files[0];
		if (!file) return;

		archivoSeleccionado = file;
		document.getElementById('rzc-nombre-archivo').textContent = '📎 ' + file.name;
		document.getElementById('rzc-btn-procesar').disabled = false;

		/* Si ya había una vista previa de un archivo anterior, se oculta
		   hasta que se procese este nuevo archivo. */
		document.getElementById('rzc-catalogo-wrap').style.display = 'none';
		document.getElementById('rzc-folios-wrap').style.display = 'none';
	}

	/* Formato oficial de folio CAROBRA: 10 dígitos numéricos. Sirve
	   para filtrar basura que se cuela al final de exports tipo
	   Salesforce (pies de página, avisos de confidencialidad, etc.)
	   que SheetJS lee como si fueran una fila más y que cae en la
	   columna de Folio. */
	const FOLIO_REGEX = /^\d{8,12}$/;

	/* Lee y parsea el xlsx ya elegido, identifica las columnas de
	   Folio, id y descripción, y manda TODAS las filas válidas (sin
	   deduplicar, cada folio es un caso distinto) a
	   previsualizarRechazoComp. El backend deduplica por id solo
	   para la parte del catálogo. Se dispara con "Procesar archivo". */
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
			const colFolio       = encontrarColumna(headers, ['folio']);
			const colId          = encontrarColumna(headers, ['diagnostico']) || encontrarColumna(headers, ['tramite']);
			const colDescripcion = encontrarColumna(headers, ['descripcion', 'rechazo']) || encontrarColumna(headers, ['descripcion']);

			if (!colFolio || !colId || !colDescripcion) {
				ocultarLoader();
				Swal.fire('Columnas no encontradas', 'No pude identificar las columnas de Folio, id y/o descripción en el xlsx. Encabezados encontrados: ' + headers.join(', '), 'error');
				return;
			}

			const filasCrudas = filas.map(f => ({
				folio       : (f[colFolio]       ?? '').toString().trim(),
				id          : (f[colId]          ?? '').toString().trim(),
				descripcion : (f[colDescripcion]  ?? '').toString().trim()
			}));

			filasParaAplicar = filasCrudas.filter(f => FOLIO_REGEX.test(f.folio) && f.id !== '');
			const descartadas = filasCrudas.length - filasParaAplicar.length;

			if (!filasParaAplicar.length) {
				ocultarLoader();
				Swal.fire('Sin filas útiles', 'No se encontró ninguna fila con un folio válido y su id.', 'warning');
				return;
			}

			/* Informativo: cuántos ids distintos se repiten dentro del
			   archivo (no afecta el conteo de folios, cada folio es su
			   propio renglón y se procesa siempre). */
			const idsVistos = new Set();
			duplicadosIdEnArchivo = 0;
			for (const f of filasParaAplicar) {
				if (idsVistos.has(f.id)) duplicadosIdEnArchivo++;
				idsVistos.add(f.id);
			}

			ocultarLoader();
			await cargarVistaPrevia();

			document.getElementById('rzc-catalogo-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });

		} catch (e) {
			ocultarLoader();
			console.error(e);
			Swal.fire('Error', 'No se pudo leer el archivo. Verifica que sea un xlsx válido.', 'error');
		}
	}

	async function cargarVistaPrevia() {
		mostrarLoader();
		try {
			const resp = await api({ action: 'previsualizarRechazoComp', filas: filasParaAplicar });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo procesar el archivo.', 'error');
				return;
			}

			document.getElementById('rzc-catalogo-wrap').style.display = 'block';
			document.getElementById('rzc-folios-wrap').style.display = 'block';
			renderResultado(resp.catalogo.filas, resp.catalogo.conteo, resp.folios.filas, resp.folios.conteo);

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	const RZC_BADGE_LABEL_CAT = { nuevo: 'Nuevo', actualizado: 'Actualizado', error: 'Error' };
	const RZC_BADGE_LABEL_FOL = { actualizado: 'Actualizado', no_encontrado: 'No encontrado', error: 'Error' };

	function renderResultado(catFilas, catConteo, folFilas, folConteo) {

		/* ── Catálogo ── */
		document.getElementById('rzc-cat-nuevo').textContent       = catConteo.nuevo       || 0;
		document.getElementById('rzc-cat-actualizado').textContent = catConteo.actualizado || 0;
		document.getElementById('rzc-cat-duplicado').textContent   = duplicadosIdEnArchivo || 0;
		document.getElementById('rzc-cat-error').textContent       = catConteo.error       || 0;

		const catTbody = document.getElementById('rzc-cat-tbody');
		catTbody.innerHTML = !catFilas.length
			? '<tr><td colspan="4" class="rzc-vacio">Sin filas para mostrar.</td></tr>'
			: catFilas.map(f => `
				<tr>
					<td>${escaparHtml(f.id)}</td>
					<td>${escaparHtml(f.descripcion)}</td>
					<td><span class="rzc-badge rzc-badge-${f.estatus}">${RZC_BADGE_LABEL_CAT[f.estatus] || f.estatus}</span></td>
					<td class="rzc-motivo">${escaparHtml(f.motivo || '')}</td>
				</tr>
			`).join('');

		/* ── Folios ── */
		document.getElementById('rzc-fol-actualizado').textContent   = folConteo.actualizado   || 0;
		document.getElementById('rzc-fol-no_encontrado').textContent = folConteo.no_encontrado || 0;
		document.getElementById('rzc-fol-error').textContent         = folConteo.error         || 0;

		const folTbody = document.getElementById('rzc-fol-tbody');
		folTbody.innerHTML = !folFilas.length
			? '<tr><td colspan="4" class="rzc-vacio">Sin filas para mostrar.</td></tr>'
			: folFilas.map(f => `
				<tr>
					<td>${escaparHtml(f.folio)}</td>
					<td>${escaparHtml(f.id)}</td>
					<td><span class="rzc-badge rzc-badge-${f.estatus}">${RZC_BADGE_LABEL_FOL[f.estatus] || f.estatus}</span></td>
					<td class="rzc-motivo">${escaparHtml(f.motivo || '')}</td>
				</tr>
			`).join('');

		const totalAAplicar = (catConteo.nuevo || 0) + (catConteo.actualizado || 0) + (folConteo.actualizado || 0);
		document.getElementById('rzc-btn-aplicar').disabled = !(totalAAplicar > 0);
	}

	function escaparHtml(txt) {
		const div = document.createElement('div');
		div.textContent = txt == null ? '' : String(txt);
		return div.innerHTML;
	}

	async function confirmarAplicarCambios() {
		if (!filasParaAplicar.length) return;

		const result = await Swal.fire({
			title: '¿Aplicar los cambios?',
			html: 'Se va a actualizar el catálogo <strong>rechazo_comp</strong> y el campo <strong>id_rechazo_comp</strong> en <code>prod_rechazos_v2</code>. Esta acción no se puede deshacer.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, aplicar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'aplicarRechazoComp', filas: filasParaAplicar });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo aplicar los cambios.', 'error');
				return;
			}

			renderResultado(resp.catalogo.filas, resp.catalogo.conteo, resp.folios.filas, resp.folios.conteo);
			document.getElementById('rzc-btn-aplicar').disabled = true;

			Swal.fire({
				icon: 'success',
				title: 'Listo',
				html: `Catálogo — nuevos: <strong>${resp.catalogo.conteo.nuevo || 0}</strong> · actualizados: <strong>${resp.catalogo.conteo.actualizado || 0}</strong><br>
					   Folios — actualizados: <strong>${resp.folios.conteo.actualizado || 0}</strong> · no encontrados: <strong>${resp.folios.conteo.no_encontrado || 0}</strong>`,
			});

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	function limpiarCarga() {
		filasParaAplicar = [];
		archivoSeleccionado = null;
		duplicadosIdEnArchivo = 0;
		document.getElementById('rzc-file-input').value = '';
		document.getElementById('rzc-nombre-archivo').textContent = '';
		document.getElementById('rzc-btn-procesar').disabled = true;
		document.getElementById('rzc-catalogo-wrap').style.display = 'none';
		document.getElementById('rzc-folios-wrap').style.display = 'none';
		document.getElementById('rzc-cat-tbody').innerHTML = '<tr><td colspan="4" class="rzc-vacio">Sube un archivo para ver la vista previa.</td></tr>';
		document.getElementById('rzc-fol-tbody').innerHTML = '<tr><td colspan="4" class="rzc-vacio">Sube un archivo para ver la vista previa.</td></tr>';
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