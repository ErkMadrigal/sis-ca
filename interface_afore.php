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

			.afc-card {
				background:#fff; border:1px solid #e0e8ec; border-radius:10px;
				padding:18px 20px; margin-bottom:18px;
			}

			.afc-titulo {
				font-size:15px; font-weight:700; color:#14507a; margin-bottom:6px;
			}

			.afc-subtitulo { font-size:12.5px; color:#888; margin-bottom:14px; }

			.afc-drop-zone {
				border:2px dashed #b0d0e0; border-radius:10px; padding:34px 16px;
				text-align:center; cursor:pointer; transition:border-color .15s, background .15s;
				background:#f7fbfd;
			}
			.afc-drop-zone:hover { border-color:#2B73B6; background:#eef6fb; }

			.afc-drop-icono { font-size:32px; margin-bottom:8px; }
			.afc-drop-texto { font-size:13px; color:#555; }
			.afc-drop-archivo { font-size:12.5px; color:#2B73B6; font-weight:600; margin-top:8px; }

			.afc-resumen {
				display:flex; flex-wrap:wrap; gap:12px; margin:18px 0;
			}

			.afc-resumen-item {
				flex:1; min-width:130px; border-radius:10px; padding:14px 16px;
				border:2px solid transparent; text-align:center; cursor:pointer;
				transition:transform .1s, box-shadow .15s;
			}
			.afc-resumen-item:hover { transform:translateY(-1px); box-shadow:0 2px 8px rgba(0,0,0,.08); }
			.afc-resumen-item.afc-activo { border-color:#2B73B6; box-shadow:0 2px 8px rgba(43,115,182,.25); }

			.afc-resumen-num { font-size:24px; font-weight:700; line-height:1; }
			.afc-resumen-label { font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:.04em; margin-top:4px; }

			.afc-resumen-todos     { background:#eef3f7; border-color:transparent; }
			.afc-resumen-todos     .afc-resumen-num, .afc-resumen-todos .afc-resumen-label { color:#2B73B6; }

			.afc-resumen-texto {
				background:#f7fbfd; border:1px solid #e0e8ec; border-radius:8px;
				padding:12px 14px; font-size:12.5px; color:#333; line-height:1.6; margin-bottom:14px;
			}
			.afc-resumen-texto strong { color:#14507a; }

			.afc-nota-truncado {
				font-size:11.5px; color:#a06b00; background:#fff8e6; border:1px solid #f5d97a;
				border-radius:6px; padding:7px 10px; margin-bottom:8px;
			}

			.afc-resumen-ok        { background:#eafaf1; border-color:#b8e8cd; }
			.afc-resumen-ok        .afc-resumen-num, .afc-resumen-ok .afc-resumen-label { color:#1e8e58; }
			.afc-resumen-ya_asignada { background:#eaf2fb; border-color:#b8d4ec; }
			.afc-resumen-ya_asignada .afc-resumen-num, .afc-resumen-ya_asignada .afc-resumen-label { color:#1a5278; }
			.afc-resumen-en_espera { background:#f1ecfb; border-color:#d6c8f5; }
			.afc-resumen-en_espera .afc-resumen-num, .afc-resumen-en_espera .afc-resumen-label { color:#6b3fa0; }
			.afc-resumen-omitida   { background:#f0f2f5; border-color:#d8dde2; }
			.afc-resumen-omitida   .afc-resumen-num, .afc-resumen-omitida .afc-resumen-label { color:#5a6570; }
			.afc-resumen-pendiente { background:#fff8e6; border-color:#f5d97a; }
			.afc-resumen-pendiente .afc-resumen-num, .afc-resumen-pendiente .afc-resumen-label { color:#a06b00; }
			.afc-resumen-error     { background:#fdeeee; border-color:#f3c4c4; }
			.afc-resumen-error     .afc-resumen-num, .afc-resumen-error .afc-resumen-label { color:#c0392b; }

			.afc-tabla-wrap {
				max-height:420px; overflow-y:auto; border:1px solid #e0e8ec; border-radius:8px;
			}

			.afc-tabla { width:100%; border-collapse:collapse; font-size:12.5px; }

			.afc-tabla thead th {
				background:#f0f6f9; color:#2B73B6; font-weight:700; font-size:11px;
				text-transform:uppercase; letter-spacing:.04em; padding:9px 10px; text-align:left;
				position:sticky; top:0; z-index:1;
			}

			.afc-tabla tbody td { padding:8px 10px; border-bottom:1px solid #eef2f4; color:#333; }
			.afc-tabla tbody tr:hover { background:#f7fbfc; }

			.afc-badge {
				display:inline-block; font-size:10.5px; font-weight:700; text-transform:uppercase;
				letter-spacing:.03em; border-radius:10px; padding:2px 9px;
			}
			.afc-badge-ok          { background:#eafaf1; color:#1e8e58; }
			.afc-badge-ya_asignada { background:#eaf2fb; color:#1a5278; }
			.afc-badge-en_espera   { background:#f1ecfb; color:#6b3fa0; }
			.afc-badge-omitida     { background:#f0f2f5; color:#5a6570; }
			.afc-badge-pendiente   { background:#fff8e6; color:#a06b00; }
			.afc-badge-error       { background:#fdeeee; color:#c0392b; }

			.afc-motivo { font-size:11px; color:#888; }

			.btn-afc-guardar {
				background:#2B73B6; color:#fff; border:none; padding:9px 22px; border-radius:6px;
				font-size:13px; font-weight:600; cursor:pointer; transition:background .2s;
			}
			.btn-afc-guardar:hover:not(:disabled) { background:#145670; }
			.btn-afc-guardar:disabled { opacity:.45; cursor:not-allowed; }

			.btn-afc-limpiar {
				background:#fff; color:#555; border:1px solid #bbb; padding:9px 18px; border-radius:6px;
				font-size:13px; cursor:pointer; transition:background .2s;
			}
			.btn-afc-limpiar:hover:not(:disabled) { background:#f5f5f5; }
			.btn-afc-limpiar:disabled { opacity:.45; cursor:not-allowed; }

			.afc-footer { display:flex; justify-content:flex-end; gap:10px; margin-top:16px; }

			.afc-vacio { text-align:center; padding:30px; color:#aaa; font-size:13px; }
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
				titulo("Interface afore cedente");
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
				/* Sin esto, #contenido se queda con el overflow:hidden por
				   defecto del framework del sitio: si el contenido dinámico
				   (tarjetas + resumen + tabla) es más alto que lo que calcula
				   calcula_h() para #contenedor, todo lo que sobra se corta y
				   desaparece sin poder hacer scroll a verlo. Mismo fix ya
				   usado en seg_kpis.php y seg_reporteador.php. */
				$divcontenido->css("overflow-y", "auto");
	?>

	<div class="container-fluid" style="padding:16px 18px; text-align:left;">

		<!-- Carga de archivo -->
		<div class="afc-card">
			<div class="afc-titulo">📤 Actualizar Afore cedente desde xlsx</div>
			<div class="afc-subtitulo">
				Sube el xlsx con las columnas <strong>CURP</strong> y <strong>Nombre de la cuenta: Afore cedente</strong>.
				Se toma el ÚLTIMO folio de producción de cada CURP (por fecha de traspaso más reciente): si ya tiene afore
				cedente capturada no se toca; si no la tiene, se le asigna la del xlsx. Si la CURP no tiene ningún folio
				todavía, la afore se guarda en espera para cuando lo tenga. Las filas con "NO ASIGNADO", "-",
				"PRESTADORA DE SERVICIOS" o vacío se omiten (no se modifican). CITIBANAMEX se toma como BANAMEX.
			</div>

			<div class="afc-drop-zone" id="afc-drop-zone">
				<input type="file" id="afc-file-input" accept=".xlsx,.xls" style="display:none;" onchange="onArchivoSeleccionado(this)">
				<div class="afc-drop-icono">📄</div>
				<div class="afc-drop-texto">Haz clic para elegir el archivo xlsx</div>
				<div class="afc-drop-archivo" id="afc-nombre-archivo"></div>
			</div>

			<div style="display:flex; gap:10px; margin-top:14px;">
				<button type="button" class="btn-afc-limpiar" onclick="document.getElementById('afc-file-input').click()">📁 Cargar archivo</button>
				<button type="button" class="btn-afc-guardar" id="afc-btn-procesar" onclick="procesarArchivoSeleccionado()" disabled>⚙️ Procesar archivo</button>
			</div>
		</div>

		<!-- Resumen + tabla de vista previa -->
		<div class="afc-card" id="afc-resultado-wrap" style="display:none;">
			<div class="afc-titulo">Vista previa</div>
			<div class="afc-subtitulo">Revisa el resultado por fila antes de aplicar los cambios. Se actualizarán las filas marcadas como <strong>OK</strong> y se guardarán en espera las marcadas como <strong>CURP no encontrada</strong>. Haz clic en cualquier tarjeta para filtrar la tabla de abajo por esa categoría.</div>

			<div class="afc-resumen-texto" id="afc-resumen-texto"></div>

			<div class="afc-resumen">
				<div class="afc-resumen-item afc-resumen-todos afc-activo" id="afc-card-todos" onclick="setFiltro('todos')">
					<div class="afc-resumen-num" id="afc-conteo-todos">0</div>
					<div class="afc-resumen-label">Todos</div>
				</div>
				<div class="afc-resumen-item afc-resumen-ok" id="afc-card-ok" onclick="setFiltro('ok')">
					<div class="afc-resumen-num" id="afc-conteo-ok">0</div>
					<div class="afc-resumen-label">Actualizados</div>
				</div>
				<div class="afc-resumen-item afc-resumen-ya_asignada" id="afc-card-ya_asignada" onclick="setFiltro('ya_asignada')">
					<div class="afc-resumen-num" id="afc-conteo-ya_asignada">0</div>
					<div class="afc-resumen-label">Ya asignada</div>
				</div>
				<div class="afc-resumen-item afc-resumen-en_espera" id="afc-card-en_espera" onclick="setFiltro('en_espera')">
					<div class="afc-resumen-num" id="afc-conteo-en_espera">0</div>
					<div class="afc-resumen-label">CURP no encontrada</div>
				</div>
				<div class="afc-resumen-item afc-resumen-omitida" id="afc-card-omitida" onclick="setFiltro('omitida')">
					<div class="afc-resumen-num" id="afc-conteo-omitida">0</div>
					<div class="afc-resumen-label">Omitidas (sin afore)</div>
				</div>
				<div class="afc-resumen-item afc-resumen-pendiente" id="afc-card-pendiente" onclick="setFiltro('pendiente')">
					<div class="afc-resumen-num" id="afc-conteo-pendiente">0</div>
					<div class="afc-resumen-label">Pendientes</div>
				</div>
				<div class="afc-resumen-item afc-resumen-error" id="afc-card-error" onclick="setFiltro('error')">
					<div class="afc-resumen-num" id="afc-conteo-error">0</div>
					<div class="afc-resumen-label">Errores</div>
				</div>
			</div>

			<div class="afc-nota-truncado" id="afc-nota-truncado" style="display:none;"></div>

			<div class="afc-tabla-wrap">
				<table class="afc-tabla">
					<thead>
						<tr>
							<th>CURP</th>
							<th>Afore en xlsx</th>
							<th>Folio</th>
							<th>Clave resuelta</th>
							<th>Estatus</th>
							<th>Detalle</th>
						</tr>
					</thead>
					<tbody id="afc-tbody">
						<tr><td colspan="6" class="afc-vacio">Sube un archivo para ver la vista previa.</td></tr>
					</tbody>
				</table>
			</div>

			<div class="afc-footer">
				<button type="button" class="btn-afc-limpiar" onclick="descargarReporteExcel()" id="afc-btn-descargar" disabled>📥 Descargar reporte completo (Excel)</button>
				<button type="button" class="btn-afc-limpiar" onclick="limpiarCarga()">Cancelar / limpiar</button>
				<button type="button" class="btn-afc-guardar" id="afc-btn-aplicar" onclick="confirmarAplicarCambios()" disabled>Aplicar cambios</button>
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

	const API = 'interface_afore_api.php';

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

	document.getElementById('afc-drop-zone').addEventListener('click', () => {
		document.getElementById('afc-file-input').click();
	});

	let filasParaAplicar = []; /* último set de filas {curp, afore_texto} parseado del xlsx, ya filtrado por CURP válida */
	let archivoSeleccionado = null; /* File elegido, pendiente de procesar */

	/* ── Estado para el "debugger": guardan el último resultado completo
	   (no solo lo que se ve en pantalla) para poder filtrar la tabla y
	   generar el Excel de auditoría con TODAS las filas, no nada más las
	   que se alcanzaron a renderizar. ── */
	let ultimasFilasResultado = [];   // resp.filas completo de la última llamada (previsualizar o aplicar)
	let filtroActivo          = 'todos';
	let filasDescartadasFormato = [];  // filas del xlsx cuyo CURP no tiene formato válido (ni siquiera se mandaron al backend)
	let totalLeidasXlsx       = 0;     // total de filas leídas del xlsx, antes de descartar por formato

	/* Con archivos de 40k+ filas, construir 40,000 <tr> de una sola vez
	   en el DOM puede sentirse lento/trabado en la vista "Todos". Se
	   muestran como máximo estas filas por filtro; para revisar el
	   detalle completo está el botón de descargar Excel (ese sí lleva
	   TODAS las filas, sin límite). */
	const AFC_MAX_FILAS_TABLA = 3000;

	/* Encuentra, sin importar mayúsculas/acentos/espacios extra, la
	   columna del xlsx que corresponde a CURP y a la columna de
	   Afore cedente, para no depender de que el encabezado venga
	   exactamente idéntico letra por letra. */
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
		document.getElementById('afc-nombre-archivo').textContent = '📎 ' + file.name;
		document.getElementById('afc-btn-procesar').disabled = false;

		/* Si ya había una vista previa de un archivo anterior, se oculta
		   hasta que se procese este nuevo archivo. */
		document.getElementById('afc-resultado-wrap').style.display = 'none';
	}

	/* Lee y parsea el xlsx ya elegido, identifica las columnas de CURP
	   y Afore cedente, y manda todo a previsualizarAfoCedente. Se
	   dispara solo con el botón "Procesar archivo". */
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
			const colCurp  = encontrarColumna(headers, ['curp']);
			const colAfore = encontrarColumna(headers, ['afore', 'cedente']) || encontrarColumna(headers, ['afore']);

			if (!colCurp || !colAfore) {
				ocultarLoader();
				Swal.fire('Columnas no encontradas', 'No pude identificar las columnas de CURP y/o Afore cedente en el xlsx. Encabezados encontrados: ' + headers.join(', '), 'error');
				return;
			}

			/* Formato oficial de CURP (18 caracteres). Sirve para filtrar
			   basura que a veces se cuela al final de exports (pies de
			   página tipo "COPYRIGHT © ...", avisos de confidencialidad,
			   etc.) que Excel/SheetJS lee como si fueran una fila más y
			   que por casualidad cae en la columna de CURP. */
			const CURP_REGEX = /^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]{2}$/i;

			const filasCrudas = filas.map(f => ({
				curp        : (f[colCurp]  || '').toString().trim(),
				afore_texto : (f[colAfore] || '').toString().trim()
			}));

			totalLeidasXlsx = filasCrudas.length;
			filasParaAplicar = filasCrudas.filter(f => CURP_REGEX.test(f.curp));
			filasDescartadasFormato = filasCrudas.filter(f => !CURP_REGEX.test(f.curp));

			if (!filasParaAplicar.length) {
				ocultarLoader();
				Swal.fire('Sin filas útiles', 'No se encontró ninguna fila con un CURP válido (18 caracteres).', 'warning');
				return;
			}

			ocultarLoader();
			await cargarVistaPrevia();

			/* Sin mensaje emergente: el bloque de "Resumen ejecutivo" arriba
			   de la tabla ya reporta cuántas filas se descartaron por no
			   traer un CURP con formato válido (18 caracteres), no hace
			   falta una alerta aparte. */

			/* Nos aseguramos de que la vista previa quede visible en
			   pantalla (por si el usuario tenía que bajar con scroll). */
			document.getElementById('afc-resultado-wrap').scrollIntoView({ behavior: 'smooth', block: 'start' });

		} catch (e) {
			ocultarLoader();
			console.error(e);
			Swal.fire('Error', 'No se pudo leer el archivo. Verifica que sea un xlsx válido.', 'error');
		}
	}

	async function cargarVistaPrevia() {
		mostrarLoader();
		try {
			const resp = await api({ action: 'previsualizarAfoCedente', filas: filasParaAplicar });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo procesar el archivo.', 'error');
				return;
			}

			document.getElementById('afc-resultado-wrap').style.display = 'block';
			renderResultado(resp.filas, resp.conteo);

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	const AFC_BADGE_LABEL = {
		ok: 'Actualizado',
		ya_asignada: 'Ya asignada',
		en_espera: 'CURP no encontrada',
		omitida: 'Omitida (sin afore)',
		pendiente: 'Pendiente',
		error: 'Error'
	};

	/* Punto central que llaman tanto cargarVistaPrevia() como
	   confirmarAplicarCambios(): guarda el resultado completo, arma el
	   "resumen ejecutivo" en texto plano y deja la tabla filtrada por
	   filtroActivo (por default 'todos', o el filtro que ya tuviera
	   seleccionado el usuario si estaba re-filtrando). */
	function renderResultado(filas, conteo) {
		ultimasFilasResultado = filas || [];

		const total          = ultimasFilasResultado.length;
		const actualizados    = conteo.ok          || 0;
		const yaAsignados      = conteo.ya_asignada || 0;
		const noEncontradas    = conteo.en_espera   || 0;
		const omitidas         = conteo.omitida     || 0;
		const pendientes       = conteo.pendiente   || 0;
		const errores          = conteo.error       || 0;
		const noActualizados   = total - actualizados;

		document.getElementById('afc-conteo-todos').textContent       = total;
		document.getElementById('afc-conteo-ok').textContent          = actualizados;
		document.getElementById('afc-conteo-ya_asignada').textContent = yaAsignados;
		document.getElementById('afc-conteo-en_espera').textContent   = noEncontradas;
		document.getElementById('afc-conteo-omitida').textContent     = omitidas;
		document.getElementById('afc-conteo-pendiente').textContent   = pendientes;
		document.getElementById('afc-conteo-error').textContent       = errores;

		/* ── Resumen ejecutivo en texto, justo lo que se pidió: cuántos
		   se actualizan, cuántos no y por qué. ── */
		let resumenHtml = `Del xlsx se leyeron <strong>${totalLeidasXlsx || total}</strong> filas`;
		if (filasDescartadasFormato.length) {
			resumenHtml += ` (<strong>${filasDescartadasFormato.length}</strong> se descartaron por traer un CURP con formato inválido y no se revisaron; usa "Descargar reporte" para verlas)`;
		}
		resumenHtml += `. De las <strong>${total}</strong> filas revisadas: se ${actualizados ? 'actualizan/actualizaron' : 'actualizan'} <strong>${actualizados}</strong>`;
		resumenHtml += ` y NO se actualizan <strong>${noActualizados}</strong> (`;
		resumenHtml += `${omitidas} sin afore capturada -- "NO ASIGNADO"/"-"/"PRESTADORA DE SERVICIOS"/vacío, `;
		resumenHtml += `${noEncontradas} con CURP que no se encontró ningún folio en producción, `;
		resumenHtml += `${yaAsignados} que ya tenían afore cedente capturada, `;
		resumenHtml += `${pendientes} pendientes de clave en catálogo, `;
		resumenHtml += `${errores} con error).`;
		document.getElementById('afc-resumen-texto').innerHTML = resumenHtml;

		document.getElementById('afc-btn-descargar').disabled = !total;

		const btnAplicar = document.getElementById('afc-btn-aplicar');
		btnAplicar.disabled = !((actualizados > 0) || (noEncontradas > 0));

		renderTabla(filtroActivo);
	}

	/* Filtra ultimasFilasResultado por estatus (o 'todos') y pinta la
	   tabla, topada a AFC_MAX_FILAS_TABLA filas para no trabar el
	   navegador con archivos de decenas de miles de registros. El
	   Excel de "Descargar reporte" sí lleva el detalle completo. */
	function renderTabla(filtro) {
		filtroActivo = filtro;

		document.querySelectorAll('.afc-resumen-item').forEach(el => el.classList.remove('afc-activo'));
		const cardActiva = document.getElementById('afc-card-' + filtro);
		if (cardActiva) cardActiva.classList.add('afc-activo');

		const filasFiltradas = filtro === 'todos'
			? ultimasFilasResultado
			: ultimasFilasResultado.filter(f => f.estatus === filtro);

		const tbody = document.getElementById('afc-tbody');
		const notaTruncado = document.getElementById('afc-nota-truncado');

		if (!filasFiltradas.length) {
			tbody.innerHTML = '<tr><td colspan="6" class="afc-vacio">Sin filas en esta categoría.</td></tr>';
			notaTruncado.style.display = 'none';
			return;
		}

		const filasAMostrar = filasFiltradas.slice(0, AFC_MAX_FILAS_TABLA);

		if (filasFiltradas.length > AFC_MAX_FILAS_TABLA) {
			notaTruncado.style.display = 'block';
			notaTruncado.textContent = `Mostrando las primeras ${AFC_MAX_FILAS_TABLA.toLocaleString()} de ${filasFiltradas.length.toLocaleString()} filas en esta categoría. Descarga el reporte en Excel para ver el detalle completo.`;
		} else {
			notaTruncado.style.display = 'none';
		}

		tbody.innerHTML = filasAMostrar.map(f => `
			<tr>
				<td>${escaparHtml(f.curp)}</td>
				<td>${escaparHtml(f.afore_texto)}</td>
				<td>${f.folio ?? '—'}</td>
				<td>${f.clave ?? '—'}</td>
				<td><span class="afc-badge afc-badge-${f.estatus}">${AFC_BADGE_LABEL[f.estatus] || f.estatus}</span></td>
				<td class="afc-motivo">${escaparHtml(f.motivo || '')}</td>
			</tr>
		`).join('');
	}

	function setFiltro(filtro) {
		renderTabla(filtro);
	}

	/* Genera un xlsx con TODAS las filas (sin el tope de la tabla en
	   pantalla), una hoja por categoría, más una hoja de Resumen y una
	   de las filas descartadas por CURP con formato inválido. Así,
	   aunque el archivo tenga 40k+ filas, se puede auditar completo
	   fuera del navegador. */
	function descargarReporteExcel() {
		if (!ultimasFilasResultado.length) return;

		const wb = XLSX.utils.book_new();

		const encabezados = ['CURP', 'Afore en xlsx', 'Folio', 'Clave resuelta', 'Estatus', 'Detalle'];
		const filaAArreglo = f => [f.curp, f.afore_texto, f.folio ?? '', f.clave ?? '', AFC_BADGE_LABEL[f.estatus] || f.estatus, f.motivo || ''];

		const hojas = [
			['Actualizados',        ultimasFilasResultado.filter(f => f.estatus === 'ok')],
			['CURP no encontrada',  ultimasFilasResultado.filter(f => f.estatus === 'en_espera')],
			['Omitidas sin afore',  ultimasFilasResultado.filter(f => f.estatus === 'omitida')],
			['Ya asignadas',        ultimasFilasResultado.filter(f => f.estatus === 'ya_asignada')],
			['Pendientes',          ultimasFilasResultado.filter(f => f.estatus === 'pendiente')],
			['Errores',             ultimasFilasResultado.filter(f => f.estatus === 'error')],
		];

		/* Hoja de resumen primero, para que sea lo primero que se vea
		   al abrir el archivo. */
		const conteos = hojas.map(([nombre, filas]) => [nombre, filas.length]);
		conteos.push(['Descartadas por CURP con formato inválido (no revisadas)', filasDescartadasFormato.length]);
		conteos.push(['Total leídas del xlsx', totalLeidasXlsx || ultimasFilasResultado.length]);
		const wsResumen = XLSX.utils.aoa_to_sheet([['Categoría', 'Filas'], ...conteos]);
		XLSX.utils.book_append_sheet(wb, wsResumen, 'Resumen');

		hojas.forEach(([nombre, filas]) => {
			const aoa = [encabezados, ...filas.map(filaAArreglo)];
			const ws  = XLSX.utils.aoa_to_sheet(aoa);
			/* Los nombres de hoja de Excel tienen tope de 31 caracteres y no
			   admiten ciertos símbolos; se recorta por seguridad. */
			XLSX.utils.book_append_sheet(wb, ws, nombre.substring(0, 31));
		});

		if (filasDescartadasFormato.length) {
			const aoaDescartadas = [
				['CURP (tal cual venía en el xlsx)', 'Afore en xlsx'],
				...filasDescartadasFormato.map(f => [f.curp, f.afore_texto])
			];
			const wsDescartadas = XLSX.utils.aoa_to_sheet(aoaDescartadas);
			XLSX.utils.book_append_sheet(wb, wsDescartadas, 'Descartadas formato CURP');
		}

		const fechaArchivo = new Date().toISOString().slice(0, 10);
		XLSX.writeFile(wb, `reporte_afore_cedente_${fechaArchivo}.xlsx`);
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
			html: 'Se va a actualizar <strong>afo_cedente</strong> en <code>produccion</code> para las filas OK, y se guardarán en <code>curp_afore_cedentes</code> las filas "En espera". Esta acción no se puede deshacer.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, aplicar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			const resp = await api({ action: 'aplicarAfoCedente', filas: filasParaAplicar });
			ocultarLoader();

			if (!resp.ok) {
				Swal.fire('Error', resp.msg || 'No se pudo aplicar los cambios.', 'error');
				return;
			}

			renderResultado(resp.filas, resp.conteo);
			document.getElementById('afc-btn-aplicar').disabled = true;

			Swal.fire({
				icon: 'success',
				title: 'Listo',
				html: `Actualizados: <strong>${resp.conteo.ok || 0}</strong> · Ya asignados: <strong>${resp.conteo.ya_asignada || 0}</strong> · CURP no encontrada: <strong>${resp.conteo.en_espera || 0}</strong> · Omitidos: <strong>${resp.conteo.omitida || 0}</strong> · Pendientes: <strong>${resp.conteo.pendiente || 0}</strong> · Errores: <strong>${resp.conteo.error || 0}</strong><br><br>Descarga el reporte completo en Excel para el detalle fila por fila.`,
			});

		} catch (e) {
			ocultarLoader();
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	function limpiarCarga() {
		filasParaAplicar = [];
		archivoSeleccionado = null;
		ultimasFilasResultado = [];
		filasDescartadasFormato = [];
		totalLeidasXlsx = 0;
		filtroActivo = 'todos';
		document.getElementById('afc-file-input').value = '';
		document.getElementById('afc-nombre-archivo').textContent = '';
		document.getElementById('afc-btn-procesar').disabled = true;
		document.getElementById('afc-btn-descargar').disabled = true;
		document.getElementById('afc-resultado-wrap').style.display = 'none';
		document.getElementById('afc-resumen-texto').innerHTML = '';
		document.getElementById('afc-nota-truncado').style.display = 'none';
		document.getElementById('afc-tbody').innerHTML = '<tr><td colspan="6" class="afc-vacio">Sube un archivo para ver la vista previa.</td></tr>';
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