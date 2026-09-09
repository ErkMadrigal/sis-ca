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
		<script type="text/javascript" src="./js/autocompletar.js"></script>
		<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">


		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>

		<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>

		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">

		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>


		<!-- compiled function datepicker -->
		<link rel="stylesheet" href="./css/datepicker.css">
		<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
		<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css"/>

		<style>
			/* =========================
			CARD GENERAL
			========================= */
			.card-vacaciones {
				border-radius: 20px;
				padding: 30px;
				background: #ffffff;
				box-shadow: 0 10px 30px rgba(0,0,0,0.08);
			}

			/* =========================
			CAJAS DE VACACIONES
			========================= */
			.badge-box {
				border-radius: 12px;
				padding: 12px 16px;
				color: #fff;
				transition: all 0.25s ease;
			}

			.badge-box h5 {
				font-size: 13px;
				margin-bottom: 4px;
			}

			.badge-box h1 {
				font-size: 26px;
				margin: 2px 0;
			}

			.badge-box small {
				font-size: 11px;
				opacity: 0.85;
			}

			.badge-box .btn-primary {
				padding: 6px 10px;
				font-size: 13px;
				margin-top: 6px;
			}

			.badge-box:hover {
				transform: translateY(-5px);
			}

			/* Colores */
			.bg-soft-green {
				background: linear-gradient(135deg, #2384c5, #004979);
			}

			.bg-soft-yellow {
				background: linear-gradient(135deg, #facc1591, #f3b704b4);
				color: #000;
			}

			.bg-soft-danger{
				background: linear-gradient(135deg, #c528286c, #ea0c08b9);
				color: #000;
			}

			/* =========================
			TABS (BOOTSTRAP DOMADO)
			========================= */

			/* contenedor */
			.nav-tabs {
				border: 0 !important;
				border-bottom: 0 !important;
				display: flex;
				gap: 10px;
				padding-bottom: 2px;
				flex-wrap: wrap;
			}

			/* reset brutal */
			.nav-tabs .nav-item {
				margin-bottom: 0 !important;
			}

			.nav-tabs .nav-link {
				margin-bottom: 0 !important;
				border: 0 !important;
				border-bottom: 0 !important;
				border-color: transparent !important;
				background: #f1f5f9 !important;
				color: #334155 !important;
				border-radius: 10px !important;
				padding: 10px 18px !important;
				transition: all 0.25s ease;
				position: relative;
			}

			/* hover */
			.nav-tabs .nav-link:hover {
				background: #e2e8f0 !important;
				color: #1e293b !important;
				transform: translateY(-1px);
			}

			/* activo */
			.nav-tabs .nav-link.active {
				background: linear-gradient(135deg, #2384c5, #0072bc) !important;
				color: #fff !important;
				box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3) !important;
			}

			/* quitar focus feo */
			.nav-tabs .nav-link:focus {
				outline: none !important;
				box-shadow: none !important;
			}

			/* 💀 eliminar cualquier linea fantasma */
			.nav-tabs::before,
			.nav-tabs::after,
			.nav-tabs .nav-link::before,
			.nav-tabs .nav-link::after {
				display: none !important;
				content: none !important;
			}

			/* quitar cualquier borde residual bootstrap */
			.nav-tabs .nav-link,
			.nav-tabs .nav-link:hover,
			.nav-tabs .nav-link.active {
				border: none !important;
				border-bottom: none !important;
				border-color: transparent !important;
			}

			/* contenido */
			.tab-content {
				border-top: 0 !important;
			}

			/* =========================
			ALERTAS Y TEXTO
			========================= */
			.text-muted {
				color: #64748b !important;
			}

			/* =========================
			BOTONES
			========================= */
			.btn-primary {
				background: linear-gradient(135deg, #2384c5, #0072bc);
				border: none;
				border-radius: 10px;
				transition: 0.2s;
			}

			.btn-primary:hover {
				background: linear-gradient(135deg, #2384c5, #0072bc);
				transform: translateY(-1px);
			}

			/* =========================
			INPUTS
			========================= */
			input.form-control {
				border-radius: 10px;
				border: 1px solid #e2e8f0;
				transition: 0.2s;
			}

			input.form-control:focus {
				border-color: #2384c5;
				box-shadow: 0 0 0 2px rgba(37, 100, 235, 0.03);
			}

			.bg-soft-yellow {
				display: flex;
				flex-direction: column;
				justify-content: center;
			}

			.detalle-vacaciones {
				background: rgba(255,255,255,0.7);
				padding: 10px;
				border-radius: 10px;
				font-size: 14px;
				margin-top: 10px;
			}
			.vac-card {
				border-radius: 10px;
				padding: 12px;
				margin-bottom: 10px;
				background: #f8fafc;
				border-left: 5px solid #ccc;
				transition: 0.2s;
			}

			.vac-card:hover {
				transform: translateX(3px);
			}

			.vac-header {
				display: flex;
				justify-content: space-between;
				font-weight: 600;
				margin-bottom: 5px;
			}

			.vac-body {
				font-size: 14px;
				color: #334155;
			}

			/* BADGES */
			.vac-badge {
				padding: 3px 8px;
				border-radius: 8px;
				font-size: 12px;
				color: #fff;
			}

			/* COLORES */
			.vac-card.proximo {
				border-color: #facc15;
			}

			.vac-badge.proximo {
				background: #facc15;
				color: #000;
			}

			.vac-card.caducado {
				border-color: #ef4444;
			}

			.vac-badge.caducado {
				background: #ef4444;
			}

			.dot {
				display: inline-block;
				width: 12px;
				height: 12px;
				border-radius: 50%;
			}

			.dot-blue {
				background: #3b82f6;
			}

			.dot-green {
				background: #22c55e;
			}

			.dot-red {
				background: #ef4444;
			}

			.vac-card {
				display: flex;
				justify-content: space-between;
				align-items: center;
				border-radius: 12px;
				padding: 15px 20px;
				background: #fff;
				box-shadow: 0 5px 15px rgba(0,0,0,0.05);
				border-left: 6px solid;
				transition: 0.2s;
			}

			.vac-card:hover {
				transform: translateX(4px);
			}

			/* colores */
			.vac-green {
				border-color: #22c55e;
			}

			.vac-yellow {
				border-color: #facc15;
			}

			.vac-red {
				border-color: #ef4444;
			}

			.vac-primary{
				border-color: #3b82f6;
			}

			/* texto */
			.vac-info h6 {
				margin: 0;
				font-size: 14px;
				color: #64748b;
			}

			.vac-info h2 {
				margin: 5px 0 0;
				font-weight: bold;
			}

			/* icono */
			.vac-icon {
				font-size: 28px;
				opacity: 0.7;
			}

			.card-info{
				align-items: center;
				border-radius: 12px;
				padding: 15px 20px;
				background: #fff;
				box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
				border-left: 6px solid;
				transition: 0.2s;
				border-color: #3b82f6;
			}

			.vac-card {
				padding: 15px;
				border-radius: 10px;
				color: #fff;
				cursor: pointer;
				transition: 0.2s;
			}
			.vac-card:hover {
				transform: scale(1.05);
			}
			.vac-green {
				background: linear-gradient(135deg, #2384c5, #004979);
			 }
			.vac-yellow {
				background: linear-gradient(135deg, #facc1591, #f3b704b4); color:#000; }
			.vac-red { background: linear-gradient(135deg, #c528286c, #ea0c08b9); }

			.vac-badge.disponible {
				background: #0d6efd; /* azul bootstrap */
				color: #fff;
				padding: 4px 10px;
				border-radius: 20px;
				font-size: 12px;
			}

			/* ── Doble aprobación: tarjetas comparativas jefe/RRHH ── */
			.pendiente-card {
				background: #fff;
				border-radius: 14px;
				padding: 16px;
				box-shadow: 0 6px 18px rgba(0,0,0,0.06);
				border-left: 5px solid #f59e0b;
				margin-bottom: 14px;
			}

			.pendiente-card .pendiente-header {
				display: flex;
				justify-content: space-between;
				align-items: center;
				margin-bottom: 10px;
			}

			.pendiente-aprob-wrap {
				display: flex;
				gap: 10px;
				margin: 10px 0;
				flex-wrap: wrap;
			}

			.pendiente-aprob-item {
				flex: 1;
				min-width: 150px;
				background: #f8fafc;
				border-radius: 8px;
				padding: 8px 12px;
				border: 1px solid #e2e8f0;
				font-size: 12.5px;
			}

			.pendiente-aprob-item.ok { border-color:#16a34a; background:#f0fdf4; }
			.pendiente-aprob-item.rechazado { border-color:#dc2626; background:#fef2f2; }

			.pendiente-aprob-item .titulo {
				font-size: 10.5px;
				font-weight: 700;
				color: #64748b;
				text-transform: uppercase;
				margin-bottom: 3px;
			}

			.pendiente-comentario {
				font-style: italic;
				color: #475569;
				margin-top: 3px;
			}

			.btn-masivas {
				background: linear-gradient(135deg, #2384c5, #0072bc);
				border: none;
				color: #fff;
				border-radius: 10px;
				padding: 9px 18px;
				font-size: 13px;
				font-weight: 600;
				transition: 0.2s;
			}

			.btn-masivas:hover {
				transform: translateY(-1px);
				color: #fff;
			}

			.btn-aprobar-todas {
				background: #fff;
				border: 2px solid #0072bc;
				color: #0072bc;
				border-radius: 10px;
				padding: 9px 18px;
				font-size: 13px;
				font-weight: 600;
				transition: 0.2s;
			}

			.btn-aprobar-todas:hover {
				background: #0072bc;
				color: #fff;
				transform: translateY(-1px);
			}

			/* Variante para "saltar" al jefe directo: mismo estilo, color
			   de advertencia porque fuerza la aprobación de su parte. */
			.btn-aprobar-todas-saltar {
				background: #fff;
				border: 2px solid #dc2626;
				color: #dc2626;
				border-radius: 10px;
				padding: 9px 18px;
				font-size: 13px;
				font-weight: 600;
				transition: 0.2s;
			}

			.btn-aprobar-todas-saltar:hover {
				background: #dc2626;
				color: #fff;
				transform: translateY(-1px);
			}

			/* ── Fix: con modales anidados (Bootstrap 4) subiendo z-index
			   dinámicamente, SweetAlert2 (z-index 1060 por defecto) puede
			   quedar por DEBAJO de un modal apilado. Lo forzamos siempre
			   por encima de cualquier modal/backdrop que podamos abrir. ── */
			.swal2-container {
				z-index: 20000 !important;
			}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>
		<div id="loader" style="
			display:none;
			position: fixed;
			top:0;
			left:0;
			width:100%;
			height:100%;
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

		function diasPorAnio($anios) {
			if ($anios == 1) return 12;
			if ($anios == 2) return 14;
			if ($anios == 3) return 16;
			if ($anios == 4) return 18;
			if ($anios == 5) return 20;
			if ($anios >= 6 && $anios <= 10) return 22;
			if ($anios >= 11 && $anios <= 15) return 24;
			if ($anios >= 16 && $anios <= 20) return 26;
			if ($anios >= 21 && $anios <= 25) return 28;
			if ($anios >= 26) return 30;
			return 0;
		}

		function calcularVacacionesDetalle($fechaIngreso) {

			if (empty($fechaIngreso) || $fechaIngreso == '0000-00-00') {
				return [
					'total' => 0,
					'disponibles' => 0,
					'proximos' => 0,
					'caducados' => 0,
					'detalle' => []
				];
			}

			$hoy = new DateTime();
			$ingreso = new DateTime($fechaIngreso);
			$anios = $ingreso->diff($hoy)->y;

			$diasDisponibles = 0;
			$diasProximosVencer = 0;
			$diasCaducados = 0;
			$detalle = [];

			for ($i = 1; $i <= $anios; $i++) {

				$fechaAniversario = clone $ingreso;
				$fechaAniversario->modify("+$i year");

				// ── Ignorar periodos anteriores a 2025 ──
				if ((int)$fechaAniversario->format('Y') < 2025) {
					continue;
				}

				$fechaVencimiento = clone $fechaAniversario;
				$fechaVencimiento->modify("+18 months");

				$dias = diasPorAnio($i);

				if ($fechaVencimiento < $hoy) {

					$diasCaducados += $dias;

					$detalle[] = [
						'periodo_inicio' => $fechaAniversario->format('d/m/Y'),
						'vence'          => $fechaVencimiento->format('d/m/Y'),
						'dias'           => $dias,
						'tipo'           => 'caducado'
					];

					continue;
				}

				$tresMeses = clone $hoy;
				$tresMeses->modify("+3 months");

				if ($fechaVencimiento <= $tresMeses) {

					$diasProximosVencer += $dias;

					$detalle[] = [
						'periodo_inicio' => $fechaAniversario->format('d/m/Y'),
						'vence'          => $fechaVencimiento->format('d/m/Y'),
						'dias'           => $dias,
						'tipo'           => 'proximo'
					];

				} else {

					$diasDisponibles += $dias;

					$detalle[] = [
						'periodo_inicio' => $fechaAniversario->format('d/m/Y'),
						'vence'          => $fechaVencimiento->format('d/m/Y'),
						'dias'           => $dias,
						'tipo'           => 'vigente'
					];
				}
			}

			return [
				'total'      => $diasDisponibles + $diasProximosVencer,
				'disponibles'=> $diasDisponibles,
				'proximos'   => $diasProximosVencer,
				'caducados'  => $diasCaducados,
				'detalle'    => $detalle
			];
		}


		function calcularVacacionesConUsos($fechaIngreso, $conecta, $clave) {

			if (empty($fechaIngreso) || $fechaIngreso == '0000-00-00') {
				return [
					'disponibles' => 0, 'proximos' => 0,
					'caducados'   => 0, 'total'    => 0,
					'usados'      => 0, 'detalle'  => []
				];
			}

			$hoy     = new DateTime();
			$ingreso = new DateTime($fechaIngreso);
			$anios   = $ingreso->diff($hoy)->y;

			$query = "SELECT fecha_inicio, dias_tomados
					FROM solicitudes_vacaciones
					WHERE clave = '$clave' AND estatus = 1
					AND YEAR(fecha_inicio) >= 2025
					ORDER BY fecha_inicio ASC";

			$res  = $conecta->consulta($query);
			$usos = [];
			while ($row = mysqli_fetch_assoc($res)) {
				$usos[] = [
					'fecha_inicio' => $row['fecha_inicio'],
					'dias'         => (int)$row['dias_tomados'],
					'restante'     => (int)$row['dias_tomados'],
				];
			}

			$totalUsados = 0;
			foreach ($usos as $u) $totalUsados += $u['dias'];

			$periodos = [];

			for ($i = 1; $i <= $anios; $i++) {

				$fechaAniversario     = (clone $ingreso)->modify("+$i year");

				// ── Ignorar periodos anteriores a 2025 ──────────────────────
				if ((int)$fechaAniversario->format('Y') < 2025) {
					continue;
				}
				// ────────────────────────────────────────────────────────────

				$siguienteAniversario = (clone $ingreso)->modify("+" . ($i + 1) . " year");
				$fechaVencimiento     = (clone $fechaAniversario)->modify("+18 months");
				$diasPeriodo          = diasPorAnio($i);
				$tresMeses            = (clone $hoy)->modify("+3 months");

				if ($fechaVencimiento < $hoy) {
					$tipo = 'caducado';
				} elseif ($fechaVencimiento <= $tresMeses) {
					$tipo = 'proximo';
				} else {
					$tipo = 'vigente';
				}

				$periodos[] = [
					'anio'                  => $i,
					'periodo_inicio'        => $fechaAniversario->format('d/m/Y'),
					'aniversario'           => $fechaAniversario,
					'siguiente_aniversario' => $siguienteAniversario,
					'vence'                 => $fechaVencimiento->format('d/m/Y'),
					'vence_dt'              => clone $fechaVencimiento,
					'dias_brutos'           => $diasPeriodo,
					'tipo'                  => $tipo,
				];
			}

			/* BUG FIX: antes solo se armaban los periodos NO caducados
			   (tipo !== 'caducado') como candidatos para consumir uso.
			   Eso hacía que, si un periodo ya venció HOY, quedara fuera
			   del reparto aunque los días se hayan tomado ANTES de que
			   venciera — el uso entonces "se recorría" y se descontaba
			   por error del siguiente periodo (todavía vigente), dejando
			   el periodo vencido completo como caducado (sin descontar
			   lo ya usado) y el periodo vigente con menos días de la
			   cuenta. El consumo FIFO debe repartirse entre TODOS los
			   periodos en orden cronológico (haya vencido o no ese
			   periodo a día de hoy); lo que sobre en cada uno, tras
			   restar lo consumido, es lo que se clasifica como
			   disponible/próximo/caducado más abajo. */
			$idxDisponibles = array_keys($periodos);

			$consumoPorPeriodo = array_fill(0, count($periodos), 0);

			/* Días usados que no alcanzan a cubrirse con NINGÚN periodo
			   ya generado (típico: RRHH registra un día directo para
			   alguien que aún no cumple su primer año, o que ya agotó
			   hasta el último periodo existente). Es un "adelanto": se
			   resta de los días disponibles más abajo, pudiendo dejarlos
			   en negativo, como recordatorio de que ese día ya se tomó
			   y se debe cargar contra el siguiente periodo que se genere
			   (en cuanto exista, este mismo FIFO ya lo absorbe solo,
			   porque el uso sigue en la BD con su fecha original). */
			$diasNoCubiertos = 0;

			foreach ($usos as $uso) {
				$diasPorAsignar = $uso['dias'];
				$usoFecha       = new DateTime($uso['fecha_inicio']);

				foreach ($idxDisponibles as $idx) {
					if ($diasPorAsignar <= 0) break;

					/* AJUSTE: un uso solo puede descontarse de un periodo
					   que TODAVÍA estaba vigente el día en que se tomó.
					   Si el periodo ya venció ANTES de la fecha del uso,
					   esos días caducados ya se perdieron para siempre --
					   un uso posterior (ej. tomado ya en 2026) no puede
					   "recuperarlos" y descontarse de un periodo 2025 que
					   para esa fecha ya estaba vencido. Sin este candado,
					   el FIFO cronológico global terminaba restando usos
					   nuevos del periodo viejo ya caducado en vez del
					   periodo vigente que le correspondía, y de paso
					   "revivía" días que ya habían caducado. */
					if ($periodos[$idx]['vence_dt'] < $usoFecha) continue;

					$disponibleEnPeriodo = $periodos[$idx]['dias_brutos'] - $consumoPorPeriodo[$idx];

					if ($disponibleEnPeriodo <= 0) continue;

					$tomarDeEste = min($diasPorAsignar, $disponibleEnPeriodo);
					$consumoPorPeriodo[$idx] += $tomarDeEste;
					$diasPorAsignar          -= $tomarDeEste;
				}

				$diasNoCubiertos += $diasPorAsignar;
			}

			$diasDisponibles = 0;
			$diasProximos    = 0;
			$diasCaducados   = 0;
			$detalle         = [];

			foreach ($periodos as $idx => $p) {
				$usadosEnPeriodo = $consumoPorPeriodo[$idx];
				$diasRestantes   = max(0, $p['dias_brutos'] - $usadosEnPeriodo);

				switch ($p['tipo']) {
					case 'caducado': $diasCaducados   += $diasRestantes; break;
					case 'proximo':  $diasProximos    += $diasRestantes; break;
					default:         $diasDisponibles += $diasRestantes; break;
				}

				$detalle[] = [
					'anio'           => $p['anio'],
					'periodo_inicio' => $p['periodo_inicio'],
					'vence'          => $p['vence'],
					'dias'           => $diasRestantes,
					'usados'         => $usadosEnPeriodo,
					'tipo'           => $p['tipo'],
				];
			}

			// El adelanto sin periodo se descuenta de disponibles (puede
			// quedar en negativo: significa que se debe ese día).
			$diasDisponibles -= $diasNoCubiertos;

			return [
				'disponibles' => $diasDisponibles,
				'proximos'    => $diasProximos,
				'caducados'   => $diasCaducados,
				'total'       => $diasDisponibles + $diasProximos,
				'usados'      => $totalUsados,
				'detalle'     => $detalle,
			];
		}

		function getPeriodoDisponible(&$vacaciones, $dias_usados){

			$usados = $dias_usados;

			// FIFO
			foreach ($vacaciones['detalle'] as &$periodo) {

				if ($usados <= 0) break;

				if ($periodo['tipo'] !== 'caducado') {

					if ($usados >= $periodo['dias']) {
						$usados -= $periodo['dias'];
						$periodo['dias'] = 0;
					} else {
						$periodo['dias'] -= $usados;
						$usados = 0;
					}
				}
			}
			unset($periodo);

			// limpiar periodos en 0
			$vacaciones['detalle'] = array_values(array_filter($vacaciones['detalle'], function($d){
				return (int)$d['dias'] > 0;
			}));

			// devolver el periodo correcto
			foreach ($vacaciones['detalle'] as $p) {

				if (($p['tipo'] === 'proximo' || $p['tipo'] === 'vigente')) {
					return $p;
				}
			}

			return null;
		}

		/* getEstatus ahora recibe estatus_jefe / estatus_rrhh y arma el
		   estatus general visible: aprobado solo si AMBOS aprobaron,
		   rechazado si CUALQUIERA rechazó, pendiente en cualquier otro caso. */
		function getEstatus($estatusJefe, $estatusRrhh = null){

			if ($estatusRrhh === null) {
				// compatibilidad con llamadas viejas de un solo argumento
				$estatus = $estatusJefe;
				switch ($estatus) {
					case 0: return '<span class="badge badge-primary">Pendiente</span>';
					case 1: return '<span class="badge badge-success">Aprobado</span>';
					case 2: return '<span class="badge badge-danger">Rechazado</span>';
					default: return '<span class="badge badge-dark">Desconocido</span>';
				}
			}

			if ($estatusJefe == 2 || $estatusRrhh == 2) {
				return '<span class="badge badge-danger">Rechazado</span>';
			}
			if ($estatusJefe == 1 && $estatusRrhh == 1) {
				return '<span class="badge badge-success">Aprobado</span>';
			}
			if ($estatusJefe == 1 && $estatusRrhh == 0) {
				return '<span class="badge badge-warning">Falta RRHH</span>';
			}
			if ($estatusJefe == 0 && $estatusRrhh == 1) {
				return '<span class="badge badge-warning">Falta Jefe</span>';
			}
			return '<span class="badge badge-primary">Pendiente</span>';
		}

		function formatoFecha($fecha){

			if (empty($fecha) || $fecha == '0000-00-00') {
				return '';
			}

			$timestamp = strtotime($fecha);

			if (!$timestamp) {
				return '';
			}

			return date('d/m/Y', $timestamp);
		}

		function getEstatusDot($estatus){

			if ($estatus === null) return '';

			switch ($estatus) {
				case 0:
					return '<span class="badge badge-primary">Enviado</span>';
				case 1:
					return '<span class="badge badge-success">autorizado</span>';
				case 2:
					return '<span class="badge badge-danger">rechazado</span>';
				default:
					return '';
			}

		}

		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo = (strtotime($fa)-strtotime($fg));

		// var_dump($periodo);
     	if($periodo > 600)
     	{
			session_destroy();
			session_unset();
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.");
			$sesion_login = true;


    	}
    	else
    	{
    		$_SESSION["ultimoAcceso"] = $fa;
    		$_SESSION["autentifica"] = "p";

		}

		if ($_SESSION["autentifica"] != "p")
		{
			session_destroy();
			session_unset();
			// mensaje("entre2");
			$sesion_login = true;

		}
		else
		{

			//--------------------------------------------------------------------------------------------------
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
				titulo("Vacaciones");
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
				//----------------------------------------------------------------------------Declaración de objetos
				$conecta= new conector();

				$nivel= nivel_usuario(utf8_decode($_SESSION["nombre"]));
				$query="Select clave from empl_usuarios
						where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);
				$clave=$tb['clave'];

				$ne=0;
				$usuario=$_SESSION["nombre"];


	?>



					<div class="container">

						<div class="row justify-content-center">
							<div class="col-md-12">

								<?php
									// ─── QUERY USUARIO ACTUAL ───────────────────────────────────────
									$query_data = "SELECT em.no_empleado, em.clave,
													STR_TO_DATE(
														COALESCE(NULLIF(em.fec_reingreso, ''), em.fec_ingreso),
														'%d/%m/%Y'
													) AS fec_ingreso, us.email, us.nombre, em.jefe_inmediato
												FROM carobra.empleados em
												LEFT JOIN usuarios us ON em.clave = us.clave
												WHERE us.activo = 1
												AND us.nombre = '$usuario'
												LIMIT 1";

									$data_u   = $conecta->consulta($query_data);
									$data_usr = mysqli_fetch_array($data_u);

									// ─── VACACIONES CON NUEVA FUNCIÓN (ya trae todo calculado) ──────
									$vacaciones  = calcularVacacionesConUsos($data_usr['fec_ingreso'], $conecta, $data_usr['clave']);
									$dias_usados = $vacaciones['usados'];
									$proximos    = $vacaciones['proximos'];

									// Periodo del año para el botón de solicitar
									$periodo_anio = date('Y');
									foreach ($vacaciones['detalle'] as $d) {
										if ($d['tipo'] === 'vigente' || $d['tipo'] === 'proximo') {
											$periodo_anio = explode('/', $d['periodo_inicio'])[2];
											break;
										}
									}

									$jefe_directo = $data_usr['jefe_inmediato'];

									// ─── HISTORIAL DEL USUARIO ACTUAL ───────────────────────────────
									$query_historial = "SELECT sv.fecha_captura, sv.fecha_inicio, sv.fecha_final,
															sv.dias_tomados, sv.periodo, sv.descripcion, sv.estatus,
															sv.estatus_jefe, sv.estatus_rrhh, sv.comentario_jefe, sv.comentario_rrhh
														FROM solicitudes_vacaciones sv
														LEFT JOIN usuarios u ON sv.clave = u.clave
														WHERE u.nombre = '$usuario'
														AND YEAR(sv.fecha_inicio) >= 2025
														ORDER BY sv.fecha_captura DESC";

									$data_t     = $conecta->consulta($query_historial);
									$data_table = [];
									while ($row = mysqli_fetch_assoc($data_t)) {
										$data_table[] = $row;
									}

									// ─── PERMISO RRHH ─────────────────────────────────────────────
									// (se calcula aquí, antes de "Mi Equipo", porque un administrador
									// con permiso de RRHH ve a TODOS los empleados en ese tab, no solo
									// a sus subordinados directos)
									$clave_RRHH      = $data_usr['clave'];
									$sql_RRHH        = "SELECT vista_RRHH ok FROM vista_vacaciones WHERE clave = '$clave_RRHH'";
									$permiso_RRHH    = $conecta->consulta($sql_RRHH);
									$data_RRHH       = mysqli_fetch_array($permiso_RRHH);
									$tienePermisoRRHH = ($data_RRHH && intval($data_RRHH['ok']) === 1);

									// ─── MI EQUIPO: subordinados directos del usuario en sesión ─────
									// Solo gente que reporta directamente a este usuario (jefe_inmediato
									// = su clave). Si no es jefe de nadie, esta lista simplemente sale
									// vacía y el tab no aparece -- no hay caso especial para admin/RRHH
									// aquí, eso ya lo cubre el tab de Recursos Humanos aparte.
									$query_subordinados = "SELECT e.no_empleado, e.clave, u.nombre,
																STR_TO_DATE(
																	COALESCE(NULLIF(e.fec_reingreso, ''), e.fec_ingreso),
																	'%d/%m/%Y'
																) AS fec_ingreso
															FROM carobra.empleados e
															INNER JOIN usuarios u ON e.clave = u.clave
															WHERE TRIM(UPPER(e.jefe_inmediato)) = TRIM(UPPER('" . mysqli_real_escape_string($conecta->conexion, $data_usr['clave']) . "'))
															AND u.activo = 1
															ORDER BY u.nombre";

									$res_subordinados  = $conecta->consulta($query_subordinados);
									$data_subordinados = [];
									while ($row = mysqli_fetch_assoc($res_subordinados)) {
										$data_subordinados[] = $row;
									}
									$tieneSubordinados = count($data_subordinados) > 0;

									// ─── LISTA DE EMPLEADOS PARA RRHH ───────────────────────────────
									$query_empleados = "SELECT
															e.no_empleado, e.clave, u.nombre,
															STR_TO_DATE(
																COALESCE(NULLIF(e.fec_reingreso, ''), e.fec_ingreso),
																'%d/%m/%Y'
															) AS fec_ingreso,
															CASE
																WHEN SUM(sv.estatus = 0) > 0 THEN 0
																ELSE (
																	SELECT sv2.estatus
																	FROM solicitudes_vacaciones sv2
																	WHERE sv2.clave = e.clave
																	ORDER BY sv2.fecha_captura DESC
																	LIMIT 1
																)
															END AS estatus
														FROM carobra.empleados e
														INNER JOIN usuarios u ON e.clave = u.clave
														LEFT JOIN solicitudes_vacaciones sv ON e.clave = sv.clave
														WHERE e.staff = 1 AND u.activo = 1
														GROUP BY e.no_empleado, e.clave, u.nombre, e.fec_ingreso";

									$data_u       = $conecta->consulta($query_empleados);
									$data_usuarios = [];
									while ($row = mysqli_fetch_assoc($data_u)) {
										$data_usuarios[] = $row;
									}

									// (permiso RRHH ya se calculó arriba, antes de "Mi Equipo")

									// ─── SOLICITUDES PENDIENTES DE RRHH (para el tab nuevo) ──────────
									$data_pendientes_rrhh = [];
									if ($tienePermisoRRHH) {
										$query_pend_rrhh = "SELECT sv.id, sv.clave, u.nombre, sv.fecha_inicio, sv.fecha_final,
																sv.dias_tomados, sv.periodo, sv.estatus_jefe, sv.estatus_rrhh,
																sv.comentario_jefe, sv.comentario_rrhh, sv.jefe_directo
															 FROM solicitudes_vacaciones sv
															 LEFT JOIN usuarios u ON sv.clave = u.clave
															 WHERE sv.estatus_rrhh = 0 AND sv.masiva = 0
															 ORDER BY sv.fecha_captura ASC";
										$res_pend_rrhh = $conecta->consulta($query_pend_rrhh);
										while ($row = mysqli_fetch_assoc($res_pend_rrhh)) {
											$data_pendientes_rrhh[] = $row;
										}
									}
								?>


								<div class="container mt-4">
									<ul class="nav nav-tabs mb-3">
										<li class="nav-item">
											<a class="nav-link active" data-toggle="tab" href="#resumen">
												📊 Mis Vacaciones
											</a>
										</li>
										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#solicitud">
												📝 Solicitar Vacaciones
											</a>
										</li>

										<li class="nav-item">
											<a class="nav-link" data-toggle="tab" href="#historial">
												📝 Historial de Vacaciones
											</a>
										</li>

										<?php if($tieneSubordinados): ?>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#subordinados">
													👥 Mi Equipo
												</a>
											</li>
										<?php endif; ?>

										<?php if($tienePermisoRRHH): ?>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#usuarios">
													📝 Recursos Humanos
												</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#pendientesRRHH">
													✅ Pendientes RRHH
													<?php if (count($data_pendientes_rrhh) > 0): ?>
														<span class="badge badge-danger"><?= count($data_pendientes_rrhh) ?></span>
													<?php endif; ?>
												</a>
											</li>
											<li class="nav-item">
												<a class="nav-link" data-toggle="tab" href="#reporte">
													📑 Reporte
												</a>
											</li>
										<?php endif; ?>

									</ul>

									<div class="tab-content" style="max-height: 400px; overflow-y: auto;">

										<div id="resumen" class="tab-pane fade show active">

											<div class="card-vacaciones text-center">

												<h5 class="font-weight-bold mb-1" style="font-size: 18px;">
													👤 <?= $data_usr['nombre']; ?>
												</h5>

												<p class="text-muted mb-2" style="font-size: 13px;">
													Clave: <strong><?= $data_usr['clave']; ?></strong><br>
													Ingreso: <strong><?= date('d/m/Y', strtotime($data_usr['fec_ingreso'])); ?></strong>
												</p>

												<div class="row mt-2">

													<div class="col-md-6 mb-3">
														<div class="badge-box bg-soft-green">
															<h5>🌴 Disponibles</h5>
															<h1><?= $vacaciones['disponibles']; ?></h1>
															<small>Disponibles reales</small>
															<br>
															<?php if ($vacaciones['disponibles'] > 0): ?>
																<button class="btn btn-primary btn-block" data-toggle="modal" data-target="#disponibles">
																	Ver Detalles
																</button>
															<?php endif; ?>
														</div>
													</div>

													<div class="col-md-6 mb-3">
														<div class="badge-box bg-soft-green">
															<h5>📤 Solicitadas</h5>
															<h1><?= $vacaciones['usados']; ?></h1>
															<small>Días ya disfrutados</small>
															<br>
															<button class="btn btn-primary btn-block invisible" style="pointer-events:none;" tabindex="-1">
																Ver Detalles
															</button>
														</div>
													</div>

													<div class="col-md-6 mb-3">
														<div class="badge-box bg-soft-yellow">
															<h5>⏳ Próximas a vencer</h5>
															<h1><?= $vacaciones['proximos']; ?></h1>
															<button class="btn btn-primary btn-block" data-toggle="modal" data-target="#detalles">
																Ver Detalles
															</button>
														</div>
													</div>

													<div class="col-md-6 mb-3">
														<div class="badge-box bg-soft-danger">
															<h5>⚠️ Vencidos</h5>
															<h1><?= $vacaciones['caducados']; ?></h1>
															<button class="btn btn-primary btn-block" data-toggle="modal" data-target="#caducadas">
																Ver Detalles
															</button>
														</div>
													</div>

												</div>

											</div>

										</div>

										<div id="solicitud" class="tab-pane fade">

											<div class="card-vacaciones">

												<h4>📝 Solicitar Vacaciones</h4>

												<p class="text-muted" style="font-size:13px;">
													Tu solicitud requiere la aprobación de tu jefe directo
													<strong>y</strong> de Recursos Humanos para quedar confirmada.
												</p>

												<input type="hidden" id="dias_disponibles" value="<?= $vacaciones['disponibles']; ?>">

												<div class="row">

													<div class="col-md-6">
														<label>Fecha inicio</label>
														<input type="date" id="inicio" class="form-control" onchange="calcularDias()">
													</div>

													<div class="col-md-6">
														<label>Fecha fin</label>
														<input type="date" id="fin" class="form-control" onchange="calcularDias()">
													</div>

												</div>

												<div class="mt-3">
													<h5>
														📅 Días solicitados:
														<span class="text-primary font-weight-bold" id="dias_calculados">0</span>
													</h5>
												</div>

												<div id="alerta" class="alert alert-danger mt-2" style="display:none;">
													❌ Excedes tus días disponibles
												</div>

												<button class="btn btn-primary mt-3 btn-block" onclick="set_vacaciones('<?= $data_usr['clave']; ?>', '<?= $periodo_anio; ?>', '<?= $jefe_directo ?>')">
													Enviar Solicitud
												</button>

											</div>

										</div>


										<div id="historial" class="tab-pane fade">

											<div class="card-vacaciones">

												<h4>📝 Historial de Vacaciones</h4>

												<table class="table table-bordered mt-3">
													<thead>
														<tr>
															<th>Fecha de solicitud</th>
															<th>Fechas solicitada</th>
															<th>Días solicitados</th>
															<th>Periodo</th>
															<th>Estado</th>
															<th>Comentario jefe</th>
															<th>Comentario RRHH</th>
														</tr>

													</thead>
													<tbody id="historial-body">

														<?php if(count($data_table) > 0): ?>

															<?php foreach($data_table as $td): ?>
																<tr>
																	<td><?= $td['fecha_captura']; ?></td>
																	<td><?= $td['fecha_inicio']; ?> al <?= $td['fecha_final']; ?></td>
																	<td><?= $td['dias_tomados']; ?></td>
																	<td><?= $td['periodo']; ?></td>
																	<td><?= getEstatus($td['estatus_jefe'], $td['estatus_rrhh']); ?></td>
																	<td><?= htmlspecialchars($td['comentario_jefe'] ?? '') ?></td>
																	<td><?= htmlspecialchars($td['comentario_rrhh'] ?? '') ?></td>
																</tr>
															<?php endforeach; ?>

														<?php else: ?>

															<tr>
																<td colspan="7" class="text-center text-muted">
																	No hay solicitudes anteriores
																</td>
															</tr>

														<?php endif; ?>

													</tbody>
												</table>

											</div>

										</div>


										<?php if($tieneSubordinados): ?>
											<div id="subordinados" class="tab-pane fade">

												<div class="card-vacaciones">

													<h4 class="mb-1">👥 Mi Equipo</h4>
													<p class="text-muted mb-2" style="font-size:13px;">
														Da clic en un colaborador para ver sus días tomados y sus solicitudes
														(aprobadas, rechazadas o pendientes).
													</p>

													<div class="mb-3 mt-3">
														<input
															type="text"
															id="buscador-subordinados"
															class="form-control"
															placeholder="🔍 Buscar por nombre o clave..."
															oninput="filtrarTablaSubordinados(this.value)"
														>
													</div>

													<table class="table table-bordered mt-3 table-hover">
														<thead>
															<tr>
																<th>Clave</th>
																<th>Nombre</th>
																<th>Fecha de ingreso</th>
																<th>Días restantes</th>
															</tr>
														</thead>
														<tbody id="tabla-subordinados">
															<?php if (count($data_subordinados) > 0): ?>
																<?php foreach ($data_subordinados as $s): ?>
																	<?php
																		$vac_s      = calcularVacacionesConUsos($s['fec_ingreso'], $conecta, $s['clave']);
																		$restantes_s = $vac_s['disponibles'] + $vac_s['proximos'];

																		$color_s = 'badge-success';
																		if ($restantes_s <= 5)      $color_s = 'badge-danger';
																		elseif ($restantes_s <= 10) $color_s = 'badge-warning';
																	?>
																	<tr style="cursor:pointer;" onclick="abrirModalSubordinado('<?= $s['clave'] ?>', '<?= htmlspecialchars($s['nombre'], ENT_QUOTES) ?>', '<?= $data_usr['clave'] ?>')">
																		<td><?= $s['clave'] ?></td>
																		<td><?= $s['nombre'] ?></td>
																		<td><?= formatoFecha($s['fec_ingreso']) ?></td>
																		<td>
																			<span class="badge <?= $color_s ?>">
																				<?= $restantes_s ?> días
																			</span>
																		</td>
																	</tr>
																<?php endforeach; ?>
															<?php else: ?>
																<tr>
																	<td colspan="4" class="text-center text-muted">
																		No tienes colaboradores a tu cargo
																	</td>
																</tr>
															<?php endif; ?>
														</tbody>
													</table>

												</div>

											</div>
										<?php endif; ?>


										<?php if($tienePermisoRRHH): ?>
											<div id="usuarios" class="tab-pane fade">

												<div class="card-vacaciones">

													<div class="d-flex justify-content-between align-items-center flex-wrap mb-2">
														<h4 class="mb-0">📝 Historial de Usuarios</h4>
														<div class="d-flex flex-wrap" style="gap:8px;">
															<button class="btn-masivas" onclick="abrirModalMasivas()">
																🏢 Vacaciones masivas
															</button>
															<button class="btn-masivas" onclick="abrirModalDirecto()">
																➕ Registrar vacaciones directo
															</button>
															<button class="btn-masivas" onclick="abrirModalBuscarEditar()">
																✏️ Editar vacaciones de un usuario
															</button>
														</div>
													</div>

													 <div class="mb-3 mt-3">
														<input
															type="text"
															id="buscador-rh"
															class="form-control"
															placeholder="🔍 Buscar por nombre o clave..."
															oninput="filtrarTablaRH(this.value)"
														>
													</div>

													<table class="table table-bordered mt-3 table-responsive table-hover" style=" overflow-y: auto; height: 21rem;">
														<thead>
															<tr>
																<th>Clave</th>
																<th>Nombre</th>
																<th>Fecha de ingreso</th>
																<th>Días restantes</th>
																<th>Estado</th>
															</tr>

														</thead>
														<tbody id="historial-usuarios">
																<?php foreach ($data_usuarios as $u): ?>
																	<?php
																		$vac_u     = calcularVacacionesConUsos($u['fec_ingreso'], $conecta, $u['clave']);
																		$restantes = $vac_u['disponibles'] + $vac_u['proximos'];

																		$color = 'badge-success';
																		if ($restantes <= 5)       $color = 'badge-danger';
																		elseif ($restantes <= 10)  $color = 'badge-warning';
																	?>
																	<tr onclick="detailUsr('<?= $u['clave'] ?>', '<?= $u['nombre'] ?>', '<?= $u['fec_ingreso'] ?>')">
																		<td><?= $u['clave'] ?></td>
																		<td><?= $u['nombre'] ?></td>
																		<td><?= formatoFecha($u['fec_ingreso']) ?></td>
																		<td>
																			<span class="badge <?= $color ?>">
																				<?= $restantes ?> días
																			</span>
																		</td>
																		<td><?= getEstatusDot($u['estatus']) ?></td>
																	</tr>
																<?php endforeach; ?>

														</tbody>
													</table>

												</div>

											</div>

											<!-- ── Tab nuevo: Pendientes de RRHH ── -->
											<div id="pendientesRRHH" class="tab-pane fade">

												<div class="card-vacaciones">

													<div class="d-flex justify-content-between align-items-start flex-wrap mb-2">
														<div>
															<h4 class="mb-1">✅ Solicitudes pendientes de Recursos Humanos</h4>
															<p class="text-muted mb-0" style="font-size:13px;">
																Estas solicitudes ya tienen (o no) la respuesta del jefe directo.
																Aquí registras la resolución de RRHH con tu propio comentario.
															</p>
													</div>
													<?php if (count($data_pendientes_rrhh) > 0): ?>
														<div class="d-flex flex-wrap" style="gap:8px;">
															<button class="btn-aprobar-todas" onclick="aprobarTodasPendientes('<?= $data_usr['clave'] ?>')">
																✔ Aprobar todas las pendientes
															</button>
															<button class="btn-aprobar-todas-saltar" onclick="aprobarTodasSaltandoJefe('<?= $data_usr['clave'] ?>')">
																⏭ Aprobar todas (saltar jefe directo)
															</button>
														</div>
													<?php endif; ?>
												</div>

												<?php if (count($data_pendientes_rrhh) > 0): ?>
													<div class="mb-3 mt-2">
														<input
															type="text"
															id="buscador-pendientes-rrhh"
															class="form-control"
															placeholder="🔍 Buscar por nombre o clave..."
															oninput="filtrarPendientesRRHH(this.value)"
														>
													</div>
												<?php endif; ?>

												<div id="lista-pendientes-rrhh">

												<?php if (count($data_pendientes_rrhh) === 0): ?>
													<div class="text-center text-muted py-4">
														🎉 No hay solicitudes pendientes de RRHH
													</div>
												<?php else: ?>
													<?php foreach ($data_pendientes_rrhh as $p): ?>
														<div class="pendiente-card" data-nombre="<?= strtolower(htmlspecialchars($p['nombre'])) ?>" data-clave="<?= strtolower(htmlspecialchars($p['clave'])) ?>">
																<div class="pendiente-header">
																	<div>
																		<strong><?= htmlspecialchars($p['nombre']) ?></strong>
																		<small class="text-muted">• <?= htmlspecialchars($p['clave']) ?></small>
																	</div>
																	<?= getEstatus($p['estatus_jefe'], $p['estatus_rrhh']) ?>
																</div>

																<div class="vac-row">
																	<span>📅 Fechas</span>
																	<strong><?= formatoFecha($p['fecha_inicio']) ?> al <?= formatoFecha($p['fecha_final']) ?></strong>
																</div>
																<div class="vac-row">
																	<span>⏱ Días</span>
																	<strong><?= $p['dias_tomados'] ?> días</strong>
																</div>
																<div class="vac-row">
																	<span>📊 Periodo</span>
																	<strong><?= $p['periodo'] ?></strong>
																</div>

																<div class="pendiente-aprob-wrap">
																	<div class="pendiente-aprob-item <?= $p['estatus_jefe'] == 1 ? 'ok' : ($p['estatus_jefe'] == 2 ? 'rechazado' : '') ?>">
																		<div class="titulo">Jefe directo</div>
																		<?= getEstatus($p['estatus_jefe']) ?>
																		<?php if (!empty($p['comentario_jefe'])): ?>
																			<div class="pendiente-comentario">"<?= htmlspecialchars($p['comentario_jefe']) ?>"</div>
																		<?php endif; ?>
																	</div>
																	<div class="pendiente-aprob-item">
																		<div class="titulo">Tu resolución (RRHH)</div>
																		<textarea class="form-control mt-1" rows="2"
																				  id="comentarioRrhh_<?= $p['id'] ?>"
																				  placeholder="Escribe tu comentario..."></textarea>
																	</div>
																</div>

																<div class="d-flex justify-content-end gap-2 mt-2">
																	<button class="btn btn-success btn-sm mr-2"
																			onclick="resolverRRHH(<?= $p['id'] ?>, '<?= $p['clave'] ?>', 1)">
																		✔ Aprobar
																	</button>
																	<button class="btn btn-danger btn-sm"
																			onclick="resolverRRHH(<?= $p['id'] ?>, '<?= $p['clave'] ?>', 2)">
																		✖ Rechazar
																	</button>
																</div>
															</div>
														<?php endforeach; ?>
													<?php endif; ?>

												</div>
												<!-- /lista-pendientes-rrhh -->

												</div>

											</div>

											<!-- ── Tab nuevo: Reporte de solicitudes de vacaciones ── -->
											<div id="reporte" class="tab-pane fade">

												<div class="card-vacaciones">

													<h4 class="mb-3">📑 Reporte de solicitudes de vacaciones</h4>

													<div class="row">
														<div class="col-md-3 mb-2">
															<label>Fecha inicio (desde)</label>
															<input type="date" id="reporte_fecha_desde" class="form-control">
														</div>
														<div class="col-md-3 mb-2">
															<label>Fecha final (hasta)</label>
															<input type="date" id="reporte_fecha_hasta" class="form-control">
														</div>
														<div class="col-md-4 mb-2">
															<label>Nombre o clave</label>
															<input type="text" id="reporte_nombre" class="form-control" placeholder="Buscar por nombre o clave..." onkeydown="if(event.key==='Enter'){event.preventDefault(); cargarReporteVacaciones();}">
														</div>
														<div class="col-md-2 mb-2 d-flex align-items-end">
															<button class="btn-masivas btn-block" onclick="cargarReporteVacaciones()">🔍 Filtrar</button>
														</div>
													</div>

													<div class="d-flex justify-content-between align-items-center mt-1 mb-2 flex-wrap">
														<small class="text-muted" id="reporte_resumen"></small>
														<button class="btn btn-outline-secondary btn-sm" onclick="limpiarFiltrosReporte()">Limpiar filtros</button>
													</div>

													<div class="table-responsive" style="max-height: 420px; overflow-y:auto;">
														<table class="table table-bordered table-hover table-sm">
															<thead>
																<tr>
																	<th>Clave</th>
																	<th>Nombre</th>
																	<th>Fecha inicio</th>
																	<th>Fecha final</th>
																	<th>Periodo</th>
																	<th>Días tomados</th>
																	<th>Fecha captura</th>
																	<th>Estatus</th>
																</tr>
															</thead>
															<tbody id="reporte-body">
																<tr>
																	<td colspan="8" class="text-center text-muted">
																		Usa los filtros y presiona "Filtrar" para ver resultados (o filtra en blanco para traer las más recientes).
																	</td>
																</tr>
															</tbody>
														</table>
													</div>

												</div>

											</div>
										<?php endif; ?>

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


		<!-- MODAL: Disponibles -->
		<div class="modal fade" id="disponibles" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-primary text-white">
						<h5 class="modal-title">Detalle de Días Disponibles</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<?php $hay = false; ?>
						<?php foreach ($vacaciones['detalle'] as $d): ?>
							<?php if (($d['tipo'] === 'vigente' || $d['tipo'] === 'proximo') && $d['dias'] > 0): $hay = true; ?>
								<div class="vac-card disponible">
									<div class="vac-header">
										<span>📅 <?= $d['periodo_inicio']; ?></span>
										<?php if ($d['tipo'] === 'proximo'): ?>
											<span class="vac-badge proximo">Por vencer</span>
										<?php else: ?>
											<span class="vac-badge disponible">Disponible</span>
										<?php endif; ?>
									</div>
									<div class="vac-body">
										<div><strong>Vence:</strong> <?= $d['vence']; ?></div>
										<div><strong>Días disponibles:</strong> <?= $d['dias']; ?></div>
										<div><strong>Días utilizados en este periodo:</strong> <?= $d['usados']; ?></div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php if (!$hay): ?>
							<div class="text-center text-muted">No hay días disponibles</div>
						<?php endif; ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Próximos a vencer -->
		<div class="modal fade" id="detalles" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-warning">
						<h5 class="modal-title">Días Próximos a Vencer</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<?php $hay = false; ?>
						<?php foreach ($vacaciones['detalle'] as $d): ?>
							<?php if ($d['tipo'] === 'proximo' && $d['dias'] > 0): $hay = true; ?>
								<div class="vac-card proximo">
									<div class="vac-header">
										<span>📅 <?= $d['periodo_inicio']; ?></span>
										<span class="vac-badge proximo">Por vencer</span>
									</div>
									<div class="vac-body">
										<div><strong>Vence:</strong> <?= $d['vence']; ?></div>
										<div><strong>Días:</strong> <?= $d['dias']; ?></div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php if (!$hay): ?>
							<div class="text-center text-muted">No hay vacaciones próximas a vencer</div>
						<?php endif; ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Caducadas -->
		<div class="modal fade" id="caducadas" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header bg-danger">
						<h5 class="modal-title">Días Vencidos</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<?php $hay = false; ?>
						<?php foreach ($vacaciones['detalle'] as $d): ?>
							<?php if ($d['tipo'] === 'caducado' && $d['dias'] > 0): $hay = true; ?>
								<div class="vac-card caducado">
									<div class="vac-header">
										<span>📅 <?= $d['periodo_inicio']; ?></span>
										<span class="vac-badge caducado">Caducado</span>
									</div>
									<div class="vac-body">
										<div><strong>Venció:</strong> <?= $d['vence']; ?></div>
										<div><strong>Días:</strong> <?= $d['dias']; ?></div>
									</div>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
						<?php if (!$hay): ?>
							<div class="text-center text-muted">No hay vacaciones caducadas 🎉</div>
						<?php endif; ?>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>


		<div class="modal fade bd-example-modal-lg" id="modalInfoUsr" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header bg-primary text-white">
						<h5 class="modal-title" id="title">...</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>

					<div class="modal-body">

					<div class="row">

							<div class="col-6 mb-3">
								<div class="vac-card vac-green" id="cardDisponibles">
									<h6>Días disponibles</h6>
									<h2 id="mdlDisponibles"></h2>
								</div>
							</div>

							<div class="col-6 mb-3">
								<div class="vac-card vac-green" id="cardUsados">
									<h6>Días utilizados</h6>
									<h2 id="mdlUsados"></h2>
								</div>
							</div>

							<div class="col-6 mb-3">
								<div class="vac-card vac-yellow" id="cardProximos">
									<h6>Próximos a vencer</h6>
									<h2 id="mdlVencer"></h2>
								</div>
							</div>

							<div class="col-6 mb-3">
								<div class="vac-card vac-red" id="cardCaducados">
									<h6>Vencidos</h6>
									<h2 id="mdlCaducads"></h2>
								</div>
							</div>

							<!-- 🔥 TABLA DINÁMICA -->
							<div class="col-12">
								<table class="table table-sm table-bordered mt-3">
									<thead>
										<tr>
											<th>Periodo</th>
											<th>Vence</th>
											<th>Días</th>
											<th></th>
										</tr>
									</thead>
									<tbody id="tablaDetalle"></tbody>
								</table>
							</div>

						</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Detalle de un subordinado (tab "Mi Equipo") -->
		<div class="modal fade bd-example-modal-lg" id="modalSubordinado" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-lg">
				<div class="modal-content">
					<div class="modal-header bg-primary text-white">
						<h5 class="modal-title" id="titleSubordinado">...</h5>
						<button type="button" class="close" data-dismiss="modal">&times;</button>
					</div>

					<div class="modal-body">

						<div class="row">

							<div class="col-6 mb-3">
								<div class="vac-card vac-green">
									<h6>Días disponibles</h6>
									<h2 id="subDisponibles">0</h2>
								</div>
							</div>

							<div class="col-6 mb-3">
								<div class="vac-card vac-green">
									<h6>Días utilizados</h6>
									<h2 id="subUsados">0</h2>
								</div>
							</div>

							<div class="col-6 mb-3">
								<div class="vac-card vac-yellow">
									<h6>Próximos a vencer</h6>
									<h2 id="subVencer">0</h2>
								</div>
							</div>

							<div class="col-6 mb-3">
								<div class="vac-card vac-red">
									<h6>Vencidos</h6>
									<h2 id="subCaducads">0</h2>
								</div>
							</div>

							<div class="col-12">
								<h6 class="mt-2 mb-2">📋 Días tomados y solicitudes postuladas</h6>
								<table class="table table-sm table-bordered">
									<thead>
										<tr>
											<th>Fechas</th>
											<th>Días</th>
											<th>Periodo</th>
											<th>Solicitada el</th>
											<th>Estado</th>
										</tr>
									</thead>
									<tbody id="tablaSolicitudesSubordinado">
										<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>
									</tbody>
								</table>
							</div>

						</div>

					</div>

					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Vacaciones masivas (RRHH) -->
		<div class="modal fade" id="modalMasivas" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header" style="background:linear-gradient(135deg,#2384c5,#0072bc); color:#fff;">
						<h5 class="modal-title">🏢 Vacaciones masivas</h5>
						<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="alert alert-warning" style="font-size:13px;">
							⚠️ Esto aplicará el rango de fechas a <strong>TODOS los empleados activos</strong>,
							ya con aprobación completa (jefe + RRHH). No se puede deshacer desde aquí.
						</div>

						<div class="row">
							<div class="col-md-6">
								<label>Fecha inicio</label>
								<input type="date" id="masiva_inicio" class="form-control" onchange="calcularDiasMasivas()">
							</div>
							<div class="col-md-6">
								<label>Fecha fin</label>
								<input type="date" id="masiva_fin" class="form-control" onchange="calcularDiasMasivas()">
							</div>
						</div>

						<div class="mt-3">
							<h5>📅 Días hábiles: <span class="text-primary font-weight-bold" id="masiva_dias_calculados">0</span></h5>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn-masivas" onclick="confirmarVacacionesMasivas('<?= $data_usr['clave'] ?>')">
							Aplicar a todos
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Registro directo de vacaciones (RRHH) -->
		<div class="modal fade" id="modalDirecto" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header" style="background:linear-gradient(135deg,#2384c5,#0072bc); color:#fff;">
						<h5 class="modal-title">➕ Registrar vacaciones directo</h5>
						<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="alert alert-warning" style="font-size:13px;">
							⚠️ Esto registra las vacaciones YA APROBADAS (sin pasar por el flujo de aprobación
							de jefe/RRHH). Úsalo cuando el colaborador ya tomó los días y solo falta dejarlo
							registrado en el sistema.
						</div>

						<div class="form-group" style="position:relative;">
							<label>Empleado</label>
							<input type="text" id="directo_empleado_input" class="form-control" placeholder="Escribe nombre o clave..." autocomplete="off" oninput="filtrarAutocompleteDirecto(this.value)" onfocus="filtrarAutocompleteDirecto(this.value)">
							<input type="hidden" id="directo_empleado" value="">
							<div id="directo_empleado_dropdown" class="list-group" style="position:absolute; z-index:1060; width:100%; max-height:220px; overflow-y:auto; display:none; box-shadow:0 6px 18px rgba(0,0,0,0.12);"></div>
						</div>

						<div class="row">
							<div class="col-md-6">
								<label>Fecha inicio</label>
								<input type="date" id="directo_inicio" class="form-control" onchange="calcularDiasDirecto()">
							</div>
							<div class="col-md-6">
								<label>Fecha fin</label>
								<input type="date" id="directo_fin" class="form-control" onchange="calcularDiasDirecto()">
							</div>
						</div>

						<div class="form-group mt-3">
							<label>Días tomados</label>
							<input type="number" id="directo_dias" class="form-control" min="1" value="0">
							<small class="text-muted">Se calcula automáticamente a partir de las fechas (sin contar sábados, domingos ni días festivos), pero puedes ajustarlo a mano si el número real de días fue distinto.</small>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn-masivas" onclick="confirmarVacacionesDirecto('<?= $data_usr['clave'] ?>')">
							Registrar
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Editar registro de vacaciones (RRHH) -->
		<div class="modal fade" id="modalEditarVacacion" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header" style="background:linear-gradient(135deg,#2384c5,#0072bc); color:#fff;">
						<h5 class="modal-title">✏️ Editar registro de vacaciones</h5>
						<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<input type="hidden" id="editar_id" value="">

						<div class="row">
							<div class="col-md-6">
								<label>Fecha inicio</label>
								<input type="date" id="editar_inicio" class="form-control" onchange="calcularDiasEditar()">
							</div>
							<div class="col-md-6">
								<label>Fecha fin</label>
								<input type="date" id="editar_fin" class="form-control" onchange="calcularDiasEditar()">
							</div>
						</div>

						<div class="form-group mt-3">
							<label>Días tomados</label>
							<input type="number" id="editar_dias" class="form-control" min="1" value="0">
							<small class="text-muted">Ajusta el número de días si no coincide con el rango de fechas (por ejemplo, si el registro dice 4 días pero en realidad fueron 5).</small>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-danger mr-auto" onclick="confirmarEliminarVacacion('<?= $data_usr['clave'] ?>')">
							🗑️ Eliminar
						</button>
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
						<button type="button" class="btn-masivas" onclick="confirmarEditarVacacion('<?= $data_usr['clave'] ?>')">
							Guardar cambios
						</button>
					</div>
				</div>
			</div>
		</div>

		<!-- MODAL: Buscar usuario para editar sus vacaciones (RRHH) -->
		<div class="modal fade" id="modalBuscarEditar" tabindex="-1" role="dialog">
			<div class="modal-dialog modal-lg modal-dialog-centered" role="document">
				<div class="modal-content">
					<div class="modal-header" style="background:linear-gradient(135deg,#2384c5,#0072bc); color:#fff;">
						<h5 class="modal-title">✏️ Editar vacaciones de un usuario</h5>
						<button type="button" class="close text-white" data-dismiss="modal">&times;</button>
					</div>
					<div class="modal-body">
						<div class="form-group" style="position:relative;">
							<label>Buscar empleado</label>
							<input type="text" id="editar_busqueda_input" class="form-control" placeholder="Escribe nombre o clave..." autocomplete="off" oninput="filtrarAutocompleteEditar(this.value)" onfocus="filtrarAutocompleteEditar(this.value)">
							<input type="hidden" id="editar_busqueda_clave" value="">
							<div id="editar_busqueda_dropdown" class="list-group" style="position:absolute; z-index:1060; width:100%; max-height:220px; overflow-y:auto; display:none; box-shadow:0 6px 18px rgba(0,0,0,0.12);"></div>
						</div>

						<div id="editar_busqueda_registros" class="mt-3">
							<div class="text-center text-muted py-3">Busca y selecciona un empleado para ver sus vacaciones registradas.</div>
						</div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>

	<?php } ?>


	<?php if($sesion_login): ?>
		<div id="login-overlay" style="
			position: fixed; top:0; left:0; width:100%; height:100%;
			background:rgb(79, 122, 185);
			z-index: 9999;
			display: flex; align-items: center; justify-content: center;
		">
			<div style="background:#fff; border-radius:16px; padding:2rem 2.5rem; width:100%; max-width:380px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">

				<div style="text-align:center; margin-bottom:1.5rem;">
				<p style="font-size:20px; font-weight:600; margin:0;">Iniciar sesión</p>
				<p style="font-size:13px; color:#64748b; margin:4px 0 0;">| CAROBRA</p>
				</div>

				<div style="margin-bottom:1rem;">
				<label style="font-size:13px; color:#64748b; display:block; margin-bottom:6px;">Usuario</label>
				<input type="text" id="inp-usr" class="form-control" placeholder="Ingresa tu usuario">
				</div>

				<div style="margin-bottom:1.25rem;">
				<label style="font-size:13px; color:#64748b; display:block; margin-bottom:6px;">Contraseña</label>
				<input type="password" id="inp-pass" class="form-control" placeholder="••••••••">
				</div>

				<div id="login-error" class="alert alert-danger" style="display:none; font-size:13px;">
				Usuario o contraseña incorrectos
				</div>

				<button id="btn-login" class="btn btn-primary btn-block" onclick="doLogin()">
				Entrar
				</button>
			</div>
		</div>
		<script>
			document.getElementById('inp-pass').addEventListener('keydown', function(e){
			if(e.key === 'Enter') doLogin();
			});

			async function doLogin(){
			const usr = document.getElementById('inp-usr').value.trim();
			const pass = document.getElementById('inp-pass').value;
			const btn  = document.getElementById('btn-login');
			const err  = document.getElementById('login-error');

			if(!usr || !pass){ err.style.display='block'; err.textContent='Completa los campos'; return; }

			btn.textContent = 'Verificando...';
			btn.disabled = true;
			err.style.display = 'none';

			try {
				let res = await fetch('vacaciones_api.php', {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify({ action: 'login', usr: usr, pass: pass })
				});
				let data = await res.json();

				if(data.status === 'ok'){
				location.reload();
				} else {
				err.textContent = data.msg || 'Usuario o contraseña incorrectos';
				err.style.display = 'block';
				}
			} catch(e){
				err.textContent = 'Error de conexión';
				err.style.display = 'block';
			}

			btn.textContent = 'Entrar';
			btn.disabled = false;
			}
		</script>
	<?php endif; ?>

    </body>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

	<script type="text/javascript">
		jQuery(document).ready(function()
	   	{
   		   	$(window).resize(function ()
   	    	{
   	    		centra_contenedor();
	   	    });
   		   	$(window).resize();

   		 	$('#men_pie').css("top",h);
   		 	$('#men_pie').css("background","red");

  			$('#tmenu td').click(function(evento)
  		  	{
  	  		  	var modulo=$(this).text().replace(/ /gi,"_");
  	  			$("#opcion").load("menu.php?modulo=" + modulo);
  		  	});

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="https://www.sis-ca.com.mx:7863/";
  		  	});

  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

			$(".datepicker").datepicker(
			{
				format: "dd/mm/yyyy"
			});

  		  	$(".filtro").click(function()
  		  	{
  		  		$("#op").val(1);
  		  		$("#valor1").val('');
  		  		$("#valor2").val('');
  		  		$("#valor3").val('');
  		  		$("#valor4").val('');
  		  		$("#valor5").val('');
  		  		$("#filtro").val($(this).text());
  		  		$("#frm-filtro").submit();
  		  	});

			$("#btn-folio, #btn-nss, #btn-cliente, #btn-sc, #btn-sb, #btn-asesor, #btn-gerencia").hover(function()
			{
				if($(this).attr("src")=="./img/aceptar2.png")
					$(this).attr("src","./img/aceptar1.png");
				else
					$(this).attr("src","./img/aceptar2.png");
			});


		});

		/* ════════════════════════════════════════
		   FIX: modales anidados (Bootstrap 4.0 no los soporta
		   de forma nativa). Sin esto, un modal abierto desde
		   otro modal (ej. "Editar" dentro de "Editar vacaciones
		   de un usuario") aparece detrás en vez de encima.
		   Se sube el z-index de cada modal/backdrop según cuántos
		   ya estén abiertos, y se restaura la clase modal-open
		   del body al cerrar uno mientras otro sigue abierto.
		════════════════════════════════════════ */
		$(document).on('show.bs.modal', '.modal', function () {
			let abiertos = $('.modal.show').length;
			let zIndex = 1050 + (abiertos + 1) * 20;
			$(this).css('z-index', zIndex);
		});

		$(document).on('shown.bs.modal', '.modal', function () {
			let modalZ = parseInt($(this).css('z-index'), 10) || 1050;
			$('.modal-backdrop:not(.stacked-fixed)').last()
				.css('z-index', modalZ - 1)
				.addClass('stacked-fixed');
		});

		$(document).on('hidden.bs.modal', '.modal', function () {
			if ($('.modal.show').length > 0) {
				$('body').addClass('modal-open');
			}
		});

		function esFeriado(fecha) {

			let mes      = fecha.getMonth() + 1;
			let dia      = fecha.getDate();
			let diaSemana = fecha.getDay(); // 0=Dom

			// Fijos
			if (mes === 1  && dia === 1)  return true;
			if (mes === 5  && dia === 1)  return true;
			if (mes === 9  && dia === 16) return true;
			if (mes === 12 && dia === 25) return true;

			// Variables (lunes de ley)
			if (mes === 2  && diaSemana === 1 && dia <= 7)               return true;
			if (mes === 3  && diaSemana === 1 && dia >= 15 && dia <= 21) return true;
			if (mes === 11 && diaSemana === 1 && dia >= 15 && dia <= 21) return true;

			return false;
		}

		function contarDiasHabiles(inicio, fin) {

			let start = new Date(inicio + 'T00:00:00');
			let end   = new Date(fin   + 'T00:00:00');
			let dias  = 0;

			while (start <= end) {
				let diaSemana = start.getDay(); // 0=Dom, 6=Sab
				if (diaSemana !== 0 && diaSemana !== 6 && !esFeriado(start)) {
					dias++;
				}
				start.setDate(start.getDate() + 1);
			}

			return dias;
		}

		function validarFechas(inicio, fin) {

			let f1 = new Date(inicio);
			let f2 = new Date(fin);

			if (f2 < f1) {
				Swal.fire({
					title: "Error",
					text: "Fecha final menor que la inicial",
					icon: "question"
				});
				return false;
			}

			let diff = (f2 - f1) / (1000 * 60 * 60 * 24);

			if (diff > 365) {
				Swal.fire({
					title: "Error",
					text: "Rango demasiado grande (revisa el año)",
					icon: "question"
				});
				return false;
			}

			return true;
		}

		let diasCalculados = 0

		function calcularDias() {

			let inicio = document.getElementById('inicio').value;
			let fin = document.getElementById('fin').value;

			if (inicio && fin) {

				if (!validarFechas(inicio, fin)) return;

				let dias = contarDiasHabiles(inicio, fin);
				document.getElementById('dias_calculados').innerText = dias;
				diasCalculados = dias;

				let disponibles = parseInt(document.getElementById('dias_disponibles').value);

				if (dias > disponibles) {
					document.getElementById('alerta').style.display = 'block';
				} else {
					document.getElementById('alerta').style.display = 'none';
				}
			}
		}


		let dataGlobal = null;
		let dataGlobalHist = [];
		let claveUsrActual = '';

		async function detailUsr(usr, nombre, fecha){

			$('#modalInfoUsr').modal('show');

			claveUsrActual = usr;

			let title = document.querySelector("#title");
			let mdlDisponibles = document.querySelector("#mdlDisponibles");
			let mdlVencer = document.querySelector("#mdlVencer");
			let mdlCaducads = document.querySelector("#mdlCaducads");
			let mdlUsados = document.querySelector("#mdlUsados");

			title.innerText = nombre;

			// LIMPIAR SIEMPRE
			mdlDisponibles.innerText = '0';
			mdlVencer.innerText = '0';
			mdlCaducads.innerText = '0';
			mdlUsados.innerText = '0';

			try {

				let res = await fetch('vacaciones_api.php', {
					method: 'POST',
					headers: {'Content-Type': 'application/json'},
					body: JSON.stringify({
						action: 'getVacaciones',
						clave: usr
					})
				});

				let data = await res.json();

				if(data.status === 'ok'){

					let vac = data.vac || {};

					mdlDisponibles.innerText = vac.disponibles_real ?? 0;

					// AQUÍ VAN
					mdlVencer.innerText = vac.proximos_real > 0 ? vac.proximos_real : 'Sin próximos';
					mdlCaducads.innerText = vac.caducados > 0 ? vac.caducados : 'Sin Vencidos';
					 dataGlobal = data.vac;
					dataGlobalHist = data.historial || [];

					mdlDisponibles.innerText = data.vac.disponibles_real ?? 0;
					mdlVencer.innerText = data.vac.proximos_real ?? 0;
					mdlCaducads.innerText = data.vac.caducados ?? 0;
					mdlUsados.innerText = data.vac.usados ?? 0;

					mdlUsados.innerText = vac.usados ?? 0;
				} else {
					console.error(data.msg);
				}

			} catch(err){
				console.error(err);
			}
		}

		document.getElementById("cardProximos").onclick = () => renderTabla('proximo');
		document.getElementById("cardCaducados").onclick = () => renderTabla('caducado');
		document.getElementById("cardDisponibles").onclick = () => renderTabla('vigente');
		document.getElementById("cardUsados").onclick = () => renderUsados();

		function renderTabla(tipo){

			let tbody = document.getElementById("tablaDetalle");
			tbody.innerHTML = "";

			if(!dataGlobal) return;

			let datos = dataGlobal.detalle.filter(d => d.tipo === tipo && parseInt(d.dias) > 0);

			if(datos.length === 0){
				tbody.innerHTML = `<tr><td colspan="4" class="text-center">Sin registros</td></tr>`;
				return;
			}

			datos.forEach(d => {
				tbody.innerHTML += `
					<tr>
						<td>${d.periodo_inicio}</td>
						<td>${d.vence}</td>
						<td>${d.dias}</td>
						<td></td>
					</tr>
				`;
			});
		}

		function renderUsados(){

			let tbody = document.getElementById("tablaDetalle");
			tbody.innerHTML = "";

			if(!dataGlobalHist || dataGlobalHist.length === 0){
				tbody.innerHTML = `<tr><td colspan="4">Sin registros</td></tr>`;
				return;
			}

			dataGlobalHist.forEach(d => {
				tbody.innerHTML += `
					<tr>
						<td>${d.fecha_inicio} al ${d.fecha_final}</td>
						<td>${d.periodo}</td>
						<td>${d.dias_tomados}</td>
						<td><button class="btn btn-sm btn-outline-warning" onclick="abrirModalEditar(${d.id}, '${d.fecha_inicio}', '${d.fecha_final}', ${d.dias_tomados})">✏️ Editar</button></td>
					</tr>
				`;
			});
		}

		function set_vacaciones(clave, periodo, jefe_directo){
			let fechaInicio = document.getElementById('inicio').value;
			let fechaFinal = document.getElementById('fin').value;
			let diasTomados = diasCalculados;

			 fetch('vacaciones_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
					action: 'setVacaciones',
                    clave: clave,
					fechaInicio: fechaInicio,
					fechaFinal: fechaFinal,
					diasTomados: diasTomados,
					periodo: periodo,
                    jefe_directo: jefe_directo,
                })
            })
            .then(res => res.json())
            .then(data => {
                Swal.fire({
                    title: data.mgs,
                    icon: "success",
                    draggable: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ok"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload();
                    }
                });
            });

		}

		$('.nav-tabs a').click(function (e) {
			e.preventDefault();
			$(this).tab('show');
		});

		function filtrarTablaRH(texto) {
			const filtro = texto.toLowerCase().trim();
			const filas  = document.querySelectorAll('#historial-usuarios tr');

			filas.forEach(function(fila) {
				const clave  = (fila.cells[0]?.textContent || '').toLowerCase();
				const nombre = (fila.cells[1]?.textContent || '').toLowerCase();
				fila.style.display = (clave.includes(filtro) || nombre.includes(filtro)) ? '' : 'none';
			});
		}

		/* ════════════════════════════════════════
		   MI EQUIPO (tab de líderes con subordinados)
		════════════════════════════════════════ */
		function filtrarTablaSubordinados(texto) {
			const filtro = texto.toLowerCase().trim();
			const filas  = document.querySelectorAll('#tabla-subordinados tr');

			filas.forEach(function(fila) {
				const clave  = (fila.cells[0]?.textContent || '').toLowerCase();
				const nombre = (fila.cells[1]?.textContent || '').toLowerCase();
				fila.style.display = (clave.includes(filtro) || nombre.includes(filtro)) ? '' : 'none';
			});
		}

		function badgeEstatusSubordinado(estatusJefe, estatusRrhh) {
			estatusJefe = parseInt(estatusJefe);
			estatusRrhh = parseInt(estatusRrhh);

			if (estatusJefe === 2 || estatusRrhh === 2) return '<span class="badge badge-danger">Rechazado</span>';
			if (estatusJefe === 1 && estatusRrhh === 1) return '<span class="badge badge-success">Aprobado</span>';
			if (estatusJefe === 1 && estatusRrhh === 0) return '<span class="badge badge-warning">Falta RRHH</span>';
			if (estatusJefe === 0 && estatusRrhh === 1) return '<span class="badge badge-warning">Falta Jefe</span>';
			return '<span class="badge badge-primary">Pendiente</span>';
		}

		async function abrirModalSubordinado(clave, nombre, claveJefe) {

			$('#modalSubordinado').modal('show');

			document.getElementById('titleSubordinado').innerText = nombre;
			document.getElementById('subDisponibles').innerText = '0';
			document.getElementById('subUsados').innerText = '0';
			document.getElementById('subVencer').innerText = '0';
			document.getElementById('subCaducads').innerText = '0';
			document.getElementById('tablaSolicitudesSubordinado').innerHTML =
				'<tr><td colspan="5" class="text-center text-muted">Cargando...</td></tr>';

			try {

				let res = await fetch('vacaciones_api.php', {
					method: 'POST',
					headers: {'Content-Type': 'application/json'},
					body: JSON.stringify({
						action: 'getSolicitudesSubordinado',
						clave: clave,
						clave_jefe: claveJefe
					})
				});

				let data = await res.json();

				if (data.status !== 'ok') {
					document.getElementById('tablaSolicitudesSubordinado').innerHTML =
						`<tr><td colspan="5" class="text-center text-danger">${data.msg || 'No se pudo cargar.'}</td></tr>`;
					return;
				}

				document.getElementById('subDisponibles').innerText = data.vac.disponibles_real ?? 0;
				document.getElementById('subUsados').innerText     = data.vac.usados ?? 0;
				document.getElementById('subVencer').innerText     = data.vac.proximos_real ?? 0;
				document.getElementById('subCaducads').innerText   = data.vac.caducados ?? 0;

				let solicitudes = data.solicitudes || [];
				let tbody = document.getElementById('tablaSolicitudesSubordinado');

				if (solicitudes.length === 0) {
					tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">Sin solicitudes registradas</td></tr>';
					return;
				}

				tbody.innerHTML = solicitudes.map(function(s){
					return '<tr>' +
						'<td>' + (s.fecha_inicio || '') + ' al ' + (s.fecha_final || '') + '</td>' +
						'<td>' + s.dias_tomados + '</td>' +
						'<td>' + (s.periodo || '') + '</td>' +
						'<td>' + (s.fecha_captura || '') + '</td>' +
						'<td>' + badgeEstatusSubordinado(s.estatus_jefe, s.estatus_rrhh) + '</td>' +
						'</tr>';
				}).join('');

			} catch(err) {
				document.getElementById('tablaSolicitudesSubordinado').innerHTML =
					'<tr><td colspan="5" class="text-center text-danger">Error de conexión.</td></tr>';
			}
		}

		/* ════════════════════════════════════════
		   RESOLUCIÓN DE RRHH (tab Pendientes RRHH)
		════════════════════════════════════════ */
		function resolverRRHH(id, claveEmpleado, estatus){

			let comentario = document.getElementById('comentarioRrhh_' + id).value.trim();

			if(comentario === ""){
				Swal.fire('Falta comentario', 'Debes escribir un comentario antes de continuar.', 'warning');
				return;
			}

			fetch('vacaciones_api.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					action: 'authVacacionesRRHH',
					id: id,
					clave: claveEmpleado,
					estatus: estatus,
					comentario: comentario,
					clave_rrhh: '<?= $data_usr["clave"] ?>'
				})
			})
			.then(res => res.json())
			.then(data => {
				if (data.status === 'ok') {
					Swal.fire({
						title: data.mgs,
						icon: 'success',
						confirmButtonColor: '#3085d6',
						confirmButtonText: 'Ok'
					}).then(() => location.reload());
				} else {
					Swal.fire('Error', data.msg || 'No se pudo resolver.', 'error');
				}
			});
		}

		/* ════════════════════════════════════════
		   VACACIONES MASIVAS (RRHH)
		════════════════════════════════════════ */
		let diasMasivosCalculados = 0;

		function abrirModalMasivas(){
			document.getElementById('masiva_inicio').value = '';
			document.getElementById('masiva_fin').value = '';
			document.getElementById('masiva_dias_calculados').innerText = '0';
			diasMasivosCalculados = 0;
			$('#modalMasivas').modal('show');
		}

		function calcularDiasMasivas(){
			let inicio = document.getElementById('masiva_inicio').value;
			let fin = document.getElementById('masiva_fin').value;

			if (inicio && fin) {
				if (!validarFechas(inicio, fin)) return;
				let dias = contarDiasHabiles(inicio, fin);
				document.getElementById('masiva_dias_calculados').innerText = dias;
				diasMasivosCalculados = dias;
			}
		}

		function confirmarVacacionesMasivas(clave_rrhh){
			let inicio = document.getElementById('masiva_inicio').value;
			let fin = document.getElementById('masiva_fin').value;

			if (!inicio || !fin) {
				Swal.fire('Faltan fechas', 'Selecciona fecha de inicio y fin.', 'warning');
				return;
			}
			if (diasMasivosCalculados <= 0) {
				Swal.fire('Rango inválido', 'El rango no tiene días hábiles.', 'warning');
				return;
			}

			Swal.fire({
				title: '¿Aplicar a TODOS los empleados?',
				html: `Se registrarán <strong>${diasMasivosCalculados} días</strong> de vacaciones
					   (${inicio} al ${fin}) para todo el personal activo, ya aprobadas.`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, aplicar a todos',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#0072bc'
			}).then((result) => {
				if (!result.isConfirmed) return;

				fetch('vacaciones_api.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						action: 'vacacionesMasivas',
						fechaInicio: inicio,
						fechaFinal: fin,
						diasTomados: diasMasivosCalculados,
						clave_rrhh: clave_rrhh
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === 'ok') {
						Swal.fire({
							title: data.mgs,
							icon: 'success',
							confirmButtonColor: '#0072bc',
							confirmButtonText: 'Ok'
						}).then(() => location.reload());
					} else {
						Swal.fire('Error', data.msg || 'No se pudo aplicar.', 'error');
					}
				});
			});
		}

		/* ════════════════════════════════════════
		   AUTOCOMPLETE de empleados (buscar por nombre o clave)
		   compartido entre "Registrar directo" y "Editar vacaciones
		   de un usuario"
		════════════════════════════════════════ */
		let empleadosList = <?= json_encode(array_map(function($u){
			return ['clave' => $u['clave'], 'nombre' => $u['nombre']];
		}, $data_usuarios)) ?>;

		function filtrarAutocompleteDirecto(texto){
			let dropdown = document.getElementById('directo_empleado_dropdown');
			let filtro = (texto || '').toLowerCase().trim();
			document.getElementById('directo_empleado').value = '';

			if (filtro === '') {
				dropdown.style.display = 'none';
				dropdown.innerHTML = '';
				return;
			}

			let matches = empleadosList.filter(e =>
				e.nombre.toLowerCase().includes(filtro) || e.clave.toLowerCase().includes(filtro)
			).slice(0, 15);

			if (matches.length === 0) {
				dropdown.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>';
				dropdown.style.display = 'block';
				return;
			}

			dropdown.innerHTML = matches.map((e, i) =>
				`<button type="button" class="list-group-item list-group-item-action" data-idx="${i}">${e.nombre} <small class="text-muted">(${e.clave})</small></button>`
			).join('');
			dropdown.style.display = 'block';

			dropdown.querySelectorAll('button[data-idx]').forEach(btn => {
				btn.addEventListener('click', function(){
					let e = matches[parseInt(this.getAttribute('data-idx'))];
					document.getElementById('directo_empleado').value = e.clave;
					document.getElementById('directo_empleado_input').value = e.nombre + ' (' + e.clave + ')';
					dropdown.style.display = 'none';
				});
			});
		}

		function filtrarAutocompleteEditar(texto){
			let dropdown = document.getElementById('editar_busqueda_dropdown');
			let filtro = (texto || '').toLowerCase().trim();
			document.getElementById('editar_busqueda_clave').value = '';
			document.getElementById('editar_busqueda_registros').innerHTML =
				'<div class="text-center text-muted py-3">Busca y selecciona un empleado para ver sus vacaciones registradas.</div>';

			if (filtro === '') {
				dropdown.style.display = 'none';
				dropdown.innerHTML = '';
				return;
			}

			let matches = empleadosList.filter(e =>
				e.nombre.toLowerCase().includes(filtro) || e.clave.toLowerCase().includes(filtro)
			).slice(0, 15);

			if (matches.length === 0) {
				dropdown.innerHTML = '<div class="list-group-item text-muted">Sin resultados</div>';
				dropdown.style.display = 'block';
				return;
			}

			dropdown.innerHTML = matches.map((e, i) =>
				`<button type="button" class="list-group-item list-group-item-action" data-idx="${i}">${e.nombre} <small class="text-muted">(${e.clave})</small></button>`
			).join('');
			dropdown.style.display = 'block';

			dropdown.querySelectorAll('button[data-idx]').forEach(btn => {
				btn.addEventListener('click', function(){
					let e = matches[parseInt(this.getAttribute('data-idx'))];
					document.getElementById('editar_busqueda_clave').value = e.clave;
					document.getElementById('editar_busqueda_input').value = e.nombre + ' (' + e.clave + ')';
					dropdown.style.display = 'none';
					cargarRegistrosParaEditar(e.clave);
				});
			});
		}

		async function cargarRegistrosParaEditar(clave){
			let cont = document.getElementById('editar_busqueda_registros');
			cont.innerHTML = '<div class="text-center text-muted py-3">Cargando...</div>';

			try {
				let res = await fetch('vacaciones_api.php', {
					method: 'POST',
					headers: {'Content-Type': 'application/json'},
					body: JSON.stringify({ action: 'getVacaciones', clave: clave })
				});
				let data = await res.json();

				if (data.status !== 'ok') {
					cont.innerHTML = '<div class="text-center text-danger py-3">No se pudo cargar el historial.</div>';
					return;
				}

				let historial = data.historial || [];

				if (historial.length === 0) {
					cont.innerHTML = '<div class="text-center text-muted py-3">Este empleado no tiene vacaciones registradas.</div>';
					return;
				}

				let filas = historial.map(d => `
					<tr>
						<td>${d.fecha_inicio} al ${d.fecha_final}</td>
						<td>${d.periodo}</td>
						<td>${d.dias_tomados}</td>
						<td><button class="btn btn-sm btn-outline-primary" onclick="abrirModalEditar(${d.id}, '${d.fecha_inicio}', '${d.fecha_final}', ${d.dias_tomados})">✏️ Editar</button></td>
					</tr>
				`).join('');

				cont.innerHTML = `
					<table class="table table-sm table-bordered mt-2">
						<thead>
							<tr>
								<th>Fechas</th>
								<th>Periodo</th>
								<th>Días</th>
								<th></th>
							</tr>
						</thead>
						<tbody>${filas}</tbody>
					</table>
				`;
			} catch(err){
				cont.innerHTML = '<div class="text-center text-danger py-3">Error de conexión.</div>';
			}
		}

		function abrirModalBuscarEditar(){
			document.getElementById('editar_busqueda_input').value = '';
			document.getElementById('editar_busqueda_clave').value = '';
			document.getElementById('editar_busqueda_dropdown').style.display = 'none';
			document.getElementById('editar_busqueda_dropdown').innerHTML = '';
			document.getElementById('editar_busqueda_registros').innerHTML =
				'<div class="text-center text-muted py-3">Busca y selecciona un empleado para ver sus vacaciones registradas.</div>';
			$('#modalBuscarEditar').modal('show');
		}

		/* ════════════════════════════════════════
		   REGISTRO DIRECTO DE VACACIONES (RRHH)
		════════════════════════════════════════ */
		function abrirModalDirecto(){
			document.getElementById('directo_empleado').value = '';
			document.getElementById('directo_empleado_input').value = '';
			document.getElementById('directo_empleado_dropdown').style.display = 'none';
			document.getElementById('directo_empleado_dropdown').innerHTML = '';
			document.getElementById('directo_inicio').value = '';
			document.getElementById('directo_fin').value = '';
			document.getElementById('directo_dias').value = 0;
			$('#modalDirecto').modal('show');
		}

		function calcularDiasDirecto(){
			let inicio = document.getElementById('directo_inicio').value;
			let fin = document.getElementById('directo_fin').value;

			if (inicio && fin) {
				if (!validarFechas(inicio, fin)) return;
				let dias = contarDiasHabiles(inicio, fin);
				document.getElementById('directo_dias').value = dias;
			}
		}

		function confirmarVacacionesDirecto(clave_rrhh){
			let clave = document.getElementById('directo_empleado').value;
			let nombreEmpleado = document.getElementById('directo_empleado_input').value;
			let inicio = document.getElementById('directo_inicio').value;
			let fin = document.getElementById('directo_fin').value;
			let dias = parseInt(document.getElementById('directo_dias').value) || 0;

			if (!clave) {
				Swal.fire('Falta empleado', 'Busca y selecciona un empleado de la lista.', 'warning');
				return;
			}
			if (!inicio || !fin) {
				Swal.fire('Faltan fechas', 'Selecciona fecha de inicio y fin.', 'warning');
				return;
			}
			if (dias <= 0) {
				Swal.fire('Días inválidos', 'El número de días debe ser mayor a 0.', 'warning');
				return;
			}

			Swal.fire({
				title: '¿Registrar vacaciones directo?',
				html: `Se registrarán <strong>${dias} días</strong> (${inicio} al ${fin}) para
					   <strong>${nombreEmpleado}</strong>, ya aprobadas por completo (sin pasar por
					   el flujo normal de aprobación).`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, registrar',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#0072bc'
			}).then((result) => {
				if (!result.isConfirmed) return;

				fetch('vacaciones_api.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						action: 'insertarVacacionesDirecto',
						clave: clave,
						fechaInicio: inicio,
						fechaFinal: fin,
						diasTomados: dias,
						clave_rrhh: clave_rrhh
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === 'ok') {
						Swal.fire({
							title: data.mgs,
							icon: 'success',
							confirmButtonColor: '#0072bc',
							confirmButtonText: 'Ok'
						}).then(() => location.reload());
					} else {
						Swal.fire('Error', data.msg || 'No se pudo registrar.', 'error');
					}
				});
			});
		}

		/* ════════════════════════════════════════
		   EDITAR REGISTRO DE VACACIONES (RRHH)
		════════════════════════════════════════ */
		function abrirModalEditar(id, fechaInicio, fechaFinal, dias){
			document.getElementById('editar_id').value = id;
			document.getElementById('editar_inicio').value = fechaInicio;
			document.getElementById('editar_fin').value = fechaFinal;
			document.getElementById('editar_dias').value = dias;
			$('#modalEditarVacacion').modal('show');
		}

		function calcularDiasEditar(){
			let inicio = document.getElementById('editar_inicio').value;
			let fin = document.getElementById('editar_fin').value;

			if (inicio && fin) {
				if (!validarFechas(inicio, fin)) return;
				let dias = contarDiasHabiles(inicio, fin);
				document.getElementById('editar_dias').value = dias;
			}
		}

		function confirmarEditarVacacion(clave_rrhh){
			let id = parseInt(document.getElementById('editar_id').value) || 0;
			let inicio = document.getElementById('editar_inicio').value;
			let fin = document.getElementById('editar_fin').value;
			let dias = parseInt(document.getElementById('editar_dias').value) || 0;

			if (id <= 0) {
				Swal.fire('Error', 'Registro inválido.', 'error');
				return;
			}
			if (!inicio || !fin) {
				Swal.fire('Faltan fechas', 'Selecciona fecha de inicio y fin.', 'warning');
				return;
			}
			if (dias <= 0) {
				Swal.fire('Días inválidos', 'El número de días debe ser mayor a 0.', 'warning');
				return;
			}

			Swal.fire({
				title: '¿Guardar cambios?',
				html: `El registro quedará con <strong>${dias} días</strong> (${inicio} al ${fin}).`,
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, guardar',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#0072bc'
			}).then((result) => {
				if (!result.isConfirmed) return;

				fetch('vacaciones_api.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						action: 'editarVacacionDirecto',
						id: id,
						fechaInicio: inicio,
						fechaFinal: fin,
						diasTomados: dias,
						clave_rrhh: clave_rrhh
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === 'ok') {
						Swal.fire({
							title: data.mgs,
							icon: 'success',
							confirmButtonColor: '#0072bc',
							confirmButtonText: 'Ok'
						}).then(() => {
							$('#modalEditarVacacion').modal('hide');
							// Si el registro se editó desde el buscador de usuario, refrescamos
							// esa lista sin recargar toda la página; si no, recargamos normal.
							let claveBusqueda = document.getElementById('editar_busqueda_clave')
								? document.getElementById('editar_busqueda_clave').value
								: '';
							if (claveBusqueda) {
								cargarRegistrosParaEditar(claveBusqueda);
							} else {
								location.reload();
							}
						});
					} else {
						Swal.fire('Error', data.msg || 'No se pudo actualizar.', 'error');
					}
				});
			});
		}

		function confirmarEliminarVacacion(clave_rrhh){
			let id = parseInt(document.getElementById('editar_id').value) || 0;

			if (id <= 0) {
				Swal.fire('Error', 'Registro inválido.', 'error');
				return;
			}

			Swal.fire({
				title: '¿Eliminar este registro de vacaciones?',
				html: 'Esta acción <strong>no se puede deshacer</strong>. El registro se borrará ' +
					  'por completo del historial del empleado.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, eliminar',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#dc2626'
			}).then((result) => {
				if (!result.isConfirmed) return;

				fetch('vacaciones_api.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						action: 'eliminarVacacionDirecto',
						id: id,
						clave_rrhh: clave_rrhh
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === 'ok') {
						Swal.fire({
							title: data.mgs,
							icon: 'success',
							confirmButtonColor: '#dc2626',
							confirmButtonText: 'Ok'
						}).then(() => {
							$('#modalEditarVacacion').modal('hide');
							// Igual que al guardar: si venimos del buscador de usuario,
							// refrescamos esa lista sin recargar toda la página.
							let claveBusqueda = document.getElementById('editar_busqueda_clave')
								? document.getElementById('editar_busqueda_clave').value
								: '';
							if (claveBusqueda) {
								cargarRegistrosParaEditar(claveBusqueda);
							} else {
								location.reload();
							}
						});
					} else {
						Swal.fire('Error', data.msg || 'No se pudo eliminar.', 'error');
					}
				});
			});
		}

		/* ════════════════════════════════════════
		   APROBAR TODAS LAS PENDIENTES (limpieza inicial)
		════════════════════════════════════════ */
		function aprobarTodasPendientes(clave_rrhh){
			Swal.fire({
				title: '¿Aprobar TODAS las pendientes?',
				html: 'Esto marcará como aprobadas por RRHH todas las solicitudes pendientes actuales, ' +
					  'con el comentario automático <strong>"Aprobación masiva inicial"</strong>. ' +
					  'De aquí en adelante solo te aparecerán solicitudes nuevas.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, aprobar todas',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#0072bc'
			}).then((result) => {
				if (!result.isConfirmed) return;

				fetch('vacaciones_api.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						action: 'aprobarTodasPendientesRRHH',
						clave_rrhh: clave_rrhh
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === 'ok') {
						Swal.fire({
							title: data.mgs,
							icon: 'success',
							confirmButtonColor: '#0072bc',
							confirmButtonText: 'Ok'
						}).then(() => location.reload());
					} else {
						Swal.fire('Error', data.msg || 'No se pudo completar.', 'error');
					}
				});
			});
		}

		/* ════════════════════════════════════════
		   APROBAR TODAS SALTANDO AL JEFE DIRECTO
		   (fuerza también estatus_jefe = 1, salvo que el jefe ya
		   haya rechazado explícitamente esa solicitud)
		════════════════════════════════════════ */
		function aprobarTodasSaltandoJefe(clave_rrhh){
			Swal.fire({
				title: '¿Aprobar TODAS saltando al jefe directo?',
				html: 'Esto marcará como aprobadas por RRHH <strong>y por el jefe directo</strong> ' +
					  'todas las solicitudes pendientes, sin esperar a que el jefe resuelva su parte. ' +
					  'Las solicitudes que el jefe ya haya <strong>rechazado</strong> no se tocarán. ' +
					  'Úsalo solo si estás seguro de que no hace falta la revisión del jefe directo.',
				icon: 'warning',
				showCancelButton: true,
				confirmButtonText: 'Sí, aprobar y saltar jefe',
				cancelButtonText: 'Cancelar',
				confirmButtonColor: '#dc2626'
			}).then((result) => {
				if (!result.isConfirmed) return;

				fetch('vacaciones_api.php', {
					method: 'POST',
					headers: { 'Content-Type': 'application/json' },
					body: JSON.stringify({
						action: 'aprobarTodasSaltandoJefe',
						clave_rrhh: clave_rrhh
					})
				})
				.then(res => res.json())
				.then(data => {
					if (data.status === 'ok') {
						Swal.fire({
							title: data.mgs,
							icon: 'success',
							confirmButtonColor: '#dc2626',
							confirmButtonText: 'Ok'
						}).then(() => location.reload());
					} else {
						Swal.fire('Error', data.msg || 'No se pudo completar.', 'error');
					}
				});
			});
		}

		/* ════════════════════════════════════════
		   REPORTE de solicitudes de vacaciones (tab Reporte)
		════════════════════════════════════════ */
		let reporteVacacionesYaCargado = false;

		function badgeEstatusReporte(estatus){
			estatus = parseInt(estatus);
			if (estatus === 1) return '<span class="badge badge-success">Aprobado</span>';
			if (estatus === 2) return '<span class="badge badge-danger">Rechazado</span>';
			return '<span class="badge badge-primary">Enviado</span>';
		}

		function formatoFechaReporte(f){
			if (!f) return '';
			let soloFecha = String(f).split(' ')[0];
			let partes = soloFecha.split('-');
			if (partes.length === 3) return `${partes[2]}/${partes[1]}/${partes[0]}`;
			return f;
		}

		function cargarReporteVacaciones(){
			let desde  = document.getElementById('reporte_fecha_desde').value;
			let hasta  = document.getElementById('reporte_fecha_hasta').value;
			let nombre = document.getElementById('reporte_nombre').value.trim();

			let tbody = document.getElementById('reporte-body');
			tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Cargando...</td></tr>';
			document.getElementById('reporte_resumen').innerText = '';

			fetch('vacaciones_api.php', {
				method: 'POST',
				headers: { 'Content-Type': 'application/json' },
				body: JSON.stringify({
					action: 'getReporteVacaciones',
					clave_rrhh: '<?= $data_usr["clave"] ?>',
					fechaDesde: desde,
					fechaHasta: hasta,
					nombre: nombre
				})
			})
			.then(res => res.json())
			.then(data => {
				if (data.status !== 'ok') {
					tbody.innerHTML = `<tr><td colspan="8" class="text-center text-danger">${data.msg || 'No se pudo cargar el reporte.'}</td></tr>`;
					return;
				}

				let filas = data.solicitudes || [];
				document.getElementById('reporte_resumen').innerText = filas.length + ' solicitud(es) encontradas.' + (data.limitado ? ' (mostrando las más recientes, afina el filtro para ver menos)' : '');

				if (filas.length === 0) {
					tbody.innerHTML = '<tr><td colspan="8" class="text-center text-muted">Sin resultados para estos filtros.</td></tr>';
					return;
				}

				tbody.innerHTML = filas.map(f => `
					<tr>
						<td>${f.clave ?? ''}</td>
						<td>${f.nombre ?? ''}</td>
						<td>${formatoFechaReporte(f.fecha_inicio)}</td>
						<td>${formatoFechaReporte(f.fecha_final)}</td>
						<td>${f.periodo ?? ''}</td>
						<td>${f.dias_tomados ?? ''}</td>
						<td>${f.fecha_captura ?? ''}</td>
						<td>${badgeEstatusReporte(f.estatus)}</td>
					</tr>
				`).join('');
			})
			.catch(() => {
				tbody.innerHTML = '<tr><td colspan="8" class="text-center text-danger">Error de conexión.</td></tr>';
			});
		}

		function limpiarFiltrosReporte(){
			document.getElementById('reporte_fecha_desde').value = '';
			document.getElementById('reporte_fecha_hasta').value = '';
			document.getElementById('reporte_nombre').value = '';
			document.getElementById('reporte-body').innerHTML = '<tr><td colspan="8" class="text-center text-muted">Usa los filtros y presiona "Filtrar" para ver resultados (o filtra en blanco para traer las más recientes).</td></tr>';
			document.getElementById('reporte_resumen').innerText = '';
		}

		// Carga automática la primera vez que se abre la pestaña, para no
		// obligar a dar clic en "Filtrar" si no se quiere filtrar nada.
		$(document).on('shown.bs.tab', 'a[href="#reporte"]', function(){
			if (!reporteVacacionesYaCargado) {
				reporteVacacionesYaCargado = true;
				cargarReporteVacaciones();
			}
		});

		/* ════════════════════════════════════════
		   BUSCADOR del tab Pendientes RRHH
		════════════════════════════════════════ */
		function filtrarPendientesRRHH(texto){
			const filtro = texto.toLowerCase().trim();
			const tarjetas = document.querySelectorAll('#lista-pendientes-rrhh .pendiente-card');

			tarjetas.forEach(function(card){
				const nombre = card.getAttribute('data-nombre') || '';
				const clave  = card.getAttribute('data-clave')  || '';
				card.style.display = (nombre.includes(filtro) || clave.includes(filtro)) ? '' : 'none';
			});
		}

	</script>
</html>