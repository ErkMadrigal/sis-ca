<?php
/* ════════════════════════════════════════════════════════════
   CARGA SEPOMEX — endpoint web de un solo uso
   ────────────────────────────────────────────────────────────
   Acceso: cargar_sepomex_web.php?clave=NAP2026sepomex

   Procesa los chunks (chunk_000.csv ... chunk_079.csv) uno a la
   vez vía AJAX, así nunca se topa con el límite de tiempo del
   hosting. Cada chunk tiene ~2000 filas.

   IMPORTANTE: borra este archivo y la carpeta /chunks del
   servidor en cuanto termines la carga. No tiene autenticación
   real, solo una clave simple en la URL.
════════════════════════════════════════════════════════════ */

$CLAVE_ACCESO = 'NAP2026sepomex';   // cámbiala si quieres, antes de subir

$accion = $_GET['accion'] ?? $_POST['accion'] ?? '';

/* ── Endpoint AJAX: procesa un chunk ── */
if ($accion === 'procesar_chunk') {
    header('Content-Type: application/json');

    if (($_POST['clave'] ?? '') !== $CLAVE_ACCESO) {
        echo json_encode(['ok' => false, 'msg' => 'Clave incorrecta.']);
        exit;
    }

    require_once __DIR__ . '/../funciones/encripta.php';

    $numChunk = intval($_POST['chunk'] ?? -1);
    $archivo  = __DIR__ . "/chunks/chunk_" . sprintf('%03d', $numChunk) . ".csv";

    if (!file_exists($archivo)) {
        echo json_encode(['ok' => false, 'msg' => "Archivo no encontrado: chunk_{$numChunk}"]);
        exit;
    }

    $conecta = new conector();
    $conecta->conexion->set_charset('utf8mb4');

    $fh = fopen($archivo, 'r');
    $header = fgetcsv($fh); // saltar encabezado

    $valores = [];
    $insertadas = 0;

    while (($fila = fgetcsv($fh)) !== false) {
        if (count($fila) < 5) continue;

        [$d_codigo, $d_asenta, $d_mnpio, $id_estado, $d_estado] = $fila;

        /* La tabla sepomex se creó como utf8mb4 (no latin1 como el resto
           del sistema viejo), así que el texto UTF-8 del CSV va directo,
           sin utf8_decode(). */
        $d_codigo  = mysqli_real_escape_string($conecta->conexion, trim($d_codigo));
        $d_asenta  = mysqli_real_escape_string($conecta->conexion, trim($d_asenta));
        $d_mnpio   = mysqli_real_escape_string($conecta->conexion, trim($d_mnpio));
        $id_estado = intval($id_estado);
        $d_estado  = mysqli_real_escape_string($conecta->conexion, trim($d_estado));

        $valores[] = "('{$d_codigo}','{$d_asenta}','{$d_mnpio}',{$id_estado},'{$d_estado}')";
    }
    fclose($fh);

    if (!empty($valores)) {
        $sql = "INSERT INTO carobra_2014.sepomex (d_codigo, d_asenta, d_mnpio, id_estado, d_estado) VALUES "
             . implode(',', $valores);
        $ok = $conecta->consulta($sql);

        if ($ok === false) {
            echo json_encode(['ok' => false, 'msg' => 'Error SQL: ' . $conecta->conexion->error]);
            exit;
        }
        $insertadas = count($valores);
    }

    echo json_encode(['ok' => true, 'insertadas' => $insertadas, 'chunk' => $numChunk]);
    exit;
}

/* ── Endpoint AJAX: verificar/truncar tabla antes de iniciar ── */
if ($accion === 'verificar') {
    header('Content-Type: application/json');

    if (($_POST['clave'] ?? '') !== $CLAVE_ACCESO) {
        echo json_encode(['ok' => false, 'msg' => 'Clave incorrecta.']);
        exit;
    }

    require_once __DIR__ . '/../funciones/encripta.php';

    $conecta = new conector();
    $rs  = $conecta->consulta("SELECT COUNT(*) as cnt FROM carobra_2014.sepomex");
    $row = mysqli_fetch_assoc($rs);

    echo json_encode(['ok' => true, 'existentes' => intval($row['cnt'])]);
    exit;
}

if ($accion === 'truncar') {
    header('Content-Type: application/json');

    if (($_POST['clave'] ?? '') !== $CLAVE_ACCESO) {
        echo json_encode(['ok' => false, 'msg' => 'Clave incorrecta.']);
        exit;
    }

    require_once __DIR__ . '/../funciones/encripta.php';

    $conecta = new conector();
    $conecta->consulta("TRUNCATE TABLE carobra_2014.sepomex");

    echo json_encode(['ok' => true]);
    exit;
}

