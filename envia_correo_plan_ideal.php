<?php
    include("./funciones/encripta.php");
   

    $conecta = new conector();

    $op = addslashes(trim($_POST["op"]));

    function envia_correo($body, $from, $cliente_email, $asesor_email=null, $subjet, $filename) 
    {
        include_once("./PHPMailer/class.phpmailer.php");
        include_once("./PHPMailer/class.smtp.php"); 
    
        try {
            $mail = new PHPMailer();
            $mail->IsSMTP();
            $mail->SMTPDebug  = 0;
            $mail->SMTPAuth = true;
            $mail->Host = "smtp.gmail.com";
            $mail->SMTPSecure = 'tls';
            $mail->Username = "aplanideal@gmail.com";
            $mail->Password = "jcywfnfttnxjzbot";
            $mail->Port = 587;

            $mail->SetFrom('aplanideal@gmail.com', 'administrador Carobra');
            $mail->AddAddress($cliente_email);

            if (!empty(trim($asesor_email))) 
                $mail->AddBCC($asesor_email);

            $mail->AddBCC($asesor_email);
            $mail->IsHTML(true);
            $mail->Subject = $subjet;
            $mail->Body = $body;

            if (file_exists($filename)) 
            {
                $mail->AddAttachment($filename);
            }
    
            if (!$mail->Send()) 
            {
                throw new Exception("Error al enviar el correo: " . $mail->ErrorInfo);
            }
    
            return true;
        } catch (Exception $e)  {
                                    echo "Error: " . $e->getMessage();
                                    return false;
                                }
    }
    

    switch ($op) 
    {
        case 1:
            $mes = addslashes(trim($_POST["mes"]));
            $anio = addslashes(trim($_POST["anio"]));

            $query = "Select p.id_pago, p.id_cliente, c.nss, CONCAT(c.nombre, ' ', c.apaterno, ' ', c.amaterno) AS cliente, 
                            p.monto, p.mes, p.anio, p.motivo, p.no_pago, cc.email, p.generado_recibo, p.envio_recibo ,uc.email as asesor_email    
                    FROM plan_ideal.clientes c
                    INNER JOIN plan_ideal.pagos p ON (c.id_cliente = p.id_cliente)
                    INNER JOIN plan_ideal.cli_contacto cc ON (c.id_cliente = cc.id_cliente)
                    left join carobra.usuarios uc on (c.asesor=uc.clave)
                    WHERE p.mes = $mes AND p.anio = $anio 
                    AND p.generado_recibo = 1 
                    AND p.envio_recibo = 0  
                    ORDER BY cliente ASC
                    limit 0,20";
            $bd = $conecta->consulta($query);
            
            $correos_fallidos = [];
            $contador = 0;
            
            if ($bd->num_rows != 0) 
            {
                while ($tb = mysqli_fetch_array($bd)) 
                {
                    $id_pago = $tb["id_pago"];
                    $cliente_email = $tb["email"];
                    $asesor_email = $tb["asesor_email"];
                    $cliente_nombre = $tb["cliente"];
                    $nss = $tb["nss"];

                    $nom_file = $nss . '_' . $mes . '_' . $anio . '.pdf';
                    $filename = "./recibos_plan_ideal/$nom_file";

                    $body = "
                        <p>Estimado/a <strong>$cliente_nombre</strong>,</p>
                        <p>Por medio del presente, le hacemos llegar el recibo de pago correspondiente a su transacción reciente.</p>
                        <p>Adjunto a este correo encontrará el comprobante oficial.</p>
                        <p><strong>Importante:</strong></p>
                        <ol>
                            <li>Para futuros pagos, recuerde incluir su número de cliente como referencia, mismo que encontrará en el documento adjunto; lo puede ingresar como referencia o concepto de pago. Esto nos permite identificar su transacción de manera rápida y eficiente.</li>
                            <li>Las fechas límite de pago son del 1 al 5 de cada mes. Le pedimos tomar esta información en cuenta para evitar inconvenientes.</li>
                        </ol>
                        <p>En caso de cualquier duda o si necesita asistencia, no dude en comunicarse con nosotros. Será un placer atenderle.</p>
                        <p>Atentamente,</p>";
                    $subjet = "Recibo de pago - $mes/$anio";

                    if (envia_correo($body, "Carobra Plan Ideal", $cliente_email, $asesor_email,$subjet, $filename)) 
                    {
                        $q_update = "UPDATE plan_ideal.pagos SET envio_recibo = 1 WHERE id_pago = $id_pago";
                        $conecta->consulta($q_update);
                    } 
                    else 
                    {
                        $correos_fallidos[] = $cliente_email;
                    }

                    sleep(3);

                    $contador++;
                }
            }

            if (count($correos_fallidos) > 0) 
            {
                echo "No se pudieron enviar los correos a las siguientes direcciones: " . implode(", ", $correos_fallidos);
            } 
            else 
            {
                echo "Todos los correos se enviaron exitosamente.";
            }
        break;
    }
?>
