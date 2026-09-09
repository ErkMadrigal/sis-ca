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

		<style>
            .vac-approve-card {
                background: #fff;
                border-radius: 15px;
                padding: 18px;
                box-shadow: 0 10px 25px rgba(0,0,0,0.08);
                border-left: 6px solid #f59e0b;
                transition: 0.3s;
            }

            .vac-approve-card:hover {
                transform: translateY(-3px);
            }

            /* HEADER */
            .vac-approve-header {
                display: flex;
                justify-content: space-between;
                align-items: center;
                margin-bottom: 15px;
            }

            .user-info {
                display: flex;
                align-items: center;
                gap: 10px;
            }

            .avatar {
                font-size: 28px;
                background: #f1f5f9;
                padding: 10px;
                border-radius: 50%;
            }

            /* BODY */
            .vac-approve-body {
                margin-bottom: 15px;
            }

            .vac-row {
                display: flex;
                justify-content: space-between;
                margin-bottom: 8px;
                font-size: 14px;
            }

            .vac-row.note {
                display: block;
            }

            .vac-row.note p {
                margin: 5px 0 0;
                color: #64748b;
            }

            /* FOOTER */
            .vac-approve-footer {
                display: flex;
                justify-content: flex-end;
                gap: 10px;
            }

            /* BADGE */
            .status-badge {
                font-size: 12px;
            }
            #comentarioJefe {
                border-radius: 10px;
                border: 1px solid #e2e8f0;
                resize: none;
                transition: 0.2s;
            }

            #comentarioJefe:focus {
                border-color: #3b82f6;
                box-shadow: 0 0 0 2px rgba(59,130,246,0.2);
            }

            .vac-deny-card {
                background: #fff;
                border-radius: 15px;
                padding: 20px;
                border-left: 6px solid #ef4444;
                box-shadow: 0 8px 20px rgba(0,0,0,0.08);
                transition: 0.2s;
            }

            .vac-deny-card:hover {
                transform: translateY(-2px);
            }

            /* contenido */
            .vac-deny-content {
                display: flex;
                align-items: center;
                gap: 15px;
            }

            /* icono */
            .vac-deny-icon {
                font-size: 30px;
                background: #fee2e2;
                color: #dc2626;
                padding: 10px;
                border-radius: 50%;
            }

            /* texto */
            .vac-deny-text h5 {
                margin: 0;
                color: #dc2626;
                font-weight: bold;
            }

            .vac-deny-text p {
                margin: 5px 0 0;
                color: #7f1d1d;
                font-size: 14px;
            }

            .vac-deny-card {
                box-shadow: 0 0 0 2px rgba(239,68,68,0.2), 0 8px 20px rgba(0,0,0,0.08);
            }

            /* ── Doble aprobación: tarjetas de estatus jefe/RRHH ── */
            .doble-aprob-wrap {
                display: flex;
                gap: 12px;
                margin-bottom: 16px;
                flex-wrap: wrap;
            }

            .doble-aprob-item {
                flex: 1;
                min-width: 160px;
                background: #f8fafc;
                border-radius: 10px;
                padding: 10px 14px;
                border: 1px solid #e2e8f0;
            }

            .doble-aprob-item .titulo {
                font-size: 11px;
                font-weight: 700;
                color: #64748b;
                text-transform: uppercase;
                letter-spacing: .04em;
                margin-bottom: 4px;
            }

            .doble-aprob-item.ok {
                border-color: #16a34a;
                background: #f0fdf4;
            }

            .doble-aprob-item.rechazado {
                border-color: #dc2626;
                background: #fef2f2;
            }

            .doble-aprob-item .comentario-mini {
                font-size: 12px;
                color: #475569;
                margin-top: 4px;
                font-style: italic;
            }
        </style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<body>
		<div id="loader" style="
			display:none;
			position: fixed;
			top:0;
			left:0;
			width:100%;
			height:100%;
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

		function getEstatus($estatus){

			switch ($estatus) {
				case 0:
					return '<span class="badge badge-primary status-badge">Pendiente</span>';
				case 1:
					return '<span class="badge badge-success status-badge">Aprobado</span>';
				case 2:
					return '<span class="badge badge-danger status-badge">Rechazado</span>';
				default:
					return '<span class="badge badge-dark status-badge">Desconocido</span>';
			}

		}

		function formatoFecha($fecha){

			if (empty($fecha) || $fecha == '0000-00-00') {
				return '';
			}

			$timestamp = strtotime($fecha);

			if (!$timestamp) {
				return '';
			}

			return date('d/m/Y', $timestamp);
		}

		$sesion_login = false;
		$fg = $_SESSION["ultimoAcceso"];
    	$fa = date("Y-n-j H:i:s");
    	$periodo = (strtotime($fa)-strtotime($fg));
     	if($periodo > 600)
     	{
      		session_destroy();
			$sesion_login = true;

    	}
    	else
    	{
    		$_SESSION["ultimoAcceso"] = $fa;
    		$_SESSION["autentifica"] = "p";
   		}

		if ($_SESSION["autentifica"] != "p")
		{
			session_destroy();
			$sesion_login = true;

		}
		else
		{
			//--------------------------------------------------------------------------------------------------
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
				titulo("Vacaciones");
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
				//----------------------------------------------------------------------------Declaración de objetos
				$conecta= new conector();

				$nivel= nivel_usuario(utf8_decode($_SESSION["nombre"]));
				$query="Select clave from empl_usuarios 
						where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
				$bd=$conecta->consulta($query);
				$tb=mysqli_fetch_array($bd);
				$clave=$tb['clave'];

				$ne=0;
				$usuario=$_SESSION["nombre"];

				
	?>
				<div class="container">
					
					<div class="row justify-content-center">
						<div class="col-md-12">

							<?php
                                $claveSolicitud = $_GET['clv'];
                                $uuid           = $_GET['uuid'];
								$query_data = "SELECT sv.*, u.nombre FROM solicitudes_vacaciones as sv
                                                LEFT JOIN usuarios as u on sv.clave = u.clave
                                                WHERE sv.clave = '$claveSolicitud' and id = $uuid";

								$data_u = $conecta->consulta($query_data);
								$data_usr = mysqli_fetch_array($data_u);

								/* Quién de RRHH resolvió, si ya lo hizo */
								$nombreRrhh = '';
								if (!empty($data_usr['clave_rrhh'])) {
									$claveRrhhEsc = mysqli_real_escape_string($conecta->conexion, $data_usr['clave_rrhh']);
									$rsRrhhNom = $conecta->consulta("SELECT nombre FROM usuarios WHERE clave='$claveRrhhEsc' LIMIT 1");
									$rowRrhhNom = $rsRrhhNom ? mysqli_fetch_assoc($rsRrhhNom) : null;
									if ($rowRrhhNom) $nombreRrhh = $rowRrhhNom['nombre'];
								}

								/* ── Validación real de permiso: la clave de la persona logueada
								   (resuelta arriba en $clave) debe coincidir con el jefe_directo
								   de la solicitud. Antes esto comparaba contra un nombre fijo
								   hardcodeado ("JAGONZALEZ"), lo cual permitía que solo esa
								   persona aprobara cualquier solicitud sin importar quién fuera
								   el jefe real. ── */
								$esElJefeCorrecto = ($clave === $data_usr['jefe_directo']);
							?>
							

							<div class="container mt-4">
                                <?php if($esElJefeCorrecto): ?>
                                    <div class="vac-approve-card">

                                        <div class="vac-approve-header">
                                            <div class="user-info">
                                                <div class="avatar">👤</div>
                                                <div>
                                                    <h5 id="usrNombre"><?= $data_usr['nombre']; ?></h5>
                                                    <small>• <?= $data_usr['clave']; ?></small>
                                                </div>
                                            </div>

                                            <?= getEstatus($data_usr['estatus']); ?>
                                        </div>

                                        <!-- BODY -->
                                        <div class="vac-approve-body">

                                            <div class="vac-row">
                                                <span>📅 Fechas</span>
                                                <strong id="usrFechas"><?= formatoFecha($data_usr['fecha_inicio']); ?> - <?= formatoFecha($data_usr['fecha_final']); ?></strong>
                                            </div>

                                            <div class="vac-row">
                                                <span>⏱ Días</span>
                                                <strong id="usrDias"><?= $data_usr['dias_tomados']; ?> días</strong>
                                            </div>

                                            <div class="vac-row">
                                                <span>📊 Periodo</span>
                                                <strong id="usrPeriodo"><?= $data_usr['periodo']; ?></strong>
                                            </div>

                                            <!-- ── Doble aprobación: estatus de jefe y de RRHH lado a lado ── -->
                                            <div class="doble-aprob-wrap">
                                                <div class="doble-aprob-item <?= $data_usr['estatus_jefe'] == 1 ? 'ok' : ($data_usr['estatus_jefe'] == 2 ? 'rechazado' : '') ?>">
                                                    <div class="titulo">Tu aprobación (Jefe)</div>
                                                    <?= getEstatus($data_usr['estatus_jefe']); ?>
                                                    <?php if (!empty($data_usr['comentario_jefe'])): ?>
                                                        <div class="comentario-mini">"<?= htmlspecialchars($data_usr['comentario_jefe']) ?>"</div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="doble-aprob-item <?= $data_usr['estatus_rrhh'] == 1 ? 'ok' : ($data_usr['estatus_rrhh'] == 2 ? 'rechazado' : '') ?>">
                                                    <div class="titulo">Recursos Humanos</div>
                                                    <?= getEstatus($data_usr['estatus_rrhh']); ?>
                                                    <?php if (!empty($nombreRrhh)): ?>
                                                        <div class="comentario-mini">Resolvió: <?= htmlspecialchars($nombreRrhh) ?></div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($data_usr['comentario_rrhh'])): ?>
                                                        <div class="comentario-mini">"<?= htmlspecialchars($data_usr['comentario_rrhh']) ?>"</div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>

                                            <!-- INPUT DEL JEFE -->
                                            <?php if($data_usr['estatus_jefe'] == 1 || $data_usr['estatus_jefe'] == 2 ): ?>
                                                <div class="vac-row note">
                                                    <span>📝 Tu comentario</span>
                                                    <p><?= htmlspecialchars($data_usr['comentario_jefe']) ?></p>
                                                </div>
                                            <?php else: ?>
                                                
                                                <div class="vac-row note">
                                                    <span>📝 Comentario del jefe</span>
                                                    <textarea id="comentarioJefe" class="form-control mt-2" rows="3" placeholder="Escribe un comentario..."></textarea>
                                                </div>
                                            <?php endif; ?>    

                                        </div>

                                        <!-- FOOTER -->
                                        <?php $clave_usr = $data_usr['clave']; $jefe = $data_usr['jefe_directo']; ?>
                                        <?php if($data_usr['estatus_jefe'] == 0 ): ?>
                                            <div class="vac-approve-footer">
                                                <button class="btn btn-success btn-sm" onclick="aprobarVacacion(<?= $data_usr['id']; ?>, '<?= $clave_usr; ?>', '<?= $jefe; ?>')">
                                                    ✔ Aprobar
                                                </button>

                                                <button class="btn btn-danger btn-sm" onclick="rechazarVacacion(<?= $data_usr['id']; ?>, '<?= $clave_usr; ?>', '<?= $jefe; ?>')">
                                                    ✖ Rechazar
                                                </button>
                                            </div>
                                        <?php else: ?>
                                            <p class="text-muted text-center mt-2" style="font-size:13px;">
                                                Ya registraste tu resolución para esta solicitud.
                                                <?php if ($data_usr['estatus_rrhh'] == 0): ?>
                                                    <br>Falta la aprobación de Recursos Humanos para que quede confirmada.
                                                <?php endif; ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="vac-deny-card">

                                        <div class="vac-deny-content">

                                            <div class="vac-deny-icon">
                                                ⛔
                                            </div>

                                            <div class="vac-deny-text">
                                                <h5>No autorizado</h5>
                                                <p>
                                                    No tienes permisos para autorizar estas vacaciones.
                                                    Contacta a tu supervisor o administrador.
                                                </p>
                                            </div>

                                        </div>

                                    </div>
                                <?php endif; ?>
							</div>

							
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>



	<?php } ?>

	<?php if($sesion_login): ?>
		<div id="login-overlay" style="
			position: fixed; top:0; left:0; width:100%; height:100%;
			background: rgba(0,0,0,0.55);
			z-index: 9999;
			display: flex; align-items: center; justify-content: center;
		">
			<div style="background:#fff; border-radius:16px; padding:2rem 2.5rem; width:100%; max-width:380px; box-shadow: 0 20px 60px rgba(0,0,0,0.2);">
				
				<div style="text-align:center; margin-bottom:1.5rem;">
				<p style="font-size:20px; font-weight:600; margin:0;">Iniciar sesión</p>
				<p style="font-size:13px; color:#64748b; margin:4px 0 0;">| CAROBRA</p>
				</div>

				<div style="margin-bottom:1rem;">
				<label style="font-size:13px; color:#64748b; display:block; margin-bottom:6px;">Usuario</label>
				<input type="text" id="inp-usr" class="form-control" placeholder="Ingresa tu usuario">
				</div>

				<div style="margin-bottom:1.25rem;">
				<label style="font-size:13px; color:#64748b; display:block; margin-bottom:6px;">Contraseña</label>
				<input type="password" id="inp-pass" class="form-control" placeholder="••••••••">
				</div>

				<div id="login-error" class="alert alert-danger" style="display:none; font-size:13px;">
				Usuario o contraseña incorrectos
				</div>

				<button id="btn-login" class="btn btn-primary btn-block" onclick="doLogin()">
				Entrar
				</button>
			</div>
		</div>	
		<script>
			document.getElementById('inp-pass').addEventListener('keydown', function(e){
			if(e.key === 'Enter') doLogin();
			});

			async function doLogin(){
			const usr = document.getElementById('inp-usr').value.trim();
			const pass = document.getElementById('inp-pass').value;
			const btn  = document.getElementById('btn-login');
			const err  = document.getElementById('login-error');

			if(!usr || !pass){ err.style.display='block'; err.textContent='Completa los campos'; return; }

			btn.textContent = 'Verificando...';
			btn.disabled = true;
			err.style.display = 'none';

			try {
				let res = await fetch('vacaciones_api.php', {
				method: 'POST',
				headers: {'Content-Type': 'application/json'},
				body: JSON.stringify({ action: 'login', usr: usr, pass: pass })
				});
				let data = await res.json();

				if(data.status === 'ok'){
				location.reload();
				} else {
				err.textContent = data.msg || 'Usuario o contraseña incorrectos';
				err.style.display = 'block';
				}
			} catch(e){
				err.textContent = 'Error de conexión';
				err.style.display = 'block';
			}

			btn.textContent = 'Entrar';
			btn.disabled = false;
			}
		</script>
	<?php endif; ?>

    </body>

	<script type="text/javascript">
		jQuery(document).ready(function()
	   	{
   		   	$(window).resize(function ()
   	    	{
   	    		centra_contenedor();
	   	    });
   		   	$(window).resize();

   		 	$('#men_pie').css("top",h);
   		 	$('#men_pie').css("background","red");

  			$('#tmenu td').click(function(evento)
  		  	{
  	  		  	var modulo=$(this).text().replace(/ /gi,"_");
  	  			$("#opcion").load("menu.php?modulo=" + modulo);
  		  	});

  		  	$("#salir").click(function()
  		  	{
  		  		window.location="https://www.sis-ca.com.mx:7863/";
  		  	});
  		  	
  		  	$("#email").click(function()
  		  	{
  		  		window.open("http://webmail.carobra.com.mx/imp/login.php");
  		  	});

			$(".datepicker").datepicker(
			{
				format: "dd/mm/yyyy"
			});

		});

        function aprobarVacacion(id, clave, jefe_directo){

           let comentario = document.getElementById("comentarioJefe").value.trim();

            if(comentario === ""){
                alert("Debes ingresar un comentario");
                return;
            }

            let estatus = 1

            fetch('vacaciones_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'authVacaciones',
                    comentario: comentario, 
                    id: id, 
                    clave: clave, 
                    estatus: estatus,
                    jefe_directo: jefe_directo
                })
            })
            .then(res => res.json())
            .then(data => {
        
                Swal.fire({
                    title: data.mgs,
                    icon: "success",
                    draggable: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ok"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); 
                    }
                });
            });
        }


        function rechazarVacacion(id, clave, jefe_directo){

            let comentario = document.getElementById("comentarioJefe").value.trim();

            if(comentario === ""){
                alert("Debes ingresar un motivo de rechazo");
                return;
            }

            let estatus = 2
            fetch('vacaciones_api.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    action: 'authVacaciones',
                    comentario: comentario, 
                    id: id, 
                    clave: clave, 
                    estatus: estatus,
                    jefe_directo: jefe_directo
                })
            })
            .then(res => res.json())
            .then(data => {
                 Swal.fire({
                    title: data.mgs,
                    icon: "success",
                    draggable: true,
                    confirmButtonColor: "#3085d6",
                    confirmButtonText: "Ok"
                }).then((result) => {
                    if (result.isConfirmed) {
                        location.reload(); 
                    }
                });
            });
        }

	</script>
</html>