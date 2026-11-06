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
			#contenedor { overflow-y:auto !important; }

			#loader {
				display:none; position:fixed; top:0; left:0; width:100%; height:100%;
				background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;
			}

			/* ── "cme-" = Carga Masiva de Empleados ── */

			.cme-card {
				background:#fff; border:1px solid #e0e8ec; border-radius:12px;
				padding:20px 22px; margin-bottom:18px; box-shadow:0 1px 3px rgba(15,23,42,.05);
			}

			.cme-titulo { font-size:16px; font-weight:700; color:#1e6b3c; margin-bottom:4px; }
			.cme-subtitulo { font-size:12.5px; color:#8896a6; margin-bottom:16px; }

			.cme-drop {
				border:2px dashed #a5d6a7; border-radius:12px; padding:26px; text-align:center;
				background:#f7fbf8; cursor:pointer; transition:.15s;
			}
			.cme-drop:hover { border-color:#2e7d32; background:#eef8ef; }
			.cme-drop input { display:none; }
			.cme-drop-icono { font-size:28px; margin-bottom:6px; }
			.cme-drop-texto { font-size:13px; color:#2e7d32; font-weight:600; }
			.cme-drop-sub { font-size:11px; color:#a3b4c2; margin-top:4px; }

			.cme-preview-info {
				display:flex; align-items:center; justify-content:space-between; gap:12px;
				margin-top:16px; margin-bottom:10px; flex-wrap:wrap;
			}
			.cme-preview-info span { font-size:12.5px; color:#1f2d3d; font-weight:600; }

			.cme-btn {
				background:#2e7d32; color:#fff; border:none; border-radius:9px; padding:9px 18px;
				font-size:12.5px; font-weight:700; cursor:pointer; transition:.15s;
			}
			.cme-btn:hover { background:#1e6b3c; }
			.cme-btn:disabled { background:#c7d7e5; cursor:not-allowed; }
			.cme-btn-secundario {
				background:#eff6ff; color:#185FA5; border:none; border-radius:9px; padding:9px 18px;
				font-size:12.5px; font-weight:700; cursor:pointer; transition:.15s;
			}
			.cme-btn-secundario:hover { background:#dbeafe; }

			.cme-tabla-wrap { border:1px solid #e0e8ec; border-radius:10px; overflow:auto; max-height:380px; }
			.cme-tabla { width:100%; border-collapse:collapse; font-size:12px; }
			.cme-tabla thead th {
				background:#eef8ef; color:#1e6b3c; font-weight:700; font-size:10.5px;
				text-transform:uppercase; letter-spacing:.03em; padding:8px 10px; text-align:left;
				position:sticky; top:0; white-space:nowrap;
			}
			.cme-tabla tbody td { padding:7px 10px; border-bottom:1px solid #eef2f4; white-space:nowrap; color:#1f2d3d; }
			.cme-tabla tbody tr:hover td { background:#f7fbfc; }
			.cme-tabla-nota { font-size:11px; color:#a3b4c2; padding:8px 4px 0; }

			.cme-vacio { text-align:center; padding:26px; color:#aaa; font-size:13px; }

			.cme-fila-omitida td { background:#fff5f5 !important; color:#a33 !important; }
			.cme-fila-advertencia td { background:#fffbea !important; }

			.cme-stats { display:grid; grid-template-columns:repeat(3, 1fr); gap:14px; margin-bottom:16px; }
			.cme-stat {
				background:#f7fbf8; border:1px solid #e0e8ec; border-radius:10px; padding:14px 16px;
			}
			.cme-stat-valor { font-size:22px; font-weight:800; color:#1e6b3c; }
			.cme-stat-label { font-size:11px; color:#5b9c6d; font-weight:600; text-transform:uppercase; letter-spacing:.02em; margin-top:2px; }
			.cme-stat.cme-stat-alerta .cme-stat-valor { color:#a06b00; }

			.cme-columnas {
				display:grid; grid-template-columns:repeat(auto-fill, minmax(230px, 1fr)); gap:6px 18px;
				font-size:12px; color:#4a5568; margin-top:6px;
			}
			.cme-columnas li { list-style:none; }
			.cme-columnas b { color:#1e6b3c; }
			.cme-columnas .cme-opcional { color:#a3b4c2; font-weight:400; }

			.cme-bloqueado { text-align:center; padding:60px 20px; color:#a33; }
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
				reg_acceso($_SESSION["nombre"],"carga_masiva_empleados");
				$_SESSION["autentifica"]="";

				/* ── Permiso: mismo requisito que "+ Nuevo empleado" en
				   seg_categorias_nap.php (Super Usuario o pestaña "usuario").
				   Esto es la puerta real -- a diferencia del botón de la
				   barra superior (que solo oculta en el cliente), aquí SÍ
				   se bloquea el contenido si no se cumple, porque esta
				   página se puede abrir directo por URL. El backend
				   (RRHH_api.php, acción importarEmpleadosMasivo) vuelve a
				   validar todo esto de nuevo antes de insertar nada. */
				$conecta = new conector();
				$queryClaveSesion = "SELECT clave FROM empl_usuarios WHERE nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
				$bdClaveSesion    = $conecta->consulta($queryClaveSesion);
				$tbClaveSesion    = mysqli_fetch_array($bdClaveSesion);
				$claveSesionCM    = $tbClaveSesion['clave'];

				$es_superusuario_cm     = false;
				$puede_carga_masiva_cm  = false;

				if (!empty($claveSesionCM)) {
					$claveSesionCMEsc = mysqli_real_escape_string($conecta->conexion, $claveSesionCM);

					$rsNivelCM = $conecta->consulta(
						"SELECT id_nivel FROM carobra_2014.usuarios_nivel WHERE clave='{$claveSesionCMEsc}'"
					);
					if ($rsNivelCM) {
						while ($rowNivelCM = mysqli_fetch_assoc($rsNivelCM)) {
							if (intval($rowNivelCM['id_nivel']) === 12) {
								$es_superusuario_cm = true;
								break;
							}
						}
					}

					if (!$es_superusuario_cm) {
						$rsPermCM = $conecta->consulta(
							"SELECT pestana FROM carobra_2014.permisos_pestanas WHERE clave='{$claveSesionCMEsc}' AND pestana='usuario'"
						);
						if ($rsPermCM && mysqli_num_rows($rsPermCM) > 0) {
							$puede_carga_masiva_cm = true;
						}
					}
				}

				$puede_carga_masiva_cm = $puede_carga_masiva_cm || $es_superusuario_cm;

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
					titulo("Carga masiva de empleados");
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

					if (!$puede_carga_masiva_cm) {
?>
	<div class="container-fluid" style="padding:16px 18px; text-align:left;">
		<div class="cme-card cme-bloqueado">
			<div style="font-size:32px; margin-bottom:10px;">🔒</div>
			<div style="font-size:15px; font-weight:700; margin-bottom:6px;">No tienes permiso para esta sección</div>
			<div style="font-size:12.5px; color:#8896a6;">Se requiere el mismo permiso que "+ Nuevo empleado" en RRHH. Si crees que deberías tener acceso, contacta al administrador del sistema.</div>
		</div>
	</div>
<?php
					} else {
?>

	<div class="container-fluid" style="padding:16px 18px; text-align:left;">

		<div class="cme-card">
			<div class="cme-titulo">📤 Carga masiva de empleados -- alta desde xlsx</div>
			<div class="cme-subtitulo">
				Sube un archivo .xlsx con un empleado por fila. Por cada fila válida se da de alta el
				usuario y el empleado igual que en "+ Nuevo empleado" -- se genera <strong>clave</strong> y
				<strong>contraseña</strong> automáticamente (sin repetir) y se manda el correo de bienvenida
				con esos datos, igual que en el alta individual.
			</div>

			<div class="cme-subtitulo" style="margin-bottom:14px;">
				<b>Columnas del xlsx</b> (los nombres de encabezado deben coincidir con estos, mayúsculas/acentos no importan):
				<ul class="cme-columnas">
					<li><b>Nombre(s)</b> -- obligatorio</li>
					<li><b>Apellido Paterno</b> -- obligatorio</li>
					<li><b>Apellido Materno</b> -- obligatorio</li>
					<li><b>Correo</b> -- obligatorio</li>
					<li class="cme-opcional">Email Institucional</li>
					<li class="cme-opcional">NSS</li>
					<li class="cme-opcional">RFC</li>
					<li class="cme-opcional">CURP</li>
					<li class="cme-opcional">Teléfono</li>
					<li class="cme-opcional">Fecha Ingreso</li>
					<li class="cme-opcional">CLABE Interbancaria</li>
					<li class="cme-opcional">Banco</li>
					<li class="cme-opcional">Nombre Cuentahabiente</li>
					<li class="cme-opcional">Estructura</li>
					<li class="cme-opcional">Nivel</li>
					<li class="cme-opcional">Régimen Fiscal</li>
					<li class="cme-opcional">Factura (Sí/No)</li>
					<li class="cme-opcional">Dto. NAP-Factura (Sí/No)</li>
					<li class="cme-opcional">Dto. NAP-Gerencia (Sí/No)</li>
					<li class="cme-opcional">Asesoría Bernal (Sí/No)</li>
					<li class="cme-opcional">Contacto Emergencia Nombre</li>
					<li class="cme-opcional">Contacto Emergencia Teléfono</li>
				</ul>
			</div>

			<label class="cme-drop" id="cmeDrop">
				<input type="file" id="cmeFile" accept=".xlsx,.xls" onchange="parsearArchivoCme(this.files[0])">
				<div class="cme-drop-icono">📄</div>
				<div class="cme-drop-texto">Da clic para elegir el archivo .xlsx de empleados</div>
				<div class="cme-drop-sub">El archivo se lee en tu navegador -- nada se sube hasta que confirmes la carga</div>
			</label>

			<div id="cmeResultado"></div>
		</div>

		<div class="cme-card" id="cmeCardResumen" style="display:none;">
			<div class="cme-titulo">✅ Resultado de la carga</div>
			<div class="cme-stats">
				<div class="cme-stat">
					<div class="cme-stat-valor" id="cmeStatProcesados">—</div>
					<div class="cme-stat-label">Filas procesadas</div>
				</div>
				<div class="cme-stat">
					<div class="cme-stat-valor" id="cmeStatInsertados">—</div>
					<div class="cme-stat-label">Empleados dados de alta</div>
				</div>
				<div class="cme-stat cme-stat-alerta">
					<div class="cme-stat-valor" id="cmeStatOmitidos">—</div>
					<div class="cme-stat-label">Filas omitidas</div>
				</div>
			</div>

			<div id="cmeDetalleResultado"></div>
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

	const CME_API = 'RRHH_api.php';

	async function cmeApi(payload){
		var res = await fetch(CME_API, {
			method:'POST',
			headers:{'Content-Type':'application/json'},
			body:JSON.stringify(payload)
		});
		return res.json();
	}

	function escaparHtmlCme(txt){
		var div = document.createElement('div');
		div.textContent = (txt === null || txt === undefined) ? '' : String(txt);
		return div.innerHTML;
	}

	function valorCme(v){
		return (v === null || v === undefined || v === '') ? '—' : escaparHtmlCme(v);
	}

	/* Normaliza un encabezado de columna del xlsx para poder emparejarlo
	   sin importar mayúsculas, acentos, paréntesis o espacios extra --
	   así "Nombre(s)" y "nombre s" y "NOMBRE(S)" caen en la misma llave. */
	function limpiarEncabezadoCme(txt){
		return (txt || '').toString()
			.toUpperCase()
			.normalize('NFD').replace(/[̀-ͯ]/g, '')
			.replace(/[^A-Z0-9]+/g, ' ')
			.trim()
			.replace(/\s+/g, ' ');
	}

	/* Encabezado normalizado -> llave interna que espera el backend
	   (acción importarEmpleadosMasivo en RRHH_api.php). Se aceptan un
	   par de alias comunes por columna para tolerar variaciones. */
	const CME_MAPA_ENCABEZADOS = {
		'NOMBRE S':                     'nombres',
		'NOMBRES':                      'nombres',
		'NOMBRE':                       'nombres',
		'APELLIDO PATERNO':             'apellido_paterno',
		'AP PATERNO':                   'apellido_paterno',
		'APELLIDO MATERNO':             'apellido_materno',
		'AP MATERNO':                   'apellido_materno',
		'CORREO':                       'correo',
		'EMAIL':                        'correo',
		'EMAIL PERSONAL':               'correo',
		'EMAIL INSTITUCIONAL':          'email_institucional',
		'CORREO INSTITUCIONAL':         'email_institucional',
		'NSS':                          'nss',
		'RFC':                          'rfc',
		'CURP':                         'curp',
		'TELEFONO':                     'telefono',
		'CELULAR':                      'telefono',
		'FECHA INGRESO':                'fecha_ingreso',
		'FEC INGRESO':                  'fecha_ingreso',
		'CLABE INTERBANCARIA':          'clabe',
		'CLABE':                        'clabe',
		'BANCO':                        'banco',
		'NOMBRE CUENTAHABIENTE':        'nombre_cuentahabiente',
		'NOMBRE CUENTABIENTE':          'nombre_cuentahabiente',
		'ESTRUCTURA':                   'estructura',
		'NIVEL':                        'nivel',
		'REGIMEN FISCAL':               'regimen_fiscal',
		'FACTURA':                      'factura',
		'DTO NAP FACTURA':              'nap_factura',
		'DTO NAP-FACTURA':              'nap_factura',
		'DTO NAP GERENCIA':             'nap_gerencia',
		'ASESORIA BERNAL':              'ase_bernal',
		'CONTACTO EMERGENCIA NOMBRE':   'contacto_emergencia_nombre',
		'CONTACTO EMERGENCIA':          'contacto_emergencia_nombre',
		'CONTACTO EMERGENCIA TELEFONO': 'contacto_emergencia_telefono',
		'CONTACTO EMERGENCIA NUMERO':   'contacto_emergencia_telefono',
	};

	const CME_COLUMNAS_PREVIEW = [
		{ key:'nombres',               label:'Nombre(s)' },
		{ key:'apellido_paterno',      label:'Ap. Paterno' },
		{ key:'apellido_materno',      label:'Ap. Materno' },
		{ key:'correo',                label:'Correo' },
		{ key:'nss',                   label:'NSS' },
		{ key:'rfc',                   label:'RFC' },
		{ key:'curp',                  label:'CURP' },
		{ key:'nivel',                 label:'Nivel' },
		{ key:'estructura',            label:'Estructura' },
		{ key:'banco',                 label:'Banco' },
	];

	/* Filas ya parseadas del xlsx en memoria, listas para mandar al
	   servidor cuando el usuario confirme con el botón "Cargar". */
	var cmeFilasPendientes = [];

	function mapearFilaEmpleadoCme(filaCruda){
		var fila = {};
		Object.keys(filaCruda).forEach(function(encabezadoOriginal){
			var llave = CME_MAPA_ENCABEZADOS[limpiarEncabezadoCme(encabezadoOriginal)];
			if(llave && fila[llave] === undefined){
				fila[llave] = (filaCruda[encabezadoOriginal] === null || filaCruda[encabezadoOriginal] === undefined)
					? '' : String(filaCruda[encabezadoOriginal]).trim();
			}
		});
		/* Todas las llaves esperadas presentes aunque vengan vacías --
		   así el preview y el backend no tienen que checar undefined. */
		['nombres','apellido_paterno','apellido_materno','correo','email_institucional','nss','rfc','curp',
		 'telefono','fecha_ingreso','clabe','banco','nombre_cuentahabiente','estructura','nivel','regimen_fiscal',
		 'factura','nap_factura','nap_gerencia','ase_bernal','contacto_emergencia_nombre','contacto_emergencia_telefono']
			.forEach(function(k){ if(fila[k] === undefined) fila[k] = ''; });
		return fila;
	}

	/* Lee el xlsx elegido con SheetJS (en el navegador, no se sube el
	   archivo crudo al servidor) y muestra una vista previa + botón
	   para confirmar la carga. */
	function parsearArchivoCme(file){

		if(!file) return;

		document.getElementById('cmeCardResumen').style.display = 'none';

		var elResultado = document.getElementById('cmeResultado');
		elResultado.innerHTML = '<div class="cme-vacio">Leyendo archivo...</div>';

		var lector = new FileReader();
		lector.onload = function(e){
			try{
				var datos = new Uint8Array(e.target.result);
				var libro = XLSX.read(datos, { type: 'array' });
				var hoja = libro.Sheets[libro.SheetNames[0]];
				var filasCrudas = XLSX.utils.sheet_to_json(hoja, { defval: '' });

				var filas = filasCrudas.map(mapearFilaEmpleadoCme);

				cmeFilasPendientes = filas;
				renderPreviewCme(elResultado, filas);

			} catch(err){
				elResultado.innerHTML = '<div class="cme-vacio">No se pudo leer el archivo -- ¿seguro que es un .xlsx válido? (' + escaparHtmlCme(err.message || err) + ')</div>';
			}
		};
		lector.readAsArrayBuffer(file);
	}

	function renderPreviewCme(contenedor, filas){

		if(!filas.length){
			contenedor.innerHTML = '<div class="cme-vacio">No se encontraron filas en el archivo.</div>';
			return;
		}

		var maxPreview = 25;
		var sinNombreOCorreo = filas.filter(function(f){
			return !f.nombres || !f.apellido_paterno || !f.apellido_materno || !f.correo;
		}).length;

		var html = '<div class="cme-preview-info">' +
						'<span>Se detectaron ' + filas.length.toLocaleString('es-MX') + ' registro(s)' +
							(sinNombreOCorreo ? ' -- <span style="color:#a06b00;">' + sinNombreOCorreo + ' con campos obligatorios vacíos (se omitirán)</span>' : '') +
						'</span>' +
						'<button type="button" class="cme-btn" onclick="confirmarCargaCme()">⬆️ Cargar ' + filas.length.toLocaleString('es-MX') + ' registro(s)</button>' +
					'</div>';

		html += '<div class="cme-tabla-wrap"><table class="cme-tabla"><thead><tr>';
		CME_COLUMNAS_PREVIEW.forEach(function(c){ html += '<th>' + escaparHtmlCme(c.label) + '</th>'; });
		html += '</tr></thead><tbody>';

		filas.slice(0, maxPreview).forEach(function(fila){
			var faltaObligatorio = !fila.nombres || !fila.apellido_paterno || !fila.apellido_materno || !fila.correo;
			html += '<tr' + (faltaObligatorio ? ' class="cme-fila-omitida"' : '') + '>';
			CME_COLUMNAS_PREVIEW.forEach(function(c){ html += '<td>' + valorCme(fila[c.key]) + '</td>'; });
			html += '</tr>';
		});

		html += '</tbody></table></div>';

		if(filas.length > maxPreview){
			html += '<div class="cme-tabla-nota">Mostrando los primeros ' + maxPreview + ' de ' + filas.length.toLocaleString('es-MX') + ' -- se procesan todos al confirmar.</div>';
		}
		html += '<div class="cme-tabla-nota">Filas en rojo: les falta Nombre(s), Apellido Paterno, Apellido Materno o Correo -- se omitirán automáticamente.</div>';

		contenedor.innerHTML = html;
	}

	async function confirmarCargaCme(){

		if(!cmeFilasPendientes.length) return;

		var confirmacion = await Swal.fire({
			icon: 'warning',
			title: '¿Confirmar carga masiva?',
			html: 'Se va a intentar dar de alta <b>' + cmeFilasPendientes.length.toLocaleString('es-MX') + '</b> empleado(s). ' +
				  'A cada uno se le va a generar su clave y contraseña, y se le va a mandar el correo de bienvenida. Esta acción no se puede deshacer.',
			showCancelButton: true,
			confirmButtonText: 'Sí, cargar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#2e7d32'
		});
		if(!confirmacion.isConfirmed) return;

		var elResultado = document.getElementById('cmeResultado');

		mostrarLoaderCme();
		try{
			var json = await cmeApi({
				action: 'importarEmpleadosMasivo',
				filas: cmeFilasPendientes
			});

			if(!json.ok){
				Swal.fire('Error', json.msg || 'No se pudo procesar el archivo.', 'error');
				return;
			}

			Swal.fire({
				icon: 'success',
				title: 'Carga completa',
				html: escaparHtmlCme(json.msg || ''),
				confirmButtonColor: '#2e7d32'
			});

			pintarResumenCme(json);

			elResultado.innerHTML = '';
			cmeFilasPendientes = [];
			document.getElementById('cmeFile').value = '';

		} catch(e){
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		} finally {
			ocultarLoaderCme();
		}
	}

	function pintarResumenCme(json){
		document.getElementById('cmeCardResumen').style.display = 'block';
		document.getElementById('cmeStatProcesados').textContent = Number(json.procesados || 0).toLocaleString('es-MX');
		document.getElementById('cmeStatInsertados').textContent = Number(json.insertados || 0).toLocaleString('es-MX');
		document.getElementById('cmeStatOmitidos').textContent = Number(json.omitidos || 0).toLocaleString('es-MX');

		var detalle = document.getElementById('cmeDetalleResultado');
		var html = '';

		var resultados = json.resultados || [];
		if(resultados.length){
			html += '<div style="font-size:12.5px; font-weight:700; color:#1e6b3c; margin:14px 0 8px;">Empleados dados de alta</div>';
			html += '<div class="cme-tabla-wrap"><table class="cme-tabla"><thead><tr>' +
						'<th>Fila</th><th>Clave</th><th>Nombre</th><th>Correo</th><th>Advertencias</th>' +
					'</tr></thead><tbody>';
			resultados.forEach(function(r){
				var tieneAdvertencias = Array.isArray(r.advertencias) && r.advertencias.length;
				html += '<tr' + (tieneAdvertencias ? ' class="cme-fila-advertencia"' : '') + '>' +
					'<td>' + valorCme(r.fila) + '</td>' +
					'<td><b>' + valorCme(r.clave) + '</b></td>' +
					'<td>' + valorCme(r.nombre) + '</td>' +
					'<td>' + valorCme(r.email) + '</td>' +
					'<td>' + (tieneAdvertencias ? escaparHtmlCme(r.advertencias.join(' · ')) : '—') + '</td>' +
				'</tr>';
			});
			html += '</tbody></table></div>';
		}

		var omitidosDetalle = json.omitidosDetalle || [];
		if(omitidosDetalle.length){
			html += '<div style="font-size:12.5px; font-weight:700; color:#a33; margin:14px 0 8px;">Filas omitidas</div>';
			html += '<div class="cme-tabla-wrap"><table class="cme-tabla"><thead><tr>' +
						'<th>Fila</th><th>Motivo</th>' +
					'</tr></thead><tbody>';
			omitidosDetalle.forEach(function(o){
				html += '<tr class="cme-fila-omitida">' +
					'<td>' + valorCme(o.fila) + '</td>' +
					'<td>' + valorCme(o.motivo) + '</td>' +
				'</tr>';
			});
			html += '</tbody></table></div>';
		}

		detalle.innerHTML = html;
	}

	function mostrarLoaderCme(){ var l = document.getElementById('loader'); if(l) l.style.display = 'flex'; }
	function ocultarLoaderCme(){ var l = document.getElementById('loader'); if(l) l.style.display = 'none'; }

	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		var elSalir = document.getElementById('salir');
		if(elSalir) elSalir.addEventListener('click', function(){ window.location = 'https://www.sis-ca.com.mx:7863/'; });

		var elEmail = document.getElementById('email');
		if(elEmail) elEmail.addEventListener('click', function(){ window.open('http://webmail.carobra.com.mx/imp/login.php'); });
	});

	</script>
</html>