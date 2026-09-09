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
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<style>
			#contenido, #contenido * { text-align: left; }

			/* ── Barra superior (mismo patrón de validacion.php) ── */
			#barra-top-notif {
				display: flex;
				align-items: center;
				justify-content: space-between;
				padding: 14px 18px;
				background: #fff;
				border-bottom: 1px solid #ddd;
				gap: 14px;
				flex-wrap: wrap;
			}

			.notif-btn-nueva {
				background: #2B73B6;
				color: #fff;
				border: none;
				border-radius: 8px;
				padding: 10px 20px;
				font-size: 13.5px;
				font-weight: 700;
				cursor: pointer;
				display: inline-flex;
				align-items: center;
				gap: 8px;
				transition: background .15s;
			}
			.notif-btn-nueva:hover { background: #1a5278; }

			.notif-filtros { display: flex; gap: 8px; flex-wrap: wrap; }

			.val-pill {
				padding: 7px 16px; border-radius: 20px; font-size: 12.5px; font-weight: 600;
				cursor: pointer; border: 1px solid #d8e2e8; background: #f4f7f9; color: #5a7385;
				transition: all .15s; white-space: nowrap;
			}
			.val-pill:hover { background: #e8f0f5; }
			.val-pill.activa { background: #2B73B6; color: #fff; border-color: #2B73B6; }

			/* ── Lista de notificaciones ── */
			#notif-lista {
				padding: 16px 18px;
				max-height: calc(100vh - 280px);
				overflow-y: auto;
			}
			#notif-lista::-webkit-scrollbar { width: 8px; }
			#notif-lista::-webkit-scrollbar-thumb { background: #c0d4e0; border-radius: 4px; }
			#notif-lista::-webkit-scrollbar-track { background: #f0f4f6; }

			.notif-card {
				background: #fff; border: 1px solid #e4eaee; border-radius: 12px;
				margin-bottom: 12px; padding: 14px 18px; transition: box-shadow .15s;
				display: flex; align-items: center; gap: 14px;
			}
			.notif-card:hover { box-shadow: 0 3px 14px rgba(0,0,0,.06); }

			.notif-icon {
				width: 42px; height: 42px; border-radius: 50%;
				background: linear-gradient(135deg, #2B73B6, #1a5278);
				color: #fff; display: flex; align-items: center; justify-content: center;
				font-size: 18px; flex-shrink: 0;
			}

			.notif-info { flex: 1; min-width: 0; }
			.notif-titulo { font-size: 14px; font-weight: 700; color: #1a1a2e; }
			.notif-mensaje-prev {
				font-size: 12px; color: #888; margin-top: 2px;
				white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 480px;
			}
			.notif-meta { font-size: 11.5px; color: #a0adb5; margin-top: 4px; display: flex; gap: 12px; flex-wrap: wrap; }

			.val-badge {
				font-size: 10.5px; font-weight: 700; padding: 3px 10px; border-radius: 20px;
				text-transform: uppercase; letter-spacing: .03em; white-space: nowrap;
			}
			.badge-todos    { background: #e6f1fb; color: #1a6785; }
			.badge-socio    { background: #e8f9ef; color: #1e8e58; }
			.badge-gerente  { background: #fdf3e6; color: #b87a16; }
			.badge-broker   { background: #eef0fb; color: #4a4ab8; }
			.badge-personal { background: #f0f0f0; color: #666; }

			.badge-programada  { background: #f0f0f0; color: #666; }
			.badge-enviando    { background: #e6f1fb; color: #1a6785; }
			.badge-completada  { background: #e8f9ef; color: #1e8e58; }
			.badge-cancelada   { background: #fdeeee; color: #c0392b; }

			.notif-progreso { font-size: 12px; color: #5a7385; font-weight: 600; white-space: nowrap; }
			.notif-btn-cancelar {
				border: 1px solid #f3c4c4; background: #fff; color: #c0392b;
				border-radius: 6px; padding: 5px 12px; font-size: 11.5px; font-weight: 600; cursor: pointer;
			}
			.notif-btn-cancelar:hover { background: #fdeeee; }

			.val-estado-vacio, .val-estado-cargando {
				text-align: center; padding: 60px 20px; color: #aaa; font-size: 13.5px;
			}
			.val-spinner {
				display: inline-block; width: 14px; height: 14px; border: 2px solid #ccc;
				border-top-color: #2B73B6; border-radius: 50%; animation: val-spin .7s linear infinite;
				vertical-align: -2px; margin-right: 6px;
			}
			@keyframes val-spin { to { transform: rotate(360deg); } }

			/* ── Modal de nueva notificación ── */
			.notif-modal-overlay {
				display: none;
				position: fixed; inset: 0; background: rgba(10,18,32,.55);
				z-index: 99999; justify-content: center; align-items: flex-start;
				padding: 40px 20px; overflow-y: auto;
			}
			.notif-modal {
				background: #fff; border-radius: 14px; width: 100%; max-width: 560px;
				box-shadow: 0 20px 60px rgba(0,0,0,.25); overflow: hidden;
			}
			.notif-modal-head {
				display: flex; align-items: center; justify-content: space-between;
				padding: 18px 22px; border-bottom: 1px solid #eef2f4;
			}
			.notif-modal-head h5 { margin: 0; font-size: 16px; font-weight: 700; color: #1a1a2e; }
			.notif-modal-close { background: none; border: none; font-size: 20px; color: #aaa; cursor: pointer; }
			.notif-modal-body { padding: 22px; display: flex; flex-direction: column; gap: 16px; }

			.notif-field label {
				font-size: 12px; font-weight: 700; color: #5a7385; text-transform: uppercase;
				letter-spacing: .03em; margin-bottom: 6px; display: block;
			}
			.notif-field input[type="text"],
			.notif-field textarea,
			.notif-field input[type="date"],
			.notif-field input[type="time"] {
				width: 100%; box-sizing: border-box; border: 1px solid #d0d0d0; border-radius: 8px;
				padding: 9px 12px; font-size: 13.5px; color: #222; outline: none; transition: border .2s;
			}
			.notif-field input:focus, .notif-field textarea:focus {
				border-color: #2B73B6; box-shadow: 0 0 0 3px rgba(43,115,182,.12);
			}

			.notif-tipo-grid { display: grid; grid-template-columns: repeat(5, 1fr); gap: 8px; }
			.notif-tipo-opt {
				display: flex; flex-direction: column; align-items: center; gap: 5px;
				padding: 12px 6px; border-radius: 10px; border: 1.5px solid #e4eaee;
				background: #f9fbfc; color: #5a7385; cursor: pointer; font-size: 11px; transition: all .15s;
			}
			.notif-tipo-opt:hover { border-color: #2B73B6; }
			.notif-tipo-opt.activo { border-color: #2B73B6; background: #e6f1fb; color: #2B73B6; font-weight: 700; }
			.notif-tipo-opt svg { width: 18px; height: 18px; }

			.notif-usuario-wrap { position: relative; }
			.notif-usuario-resultados {
				position: absolute; top: 100%; left: 0; right: 0; z-index: 10; margin-top: 4px;
				background: #fff; border: 1px solid #e4eaee; border-radius: 8px; max-height: 180px;
				overflow-y: auto; box-shadow: 0 4px 14px rgba(0,0,0,.08);
			}
			.notif-usuario-item { padding: 8px 12px; font-size: 13px; cursor: pointer; display: flex; justify-content: space-between; }
			.notif-usuario-item:hover { background: #f4f7f9; }
			.notif-usuario-chip {
				display: inline-flex; align-items: center; gap: 6px; padding: 6px 12px; border-radius: 20px;
				background: #e6f1fb; color: #1a6785; font-size: 12.5px; width: fit-content; margin-top: 8px; font-weight: 600;
			}
			.notif-usuario-chip button { background: none; border: none; color: #1a6785; cursor: pointer; font-size: 14px; }

			.notif-cuando-toggle { display: flex; gap: 8px; }
			.notif-cuando-opt {
				display: flex; align-items: center; gap: 6px; padding: 9px 16px; border-radius: 8px;
				border: 1.5px solid #e4eaee; background: #f9fbfc; color: #5a7385; cursor: pointer; font-size: 12.5px; font-weight: 600;
			}
			.notif-cuando-opt.activo { border-color: #2B73B6; background: #e6f1fb; color: #2B73B6; }

			.notif-adjunto-drop {
				border: 2px dashed #d8e2e8; border-radius: 10px; padding: 20px; text-align: center;
				cursor: pointer; background: #f9fbfc; transition: all .15s; font-size: 12.5px; color: #8a9aa5;
			}
			.notif-adjunto-drop:hover { border-color: #2B73B6; background: #e6f1fb; }
			.notif-adjunto-chip {
				display: flex; align-items: center; justify-content: space-between; gap: 8px;
				background: #e8f9ef; color: #1e8e58; border-radius: 8px; padding: 8px 12px; font-size: 12.5px; font-weight: 600;
			}
			.notif-adjunto-chip button { background: none; border: none; color: #1e8e58; cursor: pointer; }

			.notif-modal-footer {
				display: flex; justify-content: flex-end; gap: 10px;
				padding: 16px 22px; border-top: 1px solid #eef2f4; background: #fafbfc;
			}
			.notif-btn-cancelar-modal {
				border: 1px solid #d8e2e8; background: #fff; color: #5a7385; border-radius: 8px;
				padding: 9px 18px; font-size: 13px; font-weight: 600; cursor: pointer;
			}
			.notif-btn-enviar {
				border: none; background: #2B73B6; color: #fff; border-radius: 8px;
				padding: 9px 22px; font-size: 13px; font-weight: 700; cursor: pointer;
			}
			.notif-btn-enviar:hover { background: #1a5278; }
			.notif-btn-enviar:disabled { opacity: .5; cursor: not-allowed; }
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>

		<div id="loader" style="display:none; position: fixed; top:0; left:0; width:100%; height:100%; background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div>Cargando...</div>
			</div>
		</div>

	<?php
		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo = (strtotime($fa)-strtotime($fg));

		if($periodo > 600){
			session_destroy(); session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"] = "p";
		}

		if ($_SESSION["autentifica"] != "p"){
			session_destroy(); session_unset();
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
				titulo("Notificaciones");
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

	<div class="container-fluid" style="padding:0; text-align:left;">

		<div id="barra-top-notif">
			<button class="notif-btn-nueva" onclick="abrirModalNueva()">
				<svg width="16" height="16" fill="none" stroke="#fff" stroke-width="2.4" viewBox="0 0 24 24"><path d="M12 5v14M5 12h14"/></svg>
				Nueva notificación
			</button>

			<div class="notif-filtros">
				<div class="val-pill activa" data-filtro="">Todas</div>
				<div class="val-pill" data-filtro="programada">Programadas</div>
				<div class="val-pill" data-filtro="completada">Completadas</div>
				<div class="val-pill" data-filtro="cancelada">Canceladas</div>
			</div>
		</div>

		<div id="notif-lista">
			<div class="val-estado-cargando"><span class="val-spinner"></span> Cargando notificaciones...</div>
		</div>

	</div>

	<!-- ══════════════ MODAL: Nueva notificación ══════════════ -->
	<div class="notif-modal-overlay" id="notif-modal-overlay">
		<div class="notif-modal">
			<div class="notif-modal-head">
				<h5>Nueva notificación</h5>
				<button class="notif-modal-close" onclick="cerrarModalNueva()">&times;</button>
			</div>
			<div class="notif-modal-body">

				<div class="notif-field">
					<label>Título</label>
					<input type="text" id="notif-titulo" maxlength="150" placeholder="Ej. Recordatorio de pago">
				</div>

				<div class="notif-field">
					<label>Mensaje</label>
					<textarea id="notif-mensaje" rows="3" placeholder="Escribe el mensaje..."></textarea>
				</div>

				<div class="notif-field">
					<label>Destinatarios</label>
					<div class="notif-tipo-grid" id="notif-tipo-grid">
						<div class="notif-tipo-opt activo" data-tipo="todos">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87M16 3.13a4 4 0 0 1 0 7.75"/></svg>
							Todos
						</div>
						<div class="notif-tipo-opt" data-tipo="socio">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2l2.5 6.5L21 9l-5 4.5L17.5 21 12 17l-5.5 4L8 13.5 3 9l6.5-.5z"/></svg>
							Socios
						</div>
						<div class="notif-tipo-opt" data-tipo="gerente">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="7" width="20" height="14" rx="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
							Gerentes
						</div>
						<div class="notif-tipo-opt" data-tipo="broker">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1M9 13h1M9 17h1M14 9h1M14 13h1M14 17h1"/></svg>
							Brokers
						</div>
						<div class="notif-tipo-opt" data-tipo="personal">
							<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
							Personal
						</div>
					</div>
				</div>

				<div class="notif-field" id="notif-personal-wrap" style="display:none;">
					<label id="notif-buscador-label">Buscar usuario</label>
					<div class="notif-usuario-wrap">
						<input type="text" id="notif-busqueda-usuario" placeholder="Escribe un nombre..." autocomplete="off">
						<div class="notif-usuario-resultados" id="notif-usuario-resultados" style="display:none;"></div>
					</div>
					<div id="notif-usuario-chip-wrap"></div>
				</div>

				<div class="notif-field">
					<label>¿Cuándo se envía?</label>
					<div class="notif-cuando-toggle">
						<div class="notif-cuando-opt activo" id="notif-cuando-ahora" onclick="setModoEnvio('ahora')">
							📤 Enviar ahora
						</div>
						<div class="notif-cuando-opt" id="notif-cuando-programado" onclick="setModoEnvio('programado')">
							🕐 Programar
						</div>
					</div>
					<div id="notif-programacion-wrap" style="display:none; margin-top:10px; display:grid; grid-template-columns:1fr 1fr; gap:10px;">
						<input type="date" id="notif-fecha">
						<input type="time" id="notif-hora" value="10:00">
					</div>
				</div>

				<div class="notif-field">
					<label>Adjunto (opcional — PDF, JPG o PNG)</label>
					<div class="notif-adjunto-drop" id="notif-adjunto-drop" onclick="document.getElementById('notif-adjunto-input').click()">
						📎 Haz clic para adjuntar un archivo
					</div>
					<input type="file" id="notif-adjunto-input" accept=".pdf,.jpg,.jpeg,.png" style="display:none;">
					<div id="notif-adjunto-chip-wrap"></div>
				</div>

			</div>
			<div class="notif-modal-footer">
				<button class="notif-btn-cancelar-modal" onclick="cerrarModalNueva()">Cancelar</button>
				<button class="notif-btn-enviar" id="notif-btn-enviar" onclick="enviarNotificacion()">Enviar</button>
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

	const API = 'notificaciones_api.php';

	async function api(payload) {
		const resp = await fetch(API, {
			method : 'POST',
			headers: { 'Content-Type': 'application/json' },
			body   : JSON.stringify(payload)
		});
		return resp.json();
	}

	const ETIQUETAS_TIPO = {
		'todos': 'Todos', 'socio': 'Socios', 'gerente': 'Gerentes',
		'broker': 'Brokers', 'personal': 'Personal'
	};

	let filtroActual = '';
	let tipoSeleccionado = 'todos';
	let modoEnvio = 'ahora';
	let claveSeleccionada = null;
	let valorEstructuraSeleccionado = null;
	let archivoAdjunto = null;
	let timerBusquedaUsuario = null;

	// Mapea el tipo de destinatario al campo real de estruc_empleados
	const CAMPO_ESTRUCTURA = { 'socio': 'sc', 'gerente': 'gerencia', 'broker': 'broker' };
	const LABEL_BUSCADOR = {
		'personal': 'Buscar usuario',
		'socio': 'Buscar socio comercial',
		'gerente': 'Buscar gerencia',
		'broker': 'Buscar broker',
	};

	document.addEventListener('DOMContentLoaded', function () {
		cargarNotificaciones();

		document.querySelectorAll('.val-pill').forEach(pill => {
			pill.addEventListener('click', function () {
				document.querySelectorAll('.val-pill').forEach(p => p.classList.remove('activa'));
				this.classList.add('activa');
				filtroActual = this.dataset.filtro;
				cargarNotificaciones();
			});
		});

		document.querySelectorAll('.notif-tipo-opt').forEach(opt => {
			opt.addEventListener('click', function () {
				document.querySelectorAll('.notif-tipo-opt').forEach(o => o.classList.remove('activo'));
				this.classList.add('activo');
				tipoSeleccionado = this.dataset.tipo;

				noEmpleadoSeleccionado = null;
				valorEstructuraSeleccionado = null;
				document.getElementById('notif-usuario-chip-wrap').innerHTML = '';
				document.getElementById('notif-busqueda-usuario').value = '';

				const necesitaBuscador = ['personal', 'socio', 'gerente', 'broker'].includes(tipoSeleccionado);
				document.getElementById('notif-personal-wrap').style.display = necesitaBuscador ? 'block' : 'none';
				if (necesitaBuscador) {
					document.getElementById('notif-buscador-label').textContent = LABEL_BUSCADOR[tipoSeleccionado];
					document.getElementById('notif-busqueda-usuario').placeholder =
						tipoSeleccionado === 'personal' ? 'Escribe un nombre...' : 'Escribe para buscar...';
				}
			});
		});

		document.getElementById('notif-busqueda-usuario').addEventListener('keyup', function () {
			clearTimeout(timerBusquedaUsuario);
			const q = this.value.trim();
			if (q.length < 2) {
				document.getElementById('notif-usuario-resultados').style.display = 'none';
				return;
			}
			timerBusquedaUsuario = setTimeout(() => {
				if (tipoSeleccionado === 'personal') {
					buscarUsuarios(q);
				} else {
					buscarEstructura(q);
				}
			}, 300);
		});

		document.getElementById('notif-adjunto-input').addEventListener('change', function (e) {
			const file = e.target.files[0];
			if (!file) return;
			archivoAdjunto = file;
			document.getElementById('notif-adjunto-chip-wrap').innerHTML = `
				<div class="notif-adjunto-chip" style="margin-top:8px;">
					📎 ${file.name}
					<button onclick="quitarAdjunto()">&times;</button>
				</div>`;
		});

		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		document.getElementById('salir').addEventListener('click', () => {
			window.location = 'https://www.sis-ca.com.mx:7863/';
		});
		document.getElementById('email').addEventListener('click', () => {
			window.open('http://webmail.carobra.com.mx/imp/login.php');
		});
	});

	function quitarAdjunto() {
		archivoAdjunto = null;
		document.getElementById('notif-adjunto-input').value = '';
		document.getElementById('notif-adjunto-chip-wrap').innerHTML = '';
	}

	function setModoEnvio(modo) {
		modoEnvio = modo;
		document.getElementById('notif-cuando-ahora').classList.toggle('activo', modo === 'ahora');
		document.getElementById('notif-cuando-programado').classList.toggle('activo', modo === 'programado');
		document.getElementById('notif-programacion-wrap').style.display = (modo === 'programado') ? 'grid' : 'none';
	}

	async function buscarUsuarios(q) {
		const resp = await api({ action: 'buscarUsuariosPersonal', q });
		const cont = document.getElementById('notif-usuario-resultados');
		if (!resp.ok || !resp.datos.length) {
			cont.style.display = 'none';
			return;
		}
		cont.innerHTML = resp.datos.map(u => `
			<div class="notif-usuario-item" onclick='seleccionarUsuario(${JSON.stringify(u)})'>
				${u.nombre} <span style="color:#a0adb5;font-size:11px;">${u.tipo || ''}</span>
			</div>
		`).join('');
		cont.style.display = 'block';
	}

	function seleccionarUsuario(u) {
		claveSeleccionada = u.clave;
		document.getElementById('notif-busqueda-usuario').value = '';
		document.getElementById('notif-usuario-resultados').style.display = 'none';
		document.getElementById('notif-usuario-chip-wrap').innerHTML = `
			<div class="notif-usuario-chip">
				👤 ${u.nombre}
				<button onclick="quitarUsuarioSeleccionado()">&times;</button>
			</div>`;
	}

	function quitarUsuarioSeleccionado() {
		claveSeleccionada = null;
		valorEstructuraSeleccionado = null;
		document.getElementById('notif-usuario-chip-wrap').innerHTML = '';
	}

	async function buscarEstructura(q) {
		const campo = CAMPO_ESTRUCTURA[tipoSeleccionado];
		const resp = await api({ action: 'buscarEstructura', campo, q });
		const cont = document.getElementById('notif-usuario-resultados');
		if (!resp.ok || !resp.datos.length) {
			cont.innerHTML = '<div class="notif-usuario-item" style="color:#aaa;cursor:default;">Sin resultados</div>';
			cont.style.display = 'block';
			return;
		}
		cont.innerHTML = resp.datos.map(d => `
			<div class="notif-usuario-item" onclick='seleccionarEstructura(${JSON.stringify(d)})'>
				${d.valor} <span style="color:#a0adb5;font-size:11px;">${d.total} persona(s)</span>
			</div>
		`).join('');
		cont.style.display = 'block';
	}

	function seleccionarEstructura(d) {
		valorEstructuraSeleccionado = d.valor;
		document.getElementById('notif-busqueda-usuario').value = '';
		document.getElementById('notif-usuario-resultados').style.display = 'none';
		document.getElementById('notif-usuario-chip-wrap').innerHTML = `
			<div class="notif-usuario-chip">
				🏢 ${d.valor} <span style="opacity:.7">(${d.total} persona${d.total==1?'':'s'})</span>
				<button onclick="quitarUsuarioSeleccionado()">&times;</button>
			</div>`;
	}

	function abrirModalNueva() {
		document.getElementById('notif-modal-overlay').style.display = 'flex';
	}

	function cerrarModalNueva() {
		document.getElementById('notif-modal-overlay').style.display = 'none';
		document.getElementById('notif-titulo').value = '';
		document.getElementById('notif-mensaje').value = '';
		document.querySelectorAll('.notif-tipo-opt').forEach(o => o.classList.remove('activo'));
		document.querySelector('.notif-tipo-opt[data-tipo="todos"]').classList.add('activo');
		tipoSeleccionado = 'todos';
		document.getElementById('notif-personal-wrap').style.display = 'none';
		claveSeleccionada = null;
		valorEstructuraSeleccionado = null;
		quitarUsuarioSeleccionado();
		setModoEnvio('ahora');
		quitarAdjunto();
	}

	async function enviarNotificacion() {
		const titulo  = document.getElementById('notif-titulo').value.trim();
		const mensaje = document.getElementById('notif-mensaje').value.trim();

		if (!titulo || !mensaje) {
			Swal.fire('Faltan datos', 'Escribe un título y un mensaje.', 'warning');
			return;
		}
		if (tipoSeleccionado === 'personal' && !claveSeleccionada) {
			Swal.fire('Falta el usuario', 'Selecciona a quién le llega la notificación.', 'warning');
			return;
		}
		if (['socio', 'gerente', 'broker'].includes(tipoSeleccionado) && !valorEstructuraSeleccionado) {
			Swal.fire('Falta la estructura', 'Selecciona el socio, gerencia o broker específico.', 'warning');
			return;
		}

		let fechaProgramada = '';
		if (modoEnvio === 'programado') {
			const fecha = document.getElementById('notif-fecha').value;
			const hora  = document.getElementById('notif-hora').value;
			if (!fecha || !hora) {
				Swal.fire('Falta la fecha/hora', 'Selecciona cuándo se debe enviar.', 'warning');
				return;
			}
			fechaProgramada = `${fecha} ${hora}:00`;
		}

		const btn = document.getElementById('notif-btn-enviar');
		btn.disabled = true;
		btn.textContent = 'Enviando...';

		const fd = new FormData();
		fd.append('action', 'crearNotificacion');
		fd.append('titulo', titulo);
		fd.append('mensaje', mensaje);
		fd.append('tipo_destinatario', tipoSeleccionado);
		if (tipoSeleccionado === 'personal') fd.append('clave_destino', claveSeleccionada);
		if (['socio', 'gerente', 'broker'].includes(tipoSeleccionado)) fd.append('valor_estructura', valorEstructuraSeleccionado);
		if (fechaProgramada) fd.append('fecha_programada', fechaProgramada);
		if (archivoAdjunto) fd.append('adjunto', archivoAdjunto);

		try {
			const resp = await fetch(API, { method: 'POST', body: fd });
			const data = await resp.json();

			btn.disabled = false;
			btn.textContent = 'Enviar';

			if (data.ok) {
				cerrarModalNueva();
				Swal.fire({ icon: 'success', title: 'Notificación programada', text: `Llegará a ${data.total_destinatarios} destinatario(s).`, timer: 1800, showConfirmButton: false });
				cargarNotificaciones();
			} else {
				Swal.fire('Error', data.msg || 'No se pudo enviar.', 'error');
			}
		} catch (e) {
			btn.disabled = false;
			btn.textContent = 'Enviar';
			Swal.fire('Error', 'Error de comunicación con el servidor.', 'error');
		}
	}

	async function cargarNotificaciones() {
		const lista = document.getElementById('notif-lista');
		lista.innerHTML = '<div class="val-estado-cargando"><span class="val-spinner"></span> Cargando notificaciones...</div>';

		const resp = await api({ action: 'listarNotificaciones', estatus: filtroActual });
		if (!resp.ok) {
			lista.innerHTML = '<div class="val-estado-vacio">Error al cargar.</div>';
			return;
		}

		const datos = resp.datos || [];
		if (datos.length === 0) {
			lista.innerHTML = '<div class="val-estado-vacio">No hay notificaciones aún.</div>';
			return;
		}

		lista.innerHTML = datos.map(n => `
			<div class="notif-card">
				<div class="notif-icon">🔔</div>
				<div class="notif-info">
					<div class="notif-titulo">${n.titulo}</div>
					<div class="notif-mensaje-prev">${n.mensaje}</div>
					<div class="notif-meta">
						<span class="val-badge badge-${n.tipo_destinatario}">${ETIQUETAS_TIPO[n.tipo_destinatario] || n.tipo_destinatario}</span>
						<span>🗓️ ${n.fecha_programada}</span>
						<span class="val-badge badge-${n.estatus}">${n.estatus}</span>
						${n.adjunto_nombre ? `<span>📎 ${n.adjunto_nombre}</span>` : ''}
					</div>
				</div>
				<div class="notif-progreso">${n.total_enviados || 0} / ${n.total_destinatarios || 0}</div>
				${n.estatus === 'programada' ? `<button class="notif-btn-cancelar" onclick="cancelarNotificacion(${n.id})">Cancelar</button>` : ''}
			</div>
		`).join('');
	}

	async function cancelarNotificacion(id) {
		const result = await Swal.fire({
			title: '¿Cancelar esta notificación?',
			icon: 'question', showCancelButton: true,
			confirmButtonText: 'Sí, cancelar', cancelButtonText: 'Volver',
			confirmButtonColor: '#2B73B6'
		});
		if (!result.isConfirmed) return;

		const resp = await api({ action: 'cancelarNotificacion', id });
		if (resp.ok) {
			Swal.fire({ icon: 'success', title: 'Cancelada', timer: 1200, showConfirmButton: false });
			cargarNotificaciones();
		} else {
			Swal.fire('Error', resp.msg || 'No se pudo cancelar.', 'error');
		}
	}

	</script>
</html>