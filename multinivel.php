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

		<style>
            .carobra    {
                           font-size: 0.9em !important;
                        }
            .ec-card {
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 18px;
                overflow: hidden;
                box-shadow: 0 12px 32px rgba(24,95,165,.08);
                margin-bottom: 10px;
            }
            .ec-header {
                background: linear-gradient(135deg, #185FA5, #0C447C);
                padding: 10px 14px;
                display: flex;
                align-items: center;
                gap: 4px;
            }
            .ec-header-icon {
                width: 32px; height: 32px; border-radius: 10px;
                background: rgba(255,255,255,.15);
                display: flex; align-items: center; justify-content: center;
                flex-shrink: 0;
            }
            .ec-header h4 { color: #fff; font-size: 17px; font-weight: 700; margin: 0; }
            .ec-header p { color: rgba(255,255,255,.75); font-size: 12px; margin: 3px 0 0; }

            .ec-body { padding: 14px 10px; }

            .ec-fields {
                display: grid;
                grid-template-columns: 1fr 1fr 1fr;
                gap: 12px;
            }
            @media (max-width: 768px) {
                .ec-fields { grid-template-columns: 1fr; }
            }
            .ec-field .form-control:disabled {
                background: #f0f2f5;
                color: #b0b8c1;
                cursor: not-allowed;
            }

            .ec-col-form {
                border-bottom: 1px solid #e8edf3;
                padding-bottom: 18px;
                margin-bottom: 18px;
            }
            .ec-col-tabla { padding-left: 0; }

            .ec-field label {
                display: flex;
                align-items: center;
                gap: 6px;
                font-size: 11px;
                font-weight: 700;
                color: #185FA5;
                text-transform: uppercase;
                letter-spacing: .06em;
                margin-bottom: 8px;
            }
            .ec-field .form-control {
                font-size: 13.5px;
                border: 1.5px solid #dce3eb;
                border-radius: 10px;
                padding: 10px 12px;
                height: auto;
                color: #1f2d3d;
                background: #fafbfc;
                transition: border-color .15s, box-shadow .15s;
            }
            .ec-field .form-control:focus {
                border-color: #185FA5;
                box-shadow: 0 0 0 3px rgba(24,95,165,.12);
                background: #fff;
                outline: none;
            }
            .ec-hint {
                font-size: 10.5px; color: #9aa5b1; margin-top: 8px; font-style: italic; line-height: 1.4;
            }

            /* =========================
            AUTOCOMPLETE
            ========================= */
            .ac-wrapper { position: relative; }
            .ac-dropdown {
                display: none;
                position: absolute;
                top: calc(100% + 6px);
                left: 0; right: 0;
                background: #fff;
                border: 1px solid #e8edf3;
                border-radius: 12px;
                box-shadow: 0 14px 32px rgba(0,0,0,.14);
                max-height: 230px;
                overflow-y: auto;
                z-index: 100;
                text-align: left;
            }
            .ac-dropdown.open { display: block; }
            .ac-item {
                padding: 10px 14px;
                cursor: pointer;
                font-size: 13px;
                color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
                text-align: left;
                display: flex;
                align-items: center;
                gap: 10px;
            }
            .ac-item:last-child { border-bottom: none; }
            .ac-item:hover, .ac-item.active { background: #eaf2fb; }
            .ac-item .ac-dot {
                width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
            }
            .ac-empty {
                padding: 12px 14px; font-size: 12px; color: #9aa5b1; text-align: left; font-style: italic;
            }

            /* =========================
            RESUMEN + BOTON
            ========================= */
            .ec-resumen {
                margin-top: 22px;
                background: #f4f7fb;
                border: 1px dashed #cfe0f2;
                border-radius: 12px;
                padding: 14px 18px;
                font-size: 12.5px;
                color: #4b5563;
            }
            .ec-resumen b { color: #1f2d3d; }
            .resumen-clic {
                color: #185FA5; font-weight: 700; cursor: pointer;
                border-bottom: 1px dashed #185FA5;
            }
            .resumen-clic:hover { color: #0C447C; border-bottom-color: #0C447C; }

            .btn-asignar-estructura {
                display: inline-flex; align-items: center; justify-content: center; gap: 8px;
                width: auto; padding: 13px 26px;
                background: linear-gradient(135deg, #185FA5, #0C447C);
                color: #fff; border: none; border-radius: 12px;
                font-size: 14px; font-weight: 700;
                white-space: nowrap;
                cursor: pointer; transition: opacity .15s, transform .1s;
                margin-top: 24px;
            }
            @media (max-width: 768px) {
                .btn-asignar-estructura { width: 100%; }
            }
            .btn-asignar-estructura:hover { opacity: .92; }
            .btn-asignar-estructura:active { transform: scale(.99); }
            .btn-asignar-estructura:disabled { opacity: .5; cursor: not-allowed; }

            /* =========================
            TABLA
            ========================= */
            .ec-tabla-header {
                display: flex; align-items: center; justify-content: space-between;
                margin: 26px 0 12px;
            }
            .ec-tabla-header h3 { font-size: 15px; font-weight: 700; color: #1f2d3d; margin: 0; }
            .btn-exportar-estructura {
                display: flex; align-items: center; gap: 6px;
                padding: 8px 16px;
                background: #0F6E56; color: #fff;
                border: none; border-radius: 8px;
                font-size: 13px; font-weight: 600;
                cursor: pointer; transition: background .15s;
            }
            .btn-exportar-estructura:hover { background: #0B5C42; }

            .ec-tabla-buscador {
                display: flex;
                align-items: center;
                gap: 10px;
                margin-bottom: 14px;
            }
            .ec-tabla-buscador-input { position: relative; flex: 1; }
            .ec-tabla-buscador-input svg {
                position: absolute;
                left: 14px; top: 50%;
                transform: translateY(-50%);
                color: #9aa5b1;
                pointer-events: none;
            }
            .ec-tabla-buscador-input input {
                width: 100%;
                font-size: 13px;
                border: 1.5px solid #dce3eb;
                border-radius: 10px;
                padding: 10px 14px 10px 38px;
                color: #1f2d3d;
                background: #fafbfc;
                transition: border-color .15s, box-shadow .15s;
            }
            .ec-tabla-buscador-input input:focus {
                border-color: #185FA5;
                box-shadow: 0 0 0 3px rgba(24,95,165,.12);
                background: #fff;
                outline: none;
            }
            .btn-ver-detalle {
                display: flex; align-items: center; gap: 6px;
                padding: 10px 18px;
                background: #185FA5; color: #fff;
                border: none; border-radius: 10px;
                font-size: 13px; font-weight: 600;
                cursor: pointer; transition: background .15s;
                white-space: nowrap;
            }
            .btn-ver-detalle:hover { background: #0C447C; }

            .tabla-estructura-wrapper {
                width: 100%;
                overflow-x: auto;
                overflow-y: auto;
                max-height: 420px;
                border: 1px solid #e8edf3;
                border-radius: 14px;
                background: #fff;
            }
            .tabla-estructura {
                width: 100%;
                min-width: 380px;
                border-collapse: collapse;
                background: #fff;
            }
            .tabla-estructura thead tr {
                background: #f4f7fb;
                border-bottom: 1px solid #e8edf3;
            }
            .tabla-estructura thead th {
                color: #7b8794;
                padding: 10px 14px;
                text-align: left;
                font-size: 11px;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: .06em;
                white-space: nowrap;
                border: none;
                position: sticky;
                top: 0;
                z-index: 2;
                background: #f4f7fb;
            }
            .tabla-estructura tbody td {
                padding: 14px 16px;
                text-align: left;
                vertical-align: middle;
                font-size: 13px;
                line-height: 1.5;
                color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
            }
            .tabla-estructura tbody tr:last-child td { border-bottom: none; }
            .tabla-estructura tbody tr:hover { background: #f7faff; }
            .te-socio, .te-negocio, .te-gerencia.clicable { cursor: pointer; }
            .te-socio    { color: #185FA5; font-size: 0.85em !important; padding: 10px !important;}
            .te-negocio  { font-size: 0.85em !important; color: #0F6E56; padding: 10px !important;}
            .te-gerencia { font-size: 0.85em !important; color: #854F0B; padding: 10px !important;}
            .te-socio:hover, .te-negocio:hover, .te-gerencia.clicable:hover { text-decoration: underline; font-weight: 600;}

            /* =========================
            MODAL DETALLE
            ========================= */
            .ec-modal-backdrop {
                display: none; position: fixed; inset: 0; z-index: 1000000;
                background: rgba(15,32,68,.55); backdrop-filter: blur(4px);
                align-items: center; justify-content: center; padding: 24px;
            }
            .ec-modal-backdrop.open { display: flex; }
            .ec-modal {
                background: #fff; border-radius: 16px; box-shadow: 0 24px 64px rgba(0,0,0,.22);
                width: 100%; max-width: 900px; max-height: 86vh;
                display: flex; flex-direction: column; overflow: hidden;
            }
            .ec-modal-table th, .ec-modal-table td { padding: 12px 14px; font-size: 13.5px; }
            .ec-modal-header {
                background: linear-gradient(135deg, #185FA5, #0C447C);
                padding: 16px 22px; display: flex; align-items: center; justify-content: space-between;
            }
            .ec-modal-header h3 { color: #fff; font-size: 15px; font-weight: 700; margin: 0; }
            .ec-modal-close {
                background: rgba(255,255,255,.15); border: none; color: #fff; width: 28px; height: 28px;
                border-radius: 6px; cursor: pointer; font-size: 16px;
            }
            .ec-modal-body { padding: 22px 26px; overflow-y: auto; }
            .ec-modal-empty { color: #9aa5b1; font-size: 13px; padding: 16px 0; text-align: center; }
            .ec-modal-table { width: 100%; border-collapse: collapse; }
            .ec-modal-table th {
                text-align: left; font-size: 11px; font-weight: 700; color: #7b8794;
                text-transform: uppercase; letter-spacing: .05em; padding: 8px 10px;
                border-bottom: 1px solid #e8edf3;
            }
            .ec-modal-table td {
                padding: 10px 10px; font-size: 13px; color: #1f2d3d;
                border-bottom: 1px solid #f0f4f8;
            }
            .ec-modal-table tr:last-child td { border-bottom: none; }
            .btn-eliminar-fila {
                display: inline-flex; align-items: center; justify-content: center;
                width: 26px; height: 26px; padding: 0;
                background: #fdecea; color: #c0392b;
                border: none; border-radius: 7px;
                cursor: pointer; transition: background .15s;
            }
            .btn-eliminar-fila:hover { background: #f6cfca; }

            /* El backdrop del modal usa z-index 1000000; SweetAlert2 debe
               quedar SIEMPRE por encima para que su confirmación no se
               dibuje detrás del modal cuando ya está abierto. */
            .swal2-container { z-index: 2000000 !important; }
        </style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>
		<div id="loader" style="
			display:none;
			position: fixed;
			top:0; left:0;
			width:100%; height:100%;
			background: rgba(0,0,0,0.5);
			z-index:9999;
			justify-content:center;
			align-items:center;
		">
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
			session_destroy();
			session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			$sesion_login = true;
		} else {
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"]  = "p";
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
				titulo("Multinivel");
				$pag="SEGURIDAD";
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

				$conecta = new conector();
				$nombre  = $_SESSION["nombre"];
				$nivel   = nivel_usuario(utf8_decode($nombre));

				$debugSocios = $debugNegocios = $debugGerencias = $debugGerenciasDp = $debugMultinivel = '';

				// Socios que hoy ya tienen tabla de gerencias conocida:
				// - "AFORE"      -> tabla `empl_usuarios` (la gerencia es el
				//                   empleado responsable de ese socio).
				// - "DEPORTENIS" -> tabla `empleados_dp` (la gerencia es un
				//                   COORDINADOR: un empleado cuya clave
				//                   aparece como "coordinador" de algún otro
				//                   registro de esa misma tabla).
				// Cuando se agreguen más socios con su propia tabla de
				// gerencias, sumarlos aquí y en multinivel_api.php.
				$SOCIOS_CON_GERENCIA = ['AFORE', 'DPORTENIS'];

				// Socios: catálogo de socio_area_negocio (clave + nombre). Son
				// entidades/socios de negocio (AFORE, BRADESCARD, METLIFE, etc.),
				// no empleados. Un mismo socio puede repetirse con distintas
				// áreas de negocio, por eso el DISTINCT.
				$resSocios = $conecta->consulta("SELECT DISTINCT clave, nombre FROM socio_area_negocio ORDER BY nombre");
				$listaSocios = [];
				if($resSocios){
					while($s = mysqli_fetch_assoc($resSocios)){
						$listaSocios[] = ['id' => $s['clave'], 'nombre' => $s['nombre']];
					}
				} else {
					$debugSocios = mysqli_error($conecta->conexion);
				}

				// Áreas de Negocio: catálogo de areas_negocio, excluyendo CORPORATIVO
				$resNegocios = $conecta->consulta("SELECT clave, nombre FROM areas_negocio WHERE clave <> 'CORPORATIVO' ORDER BY nombre");
				$listaNegocios = [];
				if($resNegocios){
					while($n = mysqli_fetch_assoc($resNegocios)){
						$listaNegocios[] = ['id' => $n['clave'], 'nombre' => $n['nombre']];
					}
				} else {
					$debugNegocios = mysqli_error($conecta->conexion);
				}

				// Gerencias del socio "AFORE": la gerencia es un EMPLEADO, así
				// que sale del catálogo completo de empl_usuarios.
				$resGerencias = $conecta->consulta("SELECT clave, nombre FROM empl_usuarios ORDER BY nombre");
				$listaGerenciasAfores = [];
				if($resGerencias){
					while($g = mysqli_fetch_assoc($resGerencias)){
						$listaGerenciasAfores[] = ['id' => $g['clave'], 'nombre' => $g['nombre']];
					}
				} else {
					$debugGerencias = mysqli_error($conecta->conexion);
				}

				// Gerencias del socio "DEPORTENIS": la gerencia es un
				// COORDINADOR, es decir, un empleado de `empleados_dp` cuya
				// clave (no_promotor) aparece como "coordinador" de algún
				// otro registro de esa misma tabla. Se muestra su clave +
				// nombre "promotor".
				$resGerenciasDp = $conecta->consulta("
					SELECT clave, promotor
					FROM empleados_dp
					WHERE no_promotor IN (SELECT DISTINCT coordinador FROM empleados_dp)
					ORDER BY promotor
				");
				$listaGerenciasDeportenis = [];
				if($resGerenciasDp){
					while($g = mysqli_fetch_assoc($resGerenciasDp)){
						$listaGerenciasDeportenis[] = ['id' => $g['clave'], 'nombre' => $g['promotor']];
					}
				} else {
					$debugGerenciasDp = mysqli_error($conecta->conexion);
				}

				// Asignaciones ya guardadas en multinivel, con los nombres resueltos.
				// El nombre del socio se resuelve contra socio_area_negocio (mismo
				// catálogo que el combo de Socio). La gerencia se resuelve según el
				// socio: para "AFORE" contra empl_usuarios (alias "g"), para
				// "DEPORTENIS" contra empleados_dp (alias "gd"); solo uno de los
				// dos JOIN puede matchear por fila, de ahí el COALESCE. Para los
				// demás socios, id_gerencia se queda en el sentinel '0' y ninguno
				// de los dos JOIN matchea.
				$resMultinivel = $conecta->consulta("
					SELECT m.clave_socio, s.nombre AS nombre_socio, m.clave_negocio, n.nombre AS nombre_negocio,
					       m.id_gerencia, COALESCE(g.nombre, gd.promotor) AS gerencia
					FROM multinivel m
					LEFT JOIN (SELECT DISTINCT clave, nombre FROM socio_area_negocio) s ON m.clave_socio = s.clave
					LEFT JOIN areas_negocio n ON m.clave_negocio = n.clave
					LEFT JOIN empl_usuarios g ON m.clave_socio = 'AFORE' AND m.id_gerencia = g.clave
					LEFT JOIN empleados_dp gd ON m.clave_socio = 'DPORTENIS' AND m.id_gerencia = gd.clave
					ORDER BY s.nombre, n.nombre, gerencia
				");
				$multinivelActual = [];
				if($resMultinivel){
					while($m = mysqli_fetch_assoc($resMultinivel)){ $multinivelActual[] = $m; }
				} else {
					$debugMultinivel = mysqli_error($conecta->conexion);
				}
	?>

	<div class="container-fluid">
		<div class="row mt-4">
			<div class="col-md-12">

				<div class="ec-card">
					<div class="ec-header">
						<div class="ec-header-icon">
							<svg xmlns="http://www.w3.org/2000/svg" width="22" height="17" viewBox="0 0 24 24"
								fill="none" stroke="#fff" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
								<circle cx="6" cy="6" r="3"/><circle cx="6" cy="18" r="3"/><circle cx="18" cy="12" r="3"/>
								<path d="M8.5 7.5l7 3M8.5 16.5l7-3"/>
							</svg>
						</div>
						<div>
							<h4>Multinivel — Socios y Áreas de Negocio</h4>
						</div>
					</div>

					<div class="ec-body">
					<div class="row m-0">

						<div class="col-md-12 ec-col-form">
						<div class="ec-fields">

							<!-- Socio -->
							<div class="ec-field ac-wrapper" id="wrap-socio">
								<label>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
									Socio
								</label>
								<input type="text" id="input-socio" class="form-control carobra" autocomplete="off" placeholder="Busca un socio..." />
								<input type="hidden" id="hidden-clave-socio" value="" />
								<div class="ac-dropdown" id="dd-socio"></div>
								<?php if($debugSocios): ?>
									<div class="ec-hint" style="color:#c0392b;">Error Socios: <?= htmlspecialchars($debugSocios) ?></div>
								<?php else: ?>
									<div class="ec-hint">Selecciona un socio de la lista (socio_area_negocio).</div>
								<?php endif; ?>
							</div>

							<!-- Gerencia (depende del Socio; solo aplica hoy para "AFORE") -->
							<div class="ec-field ac-wrapper" id="wrap-gerencia">
								<label>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V9l6-4 6 4v12M10 21v-6h4v6"/></svg>
									Gerencia
								</label>
								<input type="text" id="input-gerencia" class="form-control carobra" autocomplete="off" placeholder="Elige primero un Socio..." disabled />
								<input type="hidden" id="hidden-id-gerencia" value="" />
								<div class="ac-dropdown" id="dd-gerencia"></div>
								<?php if($debugGerencias || $debugGerenciasDp): ?>
									<div class="ec-hint" style="color:#c0392b;">Error Gerencias: <?= htmlspecialchars($debugGerencias ?: $debugGerenciasDp) ?></div>
								<?php else: ?>
									<div class="ec-hint" id="hint-gerencia">Aún no disponible para este socio.</div>
								<?php endif; ?>
							</div>

							<!-- Área de Negocio -->
							<div class="ec-field ac-wrapper" id="wrap-negocio">
								<label>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
									Área de Negocio
								</label>
								<input type="text" id="input-negocio" class="form-control carobra" autocomplete="off" placeholder="Busca un área de negocio..." />
								<input type="hidden" id="hidden-clave-negocio" value="" />
								<div class="ac-dropdown" id="dd-negocio"></div>
								<?php if($debugNegocios): ?>
									<div class="ec-hint" style="color:#c0392b;">Error Áreas: <?= htmlspecialchars($debugNegocios) ?></div>
								<?php else: ?>
									<div class="ec-hint">No incluye CORPORATIVO.</div>
								<?php endif; ?>
							</div>

						</div>

						<div class="ec-resumen" id="ec-resumen" style="display:none;"></div>

						<button class="btn-asignar-estructura" id="btn-asignar" type="button">
							<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24"
								fill="none" stroke="currentColor" stroke-width="2.5"
								stroke-linecap="round" stroke-linejoin="round">
								<path d="M12 5v14M5 12h14"/>
							</svg>
							Asignar Socio a Área de Negocio
						</button>

						</div>

						<div class="col-md-12 ec-col-tabla">

				<div class="ec-tabla-header">
					<h3>Tabla de Multinivel</h3>
					<button class="btn-exportar-estructura" id="btn-exportar-estructura" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
							fill="none" stroke="currentColor" stroke-width="2.5"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/>
							<polyline points="7 10 12 15 17 10"/>
							<line x1="12" y1="15" x2="12" y2="3"/>
						</svg>
						Exportar a Excel
					</button>
				</div>

				<div class="ec-tabla-buscador">
					<div class="ec-tabla-buscador-input">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
							fill="none" stroke="currentColor" stroke-width="2.5"
							stroke-linecap="round" stroke-linejoin="round">
							<circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
						</svg>
						<input type="text" id="buscar-estructura" autocomplete="off" placeholder="Buscar por Socio o Área de Negocio..." />
					</div>
					<button class="btn-ver-detalle" id="btn-ver-detalle" type="button">
						<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
							fill="none" stroke="currentColor" stroke-width="2.5"
							stroke-linecap="round" stroke-linejoin="round">
							<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
						</svg>
						Ver Detalles
					</button>
				</div>

				<div class="tabla-estructura-wrapper">
					<table class="tabla-estructura">
						<thead>
							<tr>
								<th>Socio</th>
								<th>Área de Negocio</th>
								<th>Gerencia</th>
							</tr>
						</thead>
						<tbody id="body-estructura">
							<?php if($debugMultinivel): ?>
								<tr>
									<td colspan="3" class="text-center" style="padding:24px;font-size:13px;color:#c0392b;">
										Error: <?= htmlspecialchars($debugMultinivel) ?>
									</td>
								</tr>
							<?php elseif(empty($multinivelActual)): ?>
								<tr>
									<td colspan="3" class="text-center text-muted" style="padding:24px;font-size:13px;">
										Aún no hay nada asignado
									</td>
								</tr>
							<?php else: ?>
								<?php foreach($multinivelActual as $m): ?>
									<?php $tieneGer = !empty($m['id_gerencia']) && (string)$m['id_gerencia'] !== '0'; ?>
									<tr>
										<td class="te-socio" data-clave="<?= htmlspecialchars($m['clave_socio']) ?>"><?= !empty($m['nombre_socio']) ? htmlspecialchars($m['nombre_socio']) : htmlspecialchars($m['clave_socio']) ?></td>
										<td class="te-negocio" data-clave="<?= htmlspecialchars($m['clave_negocio']) ?>"><?= !empty($m['nombre_negocio']) ? htmlspecialchars($m['nombre_negocio']) : htmlspecialchars($m['clave_negocio']) ?></td>
										<td class="te-gerencia<?= $tieneGer ? ' clicable' : '' ?>" data-id-gerencia="<?= htmlspecialchars($m['id_gerencia'] ?? '0') ?>"><?= $tieneGer ? htmlspecialchars($m['gerencia']) : '<span class="text-muted">—</span>' ?></td>
									</tr>
								<?php endforeach; ?>
							<?php endif; ?>
						</tbody>
					</table>
				</div>

						</div>

						</div>
					</div>
				</div>

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
		$('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
	</script>

	<!-- MODAL DETALLE -->
	<div class="ec-modal-backdrop" id="ecModalBackdrop">
		<div class="ec-modal">
			<div class="ec-modal-header">
				<h3 id="ecModalTitulo"></h3>
				<button class="ec-modal-close" id="ecModalClose" type="button">✕</button>
			</div>
			<div class="ec-modal-body" id="ecModalBody"></div>
		</div>
	</div>

	<?php } ?>

	</body>

	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>

	<script type="text/javascript">

    jQuery(document).ready(function() {
        $(window).resize(function() { centra_contenedor(); });
        $(window).resize();

        $('#men_pie').css("top", h);
        $('#men_pie').css("background", "red");

        $('#tmenu td').click(function(evento) {
            var modulo = $(this).text().replace(/ /gi, "_");
            $("#opcion").load("menu.php?modulo=" + modulo);
        });

        $("#salir").click(function() {
            window.location = "https://www.sis-ca.com.mx:7863/";
        });

        $("#email").click(function() {
            window.open("http://webmail.carobra.com.mx/imp/login.php");
        });

        ajustarAltoEstructura();
        $(window).on("resize", ajustarAltoEstructura);
    });

    // =========================
    // ALTURA (para que el botón nunca quede fuera de vista)
    // =========================
    function ajustarAltoEstructura() {
        const body = document.querySelector(".ec-body");
        if (!body) return;

        const rect        = body.getBoundingClientRect();
        const contenedor  = document.getElementById("contenedor");
        let limiteInferior = window.innerHeight - 20;

        if (contenedor) {
            const contenedorRect = contenedor.getBoundingClientRect();
            limiteInferior = Math.min(limiteInferior, contenedorRect.bottom - 10);
        }

        const altoDisponible = Math.max(280, limiteInferior - rect.top);
        body.style.maxHeight = altoDisponible + "px";
        body.style.overflowY = "auto";
    }

    // Datos precargados desde PHP
    const LISTA_SOCIOS              = <?= json_encode($listaSocios) ?>;
    const LISTA_NEGOCIOS            = <?= json_encode($listaNegocios) ?>;
    const LISTA_GERENCIAS_AFORES    = <?= json_encode($listaGerenciasAfores) ?>;
    const LISTA_GERENCIAS_DEPORTENIS= <?= json_encode($listaGerenciasDeportenis) ?>;
    const SOCIOS_CON_GERENCIA       = <?= json_encode($SOCIOS_CON_GERENCIA) ?>;
    let   MULTINIVEL_ACTUAL         = <?= json_encode($multinivelActual) ?>;

    // Cada socio con tabla de gerencias conocida trae su propia lista de
    // candidatos; se elige la correcta según el Socio seleccionado.
    const GERENCIAS_POR_SOCIO = {
        'AFORE':      LISTA_GERENCIAS_AFORES,
        'DPORTENIS': LISTA_GERENCIAS_DEPORTENIS
    };

    function etiquetaSocio(m)   { return m.nombre_socio   ? m.nombre_socio   : m.clave_socio; }
    function etiquetaNegocio(m) { return m.nombre_negocio ? m.nombre_negocio : m.clave_negocio; }
    function etiquetaGerencia(m) { return (m.id_gerencia && String(m.id_gerencia) !== '0' && m.gerencia) ? m.gerencia : ''; }

    // =========================
    // BUSCADOR DE LA TABLA (filtra en vivo, sin ir al servidor)
    // =========================
    function renderFilasEstructura(lista) {
        const tbody = document.getElementById("body-estructura");

        if (!lista || lista.length === 0) {
            tbody.innerHTML = `
                <tr>
                    <td colspan="3" class="text-center text-muted" style="padding:24px;font-size:13px;">
                        Sin coincidencias
                    </td>
                </tr>
            `;
            return;
        }

        tbody.innerHTML = lista.map(m => {
            const ger = etiquetaGerencia(m);
            return `
            <tr>
                <td class="te-socio" data-clave="${m.clave_socio}">${etiquetaSocio(m)}</td>
                <td class="te-negocio" data-clave="${m.clave_negocio}">${etiquetaNegocio(m)}</td>
                <td class="te-gerencia${ger ? ' clicable' : ''}" data-id-gerencia="${m.id_gerencia ?? 0}">${ger ? ger : '<span class="text-muted">—</span>'}</td>
            </tr>
        `;
        }).join("");
    }

    document.getElementById("buscar-estructura").addEventListener("input", function () {
        const texto = this.value.trim().toLowerCase();

        if (texto === "") {
            renderFilasEstructura(MULTINIVEL_ACTUAL);
            return;
        }

        const filtrados = MULTINIVEL_ACTUAL.filter(m =>
            etiquetaSocio(m).toLowerCase().includes(texto) ||
            etiquetaNegocio(m).toLowerCase().includes(texto) ||
            etiquetaGerencia(m).toLowerCase().includes(texto)
        );

        renderFilasEstructura(filtrados);
    });

    // =========================
    // MODAL: VER Y ELIMINAR ASIGNACIONES
    // Es una relación muchos a muchos sin jerarquía (a diferencia de
    // Estructura Comercial), así que cada fila del modal trae su
    // propio botón de eliminar puntual (borra solo ESE par socio+área).
    // =========================
    const ecModalBackdrop = document.getElementById("ecModalBackdrop");

    function filaModalHTML(m) {
        const ger = etiquetaGerencia(m);
        return `
            <tr data-clave-socio="${m.clave_socio}" data-clave-negocio="${m.clave_negocio}" data-id-gerencia="${m.id_gerencia ?? 0}">
                <td>${etiquetaSocio(m)}</td>
                <td>${etiquetaNegocio(m)}</td>
                <td>${ger ? ger : '<span class="text-muted">—</span>'}</td>
                <td style="width:40px;text-align:center;">
                    <button class="btn-eliminar-fila" type="button" title="Eliminar esta asignación">
                        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24"
                            fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
                            <path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
                        </svg>
                    </button>
                </td>
            </tr>
        `;
    }

    function renderModal(filas, titulo) {
        document.getElementById("ecModalTitulo").textContent = titulo;

        const body = document.getElementById("ecModalBody");

        if (filas.length === 0) {
            body.innerHTML = `<div class="ec-modal-empty">Sin datos para mostrar.</div>`;
        } else {
            body.innerHTML = `
                <table class="ec-modal-table">
                    <thead>
                        <tr><th>Socio</th><th>Área de Negocio</th><th>Gerencia</th><th></th></tr>
                    </thead>
                    <tbody>
                        ${filas.map(m => filaModalHTML(m)).join('')}
                    </tbody>
                </table>
            `;
        }
    }

    function abrirModalEstructura(tipo, clave, etiqueta) {
        let filas = [];

        if (tipo === "socio") {
            filas = MULTINIVEL_ACTUAL.filter(m => m.clave_socio === clave);
            renderModal(filas, "Socio: " + etiqueta);
        } else if (tipo === "negocio") {
            filas = MULTINIVEL_ACTUAL.filter(m => m.clave_negocio === clave);
            renderModal(filas, "Área de Negocio: " + etiqueta);
        } else {
            filas = MULTINIVEL_ACTUAL.filter(m => String(m.id_gerencia ?? '0') === String(clave));
            renderModal(filas, "Gerencia: " + etiqueta);
        }

        ecModalBackdrop.classList.add("open");
    }

    document.getElementById("body-estructura").addEventListener("click", function (e) {
        const td = e.target.closest("td");
        if (!td) return;

        if (td.classList.contains("te-socio")) {
            abrirModalEstructura("socio", td.dataset.clave, td.textContent.trim());
        } else if (td.classList.contains("te-negocio")) {
            abrirModalEstructura("negocio", td.dataset.clave, td.textContent.trim());
        } else if (td.classList.contains("te-gerencia") && td.classList.contains("clicable")) {
            // Las celdas de Gerencia en blanco ("—", id 0) no abren modal:
            // agruparían de golpe TODAS las filas sin gerencia de cualquier
            // área, que no es una entidad puntual identificable.
            abrirModalEstructura("gerencia", td.dataset.idGerencia, td.textContent.trim());
        }
    });

    document.getElementById("ecModalClose").addEventListener("click", function () {
        ecModalBackdrop.classList.remove("open");
    });

    ecModalBackdrop.addEventListener("click", function (e) {
        if (e.target === ecModalBackdrop) ecModalBackdrop.classList.remove("open");
    });

    // Eliminar una fila puntual desde dentro del modal (delegación,
    // porque las filas se regeneran cada vez que se abre el modal)
    document.getElementById("ecModalBody").addEventListener("click", async function (e) {
        const btn = e.target.closest(".btn-eliminar-fila");
        if (!btn) return;

        const tr           = btn.closest("tr");
        const claveSocio   = tr.dataset.claveSocio;
        const claveNegocio = tr.dataset.claveNegocio;
        const idGerencia   = tr.dataset.idGerencia || '0';

        const fila = MULTINIVEL_ACTUAL.find(m => m.clave_socio === claveSocio && m.clave_negocio === claveNegocio && String(m.id_gerencia ?? '0') === String(idGerencia));
        const textoSocio    = fila ? etiquetaSocio(fila)    : claveSocio;
        const textoNegocio  = fila ? etiquetaNegocio(fila)  : claveNegocio;
        const textoGerencia = fila ? etiquetaGerencia(fila) : '';

        const confirm = await Swal.fire({
            icon: "warning",
            title: "¿Eliminar asignación?",
            html: `Se eliminará la asignación de <b>${textoSocio}</b> con el área <b>${textoNegocio}</b>${textoGerencia ? ` (Gerencia: <b>${textoGerencia}</b>)` : ''}. Esta acción no se puede deshacer.`,
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#c0392b"
        });

        if (!confirm.isConfirmed) return;

        btn.disabled = true;

        try {
            const response = await fetch("multinivel_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "eliminarMultinivel", clave_socio: claveSocio, clave_negocio: claveNegocio, id_gerencia: idGerencia })
            });

            const result = await response.json();

            if (result.status === "error") {
                Swal.fire({ icon: "error", title: "No se pudo eliminar", text: result.message });
                btn.disabled = false;
                return;
            }

            await Swal.fire({ icon: "success", title: "Eliminado", text: result.message || "Se eliminó correctamente." });

            location.reload();

        } catch (error) {
            Swal.fire({ icon: "error", title: "Error", text: String(error) });
            btn.disabled = false;
        }
    });

    // =========================
    // BOTÓN "VER DETALLES" (busca en ambos campos y abre el modal con todo lo que coincida)
    // =========================
    document.getElementById("btn-ver-detalle").addEventListener("click", function () {
        const texto = document.getElementById("buscar-estructura").value.trim();

        if (!texto) {
            Swal.fire({ icon: "warning", title: "Falta texto", text: "Escribe algo en el buscador primero." });
            return;
        }

        const textoLower = texto.toLowerCase();
        const filas = MULTINIVEL_ACTUAL.filter(m =>
            etiquetaSocio(m).toLowerCase().includes(textoLower) ||
            etiquetaNegocio(m).toLowerCase().includes(textoLower) ||
            etiquetaGerencia(m).toLowerCase().includes(textoLower)
        );

        renderModal(filas, `Resultados para "${texto}"`);
        ecModalBackdrop.classList.add("open");
    });

    document.getElementById("buscar-estructura").addEventListener("keydown", function (e) {
        if (e.key === "Enter") document.getElementById("btn-ver-detalle").click();
    });

    // =========================
    // EXPORTAR TABLA A EXCEL
    // =========================
    document.getElementById("btn-exportar-estructura").addEventListener("click", function () {

        if (!MULTINIVEL_ACTUAL || MULTINIVEL_ACTUAL.length === 0) {
            Swal.fire({ icon: "warning", title: "Sin datos", text: "Aún no hay asignaciones de multinivel para exportar." });
            return;
        }

        const headers = ["Socio", "Área de Negocio", "Gerencia"];
        const rows    = [headers];

        MULTINIVEL_ACTUAL.forEach(m => {
            rows.push([etiquetaSocio(m), etiquetaNegocio(m), etiquetaGerencia(m)]);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws["!cols"] = [{wch:30},{wch:30},{wch:26}];

        XLSX.utils.book_append_sheet(wb, ws, "Multinivel");

        const hoy = new Date().toISOString().slice(0,10);
        XLSX.writeFile(wb, `Multinivel_${hoy}.xlsx`);
    });

    /**
     * Quita acentos/diacríticos para que la búsqueda del autocomplete
     * sea insensible a acentos (ej. escribir "garcia" encuentra "García").
     */
    function normalizarTexto(txt) {
        return (txt || '').normalize('NFD').replace(/[̀-ͯ]/g, '').toLowerCase();
    }

    /**
     * Autocomplete genérico en modo estricto: Socio y Área de Negocio
     * vienen de catálogos reales (clave + nombre), así que solo se
     * puede elegir de la lista (no se permite texto libre ni crear).
     */
    function crearAutocomplete({ wrapperId, inputId, dropdownId, items, hiddenId, dotColor, mostrarTodoSiVacio = true, textoVacio = 'Escribe para buscar...' }) {
        const wrapper  = document.getElementById(wrapperId);
        const input    = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        const hidden   = document.getElementById(hiddenId);

        function render() {
            const texto = normalizarTexto(input.value.trim());

            // Cuando la lista puede ser grande (ej. Gerencia), no se quiere
            // "vomitar" todo de un jalón al enfocar vacío: solo se busca
            // cuando el usuario ya escribió algo.
            if (texto === '' && !mostrarTodoSiVacio) {
                dropdown.innerHTML = `<div class="ac-empty">${textoVacio}</div>`;
                dropdown.classList.add('open');
                return;
            }

            const filtrados = texto === '' ? items : items.filter(it => normalizarTexto(it.nombre).includes(texto));

            if (filtrados.length === 0) {
                dropdown.innerHTML = `<div class="ac-empty">Sin coincidencias</div>`;
            } else {
                dropdown.innerHTML = filtrados.map((it, i) => `
                    <div class="ac-item" data-idx="${i}">
                        <span class="ac-dot" style="background:${dotColor}"></span>
                        <span>${it.nombre}</span>
                    </div>
                `).join('');

                dropdown.querySelectorAll('.ac-item').forEach(el => {
                    el.addEventListener('click', function () {
                        const it = filtrados[parseInt(this.dataset.idx)];
                        input.value = it.nombre;
                        hidden.value = it.id;
                        dropdown.classList.remove('open');
                        actualizarResumen();
                    });
                });
            }

            dropdown.classList.add('open');
        }

        input.addEventListener('input', function () {
            hidden.value = '';
            render();
            actualizarResumen();
        });

        input.addEventListener('focus', render);

        document.addEventListener('click', function (e) {
            if (!e.target.closest('#' + wrapperId)) {
                dropdown.classList.remove('open');
            }
        });
    }

    crearAutocomplete({ wrapperId: 'wrap-socio',   inputId: 'input-socio',   dropdownId: 'dd-socio',   items: LISTA_SOCIOS,   hiddenId: 'hidden-clave-socio',   dotColor: '#185FA5' });
    crearAutocomplete({ wrapperId: 'wrap-negocio', inputId: 'input-negocio', dropdownId: 'dd-negocio', items: LISTA_NEGOCIOS, hiddenId: 'hidden-clave-negocio', dotColor: '#0F6E56' });

    // La Gerencia aplica hoy para los socios "AFORE" y "DPORTENIS" (cada
    // uno con su propia fuente). Se arranca vacía/deshabilitada y se activa
    // dinámicamente al elegir el Socio. Como esta lista puede ser grande,
    // no se muestra completa al enfocar: solo busca conforme se escribe.
    const GERENCIA_ITEMS = [];
    crearAutocomplete({
        wrapperId: 'wrap-gerencia', inputId: 'input-gerencia', dropdownId: 'dd-gerencia',
        items: GERENCIA_ITEMS, hiddenId: 'hidden-id-gerencia', dotColor: '#854F0B',
        mostrarTodoSiVacio: false, textoVacio: 'Escribe el nombre para buscar...'
    });

    // Se refresca el estado de Gerencia tanto al elegir un ítem del dropdown
    // de Socio (bubbling: el listener del ítem ya dejó el hidden actualizado
    // antes de que esto corra) como al escribir/borrar el campo a mano.
    document.getElementById("dd-socio").addEventListener("click", function (e) {
        if (e.target.closest(".ac-item")) actualizarEstadoGerencia();
    });
    document.getElementById("input-socio").addEventListener("input", actualizarEstadoGerencia);

    // La Gerencia depende del SOCIO elegido (no del Área de Negocio): hoy
    // solo el socio "AFORE" tiene tabla de gerencias conocida (empl_usuarios).
    function actualizarEstadoGerencia() {
        const claveSocio = document.getElementById("hidden-clave-socio").value;
        const inputGer    = document.getElementById("input-gerencia");
        const hiddenGer   = document.getElementById("hidden-id-gerencia");
        const hintGer     = document.getElementById("hint-gerencia");

        GERENCIA_ITEMS.length = 0;
        inputGer.value  = '';
        hiddenGer.value = '';

        if (claveSocio && SOCIOS_CON_GERENCIA.includes(claveSocio)) {
            GERENCIA_ITEMS.push(...(GERENCIAS_POR_SOCIO[claveSocio] || []));
            inputGer.disabled   = false;
            inputGer.placeholder = 'Busca un empleado...';
            if (hintGer) hintGer.textContent = 'Selecciona el empleado que será la Gerencia para este socio.';
        } else {
            inputGer.disabled   = true;
            inputGer.placeholder = 'Elige primero un Socio...';
            if (hintGer) hintGer.textContent = 'Aún no disponible para este socio.';
        }

        actualizarResumen();
    }

    // La Gerencia ya se refresca cuando cambia el Socio (arriba). También se
    // refresca si cambia el Área de Negocio, como reset defensivo por si ya
    // había una Gerencia elegida para otra combinación Socio+Área.
    document.getElementById("dd-negocio").addEventListener("click", function (e) {
        if (e.target.closest(".ac-item")) actualizarEstadoGerencia();
    });
    document.getElementById("input-negocio").addEventListener("input", actualizarEstadoGerencia);

    // =========================
    // RESUMEN EN VIVO
    // =========================
    function actualizarResumen() {
        const socio     = document.getElementById("input-socio").value.trim();
        const negocio   = document.getElementById("input-negocio").value.trim();
        const gerencia  = document.getElementById("input-gerencia").value.trim();
        const resumen   = document.getElementById("ec-resumen");

        if (!socio && !negocio && !gerencia) {
            resumen.style.display = "none";
            return;
        }

        resumen.style.display = "block";
        resumen.innerHTML = `
            <b>Socio:</b> ${socio || '—'}
            &nbsp;→&nbsp;
            <b>Gerencia:</b> ${gerencia || '—'}
            &nbsp;→&nbsp;
            <b>Área de Negocio:</b> ${negocio || '—'}
        `;
    }

    // =========================
    // ASIGNAR
    // =========================
    const btnAsignar = document.getElementById("btn-asignar");

    btnAsignar.addEventListener("click", async function () {

        const socioTxt     = document.getElementById("input-socio").value.trim();
        const negocioTxt   = document.getElementById("input-negocio").value.trim();
        const gerenciaTxt  = document.getElementById("input-gerencia").value.trim();
        const claveSocio   = document.getElementById("hidden-clave-socio").value;
        const claveNegocio = document.getElementById("hidden-clave-negocio").value;
        const idGerencia   = document.getElementById("hidden-id-gerencia").value;

        if (!socioTxt || !negocioTxt) {
            Swal.fire({ icon: "warning", title: "Faltan datos", text: "Selecciona un Socio y un Área de Negocio." });
            return;
        }

        if (!claveSocio || !claveNegocio) {
            Swal.fire({ icon: "warning", title: "Selección inválida", text: "Elige el Socio y el Área de Negocio de la lista desplegable." });
            return;
        }

        const requiereGerencia = SOCIOS_CON_GERENCIA.includes(claveSocio);

        if (requiereGerencia && (!gerenciaTxt || !idGerencia)) {
            Swal.fire({ icon: "warning", title: "Falta la Gerencia", text: "Para este Socio también debes elegir una Gerencia de la lista." });
            return;
        }

        const confirm = await Swal.fire({
            icon: "question",
            title: "¿Confirmar asignación?",
            html: `<b>Socio:</b> ${socioTxt}${requiereGerencia ? `<br><b>Gerencia:</b> ${gerenciaTxt}` : ''}<br><b>Área de Negocio:</b> ${negocioTxt}`,
            showCancelButton: true,
            confirmButtonText: "Sí, asignar",
            cancelButtonText: "Cancelar"
        });

        if (!confirm.isConfirmed) return;

        btnAsignar.disabled = true;

        try {
            const response = await fetch("multinivel_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "asignarMultinivel", clave_socio: claveSocio, clave_negocio: claveNegocio, id_gerencia: requiereGerencia ? idGerencia : '' })
            });

            const result = await response.json();

            if (result.status === "error") {
                Swal.fire({ icon: "error", title: "No se pudo asignar", text: result.message });
                btnAsignar.disabled = false;
                return;
            }

            await Swal.fire({ icon: "success", title: "Asignado", text: "La asignación se guardó correctamente." });

            location.reload();

        } catch (error) {
            Swal.fire({ icon: "error", title: "Error", text: String(error) });
            btnAsignar.disabled = false;
        }
    });

</script>
</html>