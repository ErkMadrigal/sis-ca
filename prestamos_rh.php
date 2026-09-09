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
	<script type="text/javascript" src="./js/jquery.formatCurrency-1.4.0.js"></script>


	<link href="./css/carobra.css" rel="stylesheet" type="text/css" />
	<link href="./css/seg_ventas.css" rel="stylesheet" type="text/css" />
	<style type="text/css">
		.carobra 
		{
			height: 30px !important;
			font-size: 11px !important;
		}

		.btn-carobra 
		{
			height: 25px;
			color: #FEFEFE;
			font-size: 12px;
			font-size: 1.1em;
			padding: 10px;
			background-color: #2B73B6;
			font-family: 'Goudy Bookletter 1911', serif;
		}

		.btn-carobra span 
		{
			color: #FEFEFE;
			font-size: 12px;
			font-weight: bold;
		}

		.btn-carobra:hover 
		{
			cursor: pointer;
			background-color: #0055A0;
			box-shadow: 1px 1px 10px #141F78;
		}

        .color-encabezado 
		{
			color: #1A1A54;
		}
		.fondo-encabezado 
		{
			background-color: #1D43A4;
			color: #FEFEFE;
		}
		.color-text 
		{
			color: #000000;
		}
	</style>
</head>
<?php 
    include("./funciones/clas_carobra.php"); 
