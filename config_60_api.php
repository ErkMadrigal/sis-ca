<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

session_start();

header('Content-Type: application/json');

require './vendor/autoload.php';
include("./funciones/encripta.php");

$conecta = new conector();

$input = json_decode(file_get_contents('php://input'), true);

$action = $input['action'] ?? '';

if($action === 'setCampania'){

    $minima      = trim($input['minima'] ?? '');
    $maxima      = trim($input['maxima'] ?? '');
    $inicio      = trim($input['inicio'] ?? '');
    $fin         = trim($input['fin'] ?? '');
    $porcentaje  = trim($input['porcentaje'] ?? '');

    // VALIDACIONES
    if(
        empty($minima) ||
        empty($maxima) ||
        empty($inicio) ||
        empty($fin) ||
        empty($porcentaje)
    ){

        echo json_encode([
            'status'  => 'error',
            'message' => 'Todos los campos son obligatorios'
        ]);

        exit;
    }

    // VALIDAR EDADES
    if($minima >= $maxima){

        echo json_encode([
            'status'  => 'error',
            'message' => 'La edad máxima debe ser mayor a la mínima'
        ]);

        exit;
    }

    // VALIDAR FECHAS
    if($inicio > $fin){

        echo json_encode([
            'status'  => 'error',
            'message' => 'La fecha final debe ser mayor a la inicial'
        ]);

        exit;
    }

    // INSERT
    $sql = "INSERT INTO invikta.config_60m(
                edad_minima,
                edad_maxima,
                fecha_inicial,
                fecha_final,
                porcentaje_comision,
                activo
            ) VALUES (
                '$minima',
                '$maxima',
                '$inicio',
                '$fin',
                '$porcentaje',
                1
            )";

    $res = $conecta->consulta($sql);

    if($res){

        echo json_encode([
            'status'  => 'ok',
            'message' => 'Campaña creada correctamente'
        ]);

    }else{

        echo json_encode([
            'status'  => 'error',
            'message' => 'Error al crear la campaña'
        ]);

    }

    exit;
}


if($action === 'getDataCapania'){

    // CADUCAR AUTOMÁTICAMENTE
    $sqlUpdate = "UPDATE invikta.config_60m
                  SET activo = 0
                  WHERE fecha_final < CURDATE()";

    $conecta->consulta($sqlUpdate);

    // TRAER SOLO MES ACTUAL
    $mesActual  = date('m');
    $anioActual = date('Y');

    $sql = "SELECT *
            FROM invikta.config_60m
            WHERE YEAR(fecha_inicial) = '$anioActual'
            ORDER BY id DESC";

    $res = $conecta->consulta($sql);

    $data = [];

    while($row = mysqli_fetch_assoc($res)){
        $data[] = $row;
    }

    echo json_encode([
        'status' => 'ok',
        'data'   => $data
    ]);

    exit;
}

if($action === 'updateDataCapania'){

    $id      = $input['id'] ?? '';
    $activo  = $input['activo'] ?? '';

    if(empty($id)){

        echo json_encode([
            'status'  => 'error',
            'message' => 'ID requerido'
        ]);

        exit;
    }

    $sql = "UPDATE invikta.config_60m
            SET activo = '$activo'
            WHERE id = '$id'";

    $res = $conecta->consulta($sql);

    if($res){

        echo json_encode([
            'status'  => 'ok',
            'message' => 'Campaña actualizada correctamente'
        ]);

    }else{

        echo json_encode([
            'status'  => 'error',
            'message' => 'No se pudo actualizar la campaña'
        ]);

    }

    exit;
}