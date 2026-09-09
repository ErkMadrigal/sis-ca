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
	<script src="./twitter/typeahead.bundle.js"></script>
	<link rel="stylesheet" href="./css/datepicker.css">
	<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700&family=Syne:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <style>
        #frm-nap { text-align:left; padding:18px 24px; font-family:'Plus Jakarta Sans',sans-serif; font-size:13px; }
        #frm-nap h5 { font-family:'Syne',sans-serif; font-size:15px; font-weight:700; margin:0 0 16px; color:#1a1a2e; border-bottom:2px solid #e8e8e8; padding-bottom:8px; }
        #frm-nap .seccion { margin-bottom:18px; }
        #frm-nap .seccion-titulo { font-size:11px; font-weight:600; letter-spacing:.07em; text-transform:uppercase; color:#888; margin-bottom:10px; text-align:left; }
        #frm-nap label { display:block; font-size:12px; color:#555; margin-bottom:3px; text-align:left; }
        #frm-nap input[type=text],#frm-nap input[type=date],#frm-nap input[type=number] { width:100%; box-sizing:border-box; border:1px solid #d0d0d0; border-radius:5px; padding:6px 10px; font-size:13px; color:#222; background:#fff; outline:none; transition:border .2s; }
        #frm-nap input:focus { border-color:#4a6cf7; }
        #frm-nap input.campo-readonly { background:#f5f5f5; color:#555; cursor:not-allowed; }
        #frm-nap input.calculado { background:#f0f4ff; color:#1a1a2e; font-weight:600; cursor:not-allowed; }
        #frm-nap input.input-error { border-color:#e53e3e !important; background:#fff5f5; }
        #frm-nap input.input-ok    { border-color:#38a169 !important; }
        #frm-nap .row-campos { display:flex; gap:12px; flex-wrap:wrap; }
        #frm-nap .col-1-2 { flex:1 1 calc(50% - 6px); min-width:160px; }
        #frm-nap .col-1-3 { flex:1 1 calc(33% - 8px); min-width:160px; }
        #frm-nap .col-full { flex:1 1 100%; }
        .card-comision { display:flex; align-items:center; background:#fafafa; border:1px solid #e8e8e8; border-radius:8px; padding:12px 16px; margin-bottom:10px; gap:16px; }
        .card-comision-left { flex:1; min-width:0; display:flex; flex-direction:column; gap:2px; }
        .card-rol { font-size:10px; font-weight:700; color:#1a56db; text-transform:uppercase; letter-spacing:.07em; }
        .card-nombre { font-size:13px; font-weight:600; color:#1a1a2e; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
        .card-clave { font-size:11px; color:#888; }
        .card-comision-right { display:flex; gap:12px; align-items:flex-end; flex-shrink:0; }
        .campo-pct,.campo-monto { display:flex; flex-direction:column; gap:3px; }
        .campo-pct input { width:110px; }
        .campo-monto input { width:140px; }
        .pct-hint { font-size:10px; color:#aaa; margin-top:2px; }
        .pct-hint.over { color:#e53e3e; font-weight:600; }
        .pct-hint.ok   { color:#1e6e42; font-weight:600; }
        #breadcrumb-estructura { display:flex; align-items:center; gap:6px; font-size:12px; flex-wrap:wrap; margin-bottom:12px; }
        .bc-item { padding:3px 10px; border-radius:20px; font-size:11px; font-weight:600; letter-spacing:.04em; }
        .bc-gerencia { background:#e6f4ea; color:#1e6e42; }
        .bc-sc       { background:#fce8e6; color:#b31412; }
        .bc-sep      { color:#aaa; font-size:14px; }
        #nota-sc { display:none; font-size:12px; color:#856404; background:#fff3cd; border:1px solid #ffe69c; border-radius:5px; padding:5px 10px; margin-bottom:12px; }
        .ta-wrapper { position:relative; }
        .ta-dropdown { display:none; position:absolute; top:100%; left:0; right:0; z-index:9999; background:#fff; border:1px solid #d0d0d0; border-top:none; border-radius:0 0 6px 6px; max-height:220px; overflow-y:auto; box-shadow:0 6px 16px rgba(0,0,0,.1); }
        .ta-dropdown.open { display:block; }
        .ta-item { padding:8px 12px; cursor:pointer; font-size:13px; border-bottom:1px solid #f0f0f0; display:flex; align-items:center; justify-content:space-between; transition:background .1s; }
        .ta-item:last-child { border-bottom:none; }
        .ta-item:hover,.ta-item.activo { background:#f0f4ff; }
        .ta-item .ta-nombre { flex:1; }
        .ta-item .ta-clave { font-size:11px; color:#fff; background:#1a56db; border-radius:4px; padding:2px 7px; margin-left:8px; white-space:nowrap; }
        .ta-highlight { color:#1a56db; font-weight:600; }
        .ta-vacio     { padding:10px 12px; font-size:13px; color:#aaa; text-align:center; }
        .ta-cargando  { padding:10px 12px; font-size:13px; color:#888; text-align:center; }
        .badge-sel { display:inline-flex; align-items:center; gap:5px; background:#e8f0fe; color:#1a56db; font-size:12px; padding:4px 12px; border-radius:20px; margin-top:6px; border:1px solid #c0d4fc; }
        .badge-sel .ta-x { cursor:pointer; font-weight:700; margin-left:4px; opacity:.5; font-size:14px; }
        .badge-sel .ta-x:hover { opacity:1; color:#c0392b; }
        #loader-estructura { display:none; font-size:12px; color:#888; margin-top:6px; align-items:center; gap:6px; }
        #btn-guardar { width:100%; padding:11px; margin-top:8px; background:#2384c5; color:#fff; border:none; border-radius:6px; font-size:14px; font-weight:600; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:background .2s; }
        #btn-guardar:hover    { background:#1648c0; }
        #btn-guardar:disabled { background:#aaa; cursor:not-allowed; }
        .spinner { display:inline-block; width:14px; height:14px; border:2px solid #ccc; border-top-color:#1a56db; border-radius:50%; animation:nap-spin .7s linear infinite; vertical-align:-3px; margin-right:4px; }
        @keyframes nap-spin { to { transform:rotate(360deg); } }
                /* ── MODAL ASESOR EXTERNO ── */
        #modal-ext-overlay {
            display:none; position:fixed; inset:0;
            background:rgba(15,23,42,.6); z-index:99999;
            justify-content:center; align-items:center;
            padding:1.5rem; backdrop-filter:blur(4px);
        }
        #modal-ext-overlay.open { display:flex; }
        .modal-ext-content {
            width:100%; max-width:520px; max-height:90vh;
            background:#fff; border-radius:16px;
            display:flex; flex-direction:column; overflow:hidden;
            animation:extModalIn .2s ease;
        }
        @keyframes extModalIn {
            from { transform:translateY(12px) scale(.98); opacity:0; }
            to   { transform:translateY(0)    scale(1);   opacity:1; }
        }
        .modal-ext-header {
            padding:18px 22px 14px;
            border-bottom:1px solid #e8e8e8;
            display:flex; justify-content:space-between; align-items:center;
        }
        .modal-ext-header h4 {
            margin:0; font-family:'Syne',sans-serif;
            font-size:15px; font-weight:700; color:#1a1a2e;
        }
        .modal-ext-body {
            flex:1; overflow-y:auto;
            padding:18px 22px;
            display:flex; flex-direction:column; gap:14px;
        }
        .modal-ext-footer {
            padding:14px 22px;
            border-top:1px solid #e8e8e8;
            display:flex; gap:10px; justify-content:flex-end;
            background:#f8fafc;
        }
        .modal-ext-footer button {
            border:none; padding:9px 20px; border-radius:8px;
            cursor:pointer; font-size:13px; font-weight:600;
            font-family:'Plus Jakarta Sans',sans-serif; transition:.15s;
        }
        .btn-ext-cancelar { background:#f1f5f9; color:#64748b; }
        .btn-ext-guardar  { background:#2384c5; color:#fff; }
        .btn-ext-guardar:hover  { background:#1a6fa8; }
        .btn-ext-cancelar:hover { background:#e2e8f0; }

        /* Input banco detectado */
        .banco-detectado-wrap {
            display:flex; align-items:center; gap:8px;
            background:#e8f4fd; border:1px solid #c0d4fc;
            border-radius:8px; padding:8px 12px; margin-top:6px;
            font-size:13px; color:#1a56db; font-weight:600;
            display:none;
        }
        .banco-detectado-wrap .bd-nombre { flex:1; }
        .banco-detectado-wrap .bd-codigo {
            background:#1a56db; color:#fff;
            border-radius:4px; padding:2px 8px; font-size:11px;
        }

        /* Btn + junto al typeahead */
        #btn-agregar-ext {
            display:none;
            width:32px; height:32px; border-radius:50%;
            background:#2384c5; color:#fff; border:none;
            font-size:18px; font-weight:700; cursor:pointer;
            flex-shrink:0; transition:.15s; line-height:1;
            align-items:center; justify-content:center;
        }
        #btn-agregar-ext:hover { background:#1a6fa8; }
        #btn-agregar-ext.visible { display:flex; }

        /* Wrapper asesor con botón + */
        .asesor-input-wrap {
            display:flex; gap:8px; align-items:flex-start;
        }
        .asesor-input-wrap .ta-wrapper { flex:1; }

        /* Badge externo */
        .badge-ext-tag {
            background:#fff3cd; border:1px solid #ffe69c;
            color:#856404; border-radius:4px;
            padding:1px 7px; font-size:10px; font-weight:700;
            margin-left:6px; text-transform:uppercase;
        }

        /* Select banco en modal */
        .ext-select {
            width:100%; border:1px solid #d0d0d0; border-radius:5px;
            padding:7px 10px; font-size:13px; color:#222;
            background:#fff; outline:none;
            font-family:'Plus Jakarta Sans',sans-serif;
        }
        .ext-select:focus { border-color:#4a6cf7; }

        /* clabe hint */
        #hint-clabe-ext {
            font-size:11px; margin-top:3px;
            display:none;
        }
        #hint-clabe-ext.ok  { color:#1e6e42; display:block; }
        #hint-clabe-ext.err { color:#e53e3e; display:block; }

        .swal2-container {
            z-index: 9999999 !important;
        }
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
            titulo("Captura de prestamos Bancrea");
            $pag = "BANCREA"; 
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
        $conecta  = new conector();
        $fecha    = addslashes(trim($_POST["fecha"]    ?? ''));
        $mensaje  = addslashes(trim($_POST["mensaje"]  ?? ''));
        $dispersa = addslashes(trim($_POST["dispersa"] ?? ''));
        $nivel    = nivel_usuario(utf8_decode($_SESSION["nombre"]));
?>
    <div class="container" style="padding:20px;">
        <div id="frm-nap">

            <h5>&#128196; Captura de comprobante</h5>

            <!-- FOLIO + FECHA -->
            <div class="seccion">
                <div class="row-campos">
                    <div class="col-1-2">
                        <label>Folio <small style="color:#aaa;">(10–15 caracteres)</small></label>
                        <input type="text" id="folio" placeholder="Escribe el folio..."
                            minlength="10" maxlength="15" oninput="validarFolio(this)" />
                        <span class="pct-hint" id="hint_folio"></span>
                    </div>
                    <div class="col-1-2">
                        <label>Fecha de captura</label>
                        <input type="date" id="fec_captura" />
                    </div>
                </div>
            </div>

            <!-- CLIENTE -->
            <div class="seccion">
                <div class="seccion-titulo">Cliente</div>
                <div class="row-campos">
                    <div class="col-full">
                        <label>Nombre del cliente</label>
                        <input type="text" id="cliente" placeholder="Escribe el nombre del cliente..." />
                    </div>
                </div>
            </div>

            <!-- SALDO -->
            <div class="seccion">
                <div class="seccion-titulo">Saldo</div>
                <div class="row-campos">
                    <div class="col-1-2">
                        <label>Saldo $</label>
                        <input type="number" id="saldo" placeholder="0.00" step="0.01" min="0" oninput="recalcular()" />
                    </div>
                </div>
            </div>

            <!-- ASESOR -->
            <div class="seccion">
                <div class="seccion-titulo">Asesor</div>
                <div class="row-campos">
                    <div class="col-full">
                        <label>Buscar asesor</label>
                        <div class="asesor-input-wrap">
                            <div class="ta-wrapper">
                                <input type="text" id="asesor_search"
                                    placeholder="Escribe nombre del asesor..."
                                    autocomplete="off" />
                                <div class="ta-dropdown" id="asesor_dropdown"></div>
                            </div>
                            <button id="btn-agregar-ext"
                                    title="Agregar asesor externo"
                                    onclick="abrirModalExterno()">+</button>
                        </div>
                        <div id="asesor_badge_wrap" style="display:none;">
                            <span class="badge-sel">
                                <span id="asesor_badge_txt"></span>
                                <span class="ta-x" onclick="limpiarAsesor()">&#x2715;</span>
                            </span>
                        </div>
                        <input type="hidden" id="asesor_clave" />
                        <input type="hidden" id="asesor_es_externo" value="0" />
                        <div id="loader-estructura" style="display:none;">
                            <span class="spinner"></span> Cargando estructura...
                        </div>
                    </div>
                </div>
            </div>

            <!-- ESTRUCTURA + COMISIONES -->
            <div id="sec-estructura-comisiones" style="display:none;">
                <div class="seccion">
                    <div class="seccion-titulo">Estructura y comisiones</div>
                    <div id="breadcrumb-estructura" style="display:none;">
                        <span class="bc-item bc-gerencia" id="bc_gerencia"></span>
                        <span class="bc-sep">›</span>
                        <span class="bc-item bc-sc" id="bc_sc"></span>
                    </div>
                    <div id="nota-sc">&#9888; SC vacío — se usa broker como SC</div>

                    <!-- ASESOR max 10% -->
                    <div class="card-comision">
                        <div class="card-comision-left">
                            <span class="card-rol">Asesor</span>
                            <span class="card-nombre" id="asesor_nombre_display"></span>
                            <span class="card-clave"  id="asesor_clave_display"></span>
                        </div>
                        <div class="card-comision-right">
                            <div class="campo-pct">
                                <label>% Comisión <small style="color:#aaa;">(máx 10%)</small></label>
                                <input type="number" id="porcen_ase" placeholder="0.00" step="0.01" min="0" max="10" oninput="recalcular()" />
                                <span class="pct-hint" id="hint_ase"></span>
                            </div>
                            <div class="campo-monto">
                                <label>Comisión $</label>
                                <input type="text" id="comision_ase" class="calculado" readonly placeholder="—" />
                            </div>
                        </div>
                    </div>

                    <!-- GERENTE max 5% -->
                    <div class="card-comision">
                        <div class="card-comision-left">
                            <span class="card-rol">Gerente</span>
                            <span class="card-nombre" id="gerente_nombre_display"></span>
                            <span class="card-clave"  id="gerente_clave_display"></span>
                            <input type="hidden" id="gerente" />
                        </div>
                        <div class="card-comision-right">
                            <div class="campo-pct">
                                <label>% Comisión <small style="color:#aaa;">(máx 5%)</small></label>
                                <input type="number" id="porcen_ger" placeholder="0.00" step="0.01" min="0" max="5" oninput="recalcular()" />
                                <span class="pct-hint" id="hint_ger"></span>
                            </div>
                            <div class="campo-monto">
                                <label>Comisión $</label>
                                <input type="text" id="comision_ger" class="calculado" readonly placeholder="—" />
                            </div>
                        </div>
                    </div>

                    <!-- SC / BROKER max 5% -->
                    <div class="card-comision">
                        <div class="card-comision-left">
                            <span class="card-rol" id="titulo-sc">SC</span>
                            <span class="card-nombre" id="sc_nombre_display"></span>
                            <span class="card-clave"  id="sc_clave_display"></span>
                            <input type="hidden" id="sc" />
                        </div>
                        <div class="card-comision-right">
                            <div class="campo-pct">
                                <label>% Comisión <small style="color:#aaa;">(máx 5%)</small></label>
                                <input type="number" id="porcen_sc" placeholder="0.00" step="0.01" min="0" max="5" oninput="recalcular()" />
                                <span class="pct-hint" id="hint_sc"></span>
                            </div>
                            <div class="campo-monto">
                                <label>Comisión $</label>
                                <input type="text" id="comision_sc" class="calculado" readonly placeholder="—" />
                            </div>
                        </div>
                    </div>

                </div>
            </div>

            <button id="btn-guardar" onclick="guardarComprobante()">Guardar comprobante</button>

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

    <!-- MODAL ASESOR EXTERNO -->
    <div id="modal-ext-overlay">
        <div class="modal-ext-content">

            <div class="modal-ext-header">
                <h4>➕ Agregar asesor externo</h4>
                <button onclick="cerrarModalExterno()"
                        style="background:none;border:none;font-size:18px;
                            cursor:pointer;color:#888;line-height:1;">✕</button>
            </div>

            <div class="modal-ext-body">

                <!-- BUSCAR EXISTENTE -->
                <div id="ext-wrap-buscar-existente">
                    <label>Buscar en externos registrados</label>
                    <div class="ta-wrapper">
                        <input type="text" id="ext_buscar_existente"
                            placeholder="Escribe para buscar..."
                            autocomplete="off"
                            oninput="onBuscarExternoExistente(this)"
                            style="width:100%;box-sizing:border-box;border:1px solid #d0d0d0;
                                    border-radius:5px;padding:7px 10px;font-size:13px;
                                    outline:none;font-family:'Plus Jakarta Sans',sans-serif;" />
                        <div class="ta-dropdown" id="ext_existente_dropdown"></div>
                    </div>
                    <div id="ext_existente_badge" style="display:none;margin-top:6px;">
                        <span class="badge-sel">
                            <span id="ext_existente_badge_txt"></span>
                            <span class="ta-x" onclick="limpiarExternoExistente()">&#x2715;</span>
                        </span>
                    </div>
                    <input type="hidden" id="ext_existente_id" />
                    <div style="display:flex;align-items:center;gap:8px;margin:14px 0 0;">
                        <div style="flex:1;height:1px;background:#e8e8e8;"></div>
                        <span style="font-size:11px;color:#aaa;font-weight:600;">O REGISTRAR NUEVO</span>
                        <div style="flex:1;height:1px;background:#e8e8e8;"></div>
                    </div>
                </div>

                <!-- NOMBRE -->
                <div>
                    <label>Nombre completo</label>
                    <input type="text" id="ext_nombre"
                        placeholder="Nombre..."
                        style="width:100%;box-sizing:border-box;border:1px solid #d0d0d0;
                                border-radius:5px;padding:7px 10px;font-size:13px;
                                outline:none;font-family:'Plus Jakarta Sans',sans-serif;" />
                </div>

                <!-- CLABE -->
                <div>
                    <label>CLABE interbancaria <small style="color:#aaa;">(18 dígitos)</small></label>
                    <input type="text" id="ext_clabe"
                        placeholder="000000000000000000"
                        maxlength="18"
                        oninput="onClabeInput(this)"
                        style="width:100%;box-sizing:border-box;border:1px solid #d0d0d0;
                                border-radius:5px;padding:7px 10px;font-size:13px;
                                outline:none;font-family:'Plus Jakarta Sans',sans-serif;
                                letter-spacing:.08em;" />
                    <span id="hint-clabe-ext"></span>
                    <div class="banco-detectado-wrap" id="banco-detectado-wrap">
                        <span class="bd-nombre" id="banco-detectado-nombre"></span>
                        <span class="bd-codigo" id="banco-detectado-codigo"></span>
                    </div>
                </div>

                <!-- BANCO SELECT -->
                <div id="banco-select-wrap">
                    <label>Banco <small style="color:#aaa;" id="banco-select-hint">(se detecta por CLABE)</small></label>
                    <select id="ext_id_banco" class="ext-select" onchange="onBancoManualChange()">
                        <option value="">— Selecciona banco —</option>
                    </select>
                </div>

            </div>

            <div class="modal-ext-footer">
                <button class="btn-ext-cancelar" onclick="cerrarModalExterno()">Cancelar</button>
                <button class="btn-ext-guardar"  onclick="guardarAsesorExterno()">
                    Guardar asesor
                </button>
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

    var API = 'ventas_bancrea_api.php';

    document.getElementById('fec_captura').value = new Date().toISOString().split('T')[0];

    /* bloquear -, e, E, + en inputs numéricos */
    document.querySelectorAll('input[type=number]').forEach(function (inp) {
        inp.addEventListener('keydown', function (e) {
            if (e.key === '-' || e.key === 'e' || e.key === 'E' || e.key === '+') e.preventDefault();
        });
        inp.addEventListener('input', function () {
            if (parseFloat(this.value) < 0) this.value = 0;
        });
    });

    /* validación en tiempo real del folio */
    window.validarFolio = function (inp) {
        var hint = document.getElementById('hint_folio');
        var len  = inp.value.length;
        if (len === 0) {
            hint.textContent = ''; hint.className = 'pct-hint';
            inp.classList.remove('input-error', 'input-ok');
        } else if (len < 10) {
            hint.textContent = len + ' / 10 mín — faltan ' + (10 - len);
            hint.className = 'pct-hint over';
            inp.classList.add('input-error'); inp.classList.remove('input-ok');
        } else {
            hint.textContent = len + ' / 15 ✓';
            hint.className = 'pct-hint ok';
            inp.classList.remove('input-error'); inp.classList.add('input-ok');
        }
    };

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
        return texto.replace(re, '<span class="ta-highlight">$1</span>');
    }

    /* ── TYPEAHEAD ASESOR ── */
    var debounceAsesor = null;
    var inputAsesor    = document.getElementById('asesor_search');
    var dropAsesor     = document.getElementById('asesor_dropdown');

    inputAsesor.addEventListener('keyup', function (e) {
        if ([13,27,38,40].indexOf(e.keyCode) !== -1) return;
        clearTimeout(debounceAsesor);
        var q = this.value.trim();
        if (q.length < 2) { dropAsesor.classList.remove('open'); dropAsesor.innerHTML = ''; return; }
        dropAsesor.innerHTML = '<div class="ta-cargando"><span class="spinner"></span> Buscando...</div>';
        dropAsesor.classList.add('open');
        debounceAsesor = setTimeout(function () { fetchAsesores(q); }, 350);
    });

    inputAsesor.addEventListener('keydown', function (e) {
        var items = dropAsesor.querySelectorAll('.ta-item');
        if (!items.length) return;
        var activo = dropAsesor.querySelector('.ta-item.activo');
        var idx = -1;
        items.forEach(function (el, i) { if (el === activo) idx = i; });
        if (e.keyCode === 40) { e.preventDefault(); if (activo) activo.classList.remove('activo'); (items[idx+1]||items[0]).classList.add('activo'); }
        else if (e.keyCode === 38) { e.preventDefault(); if (activo) activo.classList.remove('activo'); (items[idx-1]||items[items.length-1]).classList.add('activo'); }
        else if (e.keyCode === 13 && activo) { e.preventDefault(); activo.dispatchEvent(new Event('mousedown')); }
        else if (e.keyCode === 27) { dropAsesor.classList.remove('open'); }
    });

    inputAsesor.addEventListener('blur', function () {
        setTimeout(function () { dropAsesor.classList.remove('open'); }, 200);
    });

    function fetchAsesores(q) {
        apiPost({ action: 'buscarAsesor', q: q }).then(function (res) {
            dropAsesor.innerHTML = '';
            if (!res.data || res.data.length === 0) {
                dropAsesor.innerHTML = '<div class="ta-vacio">Sin resultados para "' + q + '"</div>';
                dropAsesor.classList.add('open'); return;
            }
            res.data.forEach(function (item) {
                var d = document.createElement('div');
                d.className = 'ta-item';
                d.innerHTML = '<span class="ta-nombre">' + resaltar(item.nombre, q) + '</span><span class="ta-clave">' + item.clave + '</span>';
                d.addEventListener('mousedown', function () { seleccionarAsesor(item); });
                dropAsesor.appendChild(d);
            });
            dropAsesor.classList.add('open');
        }).catch(function () { dropAsesor.innerHTML = '<div class="ta-vacio">Error al buscar</div>'; });
    }

    function seleccionarAsesor(item) {
        inputAsesor.value = item.nombre;
        document.getElementById('asesor_clave').value = item.clave;
        document.getElementById('asesor_badge_txt').textContent = item.nombre + ' (' + item.clave + ')';
        document.getElementById('asesor_badge_wrap').style.display = 'block';
        dropAsesor.classList.remove('open');
        cargarEstructura(item.clave);
    }

    window.limpiarAsesor = function () {
        inputAsesor.value = '';
        document.getElementById('asesor_clave').value                      = '';
        document.getElementById('asesor_badge_wrap').style.display         = 'none';
        document.getElementById('sec-estructura-comisiones').style.display = 'none';
        document.getElementById('loader-estructura').style.display         = 'none';
        document.getElementById('gerente').value                           = '';
        document.getElementById('gerente_nombre_display').textContent      = '';
        document.getElementById('gerente_clave_display').textContent       = '';
        document.getElementById('sc').value                                = '';
        document.getElementById('sc_nombre_display').textContent           = '';
        document.getElementById('sc_clave_display').textContent            = '';
        document.getElementById('asesor_nombre_display').textContent       = '';
        document.getElementById('asesor_clave_display').textContent        = '';
        document.getElementById('nota-sc').style.display                   = 'none';
        document.getElementById('titulo-sc').textContent                   = 'SC';
        document.getElementById('breadcrumb-estructura').style.display     = 'none';
        document.getElementById('bc_gerencia').textContent                 = '';
        document.getElementById('bc_sc').textContent                       = '';
        limpiarComisiones();
        dropAsesor.innerHTML = '';
        dropAsesor.classList.remove('open');
    };

    /* ── CARGAR ESTRUCTURA ── */
        window.cargarEstructura = function (clave) {
        document.getElementById('sec-estructura-comisiones').style.display = 'none';
        document.getElementById('loader-estructura').style.display         = 'flex';

        apiPost({ action: 'getEstructura', clave: clave }).then(function (res) {

            document.getElementById('loader-estructura').style.display = 'none';

            if (res.status !== 'ok') {
                Swal.fire({ icon: 'warning', title: 'Error', text: res.msg, confirmButtonColor: '#1a56db' });
                return;
            }

            /* Sin estructura — viaja igual, solo muestra asesor */
            if (res.sin_estructura) {
                document.getElementById('asesor_nombre_display').textContent = inputAsesor.value;
                document.getElementById('asesor_clave_display').textContent  = document.getElementById('asesor_clave').value;
                document.getElementById('gerente').value                      = '';
                document.getElementById('sc').value                           = '';
                document.getElementById('breadcrumb-estructura').style.display = 'none';
                document.getElementById('nota-sc').style.display              = 'none';
                document.getElementById('sec-estructura-comisiones').style.display = 'block';

                /* Quitar btns anteriores */
                var btnGerOld = document.getElementById('btn-add-ger');
                var btnScOld  = document.getElementById('btn-add-sc');
                if (btnGerOld) btnGerOld.remove();
                if (btnScOld)  btnScOld.remove();

                /* Mostrar "Sin gerente" con btn + */
                var cardGerLeft = document.getElementById('gerente_nombre_display').closest('.card-comision-left');
                document.getElementById('gerente_nombre_display').textContent = '—';
                document.getElementById('gerente_clave_display').textContent  = '';

                var btnGer = document.createElement('button');
                btnGer.id        = 'btn-add-ger';
                btnGer.innerHTML = '+ Agregar gerente externo';
                btnGer.style.cssText =
                    'margin-top:4px;background:#fff3cd;color:#856404;border:1px solid #ffe69c;' +
                    'border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;' +
                    'cursor:pointer;font-family:inherit;';
                btnGer.addEventListener('click', function(){
                    abrirModalExternoParaRol('gerente', '');
                });
                cardGerLeft.appendChild(btnGer);

                /* Mostrar "Sin SC" con btn + */
                var cardScLeft = document.getElementById('sc_nombre_display').closest('.card-comision-left');
                document.getElementById('sc_nombre_display').textContent = '—';
                document.getElementById('sc_clave_display').textContent  = '';

                var btnSc = document.createElement('button');
                btnSc.id        = 'btn-add-sc';
                btnSc.innerHTML = '+ Agregar SC externo';
                btnSc.style.cssText =
                    'margin-top:4px;background:#fff3cd;color:#856404;border:1px solid #ffe69c;' +
                    'border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;' +
                    'cursor:pointer;font-family:inherit;';
                btnSc.addEventListener('click', function(){
                    abrirModalExternoParaRol('sc', '');
                });
                cardScLeft.appendChild(btnSc);

                document.getElementById('porcen_ase').focus();
                return;
            }

            document.getElementById('asesor_nombre_display').textContent = inputAsesor.value;
            document.getElementById('asesor_clave_display').textContent  = document.getElementById('asesor_clave').value;

            /* GERENTE */
            document.getElementById('gerente').value = res.gerencia;
            document.getElementById('gerente_nombre_display').textContent = res.nombre_ger || res.gerencia;
            document.getElementById('gerente_clave_display').textContent  = res.gerencia;

            /* Btn + gerente si no encontrado */
            var btnGerOld = document.getElementById('btn-add-ger');
            if (btnGerOld) btnGerOld.remove();

            if (!res.ger_encontrado && res.nombre_ger) {
                var cardGerLeft = document.getElementById('gerente_nombre_display').closest('.card-comision-left');
                var btnGer = document.createElement('button');
                btnGer.id        = 'btn-add-ger';
                btnGer.title     = 'Agregar ' + res.nombre_ger + ' como externo';
                btnGer.innerHTML = '+ Agregar externo';
                btnGer.style.cssText =
                    'margin-top:4px;background:#fff3cd;color:#856404;border:1px solid #ffe69c;' +
                    'border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;' +
                    'cursor:pointer;font-family:inherit;';
                btnGer.addEventListener('click', function(){
                    abrirModalExternoParaRol('gerente', res.nombre_ger);
                });
                cardGerLeft.appendChild(btnGer);
            }

            /* SC / BROKER */
            document.getElementById('sc').value = res.sc_final;
            document.getElementById('sc_nombre_display').textContent =
                res.sc_origen === 'brocker'
                    ? (res.nombre_brk || res.brocker)
                    : (res.nombre_sc  || res.sc_raw);
            document.getElementById('sc_clave_display').textContent = res.sc_final;

            if (res.sc_origen === 'brocker') {
                document.getElementById('nota-sc').style.display = 'block';
                document.getElementById('titulo-sc').textContent = 'Broker';
            } else {
                document.getElementById('nota-sc').style.display = 'none';
                document.getElementById('titulo-sc').textContent = 'SC';
            }

            /* Btn + SC si no encontrado */
            var btnScOld = document.getElementById('btn-add-sc');
            if (btnScOld) btnScOld.remove();

            var nombreScDisplay = res.sc_origen === 'brocker' ? res.nombre_brk : res.nombre_sc;

            if (!res.sc_encontrado && nombreScDisplay) {
                var cardScLeft = document.getElementById('sc_nombre_display').closest('.card-comision-left');
                var btnSc = document.createElement('button');
                btnSc.id        = 'btn-add-sc';
                btnSc.title     = 'Agregar ' + nombreScDisplay + ' como externo';
                btnSc.innerHTML = '+ Agregar externo';
                btnSc.style.cssText =
                    'margin-top:4px;background:#fff3cd;color:#856404;border:1px solid #ffe69c;' +
                    'border-radius:6px;padding:3px 10px;font-size:11px;font-weight:600;' +
                    'cursor:pointer;font-family:inherit;';
                btnSc.addEventListener('click', function(){
                    abrirModalExternoParaRol('sc', nombreScDisplay);
                });
                cardScLeft.appendChild(btnSc);
            }

            document.getElementById('bc_gerencia').textContent =
                'Gerencia: ' + (res.nombre_ger || res.gerencia);
            document.getElementById('bc_sc').textContent =
                (res.sc_origen === 'brocker' ? 'Broker: ' : 'SC: ') +
                (res.sc_origen === 'brocker' ? (res.nombre_brk || res.brocker) : (res.nombre_sc || res.sc_raw));

            document.getElementById('breadcrumb-estructura').style.display = 'flex';
            document.getElementById('sec-estructura-comisiones').style.display = 'block';
            document.getElementById('porcen_ase').focus();
            recalcular();

        }).catch(function (err) {
            document.getElementById('loader-estructura').style.display = 'none';
            Swal.fire({ icon: 'error', title: 'Error', text: String(err), confirmButtonColor: '#1a56db' });
        });
    };

    /* ── CÁLCULO + VALIDACIÓN % ── */
    window.recalcular = function () {
        var saldo  = parseFloat(document.getElementById('saldo').value) || 0;
        var campos = [
            { id:'porcen_ase', max:10, comision:'comision_ase', hint:'hint_ase' },
            { id:'porcen_ger', max:5,  comision:'comision_ger', hint:'hint_ger' },
            { id:'porcen_sc',  max:5,  comision:'comision_sc',  hint:'hint_sc'  }
        ];
        campos.forEach(function (c) {
            var inp      = document.getElementById(c.id);
            var hint     = document.getElementById(c.hint);
            var pct      = parseFloat(inp.value) || 0;
            var negat    = pct < 0;
            var over     = pct > c.max;
            var invalido = negat || over;
            inp.classList.toggle('input-error', invalido);
            if (negat)       { hint.textContent = 'No se permiten negativos'; hint.className = 'pct-hint over'; }
            else if (over)   { hint.textContent = 'Máximo ' + c.max + '%';    hint.className = 'pct-hint over'; }
            else             { hint.textContent = '';                          hint.className = 'pct-hint'; }
            var monto = invalido ? 0 : saldo * pct / 100;
            document.getElementById(c.comision).value = monto > 0 ? '$' + monto.toLocaleString('es-MX', {minimumFractionDigits:2, maximumFractionDigits:2}) : '';
        });
    };

    function limpiarComisiones() {
        ['porcen_ase','porcen_ger','porcen_sc','comision_ase','comision_ger','comision_sc'].forEach(function (id) { document.getElementById(id).value = ''; });
        ['hint_ase','hint_ger','hint_sc'].forEach(function (id) { document.getElementById(id).textContent = ''; document.getElementById(id).className = 'pct-hint'; });
        ['porcen_ase','porcen_ger','porcen_sc'].forEach(function (id) { document.getElementById(id).classList.remove('input-error'); });
    }

    /* ── GUARDAR ── */
    window.guardarComprobante = function () {
        var folio       = document.getElementById('folio').value.trim();
        var fec_captura = document.getElementById('fec_captura').value.trim();
        var cliente     = document.getElementById('cliente').value.trim();
        var saldo       = parseFloat(document.getElementById('saldo').value) || 0;
        var asesor      = document.getElementById('asesor_clave').value.trim();
        var porcen_ase  = parseFloat(document.getElementById('porcen_ase').value) || 0;
        var porcen_ger  = parseFloat(document.getElementById('porcen_ger').value) || 0;
        var porcen_sc   = parseFloat(document.getElementById('porcen_sc').value)  || 0;
        var gerente     = document.getElementById('gerente').value.trim();
        var sc          = document.getElementById('sc').value.trim();

        if (!folio)               { Swal.fire({ icon:'warning', title:'Campo requerido', text:'Ingresa el folio.',                         confirmButtonColor:'#1a56db' }); return; }
        if (folio.length < 10)    { Swal.fire({ icon:'warning', title:'Folio inválido',  text:'El folio debe tener mínimo 10 caracteres.', confirmButtonColor:'#1a56db' }); return; }
        if (folio.length > 15)    { Swal.fire({ icon:'warning', title:'Folio inválido',  text:'El folio no puede superar 15 caracteres.',  confirmButtonColor:'#1a56db' }); return; }
        if (!cliente)             { Swal.fire({ icon:'warning', title:'Campo requerido', text:'Ingresa el nombre del cliente.',            confirmButtonColor:'#1a56db' }); return; }
        if (saldo <= 0)           { Swal.fire({ icon:'warning', title:'Saldo inválido',  text:'El saldo debe ser mayor a 0.',              confirmButtonColor:'#1a56db' }); return; }
        if (!asesor)              { Swal.fire({ icon:'warning', title:'Campo requerido', text:'Selecciona un asesor.',                     confirmButtonColor:'#1a56db' }); return; }
        if (porcen_ase < 0 || porcen_ger < 0 || porcen_sc < 0) { Swal.fire({ icon:'error', title:'% inválido', text:'Los porcentajes no pueden ser negativos.', confirmButtonColor:'#1a56db' }); return; }
        if (porcen_ase > 10)      { Swal.fire({ icon:'error', title:'% inválido', text:'El porcentaje del asesor no puede superar el 10%.', confirmButtonColor:'#1a56db' }); return; }
        if (porcen_ger > 5)       { Swal.fire({ icon:'error', title:'% inválido', text:'El porcentaje del gerente no puede superar el 5%.', confirmButtonColor:'#1a56db' }); return; }
        if (porcen_sc  > 5)       { Swal.fire({ icon:'error', title:'% inválido', text:'El porcentaje del SC/broker no puede superar el 5%.', confirmButtonColor:'#1a56db' }); return; }

        var comision_ase = parseFloat((saldo * porcen_ase / 100).toFixed(2));
        var comision_ger = parseFloat((saldo * porcen_ger / 100).toFixed(2));
        var comision_sc  = parseFloat((saldo * porcen_sc  / 100).toFixed(2));

        var btn = document.getElementById('btn-guardar');
        btn.disabled = true; btn.innerHTML = '<span class="spinner"></span> Guardando...';

        apiPost({
            action:'insertarComprobante', folio:folio, fec_captura:fec_captura,
            cliente:cliente, saldo:saldo, asesor:asesor,
            porcen_ase:porcen_ase, comision_ase:comision_ase,
            gerente:gerente, porcen_ger:porcen_ger, comision_ger:comision_ger,
            sc:sc, porcen_sc:porcen_sc, comision_sc:comision_sc
        }).then(function (res) {
            btn.disabled = false; btn.innerHTML = 'Guardar comprobante';
            if (res.status === 'ok') {
                Swal.fire({ icon:'success', title:'¡Guardado!', text:'Comprobante registrado', confirmButtonColor:'#1a56db' })
                    .then(function () { limpiarFormulario(); });
            } else {
                Swal.fire({ icon:'error', title:'Error', text:res.msg, confirmButtonColor:'#2384c5' });
            }
        }).catch(function () {
            btn.disabled = false; btn.innerHTML = 'Guardar comprobante';
            Swal.fire({ icon:'error', title:'Error de conexión', text:'No se pudo conectar con el servidor.', confirmButtonColor:'#1a56db' });
        });
    };

    /* ── LIMPIAR TODO ── */
    function limpiarFormulario() {
        var fi = document.getElementById('folio');
        fi.value = ''; fi.classList.remove('input-error','input-ok');
        document.getElementById('hint_folio').textContent = '';
        document.getElementById('hint_folio').className   = 'pct-hint';
        document.getElementById('fec_captura').value = new Date().toISOString().split('T')[0];
        document.getElementById('cliente').value = '';
        document.getElementById('saldo').value   = '';
        limpiarAsesor();
    }

    

    /* ── BUSCAR EXTERNO EXISTENTE ── */
    var debExtExistente = null;

    window.onBuscarExternoExistente = function(inp){
        clearTimeout(debExtExistente);
        var q    = inp.value.trim();
        var drop = document.getElementById('ext_existente_dropdown');
        var rol  = document.getElementById('modal-ext-overlay').dataset.rol || 'asesor';

        if(q.length < 2){ drop.classList.remove('open'); return; }

        debExtExistente = setTimeout(function(){

            /* Si es asesor busca solo en externos, si es gerente/sc busca en ambos */
            var accion = (rol === 'asesor') ? 'buscarAsesorExterno' : 'buscarParaRol';
            var payload = (rol === 'asesor')
                ? { action: accion, q: q }
                : { action: accion, q: q, rol: rol };

            apiPost(payload).then(function(res){
                drop.innerHTML = '';
                if(!res.data || res.data.length === 0){
                    drop.innerHTML = '<div class="ta-vacio">Sin resultados — registra nuevo abajo</div>';
                    drop.classList.add('open'); return;
                }
                res.data.forEach(function(item){
                    var d = document.createElement('div');
                    d.className = 'ta-item';
                    var tag = item.externo
                        ? '<span class="badge-ext-tag">Externo</span>'
                        : '<span style="font-size:10px;color:#fff;background:#38a169;border-radius:4px;padding:1px 7px;margin-left:6px;font-weight:700;">Interno</span>';
                    d.innerHTML = '<span class="ta-nombre">' + resaltar(item.nombre, q) + tag + '</span>';
                    if(item.externo){
                        d.innerHTML += '<span class="ta-clave">EXT_' + item.id + '</span>';
                    }
                    d.addEventListener('mousedown', function(){
                        if(item.externo){
                            seleccionarExternoExistente(item);
                        } else {
                            /* Interno — asignar directo por nombre */
                            seleccionarInternoParaRol(item.nombre);
                        }
                    });
                    drop.appendChild(d);
                });
                drop.classList.add('open');
            });
        }, 300);
    };

    /* Seleccionar interno (nombre) para gerente/SC */
    function seleccionarInternoParaRol(nombre){
        document.getElementById('ext_existente_id').value              = 'INTERNO';
        document.getElementById('ext_existente_badge_txt').textContent = nombre + ' (Interno)';
        document.getElementById('ext_existente_badge').style.display   = 'block';
        document.getElementById('ext_buscar_existente').value          = '';
        document.getElementById('ext_existente_dropdown').classList.remove('open');
        /* Guardar nombre para asignar */
        document.getElementById('ext_existente_badge').dataset.nombre = nombre;
    }

    document.getElementById('ext_buscar_existente').addEventListener('blur', function(){
        setTimeout(function(){
            document.getElementById('ext_existente_dropdown').classList.remove('open');
        }, 200);
    });

    function seleccionarExternoExistente(item){
        document.getElementById('ext_existente_id').value              = item.id;
        document.getElementById('ext_existente_badge_txt').textContent = item.nombre + ' (EXT_' + item.id + ')';
        document.getElementById('ext_existente_badge').style.display   = 'block';
        document.getElementById('ext_buscar_existente').value          = '';
        document.getElementById('ext_existente_dropdown').classList.remove('open');
    }

    window.limpiarExternoExistente = function(){
        document.getElementById('ext_existente_id').value              = '';
        document.getElementById('ext_existente_badge').style.display   = 'none';
        document.getElementById('ext_existente_badge_txt').textContent = '';
        document.getElementById('ext_buscar_existente').value          = '';
    };

    /* ── GUARDAR EXTERNO ── */
    window.guardarAsesorExterno = function(){

        var rol         = document.getElementById('modal-ext-overlay').dataset.rol || 'asesor';
        var existenteId = document.getElementById('ext_existente_id').value.trim();

        /* Si seleccionaron existente — asignar directo sin guardar */
        if(existenteId){

            var nombreBadge = document.getElementById('ext_existente_badge_txt').textContent;

            if(existenteId === 'INTERNO'){
                /* Interno — asignar por nombre, sin EXT_ */
                var nombreInterno = document.getElementById('ext_existente_badge').dataset.nombre;
                var rol = document.getElementById('modal-ext-overlay').dataset.rol || 'asesor';

                if(rol === 'gerente'){
                    document.getElementById('gerente').value                      = nombreInterno;
                    document.getElementById('gerente_nombre_display').textContent = nombreInterno;
                    document.getElementById('gerente_clave_display').textContent  = '';
                    var btnGer = document.getElementById('btn-add-ger');
                    if(btnGer) btnGer.remove();
                } else if(rol === 'sc'){
                    document.getElementById('sc').value                      = nombreInterno;
                    document.getElementById('sc_nombre_display').textContent = nombreInterno;
                    document.getElementById('sc_clave_display').textContent  = '';
                    var btnSc = document.getElementById('btn-add-sc');
                    if(btnSc) btnSc.remove();
                }

                cerrarModalExterno();
                return;
            }

            /* Externo existente */
            var nombre = nombreBadge.split(' (EXT_')[0];
            asignarExterno(rol, existenteId, nombre);
            cerrarModalExterno();
            return;
        }

        /* Registrar nuevo */
        var nombre   = document.getElementById('ext_nombre').value.trim();
        var clabe    = document.getElementById('ext_clabe').value.trim();
        var id_banco = parseInt(document.getElementById('ext_id_banco').value) || 0;

        if(!nombre){
            Swal.fire({ icon:'warning', title:'Campo requerido', text:'Ingresa el nombre.', confirmButtonColor:'#1a56db' });
            return;
        }

        /* CLABE obligatoria solo para asesor */
        if(rol === 'asesor' && clabe.length !== 18){
            Swal.fire({ icon:'warning', title:'CLABE inválida', text:'La CLABE debe tener 18 dígitos.', confirmButtonColor:'#1a56db' });
            return;
        }

        var btn = document.querySelector('.btn-ext-guardar');
        btn.disabled  = true;
        btn.innerHTML = '<span class="spinner"></span> Guardando...';

        apiPost({
            action:   'guardarAsesorExterno',
            nombre:   nombre,
            clabe:    clabe,
            id_banco: id_banco,
            gerencia: '',
            sc:       ''
        }).then(function(res){
            btn.disabled  = false;
            btn.innerHTML = 'Guardar';

            if(res.status !== 'ok'){
                Swal.fire({ icon:'error', title:'Error', text:res.msg, confirmButtonColor:'#1a56db' });
                return;
            }

            cerrarModalExterno();
            asignarExterno(rol, res.data.id, res.data.nombre);

            Swal.fire({
                icon:'success', title:'Registrado',
                text: res.data.nombre + ' agregado.',
                timer:1500, showConfirmButton:false
            });

        }).catch(function(){
            btn.disabled  = false;
            btn.innerHTML = 'Guardar';
            Swal.fire({ icon:'error', title:'Error de conexión', confirmButtonColor:'#1a56db' });
        });
    };

    /* ── ASIGNAR EXTERNO SEGÚN ROL ── */
    function asignarExterno(rol, id, nombre){

        var clave = 'EXT_' + id;

        if(rol === 'asesor'){
            seleccionarAsesor({
                clave:   clave,
                nombre:  nombre,
                externo: true,
                gerencia:'',
                sc:      '',
                clabe:   '',
                banco:   ''
            });

        } else if(rol === 'gerente'){
            document.getElementById('gerente').value                      = clave;
            document.getElementById('gerente_nombre_display').textContent = nombre;
            document.getElementById('gerente_clave_display').textContent  = clave;
            var btnGer = document.getElementById('btn-add-ger');
            if(btnGer) btnGer.remove();

        } else if(rol === 'sc'){
            document.getElementById('sc').value                      = clave;
            document.getElementById('sc_nombre_display').textContent = nombre;
            document.getElementById('sc_clave_display').textContent  = clave;
            var btnSc = document.getElementById('btn-add-sc');
            if(btnSc) btnSc.remove();
        }
    }


    /* ══════════════════════════════════════════════
    ASESOR EXTERNO
    ══════════════════════════════════════════════ */

    var bancosCache  = [];
    var extBancoId   = 0;
    var debExtGer    = null;
    var debExtSc     = null;

    /* Cargar bancos al init */
    apiPost({ action: 'getBancos' }).then(function(res){
        if (res.status !== 'ok') return;
        bancosCache = res.data;
        var sel = document.getElementById('ext_id_banco');
        res.data.forEach(function(b){
            var opt = document.createElement('option');
            opt.value       = b.id_banco;
            opt.textContent = b.banco;
            opt.dataset.codigo = b.codigo_banco || '';
            sel.appendChild(opt);
        });
    });

    /* Mostrar btn + cuando el typeahead tiene texto y no hay asesor seleccionado */
    document.getElementById('asesor_search').addEventListener('input', function(){
        var btn = document.getElementById('btn-agregar-ext');
        var tieneTexto = this.value.trim().length >= 2;
        var tieneSeleccion = document.getElementById('asesor_clave').value !== '';
        if (tieneTexto && !tieneSeleccion) {
            btn.classList.add('visible');
            /* Guardar el texto para pre-llenar el modal */
            btn.dataset.nombre = this.value.trim();
        } else {
            btn.classList.remove('visible');
        }
    });

    /* Modificar fetchAsesores para mostrar externos con tag */
    var _fetchOriginal = fetchAsesores;
    window.fetchAsesores = function(q) {
        Promise.all([
            apiPost({ action: 'buscarAsesor',         q: q }),
            apiPost({ action: 'buscarAsesorExterno',  q: q })
        ]).then(function(results){
            var resInt = results[0];
            var resExt = results[1];

            dropAsesor.innerHTML = '';

            var todos = [];

            (resInt.data || []).forEach(function(item){
                todos.push({ clave: item.clave, nombre: item.nombre, externo: false });
            });
            (resExt.data || []).forEach(function(item){
                todos.push({
                    clave:    'EXT_' + item.id,
                    nombre:   item.nombre,
                    externo:  true,
                    gerencia: item.gerencia,
                    sc:       item.sc,
                    clabe:    item.clabe,
                    banco:    item.banco
                });
            });

            todos.sort(function(a,b){ return a.nombre.localeCompare(b.nombre); });

            if (todos.length === 0) {
                dropAsesor.innerHTML = '<div class="ta-vacio">Sin resultados — usa + para agregar externo</div>';
                dropAsesor.classList.add('open');
                return;
            }

            todos.forEach(function(item){
                var d = document.createElement('div');
                d.className = 'ta-item';
                var tag = item.externo
                    ? '<span class="badge-ext-tag">Externo</span>'
                    : '';
                d.innerHTML = '<span class="ta-nombre">' + resaltar(item.nombre, q) + tag + '</span>'
                            + '<span class="ta-clave">' + item.clave + '</span>';
                d.addEventListener('mousedown', function(){
                    seleccionarAsesor(item);
                });
                dropAsesor.appendChild(d);
            });

            dropAsesor.classList.add('open');
        });
    };

    /* Modificar seleccionarAsesor para manejar externos */
    var _selOriginal = window.seleccionarAsesor;
    window.seleccionarAsesor = function(item) {
        inputAsesor.value = item.nombre;
        document.getElementById('asesor_clave').value = item.clave;
        document.getElementById('asesor_es_externo').value = item.externo ? '1' : '0';
        document.getElementById('asesor_badge_txt').textContent = item.nombre + ' (' + item.clave + ')';
        document.getElementById('asesor_badge_wrap').style.display = 'block';
        document.getElementById('btn-agregar-ext').classList.remove('visible');
        dropAsesor.classList.remove('open');

        if (item.externo) {
            /* Asesor externo: mostrar estructura simplificada sin comisiones de gerente/SC */
            document.getElementById('loader-estructura').style.display = 'none';
            document.getElementById('asesor_nombre_display').textContent = item.nombre;
            document.getElementById('asesor_clave_display').textContent  = item.clave;

            /* Ocultar cards de gerente y SC */
            document.getElementById('gerente').value                      = item.gerencia || '';
            document.getElementById('gerente_nombre_display').textContent = item.gerencia || '—';
            document.getElementById('gerente_clave_display').textContent  = '';
            document.getElementById('sc').value                           = item.sc || '';
            document.getElementById('sc_nombre_display').textContent      = item.sc || '—';
            document.getElementById('sc_clave_display').textContent       = '';

            document.getElementById('sec-estructura-comisiones').style.display = 'block';
            document.getElementById('breadcrumb-estructura').style.display     = 'none';
            document.getElementById('nota-sc').style.display                   = 'none';

            /* Deshabilitar inputs de gerente y SC */
            document.getElementById('porcen_ger').value    = '';
            document.getElementById('porcen_sc').value     = '';
            document.getElementById('porcen_ger').disabled = true;
            document.getElementById('porcen_sc').disabled  = true;
            document.getElementById('comision_ger').value  = '';
            document.getElementById('comision_sc').value   = '';

            document.getElementById('porcen_ase').focus();

        } else {
            /* Asesor interno: flujo normal */
            document.getElementById('porcen_ger').disabled = false;
            document.getElementById('porcen_sc').disabled  = false;
            cargarEstructura(item.clave);
        }
    };

    /* Limpiar también rehabilita inputs */
    var _limpiarOriginal = window.limpiarAsesor;
    window.limpiarAsesor = function(){
        _limpiarOriginal();
        document.getElementById('asesor_es_externo').value    = '0';
        document.getElementById('btn-agregar-ext').classList.remove('visible');
        document.getElementById('porcen_ger').disabled = false;
        document.getElementById('porcen_sc').disabled  = false;
    };

    /* ── MODAL EXTERNO ── */

    window.abrirModalExterno = function(){
        var nombrePre = document.getElementById('btn-agregar-ext').dataset.nombre || '';
        abrirModalExternoBase(nombrePre, 'asesor');
    };

    window.cerrarModalExterno = function(){
        document.getElementById('modal-ext-overlay').classList.remove('open');
        document.getElementById('modal-ext-overlay').dataset.rol = 'asesor';
    };


    /* ── MODAL EXTERNO PARA GERENTE / SC ── */
    window.abrirModalExternoParaRol = function(rol, nombre){
        abrirModalExternoBase(nombre || '', rol);
    };


    function abrirModalExternoBase(nombrePre, rol){

        /* Título según rol */
        var titulos = {
            'asesor':  '➕ Agregar asesor externo',
            'gerente': '➕ Agregar gerente externo',
            'sc':      '➕ Agregar SC externo'
        };
        document.querySelector('.modal-ext-header h4').textContent = titulos[rol] || titulos['asesor'];

        /* Guardar rol */
        document.getElementById('modal-ext-overlay').dataset.rol = rol;

        /* Limpiar todo */
        document.getElementById('ext_nombre').value               = nombrePre;
        document.getElementById('ext_clabe').value                = '';
        document.getElementById('ext_id_banco').value             = '';
        document.getElementById('hint-clabe-ext').textContent     = '';
        document.getElementById('hint-clabe-ext').className       = '';
        document.getElementById('banco-detectado-wrap').style.display = 'none';
        document.getElementById('ext_buscar_existente').value     = '';
        document.getElementById('ext_existente_badge').style.display = 'none';
        document.getElementById('ext_existente_id').value         = '';
        extBancoId = 0;

        document.getElementById('modal-ext-overlay').classList.add('open');
        setTimeout(function(){ document.getElementById('ext_buscar_existente').focus(); }, 100);
    }


    /* Cerrar con ESC / click overlay */
    document.getElementById('modal-ext-overlay').addEventListener('click', function(e){
        if (e.target === this) cerrarModalExterno();
    });

    /* ── CLABE input ── */
    window.onClabeInput = function(inp){
        /* Solo números */
        inp.value = inp.value.replace(/\D/g, '').substring(0, 18);

        var hint = document.getElementById('hint-clabe-ext');
        var len  = inp.value.length;

        if (len === 0) {
            hint.textContent = ''; hint.className = '';
            document.getElementById('banco-detectado-wrap').style.display = 'none';
            return;
        }

        if (len < 18) {
            hint.textContent = len + ' / 18 dígitos';
            hint.className = 'err';
            document.getElementById('banco-detectado-wrap').style.display = 'none';
            extBancoId = 0;
            return;
        }

        hint.textContent = '✓ 18 dígitos';
        hint.className = 'ok';

        /* Detectar banco por dígitos 4-6 */
        var codigo = inp.value.substring(0, 3);

        var banco  = bancosCache.find(function(b){ return b.codigo_banco === codigo; });

        var wrap   = document.getElementById('banco-detectado-wrap');
        var selBanco = document.getElementById('ext_id_banco');

        if (banco) {
            document.getElementById('banco-detectado-nombre').textContent = banco.banco;
            document.getElementById('banco-detectado-codigo').textContent = codigo;
            wrap.style.display = 'flex';
            selBanco.value = banco.id_banco;
            document.getElementById('banco-select-hint').textContent = '(detectado por CLABE)';
            extBancoId = banco.id_banco;
        } else {
            wrap.style.display = 'none';
            selBanco.value = '';
            document.getElementById('banco-select-hint').textContent = '(no detectado — selecciona manualmente)';
            extBancoId = 0;
        }
    };

    window.onBancoManualChange = function(){
        var sel = document.getElementById('ext_id_banco');
        extBancoId = parseInt(sel.value) || 0;
    };

    /* ── TYPEAHEAD GERENCIA ── */
    window.onExtGerenciaInput = function(inp){
        clearTimeout(debExtGer);
        var q = inp.value.trim();
        var drop = document.getElementById('ext_gerencia_dropdown');
        document.getElementById('ext_gerencia').value = q; /* guardar valor libre también */
        if (q.length < 2) { drop.classList.remove('open'); return; }
        debExtGer = setTimeout(function(){
            apiPost({ action: 'buscarGerencia', q: q }).then(function(res){
                drop.innerHTML = '';
                if (!res.data || res.data.length === 0) {
                    drop.innerHTML = '<div class="ta-vacio">Sin resultados</div>';
                    drop.classList.add('open'); return;
                }
                res.data.forEach(function(val){
                    var d = document.createElement('div');
                    d.className = 'ta-item';
                    d.innerHTML = '<span class="ta-nombre">' + resaltar(val, q) + '</span>';
                    d.addEventListener('mousedown', function(){
                        document.getElementById('ext_gerencia_search').value = val;
                        document.getElementById('ext_gerencia').value        = val;
                        drop.classList.remove('open');
                    });
                    drop.appendChild(d);
                });
                drop.classList.add('open');
            });
        }, 300);
    };

    document.getElementById('ext_gerencia_search').addEventListener('blur', function(){
        setTimeout(function(){ document.getElementById('ext_gerencia_dropdown').classList.remove('open'); }, 200);
    });

    /* ── TYPEAHEAD SC ── */
    window.onExtScInput = function(inp){
        clearTimeout(debExtSc);
        var q = inp.value.trim();
        var drop = document.getElementById('ext_sc_dropdown');
        document.getElementById('ext_sc').value = q;
        if (q.length < 2) { drop.classList.remove('open'); return; }
        debExtSc = setTimeout(function(){
            apiPost({ action: 'buscarSC', q: q }).then(function(res){
                drop.innerHTML = '';
                if (!res.data || res.data.length === 0) {
                    drop.innerHTML = '<div class="ta-vacio">Sin resultados</div>';
                    drop.classList.add('open'); return;
                }
                res.data.forEach(function(val){
                    var d = document.createElement('div');
                    d.className = 'ta-item';
                    d.innerHTML = '<span class="ta-nombre">' + resaltar(val, q) + '</span>';
                    d.addEventListener('mousedown', function(){
                        document.getElementById('ext_sc_search').value = val;
                        document.getElementById('ext_sc').value        = val;
                        drop.classList.remove('open');
                    });
                    drop.appendChild(d);
                });
                drop.classList.add('open');
            });
        }, 300);
    };

    document.getElementById('ext_sc_search').addEventListener('blur', function(){
        setTimeout(function(){ document.getElementById('ext_sc_dropdown').classList.remove('open'); }, 200);
    });

    /* ── GUARDAR ASESOR EXTERNO ── */
    window.guardarAsesorExterno = function(){

        var nombre   = document.getElementById('ext_nombre').value.trim();
        var clabe    = document.getElementById('ext_clabe').value.trim();
        var id_banco = parseInt(document.getElementById('ext_id_banco').value) || 0;
        var gerencia = document.getElementById('ext_gerencia').value.trim();
        var sc       = document.getElementById('ext_sc').value.trim();

        if (!nombre) {
            Swal.fire({ icon:'warning', title:'Campo requerido', text:'Ingresa el nombre del asesor.', confirmButtonColor:'#1a56db' });
            return;
        }
        if (clabe.length !== 18) {
            Swal.fire({ icon:'warning', title:'CLABE inválida', text:'La CLABE debe tener 18 dígitos.', confirmButtonColor:'#1a56db' });
            return;
        }

        var btn = document.querySelector('.btn-ext-guardar');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner"></span> Guardando...';

        apiPost({
            action:   'guardarAsesorExterno',
            nombre:   nombre,
            clabe:    clabe,
            id_banco: id_banco,
            gerencia: gerencia,
            sc:       sc
        }).then(function(res){
            btn.disabled = false;
            btn.innerHTML = 'Guardar asesor';

            if (res.status !== 'ok') {
                Swal.fire({ icon:'error', title:'Error', text: res.msg, confirmButtonColor:'#1a56db' });
                return;
            }

            cerrarModalExterno();

            var rol = document.getElementById('modal-ext-overlay').dataset.rol || 'asesor';

            if (!nombre) {
                Swal.fire({ icon:'warning', title:'Campo requerido', text:'Ingresa el nombre.', confirmButtonColor:'#1a56db' });
                return;
            }

            /* CLABE obligatoria solo para asesor, opcional para gerente/SC */
            if (rol === 'asesor' && clabe.length !== 18) {
                Swal.fire({ icon:'warning', title:'CLABE inválida', text:'La CLABE debe tener 18 dígitos.', confirmButtonColor:'#1a56db' });
                return;
            }

            if (rol === 'gerente') {

                /* Actualizar card gerente */
                document.getElementById('gerente').value                      = 'EXT_' + res.data.id;
                document.getElementById('gerente_nombre_display').textContent = res.data.nombre;
                document.getElementById('gerente_clave_display').textContent  = 'EXT_' + res.data.id;

                var btnGer = document.getElementById('btn-add-ger');
                if (btnGer) btnGer.remove();

            } else if (rol === 'sc') {

                /* Actualizar card SC */
                document.getElementById('sc').value                      = 'EXT_' + res.data.id;
                document.getElementById('sc_nombre_display').textContent = res.data.nombre;
                document.getElementById('sc_clave_display').textContent  = 'EXT_' + res.data.id;

                var btnSc = document.getElementById('btn-add-sc');
                if (btnSc) btnSc.remove();

            } else {

                /* Asesor nuevo — seleccionar automáticamente */
                seleccionarAsesor({
                    clave:    'EXT_' + res.data.id,
                    nombre:   res.data.nombre,
                    externo:  true,
                    gerencia: res.data.gerencia,
                    sc:       res.data.sc,
                    clabe:    res.data.clabe,
                    banco:    res.data.banco
                });

            }

            /* Resetear rol */
            document.getElementById('modal-ext-overlay').dataset.rol = 'asesor';

            Swal.fire({
                icon:'success', title:'Registrado',
                text: res.data.nombre + ' agregado correctamente.',
                timer:1500, showConfirmButton:false
            });

        }).catch(function(){
            btn.disabled = false;
            btn.innerHTML = 'Guardar asesor';
            Swal.fire({ icon:'error', title:'Error de conexión', confirmButtonColor:'#1a56db' });
        });
    };

})();
</script>
</html>