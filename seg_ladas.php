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
	<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        #ldContenido { text-align:left; padding:18px 24px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; color:#222; }
        #ldContenido h5 { font-family:'Syne',sans-serif; font-size:16px; font-weight:700; margin:0 0 4px; color:#1a1a2e; }
        #ldContenido .subt { font-size:12px; color:#888; margin-bottom:18px; }
        .ld-card { background:#fff; border:1px solid #e8e8e8; border-radius:10px; padding:16px 18px; margin-bottom:16px; }
        .ld-row { display:flex; gap:14px; flex-wrap:wrap; align-items:flex-end; }
        .ld-field { display:flex; flex-direction:column; gap:4px; }
        .ld-field label { font-size:11px; font-weight:600; color:#666; text-transform:uppercase; letter-spacing:.04em; }
        .ld-field select, .ld-field input[type=date], .ld-field input[type=text] {
            border:1px solid #d0d0d0; border-radius:8px; padding:8px 12px; font-size:13px; outline:none; font-family:inherit; background:#fff;
        }
        .ld-field select:focus, .ld-field input:focus { border-color:#4a6cf7; }
        .ld-btn { border:none; border-radius:8px; padding:9px 18px; font-size:13px; font-weight:600; cursor:pointer; font-family:inherit; white-space:nowrap; }
        .ld-btn-primario { background:#2384c5; color:#fff; } .ld-btn-primario:hover { background:#1a6fa8; }
        .ld-btn-verde { background:#1e8e58; color:#fff; } .ld-btn-verde:hover { background:#17754a; }
        .ld-btn:disabled { background:#aaa; cursor:not-allowed; }
        .ld-btn-link { background:none; border:none; color:#2384c5; font-size:12px; cursor:pointer; padding:0 6px; font-family:inherit; text-decoration:underline; }
        .ld-chips { display:flex; flex-wrap:wrap; gap:8px; margin-top:10px; }
        .ld-chip { display:inline-flex; align-items:center; gap:6px; border:1px solid #c0d4fc; background:#f0f4ff; color:#1a56db; border-radius:20px; padding:4px 10px 4px 8px; font-size:12px; font-weight:700; }
        .ld-chip input { margin:0; cursor:pointer; }
        .ld-chip label { margin:0; cursor:pointer; }
        .ld-chip .ld-x { cursor:pointer; opacity:.45; font-size:14px; line-height:1; }
        .ld-chip .ld-x:hover { opacity:1; color:#c0392b; }
        .ld-aviso { font-size:12px; color:#856404; background:#fff3cd; border:1px solid #ffe69c; border-radius:6px; padding:8px 12px; margin-top:10px; display:none; }
        .ld-resumen { display:flex; justify-content:space-between; align-items:center; margin-bottom:10px; flex-wrap:wrap; gap:10px; }
        .ld-resumen-txt { font-size:13px; color:#555; }
        .ld-tabla-wrap { overflow-x:auto; background:#fff; border-radius:10px; box-shadow:0 1px 3px rgba(0,0,0,.06); }
        table.ld-tabla { width:100%; border-collapse:collapse; font-size:12px; white-space:nowrap; }
        table.ld-tabla thead th { background:#f8fafc; text-align:left; font-size:10.5px; text-transform:uppercase; letter-spacing:.05em; color:#888; padding:9px 12px; border-bottom:1px solid #e8e8e8; position:sticky; top:0; }
        table.ld-tabla tbody td { padding:8px 12px; border-bottom:1px solid #f0f0f0; }
        table.ld-tabla tbody tr:hover { background:#fafbff; }
        .ld-badge-lada { background:#e8f0fe; color:#1a56db; border-radius:12px; padding:2px 8px; font-weight:700; font-size:11px; }
        .ld-vacio { text-align:center; padding:36px 20px; color:#aaa; }
        .ld-pag { display:flex; gap:8px; align-items:center; justify-content:flex-end; margin-top:10px; font-size:12px; color:#666; }
        .ld-pag button { border:1px solid #d0d0d0; background:#fff; border-radius:6px; padding:5px 12px; cursor:pointer; font-family:inherit; }
        .ld-pag button:disabled { opacity:.4; cursor:not-allowed; }
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
            titulo("Ladas de clientes");
            $pag = "LADAS";
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
    <div id="ldContenido">
        <h5>&#128222; Ladas de clientes</h5>
        <div class="subt">Folios de producci&oacute;n cuyo cliente tiene celular o tel&eacute;fono con las ladas de un estado, con la estructura del asesor y su pago.</div>

        <div class="ld-card">
            <div class="ld-row">
                <div class="ld-field" style="min-width:220px;">
                    <label>Estado</label>
                    <select id="ld-estado" onchange="cambiarEstado()"><option value="">Cargando...</option></select>
                </div>
                <input type="hidden" id="ld-fecha-ini" />
                <input type="hidden" id="ld-fecha-fin" />
                <button class="ld-btn ld-btn-primario" id="ld-btn-consultar" onclick="consultar()">Consultar</button>
            </div>

            <div id="ld-ladas-wrap" style="display:none;margin-top:14px;">
                <div style="font-size:11px;font-weight:600;color:#666;text-transform:uppercase;letter-spacing:.04em;">
                    Ladas del estado
                    <button class="ld-btn-link" onclick="marcarLadas(true)">todas</button>
                    <button class="ld-btn-link" onclick="marcarLadas(false)">ninguna</button>
                </div>
                <div class="ld-chips" id="ld-chips"></div>
                <div class="ld-aviso" id="ld-aviso">Este estado todav&iacute;a no tiene ladas cargadas en el cat&aacute;logo. Agrega las que correspondan abajo.</div>
                <div class="ld-row" style="margin-top:12px;">
                    <div class="ld-field">
                        <label>Agregar lada al cat&aacute;logo de este estado</label>
                        <input type="text" id="ld-nueva-lada" maxlength="3" placeholder="Ej. 821" style="width:120px;" onkeydown="if(event.key==='Enter')agregarLada()" />
                    </div>
                    <button class="ld-btn ld-btn-primario" onclick="agregarLada()">+ Agregar</button>
                </div>
            </div>
        </div>

        <div id="ld-resultado" style="display:none;">
            <div class="ld-resumen">
                <div class="ld-resumen-txt" id="ld-resumen-txt"></div>
                <div style="display:flex;gap:10px;align-items:center;">
                    <input type="text" id="ld-filtro" placeholder="Filtrar resultados..." oninput="paginaActual=1;renderTabla()" style="border:1px solid #d0d0d0;border-radius:8px;padding:8px 12px;font-size:13px;outline:none;min-width:220px;font-family:inherit;" />
                    <button class="ld-btn ld-btn-verde" onclick="exportarXLSX()">&#128196; Exportar XLSX</button>
                </div>
            </div>
            <div id="ld-debug" style="display:none;margin-bottom:12px;background:#fff8e8;border:1px solid #f5d97a;border-radius:8px;padding:12px 14px;">
                <div style="font-size:12px;font-weight:700;color:#b37f00;margin-bottom:6px;">Sin resultados &mdash; diagn&oacute;stico (c&oacute;pialo y p&aacute;salo si necesitas ayuda)</div>
                <pre id="ld-debug-txt" style="margin:0;font-size:11px;white-space:pre-wrap;word-break:break-all;max-height:320px;overflow:auto;"></pre>
            </div>
            <div class="ld-tabla-wrap">
                <table class="ld-tabla">
                    <thead><tr>
                        <th>No. emp.</th><th>Clave</th><th>Nombre</th><th>Domicilio</th>
                        <th>Cel. emp.</th><th>Tel. emp.</th>
                        <th>Gerencia</th><th>Brocker</th><th>SC</th>
                        <th>Folio</th><th>Fec. captura</th><th>Cliente</th>
                        <th>Tel. cliente</th><th>Cel. cliente</th><th>Lada</th>
                        <th style="text-align:right;">Saldo real</th><th>Fec. pago</th>
                    </tr></thead>
                    <tbody id="ld-tbody"></tbody>
                </table>
            </div>
            <div class="ld-pag">
                <span id="ld-pag-info"></span>
                <button id="ld-pag-prev" onclick="cambiarPagina(-1)">&laquo; Anterior</button>
                <button id="ld-pag-next" onclick="cambiarPagina(1)">Siguiente &raquo;</button>
            </div>
        </div>
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
var paginaActual = 1;
(function () {

    var API = 'seg_ladas_api.php';
    var POR_PAGINA = 100;
    var datos = [];

    function apiPost(payload) {
        return fetch(API, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(payload)
        }).then(function (r) { return r.text(); }).then(function (txt) {
            try { return JSON.parse(txt); }
            catch (e) {
                throw new Error('Respuesta no v\u00e1lida del servidor: ' + (txt.trim().substring(0, 400) || '(vac\u00eda)'));
            }
        });
    }

    function esc(v) {
        return String(v == null ? '' : v).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/"/g,'&quot;');
    }

    function fmtLocal(d) {
        return d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0') + '-' + String(d.getDate()).padStart(2, '0');
    }
    var hoyD = new Date();
    var hoy = fmtLocal(hoyD);
    var haceUnAnio = new Date(hoyD.getFullYear() - 1, hoyD.getMonth(), hoyD.getDate());
    document.getElementById('ld-fecha-ini').value = fmtLocal(haceUnAnio);
    document.getElementById('ld-fecha-fin').value = hoy;

    function cargarEstados(seleccionar) {
        return apiPost({ action: 'listarEstados' }).then(function (res) {
            var sel = document.getElementById('ld-estado');
            sel.innerHTML = '<option value="">Selecciona un estado...</option>';
            (res.data || []).forEach(function (e) {
                var o = document.createElement('option');
                o.value = e.estado;
                o.textContent = e.estado + ' (' + e.total + ')';
                sel.appendChild(o);
            });
            if (seleccionar) sel.value = seleccionar;
        });
    }

    window.cambiarEstado = function () {
        var estado = document.getElementById('ld-estado').value;
        var wrap = document.getElementById('ld-ladas-wrap');
        if (!estado) { wrap.style.display = 'none'; return; }
        wrap.style.display = 'block';
        cargarLadas(estado);
    };

    function cargarLadas(estado) {
        apiPost({ action: 'getLadas', estado: estado }).then(function (res) {
            var cont = document.getElementById('ld-chips');
            cont.innerHTML = '';
            var ladas = res.data || [];
            document.getElementById('ld-aviso').style.display = ladas.length ? 'none' : 'block';
            ladas.forEach(function (l) {
                var chip = document.createElement('span');
                chip.className = 'ld-chip';
                chip.innerHTML =
                    '<input type="checkbox" id="chk-' + l + '" value="' + l + '" checked />' +
                    '<label for="chk-' + l + '">' + l + '</label>' +
                    '<span class="ld-x" title="Excluir de esta consulta">&times;</span>';
                chip.querySelector('.ld-x').addEventListener('click', function () {
                    /* solo excluye de la consulta (desmarca); no toca el cat\u00e1logo */
                    var c = document.getElementById('chk-' + l);
                    c.checked = !c.checked;
                });
                cont.appendChild(chip);
            });
        });
    }

    window.marcarLadas = function (v) {
        document.querySelectorAll('#ld-chips input[type=checkbox]').forEach(function (c) { c.checked = v; });
    };

    window.agregarLada = function () {
        var estado = document.getElementById('ld-estado').value;
        var inp = document.getElementById('ld-nueva-lada');
        var lada = inp.value.trim();
        if (!/^\d{2,3}$/.test(lada)) {
            Swal.fire({ icon:'warning', title:'Lada inv\u00e1lida', text:'Escribe 2 o 3 d\u00edgitos.', confirmButtonColor:'#2384c5' });
            return;
        }
        apiPost({ action: 'agregarLada', estado: estado, lada: lada }).then(function (res) {
            if (res.status === 'ok') {
                inp.value = '';
                cargarEstados(estado).then(function () { cargarLadas(estado); });
            } else {
                Swal.fire({ icon:'error', title:'Error', text:res.msg, confirmButtonColor:'#2384c5' });
            }
        });
    };

    function quitarLada(estado, lada) {
        Swal.fire({
            icon:'warning', title:'\u00bfQuitar la lada ' + lada + '?',
            text:'Se elimina del cat\u00e1logo de ' + estado + '.',
            showCancelButton:true, confirmButtonText:'S\u00ed, quitar', cancelButtonText:'Cancelar',
            confirmButtonColor:'#b31412', cancelButtonColor:'#aaa'
        }).then(function (r) {
            if (!r.isConfirmed) return;
            apiPost({ action: 'quitarLada', estado: estado, lada: lada }).then(function (res) {
                if (res.status === 'ok') cargarEstados(estado).then(function () { cargarLadas(estado); });
                else Swal.fire({ icon:'error', title:'Error', text:res.msg, confirmButtonColor:'#2384c5' });
            });
        });
    }

    window.consultar = function () {
        var estado = document.getElementById('ld-estado').value;
        if (!estado) {
            Swal.fire({ icon:'warning', title:'Selecciona un estado', confirmButtonColor:'#2384c5' });
            return;
        }
        var ladas = [];
        document.querySelectorAll('#ld-chips input[type=checkbox]:checked').forEach(function (c) { ladas.push(c.value); });
        if (!ladas.length) {
            Swal.fire({ icon:'warning', title:'Selecciona al menos una lada', confirmButtonColor:'#2384c5' });
            return;
        }

        var btn = document.getElementById('ld-btn-consultar');
        btn.disabled = true; btn.textContent = 'Consultando...';

        apiPost({
            action: 'consultar', ladas: ladas,
            fecha_ini: document.getElementById('ld-fecha-ini').value,
            fecha_fin: document.getElementById('ld-fecha-fin').value
        }).then(function (res) {
            btn.disabled = false; btn.textContent = 'Consultar';
            if (res.status !== 'ok') {
                Swal.fire({ icon:'error', title:'Error', text:res.msg, confirmButtonColor:'#2384c5' });
                return;
            }
            datos = res.data || [];
            paginaActual = 1;
            document.getElementById('ld-filtro').value = '';
            document.getElementById('ld-resultado').style.display = 'block';
            var txt = '<strong>' + datos.length.toLocaleString('es-MX') + '</strong> folios &middot; ' + esc(estado) +
                      ' &middot; ladas: ' + ladas.join(', ') + ' &middot; ' + esc(res.fecha_ini) + ' a ' + esc(res.fecha_fin);
            if (res.truncado) txt += ' &middot; <span style="color:#c0392b;font-weight:700;">se muestran solo los primeros ' + res.limite.toLocaleString('es-MX') + '; acota las fechas</span>';
            document.getElementById('ld-resumen-txt').innerHTML = txt;
            var dbgBox = document.getElementById('ld-debug');
            if (res.debug) {
                document.getElementById('ld-debug-txt').textContent = JSON.stringify(res.debug, null, 2);
                dbgBox.style.display = 'block';
            } else {
                dbgBox.style.display = 'none';
            }
            renderTabla();
        }).catch(function (err) {
            btn.disabled = false; btn.textContent = 'Consultar';
            Swal.fire({ icon:'error', title:'No se pudo consultar', text: String(err && err.message ? err.message : err), confirmButtonColor:'#2384c5' });
        });
    };

    function filtrados() {
        var q = document.getElementById('ld-filtro').value.trim().toLowerCase();
        if (!q) return datos;
        return datos.filter(function (r) {
            return Object.keys(r).some(function (k) { return String(r[k] == null ? '' : r[k]).toLowerCase().indexOf(q) !== -1; });
        });
    }

    function fmtMoney(v) {
        var n = parseFloat(v);
        return isNaN(n) ? '' : '$' + n.toLocaleString('es-MX', { minimumFractionDigits:2, maximumFractionDigits:2 });
    }

    window.renderTabla = function () {
        var lista = filtrados();
        var tbody = document.getElementById('ld-tbody');
        var totalPag = Math.max(1, Math.ceil(lista.length / POR_PAGINA));
        if (paginaActual > totalPag) paginaActual = totalPag;

        if (!lista.length) {
            tbody.innerHTML = '<tr><td colspan="17" class="ld-vacio">Sin resultados</td></tr>';
        } else {
            var ini = (paginaActual - 1) * POR_PAGINA;
            tbody.innerHTML = lista.slice(ini, ini + POR_PAGINA).map(function (r) {
                return '<tr>' +
                    '<td>' + esc(r.no_empleado) + '</td><td>' + esc(r.clave) + '</td><td>' + esc(r.nombre) + '</td><td>' + esc(r.domicilio) + '</td>' +
                    '<td>' + esc(r.emp_celular) + '</td><td>' + esc(r.emp_telefono) + '</td>' +
                    '<td>' + esc(r.gerencia) + '</td><td>' + esc(r.brocker) + '</td><td>' + esc(r.sc) + '</td>' +
                    '<td>' + esc(r.folio) + '</td><td>' + esc(r.fec_captura) + '</td><td>' + esc(r.nombre_cliente) + '</td>' +
                    '<td>' + esc(r.cliente_telefono) + '</td><td>' + esc(r.cliente_celular) + '</td>' +
                    '<td><span class="ld-badge-lada">' + esc(r.lada) + '</span></td>' +
                    '<td style="text-align:right;">' + fmtMoney(r.saldo_real) + '</td><td>' + esc(r.fec_pago) + '</td>' +
                    '</tr>';
            }).join('');
        }
        document.getElementById('ld-pag-info').textContent = 'P\u00e1gina ' + paginaActual + ' de ' + totalPag + ' (' + lista.length.toLocaleString('es-MX') + ' filas)';
        document.getElementById('ld-pag-prev').disabled = paginaActual <= 1;
        document.getElementById('ld-pag-next').disabled = paginaActual >= totalPag;
    };

    window.cambiarPagina = function (d) { paginaActual += d; renderTabla(); };

    /* Exporta TODO lo filtrado (no solo la p\u00e1gina visible) */
    window.exportarXLSX = function () {
        var lista = filtrados();
        if (!lista.length) {
            Swal.fire({ icon:'info', title:'No hay datos para exportar', confirmButtonColor:'#2384c5' });
            return;
        }
        var filas = lista.map(function (r) {
            return {
                'No. empleado': r.no_empleado, 'Clave': r.clave, 'Nombre': r.nombre, 'Domicilio': r.domicilio,
                'Celular empleado': r.emp_celular, 'Tel\u00e9fono empleado': r.emp_telefono,
                'Gerencia': r.gerencia, 'Brocker': r.brocker, 'SC': r.sc,
                'Folio': r.folio, 'Fec. captura': r.fec_captura, 'Cliente': r.nombre_cliente,
                'Tel\u00e9fono cliente': r.cliente_telefono, 'Celular cliente': r.cliente_celular,
                'Lada': r.lada, 'Saldo real': r.saldo_real === null || r.saldo_real === '' ? '' : parseFloat(r.saldo_real),
                'Fec. pago': r.fec_pago
            };
        });
        var ws = XLSX.utils.json_to_sheet(filas);
        ws['!cols'] = Object.keys(filas[0]).map(function (k) { return { wch: Math.max(k.length + 2, 14) }; });
        var wb = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(wb, ws, 'Ladas');
        var estado = (document.getElementById('ld-estado').value || 'estado').replace(/[^\w]+/g, '_');
        XLSX.writeFile(wb, 'ladas_clientes_' + estado + '_' + new Date().toISOString().split('T')[0] + '.xlsx');
    };

    cargarEstados();

})();
</script>
</html>