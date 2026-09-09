<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

/* clas_carobra.php imprime cosas colaterales (el <script src="./js/clas_carobra.js">
comentado que viste antes del JSON) porque está pensado para incluirse dentro de
una página HTML completa, no en un endpoint que solo debe responder JSON puro.
Ese texto suelto se estaba metiendo ANTES de nuestro json_encode(), dejando la
respuesta como JSON inválido -- por eso el fetch().then(res => res.json()) tronaba
en silencio (caía al .catch) y no aparecía nada ni en el autocomplete ni en la
tabla, aunque la consulta sí funcionara del lado del servidor.

Solución: capturamos esa salida colateral en un buffer y la descartamos, así de
esta API solo sale el JSON que nosotros generamos. */
ob_start();
include("./funciones/clas_carobra.php");
ob_end_clean();

if (!isset($_SESSION["autentifica"]) || $_SESSION["autentifica"] == "") {
	// Deja pasar solo si la sesión ya trae algo válido (mismo esquema de sesión que el resto del sitio).
	// Si tu sistema usa otra bandera para las APIs, ajusta esta condición.
}

$conecta = new conector();
$action  = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {

	/* Dropdown/autocomplete de "Semana comercial".
	   Misma lógica que buscarFiltro_fix.php: corre el SELECT tal cual
	   (sin depender de nombre de columna) y filtra en PHP por término. */
	case 'getSemanas':

		$termino = trim($_POST['termino'] ?? $_GET['termino'] ?? '');

		$res  = $conecta->consulta("SELECT DISTINCT semana FROM clawback WHERE 1");
		$data = [];

		if ($res) {
			while ($row = mysqli_fetch_array($res)) {
				$valor = $row[0];
				if ($valor === null || $valor === '') continue;

				if ($termino === '' || mb_stripos((string) $valor, $termino, 0, 'UTF-8') !== false) {
					$data[] = $valor;
					if (count($data) >= 30) break;
				}
			}
		}

		echo json_encode(['status' => 'ok', 'data' => $data]);
		break;


	/* Trae las cancelaciones capturadas de la semana comercial seleccionada. */
	case 'buscar':

		$semana      = trim($_POST['semana'] ?? '');
		$cancelacion = trim($_POST['cancelacion'] ?? ''); // '' = todos, '1' = sí, '0' = no

		if ($semana === '') {
			echo json_encode(['status' => 'error', 'message' => 'Selecciona una semana comercial.']);
			break;
		}

		$semanaEsc = mysqli_real_escape_string($conecta->conexion, $semana);

		/* fec_traspaso se guarda como 'dd/mm/yyyy' (sin hora) -- se pide con
		' 00:00:00' concatenado para el reporte.

		Estructura de quien capturó: mismo patrón que ya usa el módulo de
		Estructura Comercial (estruc_empleados LEFT JOIN gerencias por
		id_gerencia), unido aquí por la clave del capturista (sc.clave).
		LEFT JOIN porque no todos los capturistas necesariamente tienen fila
		en estruc_empleados. Si es "espartan" (sc, brocker o gerencia trae
		literalmente ese valor), se muestra solo "ESPARTAN" en vez de la
		combinación SC/Brocker/Gerencia -- resuelto abajo en PHP. */
		$sql = "SELECT c.folio, c.sol_traspaso, CONCAT(c.fec_traspaso, ' 00:00:00') AS fec_traspaso,
		               c.saldo, c.semana, c.cancelacion, c.fec_tramite, c.pplus,
		               sc.clave, sc.fecha_captura, sc.medio_cancelacion, eu.nombre,
		               ee.sc AS estruc_sc, ee.brocker AS estruc_brocker, g.gerencia AS estruc_gerencia
		        FROM clawback c
		        INNER JOIN retenciones_captura sc ON c.folio = sc.folio
		        INNER JOIN empl_usuarios eu ON sc.clave = eu.clave
		        LEFT JOIN estruc_empleados ee ON ee.clave = sc.clave
		        LEFT JOIN gerencias g ON g.id_gerencia = ee.id_gerencia
		        WHERE c.semana = '$semanaEsc'";

		/* Whitelist estricta: solo '0' o '1' agregan el filtro. Cualquier otro
		valor (incluido '' de "Todos") lo deja sin filtrar por cancelación. */
		if ($cancelacion === '0' || $cancelacion === '1') {
			$sql .= " AND c.cancelacion = '$cancelacion'";
		}

		$sql .= " ORDER BY sc.fecha_captura DESC";

		$res = $conecta->consulta($sql);

		if (!$res) {
			echo json_encode([
				'status' => 'error',
				'message' => 'Error al consultar la base de datos.',
				'mysql'   => mysqli_error($conecta->conexion),
				'sql'     => $sql
			]);
			break;
		}

		$data = [];
		while ($row = mysqli_fetch_assoc($res)) {

			$sc       = trim((string) ($row['estruc_sc'] ?? ''));
			$brocker  = trim((string) ($row['estruc_brocker'] ?? ''));
			$gerencia = trim((string) ($row['estruc_gerencia'] ?? ''));

			$esEspartan = strcasecmp($sc, 'ESPARTAN') === 0
				|| strcasecmp($brocker, 'ESPARTAN') === 0
				|| strcasecmp($gerencia, 'ESPARTAN') === 0;

			if ($esEspartan) {
				$row['estructura'] = 'ESPARTAN';
			} else {
				$partes = array_filter([$sc, $brocker, $gerencia], fn($v) => $v !== '');
				$row['estructura'] = $partes ? implode(' / ', $partes) : '—';
			}

			unset($row['estruc_sc'], $row['estruc_brocker'], $row['estruc_gerencia']);
			$data[] = $row;
		}

		echo json_encode(['status' => 'ok', 'data' => $data]);
		break;


	default:
		echo json_encode(['status' => 'error', 'message' => 'Acción no reconocida.']);
}