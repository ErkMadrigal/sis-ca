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
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
		<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

		<style>
            .sn-nota { font-size:11.5px; color:#8896a6; margin:8px 4px 0; line-height:1.5; text-align:left; }
            .sp-wrap { display:flex; gap:16px; align-items:stretch; text-align:left; margin-top:6px; }
            .sp-left { width:340px; flex:0 0 340px; background:#fff; border:1px solid #e8edf3; border-radius:14px; display:flex; flex-direction:column; overflow:hidden; }
            .sp-right { flex:1; background:#fff; border:1px solid #e8edf3; border-radius:14px; padding:16px 18px; overflow:auto; }
            .sp-search { padding:10px; border-bottom:1px solid #eef2f6; }
            .sp-search input { width:100%; font-size:13px; border:1px solid #dce3eb; border-radius:8px; padding:7px 11px; background:#fafbfc; color:#1f2d3d; }
            .sp-search input:focus { outline:none; border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.1); background:#fff; }
            .sp-list { flex:1; overflow-y:auto; padding:6px; }
            .sp-card { display:flex; align-items:center; gap:10px; padding:8px 10px; border-radius:10px; cursor:pointer; border:1px solid transparent; }
            .sp-card:hover { background:#f4f8fd; }
            .sp-card.sel { background:#eaf2fb; border-color:#185FA5; }
            .sp-card.inact { opacity:.6; }
            .sp-av { width:36px; height:36px; flex:0 0 36px; border-radius:50%; background:#185FA5; color:#fff; font-size:13px; font-weight:700; display:flex; align-items:center; justify-content:center; }
            .sp-info { flex:1; min-width:0; }
            .sp-nom { font-size:12.5px; font-weight:600; color:#1f2d3d; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
            .sp-cla { font-size:11px; color:#8896a6; }
            .sp-badge { font-size:10px; font-weight:700; padding:2px 7px; border-radius:20px; white-space:nowrap; }
            .sp-b-super { background:#fdebd6; color:#b45f06; }
            .sp-b-inact { background:#e6e9ed; color:#6b7683; }
            .sp-b-n { background:#e3effa; color:#185FA5; margin-left:4px; }
            .sp-vacio { padding:30px 10px; color:#8896a6; font-size:13px; text-align:center; }
            .sp-head h5 { margin:0; font-size:17px; color:#1b3a6b; font-weight:700; }
            .sp-head span { font-size:12px; color:#8896a6; }
            .sp-q2 { width:100%; margin-top:12px; font-size:13px; border:1px solid #dce3eb; border-radius:8px; padding:7px 11px; background:#fafbfc; color:#1f2d3d; }
            .sp-q2:focus { outline:none; border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.1); background:#fff; }
            .sp-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(190px, 1fr)); gap:10px; margin-top:14px; }
            .sp-est { position:relative; border:1.5px solid #dce3eb; border-radius:12px; padding:14px 12px; cursor:pointer; background:#fafbfc; user-select:none; transition:all .12s; }
            .sp-est:hover { border-color:#185FA5; }
            .sp-est b { display:block; font-size:13px; color:#1f2d3d; word-break:break-word; }
            .sp-est small { font-size:11px; color:#8896a6; }
            .sp-est.on { background:#185FA5; border-color:#185FA5; }
            .sp-est.on b, .sp-est.on small { color:#fff; }
            .sp-est.on::after { content:"\2713"; position:absolute; top:8px; right:10px; color:#fff; font-weight:700; }
            .sp-est.busy { opacity:.55; pointer-events:none; }
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
		if (!isset($_SESSION["ultimoAcceso"]) || !isset($_SESSION["autentifica"])) {
			mensaje("Tu sesión ha expirado por seguridad. Por favor, vuelve a iniciar sesión.", "index.php");
			exit;
		}
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
				titulo("ESTRUCTURAS POR ESPARTANO");
				$pag="PAGO_LEADS_VTA";
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
	?>

	<div class="container">
		<div class="row justify-content-center mt-0 pt-0">
			<div class="col-md-12 mt-0 pt-0">
				<div class="sp-wrap">
					<div class="sp-left" id="sp-left">
						<div class="sp-search"><input type="text" id="sp-q" placeholder="Buscar por nombre o clave..." autocomplete="off" /></div>
						<div class="sp-list" id="sp-list"><div class="sp-vacio">Cargando...</div></div>
					</div>
					<div class="sp-right" id="sp-right">
						<div class="sp-vacio">Selecciona un espartano de la lista para configurar sus estructuras.</div>
					</div>
				</div>
				<div class="sn-nota">Un clic en una estructura la asigna o la quita al instante (se guarda solo). La estructura se identifica por el SC.</div>
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

	<?php } ?>

	</body>

	<script type="text/javascript">

    const SP_API = "spartan_estructuras_api.php";
    var SP_ESTRUCTURAS = [];     // [{sc, n}]
    var SP_ITEMS = [];           // espartanos listados
    var SP_SEL = null;           // espartano seleccionado
    var SP_ASIG = new Set();     // SC asignados al seleccionado
    var SP_TMR = null, SP_REQ = 0, SP_FILTRO = "";

    async function spApi(payload) {
        const res = await fetch(SP_API, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(payload) });
        const txt = await res.text();
        try { return JSON.parse(txt); } catch (e) { return { ok: false, msg: "Respuesta inválida del servidor: " + txt.substring(0, 300) }; }
    }
    function esc(v) { return String(v == null ? "" : v).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;"); }
    function iniciales(n) { const p = String(n || "").trim().split(/\s+/).filter(Boolean); return ((p[0] || "?")[0] + (p.length > 1 ? p[1][0] : "")).toUpperCase(); }

    jQuery(document).ready(function() {
        $(window).resize(function() { centra_contenedor(); });
        $(window).resize();
        $('#men_pie').css("top", h);
        $('#men_pie').css("background", "red");
        $('#tmenu td').click(function(evento) {
            var modulo = $(this).text().replace(/ /gi, "_");
            $("#opcion").load("menu.php?modulo=" + modulo);
        });
        $("#salir").click(function() { window.location = "https://www.sis-ca.com.mx:7863/"; });
        $("#email").click(function() { window.open("http://webmail.carobra.com.mx/imp/login.php"); });

        ajustarAlto();
        $(window).on("resize", ajustarAlto);

        $("#sp-q").on("input", function() { clearTimeout(SP_TMR); SP_TMR = setTimeout(cargarLista, 250); });
        iniciar();
    });

    function ajustarAlto() {
        const w = document.querySelector(".sp-wrap");
        if (!w) return;
        const top = w.getBoundingClientRect().top;
        const hh = Math.max(320, window.innerHeight - top - 190);
        w.style.height = hh + "px";
    }

    async function iniciar() {
        const r = await spApi({ action: "estructuras" });
        if (!r.ok) { document.getElementById("sp-list").innerHTML = `<div class="sp-vacio">${esc(r.msg)}</div>`; return; }
        SP_ESTRUCTURAS = r.items;
        cargarLista();
    }

    async function cargarLista() {
        const q = document.getElementById("sp-q").value.trim();
        const req = ++SP_REQ;
        const r = await spApi({ action: "listar", q });
        if (req !== SP_REQ) return;
        const cont = document.getElementById("sp-list");
        if (!r.ok) { cont.innerHTML = `<div class="sp-vacio">${esc(r.msg)}</div>`; return; }
        SP_ITEMS = r.items;
        pintarLista(r.truncado);
    }

    function pintarLista(truncado) {
        const cont = document.getElementById("sp-list");
        if (!SP_ITEMS.length) { cont.innerHTML = '<div class="sp-vacio">Sin resultados</div>'; return; }
        cont.innerHTML = SP_ITEMS.map(u => `
            <div class="sp-card ${SP_SEL && SP_SEL.clave === u.clave ? "sel" : ""} ${u.activo ? "" : "inact"}" data-clave="${esc(u.clave)}">
                <div class="sp-av">${esc(iniciales(u.nombre))}</div>
                <div class="sp-info"><div class="sp-nom" title="${esc(u.nombre)}">${esc(u.nombre)}</div><div class="sp-cla">${esc(u.clave)}</div></div>
                <div>${u.super ? '<span class="sp-badge sp-b-super">Super Admin</span>' : ""}${!u.activo ? '<span class="sp-badge sp-b-inact">Inactivo</span>' : ""}<span class="sp-badge sp-b-n" id="n-${esc(u.clave)}">${u.n}</span></div>
            </div>`).join("") + (truncado ? '<div class="sp-vacio">Hay más resultados; afina la búsqueda.</div>' : "");
        cont.querySelectorAll(".sp-card").forEach(el => el.addEventListener("click", () => seleccionar(el.dataset.clave)));
    }

    async function seleccionar(clave) {
        SP_SEL = SP_ITEMS.find(u => u.clave === clave) || null;
        if (!SP_SEL) return;
        document.querySelectorAll(".sp-card").forEach(el => el.classList.toggle("sel", el.dataset.clave === clave));
        const der = document.getElementById("sp-right");
        der.innerHTML = '<div class="sp-vacio">Cargando...</div>';
        const r = await spApi({ action: "getAsignadas", clave });
        if (!SP_SEL || SP_SEL.clave !== clave) return;
        if (!r.ok) { der.innerHTML = `<div class="sp-vacio">${esc(r.msg)}</div>`; return; }
        SP_ASIG = new Set(r.items);
        pintarDerecha();
    }

    function pintarDerecha() {
        const der = document.getElementById("sp-right");
        const grid = SP_ESTRUCTURAS.length
            ? SP_ESTRUCTURAS.map(e => `<div class="sp-est ${SP_ASIG.has(e.sc) ? "on" : ""}" data-sc="${esc(e.sc)}"><b>${esc(e.sc)}</b><small>${e.n.toLocaleString("es-MX")} registros</small></div>`).join("")
            : '<div class="sp-vacio">No hay estructuras (SC) en estruc_empleados.</div>';
        der.innerHTML = `<div class="sp-head"><h5>${esc(SP_SEL.nombre)}</h5><span>${esc(SP_SEL.clave)} &middot; Estructuras asignadas: <b id="sp-cnt">${SP_ASIG.size}</b></span></div><input type="text" id="sp-q2" class="sp-q2" placeholder="Buscar estructura..." autocomplete="off" /><div class="sp-grid">${grid}</div><div class="sp-vacio" id="sp-q2-vacio" style="display:none">Sin resultados</div>`;
        der.querySelectorAll(".sp-est").forEach(el => el.addEventListener("click", () => alternar(el)));
        const qi = document.getElementById("sp-q2");
        qi.value = SP_FILTRO;
        qi.addEventListener("input", () => { SP_FILTRO = qi.value; filtrarEstructuras(); });
        filtrarEstructuras();
    }

    function filtrarEstructuras() {
        const t = SP_FILTRO.trim().toLowerCase();
        let vis = 0;
        document.querySelectorAll("#sp-right .sp-est").forEach(el => {
            const ok = !t || el.dataset.sc.toLowerCase().includes(t);
            el.style.display = ok ? "" : "none";
            if (ok) vis++;
        });
        const v = document.getElementById("sp-q2-vacio"); if (v) v.style.display = vis ? "none" : "";
    }

    async function alternar(el) {
        if (!SP_SEL) return;
        const clave = SP_SEL.clave, sc = el.dataset.sc, on = !SP_ASIG.has(sc);
        el.classList.add("busy");
        const r = await spApi({ action: "toggle", clave, sc, on });
        el.classList.remove("busy");
        if (!r.ok) { Swal.fire({ icon: "warning", title: "Aviso", text: r.msg }); return; }
        if (!SP_SEL || SP_SEL.clave !== clave) return;
        if (on) SP_ASIG.add(sc); else SP_ASIG.delete(sc);
        el.classList.toggle("on", on);
        const c = document.getElementById("sp-cnt"); if (c) c.textContent = SP_ASIG.size;
        const b = document.getElementById("n-" + clave); if (b) b.textContent = SP_ASIG.size;
        const it = SP_ITEMS.find(u => u.clave === clave); if (it) it.n = SP_ASIG.size;
    }

</script>
</html>