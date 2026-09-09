SELECT no_empleado, clave, nombre, fec_ingreso, fec_reingreso, fec_baja,
       rfc, curp, nss, nap, domicilio, telefono, celular, email, email_inst,
       categoria, regimen_fiscal, factura, fec_factura, sc, brocker, gerencia,
       ase_bernal, costo_ase_bernal, activo, estatus_siap, staff, lista_negra,
       cent_cost, no_contpaq, reclutador, cobro, jefe_inmediato, observa
FROM (
    Select e.no_empleado as no_empleado, u.clave as clave, u.nombre as nombre,
           fec_ingreso as fec_ingreso, fec_reingreso as fec_reingreso, fec_baja as fec_baja,
           rfc as rfc, curp as curp, nss as nss, nap as nap,
           domicilio as domicilio, telefono as telefono, celular as celular,
           u.email as email, u.email_inst as email_inst,
           case when categoria=0 then ''
                when categoria=1 then 'Comisionista'
                when categoria=2 then 'Gerente Comisionista'
                when categoria=3 then 'Gerente Premium'
                when categoria=4 then 'Gerente Master'
                when categoria=5 then 'Gerente Elite'
           end as categoria,
           case when regimen_fiscal='PM' then 'Persona Moral'
                when regimen_fiscal='AE' then 'Actividad Empresarial'
                when regimen_fiscal='IF' then 'Incorporacion Fiscal'
                when regimen_fiscal='R' then 'Resico'
                else ''
           end as regimen_fiscal,
           if(factura=1,'SI','NO') as factura,
           fec_factura as fec_factura,
           est_u.sc as sc, est_u.brocker as brocker, est_u.gerencia as gerencia,
           if(ase_bernal=1,'SI','NO') as ase_bernal,
           costo_a as costo_ase_bernal,
           if(u.activo=1,'SI','NO') as activo,
           if(ifnull(nap,'')='','',if(n.baja=0,'SI','NO')) as estatus_siap,
           if(staff=1,'SI','NO') as staff,
           if(lista_negra=0,'No','Si') as lista_negra,
           if(cen_cost='','',cc.nombre_cc) as cent_cost,
           no_contpaq as no_contpaq,
           u_recluta.nombre as reclutador,
           /*CAMPO:cobro*/ifnull(cobro_acum.cobro,0) as cobro,/*FINCAMPO:cobro*/
           u_jefe.nombre as jefe_inmediato,
           eo.observa as observa
    from carobra.usuarios u
        inner join carobra.empleados e on (u.clave=e.clave)
        left join carobra_2014.estruc_empleados est_u on (u.clave=est_u.clave)
        left join carobra_2014.naps n on (e.no_empleado=n.no_empleado)
        left join carobra.usuarios u_recluta on (e.recluta=u_recluta.clave)
        left join carobra.usuarios u_jefe on (e.jefe_inmediato=u_jefe.clave)
        left join carobra_2014.empl_observa eo on (e.no_empleado=eo.no_empleado)
        left join carobra_2014.centros_costo_area cc on (e.cen_cost=cc.id_cc)
        /*JOIN:cobro*/
        left join (SELECT clave, SUM(importe) AS cobro
                   FROM sferea.acumulado_pagos
                   WHERE STR_TO_DATE(fec_pago, '%d/%m/%Y') >= '{{FECHA_INICIO}}'
                   AND STR_TO_DATE(fec_pago, '%d/%m/%Y') <= '{{FECHA_FIN}}'
                   GROUP BY clave
                   HAVING SUM(importe) {{SIMBOLO}}) as cobro_acum on (u.clave=cobro_acum.clave)
        /*FINJOIN:cobro*/
) base


-- Marcar el campo opcional en el SELECT interno
/*CAMPO:alias*/expresion as alias,/*FINCAMPO:alias*/

-- Marcar el JOIN opcional
/*JOIN:alias*/
left join (...) as alias_acum on (...)
/*FINJOIN:alias*/