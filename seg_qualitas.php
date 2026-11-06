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

			/* #contenedor trae una altura fija (calculada por JS más abajo);
			   sin esto el contenido que no cabe se corta en vez de poder
			   bajar con scroll de la página. */
			#contenedor { overflow-y:auto !important; }

			#loader {
				display:none; position:fixed; top:0; left:0; width:100%; height:100%;
				background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;
			}

			/* ── "qlt-" = Qualitas ── */

			.qlt-card {
				background:#fff; border:1px solid #e0e8ec; border-radius:12px;
				padding:20px 22px; margin-bottom:18px; box-shadow:0 1px 3px rgba(15,23,42,.05);
			}

			.qlt-titulo { font-size:16px; font-weight:700; color:#0f4d8a; margin-bottom:4px; }
			.qlt-subtitulo { font-size:12.5px; color:#8896a6; margin-bottom:16px; }

			/* Switch de 2 opciones para elegir qué archivo cargar */
			.qlt-switch {
				display:inline-flex; background:#eff6ff; border-radius:10px; padding:4px; gap:4px;
				margin-bottom:18px;
			}
			.qlt-switch button {
				border:none; background:transparent; padding:9px 18px; border-radius:8px;
				font-size:12.5px; font-weight:700; color:#5b7fa6; cursor:pointer; transition:.15s;
			}
			.qlt-switch button.activo { background:#185FA5; color:#fff; box-shadow:0 2px 6px rgba(24,95,165,.3); }
			.qlt-switch button:not(.activo):hover { background:#dbeafe; }

			.qlt-panel { display:none; }
			.qlt-panel.activo { display:block; }

			.qlt-drop {
				border:2px dashed #c7d7e5; border-radius:12px; padding:26px; text-align:center;
				background:#f9fbfd; cursor:pointer; transition:.15s;
			}
			.qlt-drop:hover { border-color:#185FA5; background:#f0f6fc; }
			.qlt-drop input { display:none; }
			.qlt-drop-icono { font-size:28px; margin-bottom:6px; }
			.qlt-drop-texto { font-size:13px; color:#5b7fa6; font-weight:600; }
			.qlt-drop-sub { font-size:11px; color:#a3b4c2; margin-top:4px; }

			.qlt-preview-info {
				display:flex; align-items:center; justify-content:space-between; gap:12px;
				margin-top:16px; margin-bottom:10px;
			}
			.qlt-preview-info span { font-size:12.5px; color:#1f2d3d; font-weight:600; }

			.qlt-btn {
				background:#185FA5; color:#fff; border:none; border-radius:9px; padding:9px 18px;
				font-size:12.5px; font-weight:700; cursor:pointer; transition:.15s;
			}
			.qlt-btn:hover { background:#0f4d8a; }
			.qlt-btn:disabled { background:#c7d7e5; cursor:not-allowed; }
			.qlt-btn-secundario {
				background:#eff6ff; color:#185FA5; border:none; border-radius:9px; padding:9px 18px;
				font-size:12.5px; font-weight:700; cursor:pointer; transition:.15s;
			}
			.qlt-btn-secundario:hover { background:#dbeafe; }

			.qlt-tabla-wrap { border:1px solid #e0e8ec; border-radius:10px; overflow:auto; max-height:340px; }
			.qlt-tabla { width:100%; border-collapse:collapse; font-size:12px; }
			.qlt-tabla thead th {
				background:#eff6ff; color:#185FA5; font-weight:700; font-size:10.5px;
				text-transform:uppercase; letter-spacing:.03em; padding:8px 10px; text-align:left;
				position:sticky; top:0; white-space:nowrap;
			}
			.qlt-tabla tbody td { padding:7px 10px; border-bottom:1px solid #eef2f4; white-space:nowrap; color:#1f2d3d; }
			.qlt-tabla tbody tr:hover td { background:#f7fbfc; }
			.qlt-tabla-nota { font-size:11px; color:#a3b4c2; padding:8px 4px 0; }

			.qlt-vacio { text-align:center; padding:26px; color:#aaa; font-size:13px; }

			/* Resumen (stats + botón de cruce) */
			.qlt-stats { display:grid; grid-template-columns:repeat(4, 1fr); gap:14px; margin-bottom:16px; }
			.qlt-stat {
				background:#f7fbff; border:1px solid #e0e8ec; border-radius:10px; padding:14px 16px;
			}
			.qlt-stat-valor { font-size:22px; font-weight:800; color:#0f4d8a; }
			.qlt-stat-label { font-size:11px; color:#5b7fa6; font-weight:600; text-transform:uppercase; letter-spacing:.02em; margin-top:2px; }
			.qlt-stat.qlt-stat-alerta .qlt-stat-valor { color:#a06b00; }

			.qlt-buscador {
				display:flex; align-items:center; gap:10px; margin-bottom:12px;
			}
			.qlt-buscador input {
				flex:1; min-width:0; border:1px solid #d5e2ea; border-radius:9px;
				padding:9px 14px; font-size:12.5px; color:#0f4d8a; outline:none;
				transition:border-color .15s, box-shadow .15s;
			}
			.qlt-buscador input:focus { border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.12); }
			.qlt-buscador input::placeholder { color:#9db3c4; }
			.qlt-buscador span { font-size:11px; color:#5b7fa6; white-space:nowrap; flex-shrink:0; }
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>

	<body onload="calcula_body();">

	<div id="loader"><img src="./img/loading.gif" onerror="this.style.display='none'"></div>

<?php

		if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"]))
		{
			mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
		}
		else
		{
			$fg = $_SESSION["ultimoAcceso"];
			$fa = date("Y-n-j H:i:s");
			$periodo = (strtotime($fa)-strtotime($fg));
			if($periodo > 600)
			{
				session_destroy();
				mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
			}
			else
			{
				$_SESSION["ultimoAcceso"] = $fa;
				$_SESSION["autentifica"] = "p";
			}

			if ($_SESSION["autentifica"] != "p")
			{
				session_destroy();
				mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
			}
			else
			{
				reg_acceso($_SESSION["nombre"],"qualitas");
				$_SESSION["autentifica"]="";

				$divpie=new div("pie");
				$divopcion=new div("opcion");
				$divcabecera=new div("cabecera");
				$divcontenido=new div("contenido");
				$divcontenedor=new div("contenedor");
				$imgpie=new img("imgpie","./img/pie.png");
				$divusuario= new div("usuario",'',"absolute","97%");
				$imgcabecera=new img("imgcabecera","./img/cabecera.png");

				$divcabecera->crear();
					$imgcabecera->crear();
					$imgcabecera->css("width","100%");
					titulo("Qualitas");
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
					<div id="vinculos" style="margin-top: 10px; margin-left: 25px;">
						<?php include("vinculos.php");?>
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

		<div class="qlt-card">
			<div class="qlt-titulo">🚗 Qualitas -- carga y cruce de datos</div>
			<div class="qlt-subtitulo">
				Carga el <strong>Formulario Qualitas</strong> (datos del cliente/vehículo) y los
				<strong>Polizas Qualitas</strong> (pólizas cobradas) por separado. Al cruzarlos por nombre
				se arma la tabla final con <strong>nombre, póliza y CURP</strong>.
			</div>

			<div class="qlt-switch">
				<button type="button" id="qltBtnSwitchForm" class="activo" onclick="cambiarPanelQlt('form')">📋 Formulario Qualitas</button>
				<button type="button" id="qltBtnSwitchPagos" onclick="cambiarPanelQlt('pagos')">💳 Polizas Qualitas</button>
			</div>

			<!-- Panel: Formulario Qualitas -->
			<div class="qlt-panel activo" id="qltPanelForm">
				<label class="qlt-drop" id="qltDropForm">
					<input type="file" id="qltFileForm" accept=".xlsx,.xls" onchange="parsearArchivoQlt(this.files[0], 'form')">
					<div class="qlt-drop-icono">📄</div>
					<div class="qlt-drop-texto">Da clic para elegir el FORMULARIO_QUALITAS.xlsx</div>
					<div class="qlt-drop-sub">Se van a tomar: Modelo (Año), Marca, Descripción, CURP, Nombre y Datos Carobra</div>
				</label>

				<div id="qltResultadoForm"></div>
			</div>

			<!-- Panel: Polizas Qualitas -->
			<div class="qlt-panel" id="qltPanelPagos">
				<label class="qlt-drop" id="qltDropPagos">
					<input type="file" id="qltFilePagos" accept=".xlsx,.xls" onchange="parsearArchivoQlt(this.files[0], 'pagos')">
					<div class="qlt-drop-icono">📄</div>
					<div class="qlt-drop-texto">Da clic para elegir "Polizas QUALITAS.xlsx"</div>
					<div class="qlt-drop-sub">Se van a tomar: CONCEPTO (nombre del cliente) y POLIZA</div>
				</label>

				<div id="qltResultadoPagos"></div>
			</div>
		</div>

		<div class="qlt-card">
			<div class="qlt-titulo">🔗 Resumen y cruce</div>
			<div class="qlt-subtitulo">
				El cruce busca, para cada póliza cargada, un registro del formulario con el mismo nombre
				(sin importar mayúsculas/acentos/espacios) y arma la tabla <strong>qualitas</strong>.
				Se puede correr las veces que haga falta -- no duplica.
			</div>

			<div class="qlt-stats">
				<div class="qlt-stat">
					<div class="qlt-stat-valor" id="qltStatForm">—</div>
					<div class="qlt-stat-label">Formularios cargados</div>
				</div>
				<div class="qlt-stat">
					<div class="qlt-stat-valor" id="qltStatPagos">—</div>
					<div class="qlt-stat-label">Pagos cargados</div>
				</div>
				<div class="qlt-stat">
					<div class="qlt-stat-valor" id="qltStatQualitas">—</div>
					<div class="qlt-stat-label">Cruces en Qualitas</div>
				</div>
				<div class="qlt-stat qlt-stat-alerta">
					<div class="qlt-stat-valor" id="qltStatSinMatch">—</div>
					<div class="qlt-stat-label">Pagos sin match</div>
				</div>
			</div>

			<button type="button" class="qlt-btn" id="qltBtnCruce" onclick="ejecutarCruceQlt()">🔗 Ejecutar cruce</button>
		</div>

		<div class="qlt-card">
			<div class="qlt-titulo">📋 Tabla Qualitas (resultado del cruce)</div>
			<div class="qlt-subtitulo">Nombre, póliza y CURP ya cruzados.</div>

			<div class="qlt-buscador">
				<input type="text" id="qltBuscadorFinal" placeholder="Buscar por nombre, póliza o CURP..." autocomplete="off" oninput="filtrarQualitasFinal(this.value)">
				<span id="qltBuscadorFinalConteo"></span>
			</div>

			<div class="qlt-tabla-wrap">
				<table class="qlt-tabla">
					<thead>
						<tr>
							<th>Nombre</th>
							<th>Póliza</th>
							<th>CURP</th>
						</tr>
					</thead>
					<tbody id="qltTbodyFinal">
						<tr><td colspan="3" class="qlt-vacio">Cargando...</td></tr>
					</tbody>
				</table>
			</div>
		</div>

	</div>

<?php
				$divcontenido->close();
				$divcontenedor->close();
				$divpie->close();
?>

	<script language='javascript'>
		var hc=calcula_h(125);
		var h=$(window).outerHeight() - hc;
		$('#pie').css("top",h+5);
		hc=calcula_h(85);
		h=$(window).outerHeight() - (hc*2);
		$('#contenedor').css("top",hc-15);
		$('#contenedor').css("height",h);
		hc=calcula_h(45);
		$('#menu').css("top",hc-20);
		$('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
	</script>

<?php } } ?>

	</body>

	<script type="text/javascript">

	const QLT_API = 'qualitas_api.php';

	async function qltApi(payload){
		var res = await fetch(QLT_API, {
			method:'POST',
			headers:{'Content-Type':'application/json'},
			body:JSON.stringify(payload)
		});
		return res.json();
	}

	function escaparHtmlQlt(txt){
		var div = document.createElement('div');
		div.textContent = (txt === null || txt === undefined) ? '' : String(txt);
		return div.innerHTML;
	}

	function valorQlt(v){
		return (v === null || v === undefined || v === '') ? '—' : escaparHtmlQlt(v);
	}

	/* ── Switch de panel (Formulario / Pagos) ── */
	function cambiarPanelQlt(cual){
		document.getElementById('qltPanelForm').classList.toggle('activo', cual === 'form');
		document.getElementById('qltPanelPagos').classList.toggle('activo', cual === 'pagos');
		document.getElementById('qltBtnSwitchForm').classList.toggle('activo', cual === 'form');
		document.getElementById('qltBtnSwitchPagos').classList.toggle('activo', cual === 'pagos');
	}

	/* Filas ya parseadas del xlsx en memoria, listas para mandar al
	   servidor cuando el usuario confirme con el botón "Cargar". */
	var qltFilasPendientesForm = [];
	var qltFilasPendientesPagos = [];

	/* Encabezados EXACTOS de cada xlsx (tal como los dio el usuario) --
	   si el archivo real trae otros nombres de columna, estas celdas
	   simplemente saldrán vacías y se nota en la vista previa. */
	function mapearFilaForm(fila){
		return {
			modelo:        fila['Modelo (Año)'] ?? '',
			marca:         fila['Marca'] ?? '',
			descripcion:   fila['Descripción'] ?? '',
			curp:          fila['CURP'] ?? '',
			nombre:        fila['Nombre'] ?? '',
			datos_carobra: fila['Datos Carobra'] ?? '',
			/* No es un dato que se pidió mostrar -- se manda nada más
			   para que el servidor pueda identificar "es la misma
			   respuesta de antes" y actualice en vez de duplicar al
			   recargar el mismo archivo. */
			submission_id: fila['Submission ID'] ?? '',
		};
	}

	function mapearFilaPagos(fila){
		return {
			concepto: fila['CONCEPTO'] ?? '',
			poliza:   fila['POLIZA'] ?? '',
		};
	}

	/* Lee el xlsx elegido con SheetJS (en el navegador, no se sube el
	   archivo crudo al servidor) y muestra una vista previa + botón
	   para confirmar la carga. */
	function parsearArchivoQlt(file, tipo){

		if(!file) return;

		var idResultado = (tipo === 'form') ? 'qltResultadoForm' : 'qltResultadoPagos';
		var elResultado = document.getElementById(idResultado);
		elResultado.innerHTML = '<div class="qlt-vacio">Leyendo archivo...</div>';

		var lector = new FileReader();
		lector.onload = function(e){
			try{
				var datos = new Uint8Array(e.target.result);
				var libro = XLSX.read(datos, { type: 'array' });
				var hoja = libro.Sheets[libro.SheetNames[0]];
				var filasCrudas = XLSX.utils.sheet_to_json(hoja, { defval: '' });

				var filas = filasCrudas.map(tipo === 'form' ? mapearFilaForm : mapearFilaPagos);

				if(tipo === 'form'){
					qltFilasPendientesForm = filas;
					renderPreviewQlt(elResultado, filas, ['modelo','marca','descripcion','curp','nombre','datos_carobra'],
						['Modelo','Marca','Descripción','CURP','Nombre','Datos Carobra'], 'form');
				} else {
					qltFilasPendientesPagos = filas;
					renderPreviewQlt(elResultado, filas, ['concepto','poliza'],
						['Concepto (nombre)','Póliza'], 'pagos');
				}

			} catch(err){
				elResultado.innerHTML = '<div class="qlt-vacio">No se pudo leer el archivo -- ¿seguro que es un .xlsx válido? (' + escaparHtmlQlt(err.message || err) + ')</div>';
			}
		};
		lector.readAsArrayBuffer(file);
	}

	function renderPreviewQlt(contenedor, filas, keys, labels, tipo){

		if(!filas.length){
			contenedor.innerHTML = '<div class="qlt-vacio">No se encontraron filas en el archivo.</div>';
			return;
		}

		var maxPreview = 25;
		var html = '<div class="qlt-preview-info">' +
						'<span>Se detectaron ' + filas.length.toLocaleString('es-MX') + ' registro(s)</span>' +
						'<button type="button" class="qlt-btn" onclick="confirmarCargaQlt(\'' + tipo + '\')">⬆️ Cargar ' + filas.length.toLocaleString('es-MX') + ' registro(s)</button>' +
					'</div>';

		html += '<div class="qlt-tabla-wrap"><table class="qlt-tabla"><thead><tr>';
		labels.forEach(function(l){ html += '<th>' + escaparHtmlQlt(l) + '</th>'; });
		html += '</tr></thead><tbody>';

		filas.slice(0, maxPreview).forEach(function(fila){
			html += '<tr>';
			keys.forEach(function(k){ html += '<td>' + valorQlt(fila[k]) + '</td>'; });
			html += '</tr>';
		});

		html += '</tbody></table></div>';

		if(filas.length > maxPreview){
			html += '<div class="qlt-tabla-nota">Mostrando los primeros ' + maxPreview + ' de ' + filas.length.toLocaleString('es-MX') + ' -- se cargan todos al confirmar.</div>';
		}

		contenedor.innerHTML = html;
	}

	async function confirmarCargaQlt(tipo){

		var filas = (tipo === 'form') ? qltFilasPendientesForm : qltFilasPendientesPagos;
		var idResultado = (tipo === 'form') ? 'qltResultadoForm' : 'qltResultadoPagos';
		var elResultado = document.getElementById(idResultado);

		if(!filas.length) return;

		mostrarLoaderQlt();
		try{
			var json = await qltApi({
				action: (tipo === 'form') ? 'importarForm' : 'importarPolizas',
				filas: filas
			});

			if(!json.ok){
				Swal.fire('Error', json.msg || 'No se pudo cargar el archivo.', 'error');
				return;
			}

			Swal.fire({
				icon: 'success',
				title: 'Carga completa',
				html: json.insertadas + ' nuevo(s)' +
					(json.actualizadas ? ' -- ' + json.actualizadas + ' actualizado(s) (ya existían)' : '') +
					(json.omitidas ? ' -- ' + json.omitidas + ' fila(s) vacía(s) omitida(s)' : '') + '.',
				confirmButtonColor: '#185FA5'
			});

			elResultado.innerHTML = '';
			if(tipo === 'form'){ qltFilasPendientesForm = []; document.getElementById('qltFileForm').value = ''; }
			else { qltFilasPendientesPagos = []; document.getElementById('qltFilePagos').value = ''; }

			cargarResumenQlt();

		} catch(e){
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		} finally {
			ocultarLoaderQlt();
		}
	}

	/* ── Resumen + cruce ── */

	async function cargarResumenQlt(){
		try{
			var json = await qltApi({ action: 'getResumen' });
			if(!json.ok) return;
			pintarResumenQlt(json);
		} catch(e){ /* silencioso -- no es crítico para el resto de la vista */ }
	}

	function pintarResumenQlt(json){
		document.getElementById('qltStatForm').textContent = Number(json.total_form).toLocaleString('es-MX');
		document.getElementById('qltStatPagos').textContent = Number(json.total_polizas).toLocaleString('es-MX');
		document.getElementById('qltStatQualitas').textContent = Number(json.total_qualitas).toLocaleString('es-MX');
		document.getElementById('qltStatSinMatch').textContent = Number(json.polizas_sin_match).toLocaleString('es-MX');
	}

	async function ejecutarCruceQlt(){
		var btn = document.getElementById('qltBtnCruce');
		btn.disabled = true;
		mostrarLoaderQlt();

		try{
			var json = await qltApi({ action: 'ejecutarCruce' });

			if(!json.ok){
				Swal.fire('Error', json.msg || 'No se pudo ejecutar el cruce.', 'error');
				return;
			}

			pintarResumenQlt(json);
			cargarQualitasFinal();

			Swal.fire({
				icon: 'success',
				title: 'Cruce ejecutado',
				html: json.total_qualitas + ' registro(s) en la tabla qualitas' + (json.polizas_sin_match ? ' -- ' + json.polizas_sin_match + ' póliza(s) sin match todavía' : '') + '.',
				confirmButtonColor: '#185FA5'
			});

		} catch(e){
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		} finally {
			btn.disabled = false;
			ocultarLoaderQlt();
		}
	}

	/* ── Tabla final (qualitas) ── */

	var qltFilasFinal = [];

	async function cargarQualitasFinal(){
		var tbody = document.getElementById('qltTbodyFinal');
		tbody.innerHTML = '<tr><td colspan="3" class="qlt-vacio">Cargando...</td></tr>';

		try{
			var json = await qltApi({ action: 'listarQualitas' });

			if(!json.ok){
				tbody.innerHTML = '<tr><td colspan="3" class="qlt-vacio">' + escaparHtmlQlt(json.msg || 'Error al cargar.') + '</td></tr>';
				return;
			}

			qltFilasFinal = json.filas || [];
			renderFilasQualitasFinal(qltFilasFinal);

		} catch(e){
			tbody.innerHTML = '<tr><td colspan="3" class="qlt-vacio">Error de comunicación con el servidor.</td></tr>';
		}
	}

	function renderFilasQualitasFinal(filas){
		var tbody = document.getElementById('qltTbodyFinal');

		if(!filas.length){
			tbody.innerHTML = '<tr><td colspan="3" class="qlt-vacio">Sin datos todavía -- carga los dos archivos y da clic en "Ejecutar cruce".</td></tr>';
			return;
		}

		tbody.innerHTML = filas.map(function(f){
			return '<tr>' +
				'<td>' + valorQlt(f.nombre) + '</td>' +
				'<td>' + valorQlt(f.poliza) + '</td>' +
				'<td>' + valorQlt(f.curp) + '</td>' +
			'</tr>';
		}).join('');
	}

	function filtrarQualitasFinal(texto){
		var termino = (texto || '').toString().trim().toLowerCase()
			.normalize('NFD').replace(/[̀-ͯ]/g, '');

		var conteoEl = document.getElementById('qltBuscadorFinalConteo');

		if(termino === ''){
			renderFilasQualitasFinal(qltFilasFinal);
			if(conteoEl) conteoEl.textContent = '';
			return;
		}

		var filtradas = qltFilasFinal.filter(function(f){
			return ['nombre','poliza','curp'].some(function(k){
				var v = f[k];
				if(v === null || v === undefined) return false;
				var vNorm = v.toString().toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g, '');
				return vNorm.indexOf(termino) !== -1;
			});
		});

		renderFilasQualitasFinal(filtradas);
		if(conteoEl) conteoEl.textContent = filtradas.length + ' de ' + qltFilasFinal.length;
	}

	function mostrarLoaderQlt(){ var l = document.getElementById('loader'); if(l) l.style.display = 'flex'; }
	function ocultarLoaderQlt(){ var l = document.getElementById('loader'); if(l) l.style.display = 'none'; }

	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		var elSalir = document.getElementById('salir');
		if(elSalir) elSalir.addEventListener('click', function(){ window.location = 'https://www.sis-ca.com.mx:7863/'; });

		var elEmail = document.getElementById('email');
		if(elEmail) elEmail.addEventListener('click', function(){ window.open('http://webmail.carobra.com.mx/imp/login.php'); });

		cargarResumenQlt();
		cargarQualitasFinal();
	});

	</script>
</html>