<?php
/* ════════════════════════════════════════════════════════════
   seg_ventas_reporteador_funciones.php

   Lógica COMPARTIDA entre seg_ventas_reporteador.php (vista en pantalla)
   y seg_ventas_reporteador_excel.php (exportación): arma la tabla temporal
   de trabajo, la enriquece con los mismos ~15 UPDATE que ya usa
   seg_ventas.php, y corre el SELECT final. Así la parte pesada del
   negocio vive en un solo lugar y no se duplica en dos archivos
   (si algún día cambia una regla de negocio, se cambia aquí y ya).

   ALCANCE (leer antes de tocar):
   Por ahora el Reporteador solo cubre búsquedas "estructurales" con
   rango de fechas: Socio Comercial, SubBrocker, Comisionista,
   Gerencia y NAP — el mismo modo que ya usa seg_ventas.php para
   generar listados masivos (varios registros a la vez).

   La búsqueda puntual por Folio/Oportunidad, NSS/CURP o Cliente NO
   está conectada aquí todavía: en seg_ventas.php ese modo depende de
   una función externa (tablas_sv2()) y de rastrear folios en tablas
   de respaldo de años anteriores — sirve para localizar UN registro
   puntual, no tanto para generar un reporte masivo con columnas a
   elegir. Si después se necesita, se puede agregar reutilizando esa
   misma lógica de seg_ventas.php.
════════════════════════════════════════════════════════════ */

/* ── Catálogo de campos disponibles para el reporte ──
   Cada key es una columna que el SELECT final de obtenerDatosReporte()
   ya trae resuelta/legible (no ids crudos, ya viene el texto).

   'grupo' organiza el panel de selección de campos POR TEMA (Saldos,
   Fechas, Personales, etc. — reemplaza el agrupado anterior por tabla
   de origen, a petición expresa para el picker en árbol). 'orden' fija
   el orden de despliegue dentro de cada grupo en el árbol; si no se
   usa 'orden' explícito el picker cae al orden de declaración aquí. */
$CAMPOS_REPORTE = [
    // ── Personales / Cliente ──
    'trabajador'        => ['label' => 'Cliente / Trabajador',       'grupo' => 'Personales'],
    'no_oportunidad'    => ['label' => 'No. oportunidad',            'grupo' => 'Personales'],
    'nss'               => ['label' => 'NSS',                        'grupo' => 'Personales'],
    'curp'              => ['label' => 'CURP',                       'grupo' => 'Personales'],
    'edad'              => ['label' => 'Edad',                       'grupo' => 'Personales'],
    'telefono'          => ['label' => 'Teléfono',                   'grupo' => 'Personales'],
    'celular'           => ['label' => 'Celular',                    'grupo' => 'Personales'],
    'email_trab'        => ['label' => 'Email',                      'grupo' => 'Personales'],
    'cp'                => ['label' => 'CP',                         'grupo' => 'Personales'],
    'observa'           => ['label' => 'Observaciones',              'grupo' => 'Personales'],

    // ── Saldos / montos ──
    'saldo_estimado'    => ['label' => 'Saldo estimado',             'grupo' => 'Saldos'],
    'saldo_real'        => ['label' => 'Saldo real',                 'grupo' => 'Saldos'],
    'saldo_pago'        => ['label' => 'Saldo pago',                 'grupo' => 'Saldos'],
    'monto_anticipo'    => ['label' => 'Monto anticipo',             'grupo' => 'Saldos'],
    'monto_seg_ant'     => ['label' => 'Monto segundo anticipo',     'grupo' => 'Saldos'],

    // ── Fechas ──
    'fec_traspaso'      => ['label' => 'Fecha traspaso',             'grupo' => 'Fechas'],
    'fec_captura'       => ['label' => 'Fecha captura',              'grupo' => 'Fechas'],
    'fec_anticipo'      => ['label' => 'Fecha anticipo',             'grupo' => 'Fechas'],
    'fec_ant2'          => ['label' => 'Fecha segundo anticipo',     'grupo' => 'Fechas'],
    'fec_cierre'        => ['label' => 'Fecha cierre',               'grupo' => 'Fechas'],
    'fec_rechazo'       => ['label' => 'Fecha rechazo',              'grupo' => 'Fechas'],
    'fec_pago'          => ['label' => 'Fecha pago',                 'grupo' => 'Fechas'],
    'fec_desc'          => ['label' => 'Fecha desc. incumplimiento', 'grupo' => 'Fechas'],
    'fec_envio'         => ['label' => 'Fecha envío AV',             'grupo' => 'Fechas'],
    'cap_movilidad'     => ['label' => 'Fecha captura movilidad',    'grupo' => 'Fechas'],
    'fec_sello'         => ['label' => 'Fecha sello movilidad',      'grupo' => 'Fechas'],

    // ── Comercial / Estructura ──
    'clave'             => ['label' => 'Clave',                      'grupo' => 'Comercial / Estructura'],
    'no_empleado'       => ['label' => 'No. empleado',                'grupo' => 'Comercial / Estructura'],
    'nombre'            => ['label' => 'Comisionista',               'grupo' => 'Comercial / Estructura'],
    'analista'          => ['label' => 'Analista (captura)',         'grupo' => 'Comercial / Estructura'],
    'sc'                => ['label' => 'Socio Comercial',            'grupo' => 'Comercial / Estructura'],
    'brocker'           => ['label' => 'SubBrocker',                 'grupo' => 'Comercial / Estructura'],
    'gerencia'          => ['label' => 'Gerencia',                   'grupo' => 'Comercial / Estructura'],

    // ── Producción / Movimiento ──
    'tipo_prod'         => ['label' => 'Tipo de producto',           'grupo' => 'Producción / Movimiento'],
    'folio'             => ['label' => 'Folio',                      'grupo' => 'Producción / Movimiento'],
    'nap'               => ['label' => 'NAP',                        'grupo' => 'Producción / Movimiento'],
    'movimiento'        => ['label' => 'Movimiento',                 'grupo' => 'Producción / Movimiento'],
    'confirma'          => ['label' => 'Confirmación',               'grupo' => 'Producción / Movimiento'],
    'cierre_cap'        => ['label' => 'Cierre captura',             'grupo' => 'Producción / Movimiento'],
    'mes'               => ['label' => 'Mes',                        'grupo' => 'Producción / Movimiento'],
    'campana'           => ['label' => 'Campaña',                    'grupo' => 'Producción / Movimiento'],

    // ── Estatus / Proceso ──
    'estatus'           => ['label' => 'Estatus',                    'grupo' => 'Estatus / Proceso'],
    'estatus_pbi'       => ['label' => 'Estatus Procesar',           'grupo' => 'Estatus / Proceso'],
    'afore'             => ['label' => 'Afore cedente',              'grupo' => 'Estatus / Proceso'],
    'est_anticipo'      => ['label' => 'Estatus anticipo',           'grupo' => 'Estatus / Proceso'],
    'tipo_rechazo'      => ['label' => 'Tipo de rechazo',            'grupo' => 'Estatus / Proceso'],
    'rechazo'           => ['label' => 'Rechazo',                    'grupo' => 'Estatus / Proceso'],
    'descrip'           => ['label' => 'Descripción (extemporáneo)', 'grupo' => 'Estatus / Proceso'],
    'movilidad'         => ['label' => 'Movilidad',                  'grupo' => 'Estatus / Proceso'],
    /* Igual que en seg_ventas.php (tabla movilidad, join por folio):
       'categoria_cliente' es la etiqueta que ahí se pinta junto al
       nombre del cliente en el título de la ficha (ej. "Cotizante") --
       aquí se muestra igual, como badge en el header del modal (ver
       renderFicha() en seg_ventas_reporteador.php), además de estar
       disponible como columna normal del reporte. */
    'categoria_cliente' => ['label' => 'Categoría cliente',          'grupo' => 'Estatus / Proceso'],
    'ley'               => ['label' => 'Ley',                        'grupo' => 'Estatus / Proceso'],
    'cotiza'            => ['label' => 'Cotiza',                     'grupo' => 'Estatus / Proceso'],
    'pplus'             => ['label' => 'P+ (cliente)',               'grupo' => 'Estatus / Proceso'],
    'transfer_out'      => ['label' => 'Transfer out / baja cliente','grupo' => 'Estatus / Proceso'],

    // ── Autorización de Vida (AV) ──
    'folio_av'          => ['label' => 'Folio AV',                   'grupo' => 'Autorización de Vida (AV)'],
    'estatus_av'        => ['label' => 'Estatus AV',                 'grupo' => 'Autorización de Vida (AV)'],
    'frecuencia'        => ['label' => 'Frecuencia',                 'grupo' => 'Autorización de Vida (AV)'],

    // ── Campos que trae el reporte viejo (xls de seg_ventas) ──
    'nivel'             => ['label' => 'Nivel',                      'grupo' => 'Comercial / Estructura'],
    'categoria'         => ['label' => 'Categoría',                  'grupo' => 'Comercial / Estructura'],
    'tipo_venta'        => ['label' => 'Tipo venta',                 'grupo' => 'Producción / Movimiento'],
    'fec_ult_traspaso'  => ['label' => 'Fecha últ. traspaso',        'grupo' => 'Fechas'],
    'estatus_pago'      => ['label' => 'Estatus pago',               'grupo' => 'Estatus / Proceso'],
    'autoriza_sin_av'   => ['label' => 'Autorización sin AV',        'grupo' => 'Autorización de Vida (AV)'],
];

