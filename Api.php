<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api extends CI_Controller {

    public function __construct() {
        parent::__construct();
        $this->load->database();
        header('Content-Type: application/json');
    }

    /**
     * Helper: obtiene el filtro de estructura (SC > Brocker > Gerencia)
     * a partir del id_gerencia del usuario logueado.
     * Regresa ['campo' => 'sc'|'brocker'|'gerencia', 'valor' => '...']
     */
    private function obtener_filtro_estructura($id_gerencia) {
        $estruc = $this->db->query("
            SELECT sc, brocker, gerencia FROM estruc_empleados WHERE id_gerencia = ? LIMIT 1
        ", [$id_gerencia])->row_array();

        if (!$estruc) {
            return ['campo' => 'gerencia', 'valor' => ''];
        }

        if (!empty($estruc['sc'])) {
            return ['campo' => 'sc', 'valor' => $estruc['sc']];
        }

        if (!empty($estruc['brocker'])) {
            return ['campo' => 'brocker', 'valor' => $estruc['brocker']];
        }

        return ['campo' => 'gerencia', 'valor' => $estruc['gerencia'] ?? ''];
    }

	// POST /api/login
	public function login() {
		$json = $this->input->raw_input_stream;
		$data = json_decode($json, true);

		$clave      = $data['clave']      ?? '';
		$contrasena = $data['contrasena'] ?? '';

		if (!$clave || !$contrasena) {
			echo json_encode([
				'success' => false,
				'message' => 'Clave y contraseña son requeridos'
			]);
			return;
		}

		$query = $this->db->query("
			SELECT u.clave, u.nombre, u.contrasena, eu.no_empleado
			FROM usuarios u
			INNER JOIN empl_usuarios eu ON u.clave = eu.clave
			WHERE u.clave = ?
			AND u.contrasena = ?
			AND u.activo = 1
			LIMIT 1
		", [$clave, $contrasena]);

		$usuario = $query->row_array();

		if (!$usuario) {
			echo json_encode([
				'success' => false,
				'message' => 'Clave o contraseña incorrectos'
			]);
			return;
		}

		$nivel_row = $this->db->query("
			SELECT clave, id_nivel FROM (
				SELECT clave, MAX(id_nivel) as id_nivel
				FROM usuarios_nivel
				WHERE clave = ?
				AND id_nivel IN (1,6,12)
				UNION ALL
				SELECT clave, MAX(id_nivel) as id_nivel
				FROM usuarios_nivel_gerencia
				WHERE clave = ?
			) n
			WHERE id_nivel IS NOT NULL
			ORDER BY FIELD(id_nivel, 12,13,6,5,1,4,3)
			LIMIT 1
		", [$clave, $clave])->row_array();

		$id_nivel = $nivel_row['id_nivel'] ?? null;

		$gerencia_row = $this->db->query("
			SELECT id_gerencia FROM usuarios_gerencia WHERE clave = ? LIMIT 1
		", [$clave])->row_array();

		$id_gerencia = $gerencia_row['id_gerencia'] ?? null;

		// Verificar si es usuario Spartan
		$spartan_row = $this->db->query("
			SELECT clave FROM spartan_usuarios WHERE clave = ? LIMIT 1
		", [$clave])->row_array();

		$es_spartan = $spartan_row ? true : false;

		echo json_encode([
			'success' => true,
			'data'    => [
				'clave'       => $usuario['clave'],
				'nombre'      => $usuario['nombre'],
				'no_empleado' => $usuario['no_empleado'],
				'id_gerencia' => $id_gerencia,
				'id_nivel'    => $id_nivel,
				'es_spartan'  => $es_spartan,
			]
		]);
	}

    // GET /api/buscar-folio?q=8080
	public function buscar_folio() {
		$q           = $this->input->get('q')           ?? '';
		$id_nivel    = $this->input->get('id_nivel')    ?? '';
		$no_empleado = $this->input->get('no_empleado') ?? '';
		$id_gerencia = $this->input->get('id_gerencia') ?? '';

		if (strlen(trim($q)) < 2) {
			echo json_encode([
				'success' => false,
				'message' => 'Ingresa al menos 2 caracteres',
				'data'    => []
			]);
			return;
		}

		$where_fecha = " STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH) ";

		if ($id_nivel == '12') {
			// SuperUsuario — ve todo
			$sql = "
				SELECT
					pt.folio, pt.trabajador, pt.empresa, pt.telefono, pt.curp,
					pt.email_trab, pt.cp, p.nss, p.nap, p.fec_traspaso,
					p.no_empleado, p.afo_cedente, p.saldo_estimado,
					CONCAT(p.nap, ' | ', eu.nombre) COLLATE latin1_general_ci AS nap_nombre,
					eu_com.nombre COLLATE latin1_general_ci AS nombre_comisionista_actual,
					(SELECT costo FROM acompaniamiento WHERE folio = p.folio LIMIT 1) AS acompanamiento_actual

				FROM prod_trabajador pt
				INNER JOIN produccion p ON pt.folio = p.folio
				LEFT JOIN naps n ON p.nap = n.nap
				LEFT JOIN empl_usuarios eu ON eu.no_empleado = n.no_empleado
				LEFT JOIN empl_usuarios eu_com ON eu_com.no_empleado = p.no_empleado
				WHERE pt.folio LIKE ?
				AND p.id_estatus_prod != 6
				AND {$where_fecha}
				LIMIT 20
			";
			$params = ['%' . $q . '%'];

		} elseif ($id_nivel == '3') {
			// Asesor — solo sus propios folios
			$sql = "
				SELECT
					pt.folio, pt.trabajador, pt.empresa, pt.telefono, pt.curp,
					pt.email_trab, pt.cp, p.nss, p.nap, p.fec_traspaso,
					p.no_empleado, p.afo_cedente, p.saldo_estimado,
					CONCAT(p.nap, ' | ', eu.nombre) COLLATE latin1_general_ci AS nap_nombre,
					eu_com.nombre COLLATE latin1_general_ci AS nombre_comisionista_actual,
					(SELECT costo FROM acompaniamiento WHERE folio = p.folio LIMIT 1) AS acompanamiento_actual

				FROM prod_trabajador pt
				INNER JOIN produccion p ON pt.folio = p.folio
				LEFT JOIN naps n ON p.nap = n.nap
				LEFT JOIN empl_usuarios eu ON eu.no_empleado = n.no_empleado
				LEFT JOIN empl_usuarios eu_com ON eu_com.no_empleado = p.no_empleado
				WHERE pt.folio LIKE ?
				AND p.no_empleado = ?
				AND p.id_estatus_prod != 6
				AND {$where_fecha}
				LIMIT 20
			";
			$params = ['%' . $q . '%', $no_empleado];

		} else {
			// Niveles 1, 4, 5, 6, 11 — jerarquía SC > Brocker > Gerencia
			$filtro = $this->obtener_filtro_estructura($id_gerencia);
			$campo  = $filtro['campo'];
			$valor  = $filtro['valor'];

			$sql = "
				SELECT
					pt.folio, pt.trabajador, pt.empresa, pt.telefono, pt.curp,
					pt.email_trab, pt.cp, p.nss, p.nap, p.fec_traspaso,
					p.no_empleado, p.afo_cedente, p.saldo_estimado,
					CONCAT(p.nap, ' | ', eu.nombre) COLLATE latin1_general_ci AS nap_nombre,
					eu_com.nombre COLLATE latin1_general_ci AS nombre_comisionista_actual,
					(SELECT costo FROM acompaniamiento WHERE folio = p.folio LIMIT 1) AS acompanamiento_actual

				FROM naps n
				LEFT JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
				LEFT JOIN estruc_empleados ee ON eu.no_empleado = ee.no_empleado
				INNER JOIN produccion p ON n.nap = p.nap
				INNER JOIN prod_trabajador pt ON pt.folio = p.folio
				LEFT JOIN empl_usuarios eu_com ON eu_com.no_empleado = p.no_empleado
				WHERE pt.folio LIKE ?
				AND ee.{$campo} = ?
				AND n.baja = 0
				AND p.id_estatus_prod != 6
				AND {$where_fecha}
				LIMIT 20
			";
			$params = ['%' . $q . '%', $valor];
		}

		$query = $this->db->query($sql, $params);

		echo json_encode([
			'success' => true,
			'data'    => $query->result_array()
		]);
	}

	// GET /api/validar-folio?folio=8080397776
	public function validar_folio() {
		$folio = $this->input->get('folio') ?? '';

		if (!$folio) {
			echo json_encode([
				'success' => false,
				'message' => 'Folio requerido'
			]);
			return;
		}

		$query = $this->db->query("
			SELECT 1 FROM produccion WHERE folio = ?
			LIMIT 1
		", [$folio]);

		$existe = $query->num_rows() > 0;

		echo json_encode([
			'success' => true,
			'data'    => [
				'existe' => $existe,
				'folio'  => $folio,
			]
		]);
	}

	// GET /api/validar-nap-activo?folio=8080397776
	public function validar_nap_activo() {
		$folio = $this->input->get('folio') ?? '';

		if (!$folio) {
			echo json_encode(['success' => false, 'message' => 'Folio requerido']);
			return;
		}

		$query = $this->db->query("
			SELECT p.nap
			FROM (SELECT folio, nap FROM produccion WHERE folio = ?) p
			INNER JOIN naps n ON (p.nap = n.nap)
			WHERE baja = 1
		", [$folio]);

		if ($query->num_rows() > 0) {
			$nap = $query->row_array()['nap'];

			$this->db->query("
				UPDATE produccion SET id_estatus_prod = 6 WHERE folio = ?
			", [$folio]);

			$this->db->query("
				UPDATE prod_trabajador
				SET observa = CONCAT(observa, ' El NAP de este folio está inhabilitado')
				WHERE folio = ?
			", [$folio]);

			$existe = $this->db->query("
				SELECT * FROM prod_rechazos WHERE folio = ?
			", [$folio]);

			if ($existe->num_rows() == 0) {
				$this->db->query("
					INSERT INTO prod_rechazos VALUES (?, ?, 2, 2806, 0)
				", [$folio, date('d/m/Y')]);
			}

			echo json_encode([
				'success' => true,
				'data'    => [
					'activo'  => false,
					'nap'     => $nap,
					'message' => 'Folio con NAP inhabilitado. No puede ser capturado.',
				]
			]);
			return;
		}

		echo json_encode([
			'success' => true,
			'data'    => [
				'activo'  => true,
				'message' => 'NAP activo',
			]
		]);
	}

	// GET /api/validar-nap?nap=1112027171&fec_traspaso=08/05/2026
	public function validar_nap() {
		$nap          = $this->input->get('nap')          ?? '';
		$fec_traspaso = $this->input->get('fec_traspaso') ?? '';

		if (!$nap || !$fec_traspaso) {
			echo json_encode(['success' => false, 'message' => 'NAP y fecha de traspaso requeridos']);
			return;
		}

		// Convertir fec_traspaso dd/mm/yyyy → yyyy-mm-dd para comparar
		$partes = explode('/', $fec_traspaso);
		if (count($partes) !== 3) {
			echo json_encode(['success' => false, 'message' => 'Fecha de traspaso inválida']);
			return;
		}
		$fecha_iso = $partes[2] . '-' . $partes[1] . '-' . $partes[0]; // 2026-07-12

		// ─── Buscar el periodo que CONTENGA la fecha del traspaso ───────────
		// Las fechas en fechas_cierre_com son dd/mm/yyyy también
		$periodo = $this->db->query("
			SELECT anio, mes, nom_periodo, fec_ini, fec_fin, folxnap
			FROM fechas_cierre_com
			WHERE STR_TO_DATE(fec_ini, '%d/%m/%Y') <= ?
			AND STR_TO_DATE(fec_fin, '%d/%m/%Y') >= ?
			LIMIT 1
		", [$fecha_iso, $fecha_iso])->row_array();

		// Si no cae en ningún periodo, tomar el más reciente
		if (!$periodo) {
			$periodo = $this->db->query("
				SELECT anio, mes, nom_periodo, fec_ini, fec_fin, folxnap
				FROM fechas_cierre_com
				ORDER BY anio DESC, mes DESC
				LIMIT 1
			")->row_array();
		}

		if (!$periodo) {
			echo json_encode(['success' => false, 'message' => 'No se encontró periodo activo']);
			return;
		}

		$mes  = (int) $periodo['mes'];
		$anio = (int) $periodo['anio'];

		// ─── Contar folios del NAP en ese periodo ───────────────────────────
		$conteo = $this->db->query("
			SELECT COUNT(*) as v
			FROM (
				SELECT folio, fec_traspaso, id_estatus_prod
				FROM produccion
				WHERE nap = ?
			) p
			INNER JOIN (
				SELECT fec_ini, fec_fin
				FROM fechas_cierre_com
				WHERE mes = ? AND anio = ?
			) cf ON (
				DATEDIFF(
					CONCAT(SUBSTR(fec_traspaso,-4),'-',SUBSTR(fec_traspaso,4,2),'-',SUBSTR(fec_traspaso,1,2)),
					CONCAT(SUBSTR(fec_ini,-4),'-',SUBSTR(fec_ini,4,2),'-',SUBSTR(fec_ini,1,2))
				) >= 0
				AND DATEDIFF(
					CONCAT(SUBSTR(fec_traspaso,-4),'-',SUBSTR(fec_traspaso,4,2),'-',SUBSTR(fec_traspaso,1,2)),
					CONCAT(SUBSTR(fec_fin,-4),'-',SUBSTR(fec_fin,4,2),'-',SUBSTR(fec_fin,1,2))
				) <= 0
			)
			LEFT JOIN movilidad m ON (p.folio = m.folio)
			WHERE id_estatus_prod != 6
			AND IFNULL(movilidad, 0) = 1
		", [$nap, $mes, $anio]);

		$total      = (int) $conteo->row_array()['v'];
		$limite     = (int) $periodo['folxnap'];
		$disponible = $limite - $total;
		$advertencia_desde = $limite - 5;

		$valido      = $disponible > 0;
		$advertencia = $total >= $advertencia_desde && $valido;

		echo json_encode([
			'success' => true,
			'data'    => [
				'nap'         => $nap,
				'periodo'     => $periodo['nom_periodo'],
				'mes'         => $periodo['mes'],
				'anio'        => $periodo['anio'],
				'fec_ini'     => $periodo['fec_ini'],
				'fec_fin'     => $periodo['fec_fin'],
				'limite'      => $limite,
				'usados'      => $total,
				'disponible'  => $disponible,
				'valido'      => $valido,
				'advertencia' => $advertencia,
			]
		]);
	}

    // GET /api/buscar-nap?q=1112
    public function buscar_nap() {
        $q = $this->input->get('q') ?? '';

        if (strlen(trim($q)) < 2) {
            echo json_encode([
                'success' => false,
                'message' => 'Ingresa al menos 2 caracteres',
                'data'    => []
            ]);
            return;
        }

        $query = $this->db->query("
            SELECT
                n.nap,
                eu.nombre
            FROM naps n
            LEFT JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
            WHERE n.nap LIKE ?
               OR eu.nombre LIKE ?
            LIMIT 20
        ", ['%' . $q . '%', '%' . $q . '%']);

        echo json_encode([
            'success' => true,
            'data'    => $query->result_array()
        ]);
    }
	
	// GET /api/dueno-nap?nap=2505003729
	public function dueno_nap() {
		$nap = $this->input->get('nap') ?? '';

		if (!$nap) {
			echo json_encode(['success' => false, 'message' => 'NAP requerido']);
			return;
		}

		$query = $this->db->query("
			SELECT eu.no_empleado, eu.nombre
			FROM naps n
			INNER JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
			WHERE n.nap = ?
			LIMIT 1
		", [$nap]);

		$dueno = $query->row_array();

		echo json_encode([
			'success' => true,
			'data'    => $dueno ?? null,
		]);
	}

	// POST /api/subir-evidencia
	public function subir_evidencia() {
		$folio = $this->input->post('folio') ?? '';

		if (!$folio) {
			echo json_encode(['success' => false, 'message' => 'Folio requerido']);
			return;
		}

		if (!isset($_FILES['archivo'])) {
			echo json_encode(['success' => false, 'message' => 'No se recibió ningún archivo']);
			return;
		}

		$archivo    = $_FILES['archivo'];
		$extension  = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
		$permitidas = ['pdf', 'jpg', 'jpeg', 'png'];

		if (!in_array($extension, $permitidas)) {
			echo json_encode(['success' => false, 'message' => 'Tipo de archivo no permitido']);
			return;
		}

		if ($archivo['size'] > 10 * 1024 * 1024) {
			echo json_encode(['success' => false, 'message' => 'El archivo excede 10 MB']);
			return;
		}

		$fecha        = date('Ymd_His');
		$tipo         = ($extension === 'pdf') ? 'documento' : 'foto';
		$nombre_final = "{$folio}_{$fecha}_{$tipo}.{$extension}";

		$carpeta_destino = '/var/www/html/afore/evidencias/' . $folio . '/';

		if (!is_dir($carpeta_destino)) {
			mkdir($carpeta_destino, 0775, true);
		}

		$ruta_completa = $carpeta_destino . $nombre_final;

		if (!move_uploaded_file($archivo['tmp_name'], $ruta_completa)) {
			echo json_encode(['success' => false, 'message' => 'Error al guardar el archivo']);
			return;
		}

		$ruta_relativa = 'evidencias/' . $folio . '/' . $nombre_final;

		$this->db->query("
			INSERT INTO evidencias (folio, nombre, tipo, ruta, tamanio)
			VALUES (?, ?, ?, ?, ?)
		", [$folio, $nombre_final, $extension, $ruta_relativa, $archivo['size']]);

		$id = $this->db->insert_id();

		echo json_encode([
			'success' => true,
			'data'    => [
				'id'      => $id,
				'nombre'  => $nombre_final,
				'tipo'    => $extension,
				'url'     => 'https://www.sis-ca.com.mx:7863/afore/' . $ruta_relativa,
				'tamanio' => $archivo['size'],
			]
		]);
	}

	// GET /api/evidencias?folio=8080397776
	public function evidencias() {
		$folio = $this->input->get('folio') ?? '';

		if (!$folio) {
			echo json_encode(['success' => false, 'message' => 'Folio requerido']);
			return;
		}

		$query = $this->db->query("
			SELECT id, nombre, tipo, ruta, tamanio, creado_en
			FROM evidencias
			WHERE folio = ?
			ORDER BY creado_en DESC
		", [$folio]);

		$resultados = $query->result_array();
		foreach ($resultados as &$r) {
			$r['url'] = base_url($r['ruta']);
		}

		echo json_encode([
			'success' => true,
			'data'    => $resultados
		]);
	}

	// GET /api/validar-comisionista?clave=AAGARCIA
	public function validar_comisionista() {
		$clave = $this->input->get('clave') ?? '';

		if (!$clave) {
			echo json_encode(['success' => false, 'message' => 'Clave requerida']);
			return;
		}

		$query = $this->db->query("
			SELECT COUNT(*) as total
			FROM insuficiencias i
			INNER JOIN (
				SELECT * FROM usuarios_nivel_gerencia WHERE clave = ?
			) ung ON i.clave = ung.clave
			WHERE fec_pago = '' AND ajuste < -200
		", [$clave]);

		$total = (int) $query->row_array()['total'];

		echo json_encode([
			'success' => true,
			'data'    => [
				'tiene_insuficiencias' => $total > 0,
				'total'               => $total,
			]
		]);
	}

    // GET /api/afores
    public function afores() {
        $query = $this->db->query("SELECT clave, afore FROM afores ORDER BY afore ASC");

        echo json_encode([
            'success' => true,
            'data'    => $query->result_array()
        ]);
    }

	// GET /api/validar-bloqueo
	public function validar_bloqueo() {
		$query = $this->db->query("SELECT * FROM bloqueo_pago LIMIT 1");
		
		echo json_encode([
			'success' => true,
			'data'    => [
				'bloqueado' => $query->num_rows() > 0,
			]
		]);
	}

	// GET /api/buscar-comisionista?q=juan
	public function buscar_comisionista() {
		$q = $this->input->get('q') ?? '';

		if (strlen(trim($q)) < 2) {
			echo json_encode([
				'success' => false,
				'message' => 'Ingresa al menos 2 caracteres',
				'data'    => []
			]);
			return;
		}

		$query = $this->db->query("
			SELECT clave, no_empleado, nombre, activo
			FROM empl_usuarios
			WHERE clave LIKE ?
			OR no_empleado LIKE ?
			OR nombre LIKE ?
			LIMIT 20
		", ['%'.$q.'%', '%'.$q.'%', '%'.$q.'%']);

		echo json_encode([
			'success' => true,
			'data'    => $query->result_array()
		]);
	}

	// POST /api/guardar-traspaso
	public function guardar_traspaso() {
		$json = $this->input->raw_input_stream;
		$data = json_decode($json, true);

		$folio           = $data['folio']          ?? '';
		$no_empleado     = $data['no_empleado']    ?? '';
		$afo_cedente     = $data['afo_cedente']    ?? '';
		$acompanamiento  = $data['acompanamiento'] ?? '0.00';
		$en_temporales   = $data['en_temporales']  ?? false;
		$id_usuario_cap  = $data['id_usuario_cap'] ?? 0;
		$clave_usuario   = $data['clave_usuario']  ?? '';
		$empresa         = $data['empresa']        ?? '';
		$email           = $data['email']          ?? '';
		$celular         = $data['celular']        ?? '';
		$cp              = $data['cp']             ?? '';

		if (!$folio || !$no_empleado) {
			echo json_encode(['success' => false, 'message' => 'Folio y comisionista requeridos']);
			return;
		}

		$prod = $this->db->query("
			SELECT * FROM produccion WHERE folio = ? LIMIT 1
		", [$folio])->row_array();

		if (!$prod) {
			echo json_encode(['success' => false, 'message' => 'Folio no encontrado en producción']);
			return;
		}

		$trab = $this->db->query("
			SELECT * FROM prod_trabajador WHERE folio = ? LIMIT 1
		", [$folio])->row_array();

		$fecha_hoy = date('d/m/Y');

		$tabla_acomp     = $en_temporales ? 'cap_acompaniamiento_tem' : 'acompaniamiento';
		$puede_acompañar = false;

		if ((float)$acompanamiento > 0) {
			$reg_acomp = $this->db->query("
				SELECT folio, fec_pago, estatus FROM {$tabla_acomp} WHERE folio = ? LIMIT 1
			", [$folio])->row_array();

			if (!$reg_acomp) {
				$this->db->query("
					INSERT INTO {$tabla_acomp} (folio, fec_pago, costo, estatus)
					VALUES (?, '', ?, 0)
				", [$folio, $acompanamiento]);
				$puede_acompañar = true;

			} elseif ($reg_acomp['estatus'] == 0 && empty($reg_acomp['fec_pago'])) {
				$this->db->query("
					UPDATE {$tabla_acomp} SET costo = ? WHERE folio = ?
				", [$acompanamiento, $folio]);
				$puede_acompañar = true;

			} else {
				$puede_acompañar = false;
			}
		}

		if ($en_temporales) {
			$existe_temp = $this->db->query("
				SELECT folio FROM cap_prod_tem WHERE folio = ? LIMIT 1
			", [$folio])->num_rows();

			if ($existe_temp > 0) {
				$this->db->query("
					UPDATE cap_prod_tem SET
						no_empleado    = ?,
						afo_cedente    = ?,
						fec_captura    = ?,
						id_usuario_cap = ?
					WHERE folio = ?
				", [$no_empleado, $afo_cedente, $fecha_hoy, $id_usuario_cap, $folio]);
			} else {
				$this->db->query("
					INSERT INTO cap_prod_tem (
						folio, fec_captura, nss, id_usuario_cap, no_empleado,
						id_tipo_mov, fec_traspaso, fec_cierre, nap,
						saldo_estimado, afo_cedente, viable, id_estatus_anticipo,
						fec_anticipo, fec_pago_ant, monto_anticipo, monto_incentivo,
						id_estatus_prod, cierre_cap, cap_principal,
						estatus_confirma, fec_ult_estatus, no_asesor_av
					) VALUES (
						?, ?, ?, ?, ?,
						?, ?, ?, ?,
						?, ?, ?, ?,
						?, ?, ?, ?,
						?, ?, ?,
						?, ?, ?
					)
				", [
					$folio,
					$fecha_hoy,
					$prod['nss'],
					$id_usuario_cap,
					$no_empleado,
					1,
					$prod['fec_traspaso'],
					$prod['fec_cierre']          ?? '',
					$prod['nap'],
					$prod['saldo_estimado'],
					$afo_cedente,
					$prod['viable'],
					$prod['id_estatus_anticipo'],
					$prod['fec_anticipo']         ?? '',
					$prod['fec_pago_ant']         ?? '',
					$prod['monto_anticipo']       ?? 0,
					$prod['monto_incentivo']      ?? 0,
					$prod['id_estatus_prod'],
					$prod['cierre_cap'],
					$prod['cap_principal'],
					$prod['estatus_confirma']     ?? '',
					$fecha_hoy,
					$prod['no_asesor_av']         ?? 0,
				]);
			}

			$existe_trab_tem = $this->db->query("
				SELECT folio FROM cap_prod_trabajador_tem WHERE folio = ? LIMIT 1
			", [$folio])->num_rows();

			if ($existe_trab_tem > 0) {
				$this->db->query("
					UPDATE cap_prod_trabajador_tem SET
						empresa    = ?,
						email_trab = ?,
						celular    = ?,
						cp         = ?
					WHERE folio = ?
				", [$empresa ?: ($trab['empresa'] ?? ''), $email ?: ($trab['email_trab'] ?? ''), $celular ?: ($trab['celular'] ?? ''), $cp ?: ($trab['cp'] ?? ''), $folio]);
			} else {
				$this->db->query("
					INSERT INTO cap_prod_trabajador_tem (
						folio, no_oportunidad, trabajador, empresa,
						telefono, celular, observa, CURP,
						email_trab, sal_mes, fec_sello, vigencia, cp
					) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
				", [
					$folio,
					$trab['no_oportunidad']  ?? '',
					$trab['trabajador']      ?? '',
					$empresa ?: ($trab['empresa']   ?? ''),
					$trab['telefono']        ?? '',
					$celular ?: ($trab['celular']   ?? ''),
					$trab['observa']         ?? '',
					$trab['CURP']            ?? '',
					$email ?: ($trab['email_trab']  ?? ''),
					$trab['sal_mes']         ?? 0,
					$trab['fec_sello']       ?? '',
					$trab['vigencia']        ?? 0,
					$cp ?: ($trab['cp']             ?? ''),
				]);
			}

		} else {
			$this->db->query("
				UPDATE produccion SET
					no_empleado    = ?,
					afo_cedente    = ?,
					id_usuario_cap = ?
				WHERE folio = ?
			", [$no_empleado, $afo_cedente, $id_usuario_cap, $folio]);

			$this->db->query("
				UPDATE prod_trabajador SET
					empresa    = ?,
					email_trab = ?,
					celular    = ?,
					cp         = ?
				WHERE folio = ?
			", [
				$empresa ?: ($trab['empresa']    ?? ''),
				$email   ?: ($trab['email_trab'] ?? ''),
				$celular ?: ($trab['celular']    ?? ''),
				$cp      ?: ($trab['cp']         ?? ''),
				$folio,
			]);
		}

		$this->db->query("
			UPDATE produccion SET estatus_validacion = 0 WHERE folio = ?
		", [$folio]);

		$existe_captura = $this->db->query("
			SELECT id FROM capturas_app WHERE folio = ? LIMIT 1
		", [$folio])->row_array();

		$estatus_app = $en_temporales ? 'TEMPORALES' : 'CAPTURADO';

		if ($existe_captura) {
			$this->db->query("
				UPDATE capturas_app SET
					no_empleado     = ?,
					id_usuario_cap  = ?,
					clave_usuario   = ?,
					afo_cedente     = ?,
					acompanamiento  = ?,
					en_temporales   = ?,
					estatus         = ?,
					fecha_captura   = NOW()
				WHERE folio = ?
			", [$no_empleado, $id_usuario_cap, $clave_usuario, $afo_cedente, $acompanamiento, $en_temporales ? 1 : 0, $estatus_app, $folio]);
		} else {
			$this->db->query("
				INSERT INTO capturas_app (
					folio, no_empleado, id_usuario_cap, clave_usuario,
					afo_cedente, acompanamiento, en_temporales, estatus
				) VALUES (?, ?, ?, ?, ?, ?, ?, ?)
			", [$folio, $no_empleado, $id_usuario_cap, $clave_usuario, $afo_cedente, $acompanamiento, $en_temporales ? 1 : 0, $estatus_app]);
		}

		// ─── Notificación push al usuario capturista ──────────────────────────
		require_once FCPATH . 'send_push.php';

		$token_row = $this->db->query("
			SELECT token FROM push_tokens WHERE no_empleado = ? LIMIT 1
		", [$id_usuario_cap])->row_array();

		if ($token_row && !empty($token_row['token'])) {
			$msg = $en_temporales
				? "Folio {$folio} guardado en temporales"
				: "Folio {$folio} capturado correctamente";

			enviar_push($token_row['token'], 'Carobra HelpiA', $msg, [
				'folio' => $folio,
			]);
		}

		echo json_encode([
			'success'          => true,
			'en_temporales'    => $en_temporales,
			'acomp_bloqueado'  => (float)$acompanamiento > 0 && !$puede_acompañar,
			'message'          => $en_temporales
				? 'Captura guardada en temporales correctamente'
				: 'Traspaso guardado correctamente',
		]);
	}

	// GET /api/dashboard-stats
	public function dashboard_stats() {
		$id_nivel    = $this->input->get('id_nivel')    ?? '';
		$no_empleado = $this->input->get('no_empleado') ?? '';
		$id_gerencia = $this->input->get('id_gerencia') ?? '';

		$hoy = date('Y-m-d');

		if ($id_nivel == '12') {
			$sql = "
				SELECT COUNT(*) as total
				FROM capturas_app ca
				INNER JOIN produccion p ON ca.folio = p.folio
				WHERE DATE(ca.fecha_captura) = ?
				AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
			";
			$params = [$hoy];

		} elseif ($id_nivel == '3') {
			$sql = "
				SELECT COUNT(*) as total
				FROM capturas_app ca
				INNER JOIN produccion p ON ca.folio = p.folio
				WHERE DATE(ca.fecha_captura) = ?
				AND ca.id_usuario_cap = ?
				AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
			";
			$params = [$hoy, $no_empleado];

		} else {
			$filtro = $this->obtener_filtro_estructura($id_gerencia);
			$campo  = $filtro['campo'];
			$valor  = $filtro['valor'];

			$sql = "
				SELECT COUNT(*) as total
				FROM capturas_app ca
				INNER JOIN produccion p ON ca.folio = p.folio
				WHERE DATE(ca.fecha_captura) = ?
				AND ca.id_usuario_cap IN (
					SELECT no_empleado FROM estruc_empleados WHERE {$campo} = ?
				)
				AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
			";
			$params = [$hoy, $valor];
		}

		$total = (int) $this->db->query($sql, $params)->row_array()['total'];

		echo json_encode([
			'success' => true,
			'data'    => [
				'traspasos_hoy' => $total
			]
		]);
	}

	// GET /api/historial-traspasos?q=&fecha=&id_nivel=&no_empleado=&id_gerencia=
	public function historial_traspasos() {
		$q           = $this->input->get('q')           ?? '';
		$fecha       = $this->input->get('fecha')       ?? '';
		$id_nivel    = $this->input->get('id_nivel')    ?? '';
		$no_empleado = $this->input->get('no_empleado') ?? '';
		$id_gerencia = $this->input->get('id_gerencia') ?? '';

		$sql = "
			SELECT
				ca.folio,
				ca.no_empleado,
				ca.afo_cedente,
				ca.acompanamiento,
				ca.en_temporales,
				ca.estatus,
				p.estatus_validacion,
				ca.fecha_captura,
				pt.trabajador,
				eu.nombre AS nombre_comisionista
			FROM capturas_app ca
			INNER JOIN produccion p ON ca.folio = p.folio
			LEFT JOIN prod_trabajador pt ON ca.folio = pt.folio
			LEFT JOIN empl_usuarios eu ON eu.no_empleado = ca.no_empleado
			WHERE STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
			AND p.id_estatus_prod != 6
		";
		$params = [];

		if ($id_nivel == '12') {
			// sin filtro extra
		} elseif ($id_nivel == '3') {
			$sql .= " AND ca.id_usuario_cap = ? ";
			$params[] = $no_empleado;
		} else {
			$filtro = $this->obtener_filtro_estructura($id_gerencia);
			$campo  = $filtro['campo'];
			$valor  = $filtro['valor'];

			$sql .= " AND ca.id_usuario_cap IN (
				SELECT no_empleado FROM estruc_empleados WHERE {$campo} = ?
			) ";
			$params[] = $valor;
		}

		if ($q) {
			$sql .= " AND (ca.folio LIKE ? OR pt.trabajador LIKE ?) ";
			$params[] = '%' . $q . '%';
			$params[] = '%' . $q . '%';
		}

		if ($fecha) {
			$sql .= " AND DATE(ca.fecha_captura) = ? ";
			$params[] = $fecha;
		}

		$sql .= " ORDER BY ca.fecha_captura DESC LIMIT 50";

		$query = $this->db->query($sql, $params);

		echo json_encode([
			'success' => true,
			'data'    => $query->result_array()
		]);
	}

	// GET /api/pendientes
	public function pendientes() {
		$id_nivel    = $this->input->get('id_nivel')    ?? '';
		$id_gerencia = $this->input->get('id_gerencia') ?? '';

		if ($id_nivel == '3') {
			echo json_encode(['success' => true, 'data' => []]);
			return;
		}

		$base = "
			SELECT pt.folio, pt.trabajador, p.nss, p.nap, p.fec_traspaso,
				CONCAT(p.nap, ' | ', eu.nombre) AS nap_nombre
			FROM naps n
			LEFT JOIN empl_usuarios eu ON n.no_empleado = eu.no_empleado
			LEFT JOIN estruc_empleados ee ON eu.no_empleado = ee.no_empleado
			INNER JOIN produccion p ON n.nap = p.nap
			INNER JOIN prod_trabajador pt ON pt.folio = p.folio
			WHERE p.no_empleado = 0
			AND n.baja = 0
			AND p.cierre_cap = 0
			AND p.id_estatus_prod != 6
			AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
		";

		if ($id_nivel == '12') {
			$sql = $base . " ORDER BY p.fec_traspaso DESC LIMIT 50 ";
			$params = [];

		} else {
			$filtro = $this->obtener_filtro_estructura($id_gerencia);
			$campo  = $filtro['campo'];
			$valor  = $filtro['valor'];

			$sql = $base . " AND ee.{$campo} = ? ORDER BY p.fec_traspaso DESC LIMIT 50 ";
			$params = [$valor];
		}

		$query = $this->db->query($sql, $params);

		echo json_encode([
			'success' => true,
			'data'    => $query->result_array()
		]);
	}

	// GET /api/folios-incompletos
	public function folios_incompletos() {
		$id_nivel    = $this->input->get('id_nivel')    ?? '';
		$no_empleado = $this->input->get('no_empleado') ?? '';
		$id_gerencia = $this->input->get('id_gerencia') ?? '';

		$base = "
			SELECT
				p.folio,
				pt.trabajador,
				p.no_empleado,
				p.afo_cedente,
				p.estatus_validacion,
				(SELECT COUNT(*) FROM evidencias e WHERE e.folio = p.folio) AS total_evidencias
			FROM produccion p
			INNER JOIN prod_trabajador pt ON pt.folio = p.folio
			WHERE p.id_estatus_prod != 6
			AND STR_TO_DATE(p.fec_traspaso, '%d/%m/%Y') >= DATE_SUB(CURDATE(), INTERVAL 2 MONTH)
			AND (
				p.no_empleado = 0
				OR p.afo_cedente IS NULL
				OR p.afo_cedente = ''
				OR (SELECT COUNT(*) FROM evidencias e WHERE e.folio = p.folio) = 0
			)
		";

		if ($id_nivel == '12') {
			$sql = $base . " ORDER BY p.fec_traspaso DESC LIMIT 50 ";
			$params = [];

		} elseif ($id_nivel == '3') {
			$sql = $base . " AND p.no_empleado = ? ORDER BY p.fec_traspaso DESC LIMIT 50 ";
			$params = [$no_empleado];

		} else {
			$filtro = $this->obtener_filtro_estructura($id_gerencia);
			$campo  = $filtro['campo'];
			$valor  = $filtro['valor'];

			$sql = $base . " AND (
				p.no_empleado = 0
				OR p.no_empleado IN (SELECT no_empleado FROM estruc_empleados WHERE {$campo} = ?)
			) ORDER BY p.fec_traspaso DESC LIMIT 50 ";
			$params = [$valor];
		}

		$query = $this->db->query($sql, $params);

		echo json_encode([
			'success' => true,
			'data'    => $query->result_array()
		]);
	}


	// POST /api/guardar-token-push
	public function guardar_token_push() {
		$json = $this->input->raw_input_stream;
		$data = json_decode($json, true);

		$token       = $data['token']       ?? '';
		$clave       = $data['clave']       ?? '';
		$no_empleado = $data['no_empleado'] ?? '';

		if (!$token || !$no_empleado) {
			echo json_encode(['success' => false]);
			return;
		}

		$this->db->query("
			INSERT INTO push_tokens (no_empleado, clave, token)
			VALUES (?, ?, ?)
			ON DUPLICATE KEY UPDATE token = ?, actualizado_en = NOW()
		", [$no_empleado, $clave, $token, $token]);

		

		echo json_encode(['success' => true]);
	}


	// GET /api/spartan-folios?q=
	// GET /api/spartan-folios?q=
	public function spartan_folios() {
		$q           = $this->input->get('q')           ?? '';
		$no_empleado = $this->input->get('no_empleado') ?? '';

		// Verificar que sea Spartan
		$spartan = $this->db->query("
			SELECT eu.clave FROM empl_usuarios eu
			INNER JOIN spartan_usuarios su ON eu.clave = su.clave
			WHERE eu.no_empleado = ? LIMIT 1
		", [$no_empleado])->row_array();

		if (!$spartan) {
			echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
			return;
		}

		$hoy      = date('d/m/Y');
		$limite   = date('d/m/Y', strtotime('+3 days'));

		$sql = "
			SELECT
				cb.folio,
				cb.nombre_Completo  AS trabajador,
				cb.nap,
				cb.fec_traspaso,
				cb.fec_tramite,
				cb.saldo,
				cb.afo_receptora,
				cb.afo_cedente,
				cb.estado,
				cb.antiguedad,
				cb.semana,
				cb.mes_procesar,
				cb.cancelacion,
				cb.fec_alta,
				cb.sol_traspaso,
				cb.pplus,
				cb.ley,
				pt.empresa,
				pt.celular,
				pt.email_trab,
				pt.CURP,
				pt.cp,
				p.nss,
				p.no_empleado,
				p.id_estatus_prod,
				af_ced.afore  AS nombre_afo_cedente,
				af_rec.afore  AS nombre_afo_receptora,
				eu.nombre     AS nombre_comisionista,
				eu.clave      AS clave_comisionista,
				ee.sc,
				ee.brocker,
				ee.gerencia,
				ee.id_gerencia
			FROM clawback cb
			INNER JOIN produccion p ON p.folio = cb.folio
			LEFT JOIN prod_trabajador pt ON pt.folio = cb.folio
			LEFT JOIN empl_usuarios eu ON eu.no_empleado = p.no_empleado
			LEFT JOIN estruc_empleados ee ON ee.no_empleado = p.no_empleado
			LEFT JOIN afores af_ced ON af_ced.clave = cb.afo_cedente
			LEFT JOIN afores af_rec ON af_rec.clave = cb.afo_receptora
			WHERE cb.fec_tramite IS NOT NULL
			AND cb.fec_tramite != ''
			AND STR_TO_DATE(cb.fec_tramite, '%d/%m/%Y') >= STR_TO_DATE(?, '%d/%m/%Y')
			AND STR_TO_DATE(cb.fec_tramite, '%d/%m/%Y') <= STR_TO_DATE(?, '%d/%m/%Y')
			ORDER BY STR_TO_DATE(cb.fec_tramite, '%d/%m/%Y') ASC LIMIT 100
		";
		$params = [$hoy, $limite];

		if (strlen(trim($q)) >= 2) {
			$sql .= " AND (cb.folio LIKE ? OR cb.nombre_Completo LIKE ? OR p.nss LIKE ?) ";
			$params[] = '%' . $q . '%';
			$params[] = '%' . $q . '%';
			$params[] = '%' . $q . '%';
		}

		$sql .= " ORDER BY STR_TO_DATE(cb.fec_tramite, '%d/%m/%Y') ASC LIMIT 100";

		$query = $this->db->query($sql, $params);

		echo json_encode([
			'success' => true,
			'data'    => $query->result_array()
		]);
	}

	// POST /api/spartan-recuperar
	public function spartan_recuperar() {
		$json        = $this->input->raw_input_stream;
		$data        = json_decode($json, true);
		$folio       = $data['folio']       ?? '';
		$no_empleado = $data['no_empleado'] ?? '';

		// Verificar Spartan
		$spartan = $this->db->query("
			SELECT eu.clave FROM empl_usuarios eu
			INNER JOIN spartan_usuarios su ON eu.clave = su.clave
			WHERE eu.no_empleado = ? LIMIT 1
		", [$no_empleado])->row_array();

		if (!$spartan) {
			echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
			return;
		}

		if (!$folio) {
			echo json_encode(['success' => false, 'message' => 'Folio requerido']);
			return;
		}

		// Verificar que el folio existe en clawback
		$cb = $this->db->query("
			SELECT folio FROM clawback WHERE folio = ? LIMIT 1
		", [$folio])->row_array();

		if (!$cb) {
			echo json_encode(['success' => false, 'message' => 'Folio no encontrado en clawback']);
			return;
		}

		// Verificar que existe en produccion
		$prod = $this->db->query("
			SELECT folio, id_estatus_prod FROM produccion WHERE folio = ? LIMIT 1
		", [$folio])->row_array();

		if (!$prod) {
			echo json_encode(['success' => false, 'message' => 'Folio no encontrado en producción']);
			return;
		}

		// Recuperar: cambiar estatus a 1
		$this->db->query("
			UPDATE produccion SET id_estatus_prod = 1 WHERE folio = ?
		", [$folio]);

		// Limpiar rechazos si existen
		$this->db->query("
			DELETE FROM prod_rechazos WHERE folio = ?
		", [$folio]);

		echo json_encode([
			'success' => true,
			'message' => 'Folio recuperado correctamente'
		]);
	}

	// POST /api/spartan-reasignar
	public function spartan_reasignar() {
		$json        = $this->input->raw_input_stream;
		$data        = json_decode($json, true);
		$folio          = $data['folio']          ?? '';
		$no_empleado    = $data['no_empleado']    ?? '';
		$nuevo_empleado = $data['nuevo_empleado'] ?? '';

		// Verificar Spartan
		$spartan = $this->db->query("
			SELECT eu.clave FROM empl_usuarios eu
			INNER JOIN spartan_usuarios su ON eu.clave = su.clave
			WHERE eu.no_empleado = ? LIMIT 1
		", [$no_empleado])->row_array();

		if (!$spartan) {
			echo json_encode(['success' => false, 'message' => 'Acceso no autorizado']);
			return;
		}

		if (!$folio || !$nuevo_empleado) {
			echo json_encode(['success' => false, 'message' => 'Datos incompletos']);
			return;
		}

		$this->db->query("
			UPDATE produccion SET no_empleado = ? WHERE folio = ?
		", [$nuevo_empleado, $folio]);

		echo json_encode(['success' => true, 'message' => 'Comisionista reasignado correctamente']);
	}
}