/* Verificar clave para ver la página */
$claveOk = ($_GET['clave'] ?? '') === $CLAVE_ACCESO;
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Carga SEPOMEX</title>
<style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background:#f4f6f8; margin:0; padding:40px 20px; }
    .card { max-width:560px; margin:0 auto; background:#fff; border-radius:12px; box-shadow:0 4px 18px rgba(0,0,0,.08); padding:32px; }
    h1 { font-size:18px; color:#1a1a2e; margin:0 0 6px; }
    p.sub { color:#888; font-size:13px; margin:0 0 24px; }
    .barra-wrap { background:#eef2f4; border-radius:8px; height:28px; overflow:hidden; margin-bottom:10px; }
    .barra { background:linear-gradient(135deg,#2B73B6,#1a5a96); height:100%; width:0%; transition:width .3s; display:flex; align-items:center; justify-content:center; color:#fff; font-size:12px; font-weight:600; }
    .stats { display:flex; justify-content:space-between; font-size:12.5px; color:#666; margin-bottom:20px; }
    button { background:#2B73B6; color:#fff; border:none; padding:11px 22px; border-radius:7px; font-size:13px; font-weight:600; cursor:pointer; width:100%; }
    button:hover { background:#1a5a96; }
    button:disabled { background:#aaa; cursor:not-allowed; }
    .log { background:#0f1117; color:#9fd4e8; font-family:monospace; font-size:11.5px; padding:14px; border-radius:8px; max-height:200px; overflow-y:auto; margin-top:16px; white-space:pre-wrap; }
    .denied { color:#c0392b; text-align:center; padding:40px 0; }
    .aviso { background:#fff3cd; border:1px solid #ffe69c; color:#856404; padding:10px 14px; border-radius:6px; font-size:12.5px; margin-bottom:18px; }
</style>
</head>
<body>
<div class="card">

<?php if (!$claveOk) { ?>
    <div class="denied">
        <h1>🔒 Acceso restringido</h1>
        <p class="sub">Agrega <code>?clave=...</code> a la URL para continuar.</p>
    </div>
<?php } else { ?>

    <h1>📦 Carga catálogo SEPOMEX</h1>
    <p class="sub">158,504 registros en 80 lotes. No cierres esta pestaña mientras corre.</p>

    <div class="aviso" id="aviso-existentes" style="display:none;"></div>

    <div class="barra-wrap">
        <div class="barra" id="barra">0%</div>
    </div>
    <div class="stats">
        <span id="stat-lote">Lote 0 / 80</span>
        <span id="stat-insertadas">0 registros insertados</span>
    </div>

    <button id="btn-iniciar" onclick="iniciarCarga()">Iniciar carga</button>

    <div class="log" id="log">Esperando inicio...</div>

</div>

<script>
const CLAVE   = <?php echo json_encode($CLAVE_ACCESO); ?>;
const URL     = window.location.pathname;
const TOTAL_CHUNKS = 80;

let chunkActual   = 0;
let totalInsertadas = 0;

function log(msg) {
    const el = document.getElementById('log');
    el.textContent += '\n' + msg;
    el.scrollTop = el.scrollHeight;
}

async function verificarExistentes() {
    const resp = await fetch(URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=verificar&clave=' + encodeURIComponent(CLAVE)
    });
    const data = await resp.json();
    if (data.ok && data.existentes > 0) {
        const aviso = document.getElementById('aviso-existentes');
        aviso.style.display = 'block';
        aviso.innerHTML = `⚠️ La tabla ya tiene <strong>${data.existentes}</strong> registros. ` +
            `<a href="#" onclick="truncarYIniciar(event)" style="color:#856404;text-decoration:underline;">Borrar todo y recargar desde cero</a>`;
    }
}

async function truncarYIniciar(e) {
    e.preventDefault();
    if (!confirm('¿Seguro que quieres BORRAR todos los registros de sepomex y recargar desde cero?')) return;
    log('Truncando tabla...');
    const resp = await fetch(URL, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'accion=truncar&clave=' + encodeURIComponent(CLAVE)
    });
    const data = await resp.json();
    if (data.ok) {
        log('Tabla truncada. Lista para cargar.');
        document.getElementById('aviso-existentes').style.display = 'none';
    } else {
        log('ERROR al truncar: ' + data.msg);
    }
}

async function iniciarCarga() {
    document.getElementById('btn-iniciar').disabled = true;
    document.getElementById('btn-iniciar').textContent = 'Cargando...';
    log('Iniciando carga...');
    procesarSiguienteChunk();
}

async function procesarSiguienteChunk() {
    if (chunkActual >= TOTAL_CHUNKS) {
        log('\n✅ CARGA TERMINADA. Total insertadas: ' + totalInsertadas);
        document.getElementById('btn-iniciar').textContent = 'Completado ✓';
        document.getElementById('barra').style.width = '100%';
        document.getElementById('barra').textContent = '100%';
        return;
    }

    try {
        const resp = await fetch(URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `accion=procesar_chunk&clave=${encodeURIComponent(CLAVE)}&chunk=${chunkActual}`
        });
        const data = await resp.json();

        if (data.ok) {
            totalInsertadas += data.insertadas;
            chunkActual++;

            const pct = Math.round((chunkActual / TOTAL_CHUNKS) * 100);
            document.getElementById('barra').style.width = pct + '%';
            document.getElementById('barra').textContent = pct + '%';
            document.getElementById('stat-lote').textContent = `Lote ${chunkActual} / ${TOTAL_CHUNKS}`;
            document.getElementById('stat-insertadas').textContent = `${totalInsertadas} registros insertados`;

            log(`Lote ${chunkActual}/${TOTAL_CHUNKS} OK (+${data.insertadas})`);

            procesarSiguienteChunk();
        } else {
            log('ERROR en lote ' + chunkActual + ': ' + data.msg);
            document.getElementById('btn-iniciar').disabled = false;
            document.getElementById('btn-iniciar').textContent = 'Reintentar';
        }
    } catch (e) {
        log('ERROR de conexión: ' + e.message);
        document.getElementById('btn-iniciar').disabled = false;
        document.getElementById('btn-iniciar').textContent = 'Reintentar';
    }
}

verificarExistentes();
</script>

<?php } ?>
</body>
</html>