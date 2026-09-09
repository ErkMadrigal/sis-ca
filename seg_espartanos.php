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
			#contenido, #contenido * { text-align: left; }

			#loader {
				display:none; position:fixed; top:0; left:0; width:100%; height:100%;
				background: rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;
			}

			.esp-section-titulo{
				font-size:15px; font-weight:800; color:#14507a; margin:4px 0 12px;
				display:flex; align-items:center; gap:8px;
			}

			/* ── Equipos ──
			   Las tarjetas de Equipo 1/2/3 se quitaron de la vista; quedan
			   dos tarjetas angostas: Premios por lugar y Rangos de meta
			   (esta última aplica igual para TODOS los espartanos, no es
			   por persona). */
			.esp-equipos-wrap{
				display:grid; grid-template-columns:minmax(280px, 380px) minmax(280px, 380px); gap:16px; margin-bottom:26px;
			}

			.esp-equipo-card{
				background:#fff; border:1px solid #e0e8ec; border-radius:14px; padding:18px 20px;
				box-shadow:0 1px 3px rgba(15,23,42,.05); border-top:4px solid var(--esp-color, #185FA5);
			}

			.esp-equipo-card[data-equipo="1"]{ --esp-color:#185FA5; }
			.esp-equipo-card[data-equipo="2"]{ --esp-color:#0F6E56; }
			.esp-equipo-card[data-equipo="3"]{ --esp-color:#B7791F; }

			.esp-equipo-nombre{ font-size:15px; font-weight:800; color:var(--esp-color, #185FA5); margin-bottom:12px; display:flex; align-items:center; gap:8px; }

			.esp-equipo-dato{ display:flex; justify-content:space-between; align-items:center; padding:7px 0; border-bottom:1px solid #f0f3f6; font-size:12.5px; }
			.esp-equipo-dato:last-child{ border-bottom:none; }
			.esp-equipo-dato label{ color:#8a94a3; font-weight:600; margin:0; }
			.esp-equipo-dato .esp-valor{ font-weight:700; color:#28323c; }
			.esp-equipo-dato .esp-valor.esp-money{ color:var(--esp-color, #185FA5); font-size:14px; }

			.esp-lugar-badge{
				display:inline-flex; align-items:center; gap:5px; font-size:12px; font-weight:700;
				padding:4px 12px; border-radius:20px; background:#f0f4f8; color:#8a94a3;
			}
			.esp-lugar-1{ background:#fff6da; color:#a06b00; }
			.esp-lugar-2{ background:#eef1f5; color:#54606e; }
			.esp-lugar-3{ background:#fdece0; color:#a05215; }

			.esp-lugar-select{
				border:1px solid #dbe4ec; border-radius:8px; padding:4px 8px; font-size:12px;
				font-weight:600; color:#28323c; background:#fff;
			}

			.esp-premios-card{
				background:linear-gradient(165deg,#eaf2fb,#f4f8fd); border:1.5px solid #cfe0f5; border-radius:14px;
				padding:18px 20px; display:flex; flex-direction:column; gap:10px;
			}
			.esp-premios-titulo{ font-size:13px; font-weight:800; color:#0f4d8a; display:flex; align-items:center; justify-content:space-between; gap:8px; margin-bottom:2px; }
			.esp-premio-row{ display:flex; justify-content:space-between; align-items:center; background:#fff; border:1px solid #dbe9fb; border-radius:10px; padding:9px 13px; font-size:12.5px; }
			.esp-premio-row label{ color:#5b7fa6; font-weight:700; margin:0; }
			.esp-premio-row .esp-valor{ font-weight:800; color:#0f4d8a; font-size:14px; }

			.btn-esp-mini{
				-webkit-appearance:none; -moz-appearance:none; appearance:none;
				background:#fff; background-image:none; box-shadow:none;
				color:#185FA5; border:1px solid #cfe0f5; padding:5px 12px; border-radius:7px;
				font-size:11.5px; font-weight:700; cursor:pointer; transition:background .15s;
				outline:none; line-height:normal; text-decoration:none; vertical-align:middle;
				display:inline-block; margin:0;
			}
			.btn-esp-mini:hover{ background:#eaf2fb; }
			.btn-esp-mini:focus, .btn-esp-mini:active{ outline:none; box-shadow:0 0 0 2px rgba(24,95,165,.25); }

			/* ── Tabla de espartanos ── */
			.esp-card{ background:#fff; border:1px solid #e0e8ec; border-radius:14px; padding:18px 20px; }

			.esp-toolbar{ display:flex; flex-wrap:wrap; gap:10px; align-items:center; margin-bottom:14px; }

			.esp-buscador{
				flex:1; min-width:220px; border:1px solid #dbe4ec; border-radius:8px; padding:8px 12px; font-size:13px;
			}
			.esp-buscador:focus{ outline:none; border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.1); }

			.esp-filtro-equipo{
				border:1px solid #dbe4ec; border-radius:8px; padding:8px 10px; font-size:13px; background:#fff;
			}

			.esp-barra-seleccion{
				display:none; align-items:center; gap:10px; background:#eaf2fb; border:1px solid #cfe0f5;
				border-radius:8px; padding:8px 14px; font-size:12.5px; color:#0f4d8a; font-weight:600; margin-bottom:12px;
			}

			.btn-esp-primario{
				background:#185FA5; color:#fff; border:none; padding:8px 18px; border-radius:8px;
				font-size:12.5px; font-weight:700; cursor:pointer; transition:background .15s;
			}
			.btn-esp-primario:hover:not(:disabled){ background:#0C447C; }
			.btn-esp-primario:disabled{ opacity:.45; cursor:not-allowed; }

			.btn-esp-secundario{
				background:#fff; color:#555; border:1px solid #bbb; padding:8px 16px; border-radius:8px;
				font-size:12.5px; cursor:pointer; transition:background .15s;
			}
			.btn-esp-secundario:hover{ background:#f5f5f5; }

			.esp-tabla-wrap{ border:1px solid #e0e8ec; border-radius:10px; }
			.esp-tabla{ width:100%; border-collapse:collapse; font-size:12.5px; }
			.esp-tabla thead th{
				background:#f0f6f9; color:#2B73B6; font-weight:700; font-size:11px; text-transform:uppercase;
				letter-spacing:.04em; padding:10px 12px; text-align:left;
			}
			.esp-tabla tbody td{ padding:9px 12px; border-bottom:1px solid #eef2f4; color:#333; vertical-align:middle; }
			.esp-tabla tbody tr:hover{ background:#f7fbfc; }

			.esp-badge-equipo{
				display:inline-flex; align-items:center; gap:5px; font-size:11px; font-weight:700;
				padding:3px 10px; border-radius:20px;
			}
			.esp-badge-equipo::before{ content:''; width:6px; height:6px; border-radius:50%; background:currentColor; }
			.esp-badge-1{ background:#e7f0fa; color:#185FA5; }
			.esp-badge-2{ background:#e3f4ee; color:#0F6E56; }
			.esp-badge-3{ background:#fbf0dd; color:#B7791F; }
			.esp-badge-sin{ background:#f0f2f5; color:#8a94a3; }
			.esp-badge-sin::before{ display:none; }

			.esp-acciones-fila{ display:flex; gap:6px; flex-wrap:wrap; justify-content:center; }

			.esp-vacio{ text-align:center; padding:30px; color:#aaa; font-size:13px; }

			/* ── Modales ── */
			.esp-modal-overlay{
				position:fixed; inset:0; background:rgba(15,23,42,.6); z-index:99999998; display:none;
				justify-content:center; align-items:center; padding:1.5rem; backdrop-filter:blur(3px);
			}
			.esp-modal{ background:#fff; border-radius:14px; width:100%; max-width:420px; box-shadow:0 25px 60px rgba(0,0,0,.3); overflow:hidden; }
			.esp-modal-header{ background:#185FA5; color:#fff; padding:14px 20px; font-size:14.5px; font-weight:700; display:flex; justify-content:space-between; align-items:center; }
			.esp-modal-header button{ background:rgba(255,255,255,.15); border:none; color:#fff; width:26px; height:26px; border-radius:7px; cursor:pointer; font-size:14px; }
			.esp-modal-header button:hover{ background:rgba(255,255,255,.28); }
			.esp-modal-body{ padding:18px 20px; display:flex; flex-direction:column; gap:14px; }
			.esp-modal-footer{ padding:14px 20px; display:flex; justify-content:flex-end; gap:10px; border-top:1px solid #eef2f4; }

			.esp-campo label{ display:block; font-size:11px; font-weight:700; color:#7b8794; text-transform:uppercase; letter-spacing:.04em; margin-bottom:5px; }
			.esp-campo input, .esp-campo select{ width:100%; border:1px solid #dbe4ec; border-radius:8px; padding:8px 11px; font-size:13px; box-sizing:border-box; }
			.esp-campo input:focus, .esp-campo select:focus{ outline:none; border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.1); }

			.esp-equipo-opciones{ display:flex; gap:8px; }
			.esp-equipo-opcion{
				flex:1; text-align:center; border:1.5px solid #dbe4ec; border-radius:9px; padding:9px 6px;
				font-size:12px; font-weight:700; cursor:pointer; color:#8a94a3; transition:all .12s;
			}
			.esp-equipo-opcion.activo{ border-color:var(--esp-color,#185FA5); color:var(--esp-color,#185FA5); background:#f5f9fd; }
			.esp-equipo-opcion[data-equipo="1"]{ --esp-color:#185FA5; }
			.esp-equipo-opcion[data-equipo="2"]{ --esp-color:#0F6E56; }
			.esp-equipo-opcion[data-equipo="3"]{ --esp-color:#B7791F; }

			.esp-modal-nombres{ font-size:12px; color:#5a6570; background:#f7fafc; border-radius:8px; padding:9px 12px; max-height:90px; overflow-y:auto; }

			.esp-modal-ancho{ max-width:640px; }

			/* ── Typeahead (buscar espartano por nombre/clave con coincidencias en dropdown) ── */
			.esp-ta-wrapper{ position:relative; }
			.esp-ta-dropdown{
				display:none; position:absolute; top:calc(100% + 2px); left:0; right:0; z-index:9000;
				background:#fff; border:1px solid #dbe4ec; border-radius:0 0 8px 8px; max-height:220px;
				overflow-y:auto; box-shadow:0 6px 16px rgba(0,0,0,.1);
			}
			.esp-ta-dropdown.open{ display:block; }
			.esp-ta-item{
				padding:8px 11px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0f0f0;
				display:flex; align-items:center; justify-content:space-between; gap:8px; transition:background .1s;
			}
			.esp-ta-item:last-child{ border-bottom:none; }
			.esp-ta-item:hover, .esp-ta-item.activo{ background:#eaf2fb; }
			.esp-ta-nombre{ color:#28323c; }
			.esp-ta-clave{ color:#8a94a3; font-size:11.5px; font-weight:700; }
			.esp-ta-vacio{ padding:10px 11px; font-size:12.5px; color:#aaa; }

			/* ── Rangos de meta (bonos escalonados) ── */
			.esp-rangos-tabla-wrap{ max-height:320px; overflow-y:auto; border:1px solid #e0e8ec; border-radius:10px; }
			.esp-rangos-tabla{ width:100%; border-collapse:collapse; font-size:12.5px; }
			.esp-rangos-tabla thead th{
				background:#f0f6f9; color:#2B73B6; font-weight:700; font-size:11px; text-transform:uppercase;
				letter-spacing:.04em; padding:10px 12px; text-align:left; position:sticky; top:0; z-index:1;
			}
			.esp-rangos-tabla tbody td{ padding:8px 10px; border-bottom:1px solid #eef2f4; }
			.esp-rangos-tabla tbody tr:hover{ background:#f7fbfc; }
			.esp-rango-input{
				width:100%; border:1px solid #cfe0f5; border-radius:7px; padding:7px 10px; font-size:13px;
				color:#185FA5; font-weight:600; box-sizing:border-box; background:#fff;
			}
			.esp-rango-input:focus{ outline:none; border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.1); }
			.esp-rango-acciones{ display:flex; gap:8px; align-items:center; }
			.btn-esp-icono{
				background:none; border:none; cursor:pointer; font-size:16px; padding:4px 6px; border-radius:6px;
				line-height:1; transition:background .12s;
			}
			.btn-esp-icono.esp-icono-guardar{ color:#185FA5; }
			.btn-esp-icono.esp-icono-guardar:hover{ background:#e7f0fa; }
			.btn-esp-icono.esp-icono-borrar{ color:#c0392b; }
			.btn-esp-icono.esp-icono-borrar:hover{ background:#fdeeee; }

			@media (max-width:700px){
				.esp-equipos-wrap{ grid-template-columns:1fr; }
			}
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
				titulo("Tabla de Incentivos");
				$pag="RETENCIONES";
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
					/* Sin esto #contenido se queda con la altura fija que
					   calcula centra_contenedor() y todo lo que no cabe
					   (la tabla completa de Espartanos, en este caso) se
					   corta en seco sin poder bajar -- con overflow-y:auto
					   aparece scroll vertical dentro del contenido en vez
					   de recortarlo. */
					$divcontenido->css("overflow-y","auto");
	?>

	<div class="container-fluid" style="padding:16px 18px; text-align:left;">

		<!-- Premios por lugar + lugar final por equipo (las 3 tarjetas de
		     Equipo 1/2/3 se quitaron; la tarjeta de Premios ya trae su
		     propio título adentro). -->
		<div class="esp-equipos-wrap" id="espEquiposWrap">
			<div class="esp-vacio" style="grid-column:1/-1;">Cargando equipos...</div>
		</div>

		<!-- Espartanos -->
		<div class="esp-card">
			<div class="esp-section-titulo" style="margin-bottom:14px;">⚔️ Espartanos</div>

			<div class="esp-toolbar">
				<input type="text" id="espBuscador" class="esp-buscador" placeholder="Buscar por nombre o clave..." oninput="filtrarTablaEspartanos()">
				<select id="espFiltroEquipo" class="esp-filtro-equipo" onchange="filtrarTablaEspartanos()">
					<option value="">Todos los equipos</option>
					<option value="sin">Sin asignar</option>
					<option value="1">Equipo 1</option>
					<option value="2">Equipo 2</option>
					<option value="3">Equipo 3</option>
				</select>
			</div>

			<div class="esp-barra-seleccion" id="espBarraSeleccion">
				<span id="espContadorSeleccion">0 seleccionados</span>
				<button type="button" class="btn-esp-primario" onclick="abrirModalAsignar()">Asignar equipo</button>
				<button type="button" class="btn-esp-secundario" onclick="limpiarSeleccion()">Cancelar selección</button>
			</div>

			<div class="esp-tabla-wrap">
				<table class="esp-tabla">
					<thead>
						<tr>
							<th style="width:34px;"><input type="checkbox" id="espCheckTodos" onchange="toggleSeleccionarTodos(this.checked)"></th>
							<th>Clave</th>
							<th>Nombre</th>
							<th>Equipo</th>
							<th>Meta individual</th>
							<th style="width:110px;">Pago Incentivos</th>
							<th style="width:110px;">Pago Asegurado</th>
							<th style="width:150px; text-align:center;">Acciones</th>
						</tr>
					</thead>
					<tbody id="espTbody">
						<tr><td colspan="8" class="esp-vacio">Cargando espartanos...</td></tr>
					</tbody>
				</table>
			</div>
		</div>

		<!-- Registro de faltas -->
		<div class="esp-card" style="margin-top:18px;">
			<div class="esp-section-titulo" style="margin-bottom:14px;">🚫 Registro de faltas</div>

			<div class="esp-toolbar">
				<select id="faltaFiltroSemana" class="esp-filtro-equipo" onchange="filtrarTablaFaltas()">
					<option value="">Todas las semanas</option>
				</select>
				<input type="text" id="faltaBuscador" class="esp-buscador" placeholder="Buscar por nombre o clave..." oninput="filtrarTablaFaltas()">
				<button type="button" class="btn-esp-primario" onclick="abrirModalFalta()">+ Registrar falta</button>
			</div>

			<div class="esp-tabla-wrap">
				<table class="esp-tabla">
					<thead>
						<tr>
							<th>Clave</th>
							<th>Nombre</th>
							<th>Semana</th>
							<th>Día de falta</th>
							<th>Registrado por</th>
							<th style="width:70px; text-align:center;">Acciones</th>
						</tr>
					</thead>
					<tbody id="faltasTbody">
						<tr><td colspan="6" class="esp-vacio">Cargando faltas...</td></tr>
					</tbody>
				</table>
			</div>
		</div>

	</div>

	<!-- Modal: asignar equipo (uno o varios) -->
	<div class="esp-modal-overlay" id="espModalAsignar">
		<div class="esp-modal">
			<div class="esp-modal-header">
				<span id="espModalAsignarTitulo">Asignar equipo</span>
				<button type="button" onclick="cerrarModalAsignar()">✕</button>
			</div>
			<div class="esp-modal-body">
				<div class="esp-modal-nombres" id="espModalNombres"></div>
				<div class="esp-campo">
					<label>Equipo</label>
					<div class="esp-equipo-opciones" id="espEquipoOpciones">
						<div class="esp-equipo-opcion" data-equipo="1" onclick="elegirEquipoModal(1)">Equipo 1</div>
						<div class="esp-equipo-opcion" data-equipo="2" onclick="elegirEquipoModal(2)">Equipo 2</div>
						<div class="esp-equipo-opcion" data-equipo="3" onclick="elegirEquipoModal(3)">Equipo 3</div>
					</div>
				</div>
				<div class="esp-campo">
					<label>Meta individual ($)</label>
					<input type="number" id="espModalMeta" min="0" step="0.01" placeholder="0.00">
				</div>
			</div>
			<div class="esp-modal-footer">
				<button type="button" class="btn-esp-secundario" id="espBtnQuitar" style="display:none; margin-right:auto;" onclick="quitarAsignacionModal()">Quitar del equipo</button>
				<button type="button" class="btn-esp-secundario" onclick="cerrarModalAsignar()">Cancelar</button>
				<button type="button" class="btn-esp-primario" onclick="guardarAsignacionModal()">Guardar</button>
			</div>
		</div>
	</div>

	<!-- Modal: editar premios -->
	<div class="esp-modal-overlay" id="espModalPremios">
		<div class="esp-modal">
			<div class="esp-modal-header">
				<span>🏆 Premios por lugar</span>
				<button type="button" onclick="cerrarModalPremios()">✕</button>
			</div>
			<div class="esp-modal-body">
				<div class="esp-campo">
					<label>1er lugar ($)</label>
					<input type="number" id="espPremio1" min="0" step="0.01" placeholder="0.00">
				</div>
				<div class="esp-campo">
					<label>2o lugar ($)</label>
					<input type="number" id="espPremio2" min="0" step="0.01" placeholder="0.00">
				</div>
				<div class="esp-campo">
					<label>3er lugar ($)</label>
					<input type="number" id="espPremio3" min="0" step="0.01" placeholder="0.00">
				</div>
			</div>
			<div class="esp-modal-footer">
				<button type="button" class="btn-esp-secundario" onclick="cerrarModalPremios()">Cancelar</button>
				<button type="button" class="btn-esp-primario" onclick="guardarPremiosModal()">Guardar</button>
			</div>
		</div>
	</div>

	<!-- Modal: rangos de meta (bonos escalonados), aparte de la meta individual -->
	<div class="esp-modal-overlay" id="espModalRangos">
		<div class="esp-modal esp-modal-ancho">
			<div class="esp-modal-header">
				<span>📊 Rangos de meta (aplica a todos)</span>
				<button type="button" onclick="cerrarModalRangos()">✕</button>
			</div>
			<div class="esp-modal-body">
				<div class="esp-modal-nombres">Bono en $ que se gana según en qué rango de ventas/producción caiga el resultado. Es UNA sola tabla, aplica igual para todos los espartanos (aparte de la meta individual de cada quien).</div>
				<div class="esp-rangos-tabla-wrap">
					<table class="esp-rangos-tabla">
						<thead>
							<tr>
								<th>Rango Inferior</th>
								<th>Rango Superior</th>
								<th>Bono ($)</th>
								<th style="width:70px;"></th>
							</tr>
						</thead>
						<tbody id="espRangosTbody">
							<tr><td colspan="4" class="esp-vacio">Cargando rangos...</td></tr>
						</tbody>
					</table>
				</div>
				<button type="button" class="btn-esp-secundario" id="espBtnAgregarRango" onclick="agregarFilaRango()">+ Agregar rango</button>
			</div>
			<div class="esp-modal-footer">
				<button type="button" class="btn-esp-secundario" onclick="cerrarModalRangos()">Cerrar</button>
			</div>
		</div>
	</div>

	<!-- Modal: registrar falta -->
	<div class="esp-modal-overlay" id="espModalFalta">
		<div class="esp-modal">
			<div class="esp-modal-header">
				<span>🚫 Registrar falta</span>
				<button type="button" onclick="cerrarModalFalta()">✕</button>
			</div>
			<div class="esp-modal-body">
				<div class="esp-campo">
					<label>Semana</label>
					<select id="faltaSemana"></select>
				</div>
				<div class="esp-campo">
					<label>Espartano</label>
					<div class="esp-ta-wrapper">
						<input type="text" id="faltaClaveSearch" placeholder="Buscar por nombre o clave..." autocomplete="off">
						<div class="esp-ta-dropdown" id="faltaClaveDropdown"></div>
					</div>
					<input type="hidden" id="faltaClave" value="">
				</div>
				<div class="esp-campo">
					<label>Día de falta</label>
					<input type="date" id="faltaDia">
				</div>
			</div>
			<div class="esp-modal-footer">
				<button type="button" class="btn-esp-secundario" onclick="cerrarModalFalta()">Cancelar</button>
				<button type="button" class="btn-esp-primario" onclick="guardarFaltaModal()">Guardar</button>
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

	const API_ESPARTANOS = 'seg_espartanos_api.php';

	async function apiEsp(payload) {
		const resp = await fetch(API_ESPARTANOS, {
			method : 'POST',
			headers: { 'Content-Type': 'application/json' },
			body   : JSON.stringify(payload)
		});
		return resp.json();
	}

	function mostrarLoader() { document.getElementById('loader').style.display = 'flex'; }
	function ocultarLoader() { document.getElementById('loader').style.display = 'none'; }

	function escaparHtml(txt) {
		const div = document.createElement('div');
		div.textContent = txt == null ? '' : String(txt);
		return div.innerHTML;
	}

	function formatoMoneda(v) {
		v = Number(v || 0);
		return '$' + v.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
	}

	/* ============================================
	ESTADO GLOBAL
	============================================ */
	var espartanosTodos   = [];   /* último listado que trajo el backend, sin filtrar */
	var espartanosSeleccionados = new Set(); /* claves marcadas con checkbox */
	var esAdminEspartanos = false;
	var equiposActuales   = [];
	var premiosActuales   = { 1:0, 2:0, 3:0 };
	var rangosPreview     = []; /* copia de solo-lectura para la tarjeta "Rangos de meta" */
	var faltasTodas       = []; /* último listado de faltas que trajo el backend, sin filtrar */
	var semanasFaltas     = []; /* semanas válidas (sale de `clawback`, a partir de la 295) */

	var LUGAR_LABEL = { 1:'🥇 1er lugar', 2:'🥈 2o lugar', 3:'🥉 3er lugar' };
	var LUGAR_CLASE = { 1:'esp-lugar-1', 2:'esp-lugar-2', 3:'esp-lugar-3' };

	/* ============================================
	CARGA INICIAL
	============================================ */
	async function cargarTodo() {
		mostrarLoader();
		try {
			await Promise.all([cargarEspartanos(), cargarEquipos(), cargarRangosPreview(), cargarSemanasFaltas(), cargarFaltas()]);
		} finally {
			ocultarLoader();
		}
	}

	/* Copia de solo-lectura de los rangos, nada más para pintar el
	   preview en la tarjeta "Rangos de meta" (el modal usa su propia
	   copia editable en rangosActuales). Se vuelve a llamar después de
	   guardar/eliminar una fila en el modal para que la tarjeta quede
	   sincronizada sin recargar toda la página. */
	async function cargarRangosPreview() {
		try {
			var json = await apiEsp({ action: 'listarRangosMeta' });
			rangosPreview = (json.status === 'ok') ? json.data : [];
		} catch (e) {
			rangosPreview = [];
		}
		renderEquipos();
	}

	async function cargarEspartanos() {
		var json = await apiEsp({ action: 'listarEspartanos' });
		if (json.status !== 'ok') {
			document.getElementById('espTbody').innerHTML =
				'<tr><td colspan="8" class="esp-vacio">' + escaparHtml(json.message || 'No se pudo cargar la lista.') + '</td></tr>';
			return;
		}
		espartanosTodos = json.data;
		filtrarTablaEspartanos();
	}

	async function cargarEquipos() {
		var json = await apiEsp({ action: 'getEquipos' });
		if (json.status !== 'ok') {
			document.getElementById('espEquiposWrap').innerHTML =
				'<div class="esp-vacio" style="grid-column:1/-1;">' + escaparHtml(json.message || 'No se pudieron cargar los equipos.') + '</div>';
			return;
		}
		equiposActuales   = json.equipos;
		premiosActuales   = json.premios || { 1:0, 2:0, 3:0 };
		esAdminEspartanos = !!json.esAdmin;
		renderEquipos();
	}

	/* ============================================
	EQUIPOS
	============================================ */
	function renderEquipos() {

		/* Ya no se muestra "Lugar final por equipo" -- eso se va a
		   definir en automático según cómo vendió cada equipo, no a
		   mano con un select aquí. Esta tarjeta ahora solo muestra los
		   3 montos de premio (editable por admin). */
		var html = '<div class="esp-premios-card">';
		html += '<div class="esp-premios-titulo"><span>🏆 Premios por lugar</span>';
		if (esAdminEspartanos) {
			html += '<button type="button" class="btn-esp-mini" onclick="abrirModalPremios()">Editar</button>';
		}
		html += '</div>';
		[1,2,3].forEach(function(l){
			html += '<div class="esp-premio-row"><label>' + LUGAR_LABEL[l] + '</label><span class="esp-valor">' + formatoMoneda(premiosActuales[l] || 0) + '</span></div>';
		});
		html += '</div>';

		/* Rangos de meta: UNA sola tabla que aplica igual para todos los
		   espartanos (no es un juego de rangos por persona) -- por eso
		   es su propia tarjeta aparte, no un botón dentro de cada fila. */
		html += '<div class="esp-premios-card">';
		html += '<div class="esp-premios-titulo"><span>📊 Rangos de meta</span>';
		html += '<button type="button" class="btn-esp-mini" onclick="abrirModalRangos()">' + (esAdminEspartanos ? 'Editar' : 'Ver') + '</button>';
		html += '</div>';
		if (!rangosPreview.length) {
			html += '<div class="esp-modal-nombres" style="margin:0;">Sin rangos capturados todavía. Bono en $ según en qué rango de ventas/producción caiga el resultado -- aplica igual para todos los espartanos.</div>';
		} else {
			rangosPreview.forEach(function(r){
				html += '<div class="esp-premio-row"><label>' + formatoMoneda(r.rango_inferior) + ' – ' + formatoMoneda(r.rango_superior) + '</label><span class="esp-valor">' + formatoMoneda(r.bono) + '</span></div>';
			});
		}
		html += '</div>';

		document.getElementById('espEquiposWrap').innerHTML = html;
	}

	function abrirModalPremios() {
		document.getElementById('espPremio1').value = premiosActuales[1] || 0;
		document.getElementById('espPremio2').value = premiosActuales[2] || 0;
		document.getElementById('espPremio3').value = premiosActuales[3] || 0;
		document.getElementById('espModalPremios').style.display = 'flex';
	}
	function cerrarModalPremios() {
		document.getElementById('espModalPremios').style.display = 'none';
	}

	async function guardarPremiosModal() {
		var premios = {
			1: parseFloat(document.getElementById('espPremio1').value) || 0,
			2: parseFloat(document.getElementById('espPremio2').value) || 0,
			3: parseFloat(document.getElementById('espPremio3').value) || 0
		};
		mostrarLoader();
		try {
			var json = await apiEsp({ action: 'guardarPremios', premios: premios });
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudieron guardar los premios.' });
				return;
			}
			cerrarModalPremios();
			await cargarEquipos();
		} finally {
			ocultarLoader();
		}
	}

	/* ============================================
	RANGOS DE META (bonos escalonados, aparte de la meta individual)

	Es UNA sola tabla compartida -- aplica igual para todos los
	espartanos, no hay un juego de rangos por persona. Se editan fila
	por fila, como la tabla del screenshot: cada renglón tiene sus
	propios inputs y su propio ícono de guardar (💾) / borrar (🗑️) --
	no hay un botón "Guardar" general del modal. rangosActuales es la
	copia en memoria de esas filas; id=0 en una fila quiere decir que
	todavía no existe en la BD (se creó con "+ Agregar rango" y aún
	no se le da guardar). Si el usuario no es admin, la tabla se ve
	nada más de lectura (sin inputs ni botones de guardar/borrar).
	============================================ */
	var rangosActuales = [];

	async function abrirModalRangos() {

		document.getElementById('espRangosTbody').innerHTML = '<tr><td colspan="4" class="esp-vacio">Cargando rangos...</td></tr>';
		document.getElementById('espBtnAgregarRango').style.display = esAdminEspartanos ? 'inline-block' : 'none';
		document.getElementById('espModalRangos').style.display = 'flex';

		mostrarLoader();
		try {
			var json = await apiEsp({ action: 'listarRangosMeta' });
			if (json.status !== 'ok') {
				document.getElementById('espRangosTbody').innerHTML =
					'<tr><td colspan="4" class="esp-vacio">' + escaparHtml(json.message || 'No se pudieron cargar los rangos.') + '</td></tr>';
				return;
			}
			rangosActuales = json.data;
			renderRangosTabla();
		} finally {
			ocultarLoader();
		}
	}

	function cerrarModalRangos() {
		document.getElementById('espModalRangos').style.display = 'none';
		rangosActuales = [];
	}

	function renderRangosTabla() {

		var tbody = document.getElementById('espRangosTbody');

		if (!rangosActuales.length) {
			tbody.innerHTML = '<tr><td colspan="4" class="esp-vacio">' +
				(esAdminEspartanos ? 'Sin rangos capturados. Usa "+ Agregar rango".' : 'Sin rangos capturados todavía.') +
				'</td></tr>';
			return;
		}

		if (!esAdminEspartanos) {
			tbody.innerHTML = rangosActuales.map(function(r){
				return '<tr>' +
					'<td>' + formatoMoneda(r.rango_inferior) + '</td>' +
					'<td>' + formatoMoneda(r.rango_superior) + '</td>' +
					'<td>' + formatoMoneda(r.bono) + '</td>' +
					'<td></td>' +
					'</tr>';
			}).join('');
			return;
		}

		tbody.innerHTML = rangosActuales.map(function(r, idx){
			return '<tr>' +
				'<td><input type="number" class="esp-rango-input" min="0" step="0.01" value="' + r.rango_inferior + '" onchange="marcarRangoDirty(' + idx + ', \'rango_inferior\', this.value)"></td>' +
				'<td><input type="number" class="esp-rango-input" min="0" step="0.01" value="' + r.rango_superior + '" onchange="marcarRangoDirty(' + idx + ', \'rango_superior\', this.value)"></td>' +
				'<td><input type="number" class="esp-rango-input" min="0" step="0.01" value="' + r.bono + '" onchange="marcarRangoDirty(' + idx + ', \'bono\', this.value)"></td>' +
				'<td class="esp-rango-acciones">' +
					'<button type="button" class="btn-esp-icono esp-icono-guardar" title="Guardar" onclick="guardarFilaRango(' + idx + ')">💾</button>' +
					'<button type="button" class="btn-esp-icono esp-icono-borrar" title="Eliminar" onclick="eliminarFilaRango(' + idx + ')">🗑️</button>' +
				'</td>' +
				'</tr>';
		}).join('');
	}

	function marcarRangoDirty(idx, campo, valor) {
		if (!rangosActuales[idx]) return;
		rangosActuales[idx][campo] = parseFloat(valor) || 0;
	}

	function agregarFilaRango() {
		rangosActuales.push({ id: 0, rango_inferior: 0, rango_superior: 0, bono: 0 });
		renderRangosTabla();
	}

	async function guardarFilaRango(idx) {

		var fila = rangosActuales[idx];
		if (!fila) return;

		if (fila.rango_superior < fila.rango_inferior) {
			Swal.fire({ icon:'warning', title:'Oops', text:'El rango superior no puede ser menor que el rango inferior.' });
			return;
		}

		mostrarLoader();
		try {
			var json = await apiEsp({
				action: 'guardarRangoMeta',
				id: fila.id || 0,
				rango_inferior: fila.rango_inferior,
				rango_superior: fila.rango_superior,
				bono: fila.bono
			});
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo guardar el rango.' });
				return;
			}
			fila.id = json.id;
			Swal.fire({ icon:'success', title:'Guardado', timer:900, showConfirmButton:false });
			cargarRangosPreview();
		} finally {
			ocultarLoader();
		}
	}

	async function eliminarFilaRango(idx) {

		var fila = rangosActuales[idx];
		if (!fila) return;

		/* Si todavía no existe en la BD (id=0, se agregó con "+ Agregar
		   rango" y nunca se guardó), nada más se quita de la lista en
		   memoria -- no hay nada que borrar en el servidor. */
		if (!fila.id) {
			rangosActuales.splice(idx, 1);
			renderRangosTabla();
			return;
		}

		var result = await Swal.fire({
			title: '¿Eliminar este rango?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#185FA5'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			var json = await apiEsp({ action: 'eliminarRangoMeta', id: fila.id });
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo eliminar el rango.' });
				return;
			}
			rangosActuales.splice(idx, 1);
			renderRangosTabla();
			cargarRangosPreview();
		} finally {
			ocultarLoader();
		}
	}

	/* ============================================
	TABLA DE ESPARTANOS
	============================================ */
	function filtrarTablaEspartanos() {

		var texto  = (document.getElementById('espBuscador').value || '').toLowerCase().trim();
		var filtro = document.getElementById('espFiltroEquipo').value;

		var filtrados = espartanosTodos.filter(function(e){
			var coincideTexto = !texto || (e.nombre || '').toLowerCase().indexOf(texto) !== -1 || (e.clave || '').toLowerCase().indexOf(texto) !== -1;
			var coincideEquipo = true;
			if (filtro === 'sin') coincideEquipo = !e.id_equipo;
			else if (filtro) coincideEquipo = (e.id_equipo === parseInt(filtro, 10));
			return coincideTexto && coincideEquipo;
		});

		renderTablaEspartanos(filtrados);
	}

	function renderTablaEspartanos(lista) {

		var tbody = document.getElementById('espTbody');

		if (!lista.length) {
			tbody.innerHTML = '<tr><td colspan="8" class="esp-vacio">Sin espartanos para mostrar.</td></tr>';
			actualizarBarraSeleccion();
			return;
		}

		/* No se deshabilitan por rol -- igual que el botón "Editar" de
		   equipo, se dejan siempre clicables; el backend (guardarPago)
		   ya rechaza el guardado si el usuario no es admin. */
		var disabledPago = '';

		tbody.innerHTML = lista.map(function(e){
			var badge = e.id_equipo
				? '<span class="esp-badge-equipo esp-badge-' + e.id_equipo + '">Equipo ' + e.id_equipo + '</span>'
				: '<span class="esp-badge-equipo esp-badge-sin">Sin asignar</span>';
			var meta = (e.id_equipo && e.meta_individual !== null) ? formatoMoneda(e.meta_individual) : '—';
			var marcado = espartanosSeleccionados.has(e.clave) ? ' checked' : '';
			var claveJs = e.clave.replace(/'/g,"\\'");
			var chkIncentivos = e.pago_incentivos ? ' checked' : '';
			var chkAsegurado  = e.pago_asegurado ? ' checked' : '';

			return '<tr>' +
				'<td><input type="checkbox" class="esp-check-fila" data-clave="' + escaparHtml(e.clave) + '"' + marcado + ' onchange="toggleSeleccion(\'' + claveJs + '\', this.checked)"></td>' +
				'<td>' + escaparHtml(e.clave) + '</td>' +
				'<td>' + escaparHtml(e.nombre) + '</td>' +
				'<td>' + badge + '</td>' +
				'<td>' + meta + '</td>' +
				'<td style="text-align:center;"><input type="checkbox"' + chkIncentivos + disabledPago + ' onchange="togglePago(\'' + claveJs + '\', \'pago_incentivos\', this.checked)"></td>' +
				'<td style="text-align:center;"><input type="checkbox"' + chkAsegurado + disabledPago + ' onchange="togglePago(\'' + claveJs + '\', \'pago_asegurado\', this.checked)"></td>' +
				'<td style="text-align:center;">' +
					'<div class="esp-acciones-fila">' +
						'<button type="button" class="btn-esp-mini" onclick=\'abrirModalAsignar(["' + e.clave.replace(/"/g,"&quot;") + '"])\'>Editar</button>' +
					'</div>' +
				'</td>' +
				'</tr>';
		}).join('');
	}

	function toggleSeleccion(clave, marcado) {
		if (marcado) espartanosSeleccionados.add(clave);
		else espartanosSeleccionados.delete(clave);
		actualizarBarraSeleccion();
	}

	/* Pago Incentivos / Pago Asegurado: se guardan al instante al
	   marcar/desmarcar, sin pasar por el modal de "Editar" -- son
	   independientes del equipo, así que no hace falta que el
	   espartano ya esté asignado para poder marcarlos. Si falla el
	   guardado, se regresa el checkbox a como estaba. */
	async function togglePago(clave, campo, valor) {

		var registro = espartanosTodos.find(function(e){ return e.clave === clave; });

		try {
			var json = await apiEsp({ action: 'guardarPago', clave: clave, campo: campo, valor: valor ? 1 : 0 });
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo guardar.' });
				if (registro) registro[campo] = !valor;
				filtrarTablaEspartanos();
				return;
			}
			if (registro) registro[campo] = valor;
		} catch (e) {
			Swal.fire({ icon:'error', title:'Error', text: 'Error de comunicación con el servidor.' });
			if (registro) registro[campo] = !valor;
			filtrarTablaEspartanos();
		}
	}

	function toggleSeleccionarTodos(marcado) {
		document.querySelectorAll('.esp-check-fila').forEach(function(chk){
			chk.checked = marcado;
			if (marcado) espartanosSeleccionados.add(chk.dataset.clave);
			else espartanosSeleccionados.delete(chk.dataset.clave);
		});
		actualizarBarraSeleccion();
	}

	function limpiarSeleccion() {
		espartanosSeleccionados.clear();
		document.getElementById('espCheckTodos').checked = false;
		filtrarTablaEspartanos();
	}

	function actualizarBarraSeleccion() {
		var n = espartanosSeleccionados.size;
		var barra = document.getElementById('espBarraSeleccion');
		barra.style.display = n > 0 ? 'flex' : 'none';
		document.getElementById('espContadorSeleccion').textContent = n + ' seleccionado' + (n === 1 ? '' : 's');
	}

	/* ============================================
	MODAL: ASIGNAR EQUIPO (uno o varios a la vez)
	============================================ */
	var espModalClaves = []; /* claves sobre las que aplica el modal abierto actualmente */
	var espModalEquipoElegido = null;

	function abrirModalAsignar(clavesForzadas) {

		espModalClaves = clavesForzadas && clavesForzadas.length
			? clavesForzadas
			: Array.from(espartanosSeleccionados);

		if (!espModalClaves.length) {
			Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona al menos un espartano.' });
			return;
		}

		var esUno = espModalClaves.length === 1;
		var registro = esUno ? espartanosTodos.find(function(e){ return e.clave === espModalClaves[0]; }) : null;

		document.getElementById('espModalAsignarTitulo').textContent = esUno ? 'Editar asignación' : 'Asignar equipo a ' + espModalClaves.length + ' espartanos';

		var nombres = espModalClaves.map(function(cl){
			var e = espartanosTodos.find(function(x){ return x.clave === cl; });
			return e ? e.nombre : cl;
		});
		document.getElementById('espModalNombres').textContent = nombres.join(', ');

		elegirEquipoModal((registro && registro.id_equipo) || null);
		document.getElementById('espModalMeta').value = (registro && registro.meta_individual !== null) ? registro.meta_individual : '';

		document.getElementById('espBtnQuitar').style.display = (esUno && registro && registro.id_equipo) ? 'inline-block' : 'none';

		document.getElementById('espModalAsignar').style.display = 'flex';
	}

	function elegirEquipoModal(idEquipo) {
		espModalEquipoElegido = idEquipo || null;
		document.querySelectorAll('#espEquipoOpciones .esp-equipo-opcion').forEach(function(op){
			op.classList.toggle('activo', parseInt(op.dataset.equipo, 10) === espModalEquipoElegido);
		});
	}

	function cerrarModalAsignar() {
		document.getElementById('espModalAsignar').style.display = 'none';
	}

	async function guardarAsignacionModal() {

		if (!espModalEquipoElegido) {
			Swal.fire({ icon:'warning', title:'Oops', text:'Elige a qué equipo va.' });
			return;
		}

		var meta = parseFloat(document.getElementById('espModalMeta').value);
		if (isNaN(meta) || meta < 0) {
			Swal.fire({ icon:'warning', title:'Oops', text:'Escribe la meta individual (en $).' });
			return;
		}

		mostrarLoader();
		try {
			var json = await apiEsp({
				action: 'asignarEquipoMasivo',
				claves: espModalClaves,
				id_equipo: espModalEquipoElegido,
				meta_individual: meta
			});
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo guardar.' });
				return;
			}
			cerrarModalAsignar();
			limpiarSeleccion();
			await cargarTodo();
		} finally {
			ocultarLoader();
		}
	}

	async function quitarAsignacionModal() {

		if (espModalClaves.length !== 1) return;

		var result = await Swal.fire({
			title: '¿Quitar del equipo?',
			text: 'Este espartano quedará sin equipo ni meta asignada.',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, quitar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#185FA5'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			var json = await apiEsp({ action: 'quitarAsignacion', clave: espModalClaves[0] });
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo quitar la asignación.' });
				return;
			}
			cerrarModalAsignar();
			await cargarTodo();
		} finally {
			ocultarLoader();
		}
	}

	/* ============================================
	REGISTRO DE FALTAS

	Semana = mismas etiquetas que ya usa el resto del sistema (salen
	en vivo de `clawback`, a partir de la semana 295), no se
	inventan aquí. Clave = cualquiera de los espartanos ya cargados
	en espartanosTodos (no hace falta que ya tenga equipo asignado).
	Un espartano solo puede tener una falta registrada por día
	(lo valida el UNIQUE de la tabla en el backend).
	============================================ */

	async function cargarSemanasFaltas() {
		try {
			var json = await apiEsp({ action: 'listarSemanasFaltas' });
			semanasFaltas = (json.status === 'ok') ? json.data : [];
		} catch (e) {
			semanasFaltas = [];
		}
		var opciones = '<option value="">Todas las semanas</option>' +
			semanasFaltas.map(function(s){ return '<option value="' + escaparHtml(s) + '">' + escaparHtml(s) + '</option>'; }).join('');
		document.getElementById('faltaFiltroSemana').innerHTML = opciones;
		document.getElementById('faltaSemana').innerHTML =
			'<option value="">Elige una semana...</option>' +
			semanasFaltas.map(function(s){ return '<option value="' + escaparHtml(s) + '">' + escaparHtml(s) + '</option>'; }).join('');
	}

	async function cargarFaltas() {
		var json = await apiEsp({ action: 'listarFaltas' });
		if (json.status !== 'ok') {
			document.getElementById('faltasTbody').innerHTML =
				'<tr><td colspan="6" class="esp-vacio">' + escaparHtml(json.message || 'No se pudo cargar el registro de faltas.') + '</td></tr>';
			return;
		}
		faltasTodas = json.data;
		filtrarTablaFaltas();
	}

	function filtrarTablaFaltas() {

		var texto  = (document.getElementById('faltaBuscador').value || '').toLowerCase().trim();
		var semana = document.getElementById('faltaFiltroSemana').value;

		var filtradas = faltasTodas.filter(function(f){
			var coincideTexto = !texto || (f.nombre || '').toLowerCase().indexOf(texto) !== -1 || (f.clave || '').toLowerCase().indexOf(texto) !== -1;
			var coincideSemana = !semana || f.semana === semana;
			return coincideTexto && coincideSemana;
		});

		renderTablaFaltas(filtradas);
	}

	function renderTablaFaltas(lista) {

		var tbody = document.getElementById('faltasTbody');

		if (!lista.length) {
			tbody.innerHTML = '<tr><td colspan="6" class="esp-vacio">Sin faltas registradas.</td></tr>';
			return;
		}

		tbody.innerHTML = lista.map(function(f){
			return '<tr>' +
				'<td>' + escaparHtml(f.clave) + '</td>' +
				'<td>' + escaparHtml(f.nombre) + '</td>' +
				'<td>' + escaparHtml(f.semana) + '</td>' +
				'<td>' + escaparHtml(f.dia_falta) + '</td>' +
				'<td>' + escaparHtml(f.registrado_por || '—') + '</td>' +
				'<td style="text-align:center;">' +
					'<button type="button" class="btn-esp-icono esp-icono-borrar" title="Eliminar" onclick="eliminarFalta(' + f.id + ')">🗑️</button>' +
				'</td>' +
				'</tr>';
		}).join('');
	}

	function abrirModalFalta() {

		document.getElementById('faltaSemana').innerHTML =
			'<option value="">Elige una semana...</option>' +
			semanasFaltas.map(function(s){ return '<option value="' + escaparHtml(s) + '">' + escaparHtml(s) + '</option>'; }).join('');

		document.getElementById('faltaClave').value = '';
		document.getElementById('faltaClaveSearch').value = '';
		document.getElementById('faltaClaveDropdown').classList.remove('open');
		document.getElementById('faltaClaveDropdown').innerHTML = '';

		document.getElementById('faltaDia').value = '';

		document.getElementById('espModalFalta').style.display = 'flex';
	}

	function cerrarModalFalta() {
		document.getElementById('espModalFalta').style.display = 'none';
	}

	/* ── Typeahead: buscar espartano por nombre/clave (client-side,
	   sobre espartanosTodos ya cargado -- coincide con cualquier
	   parte del nombre o la clave, sin importar mayúsculas/acentos
	   del acomodo original). ── */
	(function () {
		var inpFalta  = document.getElementById('faltaClaveSearch');
		var dropFalta = document.getElementById('faltaClaveDropdown');
		if (!inpFalta || !dropFalta) return;

		function normaliza(txt) {
			return (txt || '').toString().toLowerCase();
		}

		inpFalta.addEventListener('input', function () {
			var q = normaliza(this.value.trim());
			document.getElementById('faltaClave').value = '';

			if (!q) { dropFalta.classList.remove('open'); dropFalta.innerHTML = ''; return; }

			var coincidencias = espartanosTodos.filter(function(e){
				return normaliza(e.nombre).indexOf(q) !== -1 || normaliza(e.clave).indexOf(q) !== -1;
			});

			if (!coincidencias.length) {
				dropFalta.innerHTML = '<div class="esp-ta-vacio">Sin resultados</div>';
			} else {
				dropFalta.innerHTML = coincidencias.map(function(e){
					return '<div class="esp-ta-item" data-clave="' + escaparHtml(e.clave) + '" data-nombre="' + escaparHtml(e.nombre) + '">' +
						'<span class="esp-ta-nombre">' + escaparHtml(e.nombre) + '</span>' +
						'<span class="esp-ta-clave">' + escaparHtml(e.clave) + '</span>' +
						'</div>';
				}).join('');
			}
			dropFalta.classList.add('open');
		});

		dropFalta.addEventListener('mousedown', function (e) {
			var item = e.target.closest('.esp-ta-item');
			if (!item) return;
			inpFalta.value = item.dataset.nombre;
			document.getElementById('faltaClave').value = item.dataset.clave;
			dropFalta.classList.remove('open');
		});

		inpFalta.addEventListener('blur', function () {
			setTimeout(function () { dropFalta.classList.remove('open'); }, 200);
		});
	})();

	async function guardarFaltaModal() {

		var clave   = document.getElementById('faltaClave').value;
		var dia     = document.getElementById('faltaDia').value;
		var semana  = document.getElementById('faltaSemana').value;

		if (!clave) {
			Swal.fire({ icon:'warning', title:'Oops', text:'Elige al espartano.' });
			return;
		}
		if (!dia) {
			Swal.fire({ icon:'warning', title:'Oops', text:'Elige el día de la falta.' });
			return;
		}
		if (!semana) {
			Swal.fire({ icon:'warning', title:'Oops', text:'Elige la semana.' });
			return;
		}

		mostrarLoader();
		try {
			var json = await apiEsp({ action: 'guardarFalta', clave: clave, dia_falta: dia, semana: semana });
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo registrar la falta.' });
				return;
			}
			cerrarModalFalta();
			Swal.fire({ icon:'success', title:'Registrada', timer:1200, showConfirmButton:false });
			await cargarFaltas();
		} finally {
			ocultarLoader();
		}
	}

	async function eliminarFalta(id) {

		var result = await Swal.fire({
			title: '¿Eliminar esta falta?',
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Sí, eliminar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#185FA5'
		});
		if (!result.isConfirmed) return;

		mostrarLoader();
		try {
			var json = await apiEsp({ action: 'eliminarFalta', id: id });
			if (json.status !== 'ok') {
				Swal.fire({ icon:'error', title:'Error', text: json.message || 'No se pudo eliminar la falta.' });
				return;
			}
			await cargarFaltas();
		} finally {
			ocultarLoader();
		}
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

		cargarTodo();
	});

	</script>
</html>