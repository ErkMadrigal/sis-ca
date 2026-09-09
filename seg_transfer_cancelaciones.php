<?
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

		<!-- SweetAlert2 -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<!-- SheetJS para exportar a Excel -->
		<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>

		<style type="text/css">
			@import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap');

			.carobra	{
							height: 25px !important;
							font-size: 12px !important;
						}

			#repContenido, .tc-wrap * { font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; }

			.tc-wrap	{
							max-width: 1360px;
							margin: 18px auto 40px auto;
							padding: 0 16px;
						}

			/* ---------- Hero ---------- */
			.tc-hero	{
							background: linear-gradient(120deg, #14335E 0%, #1E5B9E 55%, #2B84C7 100%);
							border-radius: 18px;
							padding: 26px 30px;
							color: #FFF;
							box-shadow: 0 10px 30px -12px rgba(20,51,94,.45);
							position: relative;
							overflow: hidden;
						}

			.tc-hero::before	{
									content: "";
									position: absolute;
									right: -60px; top: -60px;
									width: 220px; height: 220px;
									border-radius: 50%;
									background: rgba(255,255,255,.08);
								}

			.tc-hero::after	{
									content: "";
									position: absolute;
									right: 80px; bottom: -90px;
									width: 160px; height: 160px;
									border-radius: 50%;
									background: rgba(255,255,255,.06);
								}

			.tc-hero-titulo	{
								font-size: 22px;
								font-weight: 800;
								letter-spacing: .2px;
								margin: 0;
								display:flex;
								align-items:center;
								gap:10px;
							}

			.tc-hero-sub	{
								font-size: 13px;
								color: rgba(255,255,255,.78);
								margin-top: 4px;
							}

			.tc-hero-icon	{
								width: 40px; height:40px;
								background: rgba(255,255,255,.15);
								border-radius: 12px;
								display:flex; align-items:center; justify-content:center;
								flex-shrink:0;
							}

			/* ---------- Barra de filtro flotante ---------- */
			.tc-filtro	{
							background:#FFFFFF;
							border-radius: 16px;
							box-shadow: 0 8px 24px -14px rgba(15,23,42,.25), 0 1px 2px rgba(15,23,42,.06);
							padding: 18px 22px;
							margin-top: -22px;
							position: relative;
							z-index: 2;
							display:flex;
							gap:16px;
							align-items:flex-end;
							flex-wrap:wrap;
						}

			.tc-campo	{	flex: 1 1 280px;	}

			.tc-campo label	{
								font-size: 11px;
								font-weight: 700;
								color: #64748B;
								text-transform: uppercase;
								letter-spacing: .6px;
								margin-bottom: 6px;
								display:block;
							}

			#tcAutocomplete	{	position: relative;	}

			.tc-input-icono	{	position: relative;	}

			.tc-input-icono svg	{
									position:absolute;
									left: 13px; top: 50%;
									transform: translateY(-50%);
									color: #94A3B8;
									pointer-events:none;
								}

			#tcSemana	{
							height: 42px !important;
							font-size: 13px !important;
							padding-left: 38px;
							border-radius: 10px;
							border: 1.5px solid #E2E8F0;
							transition: all .15s ease;
						}

			#tcSemana:focus	{
								border-color: #2B84C7;
								box-shadow: 0 0 0 3px rgba(43,132,199,.15);
								outline:none;
							}

			.tc-sugerencias	{
								position:absolute;
								top: calc(100% + 6px);
								left:0;
								right:0;
								background:#FFF;
								border:1px solid #E9EEF5;
								border-radius:12px;
								max-height:240px;
								overflow-y:auto;
								z-index:50;
								display:none;
								box-shadow: 0 12px 28px -10px rgba(15,23,42,.22);
								padding: 6px;
							}

			.tc-sugerencias div	{
									padding:9px 12px;
									cursor:pointer;
									font-size: 13px;
									border-radius: 8px;
									color:#1E293B;
									display:flex;
									align-items:center;
									gap:8px;
								}

			.tc-sugerencias div:hover	{	background:#EFF6FF; color:#1E5B9E;	}

			.tc-btn	{
						height: 42px;
						padding: 0 22px;
						border: none;
						border-radius: 10px;
						font-size: 13px;
						font-weight: 700;
						letter-spacing:.2px;
						cursor: pointer;
						display:inline-flex;
						align-items:center;
						gap:8px;
						transition: transform .12s ease, box-shadow .12s ease, background .15s ease;
						white-space: nowrap;
					}

			.tc-btn:active	{	transform: translateY(1px);	}

			.tc-btn-primario	{
									background: linear-gradient(135deg, #1E5B9E, #2B84C7);
									color: #FFF;
									box-shadow: 0 8px 18px -8px rgba(30,91,158,.6);
								}

			.tc-btn-primario:hover	{	box-shadow: 0 10px 22px -8px rgba(30,91,158,.75); transform: translateY(-1px);	}

			.tc-btn-exportar	{
									background: linear-gradient(135deg, #0E9F6E, #17B27E);
									color: #FFF;
									box-shadow: 0 8px 18px -8px rgba(14,159,110,.55);
								}

			.tc-btn-exportar:hover	{	box-shadow: 0 10px 22px -8px rgba(14,159,110,.7); transform: translateY(-1px);	}

			/* ---------- Resumen ---------- */
			.tc-resumen	{
							display:grid;
							grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
							gap:16px;
							margin: 20px 0 8px 0;
						}

			.tc-card	{
							min-width: 0;
							background:#FFFFFF;
							border-radius: 14px;
							padding: 18px 20px;
							display:flex;
							align-items:center;
							gap:14px;
							box-shadow: 0 6px 18px -12px rgba(15,23,42,.18);
							border: 1px solid #F1F5F9;
						}

			.tc-card-texto	{	min-width: 0;	}

			.tc-card-icono	{
								width:46px; height:46px;
								border-radius: 12px;
								display:flex; align-items:center; justify-content:center;
								flex-shrink:0;
							}

			.tc-card-icono.azul	{	background:#EAF2FC; color:#1E5B9E;	}
			.tc-card-icono.verde	{	background:#E7F9F1; color:#0E9F6E;	}
			.tc-card-icono.morado	{	background:#F2ECFC; color:#7C3AED;	}
			.tc-card-icono.naranja	{	background:#FEF2E3; color:#D97706;	}

			.tc-card .num	{
								font-size: clamp(16px, 1.6vw, 21px);
								font-weight:800;
								color:#0F172A;
								line-height:1.15;
								white-space: nowrap;
								overflow: hidden;
								text-overflow: ellipsis;
							}

			.tc-card .lbl	{	font-size: 10.5px; color:#94A3B8; text-transform:uppercase; letter-spacing:.5px; margin-top:3px; font-weight:700; white-space:nowrap;	}

			/* ---------- Tabla ---------- */
			.tc-tabla-cont	{
								background:#FFF;
								border-radius: 16px;
								box-shadow: 0 6px 18px -12px rgba(15,23,42,.18);
								border: 1px solid #F1F5F9;
								overflow: hidden;
								margin-top: 16px;
							}

			.tc-tabla-scroll	{	overflow-x:auto;	}

			#tcTabla	{	font-size: 12.5px; margin-bottom:0; width:100%; border-collapse:separate; border-spacing:0;	}

			#tcTabla thead th	{
									background: #F8FAFC;
									color: #475569;
									font-size: 10.5px;
									font-weight: 700;
									text-transform: uppercase;
									letter-spacing: .5px;
									border-bottom: 1.5px solid #E2E8F0;
									padding: 12px 14px;
									white-space: nowrap;
									position: sticky;
									top: 0;
								}

			#tcTabla tbody td	{
									padding: 11px 14px;
									border-bottom: 1px solid #F1F5F9;
									color: #1E293B;
									vertical-align: middle;
									white-space: nowrap;
								}

			#tcTabla tbody tr	{	transition: background .1s ease;	}
			#tcTabla tbody tr:hover	{	background: #F8FBFF;	}
			#tcTabla tbody tr:last-child td	{	border-bottom: none;	}

			.tc-folio	{	font-weight:700; color:#1E5B9E; font-variant-numeric: tabular-nums;	}
			.tc-saldo	{	font-weight:700; color:#0F172A; font-variant-numeric: tabular-nums;	}

			.tc-avatar	{
							display:inline-flex; align-items:center; gap:8px;
						}

			.tc-avatar-circulo	{
									width: 26px; height:26px;
									border-radius: 50%;
									background: linear-gradient(135deg, #1E5B9E, #2B84C7);
									color: #FFF;
									font-size: 10.5px;
									font-weight:700;
									display:flex; align-items:center; justify-content:center;
									flex-shrink:0;
								}

			.tc-badge	{
							display:inline-block;
							padding: 3px 10px;
							border-radius: 999px;
							font-size: 11px;
							font-weight: 700;
							background:#F1F5F9;
							color:#475569;
						}

			.tc-vacio	{
							text-align:center;
							padding: 46px 20px;
							color:#94A3B8;
						}

			.tc-vacio svg	{	color:#CBD5E1; margin-bottom: 10px;	}
			.tc-vacio p	{	font-size: 13px; margin:0; }
			.tc-vacio strong	{	color:#475569;	}

			#tcLoader	{
							display:none;
							position: fixed;
							top:0; left:0; width:100%; height:100%;
							background: rgba(15,23,42,.55);
							backdrop-filter: blur(2px);
							z-index:9999;
							justify-content:center;
							align-items:center;
						}

			#tcLoader .tc-loader-box	{
											background:#FFF;
											border-radius: 16px;
											padding: 26px 34px;
											text-align:center;
											box-shadow: 0 20px 40px -10px rgba(0,0,0,.3);
										}

			#tcLoader .spinner-border	{	color:#2B84C7 !important;	}
			#tcLoader .tc-loader-txt	{	margin-top:10px; font-size:13px; font-weight:600; color:#334155;	}

			@media (max-width: 767px) {
				.tc-filtro { flex-direction: column; align-items: stretch; }
				.tc-btn { width: 100%; justify-content:center; }
			}
		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>

	<body>
<?
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));
     	if($periodo > 6000)
     	{
      		session_destroy();
      		mensaje("Tu sesión ha caducado. Por favor, regístrate nuevamente","http://carobra.ddns.me:8888");
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
			reg_acceso($_SESSION["nombre"],"transfer_cancelaciones");

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
				titulo("Cancelaciones Transfer Out");
				$pag="INTERFACES";
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
<?
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
				$divcontenido->css("overflow-y","auto");
?>
					<div class="tc-wrap">

						<div class="tc-hero">
							<div>
								<p class="tc-hero-titulo">Cancelaciones Transfer Out</p>
								<div class="tc-hero-sub">Seguimiento de cancelaciones capturadas por semana comercial</div>
							</div>
						</div>

						<div class="tc-filtro">
							<div class="tc-campo">
								<label>Semana comercial</label>
								<div id="tcAutocomplete">
									<div class="tc-input-icono">
										<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
										<input type="text" id="tcSemana" class="form-control carobra" placeholder="Escribe para buscar una semana..." autocomplete="off">
									</div>
									<div class="tc-sugerencias" id="tcSugerencias"></div>
								</div>
							</div>

							<div class="tc-campo" style="flex: 0 1 170px;">
								<label>Cancelación</label>
								<select id="tcCancelacion" class="form-control carobra" style="height:42px !important; font-size:13px !important; border-radius:10px; border:1.5px solid #E2E8F0;">
									<option value="">Todos</option>
									<option value="1">Sí</option>
									<option value="0">No</option>
								</select>
							</div>

							<button class="tc-btn tc-btn-primario" id="tcBtnBuscar">
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
								Buscar
							</button>
							<button class="tc-btn tc-btn-exportar" id="tcBtnExportar" style="display:none;">
								<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
								Exportar a Excel
							</button>
						</div>

						<div class="tc-resumen" id="tcResumen" style="display:none;">
							<div class="tc-card">
								<div class="tc-card-icono azul">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M9 12l2 2 4-4"/><circle cx="12" cy="12" r="9"/></svg>
								</div>
								<div class="tc-card-texto">
									<div class="num" id="tcNumRegistros">0</div>
									<div class="lbl">Cancelaciones</div>
								</div>
							</div>
							<div class="tc-card">
								<div class="tc-card-icono verde">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7h5a3.5 3.5 0 0 1 0 7H6"/></svg>
								</div>
								<div class="tc-card-texto">
									<div class="num" id="tcNumSaldo">$0</div>
									<div class="lbl">Saldo total</div>
								</div>
							</div>
							<div class="tc-card">
								<div class="tc-card-icono morado">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/><path d="m9 12 2 2 4-4"/></svg>
								</div>
								<div class="tc-card-texto">
									<div class="num" id="tcNumCertificadas">0</div>
									<div class="lbl">Cancelaciones certificadas</div>
								</div>
							</div>
							<div class="tc-card">
								<div class="tc-card-icono naranja">
									<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M12 22s8-4.5 8-11V5l-8-3-8 3v6c0 6.5 8 11 8 11Z"/><line x1="12" y1="8" x2="12" y2="12" stroke-linecap="round"/><line x1="12" y1="16" x2="12.01" y2="16" stroke-linecap="round"/></svg>
								</div>
								<div class="tc-card-texto">
									<div class="num" id="tcNumSaldoCertificado">$0</div>
									<div class="lbl">Saldo total certificado</div>
								</div>
							</div>
						</div>

						<div class="tc-tabla-cont">
							<div class="tc-tabla-scroll">
								<table id="tcTabla">
									<thead>
										<tr>
											<th>Estructura</th>
											<th>Capturó</th>
											<th>Folio</th>
											<th>Folio sol. traspaso</th>
											<th>Fecha traspaso</th>
											<th>Saldo</th>
											<th>Semana</th>
											<th>Cancelación</th>
											<th>Fecha trámite</th>
											<th>Clave usuario</th>
											<th>Fecha captura</th>
											<th>Medio cancelación</th>
											<th>PPlus</th>
										</tr>
									</thead>
									<tbody id="tcTablaBody">
										<tr><td colspan="13" style="padding:0;">
											<div class="tc-vacio">
												<svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
												<p><strong>Sin datos por ahora.</strong><br>Selecciona una semana comercial y da clic en Buscar.</p>
											</div>
										</td></tr>
									</tbody>
								</table>
							</div>
						</div>

					</div>
<?
				$divcontenido->close();
			$divpie->close();
?>

		<div id="tcLoader">
			<div class="tc-loader-box">
				<div class="spinner-border" role="status"></div>
				<div class="tc-loader-txt">Consultando...</div>
			</div>
		</div>

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
<?php
		}
?>
    </body>

	<script type="text/javascript">
		var API = 'transfer_cancelaciones_api.php';
		var ultimosDatos = [];

		jQuery(document).ready(function()
	   	{
   		   	$(window).resize(function ()
   	    	{
   	    		centra_contenedor();
	   	    });
   		   	$(window).resize();

   		 	$('#men_pie').css("top",h);

  			$('#tmenu td').click(function(evento)
  		  	{
  	  		  	var modulo=$(this).text().replace(/ /gi,"_");
  	  			$("#opcion").load("menu.php?modulo=" + modulo);
  		  	});

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="http://carobra.ddns.me";
  		  	});

  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

		});

		/* ---------- Autocomplete de semana comercial ---------- */

		var temporizadorBusqueda = null;

		document.getElementById('tcSemana').addEventListener('input', function(){
			var termino = this.value.trim();
			clearTimeout(temporizadorBusqueda);

			if(termino === ''){
				document.getElementById('tcSugerencias').style.display = 'none';
				return;
			}

			temporizadorBusqueda = setTimeout(function(){
				fetch(API + '?action=getSemanas&termino=' + encodeURIComponent(termino))
					.then(res => res.json())
					.then(json => {
						var cont = document.getElementById('tcSugerencias');
						cont.innerHTML = '';

						if(json.status !== 'ok' || !json.data.length){
							cont.style.display = 'none';
							return;
						}

						json.data.forEach(function(valor){
							var div = document.createElement('div');
							div.textContent = valor;
							div.onclick = function(){
								document.getElementById('tcSemana').value = valor;
								cont.style.display = 'none';
							};
							cont.appendChild(div);
						});

						cont.style.display = 'block';
					})
					.catch(err => console.error(err));
			}, 250);
		});

		document.addEventListener('click', function(e){
			if(!document.getElementById('tcAutocomplete').contains(e.target)){
				document.getElementById('tcSugerencias').style.display = 'none';
			}
		});

		/* ---------- Buscar ---------- */

		document.getElementById('tcBtnBuscar').addEventListener('click', function(){

			var semana = document.getElementById('tcSemana').value.trim();

			if(semana === ''){
				Swal.fire({ icon: 'warning', title: 'Falta la semana', text: 'Selecciona una semana comercial primero.' });
				return;
			}

			document.getElementById('tcLoader').style.display = 'flex';

			var cancelacion = document.getElementById('tcCancelacion').value;

			var formData = new FormData();
			formData.append('action', 'buscar');
			formData.append('semana', semana);
			formData.append('cancelacion', cancelacion);

			fetch(API, { method: 'POST', body: formData })
				.then(res => res.json())
				.then(json => {
					document.getElementById('tcLoader').style.display = 'none';

					if(json.status !== 'ok'){
						Swal.fire({ icon: 'error', title: 'Error', text: json.message || json.mysql || 'No se pudo consultar.' });
						return;
					}

					ultimosDatos = json.data;
					mostrarResultados(json.data);
				})
				.catch(err => {
					document.getElementById('tcLoader').style.display = 'none';
					console.error(err);
					Swal.fire({ icon: 'error', title: 'Error', text: 'Fallo la conexión con el servidor.' });
				});
		});

		function iniciales(nombre){
			if(!nombre) return '--';
			var partes = String(nombre).trim().split(/\s+/);
			var ini = partes[0].charAt(0) + (partes.length > 1 ? partes[1].charAt(0) : '');
			return ini.toUpperCase();
		}

		function escapaHtml(valor){
			var div = document.createElement('div');
			div.textContent = valor ?? '';
			return div.innerHTML;
		}

		function mostrarResultados(data){

			var tbody = document.getElementById('tcTablaBody');
			tbody.innerHTML = '';

			if(!data.length){
				tbody.innerHTML =
					'<tr><td colspan="13" style="padding:0;">' +
						'<div class="tc-vacio">' +
							'<svg width="42" height="42" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="8" y1="12" x2="16" y2="12"/></svg>' +
							'<p><strong>Sin resultados.</strong><br>No hay cancelaciones capturadas para esa semana.</p>' +
						'</div>' +
					'</td></tr>';
				document.getElementById('tcResumen').style.display = 'none';
				document.getElementById('tcBtnExportar').style.display = 'none';
				return;
			}

			var sumaSaldo             = 0;
			var numCertificadas       = 0;
			var sumaSaldoCertificado  = 0;

			data.forEach(function(row){
				var saldoFila = parseFloat(row.saldo) || 0;
				sumaSaldo += saldoFila;

				/* "Certificada" = cancelacion == 1 (viene como string desde MySQL). */
				var esCertificada = (row.cancelacion == '1' || Number(row.cancelacion) === 1);
				if(esCertificada){
					numCertificadas++;
					sumaSaldoCertificado += saldoFila;
				}

				var tr = document.createElement('tr');
				tr.innerHTML =
					'<td title="' + escapaHtml(row.estructura) + '">' + escapaHtml(row.estructura) + '</td>' +
					'<td><div class="tc-avatar"><div class="tc-avatar-circulo">' + iniciales(row.nombre) + '</div>' + escapaHtml(row.nombre) + '</div></td>' +
					'<td class="tc-folio">' + escapaHtml(row.folio) + '</td>' +
					'<td>' + escapaHtml(row.sol_traspaso) + '</td>' +
					'<td>' + escapaHtml(row.fec_traspaso) + '</td>' +
					'<td class="tc-saldo">' + formatoMoneda(row.saldo) + '</td>' +
					'<td><span class="tc-badge">' + escapaHtml(row.semana) + '</span></td>' +
					'<td>' + escapaHtml(row.cancelacion) + '</td>' +
					'<td>' + escapaHtml(row.fec_tramite) + '</td>' +
					'<td>' + escapaHtml(row.clave) + '</td>' +
					'<td>' + escapaHtml(row.fecha_captura) + '</td>' +
					'<td><span class="tc-badge">' + escapaHtml(row.medio_cancelacion) + '</span></td>' +
					'<td>' + sioNo(row.pplus) + '</td>';
				tbody.appendChild(tr);
			});

			var elRegistros = document.getElementById('tcNumRegistros');
			var elSaldo = document.getElementById('tcNumSaldo');
			var elCertificadas = document.getElementById('tcNumCertificadas');
			var elSaldoCertificado = document.getElementById('tcNumSaldoCertificado');

			elRegistros.textContent = data.length;
			elSaldo.textContent = formatoMoneda(sumaSaldo);
			elSaldo.title = formatoMoneda(sumaSaldo);
			elCertificadas.textContent = numCertificadas;
			elSaldoCertificado.textContent = formatoMoneda(sumaSaldoCertificado);
			elSaldoCertificado.title = formatoMoneda(sumaSaldoCertificado);

			document.getElementById('tcResumen').style.display = 'grid';
			document.getElementById('tcBtnExportar').style.display = 'inline-flex';
		}

		/* pplus viene de la tabla clawback como 0/1 -- 0 = No, 1 = Sí. */
		function sioNo(valor){
			if(valor === null || valor === undefined || valor === '') return '—';
			return (Number(valor) === 1) ? 'Sí' : 'No';
		}

		function formatoMoneda(valor){
			var n = parseFloat(valor) || 0;
			return '$' + n.toLocaleString('es-MX', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
		}

		/* ---------- Exportar a Excel ---------- */

		document.getElementById('tcBtnExportar').addEventListener('click', function(){

			if(!ultimosDatos.length) return;

			var columnas = ['estructura','nombre','folio','sol_traspaso','fec_traspaso','saldo','semana','cancelacion','fec_tramite','clave','fecha_captura','medio_cancelacion','pplus'];
			var encabezados = ['Estructura','Capturó','Folio','Folio sol. traspaso','Fecha traspaso','Saldo','Semana','Cancelación','Fecha trámite','Clave usuario','Fecha captura','Medio cancelación','PPlus'];

			var filas = [encabezados];

			ultimosDatos.forEach(function(row){
				var fila = columnas.map(function(col){
					var val = row[col];
					if(col === 'saldo'){
						var n = parseFloat(val);
						return isNaN(n) ? 0 : n;
					}
					if(col === 'pplus'){
						return sioNo(val);
					}
					return val ?? '';
				});
				filas.push(fila);
			});

			var ws = XLSX.utils.aoa_to_sheet(filas);
			var wb = XLSX.utils.book_new();
			XLSX.utils.book_append_sheet(wb, ws, 'Cancelaciones');

			var semana = document.getElementById('tcSemana').value.trim().replace(/[^a-zA-Z0-9]/g, '_');
			XLSX.writeFile(wb, 'cancelaciones_' + semana + '.xlsx');
		});
	</script>
</html>