/* Campos que se formatean como dinero ($#,##0.00) al desplegarse. */
$CAMPOS_MONEDA = ['saldo_estimado', 'saldo_real', 'saldo_pago', 'monto_anticipo', 'monto_seg_ant'];

/* Campos de contacto/identidad que solo deben verse con nivel de
   admin (nivel 12, mismo criterio que ya usa getCumplimiento_actualizado.php
   y otras vistas del sitio). El resto del catálogo (saldos, fechas,
   estructura comercial, etc.) ya era visible para cualquier usuario
   del Reporteador desde antes de agregar estos campos y se deja así
   para no cambiar el comportamiento actual. */
$CAMPOS_SOLO_ADMIN = ['telefono', 'celular', 'cp', 'email_trab', 'frecuencia'];

/* Devuelve el catálogo recortado según el nivel del usuario: los admins
   (nivel 12) ven todo, cualquier otro nivel no ve $CAMPOS_SOLO_ADMIN.
   Se usa tanto para pintar el árbol de campos como, más importante,
   para VALIDAR en el backend (seg_ventas_reporteador_api.php y
   seg_ventas_reporteador_excel.php) qué campos puede pedir cada quien —
   nunca hay que confiar solo en que la pantalla los oculte. */
/* Nivel "efectivo" para el Reporteador: un usuario nivel 6 que además
   está dado de alta en carobra_2014.permisos_sv funge como administrador
   (nivel 12). Cualquier otro caso regresa su nivel normal.
   $nombreDecodificado = nombre ya con utf8_decode (como se pasa a nivel_usuario). */
function nivelReporteador($conecta, $nombreDecodificado) {
    $nivel = nivel_usuario($nombreDecodificado);
    if ($nivel != 6) return $nivel;

    $nomEsc = mysqli_real_escape_string($conecta->conexion, $nombreDecodificado);
    $rs = $conecta->consulta("Select clave from empl_usuarios where nombre='$nomEsc' limit 1");
    $r  = $rs ? mysqli_fetch_assoc($rs) : null;
    $cl = trim($r['clave'] ?? '');
    if ($cl === '') return $nivel;

    $clEsc = mysqli_real_escape_string($conecta->conexion, $cl);
    $rs2 = $conecta->consulta("Select 1 from carobra_2014.permisos_sv where clave='$clEsc' limit 1");
    if ($rs2 && mysqli_num_rows($rs2) > 0) return 12;

    return $nivel;
}

function camposVisiblesParaNivel($nivel, $catalogo) {
    if ($nivel == 12) return $catalogo;
    global $CAMPOS_SOLO_ADMIN;
    return array_diff_key($catalogo, array_flip($CAMPOS_SOLO_ADMIN));
}

/* Regresa los nombres de campo (de $CAMPOS_SOLO_ADMIN) que el admin le
   autorizó puntualmente a ESTA clave -- ver tabla
   carobra_2014.reporteador_campos_permisos y el modal "⚙️ Permisos de
   campos sensibles" (admin-only) en seg_ventas_reporteador.php. */
function obtenerCamposAutorizados($conecta, $clave) {
    if (empty($clave)) return [];
    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
    $bd = $conecta->consulta("Select campo from carobra_2014.reporteador_campos_permisos where clave='$claveEsc'");
    $campos = [];
    if ($bd) {
        while ($row = mysqli_fetch_assoc($bd)) {
            $campos[] = $row['campo'];
        }
    }
    return $campos;
}

/* Igual que camposVisiblesParaNivel(), pero además deja pasar los
   campos de $CAMPOS_SOLO_ADMIN que el admin le haya autorizado puntualmente
   a esta clave (reporteador_campos_permisos) -- así el admin decide
   quién más, sin necesidad de subirle el nivel a esa persona. Es la
   que deben usar seg_ventas_reporteador.php/_api.php/_excel.php; se deja
   camposVisiblesParaNivel() intacta por si algo más la sigue usando. */
function camposVisiblesParaUsuario($conecta, $nivel, $clave, $catalogo) {
    if ($nivel == 12) return $catalogo;
    global $CAMPOS_SOLO_ADMIN;
    $autorizados = obtenerCamposAutorizados($conecta, $clave);
    $bloqueados  = array_diff($CAMPOS_SOLO_ADMIN, $autorizados);
    return array_diff_key($catalogo, array_flip($bloqueados));
}

/* "Opciones adicionales" (sfp/srm0/reg_esp -- filtros contables sobre
   folios sin fecha de pago, saldo real distinto de cero y registro
   especial) solo las debe poder USAR admin (nivel 12) o Finanzas.
   Reutiliza carobra_2014.permisos_pestanas (misma fuente que ya usa
   RRHH_api.php para la pestaña "Finanzas" del expediente de un
   empleado) -- nunca hay que confiar solo en que la pantalla lo
   oculte, así que este mismo check se usa server-side en
   generarReporte/excel para forzar esos flags a 0 si no aplica. */
function puedeVerOpcionesAdicionales($conecta, $nivel, $clave) {
    if ($nivel == 12) return true;
    if (empty($clave)) return false;
    $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
    $bd = $conecta->consulta("Select 1 from carobra_2014.permisos_pestanas where clave='$claveEsc' and pestana='finanzas' limit 1");
    return ($bd && mysqli_fetch_array($bd)) ? true : false;
}

