<?php
/* ============================================================
   SECCIÓN 1: INICIO DE SESIÓN
   ============================================================ */
session_start();
?>

<!-- ============================================================
     SECCIÓN 2: HTML - CABECERA DEL DOCUMENTO
     ============================================================ -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, minimum-scale=1.0">
	<title>CAROBRA Consultores Especializados</title>

	<!-- Favicon -->
	<link rel="shortcut icon" href="./img/favicon.ico">

	<!-- JS propio -->
	<script type="text/javascript" src="./js/carobra.js"></script>

	<!-- jQuery -->
	<script src="./jquery/jquery-3.3.1.min.js"></script>

	<!-- Bootstrap -->
	<link rel="stylesheet" href="./bootstrap/css/bootstrap.min.css">
	<script src="./popper/popper.js"></script>
	<script src="./bootstrap/js/bootstrap.min.js"></script>

	<!-- Autocomplete -->
	<script src="./twitter/typeahead.bundle.js"></script>

	<!-- Datepicker -->
	<link rel="stylesheet" href="./css/datepicker.css">
	<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

	<!-- CSS propio -->
	<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>

    <style>

        body{
            background: #edf2f7;
            font-family: 'Segoe UI', sans-serif;
        }

        .main-card{
            margin-top: 50px;
            border: none;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 20px 50px rgba(0,0,0,.08);
        }

        .header-card{
            background: linear-gradient(135deg,#2184c4,#004179);
            padding: 35px;
            color: white;
        }

        .title-main{
            font-size: 32px;
            font-weight: 800;
        }

        .subtitle-main{
            opacity: .8;
            margin-top: 5px;
        }

        /* BOTÓN NUEVA CAMPAÑA */
        .btn-new-campaign{
            background: rgba(255,255,255,.15);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255,255,255,.2);
            color: white;
            padding: 14px 24px;
            border-radius: 18px;
            font-weight: 700;
            transition: .3s;
        }

        .btn-new-campaign:hover{
            background: rgba(255,255,255,.25);
            transform: translateY(-2px);
        }

        .table-container{
            padding: 30px;
            background: white;
        }

        .table{
            vertical-align: middle;
        }

        .table thead th{
            background: #f4f6fa;
            border: none;
            padding: 18px;
            color: #344054;
            font-size: 15px;
        }

        .table tbody td{
            padding: 18px;
            border-color: #edf2f7;
            font-weight: 500;
        }

        .table tbody tr{
            transition: .2s;
        }

        .table tbody tr:hover{
            background: #f8fbff;
        }

        /* BADGES */
        .badge-active{
            background: rgba(25,135,84,.15);
            color: #198754;
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
        }

        .badge-inactive{
            background: rgba(220,53,69,.15);
            color: #dc3545;
            padding: 10px 18px;
            border-radius: 50px;
            font-size: 13px;
            font-weight: 700;
        }

        /* BOTÓN INACTIVAR */
        .btn-inactivar{
            width: 48px;
            height: 48px;
            border-radius: 14px;
            border: none;
            background: linear-gradient(135deg,#ff4d6d,#c9184a);
            color: white;
            font-size: 18px;
            transition: .25s;
            box-shadow: 0 8px 18px rgba(201,24,74,.25);
        }

        .btn-inactivar:hover{
            transform: scale(1.08);
            background: linear-gradient(135deg,#ff758f,#c9184a);
            box-shadow: 0 12px 24px rgba(201,24,74,.35);
        }

        /* MODAL */
        .modal-content{
            border: none;
            border-radius: 30px;
            overflow: hidden;
            box-shadow: 0 25px 60px rgba(0,0,0,.25);
        }

        .modal-header{
            background: linear-gradient(135deg,#2184c4,#004179);
            border: none;
            padding: 30px;
            color: white;
        }

        .modal-title{
            font-size: 30px;
            font-weight: 800;
        }

        .modal-body{
            padding: 40px;
            background: #f8fbff;
        }

        .form-label{
            font-weight: 700;
            color: #344054;
            margin-bottom: 10px;
        }

        .form-control{
            height: 58px;
            border-radius: 18px;
            border: 1px solid #dce3ec;
            padding-left: 15px;
            font-size: 15px;
        }

        .form-control:focus{
            border-color: #0d6efd;
            box-shadow: 0 0 0 5px rgba(13,110,253,.15);
        }

        .btn-save{
            height: 58px;
            border-radius: 18px;
            border: none;
            font-size: 16px;
            font-weight: 700;
            color: white;
            /* background: linear-gradient(135deg,#198754,#20c997); */
            background: linear-gradient(135deg,#0d6efd,#6610f2);
            box-shadow: 0 10px 25px rgba(25,135,84,.25);
            transition: .3s;
        }

        .btn-save:hover{
            transform: translateY(-2px);
        }


        /* ESTATUS MÁS PEQUEÑO */
        .status-pill{
            padding: 5px 12px;
            border-radius: 50px;
            font-size: 11px;
            font-weight: 700;
            display: inline-block;
        }

        .status-active{
            background: rgba(25,135,84,.12);
            color: #198754;
        }

        .status-inactive{
            background: rgba(220,53,69,.12);
            color: #dc3545;
        }


        /* SWITCH */
        .switch{
            position: relative;
            display: inline-block;
            width: 58px;
            height: 30px;
        }

        .switch input{
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider{
            position: absolute;
            cursor: pointer;
            inset: 0;
            background: #dbe4ee;
            transition: .4s;
            border-radius: 50px;
        }

        .slider:before{
            position: absolute;
            content: "";
            height: 22px;
            width: 22px;
            left: 4px;
            bottom: 4px;
            background: white;
            transition: .4s;
            border-radius: 50%;
            box-shadow: 0 3px 10px rgba(0,0,0,.2);
        }

        .switch input:checked + .slider{
            background: linear-gradient(135deg,#ff4d6d,#c9184a);
        }

        .switch input:checked + .slider:before{
            transform: translateX(28px);
        }


        /* SELECT VERGAS */
        .select-comision{
            height: 58px;
            border-radius: 18px;
            border: 1px solid #dce3ec;
            background: white;
            font-weight: 700;
            color: #344054;
            padding-left: 15px;
            transition: .3s;
        }

        .select-comision:focus{
            border-color: #6610f2;
            box-shadow: 0 0 0 5px rgba(102,16,242,.12);
        }

        .table-container{
            padding: 30px;
            background: white;
            max-height: 600px;
            overflow-y: auto;
        }


        /* SCROLL BONITO */
        .table-container::-webkit-scrollbar{
            width: 10px;
        }

        .table-container::-webkit-scrollbar-track{
            background: #edf2f7;
            border-radius: 20px;
        }

        .table-container::-webkit-scrollbar-thumb{
            background: linear-gradient(
                180deg,
                #0d6efd,
                #6610f2
            );

            border-radius: 20px;
        }
    </style>

</head>

<?php
/* ============================================================
   SECCIÓN 4: PHP - INCLUDE DE CLASES
   ============================================================ */
include("./funciones/clas_carobra.php");
?>

<body>

    <?php
        /* ============================================================
        SECCIÓN 5: PHP - VALIDACIÓN DE SESIÓN Y LÓGICA PRINCIPAL
        ============================================================ */
        $fg      = $_SESSION["ultimoAcceso"];
        $fa      = date("Y-n-j H:i:s");
        $periodo = (strtotime($fa) - strtotime($fg));

        if ($periodo > 6000)
        {
            session_destroy();
            mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "http://carobra.ddns.me:8888");
        }
        else
        {
            $_SESSION["ultimoAcceso"] = $fa;
            $_SESSION["autentifica"]  = "p";
        }

        if ($_SESSION["autentifica"] != "p")
        {
            session_destroy();
            mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "index.php");
        }
        else
        {
            reg_acceso($_SESSION["nombre"], "empleados_dp");
            $_SESSION["autentifica"] = "";

            $divpie       = new div("pie");
            $divopcion    = new div("opcion");
            $divcabecera  = new div("cabecera");
            $divcontenido = new div("contenido");
            $divcontenedor= new div("contenedor");
            $imgpie       = new img("imgpie",      "./img/pie.png");
            $divusuario   = new div("usuario", '', "absolute", "97%");
            $imgcabecera  = new img("imgcabecera", "./img/cabecera.png");

            /* --- Cabecera --- */
            $divcabecera->crear();
                $imgcabecera->crear();
                $imgcabecera->css("width", "100%");
                titulo("Campañas Especiales");
                $pag = "INTERFACES";
                menu();
            $divcabecera->close();

            /* --- Pie --- */
            $divpie->crear();
                $imgpie->crear();
                $imgpie->css("width", "100%");
                $divusuario->crear();
                    echo "<br><br>";
                    $divusuario->css("top",        "10px");
                    $divusuario->css("text-align", "left");
    ?>

	<!-- ============================================================
	     SECCIÓN 6: HTML - VÍNCULOS Y USUARIO
	     ============================================================ -->
	<div id="vinculos" style="margin-top: 10px; margin-left: 25px;">
		<?php include("vinculos.php"); ?>
	</div>

    <?php
        echo "&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<spam>Usuario: " . $_SESSION["nombre"] . "</spam>";
        echo "<div style='width:100%; text-align:right; margin-top:-25px;'>";
        echo "<spam id='email'>Email</spam>";
        echo "&nbsp;&nbsp;&nbsp;<spam id='salir'>Salir</spam>";
        echo "</div>";
		$divusuario->close();
		$divpie->close();

        /* --- Contenedor principal --- */
    	$divcontenedor->crear();

		/* --- Menú de opciones --- */
		$divopcion->crear();
		$divopcion->css("background", "#FFFFFF");
		$divopcion->close();

		/* --- Contenido --- */
		$divcontenido->crear();
		$divcontenido->css("overflow-y", "auto");

		$conecta  = new conector();
		$fecha    = addslashes(trim($_POST["fecha"]));
		$mensaje  = addslashes(trim($_POST["mensaje"]));
		$dispersa = addslashes(trim($_POST["dispersa"]));
    ?>

	<!-- ============================================================
	     SECCIÓN 7: HTML - FORMULARIO PRINCIPAL
	     ============================================================ -->
	
        <div class="container">

            <div class="card main-card">

                <!-- HEADER -->
                <div class="header-card d-flex justify-content-between align-items-center">

                    <div>

                        <div class="title-main">
                            <i class="fa-solid fa-users"></i>
                            Mayores a 60 Años
                        </div>

                        <div class="subtitle-main">
                            Administración de campañas y comisiones
                        </div>

                    </div>

                    <button class="btn btn-new-campaign"
                                data-toggle="modal"
                                data-target="#modalCampaign">

                        <i class="fa-solid fa-plus"></i>
                        Nueva Campaña

                    </button>

                </div>

                <!-- TABLA -->
                <div class="table-container">

                    <div class="table-responsive">

                        <table class="table align-middle">

                            <thead>

                                <tr>
                                    <th>Edad mínima</th>
                                    <th>Edad máxima</th>
                                    <th>Fecha inicial</th>
                                    <th>Fecha final</th>
                                    <th>% Comisión</th>
                                    <th>Estatus</th>
                                    <th>Acciones</th>
                                </tr>

                            </thead>

                            <tbody id="tbodyCampanias">

                               

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>
        
                
    <?php
        $divcontenido->close();
        $divpie->close();
    ?>

    <!-- ============================================================
     SECCIÓN 8: JAVASCRIPT - LAYOUT INICIAL
     ============================================================ -->
    <script language='javascript'>
        var hc = calcula_h(125);
        var h  = $(window).outerHeight() - hc;

        $('#pie').css("top", h + 5);

        hc = calcula_h(85);
        h  = $(window).outerHeight() - (hc * 2);
        $('#contenedor').css("top",    hc - 15);
        $('#contenedor').css("height", h);

        hc = calcula_h(45);
        $('#menu').css("top", hc - 20);

        $('#opcion').load('menu.php?modulo=' + '<?php echo $pag?>');
    </script>

    <!-- MODAL -->
    <div class="modal fade"
        id="modalCampaign"
        tabindex="-1"
        role="dialog"
        aria-hidden="true">

        <div class="modal-dialog modal-xl modal-dialog-centered">

            <div class="modal-content">

                <div class="modal-header">

                    <div>

                        <div class="modal-title">
                            <i class="fa-solid fa-bullhorn"></i>
                            Nueva Campaña
                        </div>

                        <div class="mt-2 opacity-75">
                            Configura campañas para personas mayores de 60 años
                        </div>

                    </div>

                    <button type="button"
                            class="close text-white"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <form id="formCampaign">

                        <div class="row g-4">

                            <div class="col-md-6">

                                <label class="form-label">
                                    Edad mínima
                                </label>

                                <input type="number"
                                    class="form-control"
                                    id="edad_minima"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Edad máxima
                                </label>

                                <input type="number"
                                    class="form-control"
                                    id="edad_maxima"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Fecha inicial
                                </label>

                                <input type="date"
                                    class="form-control"
                                    id="fecha_inicial"
                                    required>

                            </div>

                            <div class="col-md-6">

                                <label class="form-label">
                                    Fecha final
                                </label>

                                <input type="date"
                                    class="form-control"
                                    id="fecha_final"
                                    required>

                            </div>

                            <!-- SELECT PORCENTAJES -->
                            <div class="col-md-12">

                                <label class="form-label">
                                    Porcentaje comisión
                                </label>

                                <select class="form-control select-comision"
                                        id="porcentaje"
                                        required>

                                    <option value="">
                                        Selecciona porcentaje
                                    </option>

                                    <option value="10">10%</option>
                                    <option value="15">15%</option>
                                    <option value="20">20%</option>
                                    <option value="25">25%</option>
                                    <option value="30">30%</option>
                                    <option value="35">35%</option>
                                    <option value="40">40%</option>
                                    <option value="45">45%</option>
                                    <option value="50">50%</option>
                                    <option value="55">55%</option>
                                    <option value="60">60%</option>
                                    <option value="65">65%</option>
                                    <option value="70">70%</option>
                                    <option value="75">75%</option>
                                    <option value="80">80%</option>
                                    <option value="85">85%</option>
                                    <option value="90">90%</option>
                                    <option value="95">95%</option>
                                    <option value="100">100%</option>

                                </select>

                            </div>

                            <div class="col-12 mt-4">

                                <button type="submit"
                                        class="btn btn-save w-100">

                                    <i class="fa-solid fa-floppy-disk"></i>
                                    Guardar Campaña

                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </div>

    </div>

<?php } ?>

</body>

<!-- ============================================================
     SECCIÓN 9: JAVASCRIPT - EVENTOS Y FUNCIONES
     ============================================================ -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script type="text/javascript">
	jQuery(document).ready(function ()
	{
		/* --- Resize --- */
		$(window).resize(function ()
		{
			centra_contenedor();
		});
		$(window).resize();

		$('#men_pie').css("top", h);

		/* --- Menú --- */
		$('#tmenu td').click(function (evento)
		{
			var modulo = $(this).text().replace(/ /gi, "_");
			$("#opcion").load("menu.php?modulo=" + modulo);
		});

		/* --- Salir --- */
		$("#salir").click(function ()
		{
			window.location = "http://carobra.ddns.me";
		});

		/* --- Email --- */
		$("#email").click(function ()
		{
			window.open("http://webmail.carobra.com.mx/imp/login.php");
		});


	});

	
    // CREAR CAMPAÑA
    // CREAR CAMPAÑA
    document.getElementById('formCampaign')
    .addEventListener('submit', async function(e){

        e.preventDefault();

        const minima      = document.getElementById('edad_minima').value;
        const maxima      = document.getElementById('edad_maxima').value;
        const inicio      = document.getElementById('fecha_inicial').value;
        const fin         = document.getElementById('fecha_final').value;
        const porcentaje  = document.getElementById('porcentaje').value;

        Swal.fire({

            icon: 'warning',

            title: '¿Crear campaña?',

            html: `
                <div style="font-size:15px">
                    La campaña caducará automáticamente dependiendo
                    de la fecha final seleccionada.
                </div>
            `,

            showCancelButton: true,
            confirmButtonText: 'Sí, crear',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#dc3545'

        }).then(async(result)=>{

            if(result.isConfirmed){

                try{

                    const response = await fetch('./config_60_api.php',{

                        method: 'POST',

                        headers:{
                            'Content-Type':'application/json'
                        },

                        body: JSON.stringify({

                            action: 'setCampania',
                            minima,
                            maxima,
                            inicio,
                            fin,
                            porcentaje

                        })

                    });

                    const data = await response.json();

                    if(data.status === 'ok'){

                        getCampanias();

                        Swal.fire({

                            icon: 'success',
                            title: 'Campaña creada',
                            text: data.message,
                            timer: 2200,
                            showConfirmButton: false

                        });

                        // CERRAR MODAL
                        $('#modalCampaign').modal('hide');

                        // LIMPIAR FORM
                        document.getElementById('formCampaign').reset();

                        // RECARGAR
                        setTimeout(()=>{
                            location.reload();
                        },1500);

                    }else{

                        Swal.fire({

                            icon: 'error',
                            title: 'Error',
                            text: data.message

                        });

                    }

                }catch(error){

                    Swal.fire({

                        icon: 'error',
                        title: 'Error',
                        text: 'Ocurrió un error en el servidor'

                    });

                    console.error(error);

                }

            }

        });

    });


    // INACTIVAR
    function inactivarCampania(elemento, idRow){

        Swal.fire({

            icon: 'warning',

            title: '¿Seguro que quieres inactivarla?',

            html: `
                <div style="font-size:15px">
                    Esta acción será irreversible.
                    <br><br>
                    La campaña quedará permanentemente inactiva.
                </div>
            `,

            showCancelButton: true,
            confirmButtonText: 'Sí, inactivar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            borderRadius: '22px'

        }).then((result)=>{

            if(result.isConfirmed){

                const row = document.getElementById(idRow);

                row.querySelector('td:nth-child(6)').innerHTML = `
                    <span class="status-pill status-inactive">
                        Inactiva
                    </span>
                `;

                // DESHABILITAR SWITCH
                elemento.disabled = true;
                elemento.checked = true;
                getCampanias();

                Swal.fire({

                    icon: 'success',
                    title: 'Campaña inactivada',
                    text: 'La campaña fue desactivada correctamente.',
                    timer: 2200,
                    showConfirmButton: false

                });

            }else{

                elemento.checked = false;

            }

        });

    }

    // CARGAR TABLA
    async function getCampanias(){

        try{

            const response = await fetch('./config_60_api.php',{

                method: 'POST',

                headers:{
                    'Content-Type':'application/json'
                },

                body: JSON.stringify({
                    action:'getDataCapania'
                })

            });

            const data = await response.json();

            if(data.status === 'ok'){

                let html = '';

                if(data.data.length === 0){

                    html += `
                        <tr>

                            <td colspan="7">

                                <div class="text-center py-5">

                                    <h5 class="mb-1">
                                        No hay campañas
                                    </h5>

                                    <small class="text-muted">
                                        No existen campañas activas este mes
                                    </small>

                                </div>

                            </td>

                        </tr>
                    `;

                }

                data.data.forEach(item => {

                    const statusClass =
                        item.activo == 1
                        ? 'status-active'
                        : 'status-inactive';

                    const statusText =
                        item.activo == 1
                        ? 'Activa'
                        : 'Inactiva';

                    const checked =
                        item.activo == 0
                        ? 'checked'
                        : '';

                    const disabled =
                        item.activo == 0
                        ? 'disabled'
                        : '';

                    html += `

                        <tr id="row_${item.id}">

                            <td>
                                ${item.edad_minima}
                            </td>

                            <td>
                                ${item.edad_maxima}
                            </td>

                            <td>
                                ${formatDate(item.fecha_inicial)}
                            </td>

                            <td>
                                ${formatDate(item.fecha_final)}
                            </td>

                            <td>
                                ${item.porcentaje_comision}%
                            </td>

                            <td>

                                <span class="status-pill ${statusClass}">
                                    ${statusText}
                                </span>

                            </td>

                            <td>

                                <label class="switch">

                                    <input type="checkbox"
                                        ${checked}
                                        ${disabled}
                                        onchange="updateCampania(this, ${item.id})">

                                    <span class="slider"></span>

                                </label>

                            </td>

                        </tr>

                    `;

                });

                document.getElementById('tbodyCampanias').innerHTML = html;

            }

        }catch(error){

            console.error(error);

            Swal.fire({
                icon:'error',
                title:'Error',
                text:'No se pudieron cargar las campañas'
            });

        }

    }


    // FORMATEAR FECHA
    function formatDate(fecha){

        const partes = fecha.split('-');

        return `${partes[2]}/${partes[1]}/${partes[0]}`;

    }


    // INACTIVAR
    async function updateCampania(elemento, id){

        Swal.fire({

            icon: 'warning',

            title: '¿Inactivar campaña?',

            html: `
                <div style="font-size:15px">
                    Esta acción será irreversible.
                </div>
            `,

            showCancelButton: true,
            confirmButtonText: 'Sí, inactivar',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            borderRadius: '20px'

        }).then(async(result)=>{

            if(result.isConfirmed){

                try{

                    const response = await fetch('./config_60_api.php',{

                        method:'POST',

                        headers:{
                            'Content-Type':'application/json'
                        },

                        body: JSON.stringify({

                            action : 'updateDataCapania',
                            id     : id,
                            activo : 0

                        })

                    });

                    const data = await response.json();

                    if(data.status === 'ok'){

                        const row =
                            document.getElementById(`row_${id}`);

                        row.querySelector('td:nth-child(6)').innerHTML = `
                            <span class="status-pill status-inactive">
                                Inactiva
                            </span>
                        `;

                        elemento.checked = true;
                        elemento.disabled = true;

                        Swal.fire({

                            icon:'success',
                            title:'Campaña inactivada',
                            timer:1800,
                            showConfirmButton:false

                        });

                    }

                }catch(error){

                    elemento.checked = false;

                    Swal.fire({
                        icon:'error',
                        title:'Error',
                        text:'No se pudo actualizar la campaña'
                    });

                }

            }else{

                elemento.checked = false;

            }

        });

    }


    // INIT
    getCampanias();

</script>

</html>