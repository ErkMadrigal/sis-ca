<?php
session_start();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
	<title>CAROBRA Consultores Especializados</title>
	<link rel="shortcut icon" href="./img/favicon.ico">
	<script type="text/javascript" src="./js/carobra.js"></script>
	<script src="./jquery/jquery-3.3.1.min.js"></script>
	<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
	<script src="./popper/popper.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>
	<link rel="stylesheet" href="./css/datepicker.css">
	<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        #retContenido { text-align:left; padding:18px 24px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:#222; }
        #retContenido h5 { font-family:'Syne',sans-serif; font-size:16px; font-weight:700; margin:0 0 4px; color:#1a1a2e; }
        #retContenido .subt { font-size:12px; color:#888; margin-bottom:18px; }

        .ret-stats { display:flex; gap:14px; margin-bottom:18px; flex-wrap:wrap; }
        .ret-stat-card { flex:1; min-width:140px; background:#fff; border:1px solid #e8e8e8; border-radius:10px; padding:14px 18px; }
        .ret-stat-num { font-family:'Syne',sans-serif; font-size:24px; font-weight:800; color:#1a1a2e; }
        .ret-stat-label { font-size:11px; color:#888; text-transform:uppercase; letter-spacing:.06em; margin-top:2px; }
        .ret-stat-card.activos .ret-stat-num   { color:#1e6e42; }
        .ret-stat-card.inactivos .ret-stat-num { color:#b31412; }

        .ret-toolbar { display:flex; gap:12px; align-items:center; margin-bottom:16px; flex-wrap:wrap; }
        .ret-toolbar input[type=text] {
            width:100%; box-sizing:border-box; border:1px solid #d0d0d0; border-radius:8px; padding:9px 14px;
            font-size:13px; outline:none;
        }
        .ret-toolbar input[type=text]:focus { border-color:#4a6cf7; }
        .ret-btn-primario {
            background:#2384c5; color:#fff; border:none; border-radius:8px;
            padding:9px 18px; font-size:13px; font-weight:600; cursor:pointer;
            font-family:inherit; white-space:nowrap; transition:.15s;
        }
        .ret-btn-primario:hover { background:#1a6fa8; }

        .ret-toggle-filtro { display:flex; gap:6px; background:#f1f5f9; border-radius:8px; padding:3px; }
        .ret-toggle-filtro button {
            border:none; background:transparent; padding:6px 14px; border-radius:6px;
            font-size:12px; font-weight:600; cursor:pointer; color:#64748b; font-family:inherit;
        }
        .ret-toggle-filtro button.activo { background:#fff; color:#1a56db; box-shadow:0 1px 3px rgba(0,0,0,.1); }

        table.ret-tabla { width:100%; border-collapse:collapse; background:#fff; border-radius:10px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.06); }
        table.ret-tabla thead th {
            background:#f8fafc; text-align:left; font-size:11px; text-transform:uppercase;
            letter-spacing:.05em; color:#888; padding:10px 14px; border-bottom:1px solid #e8e8e8;
        }
        table.ret-tabla tbody td { padding:10px 14px; border-bottom:1px solid #f0f0f0; font-size:13px; vertical-align:middle; }
        table.ret-tabla tbody tr:last-child td { border-bottom:none; }
        table.ret-tabla tbody tr:hover { background:#fafbff; }
        .ret-nombre { font-weight:600; color:#1a1a2e; }
        .ret-clave  { font-size:11px; color:#aaa; }
        .ret-chip { display:inline-block; font-size:10px; font-weight:700; padding:2px 8px; border-radius:20px; margin:1px 3px 1px 0; }
        .ret-chip-ger { background:#e6f4ea; color:#1e6e42; }
        .ret-chip-brk { background:#fff3cd; color:#856404; }
        .ret-chip-sc  { background:#fce8e6; color:#b31412; }
        .ret-chip-vacio { background:#f1f5f9; color:#94a3b8; }
        .ret-badge-estatus { font-size:11px; font-weight:700; padding:3px 10px; border-radius:20px; }
        .ret-badge-activo   { background:#e6f4ea; color:#1e6e42; }
        .ret-badge-inactivo { background:#f1f1f1; color:#888; }
        .ret-acciones { display:flex; gap:6px; }
        .ret-btn-mini {
            border:none; border-radius:6px; padding:5px 10px; font-size:11px; font-weight:600;
            cursor:pointer; font-family:inherit; transition:.15s;
        }
        .ret-btn-activar    { background:#e6f4ea; color:#1e6e42; }
        .ret-btn-desactivar { background:#fff3cd; color:#856404; }
        .ret-btn-eliminar   { background:#fce8e6; color:#b31412; }
        .ret-vacio { text-align:center; padding:40px 20px; color:#aaa; font-size:13px; }

        /* Modal asignar */
        #modal-ret-overlay {
            display:none; position:fixed; inset:0; background:rgba(15,23,42,.6);
            z-index:99999; justify-content:center; align-items:center; padding:1.5rem; backdrop-filter:blur(4px);
        }
        #modal-ret-overlay.open { display:flex; }
        .modal-ret-content { width:100%; max-width:480px; max-height:90vh; background:#fff; border-radius:16px; display:flex; flex-direction:column; overflow:hidden; }
        .modal-ret-header { padding:18px 22px 14px; border-bottom:1px solid #e8e8e8; display:flex; justify-content:space-between; align-items:center; }
        .modal-ret-header h4 { margin:0; font-family:'Syne',sans-serif; font-size:15px; font-weight:700; color:#1a1a2e; }
        .modal-ret-body { flex:1; overflow-y:auto; padding:18px 22px; display:flex; flex-direction:column; gap:12px; }
        .modal-ret-footer { padding:14px 22px; border-top:1px solid #e8e8e8; display:flex; gap:10px; justify-content:flex-end; background:#f8fafc; }
        .modal-ret-footer button { border:none; padding:9px 20px; border-radius:8px; cursor:pointer; font-size:13px; font-weight:600; font-family:inherit; }
        .btn-ret-cancelar { background:#f1f5f9; color:#64748b; }
        .btn-ret-cancelar:hover { background:#e2e8f0; }

        .ta-wrapper { position:relative; }
        .ta-dropdown { display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #d0d0d0; border-top:none; border-radius:0 0 6px 6px; max-height:220px; overflow-y:auto; box-shadow:0 6px 16px rgba(0,0,0,.1); }
        .ta-dropdown.open { display:block; }
        .ta-item { padding:8px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0f0f0; }
        .ta-item:last-child { border-bottom:none; }
        .ta-item:hover { background:#f0f4ff; }
        .ta-item .ta-nombre { font-weight:600; color:#1a1a2e; }
        .ta-item .ta-clave  { font-size:11px; color:#aaa; margin-left:6px; }
        .ta-vacio { padding:10px 12px; font-size:13px; color:#aaa; text-align:center; }

        .preview-empleado {
            display:none; background:#f8fafc; border:1px solid #e8e8e8; border-radius:10px; padding:14px;
        }
        .preview-empleado .pe-nombre { font-weight:700; color:#1a1a2e; font-size:14px; }
        .preview-empleado .pe-clave  { font-size:11px; color:#888; margin-bottom:8px; }

        .swal2-container { z-index: 9999999 !important; }
    </style>
</head>
<?php include("./funciones/clas_carobra.php"); ?>
<body>
<?php
    $fg      = $_SESSION["ultimoAcceso"];
    $fa      = date("Y-n-j H:i:s");
    $periodo = (strtotime($fa) - strtotime($fg));
    if ($periodo > 6000) { session_destroy(); mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente", "http://carobra.ddns.me:8888"); }
    else { $_SESSION["ultimoAcceso"] = $fa; $_SESSION["autentifica"] = "p"; }
    if ($_SESSION["autentifica"] != "p") { session_destroy(); mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente", "index.php"); }
    else {
        reg_acceso($_SESSION["nombre"], "empleados_dp");
        $_SESSION["autentifica"] = "";
        $divpie=$divopcion=$divcabecera=$divcontenido=$divcontenedor=null;
        $divpie        = new div("pie");
        $divopcion     = new div("opcion");
        $divcabecera   = new div("cabecera");
        $divcontenido  = new div("contenido");
        $divcontenedor = new div("contenedor");
        $imgpie        = new img("imgpie",      "./img/pie.png");
        $divusuario    = new div("usuario", '', "absolute", "97%");
        $imgcabecera   = new img("imgcabecera", "./img/cabecera.png");
        $divcabecera->crear();
            $imgcabecera->crear(); $imgcabecera->css("width","100%");
            titulo("Retenciones Usuarios");
            $pag = "RETENCIONES";
            menu();
        $divcabecera->close();
        $divpie->crear();
            $imgpie->crear(); $imgpie->css("width","100%");
            $divusuario->crear();
                echo "<br><br>";
                $divusuario->css("top","10px"); $divusuario->css("text-align","left");
?>
	<div id="vinculos" style="margin-top:10px;margin-left:25px;"><?php include("vinculos.php"); ?></div>
<?php
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: ".$_SESSION["nombre"]."</spam>";
        echo "<div style='width:100%;text-align:right;margin-top:-25px;'>";
        echo "<spam id='email'>Email</spam>&nbsp;&nbsp;&nbsp;<spam id='salir'>Salir</spam></div>";
        $divusuario->close(); $divpie->close();
        $divcontenedor->crear();
        $divopcion->crear(); $divopcion->css("background","#FFFFFF"); $divopcion->close();
        $divcontenido->crear(); $divcontenido->css("overflow-y","auto");
?>
    <div id="retContenido">
        <h5>&#128274; Retenciones</h5>
        <div class="subt">Personas asignadas a hacer retenciones / recuperación de cancelaciones, con su estructura comercial.</div>

        <div class="ret-stats">
            <div class="ret-stat-card">
                <div class="ret-stat-num" id="stat-total">—</div>
                <div class="ret-stat-label">Total asignados</div>
            </div>
            <div class="ret-stat-card activos">
                <div class="ret-stat-num" id="stat-activos">—</div>
                <div class="ret-stat-label">Activos</div>
            </div>
            <div class="ret-stat-card inactivos">
                <div class="ret-stat-num" id="stat-inactivos">—</div>
                <div class="ret-stat-label">Inactivos</div>
            </div>
        </div>

        <div class="ret-toolbar">
            <div class="ta-wrapper" style="flex:1;min-width:240px;">
                <input type="text" id="ret-buscar" placeholder="Buscar por nombre, clave, gerencia, brocker o SC..."
                    autocomplete="off" oninput="filtrarTabla()" onfocus="filtrarTabla()" />
                <div class="ta-dropdown" id="ret-buscar-dropdown"></div>
            </div>
            <div class="ret-toggle-filtro">
                <button class="activo" data-filtro="todos"    onclick="cambiarFiltroEstatus(this,'todos')">Todos</button>
                <button                data-filtro="activos"  onclick="cambiarFiltroEstatus(this,'activos')">Activos</button>
                <button                data-filtro="inactivos" onclick="cambiarFiltroEstatus(this,'inactivos')">Inactivos</button>
            </div>
            <button class="ret-btn-primario" onclick="abrirModalAsignar()">+ Asignar persona</button>
        </div>

        <table class="ret-tabla">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>No. empleado</th>
                    <th>Estructura</th>
                    <th>Estatus</th>
                    <th>Alta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="ret-tbody">
                <tr><td colspan="6" class="ret-vacio">Cargando...</td></tr>
            </tbody>
        </table>
    </div>

<?php $divcontenido->close(); $divpie->close(); ?>

    <script language='javascript'>
        var hc = calcula_h(125);
        var h  = $(window).outerHeight() - hc;
        $('#pie').css("top", h + 5);
        hc = calcula_h(85);
        h  = $(window).outerHeight() - (hc * 2);
        $('#contenedor').css("top", hc - 15);
        $('#contenedor').css("height", h);
        hc = calcula_h(45);
        $('#menu').css("top", hc - 20);
        $('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
    </script>

<?php } ?>

    <!-- MODAL ASIGNAR -->
    <div id="modal-ret-overlay">
        <div class="modal-ret-content">
            <div class="modal-ret-header">
                <h4>&#10133; Asignar persona a retenciones</h4>
                <button onclick="cerrarModalAsignar()" style="background:none;border:none;font-size:18px;cursor:pointer;color:#888;line-height:1;">✕</button>
            </div>
            <div class="modal-ret-body">
                <div>
                    <label style="display:block;font-size:12px;color:#555;margin-bottom:4px;">Buscar empleado</label>
                    <div class="ta-wrapper">
                        <input type="text" id="ret_buscar_empleado" placeholder="Nombre o clave..." autocomplete="off"
                            oninput="onBuscarEmpleadoRet(this)"
                            style="width:100%;box-sizing:border-box;border:1px solid #d0d0d0;border-radius:5px;padding:7px 10px;font-size:13px;outline:none;font-family:inherit;" />
                        <div class="ta-dropdown" id="ret_empleado_dropdown"></div>
                    </div>
                </div>

                <div class="preview-empleado" id="ret-preview">
                    <div class="pe-nombre" id="pe-nombre"></div>
                    <div class="pe-clave"  id="pe-clave"></div>
                    <div id="pe-estructura"></div>
                </div>

                <input type="hidden" id="ret_clave_seleccionada" />
            </div>
            <div class="modal-ret-footer">
                <button class="btn-ret-cancelar" onclick="cerrarModalAsignar()">Cancelar</button>
                <button class="ret-btn-primario" id="btn-confirmar-asignar" onclick="confirmarAsignar()" disabled>Asignar</button>
            </div>
        </div>
    </div>
</body>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
    jQuery(document).ready(function() {
        $(window).resize(function() { centra_contenedor(); });
        $(window).resize();
        $('#men_pie').css("top", h);
        $('#tmenu td').click(function() { $("#opcion").load("menu.php?modulo=" + $(this).text().replace(/ /gi,"_")); });
        $("#salir").click(function() { window.location = "http://carobra.ddns.me"; });
        $("#email").click(function() { window.open("http://webmail.carobra.com.mx/imp/login.php"); });
    });
</script>

<script>
(function () {

    var API = 'usuarios_retenciones_api.php';
    var datosRetenciones = [];
    var filtroEstatus     = 'todos';

    function apiPost(payload) {
        return fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function (r) { return r.json(); });
    }

    function resaltar(texto, q) {
        if (!q) return texto;
        var re = new RegExp('(' + q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&') + ')', 'gi');
        return texto.replace(re, '<span style="color:#1a56db;font-weight:700;">$1</span>');
    }

    /* Solo se muestra el nivel más alto de la jerarquía: sc > brocker > gerencia */
    function chipsEstructura(item) {
        if (item.sc)       return '<span class="ret-chip ret-chip-sc"  title="SC">SC · '      + item.sc       + '</span>';
        if (item.brocker)  return '<span class="ret-chip ret-chip-brk" title="Brocker">Brocker · ' + item.brocker  + '</span>';
        if (item.gerencia) return '<span class="ret-chip ret-chip-ger" title="Gerencia">Gerencia · ' + item.gerencia + '</span>';
        return '<span class="ret-chip ret-chip-vacio">Sin estructura</span>';
    }

    function formatFecha(f) {
        if (!f) return '—';
        var d = new Date(f.replace(' ', 'T'));
        if (isNaN(d.getTime())) return f;
        return d.toLocaleDateString('es-MX', { day:'2-digit', month:'short', year:'numeric' });
    }

    window.cargarRetenciones = function () {
        apiPost({ action: 'listarRetenciones' }).then(function (res) {
            if (res.status !== 'ok') {
                document.getElementById('ret-tbody').innerHTML = '<tr><td colspan="6" class="ret-vacio">Error al cargar</td></tr>';
                return;
            }
            datosRetenciones = res.data || [];
            actualizarStats();
            renderTabla();
        });
    };

    function actualizarStats() {
        var total     = datosRetenciones.length;
        var activos   = datosRetenciones.filter(function (d) { return d.activo; }).length;
        var inactivos = total - activos;
        document.getElementById('stat-total').textContent     = total;
        document.getElementById('stat-activos').textContent   = activos;
        document.getElementById('stat-inactivos').textContent = inactivos;
    }

    window.cambiarFiltroEstatus = function (btn, filtro) {
        filtroEstatus = filtro;
        document.querySelectorAll('.ret-toggle-filtro button').forEach(function (b) { b.classList.remove('activo'); });
        btn.classList.add('activo');
        renderTabla();
    };

    window.filtrarTabla = function () {
        renderTabla();
        renderDropdownBuscar();
    };

    var debounceBuscarPrincipal = null;

    function renderDropdownBuscar() {
        var inp  = document.getElementById('ret-buscar');
        var drop = document.getElementById('ret-buscar-dropdown');
        var q    = inp.value.trim();
        var qLow = q.toLowerCase();

        if (!q) { drop.classList.remove('open'); drop.innerHTML = ''; return; }

        var asignados = datosRetenciones.filter(function (item) {
            var campos = [item.nombre, item.clave, item.gerencia, item.brocker, item.sc].join(' ').toLowerCase();
            return campos.indexOf(qLow) !== -1;
        }).slice(0, 6);

        pintarDropdownBuscar(drop, qLow, asignados, null);

        if (q.length < 2) return;

        clearTimeout(debounceBuscarPrincipal);
        debounceBuscarPrincipal = setTimeout(function () {
            apiPost({ action: 'buscarEmpleado', q: q }).then(function (res) {
                /* Si el usuario ya cambió el texto mientras llegaba la respuesta, no pintar */
                if (document.getElementById('ret-buscar').value.trim().toLowerCase() !== qLow) return;
                pintarDropdownBuscar(drop, qLow, asignados, res.data || []);
            });
        }, 300);
    }

    function pintarDropdownBuscar(drop, q, asignados, noAsignados) {
        var html = '';

        if (asignados.length === 0 && (noAsignados === null)) {
            drop.innerHTML = '<div class="ta-vacio">Buscando...</div>';
            drop.classList.add('open');
            return;
        }

        if (asignados.length > 0) {
            html += '<div style="padding:6px 12px;font-size:10px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.05em;">Ya asignados</div>';
            asignados.forEach(function (item) {
                html +=
                    '<div class="ta-item" data-accion="filtrar" data-nombre="' + item.nombre.replace(/"/g, '&quot;') + '">' +
                    '<div><span class="ta-nombre">' + resaltar(item.nombre, q) + '</span><span class="ta-clave">' + item.clave + '</span></div>' +
                    '<div style="margin-top:4px;">' + chipsEstructura(item) + '</div>' +
                    '</div>';
            });
        }

        if (noAsignados && noAsignados.length > 0) {
            html += '<div style="padding:6px 12px;font-size:10px;font-weight:700;color:#aaa;text-transform:uppercase;letter-spacing:.05em;border-top:1px solid #f0f0f0;">No asignados — clic para agregar</div>';
            noAsignados.slice(0, 8).forEach(function (item) {
                html +=
                    '<div class="ta-item" data-accion="asignar" data-clave="' + item.clave + '" data-nombre="' + item.nombre.replace(/"/g, '&quot;') + '">' +
                    '<div><span class="ta-nombre">' + resaltar(item.nombre, q) + '</span><span class="ta-clave">' + item.clave + '</span></div>' +
                    '<div style="margin-top:4px;">' + chipsEstructura(item) + '</div>' +
                    '</div>';
            });
        }

        if (!html) {
            html = '<div class="ta-vacio">Sin coincidencias</div>';
        }

        drop.innerHTML = html;
        drop.classList.add('open');

        drop.querySelectorAll('.ta-item[data-accion="filtrar"]').forEach(function (el) {
            el.addEventListener('mousedown', function () {
                document.getElementById('ret-buscar').value = el.getAttribute('data-nombre');
                drop.classList.remove('open');
                renderTabla();
            });
        });

        drop.querySelectorAll('.ta-item[data-accion="asignar"]').forEach(function (el) {
            el.addEventListener('mousedown', function () {
                var clave  = el.getAttribute('data-clave');
                var nombre = el.getAttribute('data-nombre');
                drop.classList.remove('open');
                Swal.fire({
                    icon: 'question',
                    title: '¿Agregar a retenciones?',
                    text: nombre,
                    showCancelButton: true,
                    confirmButtonText: 'Sí, agregar',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#2384c5',
                    cancelButtonColor: '#aaa'
                }).then(function (result) {
                    if (!result.isConfirmed) return;
                    apiPost({ action: 'asignarRetencion', clave: clave }).then(function (res) {
                        if (res.status === 'ok') {
                            document.getElementById('ret-buscar').value = '';
                            Swal.fire({ icon: 'success', title: '¡Listo!', text: res.msg, timer: 1500, showConfirmButton: false });
                            cargarRetenciones();
                        } else {
                            Swal.fire({ icon: 'error', title: 'Error', text: res.msg, confirmButtonColor: '#1a56db' });
                        }
                    });
                });
            });
        });
    }

    document.getElementById('ret-buscar').addEventListener('blur', function () {
        setTimeout(function () { document.getElementById('ret-buscar-dropdown').classList.remove('open'); }, 200);
    });

    function renderTabla() {
        var q = document.getElementById('ret-buscar').value.trim().toLowerCase();
        var tbody = document.getElementById('ret-tbody');

        var filtrados = datosRetenciones.filter(function (item) {
            if (filtroEstatus === 'activos'   && !item.activo) return false;
            if (filtroEstatus === 'inactivos' &&  item.activo) return false;
            if (!q) return true;
            var campos = [item.nombre, item.clave, item.gerencia, item.brocker, item.sc].join(' ').toLowerCase();
            return campos.indexOf(q) !== -1;
        });

        if (filtrados.length === 0) {
            tbody.innerHTML = '<tr><td colspan="6" class="ret-vacio">Sin resultados</td></tr>';
            return;
        }

        tbody.innerHTML = filtrados.map(function (item) {
            var badge = item.activo
                ? '<span class="ret-badge-estatus ret-badge-activo">Activo</span>'
                : '<span class="ret-badge-estatus ret-badge-inactivo">Inactivo</span>';

            var btnToggle = item.activo
                ? '<button class="ret-btn-mini ret-btn-desactivar" onclick="toggleActivo(' + item.id + ', 0)">Desactivar</button>'
                : '<button class="ret-btn-mini ret-btn-activar" onclick="toggleActivo(' + item.id + ', 1)">Activar</button>';

            return '<tr>' +
                '<td><div class="ret-nombre">' + resaltar(item.nombre, q) + '</div><div class="ret-clave">' + item.clave + '</div></td>' +
                '<td>' + (item.no_empleado || '—') + '</td>' +
                '<td>' + chipsEstructura(item) + '</td>' +
                '<td>' + badge + '</td>' +
                '<td>' + formatFecha(item.fecha_alta) + '</td>' +
                '<td><div class="ret-acciones">' + btnToggle + '</div></td>' +
                '</tr>';
        }).join('');
    }

    window.toggleActivo = function (id, nuevoEstatus) {
        apiPost({ action: 'toggleActivo', id: id, activo: nuevoEstatus }).then(function (res) {
            if (res.status === 'ok') {
                cargarRetenciones();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.msg, confirmButtonColor: '#1a56db' });
            }
        });
    };

    window.eliminarRetencion = function (id, nombre) {
        Swal.fire({
            icon: 'warning',
            title: '¿Eliminar a ' + nombre + '?',
            text: 'Se borrará por completo su registro de retenciones (no solo se desactiva).',
            showCancelButton: true,
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#b31412',
            cancelButtonColor: '#aaa'
        }).then(function (result) {
            if (!result.isConfirmed) return;
            apiPost({ action: 'eliminarRetencion', id: id }).then(function (res) {
                if (res.status === 'ok') {
                    cargarRetenciones();
                } else {
                    Swal.fire({ icon: 'error', title: 'Error', text: res.msg, confirmButtonColor: '#1a56db' });
                }
            });
        });
    };

    /* ── MODAL ASIGNAR ── */
    window.abrirModalAsignar = function () {
        document.getElementById('ret_buscar_empleado').value        = '';
        document.getElementById('ret_clave_seleccionada').value     = '';
        document.getElementById('ret_empleado_dropdown').innerHTML  = '';
        document.getElementById('ret_empleado_dropdown').classList.remove('open');
        document.getElementById('ret-preview').style.display        = 'none';
        document.getElementById('btn-confirmar-asignar').disabled   = true;
        document.getElementById('modal-ret-overlay').classList.add('open');
        setTimeout(function () { document.getElementById('ret_buscar_empleado').focus(); }, 100);
    };

    window.cerrarModalAsignar = function () {
        document.getElementById('modal-ret-overlay').classList.remove('open');
    };

    document.getElementById('modal-ret-overlay').addEventListener('click', function (e) {
        if (e.target === this) cerrarModalAsignar();
    });

    var debounceBuscarEmp = null;

    window.onBuscarEmpleadoRet = function (inp) {
        clearTimeout(debounceBuscarEmp);
        var q = inp.value.trim();
        var drop = document.getElementById('ret_empleado_dropdown');

        document.getElementById('ret-preview').style.display      = 'none';
        document.getElementById('ret_clave_seleccionada').value   = '';
        document.getElementById('btn-confirmar-asignar').disabled = true;

        if (q.length < 2) { drop.classList.remove('open'); drop.innerHTML = ''; return; }

        drop.innerHTML = '<div class="ta-vacio">Buscando...</div>';
        drop.classList.add('open');

        debounceBuscarEmp = setTimeout(function () {
            apiPost({ action: 'buscarEmpleado', q: q }).then(function (res) {
                drop.innerHTML = '';
                var data = res.data || [];
                if (data.length === 0) {
                    drop.innerHTML = '<div class="ta-vacio">Sin resultados (o ya está asignado)</div>';
                    drop.classList.add('open');
                    return;
                }
                data.forEach(function (item) {
                    var d = document.createElement('div');
                    d.className = 'ta-item';
                    d.innerHTML = '<span class="ta-nombre">' + resaltar(item.nombre, q) + '</span><span class="ta-clave">' + item.clave + '</span>';
                    d.addEventListener('mousedown', function () { seleccionarEmpleadoRet(item); });
                    drop.appendChild(d);
                });
                drop.classList.add('open');
            });
        }, 300);
    };

    function seleccionarEmpleadoRet(item) {
        document.getElementById('ret_buscar_empleado').value      = item.nombre;
        document.getElementById('ret_clave_seleccionada').value   = item.clave;
        document.getElementById('ret_empleado_dropdown').classList.remove('open');

        document.getElementById('pe-nombre').textContent = item.nombre;
        document.getElementById('pe-clave').textContent  = item.clave + (item.no_empleado ? ' · No. empleado ' + item.no_empleado : '');
        document.getElementById('pe-estructura').innerHTML = chipsEstructura(item);
        document.getElementById('ret-preview').style.display = 'block';

        document.getElementById('btn-confirmar-asignar').disabled = false;
    }

    document.getElementById('ret_buscar_empleado').addEventListener('blur', function () {
        setTimeout(function () { document.getElementById('ret_empleado_dropdown').classList.remove('open'); }, 200);
    });

    window.confirmarAsignar = function () {
        var clave = document.getElementById('ret_clave_seleccionada').value.trim();
        if (!clave) return;

        var btn = document.getElementById('btn-confirmar-asignar');
        btn.disabled = true;
        btn.innerHTML = '<span style="display:inline-block;width:12px;height:12px;border:2px solid #fff;border-top-color:transparent;border-radius:50%;animation:ret-spin .7s linear infinite;vertical-align:-2px;margin-right:4px;"></span> Guardando...';

        apiPost({ action: 'asignarRetencion', clave: clave }).then(function (res) {
            btn.disabled = false;
            btn.textContent = 'Asignar';
            if (res.status === 'ok') {
                cerrarModalAsignar();
                Swal.fire({ icon: 'success', title: '¡Listo!', text: res.msg, timer: 1500, showConfirmButton: false });
                cargarRetenciones();
            } else {
                Swal.fire({ icon: 'error', title: 'Error', text: res.msg, confirmButtonColor: '#1a56db' });
            }
        }).catch(function () {
            btn.disabled = false;
            btn.textContent = 'Asignar';
            Swal.fire({ icon: 'error', title: 'Error de conexión', confirmButtonColor: '#1a56db' });
        });
    };

    var styleSpin = document.createElement('style');
    styleSpin.textContent = '@keyframes ret-spin { to { transform: rotate(360deg); } }';
    document.head.appendChild(styleSpin);

    cargarRetenciones();

})();
</script>
</html>