/* ════════════════════════════════════════════════════════════
   Blindaje por NIVEL de qué tipo de filtro puede usar cada quien,
   y a qué valor queda FIJO (sin poder elegir otro) — mismas reglas
   que ya usa seg_ventas.php real:
     - Nivel 3, 4 y 5: no pueden buscar por "Soc. Comercial" ni "NAP".
     - Nivel 3 y 4: tampoco pueden buscar por "SubBrocker".
     - Nivel 3 y 7 (Comisionista): el filtro Comisionista queda FIJO
       a su propio nombre.
     - Nivel 5 (SubBrocker): el filtro SubBrocker queda FIJO a su
       propio nombre.
     - Nivel 13 (Socio Comercial): el filtro Soc. Comercial queda
       FIJO a su propio nombre.
     - Nivel 4 (Gerencia): el filtro Gerencia queda FIJO, pero al
       nombre de SU gerencia (resuelto por BD), no a su nombre
       personal — ver resolverValorFiltroFijo().
   "Folio" (búsqueda puntual por número de folio) nunca depende del
   nivel — está siempre disponible y nunca es fijo — así que no se
   incluye en esta lista; seg_ventas_reporteador.php la agrega aparte.
   Nunca hay que confiar solo en que la pantalla lo oculte/bloquee:
   estas mismas funciones se usan también para validar en el backend
   (seg_ventas_reporteador_api.php y seg_ventas_reporteador_excel.php).
════════════════════════════════════════════════════════════ */
function filtrosPermitidosParaNivel($nivel) {
    $puedeSC         = !in_array($nivel, [3, 4, 5], true);
    $puedeSubBrocker = !in_array($nivel, [3, 4], true);
    $puedeGerencia   = ($nivel != 3);
    $puedeNap        = !in_array($nivel, [3, 4, 5], true);
    $permitidos = [];
    if ($puedeSC)         $permitidos[] = 'Soc. Comercial';
    if ($puedeSubBrocker) $permitidos[] = 'SubBrocker';
    $permitidos[] = 'Comisionista';
    if ($puedeGerencia)   $permitidos[] = 'Gerencia';
    if ($puedeNap)        $permitidos[] = 'NAP';
    return $permitidos;
}

function filtroEsFijoParaNivel($nivel, $filtro) {
    if ($filtro === 'Soc. Comercial') return ($nivel == 13);
    if ($filtro === 'SubBrocker')     return ($nivel == 5);
    if ($filtro === 'Comisionista')   return in_array($nivel, [3, 7], true);
    if ($filtro === 'Gerencia')       return ($nivel == 4);
    return false; // Folio y NAP nunca quedan fijos
}

function filtroFijoParaNivel($nivel) {
    foreach (['Soc. Comercial', 'SubBrocker', 'Comisionista', 'Gerencia'] as $ft) {
        if (filtroEsFijoParaNivel($nivel, $ft)) return $ft;
    }
    return null;
}

/* Resuelve a qué VALOR queda fijo el filtro fijo del nivel (ver arriba).
   Para todos los casos salvo Gerencia es simplemente el propio nombre
   del usuario. Para Gerencia (nivel 4) hay que resolver primero su
   clave y luego, por BD, el nombre de la gerencia a la que pertenece
   (usuarios_nivel_gerencia + gerencias) — no es su nombre personal. */
function resolverValorFiltroFijo($conecta, $nivel, $nombreUsuario, $filtro) {
    if (!filtroEsFijoParaNivel($nivel, $filtro)) return null;
    if ($filtro === 'Gerencia') {
        $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreUsuario));
        $query = "Select clave from empl_usuarios where nombre='$nombreEsc'";
        $bd    = $conecta->consulta($query);
        $tb    = $bd ? mysqli_fetch_array($bd) : null;
        $clave = $tb['clave'] ?? '';
        if ($clave === '') return '';
        $claveEsc = mysqli_real_escape_string($conecta->conexion, $clave);
        $query = "Select gerencia
                    from (Select distinct id_gerencia from usuarios_nivel_gerencia
                          where clave='$claveEsc' and pertenece=1) ung
                        inner join (Select distinct id_gerencia, gerencia from gerencias) g
                                on (ung.id_gerencia=g.id_gerencia)";
        $bd = $conecta->consulta($query);
        $tb = $bd ? mysqli_fetch_array($bd) : null;
        return $tb['gerencia'] ?? '';
    }
    return $nombreUsuario;
}

/* Vuelve a llenar la tabla de trabajo estruc_empleados (truncate +
   insert) con la estructura comercial vigente -- sc/brocker/gerencia
   de cada clave, según a qué gerencia(s) pertenece (usuarios_nivel_gerencia)
   y de dónde cuelga esa gerencia (estructura_gerencia). Se usa dentro
   de obtenerDatosReporte() antes del SELECT final, y también antes de
   cualquier búsqueda/validación que necesite consultar la jerarquía
   (ver resolverAmbitoJerarquico/valorDentroDeAmbito) -- estruc_empleados
   es una tabla de trabajo, no queda garantizado que esté poblada/al
   día si no se refresca primero. */
function refrescarEstrucEmpleados($conecta) {
    $conecta->consulta("Truncate estruc_empleados");
    $conecta->consulta(
        "Insert ignore into estruc_empleados
            Select distinct clave,no_empleado,sc,brocker,
                   ung.id_gerencia,gerencia
            from usuarios_nivel_gerencia ung
                inner join estructura_gerencia eg
                        on (ung.id_gerencia=eg.id_gerencia)
                inner join (Select distinct id_gerencia,gerencia from gerencias) g
                        on (ung.id_gerencia=g.id_gerencia)
            where pertenece=1"
    );
}

/* ════════════════════════════════════════════════════════════
   Ámbito jerárquico de la estructura comercial: un Socio Comercial
   (nivel 13) solo debe poder buscar/elegir SUS PROPIOS SubBrocker,
   Gerencias, Comisionistas y NAP -- no los de toda la empresa -- y en
   cascada un SubBrocker (nivel 5) solo los suyos, y una Gerencia
   (nivel 4) solo sus propios comisionistas. Esto es aparte del "valor
   fijo" (filtroEsFijoParaNivel/resolverValorFiltroFijo): el valor
   fijo dice a qué queda FORZADO el filtro que ya trae ese nivel de
   entrada (su propio SC/SubBrocker/Gerencia); el ámbito dice qué tan
   ancho puede ser cuando busca OTRO tipo de filtro permitido (ej. un
   SC buscando por Gerencia). Nunca hay que confiar solo en que el
   buscador del frontend no ofrezca otras opciones -- por eso
   valorDentroDeAmbito() también se usa para validar en el servidor.
════════════════════════════════════════════════════════════ */
function resolverAmbitoJerarquico($conecta, $nivel, $nombreUsuario) {
    $ambito = ['sc' => null, 'brocker' => null, 'gerencia' => null];
    if ($nivel == 13) {
        $ambito['sc'] = $nombreUsuario;
    } elseif ($nivel == 5) {
        $ambito['brocker'] = $nombreUsuario;
    } elseif ($nivel == 4) {
        $ambito['gerencia'] = resolverValorFiltroFijo($conecta, 4, $nombreUsuario, 'Gerencia');
    }
    return $ambito;
}

function hayAmbitoJerarquico($ambito) {
    return ($ambito['sc'] !== null || $ambito['brocker'] !== null || $ambito['gerencia'] !== null);
}

/* Arma el " AND {prefijo}sc = '...'" (o brocker/gerencia, el que
   corresponda) para pegarlo a un WHERE existente. $prefijo es el
   alias de tabla + punto cuando la consulta necesita unirse a
   estruc_empleados con alias (ej. "ee."); vacío si estruc_empleados
   ya es la tabla base sin alias. */
function condicionAmbitoSql($conecta, $ambito, $prefijo = '') {
    if ($ambito['sc'] !== null) {
        return " AND {$prefijo}sc = '" . mysqli_real_escape_string($conecta->conexion, $ambito['sc']) . "'";
    }
    if ($ambito['brocker'] !== null) {
        return " AND {$prefijo}brocker = '" . mysqli_real_escape_string($conecta->conexion, $ambito['brocker']) . "'";
    }
    if ($ambito['gerencia'] !== null) {
        return " AND {$prefijo}gerencia = '" . mysqli_real_escape_string($conecta->conexion, $ambito['gerencia']) . "'";
    }
    return '';
}

