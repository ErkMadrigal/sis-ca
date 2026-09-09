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
            .ac-empty.crear { color: #0F6E56; font-style: normal; font-weight: 600; }

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
            TABLA DE ESTRUCTURA
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
                min-width: 420px;
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
            .te-sc, .te-brocker, .te-gerencia { cursor: pointer; }
            .te-sc       { color: #185FA5; font-size: 0.85em !important; padding: 10px !important;}
            .te-brocker  { font-size: 0.85em !important; color: #0F6E56; padding: 10px !important;}
            .te-gerencia { font-size: 0.85em !important; color: #854F0B; padding: 10px !important;}
            .te-sc:hover, .te-brocker:hover, .te-gerencia:hover { text-decoration: underline; font-weight: 600;}

            /* =========================
            MODAL DETALLE ESTRUCTURA
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

            /* =========================
            MODAL FOOTER (eliminar)
            ========================= */
            .ec-modal-footer {
                display: none;
                padding: 14px 26px;
                border-top: 1px solid #e8edf3;
                background: #fff6f6;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }
            .ec-modal-footer.show { display: flex; }
            .ec-modal-footer-nota {
                font-size: 11.5px; color: #9a4b42; font-style: italic; line-height: 1.4;
            }
            .btn-eliminar-estructura {
                display: inline-flex; align-items: center; gap: 8px;
                padding: 10px 18px; background: #c0392b; color: #fff;
                border: none; border-radius: 10px; font-size: 13px; font-weight: 700;
                cursor: pointer; transition: background .15s; white-space: nowrap;
            }
            .btn-eliminar-estructura:hover { background: #a5311f; }

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
				titulo("Estructura Comercial");
				$pag="PAGOS_Y_FACTURAS_NAP";
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

				$debugSC = $debugBrocker = $debugGerencia = $debugEstructura = '';

				// SC: sugerencias de estruc_empleados + los que ya se hayan creado en estructura_comercial
				// + brockers (se pueden "promover" a SC). Un SC puede tener MUCHOS brockers,
				// así que aquí no se excluye nada por ya estar en uso.
				$resSC = $conecta->consulta("
					SELECT sc FROM (
						SELECT DISTINCT sc FROM estruc_empleados WHERE sc <> ''
						UNION
						SELECT DISTINCT sc FROM estructura_comercial WHERE sc <> ''
						UNION
						SELECT DISTINCT brocker FROM estruc_empleados WHERE brocker <> ''
					) t
					ORDER BY sc
				");
				$listaSC = [];
				if($resSC){
					while($s = mysqli_fetch_assoc($resSC)){ $listaSC[] = $s['sc']; }
				} else {
					$debugSC = mysqli_error($conecta->conexion);
				}

				// Brocker: TODOS los brockers (un brocker puede tener MUCHAS gerencias,
				// entonces reutilizarlo es normal, no se excluye por ya estar asignado)
				// + gerencias que todavia no tienen brocker asignado (se pueden "promover" a Brocker)
				$resBrocker = $conecta->consulta("
					SELECT brocker FROM (
						SELECT DISTINCT brocker FROM estruc_empleados WHERE brocker <> ''
						UNION
						SELECT DISTINCT gerencia AS brocker FROM estruc_empleados
						WHERE gerencia <> ''
						  AND id_gerencia NOT IN (SELECT id_gerencia FROM estructura_comercial)
					) t
					ORDER BY brocker
				");
				$listaBrocker = [];
				if($resBrocker){
					while($b = mysqli_fetch_assoc($resBrocker)){ $listaBrocker[] = $b['brocker']; }
				} else {
					$debugBrocker = mysqli_error($conecta->conexion);
				}

				// Gerencia: se resuelve id_gerencia -> nombre usando estruc_empleados
				// (ahi ya existen ambas columnas para cada empleado), y se excluyen
				// las gerencias que ya estan usadas en estructura_comercial.
				$resGerencia = $conecta->consulta("
					SELECT DISTINCT id_gerencia, gerencia FROM estruc_empleados
					WHERE id_gerencia NOT IN (SELECT id_gerencia FROM estructura_comercial)
					ORDER BY gerencia
				");
				$listaGerencia = [];
				if($resGerencia){
					while($g = mysqli_fetch_assoc($resGerencia)){
						$listaGerencia[] = ['id' => (int)$g['id_gerencia'], 'nombre' => $g['gerencia']];
					}
				} else {
					$debugGerencia = mysqli_error($conecta->conexion);
				}

				// Estructura ya armada (para la tabla de abajo). Se incluye id_gerencia
				// para poder mandarlo de vuelta al eliminar/liberar una gerencia puntual.
				$resEstructura = $conecta->consulta("
					SELECT ec.sc, ec.brocker, ec.id_gerencia, ee2.gerencia
					FROM estructura_comercial ec
					LEFT JOIN (SELECT DISTINCT id_gerencia, gerencia FROM estruc_empleados) ee2
						ON ec.id_gerencia = ee2.id_gerencia
					ORDER BY ec.sc, ec.brocker
				");
				$estructuraActual = [];
				if($resEstructura){
					while($e = mysqli_fetch_assoc($resEstructura)){ $estructuraActual[] = $e; }
				} else {
					$debugEstructura = mysqli_error($conecta->conexion);
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
								<path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h1m4 0h1m-6 4h1m4 0h1m-6 4h1m4 0h1"/>
							</svg>
						</div>
						<div>
							<h4>Configuración de Estructura Comercial</h4>
						</div>
					</div>

					<div class="ec-body">
					<div class="row m-0">

						<div class="col-md-12 ec-col-form">
						<div class="ec-fields">

							<!-- SC -->
							<div class="ec-field ac-wrapper" id="wrap-sc">
								<label>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6"/></svg>
									SC
								</label>
								<input type="text" id="input-sc" class="form-control carobra" autocomplete="off" placeholder="Elige o escribe un SC..." />
								<div class="ac-dropdown" id="dd-sc"></div>
								<?php if($debugSC): ?>
									<div class="ec-hint" style="color:#c0392b;">Error SC: <?= htmlspecialchars($debugSC) ?></div>
								<?php else: ?>
									<div class="ec-hint">Si no existe, captúralo, se crea al asignar.</div>
								<?php endif; ?>
							</div>

							<!-- Brocker -->
							<div class="ec-field ac-wrapper" id="wrap-brocker">
								<label>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="7" width="18" height="13" rx="2"/><path d="M8 7V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>
									Brocker
								</label>
								<input type="text" id="input-brocker" class="form-control carobra" autocomplete="off" placeholder="Elige o escribe un Brocker..." />
								<div class="ac-dropdown" id="dd-brocker"></div>
								<?php if($debugBrocker): ?>
									<div class="ec-hint" style="color:#c0392b;">Error Brocker: <?= htmlspecialchars($debugBrocker) ?></div>
								<?php else: ?>
									<div class="ec-hint">Si no existe, captúralo, se crea al asignar.</div>
								<?php endif; ?>
							</div>

							<!-- Gerencia -->
							<div class="ec-field ac-wrapper" id="wrap-gerencia">
								<label>
									<svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M3 21h18M6 21V9l6-4 6 4v12M10 21v-6h4v6"/></svg>
									Gerencia
								</label>
								<input type="text" id="input-gerencia" class="form-control carobra" autocomplete="off" placeholder="Busca una gerencia disponible..." />
								<input type="hidden" id="hidden-id-gerencia" value="" />
								<div class="ac-dropdown" id="dd-gerencia"></div>
								<?php if($debugGerencia): ?>
									<div class="ec-hint" style="color:#c0392b;">Error Gerencia: <?= htmlspecialchars($debugGerencia) ?></div>
								<?php else: ?>
									<div class="ec-hint">Solo se muestran las gerencias aún no asignadas.</div>
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
							Asignar a Estructura Comercial
						</button>

						</div>

						<div class="col-md-12 ec-col-tabla">

				<div class="ec-tabla-header">
					<h3>Tabla de Estructura Comercial</h3>
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
						<input type="text" id="buscar-estructura" autocomplete="off" placeholder="Buscar por SC, Brocker o Gerencia..." />
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
								<th>SC</th>
								<th>Brocker</th>
								<th>Gerencia</th>
							</tr>
						</thead>
						<tbody id="body-estructura">
							<?php if($debugEstructura): ?>
								<tr>
									<td colspan="3" class="text-center" style="padding:24px;font-size:13px;color:#c0392b;">
										Error: <?= htmlspecialchars($debugEstructura) ?>
									</td>
								</tr>
							<?php elseif(empty($estructuraActual)): ?>
								<tr>
									<td colspan="3" class="text-center text-muted" style="padding:24px;font-size:13px;">
										Aún no hay nada asignado
									</td>
								</tr>
							<?php else: ?>
								<?php foreach($estructuraActual as $e): ?>
									<tr>
										<td class="te-sc"><?= $e['sc'] !== '' ? htmlspecialchars($e['sc']) : '<span class="text-muted">—</span>' ?></td>
										<td class="te-brocker"><?= $e['brocker'] !== '' ? htmlspecialchars($e['brocker']) : '<span class="text-muted">—</span>' ?></td>
										<td class="te-gerencia"><?= !empty($e['gerencia']) ? htmlspecialchars($e['gerencia']) : '<span class="text-muted">—</span>' ?></td>
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

	<!-- MODAL DETALLE DE ESTRUCTURA -->
	<div class="ec-modal-backdrop" id="ecModalBackdrop">
		<div class="ec-modal">
			<div class="ec-modal-header">
				<h3 id="ecModalTitulo"></h3>
				<button class="ec-modal-close" id="ecModalClose" type="button">✕</button>
			</div>
			<div class="ec-modal-body" id="ecModalBody"></div>
			<div class="ec-modal-footer" id="ecModalFooter">
				<div class="ec-modal-footer-nota" id="ecModalFooterNota"></div>
				<button class="btn-eliminar-estructura" id="btnEliminarEstructura" type="button">
					<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24"
						fill="none" stroke="currentColor" stroke-width="2.5"
						stroke-linecap="round" stroke-linejoin="round">
						<polyline points="3 6 5 6 21 6"/>
						<path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/>
						<path d="M10 11v6M14 11v6M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/>
					</svg>
					<span id="btnEliminarEstructuraTexto">Eliminar</span>
				</button>
			</div>
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
    const LISTA_SC          = <?= json_encode($listaSC) ?>;
    const LISTA_BROCKER     = <?= json_encode($listaBrocker) ?>;
    const LISTA_GERENCIA    = <?= json_encode($listaGerencia) ?>;
    const ESTRUCTURA_ACTUAL = <?= json_encode($estructuraActual) ?>;

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

        tbody.innerHTML = lista.map(e => `
            <tr>
                <td class="te-sc">${e.sc ? e.sc : '<span class="text-muted">—</span>'}</td>
                <td class="te-brocker">${e.brocker ? e.brocker : '<span class="text-muted">—</span>'}</td>
                <td class="te-gerencia">${e.gerencia ? e.gerencia : '<span class="text-muted">—</span>'}</td>
            </tr>
        `).join("");
    }

    document.getElementById("buscar-estructura").addEventListener("input", function () {
        const texto = this.value.trim().toLowerCase();

        if (texto === "") {
            renderFilasEstructura(ESTRUCTURA_ACTUAL);
            return;
        }

        const filtrados = ESTRUCTURA_ACTUAL.filter(e =>
            (e.sc ?? '').toLowerCase().includes(texto) ||
            (e.brocker ?? '').toLowerCase().includes(texto) ||
            (e.gerencia ?? '').toLowerCase().includes(texto)
        );

        renderFilasEstructura(filtrados);
    });

    // =========================
    // MODAL: VER SOLO LA ESTRUCTURA DE UN SC / BROCKER / GERENCIA
    // =========================
    const ecModalBackdrop = document.getElementById("ecModalBackdrop");
    const ecModalFooter   = document.getElementById("ecModalFooter");

    // Guarda qué entidad especifica (tipo + valor) está abierta en el modal,
    // para que el botón "Eliminar" del footer sepa qué borrar.
    let modalTipoActual  = null;
    let modalValorActual = null;

    function ocultarFooterEliminar() {
        modalTipoActual  = null;
        modalValorActual = null;
        ecModalFooter.classList.remove("show");
    }

    function mostrarFooterEliminar(tipo, valor, cantidadFilas) {
        modalTipoActual  = tipo;
        modalValorActual = valor;

        const textoBtn = document.getElementById("btnEliminarEstructuraTexto");
        const nota     = document.getElementById("ecModalFooterNota");

        if (tipo === "sc") {
            textoBtn.textContent = "Eliminar SC completo";
            nota.textContent = `Libera este SC y TODOS sus Brockers y Gerencias (${cantidadFilas} fila${cantidadFilas === 1 ? '' : 's'}).`;
        } else if (tipo === "brocker") {
            textoBtn.textContent = "Eliminar Brocker";
            nota.textContent = "Libera este Brocker y su Gerencia asignada. El SC no se toca.";
        } else {
            textoBtn.textContent = "Liberar Gerencia";
            nota.textContent = "Libera esta Gerencia. El SC y Brocker de esa fila no se tocan.";
        }

        ecModalFooter.classList.add("show");
    }

    function abrirModalEstructura(tipo, valor) {
        let filas  = [];
        let titulo = "";

        if (tipo === "sc") {
            filas  = ESTRUCTURA_ACTUAL.filter(e => (e.sc ?? '') === valor);
            titulo = "SC: " + valor;
        } else if (tipo === "brocker") {
            filas  = ESTRUCTURA_ACTUAL.filter(e => (e.brocker ?? '') === valor);
            titulo = "Brocker: " + valor;
        } else {
            filas  = ESTRUCTURA_ACTUAL.filter(e => (e.gerencia ?? '') === valor);
            titulo = "Gerencia: " + valor;
        }

        document.getElementById("ecModalTitulo").textContent = titulo;

        const body = document.getElementById("ecModalBody");

        if (filas.length === 0) {
            body.innerHTML = `<div class="ec-modal-empty">Sin datos para mostrar.</div>`;
        } else {
            body.innerHTML = `
                <table class="ec-modal-table">
                    <thead>
                        <tr><th>SC</th><th>Brocker</th><th>Gerencia</th></tr>
                    </thead>
                    <tbody>
                        ${filas.map(f => `
                            <tr>
                                <td>${f.sc ? f.sc : '<span class="text-muted">—</span>'}</td>
                                <td>${f.brocker ? f.brocker : '<span class="text-muted">—</span>'}</td>
                                <td>${f.gerencia ? f.gerencia : '<span class="text-muted">—</span>'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }

        // Si no hay filas, o el valor es vacío (celda "—"), no se ofrece eliminar:
        // un SC/Brocker/Gerencia en blanco no es una entidad puntual identificable,
        // borrar "por valor vacío" borraría de golpe todas las filas sin ese dato.
        if (filas.length === 0 || !valor) {
            ocultarFooterEliminar();
        } else {
            mostrarFooterEliminar(tipo, valor, filas.length);
        }

        ecModalBackdrop.classList.add("open");
    }

    // El texto "—" es solo un placeholder visual para celdas vacías;
    // al hacer clic se debe tratar como cadena vacía, no como valor real.
    function valorCelda(td) {
        const t = td.textContent.trim();
        return t === '—' ? '' : t;
    }

    document.getElementById("body-estructura").addEventListener("click", function (e) {
        const td = e.target.closest("td");
        if (!td) return;

        if (td.classList.contains("te-sc")) {
            abrirModalEstructura("sc", valorCelda(td));
        } else if (td.classList.contains("te-brocker")) {
            abrirModalEstructura("brocker", valorCelda(td));
        } else if (td.classList.contains("te-gerencia")) {
            abrirModalEstructura("gerencia", valorCelda(td));
        }
    });

    document.getElementById("ecModalClose").addEventListener("click", function () {
        ecModalBackdrop.classList.remove("open");
    });

    ecModalBackdrop.addEventListener("click", function (e) {
        if (e.target === ecModalBackdrop) ecModalBackdrop.classList.remove("open");
    });

    // =========================
    // BOTÓN "VER DETALLES" (busca en los 3 campos y abre el modal con todo lo que coincida)
    // Es una búsqueda mixta (puede traer distintos SC/Brocker/Gerencia a la vez),
    // así que aquí NO se muestra el botón de eliminar (no hay una sola entidad puntual).
    // =========================
    document.getElementById("btn-ver-detalle").addEventListener("click", function () {
        const texto = document.getElementById("buscar-estructura").value.trim();

        if (!texto) {
            Swal.fire({ icon: "warning", title: "Falta texto", text: "Escribe algo en el buscador primero." });
            return;
        }

        const textoLower = texto.toLowerCase();
        const filas = ESTRUCTURA_ACTUAL.filter(e =>
            (e.sc ?? '').toLowerCase().includes(textoLower) ||
            (e.brocker ?? '').toLowerCase().includes(textoLower) ||
            (e.gerencia ?? '').toLowerCase().includes(textoLower)
        );

        document.getElementById("ecModalTitulo").textContent = `Resultados para "${texto}"`;

        const body = document.getElementById("ecModalBody");

        if (filas.length === 0) {
            body.innerHTML = `<div class="ec-modal-empty">Sin coincidencias para "${texto}".</div>`;
        } else {
            body.innerHTML = `
                <table class="ec-modal-table">
                    <thead>
                        <tr><th>SC</th><th>Brocker</th><th>Gerencia</th></tr>
                    </thead>
                    <tbody>
                        ${filas.map(f => `
                            <tr>
                                <td>${f.sc ? f.sc : '<span class="text-muted">—</span>'}</td>
                                <td>${f.brocker ? f.brocker : '<span class="text-muted">—</span>'}</td>
                                <td>${f.gerencia ? f.gerencia : '<span class="text-muted">—</span>'}</td>
                            </tr>
                        `).join('')}
                    </tbody>
                </table>
            `;
        }

        ocultarFooterEliminar();
        ecModalBackdrop.classList.add("open");
    });

    document.getElementById("buscar-estructura").addEventListener("keydown", function (e) {
        if (e.key === "Enter") document.getElementById("btn-ver-detalle").click();
    });

    // =========================
    // EXPORTAR TABLA A EXCEL
    // =========================
    document.getElementById("btn-exportar-estructura").addEventListener("click", function () {

        if (!ESTRUCTURA_ACTUAL || ESTRUCTURA_ACTUAL.length === 0) {
            Swal.fire({ icon: "warning", title: "Sin datos", text: "Aún no hay estructura comercial para exportar." });
            return;
        }

        const headers = ["SC", "Brocker", "Gerencia"];
        const rows    = [headers];

        ESTRUCTURA_ACTUAL.forEach(e => {
            rows.push([e.sc ?? '', e.brocker ?? '', e.gerencia ?? '']);
        });

        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(rows);
        ws["!cols"] = [{wch:24},{wch:24},{wch:24}];

        XLSX.utils.book_append_sheet(wb, ws, "Estructura Comercial");

        const hoy = new Date().toISOString().slice(0,10);
        XLSX.writeFile(wb, `Estructura_Comercial_${hoy}.xlsx`);
    });

    /**
     * Autocomplete genérico.
     * modo "free"   -> permite texto libre (crea nuevo si no hay match).
     * modo "strict" -> solo permite quedarse con un valor de la lista (guarda id en hidden).
     */
    function crearAutocomplete({ wrapperId, inputId, dropdownId, items, hiddenId, modo, dotColor }) {
        const wrapper  = document.getElementById(wrapperId);
        const input    = document.getElementById(inputId);
        const dropdown = document.getElementById(dropdownId);
        const hidden   = hiddenId ? document.getElementById(hiddenId) : null;

        function itemLabel(it) { return typeof it === 'string' ? it : it.nombre; }
        function itemValue(it) { return typeof it === 'string' ? it : it.id; }

        function render() {
            const texto = input.value.trim().toLowerCase();
            const filtrados = texto === '' ? items : items.filter(it => itemLabel(it).toLowerCase().includes(texto));

            if (filtrados.length === 0) {
                if (modo === 'free') {
                    dropdown.innerHTML = `<div class="ac-empty crear">+ Se creará "${input.value.trim()}" como nuevo</div>`;
                } else {
                    dropdown.innerHTML = `<div class="ac-empty">Sin coincidencias</div>`;
                }
            } else {
                dropdown.innerHTML = filtrados.map((it, i) => `
                    <div class="ac-item" data-idx="${i}">
                        <span class="ac-dot" style="background:${dotColor}"></span>
                        <span>${itemLabel(it)}</span>
                    </div>
                `).join('');

                dropdown.querySelectorAll('.ac-item').forEach(el => {
                    el.addEventListener('click', function () {
                        const it = filtrados[parseInt(this.dataset.idx)];
                        input.value = itemLabel(it);
                        if (hidden) hidden.value = itemValue(it);
                        dropdown.classList.remove('open');
                        actualizarResumen();
                    });
                });
            }

            dropdown.classList.add('open');
        }

        input.addEventListener('input', function () {
            if (hidden) hidden.value = '';
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

    crearAutocomplete({ wrapperId: 'wrap-sc',       inputId: 'input-sc',       dropdownId: 'dd-sc',       items: LISTA_SC,       hiddenId: null,                 modo: 'free',   dotColor: '#185FA5' });
    crearAutocomplete({ wrapperId: 'wrap-brocker',  inputId: 'input-brocker',  dropdownId: 'dd-brocker',  items: LISTA_BROCKER,  hiddenId: null,                 modo: 'free',   dotColor: '#0F6E56' });
    crearAutocomplete({ wrapperId: 'wrap-gerencia', inputId: 'input-gerencia', dropdownId: 'dd-gerencia', items: LISTA_GERENCIA, hiddenId: 'hidden-id-gerencia', modo: 'strict', dotColor: '#854F0B' });

    // =========================
    // RESUMEN EN VIVO
    // =========================
    function actualizarResumen() {
        const sc        = document.getElementById("input-sc").value.trim();
        const brocker   = document.getElementById("input-brocker").value.trim();
        const gerencia  = document.getElementById("input-gerencia").value.trim();
        const resumen   = document.getElementById("ec-resumen");

        if (!sc && !brocker && !gerencia) {
            resumen.style.display = "none";
            return;
        }

        resumen.style.display = "block";
        resumen.innerHTML = `
            <b>SC:</b> ${sc ? `<span class="resumen-clic" data-tipo="sc" data-valor="${sc}">${sc}</span>` : '—'}
            &nbsp;→&nbsp;
            <b>Brocker:</b> ${brocker ? `<span class="resumen-clic" data-tipo="brocker" data-valor="${brocker}">${brocker}</span>` : '—'}
            &nbsp;→&nbsp;
            <b>Gerencia:</b> ${gerencia ? `<span class="resumen-clic" data-tipo="gerencia" data-valor="${gerencia}">${gerencia}</span>` : '—'}
        `;
    }

    // Clic en cualquiera de los 3 valores del resumen -> abre el modal de esa estructura
    document.getElementById("ec-resumen").addEventListener("click", function (e) {
        const span = e.target.closest(".resumen-clic");
        if (!span) return;
        abrirModalEstructura(span.dataset.tipo, span.dataset.valor);
    });

    // =========================
    // ASIGNAR ESTRUCTURA COMERCIAL
    // =========================
    const btnAsignar = document.getElementById("btn-asignar");

    btnAsignar.addEventListener("click", async function () {

        const sc          = document.getElementById("input-sc").value.trim();
        const brocker     = document.getElementById("input-brocker").value.trim();
        const gerenciaTxt = document.getElementById("input-gerencia").value.trim();
        const idGerencia  = document.getElementById("hidden-id-gerencia").value;

        // SC, Brocker y Gerencia son cada uno opcional: se permite
        // cualquier combinación (sc->br->ger, br->ger, ger sola,
        // sc->ger, sc->br, etc.), mientras venga al menos uno lleno.
        if (!sc && !brocker && !gerenciaTxt) {
            Swal.fire({ icon: "warning", title: "Faltan datos", text: "Llena al menos uno: SC, Brocker o Gerencia." });
            return;
        }

        if (gerenciaTxt && !idGerencia) {
            Swal.fire({ icon: "warning", title: "Gerencia inválida", text: "Selecciona una gerencia de la lista (no se pueden crear gerencias nuevas aquí)." });
            return;
        }

        const confirm = await Swal.fire({
            icon: "question",
            title: "¿Confirmar asignación?",
            html: `<b>SC:</b> ${sc || '—'}<br><b>Brocker:</b> ${brocker || '—'}<br><b>Gerencia:</b> ${gerenciaTxt || '—'}`,
            showCancelButton: true,
            confirmButtonText: "Sí, asignar",
            cancelButtonText: "Cancelar"
        });

        if (!confirm.isConfirmed) return;

        btnAsignar.disabled = true;

        try {
            const response = await fetch("estructura_comercial_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action: "asignarEstructura", sc, brocker, id_gerencia: idGerencia })
            });

            const result = await response.json();

            if (result.status === "error") {
                Swal.fire({ icon: "error", title: "No se pudo asignar", text: result.message });
                btnAsignar.disabled = false;
                return;
            }

            await Swal.fire({ icon: "success", title: "Asignado", text: "La estructura comercial se guardó correctamente." });

            // Refresca para que Brocker/Gerencia ya no muestren lo recién asignado
            location.reload();

        } catch (error) {
            Swal.fire({ icon: "error", title: "Error", text: String(error) });
            btnAsignar.disabled = false;
        }
    });

    // =========================
    // ELIMINAR SC / BROCKER / GERENCIA (desde el modal de detalle)
    // =========================
    document.getElementById("btnEliminarEstructura").addEventListener("click", async function () {

        if (!modalTipoActual || !modalValorActual) return;

        let textoConfirm = "";
        let action       = "";
        let payload      = {};

        if (modalTipoActual === "sc") {
            const n = ESTRUCTURA_ACTUAL.filter(e => (e.sc ?? '') === modalValorActual).length;
            textoConfirm = `Esto eliminará el SC "${modalValorActual}" y TODO lo que tenga asignado (${n} fila${n === 1 ? '' : 's'}: sus Brockers y Gerencias). Se libera todo para poder crear estructuras nuevas. Esta acción no se puede deshacer.`;
            action  = "eliminarSC";
            payload = { sc: modalValorActual };

        } else if (modalTipoActual === "brocker") {
            textoConfirm = `Esto eliminará el Brocker "${modalValorActual}" y su Gerencia asignada. El SC no se modifica. Esta acción no se puede deshacer.`;
            action  = "eliminarBrocker";
            payload = { brocker: modalValorActual };

        } else {
            textoConfirm = `Esto liberará la Gerencia "${modalValorActual}". El SC y Brocker de esa fila no se tocan. Esta acción no se puede deshacer.`;
            action = "eliminarGerencia";

            const fila = ESTRUCTURA_ACTUAL.find(e => (e.gerencia ?? '') === modalValorActual);
            payload = { id_gerencia: fila ? (fila.id_gerencia ?? '') : '' };

            if (!payload.id_gerencia) {
                Swal.fire({ icon: "error", title: "No se pudo identificar la gerencia", text: "Vuelve a intentarlo desde la tabla." });
                return;
            }
        }

        const confirm = await Swal.fire({
            icon: "warning",
            title: "¿Eliminar?",
            text: textoConfirm,
            showCancelButton: true,
            confirmButtonText: "Sí, eliminar",
            cancelButtonText: "Cancelar",
            confirmButtonColor: "#c0392b"
        });

        if (!confirm.isConfirmed) return;

        const btn = document.getElementById("btnEliminarEstructura");
        btn.disabled = true;

        try {
            const response = await fetch("estructura_comercial_api.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ action, ...payload })
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

</script>
</html>