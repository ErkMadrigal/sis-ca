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


			/* "eom-" = Estruc Otros Masivo */
			.eom-card { background:#fff; border:1px solid #e0e8ec; border-radius:12px; padding:20px 22px; margin-bottom:18px; box-shadow:0 1px 3px rgba(15,23,42,.05); }
			.eom-titulo { font-size:16px; font-weight:700; color:#14507a; margin-bottom:4px; }
			.eom-sub { font-size:12.5px; color:#8896a6; margin-bottom:14px; }
			.eom-reglas { font-size:12px; color:#5a6b7b; background:#f4f8fb; border:1px solid #dde8ef; border-radius:8px; padding:10px 14px; margin-bottom:14px; line-height:1.6; }
			.eom-drop { border:2px dashed #9cc3dd; border-radius:12px; padding:24px; text-align:center; background:#f6fafd; cursor:pointer; transition:.15s; }
			.eom-drop:hover { border-color:#14507a; background:#edf5fb; }
			.eom-drop input { display:none; }
			.eom-drop b { color:#14507a; font-size:13px; }
			.eom-drop small { display:block; color:#a3b4c2; margin-top:4px; font-size:11px; }

			.eom-opts { display:flex; align-items:center; gap:18px; flex-wrap:wrap; margin:14px 0 6px; font-size:12.5px; color:#34495e; }
			.eom-opts label { display:flex; align-items:center; gap:6px; cursor:pointer; font-weight:600; }

			.eom-btn { background:#14507a; color:#fff; border:none; border-radius:9px; padding:9px 18px; font-size:12.5px; font-weight:700; cursor:pointer; }
			.eom-btn:hover { background:#0f3f61; }
			.eom-btn:disabled { background:#b8c4cc; cursor:not-allowed; }
			.eom-btn-sec { background:#eef3f7; color:#34495e; }
			.eom-btn-sec:hover { background:#dfe8ee; }

			.eom-stats { display:flex; gap:10px; flex-wrap:wrap; margin:14px 0; }
			.eom-stat { flex:1; min-width:120px; background:#f4f8fb; border:1px solid #dde8ef; border-radius:10px; padding:10px 14px; }
			.eom-stat-v { font-size:22px; font-weight:800; color:#14507a; }
			.eom-stat-l { font-size:11px; color:#7a8a99; text-transform:uppercase; letter-spacing:.04em; }
			.eom-stat.ok .eom-stat-v { color:#1e8449; }
			.eom-stat.warn .eom-stat-v { color:#b9770e; }
			.eom-stat.bad .eom-stat-v { color:#c0392b; }

			.eom-tabla-wrap { overflow:auto; max-height:480px; border:1px solid #e0e8ec; border-radius:10px; }
			.eom-tabla { width:100%; border-collapse:collapse; font-size:12.5px; }
			.eom-tabla th { position:sticky; top:0; background:#14507a; color:#fff; padding:8px 10px; font-size:11.5px; text-transform:uppercase; letter-spacing:.03em; }
			.eom-tabla td { padding:7px 10px; border-bottom:1px solid #eef2f5; vertical-align:top; }
			.eom-tabla tr.omitido td { background:#fdf2f1; }
			.eom-chip { display:inline-block; font-size:11px; font-weight:700; padding:2px 10px; border-radius:20px; white-space:nowrap; }
			.eom-chip.insertar { background:#e3f4ea; color:#1e8449; }
			.eom-chip.actualizar { background:#e6f0fa; color:#1f6fb2; }
			.eom-chip.sin_cambio { background:#eef1f3; color:#7a8a99; }
			.eom-chip.omitido { background:#f9d9d6; color:#a52a1f; }
			.eom-motivo { color:#a52a1f; font-size:12px; }
			.eom-bloqueado { text-align:center; padding:60px 20px; color:#a33; }
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
				reg_acceso($_SESSION["nombre"],"estruc_otros_masivo");
				$_SESSION["autentifica"]="";

				/* Solo Super Usuario (id_nivel 12). El API vuelve a validarlo. */
				$conecta = new conector();
				$esSuper = false;
				$bdC = $conecta->consulta("SELECT clave FROM empl_usuarios WHERE nombre='" . utf8_decode($_SESSION["nombre"]) . "'");
				$tbC = $bdC ? mysqli_fetch_array($bdC) : null;
				if (!empty($tbC['clave']) && strtoupper(trim($tbC['clave'])) === 'DIPEREZ') {
						$esSuper = true;   /* acceso total fijo */
					} elseif (!empty($tbC['clave'])) {
					$claveEsc = mysqli_real_escape_string($conecta->conexion, $tbC['clave']);
					$rsN = $conecta->consulta("SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$claveEsc}'");
					while ($rsN && ($rn = mysqli_fetch_assoc($rsN))) {
						if (intval($rn['id_nivel']) === 12) { $esSuper = true; break; }
					}
				}

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
					titulo("Estructura Bradescard - actualización masiva");
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

					if (!$esSuper) {
?>
	<div class="container-fluid" style="padding:16px 18px; text-align:left;">
		<div class="eom-card eom-bloqueado">
			<div style="font-size:32px; margin-bottom:10px;">🔒</div>
			<div style="font-size:15px; font-weight:700; margin-bottom:6px;">No tienes permiso para esta sección</div>
			<div style="font-size:12.5px; color:#8896a6;">Se requiere Super Usuario.</div>
		</div>
	</div>
<?php
					} else {
?>
	<div class="container-fluid" style="padding:16px 18px; text-align:left;">

		<div class="eom-card">
			<div class="eom-titulo">📊 Actualización masiva — estructura Bradescard</div>
			<div class="eom-sub">Sube el xlsx con las columnas <b>NOMBRE ASESOR · nivel · GERENCIA (A-Z) · ESTATUS</b>. Primero se simula (no se escribe nada) y tú decides si aplicas.</div>

			<div class="eom-reglas">
				<b>Reglas</b> (solo cuentan filas <b>ACTIVAS</b>): 1 líder por gerencia (un líder puede estar en varias gerencias) ·
				1 coordinador por gerencia · un coordinador o promotor solo puede estar en <b>una</b> gerencia (si es coordinador y promotor,
				la misma) · promotores: los que sean por gerencia. Niveles válidos: <b>lider, coordinador, promotor</b>.
				Las filas que rompen una regla se omiten y se reportan.
			</div>

			<label class="eom-drop" for="eomFile">
				<input type="file" id="eomFile" accept=".xlsx,.xls" onchange="eomLeer(this.files[0])">
				<div style="font-size:26px;">📥</div>
				<b>Elige el archivo .xlsx</b>
				<small id="eomNombreArchivo">Ningún archivo seleccionado</small>
			</label>

			<div class="eom-opts">
				<label><input type="checkbox" id="eomDesactivar" checked onchange="eomValidar()">
					Desactivar (estatus 0) las filas anteriores de estos asesores que no vengan en el archivo</label>
			</div>
			<div style="font-size:11.5px;color:#8896a6;">Así un asesor "cambia" de gerencia/nivel sin que quede la fila vieja activa. No se borra nada, solo pasa a estatus 0.</div>
		</div>

		<div class="eom-card" id="eomResultado" style="display:none;">
			<div class="eom-stats" id="eomStats"></div>
			<div class="eom-tabla-wrap">
				<table class="eom-tabla">
					<thead><tr><th>#</th><th>Asesor</th><th>Clave</th><th>Nivel</th><th>Gerencia</th><th>Estatus</th><th>Acción</th><th>Motivo</th></tr></thead>
					<tbody id="eomTbody"></tbody>
				</table>
			</div>
			<div id="eomOtros" style="margin-top:12px;font-size:12px;color:#7a5b00;"></div>
			<div style="display:flex;gap:10px;margin-top:14px;flex-wrap:wrap;">
				<button class="eom-btn" id="eomBtnAplicar" onclick="eomAplicar()" disabled>Aplicar cambios</button>
				<button class="eom-btn eom-btn-sec" onclick="eomDescargar()">⬇ Descargar reporte (xlsx)</button>
			</div>
		</div>

	</div>
<?php
					}
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
	const EOM_API = 'estruc_otros_masivo_api.php';
	var eomFilas = [];      // filas leídas del xlsx
	var eomUltimo = null;   // última respuesta del servidor

	function eomEsc(v){ return String(v == null ? '' : v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;'); }
	function eomNormH(s){ return String(s||'').toLowerCase().normalize('NFD').replace(/[̀-ͯ]/g,'').replace(/[^a-z0-9]/g,''); }

	async function eomApi(payload){
		var res = await fetch(EOM_API, { method:'POST', headers:{'Content-Type':'application/json'}, body:JSON.stringify(payload) });
		var txt = await res.text();
		try { return JSON.parse(txt); } catch(e) { return { ok:false, msg:'Respuesta inválida del servidor: ' + txt.substring(0, 300) }; }
	}

	function eomLeer(file){
		if(!file) return;
		document.getElementById('eomNombreArchivo').textContent = file.name;
		var rd = new FileReader();
		rd.onload = function(e){
			try{
				var wb = XLSX.read(new Uint8Array(e.target.result), { type:'array' });
				var ws = wb.Sheets[wb.SheetNames[0]];
				var data = XLSX.utils.sheet_to_json(ws, { header:1, defval:'' });

				// fila de encabezados = la primera que tenga "nombre"
				var hi = data.findIndex(function(r){ return r.some(function(c){ return eomNormH(c).indexOf('nombre') === 0; }); });
				if(hi < 0){ Swal.fire('Encabezados no encontrados', 'No encontré la columna "NOMBRE ASESOR".', 'error'); return; }
				var cols = { nombre:-1, nivel:-1, gerencia:-1, estatus:-1, clave:-1 };
				data[hi].forEach(function(c, i){
					var k = eomNormH(c);
					if(k.indexOf('nombre') === 0) cols.nombre = i;
					else if(k === 'nivel') cols.nivel = i;
					else if(k.indexOf('gerencia') === 0) cols.gerencia = i;
					else if(k.indexOf('estatus') === 0) cols.estatus = i;
					else if(k === 'clave') cols.clave = i;
				});
				if(cols.nombre < 0 || cols.nivel < 0 || cols.gerencia < 0 || cols.estatus < 0){
					Swal.fire('Faltan columnas', 'Se necesitan: NOMBRE ASESOR, nivel, GERENCIA, ESTATUS.', 'error'); return;
				}
				eomFilas = [];
				for(var i = hi + 1; i < data.length; i++){
					var r = data[i];
					var nombre = String(r[cols.nombre] == null ? '' : r[cols.nombre]).trim();
					var clave  = cols.clave >= 0 ? String(r[cols.clave] == null ? '' : r[cols.clave]).trim() : '';
					if(!nombre && !clave) continue;
					eomFilas.push({
						nombre: nombre, clave: clave,
						nivel: String(r[cols.nivel] == null ? '' : r[cols.nivel]).trim(),
						gerencia: String(r[cols.gerencia] == null ? '' : r[cols.gerencia]).trim(),
						estatus: String(r[cols.estatus] == null ? '' : r[cols.estatus]).trim()
					});
				}
				if(!eomFilas.length){ Swal.fire('Archivo vacío', 'No encontré filas con datos.', 'warning'); return; }
				eomValidar();
			}catch(err){
				Swal.fire('Error', 'No se pudo leer el archivo: ' + err.message, 'error');
			}
		};
		rd.readAsArrayBuffer(file);
	}

	async function eomValidar(){
		if(!eomFilas.length) return;
		Swal.fire({ title:'Revisando reglas...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
		var r = await eomApi({ action:'validar', rows:eomFilas, desactivar_anteriores: document.getElementById('eomDesactivar').checked });
		Swal.close();
		if(!r.ok){ Swal.fire('No se pudo validar', r.msg || '', 'error'); return; }
		eomPintar(r);
	}

	var ETQ = { insertar:'Insertar', actualizar:'Actualizar', sin_cambio:'Sin cambio', omitido:'Omitido' };

	function eomPintar(r){
		eomUltimo = r;
		var s = r.resumen;
		document.getElementById('eomResultado').style.display = 'block';
		document.getElementById('eomStats').innerHTML =
			'<div class="eom-stat ok"><div class="eom-stat-v">' + s.insertar + '</div><div class="eom-stat-l">Por insertar</div></div>' +
			'<div class="eom-stat"><div class="eom-stat-v">' + s.actualizar + '</div><div class="eom-stat-l">Por actualizar</div></div>' +
			'<div class="eom-stat"><div class="eom-stat-v">' + s.sin_cambio + '</div><div class="eom-stat-l">Sin cambio</div></div>' +
			'<div class="eom-stat warn"><div class="eom-stat-v">' + s.desactivar + '</div><div class="eom-stat-l">Filas viejas a desactivar</div></div>' +
			'<div class="eom-stat bad"><div class="eom-stat-v">' + s.omitido + '</div><div class="eom-stat-l">Omitidas (conflicto/error)</div></div>';

		var html = '';
		r.rows.forEach(function(x){
			html += '<tr class="' + x.accion + '"><td>' + x.fila + '</td><td>' + eomEsc(x.nombre) + '</td><td>' + eomEsc(x.clave) +
				'</td><td>' + eomEsc(x.nivel || x.nivel_in) + '</td><td>' + eomEsc(x.gerencia || x.ger_in) +
				'</td><td>' + (x.estatus === 1 ? 'ACTIVO' : (x.estatus === 0 ? 'BAJA' : eomEsc(x.est_in))) +
				'</td><td><span class="eom-chip ' + x.accion + '">' + ETQ[x.accion] + '</span></td><td class="eom-motivo">' + eomEsc(x.motivo) + '</td></tr>';
		});
		document.getElementById('eomTbody').innerHTML = html;

		var otros = document.getElementById('eomOtros');
		if(r.otros && r.otros.length){
			otros.innerHTML = '<b>Filas anteriores que pasarán a estatus 0:</b> ' + r.otros.map(function(o){
				return eomEsc((o.nombre || o.clave) + ' (' + o.nivel + ' ' + o.gerencia + ')');
			}).join(' · ');
		} else otros.innerHTML = '';

		document.getElementById('eomBtnAplicar').disabled = (s.insertar + s.actualizar + s.desactivar) === 0;
	}

	async function eomAplicar(){
		var s = eomUltimo.resumen;
		var c = await Swal.fire({
			icon:'question', title:'¿Aplicar cambios?',
			html: s.insertar + ' inserciones · ' + s.actualizar + ' actualizaciones · ' + s.desactivar + ' filas viejas a estatus 0.<br>' +
				  (s.omitido ? '<span style="color:#a52a1f">' + s.omitido + ' filas omitidas no se aplican.</span><br>' : '') +
				  '<small>Hazlo después de tu respaldo de la tabla.</small>',
			showCancelButton:true, confirmButtonText:'Sí, aplicar', cancelButtonText:'Cancelar'
		});
		if(!c.isConfirmed) return;

		Swal.fire({ title:'Aplicando...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });
		var r = await eomApi({ action:'aplicar', rows:eomFilas, desactivar_anteriores: document.getElementById('eomDesactivar').checked });
		Swal.close();
		if(!r.ok){ Swal.fire('No se aplicó', r.msg || '', 'error'); return; }
		eomPintar(r);
		document.getElementById('eomBtnAplicar').disabled = true;
		Swal.fire({ icon:'success', title:'Listo', text: r.resumen.insertar + ' insertadas, ' + r.resumen.actualizar + ' actualizadas, ' + r.resumen.desactivar + ' desactivadas.' });
	}

	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		var elSalir = document.getElementById('salir');
		if(elSalir) elSalir.addEventListener('click', function(){ window.location = 'https://www.sis-ca.com.mx:7863/'; });

		var elEmail = document.getElementById('email');
		if(elEmail) elEmail.addEventListener('click', function(){ window.open('http://webmail.carobra.com.mx/imp/login.php'); });
	});

	function eomDescargar(){
		if(!eomUltimo) return;
		var filas = eomUltimo.rows.map(function(x){
			return { Fila:x.fila, Asesor:x.nombre, Clave:x.clave, Nivel:x.nivel || x.nivel_in, Gerencia:x.gerencia || x.ger_in,
					 Estatus:(x.estatus === 1 ? 'ACTIVO' : (x.estatus === 0 ? 'BAJA' : x.est_in)), Accion:ETQ[x.accion], Motivo:x.motivo };
		});
		var ws = XLSX.utils.json_to_sheet(filas);
		var wb = XLSX.utils.book_new();
		XLSX.utils.book_append_sheet(wb, ws, 'Reporte');
		XLSX.writeFile(wb, 'reporte_estructura_bradescard.xlsx');
	}
	</script>
</html>