/* Verifica en el servidor que un valor de filtro estructural
   (SubBrocker/Gerencia/Comisionista/NAP) elegido por el usuario
   realmente caiga dentro de SU PROPIO ámbito -- se llama justo antes
   de correr el reporte (generarReporte/export a Excel), para no
   confiar solo en que el buscador del frontend no haya ofrecido otras
   opciones. Requiere que estruc_empleados ya esté fresca --
   refrescarEstrucEmpleados($conecta) antes de llamar esto. Si el
   usuario no tiene ámbito (nivel sin restricción, ej. admin) siempre
   regresa true -- nada que validar. */
function valorDentroDeAmbito($conecta, $ambito, $filtro, $valor1) {
    if (!hayAmbitoJerarquico($ambito)) return true;
    if ($valor1 === '' || $valor1 === null) return false;

    $condicion = condicionAmbitoSql($conecta, $ambito, 'ee.');
    if ($condicion === '') return true;

    $valorEsc = mysqli_real_escape_string($conecta->conexion, $valor1);

    if ($filtro === 'SubBrocker') {
        $sql = "Select 1 from carobra_2014.estruc_empleados ee where ee.brocker='$valorEsc' $condicion limit 1";
    } elseif ($filtro === 'Gerencia') {
        $sql = "Select 1 from carobra_2014.estruc_empleados ee where ee.gerencia='$valorEsc' $condicion limit 1";
    } elseif ($filtro === 'Comisionista') {
        $nombreEmp    = ($valor1 == 'Usuario de prueba') ? 'MARIA MAYRA SANTOS CORTES' : $valor1;
        $nombreEmpEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreEmp));
        $sql = "Select 1 from carobra_2014.estruc_empleados ee
                    inner join empl_usuarios eu on (ee.no_empleado=eu.no_empleado)
                where eu.nombre='$nombreEmpEsc' $condicion limit 1";
    } elseif ($filtro === 'NAP') {
        $partes = explode('(', $valor1);
        $nap    = trim(str_replace(')', '', $partes[1] ?? $valor1));
        $napEsc = mysqli_real_escape_string($conecta->conexion, $nap);
        $sql = "Select 1 from carobra_2014.naps n
                    inner join carobra_2014.estruc_empleados ee on (n.no_empleado=ee.no_empleado)
                where n.nap='$napEsc' $condicion limit 1";
    } else {
        return true; // Soc. Comercial no llega aquí con ámbito activo (siempre es fijo para nivel 13)
    }

    $bd = $conecta->consulta($sql);
    return ($bd && mysqli_num_rows($bd) > 0);
}

/* ── FIX DE SEGURIDAD ──────────────────────────────────────────
   Bug encontrado: la búsqueda por Folio NUNCA pasaba por ningún
   filtro de estructura -- cualquier usuario, de cualquier nivel,
   podía buscar y ver CUALQUIER folio del sistema con solo teclear el
   número (bastaba con que valorDentroDeAmbito() cayera en su "else
   return true" para Folio, y nivel 3/7 -Comisionista- ni siquiera
   tenía ámbito establecido -- resolverAmbitoJerarquico() solo cubre
   13/5/4). Esta función restringe la búsqueda de folio a la propia
   estructura del usuario, salvo administrador (nivel 12) que sigue
   viendo todo:
     - Nivel 12 (admin): sin restricción.
     - Nivel 13 (SC) / 5 (SubBrocker) / 4 (Gerencia): mismo ámbito
       jerárquico que ya usan los demás filtros (ver
       resolverAmbitoJerarquico/condicionAmbitoSql).
     - Nivel 3 y 7 (Comisionista): solo SUS PROPIOS folios (por
       no_empleado) -- no tienen ámbito jerárquico más amplio.
     - Cualquier otro nivel no reconocido: se niega por defecto (nunca
       hay que arriesgarse a exponer folios ajenos por un nivel que no
       se contempló).
   Regresa el fragmento SQL " AND ..." para pegar al WHERE de la
   búsqueda de folio en obtenerDatosReporte() -- requiere que
   estruc_empleados ya esté fresca (refrescarEstrucEmpleados corre
   antes, sin importar $esFolio). */
function condicionFolioParaNivel($conecta, $nivel, $nombreUsuario) {

    if ($nivel == 12) return '';

    if (in_array($nivel, [3, 7], true)) {
        $nombreEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreUsuario));
        $bd = $conecta->consulta("Select no_empleado from empl_usuarios where nombre='$nombreEsc'");
        $tb = $bd ? mysqli_fetch_array($bd) : null;
        $noEmp = intval($tb['no_empleado'] ?? ($tb[0] ?? 0));
        return " AND p.no_empleado = $noEmp";
    }

    $ambito = resolverAmbitoJerarquico($conecta, $nivel, $nombreUsuario);
    if (hayAmbitoJerarquico($ambito)) {
        $condicion = condicionAmbitoSql($conecta, $ambito, 'ee.');
        if ($condicion !== '') return $condicion;
    }

    return ' AND 1=0';
}

/* Tipos de filtro soportados por el Reporteador (ver nota de alcance
   arriba). Se usa tanto para pintar los botones como para validar.
   'Folio' es distinto a los demás: es una búsqueda puntual de UN
   registro (sin rango de fechas ni filtro estructural), ver el
   branch $esFolio dentro de obtenerDatosReporte(). */
$FILTROS_REPORTEADOR = ['Soc. Comercial', 'SubBrocker', 'Comisionista', 'Gerencia', 'NAP', 'Folio'];

/* Selección por default cuando el usuario no ha elegido nada aún
   (primera visita, sin nada guardado en localStorage). */
$CAMPOS_DEFAULT = ['sc', 'brocker', 'gerencia', 'tipo_prod', 'folio', 'nss', 'trabajador', 'nombre', 'mes', 'saldo_estimado', 'saldo_real', 'est_anticipo', 'monto_anticipo', 'monto_seg_ant', 'estatus'];

/* Recibe una lista de keys (ej. desde $_POST/$_GET campos_csv) y
   regresa solo las que existen en el catálogo. IMPORTANTE: preserva
   el ORDEN en que vienen en $seleccion (no el orden del catálogo),
   porque ese orden es el que el usuario armó arrastrando los campos
   en el picker, y define el orden de columnas en pantalla y en el
   Excel. También quita duplicados. */
function filtrarCamposValidos($seleccion, $catalogo) {
    if (!is_array($seleccion)) return [];
    $validos = array_keys($catalogo);
    $limpios = [];
    foreach ($seleccion as $campo) {
        $campo = trim($campo);
        if ($campo !== '' && in_array($campo, $validos, true) && !in_array($campo, $limpios, true)) {
            $limpios[] = $campo;
        }
    }
    return $limpios;
}

