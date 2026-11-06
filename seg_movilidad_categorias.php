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

			/* ── "mvc-" = Movilidad por Categorías ── */

			.mvc-card {
				background:#fff; border:1px solid #e0e8ec; border-radius:12px;
				padding:20px 22px; margin-bottom:18px; box-shadow:0 1px 3px rgba(15,23,42,.05);
			}

			.mvc-titulo { font-size:16px; font-weight:700; color:#0f4d8a; margin-bottom:4px; }
			.mvc-subtitulo { font-size:12.5px; color:#8896a6; margin-bottom:16px; }

			.mvc-tabla { width:100%; border-collapse:collapse; font-size:13.5px; }

			.mvc-tabla thead th {
				background:#eff6ff; color:#185FA5; font-weight:700; font-size:11px;
				text-transform:uppercase; letter-spacing:.04em; padding:11px 14px; text-align:left;
				border-bottom:2px solid #dbeafe;
			}
			.mvc-tabla thead th.mvc-col-num,
			.mvc-tabla tbody td.mvc-col-num { text-align:right; }
			.mvc-tabla thead th.mvc-col-accion,
			.mvc-tabla tbody td.mvc-col-accion { text-align:center; }

			.mvc-tabla tbody td { padding:12px 14px; border-bottom:1px solid #eef2f7; color:#1f2d3d; }
			.mvc-tabla tbody tr:hover td { background:#f7fbff; }
			.mvc-tabla tfoot td {
				padding:12px 14px; font-weight:700; color:#0f4d8a; border-top:2px solid #dbeafe;
				background:#f7fbff;
			}

			.mvc-categoria { font-weight:700; color:#1f2d3d; }
			.mvc-categoria-vacia { color:#94a3b8; font-style:italic; font-weight:500; }

			.mvc-pct-barra-wrap { display:flex; align-items:center; gap:8px; justify-content:flex-end; }
			.mvc-pct-barra {
				width:70px; height:6px; border-radius:4px; background:#eef2f7; overflow:hidden; flex-shrink:0;
			}
			.mvc-pct-barra-fill { height:100%; background:#185FA5; border-radius:4px; }

			.mvc-comision-pill {
				display:inline-block; font-size:12px; font-weight:700; color:#0b6b3a;
				background:#d7f5e3; border-radius:20px; padding:4px 12px;
			}
			.mvc-comision-pill.mvc-comision-cero { color:#7b8794; background:#f0f4f8; }

			.btn-mvc-ver {
				width:30px; height:30px; border-radius:50%; border:none;
				background:#eff6ff; color:#185FA5; font-size:16px; font-weight:700;
				cursor:pointer; transition:.15s; line-height:1;
			}
			.btn-mvc-ver:hover { background:#185FA5; color:#fff; }
			.btn-mvc-ver.abierto { background:#185FA5; color:#fff; transform:rotate(45deg); }

			/* Renglón expandible con el acordeón de folios, justo debajo
			   de la categoría -- se inserta dinámicamente al dar clic en +. */
			.mvc-fila-detalle td {
				padding:0; background:#f7fbff; border-bottom:1px solid #dbeafe;
			}
			.mvc-detalle-wrap { padding:16px 20px 20px 20px; }

			.mvc-vacio { text-align:center; padding:30px; color:#aaa; font-size:13px; }

			/* Nota sobre el % de comisión, hasta que el negocio defina
			   la tasa real por categoría (ver seg_movilidad_categorias_api.php). */
			.mvc-nota {
				font-size:11.5px; color:#a06b00; background:#fff8e6; border:1px solid #f5d97a;
				border-radius:8px; padding:8px 12px; margin-top:14px;
			}

			/* ── Buscador + acordeón de folios (se inserta inline debajo
			   de cada categoría, ver .mvc-fila-detalle más abajo) ── */

			.mvc-modal-buscador {
	display:flex; align-items:center; gap:10px;
	margin-bottom:12px;
}
.mvc-modal-buscador input {
	flex:1; min-width:0; border:1px solid #d5e2ea; border-radius:9px;
	padding:9px 14px; font-size:12.5px; color:#0f4d8a; outline:none;
	transition:border-color .15s, box-shadow .15s;
}
.mvc-modal-buscador input:focus {
	border-color:#185FA5; box-shadow:0 0 0 3px rgba(24,95,165,.12);
}
.mvc-modal-buscador input::placeholder { color:#9db3c4; }
.mvc-modal-buscador span {
	font-size:11px; color:#5b7fa6; white-space:nowrap; flex-shrink:0;
}
/* ── Tabla de folios (detalle inline de una categoría) -- sin
   acordeón, cada folio se ve completo de una vez ── */
			.mvc-detalle-tabla-wrap { border:1px solid #e0e8ec; border-radius:10px; overflow:auto; max-height:60vh; }
			.mvc-detalle-tabla { width:100%; border-collapse:collapse; font-size:12.5px; }
			.mvc-detalle-tabla thead th {
				background:#eff6ff; color:#185FA5; font-weight:700; font-size:10.5px;
				text-transform:uppercase; letter-spacing:.03em; padding:9px 10px; text-align:left;
				position:sticky; top:0; white-space:nowrap;
			}
			.mvc-detalle-tabla tbody td { padding:8px 10px; border-bottom:1px solid #eef2f4; white-space:nowrap; color:#1e2a37; }
			.mvc-detalle-tabla tbody tr:hover td { background:#f7fbfc; }
			.mvc-detalle-tabla tbody td:first-child { font-weight:700; color:#0f4d8a; }

			.mvc-modal-limitado {
				font-size:11.5px; color:#a06b00; background:#fff8e6; border:1px solid #f5d97a;
				border-radius:8px; padding:7px 12px; margin-bottom:12px;
			}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>

		<div id="loader">
			<div class="text-center text-white">
				<div class="spinner-border text-light mb-3" role="status"></div>
				<div>Cargando...</div>
			</div>
		</div>

<?php
		$fg = $_SESSION["ultimoAcceso"];
		$fa = date("Y-n-j H:i:s");
		$periodo = (strtotime($fa)-strtotime($fg));
		if($periodo > 600)
		{
			session_destroy();
			mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
		}
		else
		{
			$_SESSION["ultimoAcceso"] = $fa;
			$_SESSION["autentifica"] = "p";
		}

		if ($_SESSION["autentifica"] != "p")
		{
			session_destroy();
			mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","index.php");
		}
		else
		{
			reg_acceso($_SESSION["nombre"],"movilidad_categorias");
			$_SESSION["autentifica"]="";

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
				titulo("Categorías de Movilidad");
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
?>

	<div class="container-fluid" style="padding:16px 18px; text-align:left;">

		<div class="mvc-card">
			<div class="mvc-titulo">📊 Folios por categoría de cliente</div>
			<div class="mvc-subtitulo">
				Resumen de la tabla <strong>movilidad</strong>, agrupado por <strong>categoria_cliente</strong>.
				Da clic en <strong>+</strong> para ver el detalle de los folios de cada categoría.
			</div>

			<table class="mvc-tabla">
				<thead>
					<tr>
						<th>Categoría</th>
						<th class="mvc-col-num">No. Folios</th>
						<th class="mvc-col-num">% Participación</th>
						<th class="mvc-col-num">% Comisión</th>
						<th class="mvc-col-accion">Detalle</th>
					</tr>
				</thead>
				<tbody id="mvcTbody">
					<tr><td colspan="5" class="mvc-vacio">Cargando...</td></tr>
				</tbody>
				<tfoot id="mvcTfoot" style="display:none;">
					<tr>
						<td>Total</td>
						<td class="mvc-col-num" id="mvcTotalFolios">0</td>
						<td class="mvc-col-num">100%</td>
						<td class="mvc-col-num">—</td>
						<td></td>
					</tr>
				</tfoot>
			</table>

			<div class="mvc-nota">
				⚠️ El % de comisión todavía no está definido por el negocio -- por ahora es un valor fijo en 0%
				mientras nos mandan la tasa real por categoría.
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

<?php } ?>

	</body>

	<script type="text/javascript">

	const MVC_API = 'seg_movilidad_categorias_api.php';

	async function mvcApi(payload){
		var res = await fetch(MVC_API, {
			method:'POST',
			headers:{'Content-Type':'application/json'},
			body:JSON.stringify(payload)
		});
		return res.json();
	}

	function escaparHtmlMvc(txt){
		var div = document.createElement('div');
		div.textContent = (txt === null || txt === undefined) ? '' : String(txt);
		return div.innerHTML;
	}

	function mostrarLoaderMvc(){ document.getElementById('loader').style.display = 'flex'; }
	function ocultarLoaderMvc(){ document.getElementById('loader').style.display = 'none'; }

	/* ── Tabla resumen ── */

	async function cargarResumenMvc(){
		var tbody = document.getElementById('mvcTbody');
		tbody.innerHTML = '<tr><td colspan="5" class="mvc-vacio">Cargando...</td></tr>';
		document.getElementById('mvcTfoot').style.display = 'none';

		try{
			var json = await mvcApi({ action: 'listarResumen' });

			if(!json.ok){
				tbody.innerHTML = '<tr><td colspan="5" class="mvc-vacio">' + escaparHtmlMvc(json.msg || 'Error al cargar.') + '</td></tr>';
				return;
			}

			if(!json.filas || json.filas.length === 0){
				tbody.innerHTML = '<tr><td colspan="5" class="mvc-vacio">Sin datos en la tabla movilidad.</td></tr>';
				return;
			}

			tbody.innerHTML = json.filas.map(function(f, idx){

				var claseCat = (f.etiqueta === 'Sin categoría') ? 'mvc-categoria mvc-categoria-vacia' : 'mvc-categoria';
				var claseComision = (Number(f.pct_comision) > 0) ? 'mvc-comision-pill' : 'mvc-comision-pill mvc-comision-cero';

				return '<tr class="mvc-fila-cat" id="mvc-fila-' + idx + '">' +
					'<td class="' + claseCat + '">' + escaparHtmlMvc(f.etiqueta) + '</td>' +
					'<td class="mvc-col-num">' + Number(f.no_folios).toLocaleString('es-MX') + '</td>' +
					'<td class="mvc-col-num">' +
						'<div class="mvc-pct-barra-wrap">' +
							'<div class="mvc-pct-barra"><div class="mvc-pct-barra-fill" style="width:' + Math.min(100, Number(f.pct_participacion)) + '%;"></div></div>' +
							'<span>' + Number(f.pct_participacion).toFixed(1) + '%</span>' +
						'</div>' +
					'</td>' +
					'<td class="mvc-col-num"><span class="' + claseComision + '">' + Number(f.pct_comision).toFixed(1) + '%</span></td>' +
					'<td class="mvc-col-accion"><button type="button" class="btn-mvc-ver" id="mvc-btn-' + idx + '" title="Ver folios" onclick="toggleDetalleCategoriaMvc(' + idx + ', \'' + encodeURIComponent(f.categoria) + '\')">+</button></td>' +
				'</tr>';

			}).join('');

			document.getElementById('mvcTotalFolios').textContent = Number(json.total_folios).toLocaleString('es-MX');
			document.getElementById('mvcTfoot').style.display = '';

		} catch(e){
			tbody.innerHTML = '<tr><td colspan="5" class="mvc-vacio">Error de comunicación con el servidor.</td></tr>';
		}
	}

	/* ── Detalle de una categoría (renglón expandible debajo de la fila) ── */

	/* Folios ya pedidos al servidor, por categoría (cache para no volver
	   a pedirlos si se cierra y se vuelve a abrir el mismo renglón). Clave =
	   categoria cruda (puede ser ''); valor = { filas, limitado }. */
	var mvcCacheDetalle = {};

	/* Folios (sin filtrar) que está mostrando CADA renglón expandido
	   ahora mismo, indexado por idx de fila -- así el buscador de cada
	   categoría filtra su propio conjunto sin pisar el de las demás. */
	var mvcFilasPorIdx = {};

	/* Abre/cierra, justo debajo de la fila de categoría en la tabla
	   resumen, un renglón con el buscador + acordeón de sus folios. Se
	   arma la primera vez que se abre esa categoría; después solo se
	   muestra/oculta (no se vuelve a pedir al servidor). */
	async function toggleDetalleCategoriaMvc(idx, categoriaCodificada){

		var categoria = decodeURIComponent(categoriaCodificada);
		var btn = document.getElementById('mvc-btn-' + idx);
		var filaDetalle = document.getElementById('mvc-detalle-' + idx);

		if(filaDetalle){
			var abierta = filaDetalle.style.display !== 'none';
			filaDetalle.style.display = abierta ? 'none' : 'table-row';
			btn.classList.toggle('abierto', !abierta);
			return;
		}

		btn.classList.add('abierto');

		var filaCat = document.getElementById('mvc-fila-' + idx);
		var tr = document.createElement('tr');
		tr.className = 'mvc-fila-detalle';
		tr.id = 'mvc-detalle-' + idx;
		var td = document.createElement('td');
		td.colSpan = 5;
		td.innerHTML = '<div class="mvc-detalle-wrap" id="mvc-detalle-wrap-' + idx + '"><div class="mvc-vacio">Cargando...</div></div>';
		tr.appendChild(td);
		filaCat.insertAdjacentElement('afterend', tr);

		try{
			var json = mvcCacheDetalle[categoria];
			if(!json){
				json = await mvcApi({ action: 'listarFoliosCategoria', categoria: categoria });
				if(json.ok) mvcCacheDetalle[categoria] = json;
			}

			var wrap = document.getElementById('mvc-detalle-wrap-' + idx);
			if(!wrap) return; // el usuario ya cerró el renglón antes de que respondiera

			if(!json.ok){
				wrap.innerHTML = '<div class="mvc-vacio">' + escaparHtmlMvc(json.msg || 'Error al cargar el detalle.') + '</div>';
				return;
			}

			renderDetalleCategoriaMvc(idx, json);

		} catch(e){
			var wrap2 = document.getElementById('mvc-detalle-wrap-' + idx);
			if(wrap2) wrap2.innerHTML = '<div class="mvc-vacio">Error de comunicación con el servidor.</div>';
		}
	}

	/* Arma el "cascarón" del detalle de una categoría UNA sola vez
	   (aviso de límite + buscador + acordeón vacío); a partir de ahí
	   solo se repinta la lista (renderFilasDetalleMvc) para que el
	   buscador filtre sin perder el foco del input en cada tecla. */
	function renderDetalleCategoriaMvc(idx, json){

		var wrap = document.getElementById('mvc-detalle-wrap-' + idx);
		if(!wrap) return;

		mvcFilasPorIdx[idx] = json.filas || [];

		if(!mvcFilasPorIdx[idx].length){
			wrap.innerHTML = '<div class="mvc-vacio">Sin folios para esta categoría.</div>';
			return;
		}

		var html = '';

		if(json.limitado){
			html += '<div class="mvc-modal-limitado">⚠️ Se muestran los primeros folios encontrados -- hay más de los que caben aquí. Afina la categoría o pide un reporte aparte si necesitas verlos todos.</div>';
		}

		html += '<div class="mvc-modal-buscador">' +
					'<input type="text" placeholder="Buscar por folio, afore, ley..." autocomplete="off" oninput="filtrarDetalleMvc(' + idx + ', this.value)">' +
					'<span id="mvc-conteo-' + idx + '"></span>' +
				'</div>';

		html += '<div class="mvc-detalle-tabla-wrap"><table class="mvc-detalle-tabla"><thead><tr>';
		MVC_COLUMNAS_DETALLE.forEach(function(col){
			html += '<th>' + escaparHtmlMvc(col.label) + '</th>';
		});
		html += '</tr></thead><tbody id="mvc-tbody-' + idx + '"></tbody></table></div>';

		wrap.innerHTML = html;

		renderFilasDetalleMvc(idx, mvcFilasPorIdx[idx]);
	}

	function valorAccMvc(v){
		return (v === null || v === undefined || v === '') ? '—' : escaparHtmlMvc(v);
	}

	/* Columnas que se pintan en el detalle -- mismas que trae
	   listarFoliosCategoria (tal cual la tabla movilidad, sin cruzar
	   con nada más, según se pidió). */
	var MVC_COLUMNAS_DETALLE = [
		{ key: 'folio',           label: 'Folio' },
		{ key: 'fec_captura',     label: 'Fec. captura' },
		{ key: 'fec_sello',       label: 'Fec. sello' },
		{ key: 'afore',           label: 'Afore' },
		{ key: 'movilidad',       label: 'Movilidad' },
		{ key: 'ley',             label: 'Ley' },
		{ key: 'salario',         label: 'Salario' },
		{ key: 'pplus',           label: 'PPlus' },
		{ key: 'cotiza',          label: 'Cotiza' },
		{ key: 'edad_cliente',    label: 'Edad cliente' },
	];

	/* Pinta las filas de la tabla de folios de la categoría "idx" --
	   sin acordeón, cada folio se ve completo de una vez. Recibe ya el
	   subconjunto a mostrar (todas las filas, o las que hayan pasado
	   el filtro de texto). */
	function renderFilasDetalleMvc(idx, filas){

		var tbody = document.getElementById('mvc-tbody-' + idx);
		if(!tbody) return;

		if(!filas.length){
			tbody.innerHTML = '<tr><td colspan="' + MVC_COLUMNAS_DETALLE.length + '" class="mvc-vacio">Sin resultados para esa búsqueda.</td></tr>';
			return;
		}

		tbody.innerHTML = filas.map(function(fila){
			var fila_html = '<tr>';
			MVC_COLUMNAS_DETALLE.forEach(function(col){
				fila_html += '<td>' + valorAccMvc(fila[col.key]) + '</td>';
			});
			return fila_html + '</tr>';
		}).join('');
	}

	/* Filtra los folios de la categoría "idx" por coincidencia de texto
	   en CUALQUIER columna del detalle (folio, afore, ley, etc.), sin
	   distinguir mayúsculas/minúsculas ni acentos -- así "cdmx"
	   encuentra "CDMX" y "97" encuentra folios/ley que traigan ese
	   pedazo. */
	function filtrarDetalleMvc(idx, texto){

		var termino = (texto || '').toString().trim().toLowerCase()
			.normalize('NFD').replace(/[̀-ͯ]/g, '');

		var conteoEl = document.getElementById('mvc-conteo-' + idx);
		var todas = mvcFilasPorIdx[idx] || [];

		if(termino === ''){
			renderFilasDetalleMvc(idx, todas);
			if(conteoEl) conteoEl.textContent = '';
			return;
		}

		var filtradas = todas.filter(function(fila){
			return MVC_COLUMNAS_DETALLE.some(function(col){
				var v = fila[col.key];
				if(v === null || v === undefined) return false;
				var vNorm = v.toString().toLowerCase()
					.normalize('NFD').replace(/[̀-ͯ]/g, '');
				return vNorm.indexOf(termino) !== -1;
			});
		});

		renderFilasDetalleMvc(idx, filtradas);
		if(conteoEl) conteoEl.textContent = filtradas.length + ' de ' + todas.length;
	}

	document.addEventListener('DOMContentLoaded', function () {
		window.addEventListener('resize', centra_contenedor);
		centra_contenedor();

		var elSalir = document.getElementById('salir');
		if(elSalir) elSalir.addEventListener('click', function(){ window.location = 'https://www.sis-ca.com.mx:7863/'; });

		var elEmail = document.getElementById('email');
		if(elEmail) elEmail.addEventListener('click', function(){ window.open('http://webmail.carobra.com.mx/imp/login.php'); });

		cargarResumenMvc();
	});

	</script>
</html>