?>
<body>
<?php
	$fg = $_SESSION["ultimoAcceso"];
	$fa = date("Y-n-j H:i:s");
	$periodo = (strtotime($fa) - strtotime($fg));
	if ($periodo > 600) 
	{
		session_destroy();
		mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "index.php");
	} 
	else 
	{
		$_SESSION["ultimoAcceso"] = $fa;
		$_SESSION["autentifica"] = "p";
	}

	if ($_SESSION["autentifica"] != "p") 
	{
		session_destroy();
		mensaje("Tu sesi\u00f3n ha caducado. Por favor, reg\u00edstrate nuevamente", "index.php");
	} else 
	{
		//---------------------------------------------------------------------------Recepción de parametros
		$_SESSION["autentifica"] = "";
        $nuevo  = recibe_POST('nuevo',0);
        $id_p = recibe_POST('id-p',0);
		$empleado = recibe_POST('txt-empleado','');

        $semanas = [];

        for ($i = 1; $i <= 200; $i++) 
        {
            $semanas[] = 
            [
                "id" => $i,
                "semana" => $i . " " . ($i === 1 ? "Semana" : "Semanas")
            ];
        }

        $quiencenas = [];

        for ($i = 1; $i <= 90; $i++) 
        {
            $quiencenas[] = 
            [
                "id" => $i,
                "quincena" => $i . " " . ($i === 1 ? "Quincena" : "Quincenas")
            ];
        }

        $meses = [];

        for ($i = 1; $i <= 50; $i++) 
        {
            $meses[] = 
            [
                "id" => $i,
                "mes" => $i . " " . ($i === 1 ? "Mes" : "Meses")
            ];
        }
        $periodos = [["id" => "SEMANAL","descripcion" => "Semanal"],
                     ["id" => "QUINCENAL","descripcion" => "Quincenal"],
                     ["id" => "MENSUAL","descripcion" => "Mensual"]];

        //$valor2 = addslashes(trim($_POST["txt-asesorr"]));

		//--------------------------------------------------------------------------------------------------
		$divpie = new div("pie");
		$divopcion = new div("opcion");
		$divcabecera = new div("cabecera");
		$divcontenido = new div("contenido");
		$divcontenedor = new div("contenedor");
		$imgpie = new img("imgpie", "./img/pie.png");
		$divusuario = new div("usuario", '', "absolute", "97%");
		$imgcabecera = new img("imgcabecera", "./img/cabecera.png");

		$divcabecera->crear();
		$imgcabecera->crear();
		$imgcabecera->css("width", "100%");
		titulo("PRESTAMOS Y FINANCIAMIENTO");
		$pag = "REC_HUMANOS";
		menu();
		$divcabecera->close();

		$divpie->crear();
		$imgpie->crear();
		$imgpie->css("width", "100%");
		$divusuario->crear();
		echo "<br><br>";
		$divusuario->css("top", "10px");
		$divusuario->css("text-align", "left");
?>
		<div id="vinculos" style="margin-top: 10px;">
			<a href="https://carobra.com.mx/" target="_blank" class='a-vinculos'>CAROBRA PAGINA OFICIAL</a>
			<a href="https://nube.carobra.com.mx/" target="_blank" class='a-vinculos'>NUBE CAROBRA</a>
			<a href="http://www.creativesoft.mx/universidadcarobra" target="_blank" class='a-vinculos'>UNIVERSIDAD CAROBRA</a>
			<a href="./img/CAPTURA_GENERICA _CAROBRA.xlsx" target="_blank" class='a-vinculos'>HOJA DE CAPTURA-AFORE</a>
			<a href="./img/CALCULADORA_CAROBRA.xlsx" target="_blank" class='a-vinculos'>CALCULADORA CAROBRA</a>
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
		$divopcion->css("background", "#FFFFFF");
		$divopcion->close();
		$divcontenido->crear();
        $divcontenido->css("overflow-y","auto");
		//----------------------------------------------------------------------------Declaración de objetos
		$conecta = new conector();

		$query = "Select clave from empl_usuarios where nombre='" . utf8_decode($_SESSION["nombre"]) . "'";
		$bd = $conecta->consulta($query);
		$tb = mysqli_fetch_array($bd);
		$clav = $tb['clave'];
		$ne = 1011;
?>
        <div class="container">
                <form id="frm-filtro" action="prestamos_rh.php" method="POST" autocomplete="off">
                    <input type="hidden" id="nuevo" name="nuevo" value=0 />
                    <input type="hidden" id="id-p" name="id-p" value=0 />
                    <div class="form-row ml-0 align-items-center">
                        <div class="col-12 col-md-6 pl-0 text-left">
                            <label for='txt-empleado'>Empleados</label>
                            <input type="text" class="form-control form-control-sm" id="txt-empleado" name="txt-empleado" placeholder="Empleado" data-n='<?php echo $nivel; ?>' data-ne='<?php echo $ne; ?>' value='<?php echo $valor1; ?>' />
                        </div>
                        <div class="col-12 col-md-2 text-left">
                            <a class="btn btn-carobra btn-sm active pt-1 mt-4" id="btn-buscar" style='height: 25px;'>
                                <span><img src="./img/lupa.png" width="15" height="15"></span> 
                            </a>
                            <a>
                                <span><img src="./img/excel.png" class="btn-filtro mt-4 ml-3" id="btn-excel" width="28px"/></span>
                            </a>
                        </div>
                        <div class="col-12 col-md-4 d-flex justify-content-end">
                            <a class="btn btn-carobra btn-sm p-1 mt-4" id="btn-nuevo">
                                <span>Nuevo préstamo</span>
                            </a>
                        </div>
                    </div>
                </form>
                <div class='row pt-0 pr-3'>
                    <div class="col-12 p-0">
                        <hr style="background: #CCCCCC; box-shadow: 2px 2px 5px #6E6E6E;" />
                    </div>
                </div>
<?php 
                if($nuevo == 1)
                {
                    $empleado = "";
                    $monto = 0;
                    $semana = 0;
                    $quincena = 0;
                    $mes = 0;
                    $fec_prestamo = "";
                    $monto_semana = 0;
                    $motivo = "";
                    $centro = "";
                    $periodo = "";
?>
                    <form id="frm-nuevo" action="prestamos_rh.php" method="POST">
                        <input type="hidden" id="nuevo" name="nuevo" value=2 />
                        <input type="hidden" id="activo" name="activo" value=0 />
                        <div class="row">
                            <div class="col-1"></div>
                            <div class="col-8" style="text-align: left;">
                                <label>Empleado</label>
                                <input type="text" id="txt-empleado-nombre" class="form-control form-control-sm carobra" name="txt-empleado-nombre" value="<?=$empleado; ?>" data-n='<?php echo $nivel; ?>' data-ne='<?php echo $ne; ?>' required/>
                            </div>
                            <div class="col-2 mt-0" style="text-align: left;">
                                <label for="txt-fec-prestamo">Fecha</label>
                                <input type="date" id="txt-fec-prestamo" class="form-control form-control-sm carobra" name="txt-fec-prestamo" value="<?=$fec_prestamo; ?>" />
                            </div>
                            <div class="col-1"></div>
                            <div class="col-1"></div>
                            <div class="col-4 mt-2 pr-0" style="text-align: left;">
                                <label for="sel-motivos">Motivos</label>
                                <select name="sel-motivos" id="sel-motivos" class="form-control form-control-sm carobra" required>
                                    <option value=""></option>
<?php
                                    $bd_mot = $conecta->consulta("Select motivo FROM motivos_prestamos");
                                    while($tb_mot=mysqli_fetch_array($bd_mot))
                                    {
                                        $motivo_bd = $tb_mot["motivo"];
?>
                                        <option value="<?= $motivo_bd; ?>" <?= $motivo_bd == $motivo ? 'selected' : ''; ?>>
                                            <?= $motivo_bd; ?>
                                        </option>
<?php
                                    }
?>
                                </select>
                            </div>
                            <div class="col-1 mt-2 pl-0 pr-0 mr-0">
                                <a class="btn btn-carobra btn-sm active pt-2 mt-4" id="btn-agregar-mot" style='height: 30px;'>
                                    <span><img src="./img/mas.png" width="15" height="15"></span> 
                                </a>
                            </div>
                            <div class="col-5 mt-2" style="text-align: left;">
                                <label for="sel-centro">Centro de costos</label>
                                <select class="form-control form-control-sm carobra" id="sel-centro" name="sel-centro" style="font-size: 0.9em;">
                                    <option value="" selected></option>
<?php
									$bd_mod = $conecta->consulta("SELECT id_cc, CONCAT (nombre_cc, ' (', id_cc, ')') AS centro FROM centros_costo_area");
                                    while($tb_mod=mysqli_fetch_array($bd_mod))
									{
										$id_cc = $tb_mod["id_cc"];
										$centro = $tb_mod["centro"];
?>
										<option value="<?= $id_cc; ?>">
                                            <?= $centro; ?>
                                        </option>
<?php
									}
?>
                                </select>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-1"></div>
                            <div class="col-10 mt-2" style="text-align: left;">
                                <label for="txt-estructura">Estructura</label>
                                <input type="text" id="txt-estructura" class="form-control form-control-sm carobra" name="txt-estructura" value="<?=$estructuraFinal; ?>" disabled placeholder="Estructura" style="font-size: 0.85em !important;"/>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-1"></div>
                            <div class="col-2 mt-2" style="text-align: left;">
                                <label for="txt-monto">Monto</label>
                                <input type="text" id="txt-monto" class="form-control form-control-sm carobra" name="txt-monto" required/>
                            </div>
                            <div class="col-2 mt-2 pr-4" style="text-align: left;">
                                 <label for="sel-periodo">Periodo</label>
                                 <select name="sel-periodo" id="sel-periodo" class="form-control form-control-sm carobra" required>
                                    <option value=""></option>
<?
                                    foreach ($periodos as $pe) 
                                    {
?>
                                        <option value="<?= $pe['id']; ?>" <?= ($pe['id'] == $periodo) ? 'selected' : ''; ?>>
                                            <?= $pe['descripcion']; ?>
                                        </option>
<?
                                    }
?>
                                 </select>
                            </div>
                            <div class="col-2 mt-2" style="text-align: left;">
                                <div id="div-semana" style="display: none;">
                                    <label for="sel-semana">Semanas</label>
                                    <select name="sel-semana" id="sel-semana" class="form-control form-control-sm carobra" required>
									    <option value=""></option>
<?
                                        foreach ($semanas as $sem) 
                                        {
?>
                                            <option value="<?= $sem['id']; ?>" <?= ($sem['id'] == $semana) ? 'selected' : ''; ?>>
                                                <?= $sem['semana']; ?>
                                            </option>
<?
                                        }
?>
								    </select>
                                </div>

                                <div id="div-quincenal" style="display: none;">
                                    <label for="sel-quincenal">Quincenas</label>
                                    <select name="sel-quincenal" id="sel-quincenal" class="form-control form-control-sm carobra" required>
									    <option value=""></option>
<?
                                        foreach ($quiencenas as $quin) 
                                        {
?>
                                            <option value="<?= $quin['id']; ?>" <?= ($quin['id'] == $quincena) ? 'selected' : ''; ?>>
                                                <?= $quin['quincena']; ?>
                                            </option>
<?
                                        }
?>
								    </select>
                                </div>

                                <div id="div-mes" style="display: none;">
                                    <label for="sel-mes">Meses</label>
                                    <select name="sel-mes" id="sel-mes" class="form-control form-control-sm carobra" required>
                                        <option value=""></option>
<?
                                        foreach ($meses as $m) 
                                        {
?>
                                            <option value="<?= $m['id']; ?>" <?= ($m['id'] == $mes) ? 'selected' : ''; ?>>
                                                <?= $m['mes']; ?>
                                            </option>
<?
                                        }
?>
								    </select>
                                </div>
                            </div>
                            <div class="col-2 mt-2" style="text-align: left;">
                                <div id="div-monto-pago" style="display: none;">
                                    <label for="txt-monto-semana" id="texto-monto"></label>
                                    <input type="text" id="txt-monto-semana" class="form-control form-control-sm carobra" name="txt-monto-semana" disabled required/>
                                </div>
                            </div>
                            <div class="col-2 mt-2" style="text-align: left;">
                                <div id="div-fec-desc" style="display: none;">
                                    <label for="txt-fec-desc" id="texto-monto">Desc. a partir de</label>
                                    <input type="date" id="txt-fec-desc" class="form-control form-control-sm carobra" name="txt-fec-desc"/>
                                </div>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-1"></div>
                            <div class="col-10 mt-2" style="text-align: left;">
                                <label for="txt-observa">Observaciones</label>
                                <input type="text" id="txt-observa" class="form-control form-control-sm carobra" name="txt-observa" placeholder="Observaciones" style="font-size: 0.85em !important;"/>
                            </div>
                            <div class="col-1"></div>
                            <div class="col-12 d-flex justify-content-end">
                                <div class="col-2">
                                    <a  class="btn btn-carobra btn-block p-1 mt-4 ml-3" id="btn-guarda-nuevo">
                                        <span>Guardar</span>
                                    </a>
                                </div>
                                <div class="col-1"></div>
                            </div>
                        </div>
                    </form>
                    <script>
                        document.addEventListener('DOMContentLoaded', function () 
                        {
                            const montoCredito = document.getElementById('txt-monto');
                            const valorPorDefecto = "$0.00";

                            montoCredito.value = valorPorDefecto;

                            function formatearMoneda(valor) 
                            {
                                let num = parseFloat(valor.replace(/[^0-9.-]+/g, ''));
                                if (isNaN(num)) num = 0;
                                    return `$${num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            }

                            montoCredito.addEventListener('focus', function () 
                            {
                                montoCredito.value = "";
                            });

                            montoCredito.addEventListener('blur', function () 
                            {
                                if (montoCredito.value.trim() === "") 
                                {
                                    montoCredito.value = valorPorDefecto;
                                } 
                                else 
                                {
                                    montoCredito.value = formatearMoneda(montoCredito.value);
                                }
                            });
                        });

                        document.addEventListener('DOMContentLoaded', function () 
                        {
                            const montoCredito = document.getElementById('txt-monto-semana');
                            const valorPorDefecto = "$0.00";

                            montoCredito.value = valorPorDefecto;

                            function formatearMoneda(valor) 
                            {
                                let num = parseFloat(valor.replace(/[^0-9.-]+/g, ''));
                                if (isNaN(num)) num = 0;
                                    return `$${num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                            }

                            montoCredito.addEventListener('focus', function () 
                            {
                                montoCredito.value = "";
                            });

                            montoCredito.addEventListener('blur', function () 
                            {
                                if (montoCredito.value.trim() === "") 
                                {
                                    montoCredito.value = valorPorDefecto;
                                } 
                                else 
                                {
                                    montoCredito.value = formatearMoneda(montoCredito.value);
                                }
                            });
                        });

                        autocompletar('txt-empleado-nombre', 4, 'empleados_activos', $("txt-empleado-nombre").data('n') + "|" + $("txt-empleado-nombre").data('ne'));

                        let valueEmpleado = document.getElementById('txt-empleado-nombre');
					    let debounceTimeout;

                        valueEmpleado.addEventListener('change', function(e)
						{
                            clearTimeout(debounceTimeout);
							debounceTimeout = setTimeout(() => 
							{
								if (valueEmpleado.value.trim() === "") 
								{
									document.getElementById('txt-estructura').value = "";
									document.getElementById('txt-estructura').placeholder = "Estructura";
									return;
								}
												
								const data_a = 
                                {
                                    op: 110,
                                    'empleado': valueEmpleado.value
                                };

								console.log(data_a);
                                $.ajax({
                                    type: "POST",
                                    url: "./ajax/facilitador.php",
                                    data: data_a,
                                        success: function(response) 
                                        {
                                            console.log(':3 ',response);
                                            let estructura = response;
                                            if (estructura.length > 0) 
                                            {
                                                const item = estructura[0];
                                                let partes = [];

                                                if (item.gerencia) partes.push(item.gerencia);
                                                if (item.brocker) partes.push(item.brocker);
                                                if (item.sc) partes.push(item.sc);

                                                document.getElementById('txt-estructura').value = partes.join(" / ");
                                            } 
                                            else 
                                            {
                                                document.getElementById('txt-estructura').value = "";
                                            }
                                        }
                                });
							}, 500);

                        });

                        $(document).ready(function () {
                            function calcularMontoSemanal() 
                            {
                                const monto = parseFloat($("#txt-monto").val().replace(/[$,]/g, "")) || 0;
                                const periodo = $("#sel-periodo").val();

                                let divisor = 0;

                                if (periodo === "SEMANAL") 
                                {
                                    divisor = parseInt($("#sel-semana").val()) || 0;
                                } 
                                else if (periodo === "QUINCENAL") 
                                {
                                    divisor = parseInt($("#sel-quincenal").val()) || 0;
                                } else if (periodo === "MENSUAL") 
                                {
                                    divisor = parseInt($("#sel-mes").val()) || 0;
                                }

                                if (monto > 0 && divisor > 0) 
                                {
                                    const montoSemanal = monto / divisor;
                                    $("#txt-monto-semana").val(`$${montoSemanal.toFixed(2)}`);
                                } 
                                else 
                                {
                                    $("#txt-monto-semana").val("");
                                }
                            }

                            $("#sel-semana, #sel-quincenal, #sel-mes, #sel-periodo, #txt-monto").on("change input", function () 
                            {
                                calcularMontoSemanal();
                            });

                            function actualizarSelects() 
                            {
                                let periodo = $('#sel-periodo').val();
                                
                                $('#div-semana, #div-quincenal, #div-mes').hide();
                                $('#div-monto-pago').hide();
                                $('#div-fec-desc').hide();
                                $('#txt-monto-semana').text("");
                                
                                if (periodo === "SEMANAL") 
                                {
                                    $('#div-semana').show();
                                    $('#texto-monto').text("Monto semanal");
                                    $('#div-monto-pago').show();
                                    $('#div-fec-desc').show();
                                } 
                                else if (periodo === "QUINCENAL") 
                                {
                                    $('#div-quincenal').show();
                                    $('#texto-monto').text("Monto quincenal");
                                    $('#div-monto-pago').show();
                                    $('#div-fec-desc').show();
                                } 
                                else if (periodo === "MENSUAL") 
                                {
                                    $('#div-mes').show();
                                    $('#texto-monto').text("Monto mensual");
                                    $('#div-monto-pago').show();
                                    $('#div-fec-desc').show();
                                }
                            }
                            $('#sel-periodo').change(actualizarSelects);
                            actualizarSelects();
                        });

                        let bt_guardar_nuevo = document.getElementById("btn-guarda-nuevo");
						let fmr_nuevo = document.getElementById("frm-nuevo");
						let frm_buscar2 = document.getElementById("frm-filtro");

                        bt_guardar_nuevo.addEventListener('click', function(e) 
						{
							e.preventDefault();
                            let usuario_cap = "<?php echo $clav; ?>";                           
                            let no_pagos = 0;
                            let periodo = $('#sel-periodo').val();

                            if (periodo === "SEMANAL") 
                                no_pagos = $('#sel-semana').val();
                            else
                            {
                                if (periodo === "QUINCENAL")
                                    no_pagos = $('#sel-quincenal').val();
                                else
                                {
                                    if (periodo === "MENSUAL")
                                         no_pagos = $('#sel-mes').val();
                                }
                            }
							const data = 
                            {
                                'op': 109,
                                'empleado': $('#txt-empleado-nombre').val(),
                                'fec_prestamo': $('#txt-fec-prestamo').val(),
                                'monto': $('#txt-monto').val(),
                                'no_pagos': no_pagos,
                                'monto_pago': $('#txt-monto-semana').val(),
                                'motivo': $('#sel-motivos').val(),
                                'centro': $('#sel-centro').val(),
                                'responsable': usuario_cap,
                                'periodo': $('#sel-periodo').val(),
                                'fec_desc': $('#txt-fec-desc').val(),
                                'observa': $("#txt-observa").val()
                            };

                            $.ajax({
                                type: "POST",
                                url: "./ajax/facilitador.php",
                                data: data,
                                    success: function(response) 
                                    {                                        
                                        let [mensaje, empleado, tipoError, idPrestamo] = response.split("|");
                                        if (tipoError == "EXITO") 
                                        {
                                            alert(mensaje);
                                            if(empledo!="")
                                            {
                                                document.getElementById("txt-empleado").value = empleado;
                                                document.getElementById("id-p").value = idPrestamo;
                                                document.getElementById("nuevo").value = 2;
                                                frm_buscar2.submit();
                                            }
                                        }
                                        else 
                                        {
                                            document.getElementById("txt-prestajuste").value = idPrestamo;
                                            document.getElementById("txt-prestanombre").value = empleado;
                                            $('#msg-ajustes').text("El préstamo fue creado correctamente pero ocurrió un error al crear el ajuste. Por favor vuelve a confirmar el proceso.").addClass('text-danger');
                                            $('#modal-prestamo').modal("show");
                                        }
                                    }
                            });
						});
                    </script>
<?php
                } 
                else 
                {
                    if($nuevo == 2) 
					{
                        $id = null;
                        $empl = $empleado;

                        if (preg_match('/^(.*?)\s*\((\d+)\)$/', $empleado, $matches)) 
                        {
                            $empleado = $matches[1];
                            $id = (int)$matches[2];
                        }
                        if(is_null($id))
                            $id=$id_p;

                        $query = "Select p.id_prestamos, eu.nombre, p.clave_empleado, p.fec_prestamo, p.monto, p.no_pagos, p.monto_pago, p.motivo,
                                         p.cen_cost, p.periodo,observa,fec_desc,detener_cobro  
                                    FROM carobra_2014.prestamos p
                                    INNER JOIN carobra_2014.empl_usuarios eu ON (p.clave_empleado = eu.clave)
                                    WHERE eu.nombre = '$empleado' AND p.id_prestamos = $id";
                        $dbp = $conecta->consulta($query);
                        $resultado = [];
                        $pagos = [];
                        $squery = "";

                        while ($row = $dbp->fetch_assoc()) 
                        {
                            $resultado[] = $row;
                        }

                        if (!empty($resultado)) 
                        {
                            //print_r($resultado);
                            foreach ($resultado as $fila) 
						    {
                                $empleado = $fila['nombre'];
                                $monto = $fila['monto'];
                                $no_pagos = $fila['no_pagos'];
                                $fec_prestamo = $fila['fec_prestamo'];
                                $clave = $fila['clave_empleado'];
                                $id_prestamo = $fila['id_prestamos'];
                                $monto_pago = $fila['monto_pago'];
                                $motivo = $fila['motivo'];
                                $cc = $fila['cen_cost'];
                                $periodo = $fila['periodo'];
                                $observa= $fila['observa'];
                                $fec_desc= $fila['fec_desc'];
                                $det_cobro=$fila['detener_cobro'];
                                $texto_monto = "";

                                $squery = "Select no_pago, fec_pago, monto 
                                           FROM carobra_2014.prestamos_pagos 
                                           WHERE id_prestamos =  $id_prestamo
                                           order by no_pago";
                                $dbpp = $conecta->consulta($squery);
                                while ($row = $dbpp->fetch_assoc()) 
                                {
                                    $pagos[] = $row;
                                }

                                if (!empty($fec_prestamo)) 
                                    $fecha_convertida = DateTime::createFromFormat('d/m/Y', $fec_prestamo)->format('Y-m-d'); 
                                else 
                                    $fecha_convertida = '';

                                if (!empty($fec_desc)) 
                                    $fec_desc = DateTime::createFromFormat('d/m/Y', $fec_desc)->format('Y-m-d'); 
                                else 
                                    $fec_desc = '';

                                if ($clave == "") 
                                {
                                    $estructuraFinal = "";
                                } 
                                else 
                                {
                                                    
                                    $queryc = "Select ee.sc, ee.brocker, ee.id_gerencia, ee.gerencia 
                                                FROM carobra_2014.estruc_empleados ee
                                                INNER JOIN carobra_2014.empl_usuarios eu ON (ee.clave = eu.clave)
                                                WHERE eu.nombre =  '$empleado'";
                                    $bdc = $conecta->consulta($queryc);
                                    
                                    if ($bdc->num_rows > 0) 
                                    {
                                        while ($row = mysqli_fetch_assoc($bdc)) 
                                        {
                                            $response[] = $row;
                                        }
                                    } 
                                    else 
                                    {
                                        $response = [];
                                    }
                                    $estructuraArray = $response;
                                    $gerencia = "";
                                    $brocker = "";
                                    $sc = "";
                                                
                                    if (!empty($estructuraArray) && isset($estructuraArray[0])) 
                                    {
                                        $item = $estructuraArray[0];
                                                
                                        $gerencia = !empty($item['gerencia']) ? $item['gerencia'] : "";
                                        $brocker = !empty($item['brocker']) ? $item['brocker'] : "";
                                        $sc = !empty($item['sc']) ? $item['sc'] : "";
                                    }
                                                
                                    $partes = [];
                                    if (!empty($gerencia)) $partes[] = $gerencia;
                                    if (!empty($brocker)) $partes[] = $brocker;
                                    if (!empty($sc)) $partes[] = $sc;
                                                
                                    $estructuraFinal = implode(" / ", $partes);
                                }    
                                //print_r($pagos);

                            }
                        }
?>
                        <form id="frm-content" action="prestamos_rh.php" method="POST">
                            <input type="hidden" id="nuevo" name="nuevo" value=2 />
                            <input type="hidden" id="activo" name="activo" value=0 />
                            <input type="hidden" name="txt-id-prestamo" id="txt-id-prestamo" value="<?=$id_prestamo; ?>" />
                            <input type="hidden" name="txt-motivo-prestamo" id="txt-motivo-prestamo" value="<?=$motivo; ?>" />
                            <input type="hidden" name="txt-fecha-prestamo" id="txt-fecha-prestamo" value="<?=$fec_prestamo; ?>" />
                            <input type="hidden" name="txt-clave-prestamo" id="txt-clave-prestamo" value="<?=$clave; ?>" />

                            <div class="row">
                                <div class="col-9">
                                     <div class="row pl-0 pr-0">
                                        <div class="col-9 pl-0" style="text-align: left;">
                                            <label>Empleado</label>
                                            <input type="text" id="txt-empleado-nombre" class="form-control form-control-sm carobra" name="txt-empleado-nombre" value="<?=$empleado; ?>" data-n='<?php echo $nivel; ?>' data-ne='<?php echo $ne; ?>' required />
                                        </div>
                                        <div class="col-3 mt-0 pr-1" style="text-align: left;">
                                            <label for="txt-fec-prestamo">Fecha</label>
                                            <input type="date" id="txt-fec-prestamo" class="form-control form-control-sm carobra" name="txt-fec-prestamo" value="<?=$fecha_convertida; ?>" />
                                        </div>
                                        <div class="col-6 mt-2 pl-0" style="text-align: left;">
                                            <label for="sel-motivos">Motivos</label>
                                            <select name="sel-motivos" id="sel-motivos" class="form-control form-control-sm carobra" required>
                                                <option value=""></option>
<?php
                                                    $bd_mot = $conecta->consulta("Select motivo FROM motivos_prestamos");
                                                    while($tb_mot=mysqli_fetch_array($bd_mot))
                                                    {
                                                        $motivo_bd = $tb_mot["motivo"];
?>
                                                        <option value="<?= $motivo_bd; ?>" <?= $motivo_bd == $motivo ? 'selected' : ''; ?>>
                                                            <?= $motivo_bd; ?>
                                                        </option>
<?php
                                                    }
?>
                                            </select>
                                        </div>
                                        <div class="col-6 mt-2 pr-1" style="text-align: left;">
                                            <label for="sel-centro">Centro de costos</label>
                                            <select class="form-control form-control-sm carobra" id="sel-centro" name="sel-centro" style="font-size: 0.9em;">
                                                <option value="" selected></option>
<?php
                                                $bd_mod = $conecta->consulta("SELECT id_cc, CONCAT (nombre_cc, ' (', id_cc, ')') AS centro FROM centros_costo_area");
                                                while($tb_mod=mysqli_fetch_array($bd_mod))
                                                {
                                                    $id_cc = $tb_mod["id_cc"];
                                                    $centro = $tb_mod["centro"];
?>
                                                    <option value="<?= $id_cc; ?>" <?= $id_cc == $cc ? 'selected' : ''; ?>>
                                                        <?= $centro; ?>
                                                    </option>
<?php
                                                }
?>
                                            </select>
                                        </div>
                                        <div class="col-12 mt-2 pl-0 pr-1" style="text-align: left;">
                                            <label for="txt-estructura">Estructura</label>
                                            <input type="text" id="txt-estructura" class="form-control form-control-sm carobra" name="txt-estructura" disabled value="<?=$estructuraFinal; ?>" placeholder="Estructura" style="font-size: 0.85em !important;" />
                                        </div>
                                        <div class="col-4">
                                            <div class="row">
                                                <div class="col-5 mt-2 pl-0 pr-0" style="text-align: left;">
                                                    <label for="txt-monto">Monto</label>
                                                    <input type="text" id="txt-monto" class="form-control form-control-sm carobra" value="<?=$monto; ?>" name="txt-monto" required/>
                                                </div>
                                                <div class="col-7 mt-2 pr-0" style="text-align: left;">
                                                    <label for="sel-periodo">Periodo</label>
                                                    <select name="sel-periodo" id="sel-periodo" class="form-control form-control-sm carobra" required>
                                                        <option value=""></option>
<?
                                                        foreach ($periodos as $pe) 
                                                        {
?>
                                                            <option value="<?= $pe['id']; ?>" <?= ($pe['id'] == $periodo) ? 'selected' : ''; ?>>
                                                                <?= $pe['descripcion']; ?>
                                                            </option>
<?
                                                        }
?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-5">
                                            <div class="row">                                      
                                                <div class="col-7 mt-2" style="text-align: left;">
<?
                                                    if ($periodo == "SEMANAL") 
                                                    {
                                                        $texto_monto = "Monto semanal";
?>
                                                        <div id="div-semana">
                                                            <label for="sel-semana">Semanas</label>
                                                            <select name="sel-semana" id="sel-semana" class="form-control form-control-sm carobra" required>
                                                                <option value=""></option>
<?
                                                                foreach ($semanas as $sem) 
                                                                {
?>
                                                                    <option value="<?= $sem['id']; ?>" <?= ($sem['id'] == $no_pagos) ? 'selected' : ''; ?>>
                                                                        <?= $sem['semana']; ?>
                                                                    </option>
<?
                                                                }
?>
                                                            </select>
                                                        </div>
<?
                                                    }
                                                    elseif ($periodo == "QUINCENAL") 
                                                    {
                                                        $texto_monto = "Monto quincenal";
?>
                                                        <div id="div-quincenal">
                                                            <label for="sel-quincenal">Quincenas</label>
                                                            <select name="sel-quincenal" id="sel-quincenal" class="form-control form-control-sm carobra" required>
                                                                <option value=""></option>
<?
                                                                foreach ($quiencenas as $quin) 
                                                                {
?>
                                                                    <option value="<?= $quin['id']; ?>" <?= ($quin['id'] == $no_pagos) ? 'selected' : ''; ?>>
                                                                        <?= $quin['quincena']; ?>
                                                                    </option>
<?
                                                                }
?>
                                                            </select>
                                                        </div>
<?
                                                    }
                                                    elseif ($periodo == "MENSUAL") 
                                                    {
                                                        $texto_monto = "Monto mensual";
?>
                                                        <div id="div-mes">
                                                            <label for="sel-mes">Meses</label>
                                                            <select name="sel-mes" id="sel-mes" class="form-control form-control-sm carobra" required>
                                                                <option value=""></option>
<?
                                                                foreach ($meses as $m) 
                                                                {
?>
                                                                    <option value="<?= $m['id']; ?>" <?= ($m['id'] == $no_pagos) ? 'selected' : ''; ?>>
                                                                        <?= $m['mes']; ?>
                                                                    </option>
<?
                                                                }
?>
                                                            </select>
                                                        </div>
<?
                                                    }
?>
                                                </div>
                                                <div class="col-5 mt-2 pl-0 pr-0" style="text-align: left;">
                                                    <div id="div-monto-pago">
                                                        <label for="txt-monto-semana" id="texto-monto"><? echo $texto_monto; ?></label>
                                                        <input type="text" id="txt-monto-semana" class="form-control form-control-sm carobra" name="txt-monto-semana" disabled  value="<? echo $monto_pago; ?>"/>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-3 mt-2 pr-1" style="text-align: left;">
                                            <div id="div-fec-desc">
                                                <label for="txt-fec-desc" id="texto-monto">Desc. a partir de</label>
                                                <input type="date" id="txt-fec-desc" class="form-control form-control-sm carobra" name="txt-fec-desc" disabled value="<? echo $fec_desc; ?>"/>
                                            </div>
                                        </div>
                                        <div class="col-12 mt-2 pl-0 pr-0" style="text-align: left;">
                                            <label for="txt-observa">Observaciones</label>
                                            <input type="text" id="txt-observa" class="form-control form-control-sm carobra" name="txt-observa" value="<?=$observa; ?>" placeholder="Observaciones" style="font-size: 0.85em !important;"/>
                                        </div>

                                        <div class="col-12 d-flex justify-content-end">
<?php
                                            if($det_cobro==1)
                                            {
?>
                                                <div class="col-6 mt-4 text-center">
                                                        <span><b>Cobro de préstamo detenido</b></span>
                                                </div>
<?php 
                                            }
                                            if ($nivel == 12) 
                                            {
?>
                                                <div class="col-3 m-0 pr-2">
                                                    <a  class="btn btn-carobra btn-block p-1 mt-4 ml-0 mr-0" id="btn-eliminar" style="background-color: red;">
                                                        <span>Eliminar</span>
                                                    </a>
                                                </div>
                                                <div class="col-3 m-0 pr-2">
                                                    <a  class="btn btn-carobra btn-block p-1 mt-4 ml-2 mr-0" id="btn-actualizar">
                                                        <span>Actualizar prestamo</span>
                                                    </a>
                                                </div>
<?php
                                            }
?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-3">
                                    <div class="row">
                                        <div class="col-12 pl-1 pr-1">
                                            <div style="max-height: 350px; overflow-y: auto;">
                                                <table class="table table-striped table-sm">
                                                    <thead>
                                                        <tr>
                                                            <th>No. pago</th>
                                                            <th>Fecha</th>
                                                            <th>Monto</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
<?
                                                    if (!empty($pagos)) 
                                                    {  
                                                        foreach ($pagos as $pago) 
                                                        {
?>
                                                            <tr>
                                                                <td><? echo $pago['no_pago']; ?></td>
                                                                <td><? echo $pago['fec_pago']; ?></td>
                                                                <td><?php echo '$' . number_format($pago['monto'], 2); ?></td>
                                                            </tr>
<?
                                                        }
                                                    }
?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                        <div class="col-3"></div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <script>
                            autocompletar('txt-empleado-nombre', 4, 'empleados_activos', $("txt-empleado-nombre").data('n') + "|" + $("txt-empleado-nombre").data('ne'));

                            let valueEmpleado2 = document.getElementById('txt-empleado-nombre');
					        let debounceTimeout2;

                            valueEmpleado2.addEventListener('change', function(e)
                            {
                                clearTimeout(debounceTimeout2);
                                debounceTimeout2 = setTimeout(() => 
                                {
                                    if (valueEmpleado2.value.trim() === "") 
                                    {
                                        document.getElementById('txt-estructura').value = "";
                                        document.getElementById('txt-estructura').placeholder = "Estructura";
                                        return;
                                    }
                                                    
                                    const data_a = 
                                    {
                                        op: 110,
                                        'empleado': valueEmpleado2.value
                                    };

                                    console.log(data_a);
                                    $.ajax({
                                        type: "POST",
                                        url: "./ajax/facilitador.php",
                                        data: data_a,
                                            success: function(response) 
                                            {
                                                console.log(':3 ',response);
                                                let estructura = response;
                                                if (estructura.length > 0) 
                                                {
                                                    const item = estructura[0];
                                                    let partes = [];

                                                    if (item.gerencia) partes.push(item.gerencia);
                                                    if (item.brocker) partes.push(item.brocker);
                                                    if (item.sc) partes.push(item.sc);

                                                    document.getElementById('txt-estructura').value = partes.join(" / ");
                                                } 
                                                else 
                                                {
                                                    document.getElementById('txt-estructura').value = "";
                                                }
                                            }
                                    });
                                }, 500);

                            });

                            document.addEventListener('DOMContentLoaded', function () 
							{
								const montoCredito = document.getElementById('txt-monto');
								const valorInicial = montoCredito.value.trim() !== "" ? montoCredito.value : "$0.00";

								montoCredito.value = formatearMoneda(valorInicial);

								function formatearMoneda(valor) 
								{
									let num = parseFloat(valor.replace(/[^0-9.-]+/g, ''));
									if (isNaN(num)) num = 0;
										return `$${num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
								}

								montoCredito.addEventListener('focus', function () 
								{
									montoCredito.value = "";
								});

								montoCredito.addEventListener('blur', function () 
								{
									if (montoCredito.value.trim() === "") 
									{
										montoCredito.value = formatearMoneda(valorInicial);
									} 
									else 
									{
										montoCredito.value = formatearMoneda(montoCredito.value);
									}
								});
												
						    });

                            document.addEventListener('DOMContentLoaded', function () 
							{
								const montoCredito = document.getElementById('txt-monto-semana');
								const valorInicial = montoCredito.value.trim() !== "" ? montoCredito.value : "$0.00";

								montoCredito.value = formatearMoneda(valorInicial);

								function formatearMoneda(valor) 
								{
									let num = parseFloat(valor.replace(/[^0-9.-]+/g, ''));
									if (isNaN(num)) num = 0;
										return `$${num.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
								}

								montoCredito.addEventListener('focus', function () 
								{
									montoCredito.value = "";
								});

								montoCredito.addEventListener('blur', function () 
								{
									if (montoCredito.value.trim() === "") 
									{
										montoCredito.value = formatearMoneda(valorInicial);
									} 
									else 
									{
										montoCredito.value = formatearMoneda(montoCredito.value);
									}
								});
												
						    });

                            $(document).ready(function () {
                                
                                function calcularMontoSemanal() 
                                {
                                    const monto = parseFloat($("#txt-monto").val().replace(/[$,]/g, "")) || 0;
                                    const semanas = parseInt($("#sel-semana").val()) || 0;

                                    if (monto > 0 && semanas > 0) 
                                    {
                                        const montoSemanal = monto / semanas;
                                        $("#txt-monto-semana").val(`$${montoSemanal.toFixed(2)}`);
                                    } 
                                    else 
                                    {
                                        $("#txt-monto-semana").val("");
                                    }
                                }

                                $("#sel-semana").on("change", function () 
                                {
                                    calcularMontoSemanal();
                                });

                                $("#txt-monto").on("input", function () 
                                {
                                    calcularMontoSemanal();
                                });
                            });

                            let btn_guardar = document.getElementById("btn-guarda");
							let fmr_update = document.getElementById("frm-content");
							let frm_buscar3 = document.getElementById("frm-filtro");

                            /*btn_guardar.addEventListener('click', function(e) 
                            {
                                e.preventDefault();
                                let empleado = $('#txt-empleado-nombre').val();
                                let fec_prestamo = $('#txt-fec-prestamo').val();
                                let monto = $('#txt-monto').val();
                                let semana = $('#sel-semana').val();
                                let monto_semana = $('#txt-monto-semana').val();
                                let id_prestamo =  $('#txt-id-prestamo').val();
                                let motivo = $('#sel-motivos').val();

                                const data = 
                                {
                                    'op': 111,
                                    'empleado': empleado,
                                    'fec_prestamo': fec_prestamo,
                                    'monto': monto,
                                    'semana': semana,
                                    'monto_semana': monto_semana,
                                    'id_prestamo': id_prestamo,
                                    'motivo': motivo
                                };

                                console.log(data);
                                $.ajax({
                                    type: "POST",
                                    url: "./ajax/facilitador.php",
                                    data: data,
                                        success: function(response) 
                                        {
                                            console.log(':3 ',response);
                                            let [mensaje, variable] = response.split("|");
                                            alert(mensaje);
                                            document.getElementById("txt-empleado").value = variable;
                                            document.getElementById("nuevo").value = 2;
                                            frm_buscar3.submit();
                                        }
                                });
                            });*/

                            $('#btn-eliminar').on('click', function() 
                            {
                                let id_prestamo = $('#txt-id-prestamo').val();
                                let motivo_prestamo = $('#txt-motivo-prestamo').val();
                                let fec_prestamo = $('#txt-fecha-prestamo').val();
                                let clave_prestamo = $('#txt-clave-prestamo').val();

                                
                                const formData = {
                                    'id_prestamo': id_prestamo,
                                    'motivo_prestamo': motivo_prestamo,
                                    'fec_prestamo': fec_prestamo,
                                    'clave_prestamo': clave_prestamo,
                                    'op': 117
                                };

                                if (confirm("¿Está seguro de que desea eliminar este préstamo?")) 
                                {
                                    console.log(formData);
                                    $.ajax({
                                        type: "POST",
                                        url: "./ajax/facilitador.php",
                                        data: formData,
                                            success: function(response) 
                                            {
                                                if (response == "El préstamo fue eliminado con exito") 
                                                {
                                                    alert(response);
                                                    window.location = 'prestamos_rh.php';
                                                    
                                                } 
                                                else 
                                                {
                                                    alert(response);
                                                }
                                               
                                            }
                                    });

                                }
                            });

                            $('#btn-actualizar').on('click', function() 
                            {
                                $('#modal-actualiza').find('b').text('');
                                $('#sel-nuevo-semana, #sel-nuevo-quincena, #sel-nuevo-mes').val('');
                                $('#txt-nuevo-monto-semana').val('');

                                $('#sel-nuevo-semana, #sel-nuevo-quincena, #sel-nuevo-mes').closest('.form-group').hide();

                                let id_prestamo = $('#txt-id-prestamo').val();
                                const formData = {
                                    'op': 118,
                                    'id_prestamo': id_prestamo
                                };
                                $.ajax({
                                    type: "POST",
                                    url: "./ajax/facilitador.php",
                                    data: formData,
                                    success: function(response) 
                                    {
                                        //document.write(response);
                                        const data = JSON.parse(response);
                                        const prestamo = data.data;
                                        const periodo = prestamo.periodo; // El período recibido (SEMANAL, QUINCENAL o MENSUAL)

                                        const formatCurrency = (number) =>
                                            new Intl.NumberFormat('es-MX', { style: 'currency', currency: 'MXN' }).format(number);

                                        $('p:contains("Monto del prestamo:") b').text(formatCurrency(prestamo.monto_total));
                                        $('p:contains("No. pagos:") b').text(prestamo.no_pagos);
                                        $('p:contains("Pagos realizados:") b').text(prestamo.pagos_realizados + '/' + prestamo.no_pagos);
                                        $('p:contains("Monto pagado:") b').text(formatCurrency(prestamo.monto_pagado));

                                        if(prestamo.detener_cobro==1)
                                            $('#chk-detener-cobro').prop('checked', true);

                                        // Mostrar el select correcto según el período del préstamo
                                        if (periodo === "SEMANAL")
                                            $('#sel-nuevo-semana').closest('.form-group').show();
                                            //$('#sel-nuevo-semana').closest('.show-class').show();
                                        else 
                                        {
                                            if (periodo === "QUINCENAL")
                                                $('#sel-nuevo-quincena').closest('.form-group').show();
                                             else 
                                                $('#sel-nuevo-mes').closest('.form-group').show();
                                        }

                                        $('#modal-actualiza').modal('show');

                                        // Agregar evento de cambio dinámico
                                        $('#sel-nuevo-semana, #sel-nuevo-quincena, #sel-nuevo-mes').off('change').on('change', function() {
                                            calcularNuevoMonto(prestamo, periodo);
                                        });
                                    }
                                });
                            });

                            // Función para calcular el monto basado en el período seleccionado
                            function calcularNuevoMonto(prestamo, periodo) {
                                const montoTotal = prestamo.monto_total;
                                const montoPagado = prestamo.monto_pagado;
                                const pagosRealizados = prestamo.pagos_realizados;

                                let divisor = 0;

                                if (periodo === "SEMANAL") {
                                    divisor = parseInt($("#sel-nuevo-semana").val()) || 0;
                                } else if (periodo === "QUINCENAL") {
                                    divisor = parseInt($("#sel-nuevo-quincena").val()) || 0;
                                } else if (periodo === "MENSUAL") {
                                    divisor = parseInt($("#sel-nuevo-mes").val()) || 0;
                                }

                                if (divisor > 0) {
                                    const montoRestante = montoTotal - montoPagado;
                                    const pagosRestantes = divisor - pagosRealizados;

                                    if (pagosRestantes > 0) {
                                        const nuevoMonto = montoRestante / pagosRestantes;
                                        $('#txt-nuevo-monto-semana').val(`$${nuevoMonto.toFixed(2)}`);
                                    } else {
                                        $('#txt-nuevo-monto-semana').val('');
                                        alert(`No puedes seleccionar menos pagos de los ya realizados.`);
                                    }
                                } else {
                                    $('#txt-nuevo-monto-semana').val('');
                                }
                            }

                        </script>
<?
                    }
                }
?>
        </div>
<?php
        $divcontenido->close();
	    $divpie->close();
?>
        <script language='javascript'>
            var hc = calcula_h(125);
            var h = $(window).outerHeight() - hc;

            $('#pie').css("top", h + 5);

            hc = calcula_h(85);
            h = $(window).outerHeight() - (hc * 2);
            $('#contenedor').css("top", hc - 15);
            $('#contenedor').css("height", h);
            hc = calcula_h(45);
            $('#menu').css("top", hc - 20);
            $('#opcion').load('menu.php?modulo=' + '<?php echo $pag ?>');
        </script>
<?php
	}
?>
    <div class="modal fade" id="modal-actualiza" data-backdrop="static" data-keyboard="false"  tabindex="-1" aria-labelledby="modal-actualiza" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header fondo-encabezado ">
					<h5 class="modal-title" id="encabezado-ajuste">Actualiza información de prestamo</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">	
                    <form id="form-actualiza" method="POST">
                        <input type="hidden" name="txt-id-prestamo-act" id="txt-id-prestamo-act" value="<?=$id_prestamo; ?>" />
                        <input type="hidden" name="txt-empleado-act" id="txt-empleado-act" value="<?=$empl; ?>" />
                        <div class="row">
                            <div class="col-6">
                                <p style="font-size: small;">Monto del prestamo: <b></b></p>
                            </div>
                            <div class="col-6">
                                <p style="font-size: small;">No. pagos: <b></b></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <p style="font-size: small;">Pagos realizados: <b></b></p>
                            </div>
                            <div class="col-6">
                                <p style="font-size: small;">Monto pagado: <b></b></p>
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-6" style="text-align: left;">
                                <!-- Selector para Semanas -->
                                <div class="form-group show-semana">
                                    <label for="sel-nuevo-semana" style="font-size: small;">No. semanas</label>
                                    <select name="sel-nuevo-semana" id="sel-nuevo-semana" class="form-control form-control-sm">
                                        <option value=""></option>
                                        <? foreach ($semanas as $sem) { ?>
                                            <option value="<?= $sem['id']; ?>"><?= $sem['semana']; ?></option>
                                        <? } ?>
                                    </select>
                                </div>

                                <!-- Selector para Quincenas -->
                                <div class="form-group show-quincena">
                                    <label for="sel-nuevo-quincena" style="font-size: small;">No. quincenas</label>
                                    <select name="sel-nuevo-quincena" id="sel-nuevo-quincena" class="form-control form-control-sm">
                                        <option value=""></option>
                                        <? foreach ($quincenas as $quin) { ?>
                                            <option value="<?= $quin['id']; ?>"><?= $quin['quincena']; ?></option>
                                        <? } ?>
                                    </select>
                                </div>

                                <!-- Selector para Meses -->
                                <div class="form-group show-meses">
                                    <label for="sel-nuevo-mes" style="font-size: small;">No. meses</label>
                                    <select name="sel-nuevo-mes" id="sel-nuevo-mes" class="form-control form-control-sm">
                                        <option value=""></option>
                                        <? foreach ($meses as $mes) { ?>
                                            <option value="<?= $mes['id']; ?>"><?= $mes['mes']; ?></option>
                                        <? } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-5" style="text-align: left;">
                                <label for="txt-nuevo-monto-semana" style="font-size: small;">Monto semanal</label>
                                <input type="text" id="txt-nuevo-monto-semana" class="form-control form-control-sm" value="" name="txt-nuevo-monto-semana" disabled/>
                            </div>
                            <div class="col-7" style="text-align: right;">
                                <div class="form-check mt-2">
                                    <input type="checkbox" id="chk-detener-cobro" name="chk-detener-cobro" class="form-check-input" />
                                    <label class="form-check-label" for="chk-detener-cobro" style="font-size: small;">Detener temporalmente el cobro</label>
                                </div>
                            </div>
                        </div>
                    </form>
				</div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" id="btn-cancelar-act">Cancelar</button>
                    <button type="button" class="btn btn-primary" id="btn-aceptar-act">Aceptar</button>
                </div>
			</div>
		</div>
	</div>
    <div class="modal fade" id="modal-prestamo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="staticBackdropLabel">Confirma ajuste</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-3"></div>
                            <input type="hidden" name="txt-prestajuste" id="txt-prestajuste" />
                            <input type="hidden" name="txt-prestanombre" id="txt-prestanombre" />
                            <p id="msg-ajustes"></p>
                            <button class="btn btn-primary" id="btn-confirma">Confirmar ajuste</button>
                            <div class="col-3"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal-agregar-motivo" data-backdrop="static" data-keyboard="false" tabindex="-1" aria-labelledby="modal-agregar-motivo-label" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header fondo-encabezado">
                    <h5 class="modal-title" id="modal-agregar-motivo-label">Agregar motivo</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group text-left">
                        <label for="txt-nuevo-motivo">Nuevo motivo de préstamo</label>
                        <input type="text" id="txt-nuevo-motivo" class="form-control form-control-sm" maxlength="100" autocomplete="off" />
                    </div>
                    <div class="form-group text-left autocompletar">
                        <label for="txt-partida">Partida</label>
                        <input type="text" id="txt-partida" class="form-control form-control-sm" autocomplete="off" data-n='<?php echo $nivel; ?>' data-ne='<?php echo $ne; ?>'/>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary btn-sm" id="btn-guardar-motivo">Agregar</button>
                    <button type="button" class="btn btn-secondary btn-sm" id="btn-salir-motivo">Salir</button>
                </div>
            </div>
        </div>
    </div>

<script type="text/javascript">
    $('#btn-nuevo').on('click', function(e) 
    {
        document.getElementById("nuevo").value = 1;
        $('#frm-filtro').submit();
    });

    $('#btn-aceptar-act').on('click', function(e) 
    {
        const id_prestamo = $('#txt-id-prestamo-act').val();
        const empleado = $('#txt-empleado-act').val();
        let semana =  $('#sel-nuevo-semana').val();

        if(semana=="")
            semana =$('#sel-nuevo-quincena').val();

        if(semana=="")
            semana =$('#sel-nuevo-mes').val();
        
        let monto =  $('#txt-nuevo-monto-semana').val();
        let detener_cobro = $('#chk-detener-cobro').is(':checked') ? 1 : 0;
        let frm_buscar5 = document.getElementById("frm-filtro");

/*
        if (semana == "") 
        {
            alert('Por favor ingresar el nuevo no. semanas');
            return;
        }
        if (monto == "") 
        {
            alert('Por favor ingresar el nuevo monto');
            return;
        }
*/
        const formData = 
        {
            'id_prestamo': id_prestamo,
            'empleado': empleado,
            'semana': semana,
            'monto': monto,
            'detener_cobro': detener_cobro,
            'op': 119
        };

        $.ajax({
            type: "POST",
            url: "./ajax/facilitador.php",
            data: formData,
            success: function(response) 
            {
                let [mensaje, variable] = response.split("|");
                alert(mensaje);
                $('#modal-actualiza').modal('hide');
                document.getElementById("txt-empleado").value = variable;
                document.getElementById("nuevo").value = 2;
                frm_buscar5.submit();                        
            }
        });
    });

    $('#btn-confirma').on('click', function(e) 
    {
        let id = $('#txt-prestajuste').val();
        let usuario_cap = "<?php echo $clav; ?>";
        let empleado = $('#txt-prestanombre').val();
        let frm_buscar7 = document.getElementById("frm-filtro");


        let formData = {
            'op': 125,
            'id_prestamo': id,
            'responsable': usuario_cap,
            'empleado': empleado,
        };
        console.log(formData);
        $.ajax({
            type: "POST",
            url: "./ajax/facilitador.php",
            data: formData,
            success: function(response) 
            {
                let [mensaje, empleado] = response.split("|");
                $('#modal-prestamo').modal("hide");
                alert(mensaje);
                document.getElementById("txt-empleado").value = empleado;
                document.getElementById("nuevo").value = 2;
                frm_buscar7.submit();
            }
        });
    });

    $('#btn-agregar-mot').on('click', function(e) 
    {
        e.preventDefault();
        $('#txt-nuevo-motivo').val('');
        $('#txt-partida').val('');
        $('#modal-agregar-motivo').modal('show');
        setTimeout(function() 
        {
            $('#txt-nuevo-motivo').focus();
        }, 500);
    });

    $('#btn-salir-motivo').on('click', function(e) 
    {
        e.preventDefault();
        $('#modal-agregar-motivo').modal('hide');
    });

    $('#btn-guardar-motivo').on('click', function(e) 
    {
        e.preventDefault();

        let motivo = $('#txt-nuevo-motivo').val().trim().toUpperCase();
        let partida = $('#txt-partida').val().trim().toUpperCase();

        if (motivo === '') 
        {
            alert('Por favor capture el nuevo motivo de préstamo');
            $('#txt-nuevo-motivo').focus();
            return;
        }

        $.ajax({
            type: "POST",
            url: "./ajax/facilitador.php",
            dataType: "json",
            data: {
                op: 159,
                motivo: motivo,
                partida: partida
            },
            success: function(response) 
            {
                if (response.estatus !== 'OK') 
                {
                    alert(response.mensaje);
                    return;
                }

                const selMotivos = $('#sel-motivos');
                selMotivos.empty();
                selMotivos.append('<option value=""></option>');

                response.motivos.forEach(function(item) 
                {
                    const option = $('<option></option>').val(item.motivo).text(item.motivo);
                    if (item.motivo === response.motivo) 
                    {
                        option.prop('selected', true);
                    }
                    selMotivos.append(option);
                });

                $('#modal-agregar-motivo').modal('hide');
                alert(response.mensaje);
            },
            error: function() 
            {
                alert('No fue posible agregar el motivo de préstamo');
            }
        });
    });

    if (document.getElementById('txt-partida')) 
    {
        autocompletar('txt-partida', 4, 'partida_socio');
    }
</script>
</body>
<script type="text/javascript">
	jQuery(document).ready(function() 
	{
		$(window).resize(function() 
		{
			centra_contenedor();
		});
		$(window).resize();

		$('#men_pie').css("top", h);
		$('#men_pie').css("background", "red");

		$('#tmenu td').click(function(evento) 
		{
			var modulo = $(this).text().replace(/ /gi, "_");
			$("#opcion").load("menu.php?modulo=" + modulo);
		});

		$("#salir").click(function() 
		{
			window.location = "https://www.sis-ca.com.mx:7863/";
		});

		$("#email").click(function() 
		{
			window.open("http://webmail.carobra.com.mx/imp/login.php");
		});

        // formato campos tipo moneda
		$(".moneda").formatCurrency();

        $(".moneda").css("text-align","right");

        $('body').on('focusout', '.moneda',function()
        {
            $(this).formatCurrency();
        });

        $('#btn-buscar').on('click', function(e) 
		{
            document.getElementById("nuevo").value = 2;
			$('#frm-filtro').submit();
		});

        autocompletar('txt-empleado', 4, 'empleados_prestamos_activos', $("txt-empleado").data('n') + "|" + $("txt-empleado").data('ne'));

        $("#btn-excel").click(function() 
		{
	  		pag="prestamos_rh_excel.php?" + $("#frm-filtro").serialize();
	  		window.open(pag, '_blank');
        });
	});
</script>
</html>
