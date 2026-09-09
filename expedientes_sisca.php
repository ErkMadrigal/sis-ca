<?php session_start(); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
  <title>CAROBRA — Expedientes Digitales</title>
  <link rel="shortcut icon" href="./img/favicon.ico">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" crossorigin="anonymous">
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" crossorigin="anonymous"></script>
  <link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
  <script src="./js/carobra.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

  <style>
    #contenido, #contenido * { text-align: left; }

    .exp-wrap { display: flex; height: calc(100vh - 120px); overflow: hidden; }

    /* ── Panel izquierdo ── */
    .exp-sidebar {
      width: 340px;
      flex-shrink: 0;
      border-right: 1px solid #e4eaee;
      display: flex;
      flex-direction: column;
      background: #f8fafc;
    }

    .exp-sidebar-top {
      padding: 12px 14px;
      border-bottom: 1px solid #e4eaee;
      background: #fff;
    }

    .exp-search {
      width: 100%;
      box-sizing: border-box;
      border: 1px solid #d0d0d0;
      border-radius: 20px;
      padding: 8px 14px 8px 36px;
      font-size: 13px;
      background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' fill='%23999' viewBox='0 0 16 16'%3E%3Cpath d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0z'/%3E%3C/svg%3E") no-repeat 12px center;
      outline: none;
      margin-bottom: 8px;
    }

    .exp-pills { display: flex; gap: 6px; }
    .exp-pill {
      padding: 4px 12px;
      border-radius: 14px;
      font-size: 11.5px;
      font-weight: 600;
      cursor: pointer;
      border: 1px solid #dde8ee;
      background: #f4f7f9;
      color: #5a7385;
    }
    .exp-pill.activa { background: #2B73B6; color: #fff; border-color: #2B73B6; }

    .exp-list { flex: 1; overflow-y: auto; padding: 8px; }
    .exp-list::-webkit-scrollbar { width: 6px; }
    .exp-list::-webkit-scrollbar-thumb { background: #c0d4e0; border-radius: 3px; }

    .exp-item {
      background: #fff;
      border: 1px solid #e4eaee;
      border-radius: 10px;
      padding: 10px 12px;
      margin-bottom: 6px;
      cursor: pointer;
      transition: box-shadow .15s, border-color .15s;
    }
    .exp-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,.06); }
    .exp-item.activo { border-color: #2B73B6; background: #f0f6fb; }

    .exp-item-nombre {
      font-size: 13px;
      font-weight: 700;
      color: #1a1a2e;
      white-space: nowrap;
      overflow: hidden;
      text-overflow: ellipsis;
    }

    .exp-item-meta {
      font-size: 11px;
      color: #888;
      margin-top: 3px;
      display: flex;
      gap: 8px;
      align-items: center;
    }

    .exp-item-badges { display: flex; gap: 4px; margin-top: 5px; }
    .exp-bdg {
      font-size: 10px;
      font-weight: 700;
      padding: 2px 7px;
      border-radius: 10px;
    }
    .bdg-ok       { background: #e8f9ef; color: #1e8e58; }
    .bdg-pend     { background: #fff8e6; color: #856404; }
    .bdg-rec      { background: #fdeeee; color: #c0392b; }
    .bdg-fisica   { background: #e8f0fb; color: #1a5276; }
    .bdg-moral    { background: #f3e8ff; color: #7b00c0; }

    /* ── Panel derecho ── */
    .exp-detail {
      flex: 1;
      overflow-y: auto;
      padding: 20px 24px;
      background: #fff;
    }

    .exp-detail-empty {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;
      height: 100%;
      color: #aaa;
      gap: 12px;
      font-size: 14px;
    }

    .exp-detail-header {
      display: flex;
      align-items: flex-start;
      gap: 14px;
      margin-bottom: 20px;
      padding-bottom: 16px;
      border-bottom: 1px solid #eef2f4;
    }

    .exp-avatar {
      width: 48px;
      height: 48px;
      border-radius: 14px;
      display: flex;
      align-items: center;
      justify-content: center;
      font-weight: 800;
      font-size: 18px;
      color: #fff;
      flex-shrink: 0;
    }

    .exp-detail-nombre { font-size: 18px; font-weight: 800; color: #1a1a2e; }
    .exp-detail-meta   { font-size: 12px; color: #888; margin-top: 3px; display: flex; gap: 12px; }

    .exp-info-grid {
      display: grid;
      grid-template-columns: repeat(3, 1fr);
      gap: 10px;
      margin-bottom: 20px;
    }

    .exp-info-item {
      background: #f8fafc;
      border: 1px solid #eef2f4;
      border-radius: 10px;
      padding: 10px 12px;
    }

    .exp-info-label {
      font-size: 10px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .04em;
      color: #95aab8;
      margin-bottom: 3px;
    }

    .exp-info-val {
      font-size: 13px;
      font-weight: 600;
      color: #1a1a2e;
    }

    .exp-docs-titulo {
      font-size: 11px;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: .05em;
      color: #2B73B6;
      margin-bottom: 12px;
      padding-bottom: 6px;
      border-bottom: 1px solid #e4eaee;
    }

    .exp-doc-item {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 14px;
      border: 1px solid #eef2f4;
      border-radius: 10px;
      margin-bottom: 8px;
      transition: box-shadow .15s;
    }
    .exp-doc-item:hover { box-shadow: 0 2px 8px rgba(0,0,0,.05); }

    .exp-doc-thumb {
      width: 44px;
      height: 44px;
      border-radius: 8px;
      overflow: hidden;
      flex-shrink: 0;
      background: #f0f4f8;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 22px;
      cursor: pointer;
    }

    .exp-doc-thumb img { width: 100%; height: 100%; object-fit: cover; }

    .exp-doc-info { flex: 1; min-width: 0; }
    .exp-doc-nombre { font-size: 13px; font-weight: 600; color: #1a1a2e; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .exp-doc-tipo   { font-size: 11px; color: #888; margin-top: 2px; }

    .exp-doc-estatus {
      font-size: 10.5px;
      font-weight: 700;
      padding: 3px 10px;
      border-radius: 12px;
      white-space: nowrap;
      flex-shrink: 0;
    }
    .est-pendiente { background: #fff8e6; color: #856404; }
    .est-aprobado  { background: #e8f9ef; color: #1e8e58; }
    .est-rechazado { background: #fdeeee; color: #c0392b; }

    .exp-doc-actions { display: flex; gap: 6px; flex-shrink: 0; }
    .exp-btn-doc {
      border: 1px solid #dde8ee;
      background: #fff;
      color: #5a7385;
      border-radius: 7px;
      padding: 5px 10px;
      font-size: 11.5px;
      font-weight: 600;
      cursor: pointer;
      transition: all .15s;
    }
    .exp-btn-doc:hover { background: #f0f6f9; }
    .exp-btn-aprobar  { border-color: #a8e0bd; color: #1e8e58; }
    .exp-btn-aprobar:hover  { background: #eafaf1; }
    .exp-btn-rechazar { border-color: #f3c4c4; color: #c0392b; }
    .exp-btn-rechazar:hover { background: #fdeeee; }

    .exp-doc-comentario {
      font-size: 11px;
      color: #c0392b;
      background: #fdeeee;
      border-radius: 6px;
      padding: 4px 8px;
      margin-top: 4px;
      display: inline-block;
    }

    /* Lightbox */
    #exp-lightbox {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(10,18,32,.88);
      z-index: 99999;
      justify-content: center;
      align-items: center;
      padding: 30px;
      cursor: zoom-out;
    }
    #exp-lightbox img { max-width: 90%; max-height: 90%; border-radius: 8px; }

    .exp-spinner {
      display: inline-block;
      width: 14px; height: 14px;
      border: 2px solid #ccc;
      border-top-color: #2B73B6;
      border-radius: 50%;
      animation: spin .7s linear infinite;
      vertical-align: -2px;
      margin-right: 6px;
    }
    @keyframes spin { to { transform: rotate(360deg); } }
  </style>
</head>

<?php include("./funciones/clas_carobra.php"); ?>
<body>

<div id="loader" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:9999; justify-content:center; align-items:center;">
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
    mensaje("Tu sesión ha expirado.", "index.php");
    $sesion_login = true;
  } else {
    $_SESSION["ultimoAcceso"] = $fa;
    $_SESSION["autentifica"]  = "p";
  }

  if($_SESSION["autentifica"] != "p"){
    session_destroy(); session_unset();
    mensaje("Tu sesión ha expirado.", "index.php");
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
      titulo("Expedientes Digitales");
      $pag = "PRODUCCION";
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
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: ".$_SESSION["nombre"]."</spam>";
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

<div class="container-fluid" style="padding:0;">

  <!-- Barra superior -->

  <div style="padding:10px 16px; background:#fff; border-bottom:1px solid #ddd; display:flex; align-items:center; gap:10px;">
    <input type="text" id="exp-busqueda" class="exp-search" style="margin:0; width:260px;"
          placeholder="Buscar por nombre o email...">
    <div class="exp-pills">
      <div class="exp-pill activa" data-filtro="TODOS">Todos</div>
      <div class="exp-pill" data-filtro="completo">Completos</div>
      <div class="exp-pill" data-filtro="incompleto">Incompletos</div>
    </div>

    <!-- BOTÓN NUEVO EXPEDIENTE -->
    <button onclick="abrirModalNuevo()"
            style="margin-left:auto; background:#2B73B6; color:#fff; border:none;
                  border-radius:8px; padding:7px 16px; font-size:12px;
                  font-weight:700; cursor:pointer;">
      + Nuevo expediente
    </button>
  </div>

  <!-- Layout principal -->
  <div class="exp-wrap">

    <!-- Lista -->
    <div class="exp-sidebar">
      <div class="exp-list" id="exp-list">
        <div style="text-align:center; padding:40px; color:#aaa;">
          <span class="exp-spinner"></span> Cargando...
        </div>
      </div>
    </div>

    <!-- Detalle -->
    <div class="exp-detail" id="exp-detail">
      <div class="exp-detail-empty">
        <div style="font-size:48px;">📁</div>
        <p>Selecciona un expediente para revisarlo</p>
      </div>
    </div>

  </div>

</div>

<!-- Lightbox -->
<div id="exp-lightbox" onclick="cerrarLightbox()">
  <img id="exp-lightbox-img" src="" alt="">
</div>

<?php
    $divcontenido->close();
    $divcontenedor->close();
    $divpie->close();
?>

<script language="javascript">
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

<script>
const API = 'expedientes_sisca_api.php';

const LABELS_DOC = {
  ine_frente:                 'INE (frente)',
  ine_reverso:                'INE (reverso)',
  curp:                       'CURP',
  situacion_fiscal:           'Constancia fiscal',
  comprobante_domicilio:      'Comprobante domicilio',
  estado_cuenta:              'Estado de cuenta',
  ine_representante:          'INE representante legal',
  comprobante_domicilio_fiscal:'Comprobante domicilio fiscal',
  estado_cuenta_moral:        'Estado de cuenta (razón social)',
  acta_constitutiva:          'Acta constitutiva',
  rfc_empresa:                'RFC empresa',
};

async function api(payload) {
  const r = await fetch(API, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify(payload),
  });
  return r.json();
}

let filtroActual = 'TODOS';
let timerBusq    = null;
let expActivo    = null;

// ── Init ──────────────────────────────────────────
document.addEventListener('DOMContentLoaded', () => {
  /* Sin esto, el layout (header/menú/contenedor) no se recalcula
     correctamente y la vista queda descuadrada, tal como en las
     demás páginas del sistema que sí lo llaman. */
  window.addEventListener('resize', centra_contenedor);
  centra_contenedor();

  cargarLista();

  document.getElementById('exp-busqueda').addEventListener('keyup', () => {
    clearTimeout(timerBusq);
    timerBusq = setTimeout(cargarLista, 350);
  });

  document.querySelectorAll('.exp-pill').forEach(p => {
    p.addEventListener('click', function() {
      document.querySelectorAll('.exp-pill').forEach(x => x.classList.remove('activa'));
      this.classList.add('activa');
      filtroActual = this.dataset.filtro;
      cargarLista();
    });
  });

  document.getElementById('salir').addEventListener('click', () => {
    window.location = 'https://www.sis-ca.com.mx:7863/';
  });
  document.getElementById('email').addEventListener('click', () => {
    window.open('http://webmail.carobra.com.mx/imp/login.php');
  });
});

// ── Lista ─────────────────────────────────────────
async function cargarLista() {
  const q = document.getElementById('exp-busqueda').value.trim();
  const lista = document.getElementById('exp-list');
  lista.innerHTML = '<div style="text-align:center;padding:40px;color:#aaa;"><span class="exp-spinner"></span> Cargando...</div>';

  const res = await api({ action: 'listarExpedientes', q, filtro: filtroActual });
  if (!res.ok) { lista.innerHTML = '<div style="text-align:center;padding:40px;color:#aaa;">Error al cargar</div>'; return; }

  const datos = res.datos || [];
  if (datos.length === 0) {
    lista.innerHTML = '<div style="text-align:center;padding:40px;color:#aaa;">Sin expedientes</div>';
    return;
  }

  lista.innerHTML = datos.map(e => {
    const ini = iniciales(e.nombre);
    const bg  = e.tipo_persona === 'moral' ? '#f3e8ff' : '#e8f0fb';
    const fg  = e.tipo_persona === 'moral' ? '#7b00c0' : '#1a5276';
    return `
      <div class="exp-item${expActivo == e.id ? ' activo' : ''}" onclick="verDetalle(${e.id})">
        <div style="display:flex;align-items:center;gap:10px;">
          <div style="width:36px;height:36px;border-radius:10px;background:${bg};color:${fg};
                      display:flex;align-items:center;justify-content:center;font-weight:800;font-size:13px;flex-shrink:0;">
            ${ini}
          </div>
          <div style="flex:1;min-width:0;">
            <div class="exp-item-nombre">${e.nombre}</div>
            <div class="exp-item-meta">
              <span>${e.tipo_persona === 'moral' ? '🏢 Moral' : '👤 Física'}</span>
              <span>${e.total_docs} doc(s)</span>
            </div>
          </div>
        </div>
        <div class="exp-item-badges" style="margin-top:6px;">
          ${e.docs_aprobados  > 0 ? `<span class="exp-bdg bdg-ok">✓ ${e.docs_aprobados} aprobado(s)</span>` : ''}
          ${e.docs_pendientes > 0 ? `<span class="exp-bdg bdg-pend">⏳ ${e.docs_pendientes} pendiente(s)</span>` : ''}
          ${e.docs_rechazados > 0 ? `<span class="exp-bdg bdg-rec">✗ ${e.docs_rechazados} rechazado(s)</span>` : ''}
          <span class="exp-bdg ${e.estatus === 'completo' ? 'bdg-ok' : 'bdg-pend'}">
            ${e.estatus === 'completo' ? '✅ Completo' : '⏳ Incompleto'}
          </span>
        </div>
      </div>
    `;
  }).join('');
}

// ── Detalle ───────────────────────────────────────
async function verDetalle(id) {
  expActivo = id;
  cargarLista();

  const detail = document.getElementById('exp-detail');
  detail.innerHTML = '<div style="text-align:center;padding:60px;color:#aaa;"><span class="exp-spinner"></span> Cargando expediente...</div>';

  const res = await api({ action: 'detalleExpediente', id });
  if (!res.ok) { detail.innerHTML = '<div style="padding:40px;color:#c0392b;">Error al cargar.</div>'; return; }

  const e    = res.exp;
  const docs = res.docs || [];
  const bg   = e.tipo_persona === 'moral' ? '#f3e8ff' : '#e8f0fb';
  const fg   = e.tipo_persona === 'moral' ? '#7b00c0' : '#1a5276';

  const totalDocs = docs.length;
  const aprobados = docs.filter(d => d.estatus === 'aprobado').length;
  const pct       = totalDocs > 0 ? Math.round((aprobados / totalDocs) * 100) : 0;

  detail.innerHTML = `
    <div class="exp-detail-header">
      <div class="exp-avatar" style="background:${fg};">${iniciales(e.nombre)}</div>
      <div style="flex:1;">
        <div class="exp-detail-nombre">${e.nombre}</div>
        <div class="exp-detail-meta">
          <span>${e.tipo_persona === 'moral' ? '🏢 Persona moral' : '👤 Persona física'}</span>
          ${e.email   ? `<span>✉️ ${e.email}</span>`   : ''}
          ${e.celular ? `<span>📱 ${e.celular}</span>` : ''}
          ${e.rfc     ? `<span>RFC: ${e.rfc}</span>`   : ''}
          <span>${e.factura == 1 ? '🧾 Factura: Sí' : 'Sin factura'}</span>
        </div>
        <div style="margin-top:8px;">
          <div style="display:flex;justify-content:space-between;font-size:11px;color:#888;margin-bottom:4px;">
            <span>Documentos aprobados</span><span>${aprobados}/${totalDocs} · ${pct}%</span>
          </div>
          <div style="height:6px;background:#eef2f4;border-radius:3px;overflow:hidden;">
            <div style="height:6px;background:#1e8e58;border-radius:3px;width:${pct}%;transition:width .4s;"></div>
          </div>
        </div>

        <!-- Clave SISCA -->
        <div style="margin-top:12px; display:flex; align-items:center; gap:10px;">
          ${e.clave_sisca
            ? `<span style="font-size:12px;font-weight:700;padding:4px 12px;border-radius:8px;background:#e8f9ef;color:#1e8e58;">
                ✓ Clave SISCA: ${e.clave_sisca}
               </span>
               <button onclick="editarClave(${e.id}, '${e.clave_sisca}')"
                       style="border:1px solid #dde8ee;background:#fff;color:#5a7385;border-radius:7px;
                              padding:4px 10px;font-size:11px;font-weight:600;cursor:pointer;">
                 Cambiar
               </button>`
            : `<input id="inp-clave-sisca" type="text" placeholder="Clave SISCA del empleado"
                      style="border:1px solid #d0d0d0;border-radius:8px;padding:6px 12px;
                             font-size:12px;width:180px;outline:none;">
               <button onclick="asignarClaveSisca(${e.id})"
                       style="background:#2B73B6;color:#fff;border:none;border-radius:8px;
                              padding:6px 14px;font-size:12px;font-weight:700;cursor:pointer;">
                 Asignar
               </button>`
          }
        </div>

        <!-- Botones acción -->
        <div style="margin-top:10px; display:flex; gap:8px; flex-wrap:wrap;">
          <button onclick="abrirEdicion(${e.id})"
                  style="border:1px solid #2B73B6;background:#fff;color:#2B73B6;border-radius:8px;
                         padding:5px 12px;font-size:11.5px;font-weight:700;cursor:pointer;">
            ✏️ Editar datos
          </button>
          <button onclick="generarQR(${e.id})"
                  style="background:#1a5276;color:#fff;border:none;border-radius:8px;
                         padding:5px 12px;font-size:11.5px;font-weight:700;cursor:pointer;">
            📲 Generar QR / Link
          </button>
        </div>

      </div>
      <div>
        <span style="font-size:11px;font-weight:700;padding:4px 12px;border-radius:12px;
                     background:${e.estatus==='completo'?'#e8f9ef':'#fff8e6'};
                     color:${e.estatus==='completo'?'#1e8e58':'#856404'};">
          ${e.estatus === 'completo' ? '✅ Completo' : '⏳ Incompleto'}
        </span>
      </div>
    </div>

    <!-- Formulario edición (oculto por defecto) -->
    <div id="form-edicion-${e.id}" style="display:none; background:#f8fafc; border:1px solid #e4eaee;
         border-radius:10px; padding:16px; margin-bottom:16px;">
      <p style="font-size:12px;font-weight:700;color:#2B73B6;margin-bottom:12px;">✏️ Editar datos del expediente</p>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
        <div>
          <label style="font-size:11px;color:#888;">Nombre *</label>
          <input id="edit-nombre-${e.id}" type="text" value="${e.nombre}"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;box-sizing:border-box;margin-top:3px;">
        </div>
        <div>
          <label style="font-size:11px;color:#888;">Email</label>
          <input id="edit-email-${e.id}" type="email" value="${e.email || ''}"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;box-sizing:border-box;margin-top:3px;">
        </div>
        <div>
          <label style="font-size:11px;color:#888;">Celular</label>
          <input id="edit-celular-${e.id}" type="text" value="${e.celular || ''}" maxlength="10"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;box-sizing:border-box;margin-top:3px;">
        </div>
        <div>
          <label style="font-size:11px;color:#888;">RFC</label>
          <input id="edit-rfc-${e.id}" type="text" value="${e.rfc || ''}" maxlength="13"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:6px;padding:6px 10px;font-size:12px;outline:none;box-sizing:border-box;margin-top:3px;">
        </div>
        <div style="display:flex;align-items:center;gap:8px;margin-top:8px;">
          <label style="font-size:11px;color:#888;">¿Factura?</label>
          <input id="edit-factura-${e.id}" type="checkbox" ${e.factura == 1 ? 'checked' : ''}
                 style="width:16px;height:16px;cursor:pointer;">
        </div>
      </div>
      <div style="display:flex;gap:8px;margin-top:12px;">
        <button onclick="guardarEdicion(${e.id})"
                style="background:#2B73B6;color:#fff;border:none;border-radius:8px;
                       padding:7px 16px;font-size:12px;font-weight:700;cursor:pointer;">
          💾 Guardar
        </button>
        <button onclick="document.getElementById('form-edicion-${e.id}').style.display='none'"
                style="border:1px solid #dde8ee;background:#fff;color:#5a7385;border-radius:8px;
                       padding:7px 16px;font-size:12px;font-weight:600;cursor:pointer;">
          Cancelar
        </button>
      </div>
    </div>

    <div class="exp-docs-titulo">Documentos (${totalDocs})</div>
    ${docs.length === 0
      ? '<div style="color:#aaa;text-align:center;padding:30px;">Sin documentos subidos aún</div>'
      : docs.map(d => renderDoc(d)).join('')
    }
  `;
}

function abrirEdicion(id) {
  const form = document.getElementById(`form-edicion-${id}`);
  if (form) form.style.display = form.style.display === 'none' ? 'block' : 'none';
}

async function guardarEdicion(id) {
  const nombre  = document.getElementById(`edit-nombre-${id}`)?.value.trim();
  const email   = document.getElementById(`edit-email-${id}`)?.value.trim();
  const celular = document.getElementById(`edit-celular-${id}`)?.value.trim();
  const rfc     = document.getElementById(`edit-rfc-${id}`)?.value.trim();
  const factura = document.getElementById(`edit-factura-${id}`)?.checked ? 1 : 0;

  if (!nombre) { Swal.fire('Error', 'El nombre es requerido', 'warning'); return; }

  document.getElementById('loader').style.display = 'flex';
  const res = await api({ action: 'editarExpediente', id, nombre, email, celular, rfc, factura });
  document.getElementById('loader').style.display = 'none';

  if (res.ok) {
    Swal.fire({ icon: 'success', title: 'Guardado', timer: 1000, showConfirmButton: false });
    verDetalle(id);
  } else {
    Swal.fire('Error', res.msg || 'No se pudo guardar', 'error');
  }
}

function editarClave(id, claveActual) {
  Swal.fire({
    title: 'Cambiar clave SISCA',
    input: 'text',
    inputValue: claveActual,
    showCancelButton: true,
    confirmButtonText: 'Guardar',
    cancelButtonText: 'Cancelar',
  }).then(async result => {
    if (!result.isConfirmed) return;
    const clave = result.value.trim().toUpperCase();
    if (!clave) return;
    const res = await api({ action: 'asignarClaveSisca', id, clave_sisca: clave });
    if (res.ok) {
      Swal.fire({ icon: 'success', title: 'Clave actualizada', timer: 1000, showConfirmButton: false });
      verDetalle(id);
    }
  });
}

async function generarQR(id) {
  document.getElementById('loader').style.display = 'flex';
  const res = await api({ action: 'generarToken', id });
  document.getElementById('loader').style.display = 'none';

  if (!res.ok) { Swal.fire('Error', res.msg || 'No se pudo generar', 'error'); return; }

  const url = res.url;

  Swal.fire({
    title: '📲 Link del expediente',
    html: `
      <p style="font-size:12px;color:#888;margin-bottom:12px;">
        Válido por 72 horas. El candidato abre este link para subir sus documentos.
      </p>
      <div style="background:#f8fafc;border:1px solid #e4eaee;border-radius:8px;
                  padding:10px 14px;font-size:12px;word-break:break-all;
                  color:#1a1a2e;margin-bottom:12px;">
        ${url}
      </div>
      <div style="display:flex;gap:8px;justify-content:center;">
        <button onclick="navigator.clipboard.writeText('${url}').then(()=>Swal.showValidationMessage('✅ Copiado'))"
                style="background:#1a5276;color:#fff;border:none;border-radius:8px;
                       padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;">
          📋 Copiar link
        </button>
        <a href="https://wa.me/?text=${encodeURIComponent('Hola, aquí está tu link para subir tus documentos:\n\n' + url + '\n\n⏰ Válido por 72 horas.')}"
           target="_blank"
           style="background:#25D366;color:#fff;border:none;border-radius:8px;
                  padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;
                  text-decoration:none;display:inline-block;">
          💬 WhatsApp
        </a>
      </div>
    `,
    showConfirmButton: false,
    showCloseButton: true,
    width: 480,
  });
}

function renderDoc(d) {
  const esImagen = ['jpg','jpeg','png'].some(ext => d.url.toLowerCase().endsWith(ext));
  const thumb    = esImagen
    ? `<img src="${d.url}" alt="" onclick="abrirLightbox('${d.url}')" style="width:100%;height:100%;object-fit:cover;">`
    : `<a href="${d.url}" target="_blank" style="font-size:24px;text-decoration:none;">📄</a>`;

  const clsEst = d.estatus === 'aprobado' ? 'est-aprobado' : d.estatus === 'rechazado' ? 'est-rechazado' : 'est-pendiente';
  const txtEst = d.estatus === 'aprobado' ? '✓ Aprobado' : d.estatus === 'rechazado' ? '✗ Rechazado' : '⏳ Pendiente';

  return `
    <div class="exp-doc-item" id="doc-${d.id}">
      <div class="exp-doc-thumb">${thumb}</div>
      <div class="exp-doc-info">
        <div class="exp-doc-nombre">${LABELS_DOC[d.tipo_doc] || d.tipo_doc}</div>
        <div class="exp-doc-tipo">${d.nombre}</div>
        ${d.comentario ? `<div class="exp-doc-comentario">💬 ${d.comentario}</div>` : ''}
      </div>
      <span class="exp-doc-estatus ${clsEst}">${txtEst}</span>
      <div class="exp-doc-actions">
        <button class="exp-btn-doc exp-btn-aprobar" onclick="revisarDoc(${d.id}, 'aprobado', '')">✓ Aprobar</button>
        <button class="exp-btn-doc exp-btn-rechazar" onclick="pedirRechazo(${d.id})">✗ Rechazar</button>
      </div>
    </div>
  `;
}

// ── Aprobar / Rechazar ────────────────────────────
async function revisarDoc(docId, estatus, comentario) {
  document.getElementById('loader').style.display = 'flex';
  const res = await api({ action: 'revisarDoc', doc_id: docId, estatus, comentario });
  document.getElementById('loader').style.display = 'none';

  if (res.ok) {
    Swal.fire({ icon: 'success', title: estatus === 'aprobado' ? 'Aprobado' : 'Rechazado', timer: 1000, showConfirmButton: false });
    verDetalle(expActivo);
  } else {
    Swal.fire('Error', res.msg || 'No se pudo actualizar', 'error');
  }
}

async function pedirRechazo(docId) {
  const { value: comentario, isConfirmed } = await Swal.fire({
    title: 'Motivo de rechazo',
    input: 'textarea',
    inputPlaceholder: 'Escribe el motivo del rechazo...',
    inputAttributes: { maxlength: 255 },
    showCancelButton: true,
    confirmButtonText: 'Rechazar',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#c0392b',
  });
  if (!isConfirmed) return;
  await revisarDoc(docId, 'rechazado', comentario || '');
}

// ── Lightbox ──────────────────────────────────────
function abrirLightbox(url) {
  document.getElementById('exp-lightbox-img').src = url;
  document.getElementById('exp-lightbox').style.display = 'flex';
}
function cerrarLightbox() {
  document.getElementById('exp-lightbox').style.display = 'none';
}

// ── Utils ─────────────────────────────────────────
function iniciales(nombre) {
  if (!nombre) return '?';
  const p = nombre.trim().split(/\s+/);
  return ((p[0]?.[0] || '') + (p[1]?.[0] || '')).toUpperCase();
}

async function asignarClaveSisca(id) {
  const clave = document.getElementById('inp-clave-sisca').value.trim();
  if (!clave) { Swal.fire('Falta la clave', 'Ingresa la clave SISCA del empleado', 'warning'); return; }

  const res = await api({ action: 'asignarClaveSisca', id, clave_sisca: clave });
  if (res.ok) {
    Swal.fire({ icon: 'success', title: 'Clave asignada', timer: 1200, showConfirmButton: false });
    verDetalle(id);
  } else {
    Swal.fire('Error', res.msg || 'No se pudo asignar', 'error');
  }
}

function abrirModalNuevo() {
  Swal.fire({
    title: '📁 Nuevo expediente',
    html: `
      <div style="text-align:left;">
        <div style="margin-bottom:10px;">
          <label style="font-size:12px;color:#888;display:block;margin-bottom:4px;">Tipo de persona *</label>
          <div style="display:flex;gap:8px;">
            <button id="btn-fisica" onclick="setTipo('fisica')"
                    style="flex:1;padding:8px;border-radius:8px;border:2px solid #2B73B6;
                           background:#2B73B6;color:#fff;font-size:12px;font-weight:700;cursor:pointer;">
              👤 Física
            </button>
            <button id="btn-moral" onclick="setTipo('moral')"
                    style="flex:1;padding:8px;border-radius:8px;border:1px solid #ddd;
                           background:#fff;color:#5a7385;font-size:12px;font-weight:700;cursor:pointer;">
              🏢 Moral
            </button>
          </div>
          <input type="hidden" id="nuevo-tipo" value="fisica">
        </div>
        <div style="margin-bottom:10px;">
          <label style="font-size:12px;color:#888;display:block;margin-bottom:4px;">Nombre completo / Razón social *</label>
          <input id="nuevo-nombre" type="text" placeholder="Nombre del candidato o empresa"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:8px;padding:8px 12px;
                        font-size:12px;outline:none;box-sizing:border-box;">
        </div>
        <div style="margin-bottom:10px;">
          <label style="font-size:12px;color:#888;display:block;margin-bottom:4px;">Email</label>
          <input id="nuevo-email" type="email" placeholder="correo@ejemplo.com"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:8px;padding:8px 12px;
                        font-size:12px;outline:none;box-sizing:border-box;">
        </div>
        <div style="margin-bottom:10px;">
          <label style="font-size:12px;color:#888;display:block;margin-bottom:4px;">Celular</label>
          <input id="nuevo-celular" type="tel" placeholder="10 dígitos" maxlength="10"
                 style="width:100%;border:1px solid #d0d0d0;border-radius:8px;padding:8px 12px;
                        font-size:12px;outline:none;box-sizing:border-box;">
        </div>
        <div style="display:flex;align-items:center;gap:8px;">
          <input id="nuevo-factura" type="checkbox" style="width:15px;height:15px;cursor:pointer;">
          <label style="font-size:12px;color:#555;cursor:pointer;" onclick="document.getElementById('nuevo-factura').click()">
            ¿Va a facturar?
          </label>
        </div>
      </div>
    `,
    showCancelButton: true,
    confirmButtonText: '📁 Crear y generar link',
    cancelButtonText: 'Cancelar',
    confirmButtonColor: '#2B73B6',
    width: 480,
    preConfirm: async () => {
      const nombre  = document.getElementById('nuevo-nombre')?.value.trim();
      const email   = document.getElementById('nuevo-email')?.value.trim();
      const celular = document.getElementById('nuevo-celular')?.value.trim();
      const factura = document.getElementById('nuevo-factura')?.checked ? 1 : 0;
      const tipo    = document.getElementById('nuevo-tipo')?.value;

      if (!nombre) {
        Swal.showValidationMessage('El nombre es requerido');
        return false;
      }

      // Crear expediente
      const res = await fetch('expedientes_sisca_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          action: 'crearExpediente',
          tipo_persona: tipo,
          nombre, email, celular, factura,
        })
      }).then(r => r.json());

      if (!res.ok) {
        Swal.showValidationMessage(res.msg || 'Error al crear');
        return false;
      }

      // Generar token automáticamente
      const resToken = await fetch('expedientes_sisca_api.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ action: 'generarToken', id: res.id })
      }).then(r => r.json());

      return { expediente: res, token: resToken };
    }
  }).then(result => {
    if (!result.isConfirmed || !result.value) return;

    const { token } = result.value;
    const url = token.url;

    cargarLista();

    Swal.fire({
      title: '✅ Expediente creado',
      html: `
        <p style="font-size:12px;color:#888;margin-bottom:12px;">
          Comparte este link con el candidato para que suba sus documentos.<br>
          <strong>Válido por 72 horas.</strong>
        </p>
        <div style="background:#f8fafc;border:1px solid #e4eaee;border-radius:8px;
                    padding:10px 14px;font-size:12px;word-break:break-all;
                    color:#1a1a2e;margin-bottom:12px;">
          ${url}
        </div>
        <div style="display:flex;gap:8px;justify-content:center;">
          <button onclick="navigator.clipboard.writeText('${url}').then(()=>Swal.showValidationMessage('✅ Copiado'))"
                  style="background:#1a5276;color:#fff;border:none;border-radius:8px;
                         padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;">
            📋 Copiar link
          </button>
          <a href="https://wa.me/?text=${encodeURIComponent('Hola, aquí está tu link para subir tus documentos:\n\n' + url + '\n\n⏰ Válido por 72 horas.')}"
             target="_blank"
             style="background:#25D366;color:#fff;border:none;border-radius:8px;
                    padding:8px 16px;font-size:12px;font-weight:700;cursor:pointer;
                    text-decoration:none;display:inline-block;">
            💬 WhatsApp
          </a>
        </div>
      `,
      showConfirmButton: false,
      showCloseButton: true,
      width: 480,
    });
  });
}

function setTipo(tipo) {
  document.getElementById('nuevo-tipo').value = tipo;
  const btnFisica = document.getElementById('btn-fisica');
  const btnMoral  = document.getElementById('btn-moral');
  if (tipo === 'fisica') {
    btnFisica.style.background = '#2B73B6';
    btnFisica.style.color = '#fff';
    btnFisica.style.border = '2px solid #2B73B6';
    btnMoral.style.background = '#fff';
    btnMoral.style.color = '#5a7385';
    btnMoral.style.border = '1px solid #ddd';
  } else {
    btnMoral.style.background = '#2B73B6';
    btnMoral.style.color = '#fff';
    btnMoral.style.border = '2px solid #2B73B6';
    btnFisica.style.background = '#fff';
    btnFisica.style.color = '#5a7385';
    btnFisica.style.border = '1px solid #ddd';
  }
}
</script>
</html>