/* ════════════════════════════════════════════════════════════
   obtenerDatosReporte — arma la tabla temporal, la enriquece y
   corre el SELECT final. SIEMPRE regresa todas las columnas del
   catálogo (el picker de campos solo decide cuáles se PINTAN /
   exportan después, no cuáles se consultan — así no hay que rearmar
   la consulta según lo que el usuario haya elegido).

   $p espera: filtro, valor1, valor2 (fecha ini), valor3 (fecha fin),
              te, sfp, srm0, reg_esp, anio_ini, anio_fin, mes_i, mes_f,
              nivel, clave, nombre

   $limite: tope de filas para no tronar memoria/tiempo en un reporte
            sin filtrar lo suficiente. Pantalla usa uno más chico,
            Excel uno más grande (ver seg_ventas_reporteador.php / _excel.php).
════════════════════════════════════════════════════════════ */
function obtenerDatosReporte($conecta, $p, $limite = 2000) {

    global $FILTROS_REPORTEADOR;

    $filtro   = $p['filtro']   ?? '';
    $valor1   = $p['valor1']   ?? '';
    $valor2   = $p['valor2']   ?? '';
    $valor3   = $p['valor3']   ?? '';
    $te       = $p['te']       ?? '';
    $sfp      = intval($p['sfp']      ?? 0);
    $srm0     = intval($p['srm0']     ?? 0);
    $reg_esp  = intval($p['reg_esp']  ?? 0);
    $anio_ini = $p['anio_ini'] ?? '';
    $anio_fin = $p['anio_fin'] ?? '';
    $mes_i    = $p['mes_i']    ?? '';
    $mes_f    = $p['mes_f']    ?? '';
    $clave    = $p['clave']    ?? '';
    $nivel    = $p['nivel']    ?? 0;
    $nombreUsuario = $p['nombre'] ?? '';

    $modoCache = !empty($p['cache_build']);   /* true = llenar sv_cache_new (ver sv_cache_funciones.php) */
    $enVivo    = !empty($p['en_vivo']);       /* true = ignorar la caché */

    $resultado = ['ok' => true, 'msg' => '', 'tit_mes' => '', 'filas' => [], 'limitado' => false, 'origen' => 'vivo', 'cache_at' => ''];

    if (!in_array($filtro, $FILTROS_REPORTEADOR, true)) {
        $resultado['ok']  = false;
        $resultado['msg'] = 'Este modo de búsqueda todavía no está disponible en el Reporteador (por ahora solo: '
                           . implode(', ', $FILTROS_REPORTEADOR) . ').';
        return $resultado;
    }

    $tit_mes = ($te == "ep") ? "Mes Pago" : "Mes Com.";
    $resultado['tit_mes'] = $tit_mes;

    /* 'Folio' es una búsqueda puntual de UN registro por número de
       folio -- no necesita rango de fechas ni filtro estructural
       (Socio Comercial/SubBrocker/Gerencia/NAP/Comisionista), a
       diferencia de los demás modos. Todo lo que sigue en este if/else
       solo aplica a los modos "estructurales con rango de fechas". */
    $esFolio = ($filtro === 'Folio');

    $fec_inc = '';
    $fec_fin = '';
    $q_emp   = 1;
    $w_nap   = '';
    $qestruc = '';
    $cacheWhere = '';   /* mismo filtro estructural pero contra sv_cache (alias c) */

    if (!$esFolio) {

        /* ── Resolver rango de fechas (mismo criterio que seg_ventas.php) ── */
        if ($valor2 == "" && $valor3 == "") {
            $cal = ($te == "ep") ? "fechas_cierre" : "fechas_cierre_com";

            $anioIniInt = intval($anio_ini);
            $mesIniInt  = intval($mes_i);
            $query = "Select fec_ini from $cal where anio=$anioIniInt and mes=$mesIniInt";
            $bd = $conecta->consulta($query);
            $tb = $bd ? mysqli_fetch_array($bd) : null;
            $valor2 = $tb['fec_ini'] ?? '';

            $anioFinInt = intval($anio_fin);
            $mesFinInt  = intval($mes_f);
            $query = "Select fec_fin from $cal where anio=$anioFinInt and mes=$mesFinInt";
            $bd = $conecta->consulta($query);
            $tb = $bd ? mysqli_fetch_array($bd) : null;
            $valor3 = $tb['fec_fin'] ?? '';
        } else {

            /* Calendario abierto (fecha exacta, no periodo por año/mes).
               Para "Mes de pago" (te=='ep', calendario anual) se manda
               la fecha tal cual la eligió el usuario -- comportamiento
               de siempre. Para "Mes de comisión" (calendario comercial)
               los periodos NO caen en límites de mes calendario (ver
               fechas_cierre_com), así que cada fecha elegida se ajusta
               al periodo comercial que la contiene ("se va al más
               próximo"), para que el reporte siempre respete periodos
               completos aunque el usuario haya picado una fecha suelta
               que caiga a la mitad de uno. Si por alguna razón la fecha
               no cae dentro de ningún periodo registrado, se deja la
               fecha literal tal cual (sin tronar). */
            if ($te !== 'ep') {
                $valor2Esc = mysqli_real_escape_string($conecta->conexion, $valor2);
                $query = "Select fec_ini from fechas_cierre_com
                          where str_to_date(fec_ini,'%d/%m/%Y') <= str_to_date('$valor2Esc','%d/%m/%Y')
                            and str_to_date(fec_fin,'%d/%m/%Y') >= str_to_date('$valor2Esc','%d/%m/%Y')
                          limit 1";
                $bd = $conecta->consulta($query);
                $tb = $bd ? mysqli_fetch_array($bd) : null;
                if ($tb && !empty($tb['fec_ini'])) { $valor2 = $tb['fec_ini']; }

                $valor3Esc = mysqli_real_escape_string($conecta->conexion, $valor3);
                $query = "Select fec_fin from fechas_cierre_com
                          where str_to_date(fec_ini,'%d/%m/%Y') <= str_to_date('$valor3Esc','%d/%m/%Y')
                            and str_to_date(fec_fin,'%d/%m/%Y') >= str_to_date('$valor3Esc','%d/%m/%Y')
                          limit 1";
                $bd = $conecta->consulta($query);
                $tb = $bd ? mysqli_fetch_array($bd) : null;
                if ($tb && !empty($tb['fec_fin'])) { $valor3 = $tb['fec_fin']; }
            }

            $anio_ini = substr($valor2, -4);
            $anio_fin = substr($valor3, -4);
        }

        if (empty($valor2) || empty($valor3)) {
            $resultado['ok']  = false;
            $resultado['msg'] = 'No se pudo resolver el rango de fechas de la búsqueda. Verifica el calendario, el periodo y/o las fechas.';
            return $resultado;
        }

        $fec_inc = $valor2;
        $fec_fin = $valor3;

        /* ── Armar el filtro estructural (mismo criterio que seg_ventas.php) ── */

        /* "TODOS"/"TODAS" (solo admin, nivel 12): no se filtra por SC /
           SubBrocker / Gerencia / NAP / Comisionista; solo aplica el
           rango de fechas. Para cualquier otro nivel se ignora esta
           palabra clave y se busca como valor literal (no encuentra nada). */
        $valorNorm = mb_strtoupper(trim($valor1), 'UTF-8');
        $esTodos   = ($nivel == 12 && ($valorNorm === 'TODOS' || $valorNorm === 'TODAS'));

        if ($esTodos) {
            $qestruc = "(Select * from carobra_2014.estruc_empleados)";
            /* $q_emp = 1, $w_nap = '', $cacheWhere = '' -> sin filtro estructural */
        } elseif ($filtro == "Soc. Comercial") {
            $valor1Esc = mysqli_real_escape_string($conecta->conexion, $valor1);
            $qestruc = "(Select * from carobra_2014.estruc_empleados where sc='$valor1Esc')";
            $cacheWhere = " AND c.sc='$valor1Esc'";
        } elseif ($filtro == "SubBrocker") {
            $valor1Esc = mysqli_real_escape_string($conecta->conexion, $valor1);
            $qestruc = "(Select * from carobra_2014.estruc_empleados where brocker='$valor1Esc')";
            $cacheWhere = " AND c.brocker='$valor1Esc'";
        } elseif ($filtro == "Gerencia") {
            if (true) {
                $valor1Esc = mysqli_real_escape_string($conecta->conexion, $valor1);
                $qestruc = "(Select * from carobra_2014.estruc_empleados where gerencia='$valor1Esc')";
                $cacheWhere = " AND c.gerencia='$valor1Esc'";
            } else {
                $qestruc = "(Select * from carobra_2014.estruc_empleados)";
            }
        } elseif ($filtro == "NAP") {
            $partes = explode("(", $valor1);
            $nap = str_replace(")", "", $partes[1] ?? $valor1);
            $nap = trim($nap);
            $napEsc = mysqli_real_escape_string($conecta->conexion, $nap);
            $qestruc = "(Select * from carobra_2014.estruc_empleados)";
            $w_nap = " and p.nap='$napEsc'";
            $cacheWhere = " AND c.nap='$napEsc'";
        } else { // Comisionista
            $q_emp = 0;
            $nombreEmp = ($valor1 == "Usuario de prueba") ? "MARIA MAYRA SANTOS CORTES" : $valor1;
            $nombreEmpEsc = mysqli_real_escape_string($conecta->conexion, utf8_decode($nombreEmp));
            $query = "Select no_empleado from empl_usuarios where nombre='$nombreEmpEsc'";
            $bd = $conecta->consulta($query);
            $tb = $bd ? mysqli_fetch_array($bd) : null;
            $noEmp = intval($tb[0] ?? 0);
            $qestruc = "(Select * from carobra_2014.estruc_empleados where no_empleado=$noEmp)";
            $cacheWhere = " AND c.no_empleado=$noEmp AND $noEmp>0";
        }
        /* con "saldo real > 0" el cruce original NO usa el respaldo por NAP
           (p.no_empleado=ee.no_empleado) -> excluir los folios con no_empleado=0 */
        if ($q_emp == 1 && $srm0 == 1) { $cacheWhere .= " AND c.no_empleado<>0"; }

        /* ── ⚡ Ruta rápida: caché precalculada (sin tablas temporales ni UPDATEs) ── */
        if (!$modoCache && !empty($valor1)) {
            $iso = function ($d) { return substr($d, -4) . '-' . substr($d, 3, 2) . '-' . substr($d, 0, 2); };
            $iniY = $iso($fec_inc); $finY = $iso($fec_fin);
            if (svCacheUsable($conecta, $iniY, $finY, $enVivo)) {
                $limInt = max(1, intval($limite));
                $filasC = svCacheConsultar($conecta, [
                    'fec_ini' => $iniY, 'fec_fin' => $finY, 'reg_esp' => $reg_esp,
                    'sfp' => $sfp, 'srm0' => $srm0, 'where' => $cacheWhere,
                ], $limInt);
                if ($filasC !== null) {
                    $est = svCacheEstado($conecta);
                    $resultado['filas']    = $filasC;
                    $resultado['limitado'] = (count($filasC) >= $limInt);
                    $resultado['origen']   = 'cache';
                    $resultado['cache_at'] = $est['refreshed_at'] ?? '';
                    if (empty($filasC)) $resultado['msg'] = 'Sin resultados para estos filtros.';
                    svCacheProgramarRefresco($conecta);
                    return $resultado;
                }
            }
        }
    }

    /* ── Refrescar la tabla de apoyo estruc_empleados (misma lógica) ──
       sc/brocker/id_gerencia/gerencia NO son columnas de produccion --
       viven en estruc_empleados. Los modos estructurales ya la
       necesitaban para el filtro; Folio también la necesita (aunque
       no filtra por ella) porque el SELECT final las lee igual. Por
       eso este refresh corre siempre, no solo cuando hay filtro
       estructural. */
    refrescarEstrucEmpleados($conecta);

    if (empty($valor1)) {
        $resultado['ok']  = false;
        $resultado['msg'] = $esFolio ? 'Falta el folio a buscar.' : 'Falta el valor a buscar (' . $filtro . ').';
        return $resultado;
    }

    /* ── ⚡ Folio: ruta rápida contra la caché (si el folio está en su ventana) ── */
    if ($esFolio && !$modoCache && svCacheUsable($conecta, null, null, $enVivo)) {
        $condC = condicionFolioParaNivel($conecta, $nivel, $nombreUsuario);
        $condC = str_replace(['p.no_empleado', 'ee.'], ['c.no_empleado', 'c.'], $condC);
        $filasC = svCacheConsultar($conecta, ['folio' => trim($valor1), 'cond_folio' => $condC], max(1, intval($limite)));
        if (!empty($filasC)) {
            $est = svCacheEstado($conecta);
            $resultado['filas']    = $filasC;
            $resultado['limitado'] = (count($filasC) >= max(1, intval($limite)));
            $resultado['origen']   = 'cache';
            $resultado['cache_at'] = $est['refreshed_at'] ?? '';
            svCacheProgramarRefresco($conecta);
            return $resultado;
        }
    }

    /* ── Tabla temporal de trabajo (nombre único por consulta) ── */
    $sufijo = date("dmYGis") . '_' . mt_rand(1000, 9999);
    $qsv = "w_rep_" . preg_replace('/[^A-Za-z0-9_]/', '', $clave) . "_" . $sufijo;
    $conecta->consulta("Create TABLE $qsv LIKE sv2");

    $ttProd = '';

    if ($esFolio) {

        /* Búsqueda puntual por folio: sin rango de fechas ni filtro
           estructural, solo número de folio (LIKE para tolerar que el
           usuario no lo escriba completo). LEFT JOIN a estruc_empleados,
           naps y calendarios para no perder el folio si falta alguno.
           Mismo layout de columnas que sv2 (ver seg_ventas.php). */
        $folioEsc = mysqli_real_escape_string($conecta->conexion, trim($valor1));

        /* Seguridad: ver condicionFolioParaNivel() -- admin sin
           restricción; otros niveles solo su propia rama. */
        $condicionFolio = condicionFolioParaNivel($conecta, $nivel, $nombreUsuario);

        $query = "Insert into carobra_2014.$qsv
                    Select p.folio,'','','','','',p.id_estatus_prod,p.id_estatus_prod,p.estatus_confirma,p.cierre_cap,0,
                           0,p.id_tipo_mov,p.nss,'',p.nap,'','',p.afo_cedente,'','',
                           '',ee.sc,ee.brocker,ee.id_gerencia,ee.gerencia,
                           p.id_usuario_cap,'',fc.campana,p.no_empleado,'','',
                           p.id_estatus_anticipo,p.monto_anticipo,0,
                           p.saldo_estimado,0,0,if(f.mes <=12,m.mes_largo,f.nom_periodo),p.fec_captura,p.fec_pago_ant,
                           '',p.fec_traspaso,p.fec_cierre,'','',
                           p.fec_ult_estatus,'','','',0,''
                    FROM carobra_2014.produccion p
                        LEFT JOIN carobra_2014.naps n on (p.nap=n.nap)
                        LEFT JOIN carobra_2014.estruc_empleados ee
                                on (if(p.no_empleado=0,n.no_empleado,p.no_empleado)=ee.no_empleado)
                        LEFT JOIN fec_cierre_campana fc
                                on (str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date(fc.fec_ini,'%d/%m/%Y')
                                    and str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date(fc.fec_fin,'%d/%m/%Y'))
                        LEFT JOIN fechas_cierre_com f
                                on (str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date(f.fec_ini,'%d/%m/%Y')
                                    and str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date(f.fec_fin,'%d/%m/%Y'))
                        LEFT JOIN meses m on (f.mes=m.no_mes)
                    WHERE p.folio LIKE '%$folioEsc%' $condicionFolio";

    } else {

        /* Igual que seg_ventas.php (versión optimizada): primero una
           tabla temporal con los folios del rango (indexada), luego un
           solo INSERT con CTEs de estructura/campaña/mes -- ya no hay
           UNION ALL ni UPDATEs de nom_mes/campana. */
        $w_query = " where p.fec_traspaso REGEXP '^[0-9]{2}/[0-9]{2}/[0-9]{4}$'
                        AND str_to_date(p.fec_traspaso,'%d/%m/%Y') >= str_to_date('$fec_inc','%d/%m/%Y')
                        AND str_to_date(p.fec_traspaso,'%d/%m/%Y') <= str_to_date('$fec_fin','%d/%m/%Y')
                        $w_nap";
        if ($reg_esp != 2) {
            $w_query .= ($reg_esp == 1) ? " and length(p.folio) < 10 " : " and length(p.folio) >= 10 ";
        }

        if ($srm0 == 0 && $q_emp == 1) {
            $qcruce = "if(p.no_empleado=0,n.no_empleado,p.no_empleado)=ee.no_empleado";
        } else {
            $qcruce = "p.no_empleado=ee.no_empleado";
        }

        $ttProd = "w_rep_prod_" . preg_replace('/[^A-Za-z0-9_]/', '', $clave) . "_" . $sufijo;
        $conecta->consulta("Create TABLE $ttProd as
                            Select p.*,STR_TO_DATE(fec_traspaso, '%d/%m/%Y') as fec_traspaso_date
                            FROM carobra_2014.produccion p $w_query");
        $conecta->consulta("Alter TABLE $ttProd
                              ADD INDEX idx_nap (nap),
                              ADD INDEX idx_no_empleado (no_empleado),
                              ADD INDEX idx_fec_traspaso (fec_traspaso_date)");

        $query = "Insert into carobra_2014.$qsv
                    with
                        estruc as $qestruc,
                        fec_cierre as (Select campana,STR_TO_DATE(fec_ini, '%d/%m/%Y') as fec_ini,
                                              STR_TO_DATE(fec_fin, '%d/%m/%Y') as fec_fin
                                       from fec_cierre_campana),
                        meses as (Select if(fc.mes <=12,m.mes_largo,fc.nom_periodo) as nom_mes,
                                         STR_TO_DATE(fec_ini, '%d/%m/%Y') as fec_ini,
                                         STR_TO_DATE(fec_fin, '%d/%m/%Y') as fec_fin
                                  from fechas_cierre_com fc
                                        left join meses m on (fc.mes=m.no_mes))
                    Select p.folio,'','','','','',p.id_estatus_prod,p.id_estatus_prod,p.estatus_confirma,p.cierre_cap,0,0,p.id_tipo_mov,p.nss,
                           '',p.nap,'','',p.afo_cedente,'','','',ee.sc,ee.brocker,ee.id_gerencia,ee.gerencia,p.id_usuario_cap,'',ifnull(fc.campana,''),
                           p.no_empleado,'','',p.id_estatus_anticipo,p.monto_anticipo,0,p.saldo_estimado,0,0,ifnull(m.nom_mes,''),p.fec_captura,
                           p.fec_pago_ant,'',p.fec_traspaso,p.fec_cierre,'','',p.fec_ult_estatus,'','','',0,''
                    from $ttProd p
                        join carobra_2014.naps n on (p.nap=n.nap)
                        join estruc ee on ($qcruce)
                        left join fec_cierre fc on (p.fec_traspaso_date BETWEEN fc.fec_ini and fc.fec_fin)
                        left join meses m on (p.fec_traspaso_date BETWEEN m.fec_ini and m.fec_fin)";

        $w2 = "";
        if ($sfp == 1) {
            $query .= " left join carobra_2014.calculo_pago cp on (p.folio=cp.folio) ";
            $w2 = " where ifnull(cp.fec_pago,'')=''";
            if ($srm0 == 1) {
                $query .= " left join carobra_2014.pagos pa on (p.folio=pa.folio) ";
                $w2 .= " and ifnull(pa.saldo_real,0)!=0";
            }
        } else if ($srm0 == 1) {
            $query .= " left join carobra_2014.pagos pa on (p.folio=pa.folio) ";
            $w2 = " where ifnull(pa.saldo_real,0)!=0";
        }
        $query .= $w2;
    }

    $conecta->consulta($query);
    $errInsert = mysqli_error($conecta->conexion);
    if ($ttProd !== '') { $conecta->consulta("Drop TABLE IF EXISTS $ttProd"); }

    $bd = $conecta->consulta("Select 1 from carobra_2014.$qsv");
    if (!$bd || $bd->num_rows == 0) {
        $conecta->consulta("DROP TABLE $qsv");
        $resultado['ok']  = true;
        $resultado['msg'] = 'Sin resultados para estos filtros.';
        /* Si el INSERT de la tabla temporal falló de plano (error de SQL,
           no "cero filas" legítimo), lo dejamos ver para poder diagnosticar
           -- si no, "Sin resultados" es indistinguible de un query roto. */
        if ($errInsert !== '') {
            $resultado['msg']   = 'Error al armar el reporte: ' . $errInsert;
            $resultado['debug_sql'] = $query;
        }
        return $resultado;
    }

    /* Para folio, anio_ini/anio_fin nunca se resolvieron (no hubo rango
       de fechas) -- se sacan del propio folio ya insertado, para que
       el enriquecimiento de saldo_pago (tabla acum_det_prod_<año>)
       apunte al año real del folio y no a un valor por default. */
    if ($esFolio) {
        $bdFecha = $conecta->consulta("Select fec_traspaso from carobra_2014.$qsv limit 1");
        $rowFecha = $bdFecha ? mysqli_fetch_assoc($bdFecha) : null;
        $anioFolio = ($rowFecha && strlen($rowFecha['fec_traspaso']) === 10) ? substr($rowFecha['fec_traspaso'], -4) : date('Y');
        $anio_ini = $anioFolio;
        $anio_fin = $anioFolio;
    }

    $t_pt  = "prod_trabajador";
    $t_cp  = "calculo_pago";
    $t_ap2 = "";
    if ($anio_ini == $anio_fin) {
        $t_ap = "carobra.acum_det_prod_" . $anio_fin;
    } else {
        $t_ap  = "carobra.acum_det_prod_" . $anio_ini;
        $t_ap2 = "carobra.acum_det_prod_" . $anio_fin;
    }

    /* ── Enriquecer (mismas ~15 actualizaciones que seg_ventas.php) ── */
    $updates   = [];
    $updates[] = "Update $qsv s, $t_pt pt
                  set s.trabajador=pt.trabajador,s.telefono=pt.telefono,
                      s.celular=pt.celular,s.cp=pt.cp,
                      s.email_trab=pt.email_trab,s.curp=pt.CURP,
                      s.no_oportunidad=pt.no_oportunidad,
                      s.observa=pt.observa
                  where s.folio=pt.folio";

    $updates[] = "Update $qsv
                  set edad=TIMESTAMPDIFF(YEAR,STR_TO_DATE(CONCAT(
                            CASE
                              WHEN SUBSTRING(curp,5,2) <= DATE_FORMAT(STR_TO_DATE(fec_traspaso,'%d/%m/%Y'), '%y')
                                THEN CONCAT('20', SUBSTRING(curp,5,2))
                              ELSE CONCAT('19', SUBSTRING(curp,5,2))
                            END,SUBSTRING(curp,7,4)),'%Y%m%d'),STR_TO_DATE(fec_traspaso,'%d/%m/%Y'))
                  where length(curp)=18 and length(fec_traspaso)=10";

    $updates[] = "Update $qsv s, empl_usuarios eu
                  set s.clave=eu.clave, s.nombre=eu.nombre
                  where s.no_empleado > 0 and s.no_empleado=eu.no_empleado";

    $updates[] = "Update $qsv s, prod_rechazos pr
                  set s.id_rechazo=pr.id_rechazo, s.id_tipo_rechazo=pr.id_tipo_rechazo
                  where s.folio=pr.folio";

    $updates[] = "Update $qsv s, segundo_anticipo sa
                  set s.monto_seg_ant=sa.monto, s.fec_pago_ant2=sa.fec_pago
                  where s.folio=sa.folio and length(s.fec_pago_ant2)=0";

    $updates[] = "Update $qsv s, pagos pa
                  set s.saldo_real=pa.saldo_real
                  where s.folio=pa.folio";

    $updates[] = "Update $qsv s, $t_cp cp
                  set s.fec_rechazo=cp.fec_pago
                  where cp.rechazo=1 and s.folio=cp.folio";

    $updates[] = "Update $qsv s, $t_cp cp
                  set s.fec_pago=cp.fec_pago
                  where cp.rechazo=0 and s.folio=cp.folio";

    $updates[] = "Update $qsv s
                    JOIN (select folio_pr, max(cast(trim(ap.folio_av) as DOUBLE)) as folio_av
                          from apovol_prod ap
                                inner join $qsv s
                                        on (ap.folio_pr=s.folio)
                          where trim(ap.folio_av) regexp '^[0-9]+$'
                          group by folio_pr) av on (s.folio=av.folio_pr)
                    JOIN av_pagos avp on (av.folio_av=avp.tramite)
                  SET s.folio_av=avp.folio, s.estatus_av=avp.estatus,
                      s.frecuencia=avp.frecuencia
                  WHERE avp.tramite regexp '^[0-9]+$'";

    $updates[] = "Update $qsv s, av_fechas avf
                  set s.fec_envio=avf.fec_envio
                  where s.folio_av !='' and s.folio_av=avf.folio";

    $updates[] = "Update carobra_2014.$qsv s
                    join carobra_2014.clawback c on (s.folio=c.folio)
                  set s.transfer_out='Baja del cliente'
                  where cancelacion=0";

    $updates[] = "Update carobra_2014.$qsv s, $t_ap ap
                  set s.saldo_pago=ap.saldo_real
                  where ap.rechazo=0 and ap.com_asesor+ap.com_gerente+ap.com_brocker+ap.com_sc > 0
                      and s.folio=ap.folio";

    $updates[] = "Update carobra_2014.$qsv s, carobra_pagos.desc_incumplimiento d
                  set s.fec_desc=d.fec_descuento
                  where s.folio=d.folio";

    if ($t_ap2 != "") {
        $updates[] = "Update carobra_2014.$qsv s, $t_ap2 ap
                      set s.saldo_pago=ap.saldo_real
                      where ap.rechazo=0 and ap.com_asesor+ap.com_gerente+ap.com_brocker+ap.com_sc > 0
                          and s.folio=ap.folio";
    }

    foreach ($updates as $u) {
        $conecta->consulta($u);
    }

    /* ── SELECT final (mismas columnas que seg_ventas.php, ya con
       nombres que calzan 1:1 con las keys de $CAMPOS_REPORTE) ── */
    $limiteInt = max(1, intval($limite));
    $extraCache = $modoCache ? ", STR_TO_DATE(s.fec_traspaso,'%d/%m/%Y') as fec_traspaso_date" : "";
    $query = "Select distinct
                   concat(if(id_tipo_mov=1,'Traspaso','Registro'),' ',if(ifnull(n.nap,'')='','NAE','NAP')) as tipo_prod,
                   s.folio,nss,trabajador,s.nombre,s.clave,s.no_empleado,s.telefono,s.celular,s.cp,s.email_trab,
                   saldo_estimado,saldo_real,nom_mes as mes,
                   if(id_estatus_anticipo=1,'Solicitado',if(id_estatus_anticipo=2,'Pagado','')) as est_anticipo,
                   monto_anticipo,monto_seg_ant,esf.estatus,s.sc,s.brocker,s.gerencia,s.nap,
                   if(id_tipo_mov=1,'Traspaso','Registro') as movimiento,fec_pago_ant2 as fec_ant2,s.fec_traspaso,
                   s.fec_captura,fec_pago_ant as fec_anticipo,fec_pago,trec.tipo_rechazo,af.afore,r.rechazo,
                   curp,eu.nombre as analista,if(cierre_cap=0,'No','Si') as cierre_cap,observa,fec_rechazo,descrip,
                   fec_cierre,folio_av,estatus_av,fec_envio,no_oportunidad,'' as autoriza_sin_av,transfer_out,
                   esf.estatus as estatus_pbi,campana,
                   if(confirma='','',if(substring(confirma,-10)='Confirmado',confirma,concat('En proceso (',confirma,')'))) as confirma,
                   saldo_pago,frecuencia,edad,fec_desc,m.fec_sello,
                   case when m.movilidad=1 then 'Igual o mayor a 18 meses'
                        else if(ifnull(m.folio,0)=0,'Sin estatus movilidad','Menos de 18 meses')
                   end as movilidad,m.fec_captura as cap_movilidad,
                   if(m.pplus='','',concat('Cliente ',m.pplus)) as pplus,
                   m.ley,m.categoria_cliente,
                   case when m.cotiza=1 then 'SI'
                        when m.cotiza=0 then 'NO'
                        else ''
                   end as cotiza,
                   /* Nivel del comisionista dentro de la estructura (mismo resultado que el
                      reporte viejo): SC / SubBroker / Gerente / Asesor; vacío si el folio no
                      trae empleado. */
                   case when s.no_empleado=0 or ifnull(s.nombre,'')='' then ''
                        when s.nombre=s.sc then 'Socio Comercial'
                        when s.nombre=s.brocker then 'SubBroker'
                        when ifnull(s.gerencia,'')<>'' and s.nombre like concat(s.gerencia,'%') then 'Gerente'
                        else 'Asesor'
                   end as nivel,
                   case ce.ce_categoria
                        when 1 then 'Comisionista'
                        when 2 then 'Gerente Comisionista'
                        when 3 then 'Gerente Premium'
                        when 4 then 'Gerente Master'
                        when 5 then 'Gerente Elite'
                        else ''
                   end as categoria,
                   /* Venta propia = el folio es del dueño del NAP (o no trae empleado y se
                      resolvió por NAP); referida = capturado con empleado distinto al del NAP. */
                   case when s.no_empleado=0 or s.no_empleado=ifnull(n.no_empleado,0) then 'Venta propia'
                        else 'Venta referida'
                   end as tipo_venta,
                   '' as fec_ult_traspaso,
                   '' as estatus_pago $extraCache
              from carobra_2014.$qsv s
                left join carobra_2014.naps n on (s.nap=n.nap)
                left join (Select no_empleado as ce_no_empleado, max(categoria) as ce_categoria
                           from carobra.empleados group by no_empleado) ce
                       on (s.no_empleado>0 and s.no_empleado=ce.ce_no_empleado)
                left join carobra_2014.estatus esf on (s.id_estatus_prod=esf.id_estatus)
                left join tipo_rechazo trec on (s.id_tipo_rechazo=trec.id_tipo)
                left join afores af on (s.afo_cedente=af.clave)
                left join rechazos r on (s.id_rechazo=r.id_rechazo)
                left join empl_usuarios eu on (s.id_usuario_cap=eu.no_empleado)
                left join movilidad m on (s.folio=m.folio)";

    if ($modoCache) {
        global $SV_CACHE_COLS;
        $conecta->consulta("Insert into carobra_2014.sv_cache_new (" . implode(',', $SV_CACHE_COLS) . ",fec_traspaso_date) " . $query);
        $conecta->consulta("DROP TABLE $qsv");
        $resultado['msg'] = mysqli_error($conecta->conexion);
        $resultado['ok']  = ($resultado['msg'] === '');
        return $resultado;
    }

    $query .= " LIMIT $limiteInt";
    $bd = $conecta->consulta($query);
    $filas = [];
    if ($bd) {
        while ($tb = mysqli_fetch_assoc($bd)) {
            $filas[] = $tb;
        }
    }

    $conecta->consulta("DROP TABLE $qsv");

    $resultado['filas']    = $filas;
    $resultado['limitado'] = (count($filas) >= $limiteInt);

    svCacheProgramarRefresco($conecta);

    return $resultado;
}

require_once(__DIR__ . '/sv_cache_funciones.php');