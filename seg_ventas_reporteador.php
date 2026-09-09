<?php
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

		<!-- compiled function autocomplete -->
		<script src="./twitter/typeahead.bundle.js"></script>

		<!-- compiled function datepicker -->
		<link rel="stylesheet" href="./css/datepicker.css">
		<script src="./js/bootstrap-datepicker.js" charset="UTF-8"></script>

		<!-- SweetAlert2: misma versión/CDN que ya usa el resto del sitio (ej. seg_kpis.php) -->
		<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

		<link href="./css/carobra.css" rel="stylesheet" type="text/css"/>

		<style type="text/css">

			/* Todo lo de abajo va scopeado a clases/IDs propios (rv-*, mc-*,
			   mf-*) — nada de tocar body{}, *{} ni .container{} aquí. La
			   plantilla en blanco del sitio no trae ningún reset global y
			   se ve perfecta, así que este archivo tampoco lo necesita. */

			.rv-page{
				width:100%;
				max-width:1100px;
			}

			.rv-grid{
				display:grid;
				grid-template-columns:repeat(auto-fill, minmax(240px, 1fr));
				gap:16px;
			}

			.rv-card{
				position:relative;
				background:white;
				border-radius:18px;
				padding:26px 22px;
				text-align:center;
				cursor:pointer;
				box-shadow:0 4px 16px rgba(15,23,42,.06);
				border:1px solid #eef2ff;
				transition:.15s;
			}

			.rv-card:hover{
				transform:translateY(-2px);
				box-shadow:0 10px 24px rgba(15,23,42,.1);
				border-color:#bfdbfe;
			}

			.rv-card h3{
				font-size:16px;
				font-weight:700;
				color:#0f172a;
				margin-bottom:8px;
			}

			.rv-card p{
				color:#94a3b8;
				font-size:13px;
			}

			.rv-card-nuevo{
				background:#eff6ff;
				border:2px dashed #93c5fd;
			}

			.rv-card-nuevo:hover{
				background:#e0f2fe;
				border-color:#0177cb;
			}

			.rv-card-nuevo .rv-card-plus{
				font-size:30px;
				font-weight:700;
				color:#0177cb;
				line-height:1;
				margin-bottom:8px;
			}

			.rv-card-nuevo h3{ color:#0177cb; }

			/* "Buscar por folio" usa el mismo azul que "+ Nuevo reporte"
			   (antes tenía una variante violeta para distinguirla, pero
			   se pidió dejar todo en la misma paleta azul del sitio) --
			   .rv-card-folio ya no necesita overrides de color, hereda
			   todo de .rv-card-nuevo. */

			.rv-card-borrar{
				position:absolute;
				top:10px;
				right:10px;
				width:24px;
				height:24px;
				border:none;
				border-radius:8px;
				background:#fee2e2;
				color:#ef4444;
				font-size:12px;
				cursor:pointer;
				display:none;
				align-items:center;
				justify-content:center;
			}

			.rv-card:hover .rv-card-borrar{ display:flex; }

			.rv-card-borrar:hover{ background:#ef4444; color:white; }

			/* ============================================
			MODAL FULLSCREEN CONSTRUCTOR
			============================================ */

			#modalConstructor{
				position:fixed;
				inset:0;
				background:rgba(15,23,42,.7);
				z-index:999999;
				display:none;
				justify-content:center;
				align-items:center;
				padding:20px;
				backdrop-filter:blur(6px);
			}

			.mc-content{
				width:100%;
				max-width:1400px;
				height:90vh;
				background:white;
				border-radius:24px;
				display:flex;
				flex-direction:column;
				overflow:hidden;
				animation:modalIn .2s ease;
			}

			@keyframes modalIn{
				from{ transform:translateY(16px) scale(.98); opacity:0; }
				to{   transform:translateY(0)    scale(1);   opacity:1; }
			}

			.mc-header{
				padding:18px 24px;
				border-bottom:1px solid #e2e8f0;
				display:flex;
				align-items:center;
				gap:14px;
				flex-shrink:0;
				background:white;
			}

			.mc-header h2{
				font-size:18px;
				font-weight:700;
				color:#0f172a;
				flex:1;
			}

			.mc-btn-close{
				width:34px;
				height:34px;
				border:none;
				border-radius:10px;
				background:#f1f5f9;
				color:#64748b;
				font-size:16px;
				cursor:pointer;
				display:flex;
				align-items:center;
				justify-content:center;
				flex-shrink:0;
				transition:.15s;
			}

			.mc-btn-close:hover{ background:#e2e8f0; color:#0f172a; }

			.mc-body{
				flex:1;
				display:flex;
				overflow:hidden;
			}

			/* IZQUIERDO: CAMPOS */

			.mc-campos{
				width:280px;
				flex-shrink:0;
				border-right:1px solid #e2e8f0;
				display:flex;
				flex-direction:column;
				overflow:hidden;
			}

			.mc-campos-buscar{
				padding:12px;
				border-bottom:1px solid #f1f5f9;
				flex-shrink:0;
			}

			.mc-campos-buscar input{
				width:100%;
				border:1.5px solid #e2e8f0;
				border-radius:10px;
				padding:8px 12px;
				font-size:13px;
				outline:none;
				transition:border-color .2s;
			}

			.mc-campos-buscar input:focus{ border-color:#0177cb; }

			.mc-campos-list{
				flex:1;
				overflow-y:auto;
				padding:8px 10px;
			}

			.mc-campos-group-title{
				font-size:11px;
				font-weight:700;
				color:#64748b;
				text-transform:uppercase;
				letter-spacing:.06em;
				padding:10px 8px;
				margin-top:4px;
				display:flex;
				align-items:center;
				gap:6px;
				cursor:pointer;
				user-select:none;
				border-radius:8px;
				transition:.15s;
			}

			.mc-campos-group-title:hover{ background:#f1f5f9; color:#0177cb; }

			.mc-campos-group-title .grupo-icono{ font-size:13px; flex-shrink:0; }

			.mc-campos-group-title .grupo-nombre{ flex:1; }

			.mc-campos-group-title .grupo-count{
				font-size:10px;
				font-weight:700;
				color:#94a3b8;
				background:#f1f5f9;
				border-radius:999px;
				padding:1px 7px;
				text-transform:none;
				letter-spacing:0;
			}

			.mc-campos-group-title .chevron{
				font-size:9px;
				color:#94a3b8;
				transition:transform .15s;
				flex-shrink:0;
			}

			.mc-campos-group-title.colapsado .chevron{ transform:rotate(-90deg); }

			.mc-campos-group-body{
				overflow:hidden;
				padding-left:6px;
				border-left:2px solid #f1f5f9;
				margin-left:14px;
			}

			.mc-campos-group-body.colapsado{ display:none; }

			.mc-campo-item{
				display:flex;
				align-items:center;
				gap:8px;
				padding:8px 10px;
				border-radius:10px;
				background:#f8fafc;
				border:1px solid #e2e8f0;
				cursor:grab;
				transition:.15s;
				font-size:13px;
				color:#0f172a;
				font-weight:500;
				user-select:none;
				margin:0 0 6px 4px;
			}

			.mc-campo-item:hover{ background:#eff6ff; border-color:#bfdbfe; color:#0177cb; }
			.mc-campo-item.activo{ background:#e0f2fe; border-color:#0177cb; color:#0177cb; }

			.mc-campo-item .drag-icon{ color:#cbd5e1; font-size:12px; flex-shrink:0; }

			/* DERECHO: CONSTRUCTOR / RESULTADOS */

			.mc-derecho{
				flex:1;
				display:flex;
				flex-direction:column;
				overflow:hidden;
			}

			.mc-derecho-title{
				padding:14px 16px;
				font-size:11px;
				font-weight:700;
				color:#94a3b8;
				text-transform:uppercase;
				letter-spacing:.06em;
				border-bottom:1px solid #f1f5f9;
				flex-shrink:0;
				display:flex;
				align-items:center;
				justify-content:space-between;
			}

			.mc-derecho-title button{
				background:#f1f5f9;
				border:none;
				padding:5px 12px;
				border-radius:8px;
				cursor:pointer;
				font-size:11px;
				font-weight:600;
				color:#64748b;
				text-transform:none;
				letter-spacing:0;
			}

			.mc-derecho-title button:hover{ background:#e2e8f0; }

			.mc-dropzone{
				flex:1;
				overflow:auto;
				padding:16px;
				transition:background .2s;
			}

			.mc-dropzone.drag-over{ background:#eff6ff; }

			.mc-tabla{
				width:100%;
				border-collapse:collapse;
				font-size:13px;
			}

			.mc-tabla th{
				background:#f1f5f9;
				border:1px solid #e2e8f0;
				padding:12px 14px;
				min-width:150px;
				cursor:move;
				white-space:nowrap;
				text-align:left;
			}

			.mc-tabla td{
				border:1px solid #e2e8f0;
				padding:9px 14px;
				background:white;
				white-space:nowrap;
			}

			.mc-placeholder{
				height:100%;
				display:flex;
				flex-direction:column;
				align-items:center;
				justify-content:center;
				gap:10px;
				color:#94a3b8;
				font-size:14px;
				text-align:center;
				padding:20px;
			}

			.mc-placeholder span{ font-size:40px; }

			.col-header{
				display:flex;
				align-items:center;
				justify-content:space-between;
				gap:10px;
			}

			.col-header label{ font-size:13px; font-weight:600; color:#0f172a; }

			.btn-del-col{
				background:#fee2e2;
				color:#ef4444;
				border:none;
				width:22px;
				height:22px;
				border-radius:6px;
				cursor:pointer;
				font-size:12px;
				flex-shrink:0;
				transition:.15s;
			}

			.btn-del-col:hover{ background:#ef4444; color:white; }

			.mc-footer{
				padding:14px 20px;
				border-top:1px solid #e2e8f0;
				display:flex;
				gap:10px;
				justify-content:flex-end;
				flex-shrink:0;
				background:#f8fafc;
			}

			.mc-footer button{
				border:none;
				padding:11px 20px;
				border-radius:12px;
				cursor:pointer;
				font-size:13px;
				font-weight:600;
				transition:.2s;
			}

			.mc-footer button:hover{ transform:translateY(-1px); }

			#mcBtnGenerar{ background:#0177cb; color:white; }
			#mcBtnExportar{ background:#015a9a; color:white; display:none; }
			#mcBtnLimpiar{ background:#f1f5f9; color:#64748b; }
			#mcBtnGuardar{ background:#eff6ff; color:#0177cb; }

			.mc-resumen{
				font-size:12px;
				color:#64748b;
				padding:8px 16px 0;
			}

			/* ============================================
			MODAL FILTROS
			============================================ */

			#modalFiltros, #modalPermisos{
				position:fixed;
				inset:0;
				background:rgba(15,23,42,.75);
				z-index:9999999;
				display:none;
				justify-content:center;
				align-items:center;
				padding:1.5rem;
				backdrop-filter:blur(6px);
			}

			.mf-content{
				width:100%;
				max-width:640px;
				max-height:90vh;
				background:white;
				border-radius:24px;
				display:flex;
				flex-direction:column;
				overflow:hidden;
				animation:modalIn .2s ease;
			}

			.mf-header{
				padding:20px 24px;
				border-bottom:1px solid #e2e8f0;
				display:flex;
				justify-content:space-between;
				align-items:center;
				flex-shrink:0;
			}

			.mf-header h2{ font-size:18px; font-weight:700; color:#0f172a; }

			.mf-body{
				flex:1;
				overflow-y:auto;
				padding:20px 24px;
				display:flex;
				flex-direction:column;
				gap:18px;
			}

			.mf-footer{
				padding:14px 24px;
				border-top:1px solid #e2e8f0;
				display:flex;
				gap:10px;
				justify-content:flex-end;
				background:#f8fafc;
				flex-shrink:0;
			}

			.mf-footer button{
				border:none;
				padding:11px 22px;
				border-radius:12px;
				cursor:pointer;
				font-size:13px;
				font-weight:600;
				transition:.2s;
			}

			.mf-footer button:hover{ transform:translateY(-1px); }

			#mfBtnLimpiar{ background:#f1f5f9; color:#64748b; }
			#mfBtnAplicar{ background:#0177cb; color:white; }

			.mf-label{
				display:block;
				font-size:11px;
				font-weight:700;
				color:#64748b;
				text-transform:uppercase;
				letter-spacing:.06em;
				margin-bottom:10px;
			}

			.mf-tipos{
				display:flex;
				flex-wrap:wrap;
				gap:8px;
			}

			.mf-tipo-btn{
				background:#f8fafc;
				border:1.5px solid #e2e8f0;
				border-radius:10px;
				padding:8px 16px;
				cursor:pointer;
				font-size:13px;
				font-weight:600;
				color:#0f172a;
				transition:.15s;
			}

			.mf-tipo-btn.activo{
				background:#0177cb;
				border-color:#0177cb;
				color:white;
			}

			.ac-wrapper{ position:relative; margin-top:10px; }

			.ac-wrapper input{
				width:100%;
				border:1.5px solid #e2e8f0;
				border-radius:10px;
				padding:10px 14px;
				font-size:13px;
				color:#0f172a;
				background:#f8fafc;
				outline:none;
				transition:border-color .2s, box-shadow .2s;
			}

			.ac-wrapper input:focus{
				border-color:#0177cb;
				background:white;
				box-shadow:0 0 0 3px rgba(1,119,203,.12);
			}

			.ac-dropdown{
				position:absolute;
				top:calc(100% + 6px);
				left:0;
				right:0;
				background:white;
				border:1.5px solid #e2e8f0;
				border-radius:12px;
				box-shadow:0 10px 30px rgba(0,0,0,.12);
				z-index:99999;
				max-height:220px;
				overflow-y:auto;
				display:none;
			}

			.ac-item{
				padding:10px 14px;
				font-size:13px;
				cursor:pointer;
				color:#0f172a;
				border-bottom:1px solid #f1f5f9;
				transition:.15s;
			}

			.ac-item:last-child{ border-bottom:none; }
			.ac-item:hover{ background:#eff6ff; color:#0177cb; }

			.ac-empty{
				padding:12px 14px;
				font-size:13px;
				color:#94a3b8;
				text-align:center;
			}

			.mf-chip-wrap{ margin-top:10px; min-height:4px; }

			.mf-chip{
				display:inline-flex;
				align-items:center;
				gap:8px;
				background:#e0f2fe;
				border:1.5px solid #bae6fd;
				border-radius:999px;
				padding:6px 14px;
				font-size:13px;
				font-weight:600;
				color:#0369a1;
			}

			.mf-chip button{
				background:none;
				border:none;
				color:#0369a1;
				cursor:pointer;
				font-size:14px;
				line-height:1;
				padding:0;
			}

			.mf-fechas{
				display:flex;
				align-items:center;
				gap:10px;
			}

			.mf-fechas input, .mf-select{
				flex:1;
				border:1.5px solid #e2e8f0;
				border-radius:10px;
				padding:9px 12px;
				font-size:13px;
				color:#0f172a;
				background:white;
				outline:none;
				transition:border-color .2s;
			}

			.mf-fechas input:focus, .mf-select:focus{ border-color:#0177cb; }

			.mf-fechas span{ color:#94a3b8; font-size:13px; flex-shrink:0; }

			.mf-checks{ display:flex; flex-direction:column; gap:8px; }

			.mf-checks label{
				display:flex;
				align-items:center;
				gap:8px;
				font-size:13px;
				color:#0f172a;
				font-weight:500;
				cursor:pointer;
			}

			.mf-checks input[type="checkbox"]{ width:15px; height:15px; accent-color:#0177cb; cursor:pointer; }

			/* ============================================
			MODAL FICHA (detalle full-page al doble clic en una fila)
			============================================ */

			#modalFicha{
				position:fixed;
				inset:0;
				background:rgba(15,23,42,.78);
				z-index:99999999;
				display:none;
				justify-content:center;
				align-items:center;
				padding:1.5rem;
				backdrop-filter:blur(6px);
			}

			/* SweetAlert2 por defecto trae su propio z-index (~1060), muy por
			   debajo de nuestros modales apilados (modalConstructor 999999,
			   modalFiltros/modalPermisos 9999999, modalFicha 99999999) --
			   sin esto, cualquier Swal.fire() (ej. "Selecciona un valor de
			   la lista de búsqueda" en aplicarFiltros) queda TAPADO detrás
			   del fondo oscuro del modal y no se ve, aunque sí está ahí. */
			.swal2-container{
				z-index:999999999 !important;
			}

			/* Estilo del SweetAlert de "Buscar por folio" (ver
			   buscarPorFolioDirecto()) -- mismo azul/radios/sombras que
			   el resto del sitio, en vez del look genérico de SweetAlert
			   por default. */
			.swal-folio-popup{
				border-radius:20px !important;
				padding-bottom:28px !important;
				box-shadow:0 30px 70px rgba(15,23,42,.28) !important;
			}
			.swal-folio-icon{
				width:56px;
				height:56px;
				border-radius:16px;
				background:linear-gradient(135deg,#eff6ff,#dbeafe);
				display:flex;
				align-items:center;
				justify-content:center;
				font-size:26px;
				margin:4px auto 14px;
			}
			.swal-folio-title{
				font-size:19px !important;
				font-weight:700 !important;
				color:#0f2942 !important;
			}
			.swal-folio-sub{
				font-size:13px;
				color:#7b8794;
				margin-top:-4px;
				margin-bottom:4px;
			}
			.swal-folio-popup .swal2-input{
				border:1.5px solid #dbe9fb !important;
				border-radius:12px !important;
				box-shadow:none !important;
				font-size:15px !important;
				height:46px !important;
				margin-top:10px !important;
				transition:border-color .15s, box-shadow .15s;
			}
			.swal-folio-popup .swal2-input:focus{
				border-color:#185FA5 !important;
				box-shadow:0 0 0 3px rgba(24,95,165,.15) !important;
			}
			.swal-folio-popup .swal2-actions{ margin-top:18px !important; gap:10px; }
			.swal-folio-btn-confirm{
				border-radius:10px !important;
				font-weight:700 !important;
				padding:10px 26px !important;
				box-shadow:0 4px 12px rgba(24,95,165,.28) !important;
			}
			.swal-folio-btn-cancel{
				border-radius:10px !important;
				font-weight:600 !important;
				padding:10px 22px !important;
				box-shadow:none !important;
			}
			.swal-folio-popup .swal2-validation-message{
				border-radius:10px !important;
				font-size:12.5px !important;
			}

			/* height:auto (con tope max-height) en vez de height:94vh fijo --
			   con el layout FIJO (mismos campos siempre, no grupos que
			   crecen según cuántos traigan dato) el contenido casi nunca
			   llena 94vh, y forzar esa altura dejaba un colchón de espacio
			   en blanco enorme entre las tarjetas y las franjas de abajo.
			   Con auto el modal se encoge a lo que realmente necesita; el
			   tope sigue ahí por si algún folio trae tanto texto que se
			   acerque al límite de la pantalla. */
			.mfi-content{
				width:96vw;
				max-width:1600px;
				max-height:94vh;
				background:#f4f7fb;
				border-radius:16px;
				display:flex;
				flex-direction:column;
				overflow:hidden;
				animation:modalIn .2s ease;
				box-shadow:0 30px 70px rgba(0,0,0,.28);
			}

			.mfi-header{
				background:#185FA5;
				background-image:linear-gradient(120deg, #185FA5, #0f4d8a);
				color:white;
				padding:10px 21px;
				display:flex;
				align-items:center;
				gap:11px;
				flex-shrink:0;
				border-bottom:2px solid #0b3a6b;
			}

			.mfi-header-avatar{
				width:32px;
				height:32px;
				border-radius:10px;
				background:rgba(255,255,255,.16);
				display:flex;
				align-items:center;
				justify-content:center;
				font-size:13px;
				flex-shrink:0;
			}

			.mfi-header-txt{ flex:1; min-width:0; }

			.mfi-header h2{
				font-size:13px;
				font-weight:600;
				letter-spacing:.01em;
				white-space:nowrap;
				overflow:hidden;
				text-overflow:ellipsis;
			}

			.mfi-header .mfi-sub{
				font-size:9.5px;
				opacity:.8;
				font-weight:500;
				margin-top:1px;
			}

			/* Badge de categoría del cliente (ej. "Cotizante"), igual que
			   en seg_ventas.php -- ahí se pinta junto al nombre del
			   cliente en el título de la ficha (m.categoria_cliente,
			   tabla movilidad). Aquí se muestra igual, en el header del
			   modal, junto al botón de cerrar. Solo aparece si el folio
			   trae dato (display:none por default, ver renderFicha()). */
			.mfi-badge-categoria{
				flex-shrink:0;
				background:rgba(255,255,255,.18);
				border:1px solid rgba(255,255,255,.35);
				color:white;
				font-size:9.5px;
				font-weight:700;
				text-transform:uppercase;
				letter-spacing:.04em;
				padding:5px 12px;
				border-radius:20px;
				white-space:nowrap;
			}

			.mfi-btn-close{
				width:27px;
				height:27px;
				border:none;
				border-radius:6px;
				background:rgba(255,255,255,.15);
				color:white;
				font-size:12px;
				cursor:pointer;
				flex-shrink:0;
				transition:.15s;
				display:flex;
				align-items:center;
				justify-content:center;
			}

			.mfi-btn-close:hover{ background:rgba(255,255,255,.28); }

			/* SIN scroll a propósito: esto es lo primero que el usuario ve
			   al abrir la ficha y se pidió expresamente que se vea TODA la
			   información de un vistazo. Estilo calcado del modal "Detalle
			   De Cumplimiento" (seg_cumplimiento.php) que ya usa el sitio:
			   cada dato es su propia tarjeta blanca con borde suave
			   (.mfi-campo), los montos importantes salen en tarjeta blanca
			   con número grande de color (.mfi-total, como SALDO TOTAL/
			   BONO), y los estatus van en pastilla de color según su
			   significado (verde=positivo, rojo=negativo, azul=neutro) en
			   vez de texto plano -- así se reconoce el dato de un vistazo
			   sin tener que leerlo completo. */
			.mfi-body{
				display:flex;
				flex-direction:column;
				padding:12px 18px;
				gap:6px;
			}

			.mfi-loading, .mfi-vacio-msg{
				flex:1;
				display:flex;
				flex-direction:column;
				align-items:center;
				justify-content:center;
				gap:12px;
				padding:70px 20px;
				color:#94a3b8;
				font-size:14px;
			}

			.mfi-spinner{
				width:34px;
				height:34px;
				border:3px solid #dbeafe;
				border-top-color:#185FA5;
				border-radius:50%;
				animation:mfiSpin .7s linear infinite;
			}

			@keyframes mfiSpin{ to{ transform:rotate(360deg); } }

			/* Franja de estatus (arriba del todo): 4 tarjetas blancas con
			   pastilla de color, igual que "Estatus SF / Estatus Procesar /
			   Confirmación / Cierre captura" de la referencia. */
			.mfi-status-strip{
				flex-shrink:0;
				display:grid;
				grid-template-columns:repeat(4, 1fr);
				gap:9px;
			}

			.mfi-status-item{
				background:white;
				border:1px solid #e8edf3;
				border-left:3px solid #cbd5e1;
				border-radius:11px;
				padding:8px 14px;
				display:flex;
				align-items:center;
				justify-content:space-between;
				gap:9px;
				box-shadow:0 1px 3px rgba(15,23,42,.05);
				transition:box-shadow .15s, transform .15s;
			}

			.mfi-status-item:hover{ box-shadow:0 4px 14px rgba(15,23,42,.09); transform:translateY(-1px); }

			.mfi-status-item.mfi-pill-ok-acc      { border-left-color:#22a35a; }
			.mfi-status-item.mfi-pill-bad-acc     { border-left-color:#e14b4b; }
			.mfi-status-item.mfi-pill-neutral-acc { border-left-color:#185FA5; }

			.mfi-status-item .mfi-status-label{
				font-size:9px;
				font-weight:700;
				color:#7b8794;
				text-transform:uppercase;
				letter-spacing:.03em;
				white-space:nowrap;
			}

			/* Bloque principal: 3 columnas de tarjetas + panel lateral.
			   align-items+align-self:start -- las tarjetas NO se estiran a
			   la altura de la más alta del renglón; cada una mide lo que
			   necesita su propio contenido y se queda pegada arriba, en
			   vez de dejar un hueco en blanco al fondo cuando trae menos
			   campos que sus vecinas. El "panel lateral" (Campaña y
			   Movilidad) además va con acento azul y letra más grande --
			   es de los primeros datos que se leen, así que tiene que
			   notarse a simple vista, no perderse como un cajón gris más. */
			.mfi-main{
				display:grid;
				grid-template-columns:2.7fr 1fr .95fr;
				align-items:start;
				gap:7px;
			}

			/* Agrupa Identificación/Movimiento + AV en su propia mini-grid
			   de 2 columnas, para que Observaciones (que va justo debajo,
			   con el mismo ancho de esas dos) se pegue a su altura real y
			   no a la de Estructura comercial (que es más alta y queda en
			   una columna aparte, fuera de este grupo). Así no queda un
			   hueco en blanco esperando a que termine la columna vecina. */
			.mfi-left-group{
				display:grid;
				grid-template-columns:1.7fr 1fr;
				align-items:start;
				gap:7px;
			}

			.mfi-col{
				background:white;
				border:1px solid #e8edf3;
				border-radius:11px;
				padding:8px 12px;
				display:flex;
				flex-direction:column;
				gap:6px;
				justify-content:flex-start;
				align-self:start;
				box-shadow:0 1px 3px rgba(15,23,42,.05);
			}

			/* 2 columnitas internas para tarjetas con muchos campos
			   (ej. Identificación/Movimiento) -- evita que quede una
			   lista larga y angosta cuando trae el doble o triple de
			   datos que sus vecinas. */
			.mfi-col-grid{
				display:grid;
				grid-template-columns:1fr 1fr;
				gap:6px 12px;
			}

			/* Tarjeta de Observaciones/Descripción: mismo ancho que
			   Identificación/Movimiento + AV juntas (columnas 1 y 2),
			   no se extiende bajo Estructura comercial ni el panel lateral. */
			.mfi-col-wide{ grid-column:1 / 3; }

			.mfi-side{
				background:linear-gradient(165deg,#eaf2fb,#f4f8fd);
				border:1.5px solid #cfe0f5;
				border-radius:11px;
				padding:8px 12px;
				display:flex;
				flex-direction:column;
				gap:4px;
				align-self:start;
				box-shadow:0 4px 16px rgba(24,95,165,.1);
			}

			/* SIN caja individual (a diferencia de como se veía antes) --
			   cada campo de las demás columnas (.mfi-col) tampoco trae su
			   propia tarjeta blanca, solo label+valor apilados; esta
			   columna traía ese "cajón" extra en cada campo y por eso
			   salía mucho más alta que sus vecinas (Estructura comercial,
			   Identificación/AV/Observaciones), dejando un hueco en
			   blanco debajo de ellas antes de llegar a las franjas de
			   dinero/fechas. Sin la caja, el panel mide lo mismo que sus
			   vecinas y las franjas de abajo suben justo donde debían. */
			.mfi-side .mfi-campo{
				padding:2px 0;
				min-width:0;
			}

			.mfi-side .mfi-campo label{ color:#5b7fa6; margin-bottom:0; }

			.mfi-side .mfi-campo .mfi-valor{ font-size:11.5px; font-weight:700; color:#0f4d8a; }

			/* Ley + Cotiza lado a lado, dentro de la misma tarjeta de
			   Campaña/Movilidad (ver renderFicha()) -- min-width:0 en
			   las celdas de arriba es lo que evita que la pastilla de
			   Cotiza (con su padding fijo) empuje el grid y se salga de
			   la tarjeta hacia la columna vecina. */
			.mfi-side-grid{
				display:grid;
				grid-template-columns:1fr 1fr;
				gap:7px;
			}

			.mfi-side-grid .mfi-pill{ font-size:9.5px; padding:3px 9px; }

			.mfi-campo label{
				display:block;
				font-size:8.5px;
				font-weight:700;
				color:#7b8794;
				text-transform:uppercase;
				letter-spacing:.03em;
				margin-bottom:2px;
			}

			.mfi-campo .mfi-valor{
				font-size:11.5px;
				font-weight:600;
				color:#1f2d3d;
				word-break:break-word;
				line-height:1.25;
			}

			.mfi-campo .mfi-valor.mfi-vacio{ color:#c3cbd6; font-weight:400; }

			/* Pastillas de estatus -- coloreadas según el significado del
			   valor (ok=verde, alerta=rojo, neutro=azul claro), con un
			   puntito antes del texto para que se reconozcan de reojo. */
			.mfi-pill{
				display:inline-flex;
				align-items:center;
				gap:6px;
				font-size:10.5px;
				font-weight:700;
				padding:4px 12px;
				border-radius:20px;
				white-space:nowrap;
			}
			.mfi-pill::before{
				content:'';
				width:6px;
				height:6px;
				border-radius:50%;
				background:currentColor;
				flex-shrink:0;
			}
			.mfi-pill-ok      { background:#d7f5e3; color:#0b6b3a; }
			.mfi-pill-bad     { background:#f5d7d7; color:#8a1f1f; }
			.mfi-pill-neutral { background:#dbe9fb; color:#185FA5; }
			.mfi-pill-vacio   { background:#f0f4f8; color:#c3cbd6; }
			.mfi-pill-vacio::before{ display:none; }

			/* Franjas inferiores (dinero / fechas): fila de tarjetas
			   iguales, cada una label arriba + valor abajo. Dinero usa
			   número grande de color, igual que SALDO TOTAL/BONO. */
			.mfi-strip{
				flex-shrink:0;
				display:grid;
				grid-auto-flow:column;
				grid-auto-columns:1fr;
				gap:7px;
			}

			.mfi-strip .mfi-campo{
				background:white;
				border:1px solid #e8edf3;
				border-radius:9px;
				padding:6px 9px;
				box-shadow:0 1px 3px rgba(15,23,42,.05);
				transition:box-shadow .15s, transform .15s;
			}

			.mfi-strip .mfi-campo:hover{ box-shadow:0 4px 14px rgba(15,23,42,.09); transform:translateY(-1px); }

			.mfi-strip .mfi-campo label{ font-size:8px; margin-bottom:1px; }
			.mfi-strip .mfi-campo .mfi-valor{ font-size:10.5px; }

			.mfi-campo-money{ border-color:#dbe9fb; }
			.mfi-campo-money .mfi-valor{ font-size:14px; font-weight:700; color:#185FA5; letter-spacing:-.01em; }
			.mfi-campo-money.mfi-money-verde{ border-color:#cdeede; }
			.mfi-campo-money.mfi-money-verde .mfi-valor{ color:#0F6E56; }

			/* Pantallas chicas: apila las columnas y achica la letra, pero
			   SIN scroll bajo ninguna circunstancia. */
			@media (max-width: 1100px){
				.mfi-status-strip{ grid-template-columns:repeat(2, 1fr); }
				.mfi-main{ grid-template-columns:1fr 1fr; grid-auto-rows:min-content; row-gap:10px; }
				.mfi-left-group{ grid-column:1 / -1; }
			}
			@media (max-width: 700px){
				.mfi-body{ padding:8px 10px; gap:6px; }
				.mfi-status-strip{ grid-template-columns:1fr; gap:6px; }
				.mfi-main{ grid-template-columns:1fr; gap:8px; }
				.mfi-left-group{ grid-template-columns:1fr; gap:8px; }
				.mfi-col-grid{ grid-template-columns:1fr; gap:6px; }
				.mfi-strip{ grid-auto-flow:row; grid-template-columns:repeat(2,1fr); gap:6px; }
				.mfi-campo label{ font-size:8px; }
				.mfi-campo .mfi-valor{ font-size:10px; }
				.mfi-campo-money .mfi-valor{ font-size:13px; }
			}

			.mc-tabla tbody tr{ cursor:pointer; transition:background .1s; }
			.mc-tabla tbody tr:hover td{ background:#f0f9ff; }

		</style>
	</head>

	<?php include("./funciones/clas_carobra.php"); ?>
	<?php require_once('./seg_ventas_reporteador_funciones.php'); ?>

	<body>
<?php
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
			reg_acceso($_SESSION["nombre"],"reporteador_ventas");
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
				titulo("Reporteador de Ventas");
				$pag="PAGOS_Y_FACTURAS_NAP";
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
				$divcontenido->css("overflow-y","auto");

				/* --- Datos propios del reporteador (no vienen en la plantilla base) --- */
				$conecta = new conector();
				$nombre  = $_SESSION["nombre"];
				$nivel   = nivel_usuario(utf8_decode($nombre));
				$esAdmin = ($nivel == 12);

				$queryClave = "Select clave from empl_usuarios where nombre='" . mysqli_real_escape_string($conecta->conexion, utf8_decode($nombre)) . "'";
				$bdClave    = $conecta->consulta($queryClave);
				$tbClave    = $bdClave ? mysqli_fetch_array($bdClave) : null;
				$claveSesion = $tbClave['clave'] ?? '';

				/* "Opciones adicionales" (folios sin fecha de pago / saldo
				   real distinto de cero / registro especial) son filtros
				   contables -- solo Finanzas y admin deben verlos. Ver
				   puedeVerOpcionesAdicionales() en
				   seg_ventas_reporteador_funciones.php (misma fuente que
				   ya usa RRHH: carobra_2014.permisos_pestanas). */
				$puedeVerOpciones = puedeVerOpcionesAdicionales($conecta, $nivel, $claveSesion);

				/* Campos de contacto (teléfono/celular/email/CP/frecuencia)
				   solo se muestran a admins (nivel 12) salvo que el admin
				   se los haya autorizado puntualmente a esta clave -- ver
				   camposVisiblesParaUsuario() en
				   seg_ventas_reporteador_funciones.php y el modal
				   "⚙️ Permisos de campos sensibles" (admin-only) más abajo. */
				$camposVisibles = camposVisiblesParaUsuario($conecta, $nivel, $claveSesion, $CAMPOS_REPORTE);

				/* Bloqueo por nivel -- ver filtrosPermitidosParaNivel() /
				   filtroEsFijoParaNivel() / resolverValorFiltroFijo() en
				   seg_ventas_reporteador_funciones.php (mismas reglas que
				   el seg_ventas.php real: SC y NAP bloqueados para nivel
				   3/4/5, Comisionista fijo para nivel 3 y 7, Gerencia fija
				   -a la gerencia del usuario, no a su nombre- para nivel 4).
				   "Folio" es una búsqueda puntual por número de folio y no
				   depende del nivel, siempre está disponible. */
				$filtrosPermitidos = array_merge(['Folio'], filtrosPermitidosParaNivel($nivel));
				$filtroFijo      = filtroFijoParaNivel($nivel);
				$valorFiltroFijo = ($filtroFijo !== null)
					? resolverValorFiltroFijo($conecta, $nivel, $nombre, $filtroFijo)
					: null;
?>
					<div class="container">

						<div class="rv-page">
							<div class="rv-grid" id="rvGridReportes"></div>
						</div>

						<!-- MODAL FULLSCREEN CONSTRUCTOR -->
						<div id="modalConstructor">
							<div class="mc-content">

								<div class="mc-header">
									<h2>📊 Reporteador de Ventas</h2>
									<?php if ($esAdmin): ?>
									<button class="mc-btn-close" onclick="abrirModalPermisos()" title="Permisos de campos sensibles" style="margin-right:6px;">⚙️</button>
									<?php endif; ?>
									<button class="mc-btn-close" onclick="cerrarConstructor()">✕</button>
								</div>

								<div class="mc-body">

									<div class="mc-campos">
										<div class="mc-campos-buscar">
											<input type="text" id="mcBuscarCampo" placeholder="Buscar campo...">
										</div>
										<div class="mc-campos-list" id="mcCamposList"></div>
									</div>

									<div class="mc-derecho">
										<div class="mc-derecho-title">
											<span id="mcDerechoTitle">Columnas del reporte</span>
											<button onclick="restaurarCamposDefault()">↺ Restaurar default</button>
										</div>
										<div class="mc-resumen" id="mcResumen"></div>
										<div class="mc-dropzone" id="mcDropzone">
											<div class="mc-placeholder" id="mcPlaceholder">
												<span>📋</span>
												<p>Arrastra o haz clic en un campo de la izquierda para agregarlo a tu reporte</p>
											</div>
											<table class="mc-tabla" id="mcTabla" style="display:none;">
												<thead><tr id="mcThead"></tr></thead>
												<tbody id="mcTbody"></tbody>
											</table>
										</div>
									</div>

								</div>

								<div class="mc-footer">
									<button id="mcBtnLimpiar" onclick="quitarTodosCampos()">🗑 Quitar todos</button>
									<button id="mcBtnGuardar" onclick="guardarConfigComo()">💾 Guardar como...</button>
									<button id="mcBtnGenerar">🔍 Filtrar y generar</button>
									<button id="mcBtnExportar">📥 Exportar a Excel</button>
								</div>

							</div>
						</div>

					</div>
<?php
				$divcontenido->close();
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

					<!-- MODAL FILTROS -->
					<div id="modalFiltros">
						<div class="mf-content">

							<div class="mf-header">
								<h2>🔍 Filtros del reporte</h2>
								<button class="mc-btn-close" onclick="cerrarModalFiltros()">✕</button>
							</div>

							<div class="mf-body">

								<div>
									<label class="mf-label">Buscar por</label>
									<div class="mf-tipos" id="mfTipos">
										<?php /* "Folio" ya no se ofrece aquí -- tiene su propia card
										   "🔎 Buscar por folio" en el landing (ver pintarConfigsGuardadas()
										   en el JS) que abre la ficha directo, sin pasar por este modal.
										   $filtrosPermitidos sigue incluyendo 'Folio' porque el backend
										   (generarReporte) todavía necesita aceptarlo como filtro válido
										   para esa card -- solo se quita el botón de esta lista. */ ?>
										<?php foreach (array_diff($filtrosPermitidos, ['Folio']) as $ft): ?>
											<button type="button" class="mf-tipo-btn" data-tipo="<?= htmlspecialchars($ft) ?>"><?= htmlspecialchars($ft) ?></button>
										<?php endforeach; ?>
									</div>
									<div class="ac-wrapper" id="mfBuscadorWrap" style="display:none;">
										<input type="text" id="mfBuscadorInput" placeholder="Escribe para buscar..." autocomplete="off">
										<div class="ac-dropdown" id="mfDropdown"></div>
									</div>
									<div class="mf-chip-wrap" id="mfChipWrap"></div>
								</div>

								<div id="mfTipoMesWrap">
									<label class="mf-label">Tipo de mes</label>
									<select class="mf-select" id="mfTipoMes">
										<option value="">Calendario comercial</option>
										<option value="ep">Calendario anual</option>
									</select>
								</div>

								<div id="mfFechasWrap">
									<label class="mf-label">Rango de fechas</label>
									<div class="mf-tipos" id="mfModoFechaBtns" style="margin-bottom:10px;">
										<button type="button" class="mf-tipo-btn" data-modo="abierto">Calendario abierto</button>
										<button type="button" class="mf-tipo-btn" data-modo="periodo">Periodo</button>
									</div>
									<label class="mf-label" id="mfFechasLabel">Rango de fechas (fecha de traspaso)</label>
									<div class="mf-fechas" id="mfFechasExactas">
										<input type="date" id="mfFechaDesde">
										<span>al</span>
										<input type="date" id="mfFechaHasta">
									</div>
									<div id="mfFechasPeriodo" style="display:none;flex-direction:column;gap:8px;">
										<div class="mf-fechas">
											<select class="mf-select" id="mfAnioIni"><option value="">Año...</option></select>
											<select class="mf-select" id="mfMesIni"><option value="">Mes...</option></select>
											<span>al</span>
											<select class="mf-select" id="mfAnioFin"><option value="">Año...</option></select>
											<select class="mf-select" id="mfMesFin"><option value="">Mes...</option></select>
										</div>
									</div>
									<div id="mfRangoRealPeriodo" style="display:none;font-size:12px;color:#64748b;margin-top:6px;"></div>
								</div>

								<div id="mfOpcionesWrap">
									<label class="mf-label">Opciones adicionales</label>
									<div class="mf-checks">
										<label><input type="checkbox" id="mfSfp"> Solo folios sin fecha de pago</label>
										<label><input type="checkbox" id="mfSrm0"> Solo con saldo real diferente de cero</label>
										<label><input type="checkbox" id="mfRegEsp"> Registro especial (folio corto)</label>
									</div>
								</div>

							</div>

							<div class="mf-footer">
								<button id="mfBtnLimpiar" onclick="limpiarFiltros()">Limpiar</button>
								<button id="mfBtnAplicar" onclick="aplicarFiltros()">⚡ Aplicar y generar</button>
							</div>

						</div>
					</div>

					<!-- MODAL FICHA (detalle completo de un folio, doble clic en una fila) -->
					<div id="modalFicha">
						<div class="mfi-content">

							<div class="mfi-header">
								<div class="mfi-header-avatar">👤</div>
								<div class="mfi-header-txt">
									<h2 id="mfiTitulo">Detalle del folio</h2>
									<div class="mfi-sub" id="mfiSubtitulo"></div>
								</div>
								<span class="mfi-badge-categoria" id="mfiCategoria" style="display:none;"></span>
								<button class="mfi-btn-close" onclick="cerrarModalFicha()">✕</button>
							</div>

							<div class="mfi-body" id="mfiBody">
								<div class="mfi-loading">
									<div class="mfi-spinner"></div>
									<span>Cargando información...</span>
								</div>
							</div>

						</div>
					</div>

					<?php if ($esAdmin): ?>
					<!-- MODAL PERMISOS DE CAMPOS SENSIBLES (admin-only) -->
					<div id="modalPermisos">
						<div class="mf-content" style="max-width:720px;">

							<div class="mf-header">
								<h2>🔒 Permisos de campos sensibles</h2>
								<button class="mc-btn-close" onclick="cerrarModalPermisos()">✕</button>
							</div>

							<div class="mf-body">

								<div>
									<label class="mf-label">Buscar usuario</label>
									<div class="ac-wrapper" style="margin-top:0;">
										<input type="text" id="permUsuarioBuscador" placeholder="Buscar por nombre o clave..." autocomplete="off">
										<div class="ac-dropdown" id="permUsuarioDropdown"></div>
									</div>
								</div>

								<div id="permUsuarioSeleccionado" style="display:none;">
									<div style="font-weight:700;color:#0f172a;font-size:14px;margin-bottom:2px;" id="permUsuarioNombre"></div>
									<div style="font-size:12px;color:#94a3b8;margin-bottom:12px;">Marca los campos a los que le das acceso -- se guarda al instante, sin botón aparte.</div>
									<div class="mf-checks" id="permCamposCheckbox"></div>
								</div>

								<div>
									<label class="mf-label">Usuarios con excepciones activas</label>
									<div id="permResumenLista" style="display:flex;flex-direction:column;gap:6px;"></div>
								</div>

							</div>

							<div class="mf-footer">
								<button id="mfBtnLimpiar" onclick="cerrarModalPermisos()">Cerrar</button>
							</div>

						</div>
					</div>
					<?php endif; ?>
<?php
		}
?>
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
	</script>

	<!-- ============================================================
	     JAVASCRIPT - REPORTEADOR (propio de esta página)
	     ============================================================ -->
	<script>

	const API_REPORTEADOR = './seg_ventas_reporteador_api.php';

	var CAMPOS_REPORTE = <?php echo json_encode($camposVisibles); ?>;
	var CAMPOS_DEFAULT = <?php echo json_encode($CAMPOS_DEFAULT); ?>;
	var CAMPOS_MONEDA  = <?php echo json_encode($CAMPOS_MONEDA); ?>;
	var ES_ADMIN       = <?php echo json_encode($esAdmin); ?>;
	var PUEDE_VER_OPCIONES = <?php echo json_encode($puedeVerOpciones); ?>;

	/* Campos sensibles administrables por usuario específico (ver modal
	   "⚙️ Permisos de campos sensibles", solo visible si ES_ADMIN) --
	   mismo array que $CAMPOS_SOLO_ADMIN en seg_ventas_reporteador_funciones.php.
	   Sus labels salen de CAMPOS_REPORTE de arriba porque el admin SIEMPRE
	   ve el catálogo completo (camposVisiblesParaUsuario). */
	var CAMPOS_SENSIBLES = <?php echo json_encode($CAMPOS_SOLO_ADMIN); ?>;
	var permisosActuales = {}; // campoKey -> [{clave, nombre}]

	/* Filtro fijo por nivel (SC para nivel 13, SubBrocker para nivel 5,
	   Comisionista para nivel 3/7, Gerencia para nivel 4) -- ver
	   filtroFijoParaNivel()/resolverValorFiltroFijo() en
	   seg_ventas_reporteador_funciones.php. Folio nunca es fijo. */
	var REP_FILTRO_FIJO = <?php echo json_encode($filtroFijo); ?>;
	var REP_VALOR_FIJO  = <?php echo json_encode($valorFiltroFijo); ?>;

	/* Orden fijo de los grupos en el árbol (si algún grupo no aparece
	   aquí, se pinta al final en el orden en que lo entregue el catálogo). */
	var ORDEN_GRUPOS = ['Personales', 'Saldos', 'Fechas', 'Comercial / Estructura', 'Producción / Movimiento', 'Estatus / Proceso', 'Autorización de Vida (AV)'];
	var GRUPO_ICONOS  = {
		'Personales':                 '👤',
		'Saldos':                     '💰',
		'Fechas':                     '📅',
		'Comercial / Estructura':     '🏢',
		'Producción / Movimiento':    '📦',
		'Estatus / Proceso':          '🚦',
		'Autorización de Vida (AV)':  '📄'
	};
	var gruposColapsados = {};

	var camposActuales   = [];
	var filtroTipoActivo = '';
	var filtroValor       = null;   // { etiqueta, valor }
	/* v4 -> v5: se cambió el orden/contenido de $CAMPOS_DEFAULT (ahora
	   empieza con sc, brocker, gerencia) -- si dejábamos el mismo nombre
	   de key, a cualquiera que ya tuviera un borrador sin nombre guardado
	   en su navegador (de antes de este cambio) le seguiría ganando ESE
	   borrador viejo sobre el default nuevo, y parecería que el cambio
	   "no aplicó". Bumpear la key hace que ese borrador viejo se ignore
	   solo (sin que el usuario tenga que darle "Restaurar default" a
	   mano) y arranque limpio con el default correcto. Los reportes
	   GUARDADOS con nombre (LS_KEY_CONFIGS) no se tocan -- esos sí deben
	   conservar lo que el usuario armó explícitamente. */
	var LS_KEY         = 'reporteador_campos_v5';       // borrador de trabajo (auto-guardado, sin nombre)
	var LS_KEY_CONFIGS = 'reporteador_configs_v1';       // lista de reportes guardados con nombre (cards del landing)
	var configActivaId  = null;                          // id del reporte guardado que se está editando (null = borrador nuevo)

	/* ============================================
	INIT
	============================================ */

	document.addEventListener('DOMContentLoaded', function(){

		pintarConfigsGuardadas();

		document.getElementById('mcBuscarCampo').addEventListener('input', function(){
			pintarCamposDisponibles(this.value.trim().toLowerCase());
		});

		document.getElementById('mcBtnGenerar').addEventListener('click', function(){
			if(camposActuales.length === 0){
				Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos un campo a tu reporte' });
				return;
			}
			document.getElementById('modalFiltros').style.display = 'flex';
			/* Si el nivel tiene un filtro fijo (SC/SubBrocker/Comisionista/
			   Gerencia), seleccionarlo de una vez -- nunca dejarle elegir
			   otro tipo de búsqueda distinto al que le corresponde. */
			if(REP_FILTRO_FIJO && !filtroTipoActivo){
				seleccionarFiltroTipo(REP_FILTRO_FIJO);
			}
		});

		document.getElementById('mcBtnExportar').addEventListener('click', exportarReporte);

		initDropzone();

		/* Botones de tipo de filtro */
		document.querySelectorAll('.mf-tipo-btn').forEach(function(btn){
			btn.addEventListener('click', function(){
				seleccionarFiltroTipo(btn.dataset.tipo);
			});
		});

		/* Buscador con dropdown (debounce) -- excepto Folio, que es un
		   valor directo (no una lista para elegir de un catálogo). */
		var timerBusqueda = null;
		document.getElementById('mfBuscadorInput').addEventListener('input', function(){
			var termino = this.value.trim();

			if(filtroTipoActivo === 'Folio'){
				filtroValor = termino ? { etiqueta: termino, valor: termino } : null;
				return;
			}

			clearTimeout(timerBusqueda);
			if(termino.length < 1){ document.getElementById('mfDropdown').style.display = 'none'; return; }
			timerBusqueda = setTimeout(function(){ buscarFiltroValor(termino); }, 250);
		});

		document.getElementById('mfBuscadorInput').addEventListener('blur', function(){
			setTimeout(function(){ document.getElementById('mfDropdown').style.display = 'none'; }, 200);
		});

		/* Calendario abierto (fecha exacta) vs Periodo (año+mes) -- es un
		   modo independiente del "Tipo de mes" (que solo decide contra
		   cuál tabla se resuelve: fechas_cierre_com para "Mes de
		   comisión", fechas_cierre para "Mes de pago"). Ver
		   seleccionarModoFecha() / cargarPeriodos() / actualizarRangoRealAbierto(). */
		document.querySelectorAll('#mfModoFechaBtns .mf-tipo-btn').forEach(function(btn){
			btn.addEventListener('click', function(){ seleccionarModoFecha(btn.dataset.modo); });
		});
		document.getElementById('mfTipoMes').addEventListener('change', cargarPeriodos);
		document.getElementById('mfAnioIni').addEventListener('change', function(){ poblarMeses('mfAnioIni', 'mfMesIni'); actualizarRangoRealPeriodo(); });
		document.getElementById('mfAnioFin').addEventListener('change', function(){ poblarMeses('mfAnioFin', 'mfMesFin'); actualizarRangoRealPeriodo(); });
		document.getElementById('mfMesIni').addEventListener('change', actualizarRangoRealPeriodo);
		document.getElementById('mfMesFin').addEventListener('change', actualizarRangoRealPeriodo);
		document.getElementById('mfFechaDesde').addEventListener('change', actualizarRangoRealAbierto);
		document.getElementById('mfFechaHasta').addEventListener('change', actualizarRangoRealAbierto);
		seleccionarModoFecha('abierto');
		cargarPeriodos();

	});

	/* Nombres de mes 1..12 para mostrar "Enero", "Febrero", etc. en vez
	   del "PERIODO 01" crudo de la tabla -- así lo pidió el cliente. Si
	   el número de mes no cae en 1-12 (ej. el 13 que a veces aparece
	   para un periodo extra de cierre, "Diciembre bis"/"MCT3") se usa
	   nom_periodo tal cual viene en fechas_cierre/fechas_cierre_com,
	   porque ese caso no tiene nombre de mes real. */
	var NOMBRES_MES = ['Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'];

	function etiquetaMes(mes, nomPeriodo){
		var m = parseInt(mes, 10);
		if(m >= 1 && m <= 12) return NOMBRES_MES[m - 1];
		return nomPeriodo || ('Mes ' + mes);
	}

	/* periodosPorAnio: { anio: [{anio, mes, nom_periodo, fec_ini, fec_fin}, ...] }
	   -- se arma una sola vez al cargar el catálogo (cargarPeriodos) y la
	   usan tanto poblarMeses() (llenar el <select> de mes de un año) como
	   actualizarRangoRealAbierto() (encontrar a qué periodo pertenece una
	   fecha suelta en modo "Calendario abierto"). periodosPorClave:
	   "anio|mes" -> {fec_ini, fec_fin} -- atajo para el modo Periodo. */
	var periodosPorAnio  = {};
	var periodosPorClave = {};
	var modoFechaActivo  = 'abierto';

	/* Alterna entre "Calendario abierto" (fecha exacta, input type=date)
	   y "Periodo" (selects de año+mes) -- disponible para los dos
	   valores de "Tipo de mes" (antes solo "Mes de comisión" ofrecía
	   periodo). */
	function seleccionarModoFecha(modo){

		modoFechaActivo = modo;
		document.querySelectorAll('#mfModoFechaBtns .mf-tipo-btn').forEach(function(b){
			b.classList.toggle('activo', b.dataset.modo === modo);
		});

		var esPeriodo = (modo === 'periodo');
		document.getElementById('mfFechasLabel').textContent = esPeriodo
			? 'Periodo (año y mes)'
			: 'Rango de fechas (fecha de traspaso)';
		document.getElementById('mfFechasExactas').style.display = esPeriodo ? 'none' : 'flex';
		document.getElementById('mfFechasPeriodo').style.display = esPeriodo ? 'flex' : 'none';
		document.getElementById('mfRangoRealPeriodo').style.display = 'none';

		if(esPeriodo) actualizarRangoRealPeriodo();
		else actualizarRangoRealAbierto();

	}

	async function cargarPeriodos(){

		var te      = document.getElementById('mfTipoMes').value;
		var selAnioIni = document.getElementById('mfAnioIni');
		var selAnioFin = document.getElementById('mfAnioFin');
		var selMesIni  = document.getElementById('mfMesIni');
		var selMesFin  = document.getElementById('mfMesFin');

		selAnioIni.innerHTML = '<option value="">Cargando...</option>';
		selAnioFin.innerHTML = '<option value="">Cargando...</option>';
		selMesIni.innerHTML  = '<option value="">Mes...</option>';
		selMesFin.innerHTML  = '<option value="">Mes...</option>';
		periodosPorAnio  = {};
		periodosPorClave = {};

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'listarPeriodos', te:te })
			});
			var json = await res.json();

			var anios = [];
			if(json.status === 'ok'){
				json.data.forEach(function(p){
					var clave = p.anio + '|' + p.mes;
					periodosPorClave[clave] = { fec_ini: p.fec_ini, fec_fin: p.fec_fin };
					if(!periodosPorAnio[p.anio]){ periodosPorAnio[p.anio] = []; anios.push(p.anio); }
					periodosPorAnio[p.anio].push({ anio: p.anio, mes: p.mes, nom_periodo: p.nom_periodo, fec_ini: p.fec_ini, fec_fin: p.fec_fin });
				});
			}

			var opcionesAnio = '<option value="">Año...</option>';
			anios.forEach(function(a){ opcionesAnio += '<option value="' + a + '">' + a + '</option>'; });

			selAnioIni.innerHTML = opcionesAnio;
			selAnioFin.innerHTML = opcionesAnio;

		} catch(e){
			selAnioIni.innerHTML = '<option value="">Error al cargar</option>';
			selAnioFin.innerHTML = '<option value="">Error al cargar</option>';
		}

		if(modoFechaActivo === 'periodo') actualizarRangoRealPeriodo();
		else actualizarRangoRealAbierto();

	}

	/* Al elegir un año (inicio o fin) se llena su <select> de mes
	   correspondiente con SOLO los meses que existen para ese año en
	   la tabla, en orden -- ej. elegir 2025 muestra Enero..Diciembre
	   (y "Diciembre bis" si ese año tuvo el periodo 13 extra). */
	function poblarMeses(idAnio, idMes){
		var anio = document.getElementById(idAnio).value;
		var selMes = document.getElementById(idMes);

		var meses = periodosPorAnio[anio] || [];
		var opciones = '<option value="">Mes...</option>';
		meses.forEach(function(m){
			opciones += '<option value="' + m.mes + '">' + escaparHtmlRV(etiquetaMes(m.mes, m.nom_periodo)) + '</option>';
		});
		selMes.innerHTML = opciones;
	}

	/* Al terminar de elegir año+mes inicial y año+mes final, muestra
	   debajo de los selects las fechas reales (fec_ini/fec_fin) tal
	   cual vienen en la tabla -- para que el usuario confirme
	   visualmente el rango que se va a usar, sin adivinar a partir del
	   nombre del mes. */
	function actualizarRangoRealPeriodo(){
		if(modoFechaActivo !== 'periodo') return;

		var anioIni = document.getElementById('mfAnioIni').value;
		var mesIni  = document.getElementById('mfMesIni').value;
		var anioFin = document.getElementById('mfAnioFin').value;
		var mesFin  = document.getElementById('mfMesFin').value;
		var caja = document.getElementById('mfRangoRealPeriodo');

		var pIni = periodosPorClave[anioIni + '|' + mesIni];
		var pFin = periodosPorClave[anioFin + '|' + mesFin];

		if(!pIni || !pFin){
			caja.style.display = 'none';
			return;
		}

		caja.textContent = 'Rango real: ' + pIni.fec_ini + ' al ' + pFin.fec_fin;
		caja.style.display = 'block';
	}

	/* "DD/MM/YYYY" -> "YYYY-MM-DD" para poder comparar contra el valor
	   ISO que entregan los <input type="date">. */
	function dmyToIso(d){
		var p = (d || '').split('/');
		if(p.length !== 3) return '';
		return p[2] + '-' + p[1] + '-' + p[0];
	}

	/* Busca, entre TODOS los años cargados, el periodo cuyo fec_ini/fec_fin
	   contiene la fecha dada (ISO). Se usa solo para Calendario abierto +
	   Mes de comisión, donde los periodos no caen en límites de mes
	   calendario -- ver actualizarRangoRealAbierto(). */
	function buscarPeriodoQueContiene(fechaIso){
		for(var anio in periodosPorAnio){
			var meses = periodosPorAnio[anio];
			for(var i = 0; i < meses.length; i++){
				var m = meses[i];
				if(fechaIso >= dmyToIso(m.fec_ini) && fechaIso <= dmyToIso(m.fec_fin)) return m;
			}
		}
		return null;
	}

	/* En modo "Calendario abierto": si Tipo de mes es "Mes de pago"
	   (calendario anual), la fecha elegida se manda tal cual -- no hay
	   nada que mostrar aquí. Si es "Mes de comisión" (calendario
	   comercial), cada fecha se ajusta al periodo comercial que la
	   contiene ("se va al más próximo") -- se muestra el rango real
	   resultante para que quede claro qué se va a usar realmente,
	   mismo criterio que aplica el backend en obtenerDatosReporte(). */
	function actualizarRangoRealAbierto(){
		if(modoFechaActivo !== 'abierto') return;

		var caja   = document.getElementById('mfRangoRealPeriodo');
		var te     = document.getElementById('mfTipoMes').value;
		var fDesde = document.getElementById('mfFechaDesde').value;
		var fHasta = document.getElementById('mfFechaHasta').value;

		if(te === 'ep' || !fDesde || !fHasta){
			caja.style.display = 'none';
			return;
		}

		var pIni = buscarPeriodoQueContiene(fDesde);
		var pFin = buscarPeriodoQueContiene(fHasta);

		if(!pIni || !pFin){
			caja.style.display = 'none';
			return;
		}

		caja.textContent = 'Se ajusta al periodo comercial que contiene cada fecha -- Rango real: ' + pIni.fec_ini + ' al ' + pFin.fec_fin;
		caja.style.display = 'block';
	}

	/* Centraliza la selección de tipo de filtro (SC/SubBrocker/
	   Comisionista/Gerencia/NAP/Folio), botón + estado + UI de abajo.
	   Si el tipo elegido es el filtro fijo del nivel del usuario, se
	   arma el chip ya resuelto (nombre de su gerencia, etc.) y no se
	   deja tocar -- ni buscador ni "✕" para quitarlo (ver renderChipFiltro). */
	function seleccionarFiltroTipo(tipo){

		document.querySelectorAll('.mf-tipo-btn').forEach(function(b){
			b.classList.toggle('activo', b.dataset.tipo === tipo);
		});
		filtroTipoActivo = tipo;
		filtroValor = null;

		var esFolio = (filtroTipoActivo === 'Folio');
		document.getElementById('mfFechasWrap').style.display   = esFolio ? 'none' : 'block';
		document.getElementById('mfTipoMesWrap').style.display  = esFolio ? 'none' : 'block';
		document.getElementById('mfOpcionesWrap').style.display = (esFolio || !PUEDE_VER_OPCIONES) ? 'none' : 'block';
		document.getElementById('mfBuscadorInput').placeholder  = esFolio ? 'Escribe el número de folio...' : 'Escribe para buscar...';

		if(REP_FILTRO_FIJO && tipo === REP_FILTRO_FIJO){
			filtroValor = { etiqueta: REP_VALOR_FIJO, valor: REP_VALOR_FIJO };
			renderChipFiltro();
			document.getElementById('mfBuscadorWrap').style.display = 'none';
			document.getElementById('mfDropdown').style.display = 'none';
			return;
		}

		renderChipFiltro();
		document.getElementById('mfBuscadorWrap').style.display = 'block';
		document.getElementById('mfBuscadorInput').value = '';
		document.getElementById('mfDropdown').style.display = 'none';
		document.getElementById('mfBuscadorInput').focus();

	}

	/* ESC cierra los modales */
	document.addEventListener('keydown', function(e){
		if(e.key === 'Escape'){
			cerrarConstructor();
			cerrarModalFiltros();
			cerrarModalFicha();
		}
	});

	/* ============================================
	CAMPOS DISPONIBLES (izquierda, árbol agrupado por tema:
	Personales, Saldos, Fechas, etc. — ver ORDEN_GRUPOS arriba)
	============================================ */

	function pintarCamposDisponibles(filtroTexto){

		var lista    = document.getElementById('mcCamposList');
		var grupos   = {};
		var orden    = [];
		var buscando = !!filtroTexto;

		Object.keys(CAMPOS_REPORTE).forEach(function(key){
			var campo = CAMPOS_REPORTE[key];
			if(filtroTexto && campo.label.toLowerCase().indexOf(filtroTexto) === -1) return;
			if(!grupos[campo.grupo]){ grupos[campo.grupo] = []; orden.push(campo.grupo); }
			grupos[campo.grupo].push({ key:key, label:campo.label });
		});

		orden.sort(function(a, b){
			var ia = ORDEN_GRUPOS.indexOf(a); if(ia === -1) ia = 999;
			var ib = ORDEN_GRUPOS.indexOf(b); if(ib === -1) ib = 999;
			return ia - ib;
		});

		lista.innerHTML = '';

		if(orden.length === 0){
			lista.innerHTML = '<p style="color:#94a3b8;font-size:13px;padding:10px;text-align:center;">Sin resultados</p>';
			return;
		}

		orden.forEach(function(grupo){

			var colapsado = !buscando && !!gruposColapsados[grupo];

			var titulo = document.createElement('div');
			titulo.className = 'mc-campos-group-title' + (colapsado ? ' colapsado' : '');
			titulo.innerHTML =
				'<span class="chevron">▾</span>' +
				'<span class="grupo-icono">' + (GRUPO_ICONOS[grupo] || '📁') + '</span>' +
				'<span class="grupo-nombre">' + grupo + '</span>' +
				'<span class="grupo-count">' + grupos[grupo].length + '</span>';

			var cuerpo = document.createElement('div');
			cuerpo.className = 'mc-campos-group-body' + (colapsado ? ' colapsado' : '');

			titulo.addEventListener('click', function(){
				gruposColapsados[grupo] = !gruposColapsados[grupo];
				titulo.classList.toggle('colapsado');
				cuerpo.classList.toggle('colapsado');
			});

			grupos[grupo].forEach(function(campo){

				var item = document.createElement('div');
				item.className = 'mc-campo-item';
				item.draggable = true;
				item.dataset.campo = campo.key;
				if(camposActuales.indexOf(campo.key) !== -1) item.classList.add('activo');
				item.innerHTML = '<span class="drag-icon">⠿</span><span>' + campo.label + '</span>';

				item.addEventListener('dragstart', function(e){
					e.dataTransfer.setData('campo', campo.key);
				});

				item.addEventListener('click', function(){ agregarColumna(campo.key); });

				cuerpo.appendChild(item);

			});

			lista.appendChild(titulo);
			lista.appendChild(cuerpo);

		});

	}

	/* ============================================
	CONSTRUCTOR
	============================================ */

	function abrirConstructor(camposIniciales){

		camposActuales = [];
		resetTabla();
		pintarCamposDisponibles();

		var inicial = camposIniciales;

		if(!inicial){
			var guardados = null;
			try{ guardados = JSON.parse(localStorage.getItem(LS_KEY) || 'null'); } catch(e){ guardados = null; }
			inicial = (guardados && guardados.length) ? guardados : CAMPOS_DEFAULT;
		}

		inicial.forEach(function(key){ if(CAMPOS_REPORTE[key]) agregarColumna(key, true); });

		document.getElementById('modalConstructor').style.display = 'flex';
		document.body.style.overflow = 'hidden';

	}

	/* ============================================
	REPORTES GUARDADOS (cards del landing)
	============================================ */

	function obtenerConfigsGuardadas(){
		try{ return JSON.parse(localStorage.getItem(LS_KEY_CONFIGS) || '[]'); } catch(e){ return []; }
	}

	function guardarConfigsGuardadas(lista){
		try{ localStorage.setItem(LS_KEY_CONFIGS, JSON.stringify(lista)); } catch(e){}
	}

	function pintarConfigsGuardadas(){

		var cont  = document.getElementById('rvGridReportes');
		var lista = obtenerConfigsGuardadas();

		cont.innerHTML = '';

		/* Card "+ Nuevo reporte" siempre primero, mismo tamaño que las demás */
		var cardNueva = document.createElement('div');
		cardNueva.className = 'rv-card rv-card-nuevo';
		cardNueva.innerHTML = '<div class="rv-card-plus">+</div><h3>Nuevo reporte</h3>';
		cardNueva.addEventListener('click', function(){
			configActivaId = null;
			abrirConstructor();
		});
		cont.appendChild(cardNueva);

		/* Card "Buscar por folio" -- acceso directo a la ficha de un
		   folio puntual, sin pasar por el constructor de reporte ni el
		   modal de filtros (que ya no ofrece "Folio" como botón porque
		   vive aquí). Reutiliza abrirFichaFolio(), la misma función que
		   ya usa el doble clic sobre una fila de resultados. */
		var cardFolio = document.createElement('div');
		cardFolio.className = 'rv-card rv-card-nuevo rv-card-folio';
		cardFolio.innerHTML = '<div class="rv-card-plus">🔎</div><h3>Buscar por folio</h3>';
		cardFolio.addEventListener('click', buscarPorFolioDirecto);
		cont.appendChild(cardFolio);

		lista.forEach(function(cfg){

			var card = document.createElement('div');
			card.className = 'rv-card';
			card.innerHTML =
				'<button class="rv-card-borrar" title="Eliminar">✕</button>' +
				'<h3>' + escaparHtmlRV(cfg.nombre) + '</h3>' +
				'<p>' + cfg.campos.length + ' campo(s) — clic para construir reporte</p>';

			card.addEventListener('click', function(){ abrirConfigGuardada(cfg.id); });

			card.querySelector('.rv-card-borrar').addEventListener('click', function(e){
				e.stopPropagation();
				borrarConfigGuardada(cfg.id);
			});

			cont.appendChild(card);

		});

	}

	function escaparHtmlRV(texto){
		var d = document.createElement('div');
		d.textContent = texto == null ? '' : String(texto);
		return d.innerHTML;
	}

	function abrirConfigGuardada(id){
		var lista = obtenerConfigsGuardadas();
		var cfg   = lista.find(function(c){ return c.id === id; });
		if(!cfg) return;
		configActivaId = id;
		abrirConstructor(cfg.campos);
	}

	function borrarConfigGuardada(id){
		Swal.fire({
			icon:'warning',
			title:'¿Eliminar este reporte guardado?',
			showCancelButton:true,
			confirmButtonText:'Sí, eliminar',
			cancelButtonText:'Cancelar',
			confirmButtonColor:'#ef4444',
		}).then(function(resultado){
			if(!resultado.isConfirmed) return;
			var lista = obtenerConfigsGuardadas().filter(function(c){ return c.id !== id; });
			guardarConfigsGuardadas(lista);
			if(configActivaId === id) configActivaId = null;
			pintarConfigsGuardadas();
		});
	}

	function guardarConfigComo(){

		if(camposActuales.length === 0){
			Swal.fire({ icon:'warning', title:'Oops', text:'Agrega al menos un campo antes de guardar' });
			return;
		}

		var lista        = obtenerConfigsGuardadas();
		var configActual = lista.find(function(c){ return c.id === configActivaId; });

		Swal.fire({
			title:'Guardar reporte',
			input:'text',
			inputLabel:'Nombre del reporte',
			inputValue: configActual ? configActual.nombre : '',
			inputPlaceholder:'Ej. Comisionistas activos',
			showCancelButton:true,
			confirmButtonText:'Guardar',
			cancelButtonText:'Cancelar',
			inputValidator:function(valor){
				if(!valor || !valor.trim()) return 'Ponle un nombre al reporte';
			}
		}).then(function(resultado){

			if(!resultado.isConfirmed) return;

			var nombre = resultado.value.trim();

			if(configActivaId && configActual){
				configActual.nombre = nombre;
				configActual.campos = camposActuales.slice();
			} else {
				configActivaId = 'cfg_' + Date.now() + '_' + Math.floor(Math.random() * 1000);
				lista.push({ id:configActivaId, nombre:nombre, campos:camposActuales.slice() });
			}

			guardarConfigsGuardadas(lista);
			pintarConfigsGuardadas();

			Swal.fire({ icon:'success', title:'Guardado', timer:1200, showConfirmButton:false });

		});

	}

	function cerrarConstructor(){
		document.getElementById('modalConstructor').style.display = 'none';
		document.body.style.overflow = '';
	}

	function initDropzone(){
		var dz = document.getElementById('mcDropzone');
		dz.addEventListener('dragover', function(e){ e.preventDefault(); dz.classList.add('drag-over'); });
		dz.addEventListener('dragleave', function(){ dz.classList.remove('drag-over'); });
		dz.addEventListener('drop', function(e){
			e.preventDefault();
			dz.classList.remove('drag-over');
			var campo = e.dataTransfer.getData('campo');
			if(campo) agregarColumna(campo);
		});
	}

	function resetTabla(){
		document.getElementById('mcThead').innerHTML = '';
		document.getElementById('mcTbody').innerHTML = '';
		document.getElementById('mcTabla').style.display = 'none';
		document.getElementById('mcPlaceholder').style.display = 'flex';
		document.getElementById('mcResumen').textContent = '';
		document.getElementById('mcBtnExportar').style.display = 'none';
	}

	function guardarSeleccionLS(){
		try{ localStorage.setItem(LS_KEY, JSON.stringify(camposActuales)); } catch(e){}
	}

	function agregarColumna(campoKey, silencioso){

		if(!CAMPOS_REPORTE[campoKey]) return;
		if(camposActuales.indexOf(campoKey) !== -1) return;

		camposActuales.push(campoKey);

		document.getElementById('mcPlaceholder').style.display = 'none';
		document.getElementById('mcTabla').style.display = 'table';

		var thead = document.getElementById('mcThead');
		var tbody = document.getElementById('mcTbody');

		var th = document.createElement('th');
		th.dataset.campo = campoKey;
		th.draggable = true;
		th.innerHTML = '<div class="col-header"><label>' + CAMPOS_REPORTE[campoKey].label +
			'</label><button class="btn-del-col" onclick="eliminarColumna(\'' + campoKey + '\')">✕</button></div>';

		th.addEventListener('dragstart', function(e){ e.dataTransfer.setData('reordenar', campoKey); e.stopPropagation(); });
		th.addEventListener('dragover',  function(e){ e.preventDefault(); });
		th.addEventListener('drop', function(e){
			e.preventDefault(); e.stopPropagation();
			var origen = e.dataTransfer.getData('reordenar');
			if(origen && origen !== campoKey) reordenarColumna(origen, campoKey);
		});

		thead.appendChild(th);

		var tr = tbody.querySelector('tr') || document.createElement('tr');
		if(!tbody.querySelector('tr')) tbody.appendChild(tr);

		var td = document.createElement('td');
		td.dataset.campo = campoKey;
		td.style.color = '#94a3b8';
		td.textContent = '—';
		tr.appendChild(td);

		var itemIzq = document.querySelector('.mc-campo-item[data-campo="' + campoKey + '"]');
		if(itemIzq) itemIzq.classList.add('activo');

		if(!silencioso){
			guardarSeleccionLS();
			document.getElementById('mcResumen').textContent = camposActuales.length + ' columna(s) seleccionada(s)';
		}

	}

	function eliminarColumna(campoKey){

		camposActuales = camposActuales.filter(function(c){ return c !== campoKey; });

		var th = document.querySelector('#mcThead [data-campo="' + campoKey + '"]');
		var td = document.querySelector('#mcTbody [data-campo="' + campoKey + '"]');
		if(th) th.remove();
		if(td) td.remove();

		var itemIzq = document.querySelector('.mc-campo-item[data-campo="' + campoKey + '"]');
		if(itemIzq) itemIzq.classList.remove('activo');

		if(document.getElementById('mcThead').children.length === 0) resetTabla();

		guardarSeleccionLS();
		document.getElementById('mcResumen').textContent = camposActuales.length + ' columna(s) seleccionada(s)';

	}

	function quitarTodosCampos(){
		camposActuales.slice().forEach(function(c){ eliminarColumna(c); });
	}

	function restaurarCamposDefault(){
		quitarTodosCampos();
		CAMPOS_DEFAULT.forEach(function(key){ agregarColumna(key); });
	}

	function reordenarColumna(origenKey, destinoKey){

		var thead = document.getElementById('mcThead');
		var tbody = document.getElementById('mcTbody');

		var thO = thead.querySelector('[data-campo="' + origenKey  + '"]');
		var thD = thead.querySelector('[data-campo="' + destinoKey + '"]');
		var tdO = tbody.querySelector('[data-campo="' + origenKey  + '"]');
		var tdD = tbody.querySelector('[data-campo="' + destinoKey + '"]');

		if(thO && thD) thead.insertBefore(thO, thD);
		if(tdO && tdD) tbody.querySelector('tr').insertBefore(tdO, tdD);

		camposActuales = [];
		thead.querySelectorAll('[data-campo]').forEach(function(th){ camposActuales.push(th.dataset.campo); });

		guardarSeleccionLS();

	}

	/* ============================================
	FILTROS
	============================================ */

	function renderChipFiltro(){
		var wrap = document.getElementById('mfChipWrap');
		wrap.innerHTML = '';
		if(!filtroValor) return;
		var chip = document.createElement('div');
		chip.className = 'mf-chip';
		/* Filtro fijo por nivel: no se deja quitar (no lleva botón "✕"),
		   para que no pueda vaciarlo y buscar sin ese filtro puesto. */
		var esFijoActivo = (REP_FILTRO_FIJO && filtroTipoActivo === REP_FILTRO_FIJO);
		chip.innerHTML = '<span>' + filtroValor.etiqueta + '</span>' +
			(esFijoActivo ? '' : '<button onclick="quitarChipFiltro()">✕</button>');
		wrap.appendChild(chip);
	}

	function quitarChipFiltro(){
		filtroValor = null;
		renderChipFiltro();
	}

	async function buscarFiltroValor(termino){

		var drop = document.getElementById('mfDropdown');
		drop.innerHTML = '<div class="ac-empty">Buscando...</div>';
		drop.style.display = 'block';

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'buscarFiltro', tipo:filtroTipoActivo, termino:termino })
			});

			var json = await res.json();

			if(json.status !== 'ok' || json.data.length === 0){
				drop.innerHTML = '<div class="ac-empty">Sin resultados</div>';
				return;
			}

			drop.innerHTML = '';
			json.data.forEach(function(item){
				var el = document.createElement('div');
				el.className = 'ac-item';
				el.textContent = item.etiqueta;
				el.addEventListener('mousedown', function(){
					filtroValor = { etiqueta:item.etiqueta, valor:item.valor };
					renderChipFiltro();
					document.getElementById('mfBuscadorInput').value = '';
					drop.style.display = 'none';
				});
				drop.appendChild(el);
			});

		} catch(e){
			drop.innerHTML = '<div class="ac-empty">Error de búsqueda</div>';
		}

	}

	function limpiarFiltros(){
		document.querySelectorAll('.mf-tipo-btn').forEach(function(b){ b.classList.remove('activo'); });
		filtroTipoActivo = '';
		filtroValor = null;
		renderChipFiltro();
		document.getElementById('mfBuscadorWrap').style.display = 'none';
		document.getElementById('mfFechaDesde').value = '';
		document.getElementById('mfFechaHasta').value = '';
		document.getElementById('mfTipoMes').value = '';
		document.getElementById('mfSfp').checked = false;
		document.getElementById('mfSrm0').checked = false;
		document.getElementById('mfRegEsp').checked = false;
		document.getElementById('mfFechasWrap').style.display   = 'block';
		document.getElementById('mfTipoMesWrap').style.display  = 'block';
		document.getElementById('mfOpcionesWrap').style.display = PUEDE_VER_OPCIONES ? 'block' : 'none';
		seleccionarModoFecha('abierto');
		cargarPeriodos();
	}

	function cerrarModalFiltros(){
		document.getElementById('modalFiltros').style.display = 'none';
	}

	function fechaADMY(iso){
		if(!iso) return '';
		var p = iso.split('-');
		if(p.length !== 3) return '';
		return p[2] + '/' + p[1] + '/' + p[0];
	}

	async function aplicarFiltros(){

		if(!filtroTipoActivo){
			Swal.fire({ icon:'warning', title:'Oops', text:'Elige por qué quieres buscar (Socio Comercial, SubBrocker, etc.)' });
			return;
		}

		if(!filtroValor){
			var msgFalta = (filtroTipoActivo === 'Folio') ? 'Escribe el número de folio' : 'Selecciona un valor de la lista de búsqueda';
			Swal.fire({ icon:'warning', title:'Oops', text:msgFalta });
			return;
		}

		/* Folio no lleva rango de fechas (busca UN registro puntual).
		   Fuera de Folio, el modo Periodo/Calendario abierto lo decide
		   seleccionarModoFecha() (independiente del Tipo de mes) -- en
		   Calendario abierto + Mes de comisión, el backend ajusta cada
		   fecha al periodo comercial que la contiene (ver
		   obtenerDatosReporte en seg_ventas_reporteador_funciones.php). */
		var esFolio     = (filtroTipoActivo === 'Folio');
		var usarPeriodo = (!esFolio && modoFechaActivo === 'periodo');

		var fDesde  = document.getElementById('mfFechaDesde').value;
		var fHasta  = document.getElementById('mfFechaHasta').value;
		var anioIni = document.getElementById('mfAnioIni').value;
		var mesIni  = document.getElementById('mfMesIni').value;
		var anioFin = document.getElementById('mfAnioFin').value;
		var mesFin  = document.getElementById('mfMesFin').value;

		if(!esFolio && !usarPeriodo && (!fDesde || !fHasta)){
			Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona el rango de fechas' });
			return;
		}

		if(usarPeriodo && (!anioIni || !mesIni || !anioFin || !mesFin)){
			Swal.fire({ icon:'warning', title:'Oops', text:'Selecciona el año y mes inicial, y el año y mes final' });
			return;
		}

		/* En modo periodo, valor2/valor3 (fechas exactas) van vacíos y en
		   su lugar se manda anio_ini/mes_i/anio_fin/mes_f -- el backend
		   (obtenerDatosReporte) ya sabe resolver esas fechas contra
		   fechas_cierre / fechas_cierre_com según "te". */
		var payload = {
			action:  'generarReporte',
			filtro:  filtroTipoActivo,
			valor1:  filtroValor.valor,
			valor2:  (esFolio || usarPeriodo) ? '' : fechaADMY(fDesde),
			valor3:  (esFolio || usarPeriodo) ? '' : fechaADMY(fHasta),
			anio_ini: usarPeriodo ? anioIni : '',
			mes_i:    usarPeriodo ? mesIni  : '',
			anio_fin: usarPeriodo ? anioFin : '',
			mes_f:    usarPeriodo ? mesFin  : '',
			te:      esFolio ? '' : document.getElementById('mfTipoMes').value,
			sfp:     (!esFolio && document.getElementById('mfSfp').checked) ? 1 : 0,
			srm0:    (!esFolio && document.getElementById('mfSrm0').checked) ? 1 : 0,
			reg_esp: (!esFolio && document.getElementById('mfRegEsp').checked) ? 1 : 0,
			/* Folio pide TODOS los campos de una sola vez (los de la
			   tabla Y los de la ficha). El backend de Folio ya es lento
			   de por sí (ver obtenerDatosReporte) -- antes se disparaba
			   esa misma consulta pesada DOS veces (una para la tabla,
			   otra nada más para abrir la ficha); pidiendo todo de una
			   vez se abre la ficha con los mismos datos, sin repetir
			   la consulta. */
			campos:  esFolio ? Object.keys(CAMPOS_REPORTE) : camposActuales
		};

		cerrarModalFiltros();

		Swal.fire({ title:'Generando reporte...', allowOutsideClick:false, didOpen:function(){ Swal.showLoading(); } });

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify(payload)
			});

			var json = await res.json();

			Swal.close();

			if(json.status !== 'ok'){
				Swal.fire({ icon:'error', title:'Error', text:json.message || json.mysql || 'No se pudo generar el reporte' });
				return;
			}

			if(esFolio && json.data && json.data.length > 0){

				var filaCompleta = json.data[0];
				var folioAuto     = filaCompleta._folio || filaCompleta.folio || filtroValor.valor;

				/* La tabla de resultados solo debe mostrar las columnas
				   que el usuario eligió (camposActuales), aunque acabamos
				   de pedir TODOS los campos al backend -- se arma una
				   copia filtrada nada más para pintar la tabla. */
				var columnasVista = camposActuales.map(function(key){
					return { key:key, label:(CAMPOS_REPORTE[key] ? CAMPOS_REPORTE[key].label : key) };
				});
				var filaVista = {};
				columnasVista.forEach(function(col){ filaVista[col.key] = filaCompleta[col.key]; });
				filaVista._folio = folioAuto;

				mostrarResultados({ status:'ok', columnas:columnasVista, data:[filaVista], limitado:false, message:json.message });

				/* Buscar por Folio es una búsqueda puntual de UN registro --
				   no tiene caso obligar a doble clic sobre la única fila que
				   va a salir. Se abre la ficha de una vez, con los datos que
				   YA llegaron (sin segunda consulta al backend). */
				document.getElementById('mfiTitulo').textContent = 'Folio ' + folioAuto;
				document.getElementById('mfiSubtitulo').textContent = 'Cargando información...';
				document.getElementById('mfiBody').innerHTML =
					'<div class="mfi-loading"><div class="mfi-spinner"></div><span>Cargando información...</span></div>';
				document.getElementById('modalFicha').style.display = 'flex';
				document.body.style.overflow = 'hidden';
				renderFicha(folioAuto, filaCompleta);

			} else {
				mostrarResultados(json);
			}

		} catch(e){
			Swal.close();
			Swal.fire({ icon:'error', title:'Error', text:e.message });
		}

	}

	/* ============================================
	RESULTADOS
	============================================ */

	var ultimoResultado = null;

	function mostrarResultados(json){

		ultimoResultado = json;

		var tbody = document.getElementById('mcTbody');
		tbody.innerHTML = '';

		document.getElementById('mcPlaceholder').style.display = 'none';
		document.getElementById('mcTabla').style.display = 'table';
		document.getElementById('mcDerechoTitle').textContent = 'Resultados';
		document.getElementById('mcBtnExportar').style.display = 'inline-block';

		var msg = json.data.length + ' registro(s)';
		if(json.limitado) msg += ' — mostrando el tope de 1,000 en pantalla, usa "Exportar a Excel" para el reporte completo';
		document.getElementById('mcResumen').textContent = msg;

		if(json.data.length === 0){
			var trV = document.createElement('tr');
			var tdV = document.createElement('td');
			tdV.colSpan = json.columnas.length;
			tdV.style.cssText = 'text-align:center;color:#94a3b8;padding:30px;';
			/* json.message trae la razón puntual del backend (p.ej. "Sin
			   resultados para estos filtros." o, si algo falló armando el
			   reporte, el error de SQL) -- mostrarla en vez de un genérico
			   siempre igual, para poder diagnosticar sin abrir la consola. */
			tdV.textContent = json.message || 'Sin resultados para estos filtros.';
			trV.appendChild(tdV);
			tbody.appendChild(trV);
			document.getElementById('mcResumen').textContent = json.message || msg;
			return;
		}

		json.data.forEach(function(row){
			var tr = document.createElement('tr');
			tr.title = 'Doble clic para ver el detalle completo';
			json.columnas.forEach(function(col){
				var td = document.createElement('td');
				var val = row[col.key];
				td.textContent = (val !== null && val !== undefined && val !== '') ? val : '—';
				tr.appendChild(td);
			});
			/* _folio viaja siempre en cada fila (ver seg_ventas_reporteador_api.php)
			   sin importar si "Folio" está entre las columnas elegidas -- es lo
			   que permite abrir la ficha completa aunque el usuario no haya
			   agregado esa columna a su reporte. */
			tr.addEventListener('dblclick', function(){
				abrirFichaFolio(row._folio || row.folio || '');
			});
			tbody.appendChild(tr);
		});

	}

	function exportarReporte(){

		if(!filtroTipoActivo || !filtroValor){
			Swal.fire({ icon:'info', title:'Genera el reporte primero', text:'Usa "Filtrar y generar" antes de exportar.' });
			return;
		}

		var esFolioExp     = (filtroTipoActivo === 'Folio');
		var usarPeriodoExp = (!esFolioExp && modoFechaActivo === 'periodo');

		var params = new URLSearchParams({
			filtro:      filtroTipoActivo,
			valor1:      filtroValor.valor,
			valor2:      (esFolioExp || usarPeriodoExp) ? '' : fechaADMY(document.getElementById('mfFechaDesde').value),
			valor3:      (esFolioExp || usarPeriodoExp) ? '' : fechaADMY(document.getElementById('mfFechaHasta').value),
			anio_ini:    usarPeriodoExp ? document.getElementById('mfAnioIni').value : '',
			mes_i:       usarPeriodoExp ? document.getElementById('mfMesIni').value  : '',
			anio_fin:    usarPeriodoExp ? document.getElementById('mfAnioFin').value : '',
			mes_f:       usarPeriodoExp ? document.getElementById('mfMesFin').value  : '',
			te:          esFolioExp ? '' : document.getElementById('mfTipoMes').value,
			sfp:         (!esFolioExp && document.getElementById('mfSfp').checked) ? 1 : 0,
			srm0:        (!esFolioExp && document.getElementById('mfSrm0').checked) ? 1 : 0,
			reg_esp:     (!esFolioExp && document.getElementById('mfRegEsp').checked) ? 1 : 0,
			campos_csv:  camposActuales.join(',')
		});

		window.open('seg_ventas_reporteador_excel.php?' + params.toString(), '_blank');

	}

	/* ============================================
	MODAL FICHA (detalle completo de un folio)

	Reutiliza el mismo endpoint/acción generarReporte, pero con
	filtro=Folio y pidiendo TODOS los campos que el nivel del usuario
	puede ver (no solo los que eligió mostrar en su reporte) -- así la
	ficha siempre sale completa sin importar qué tan chico sea el
	reporte que el usuario armó. No hace falta backend nuevo: la
	búsqueda puntual por Folio ya existe y ya trae las 53 columnas de
	sv resueltas.
	============================================ */

	function cerrarModalFicha(){
		document.getElementById('modalFicha').style.display = 'none';
	}

	/* Card "Buscar por folio" del landing -- pide el folio con un
	   SweetAlert y abre la ficha directo (abrirFichaFolio ya hace todo
	   lo necesario: abre #modalFicha, pide generarReporte con
	   filtro=Folio y pinta la información, sin necesidad de abrir el
	   constructor de reporte). */
	function buscarPorFolioDirecto(){
		/* Todo el encabezado (icono + título + subtítulo) va en 'html'
		   en vez de usar la opción 'title' de SweetAlert -- así el
		   ícono queda ARRIBA del título, no entre título e input (que
		   es el orden que SweetAlert arma cuando 'title' y 'html' se
		   usan juntos). */
		Swal.fire({
			html: '<div class="swal-folio-icon">🔎</div>' +
			      '<div class="swal-folio-title">Buscar por folio</div>' +
			      '<div class="swal-folio-sub">Escribe el número de folio para abrir su ficha directo.</div>',
			input: 'text',
			inputPlaceholder: 'Número de folio...',
			showCancelButton: true,
			reverseButtons: true,
			confirmButtonText: 'Buscar',
			cancelButtonText: 'Cancelar',
			confirmButtonColor: '#185FA5',
			cancelButtonColor: '#94a3b8',
			customClass: {
				popup:         'swal-folio-popup',
				confirmButton: 'swal-folio-btn-confirm',
				cancelButton:  'swal-folio-btn-cancel'
			},
			inputValidator: function(value){
				if(!value || !value.trim()) return 'Escribe un número de folio';
			}
		}).then(function(resultado){
			if(!resultado.isConfirmed) return;
			abrirFichaFolio(resultado.value);
		});
	}

	async function abrirFichaFolio(folio){

		folio = (folio || '').toString().trim();
		if(!folio) return;

		document.getElementById('mfiTitulo').textContent = 'Folio ' + folio;
		document.getElementById('mfiSubtitulo').textContent = 'Cargando información...';
		document.getElementById('mfiBody').innerHTML =
			'<div class="mfi-loading"><div class="mfi-spinner"></div><span>Cargando información...</span></div>';
		document.getElementById('modalFicha').style.display = 'flex';
		document.body.style.overflow = 'hidden';

		try{

			var res = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({
					action:  'generarReporte',
					filtro:  'Folio',
					valor1:  folio,
					valor2:  '', valor3:  '', te: '',
					sfp: 0, srm0: 0, reg_esp: 0,
					campos:  Object.keys(CAMPOS_REPORTE)
				})
			});

			var json = await res.json();

			if(json.status !== 'ok' || !json.data || json.data.length === 0){
				document.getElementById('mfiSubtitulo').textContent = '';
				document.getElementById('mfiBody').innerHTML =
					'<div class="mfi-vacio-msg"><span style="font-size:34px;">🔍</span><span>' +
					escaparHtmlRV(json.message || 'No se encontró información para este folio.') + '</span></div>';
				return;
			}

			renderFicha(folio, json.data[0]);

		} catch(e){
			document.getElementById('mfiSubtitulo').textContent = '';
			document.getElementById('mfiBody').innerHTML =
				'<div class="mfi-vacio-msg"><span style="font-size:34px;">⚠️</span><span>Error al cargar la ficha: ' +
				escaparHtmlRV(e.message) + '</span></div>';
		}

	}

	/* Layout FIJO (no genérico por 'grupo' del catálogo): cada dato
	   siempre cae en el mismo lugar -- franja de estatus arriba, bloque
	   de datos en 3 columnas + panel lateral, y dos franjas abajo
	   (dinero / fechas). Así alguien que ya conoce la ficha encuentra
	   el dato de un vistazo sin tener que releer etiquetas de sección
	   que cambian de posición según qué campos traigan información. */
	/* Clasifica un valor de estatus/confirmación en ok (verde) / bad
	   (rojo) / neutral (azul), igual que las pastillas que ya usa
	   seg_cumplimiento.php (badge-estatus st-activo/st-inactivo) --
	   por palabras clave, porque el mismo campo puede traer textos
	   distintos según el módulo de origen (ACEPTADA PROCESAR, Confirmado,
	   Pagado, Si, Rechazado, etc.). */
	function pillClase(v){
		var t = (v || '').toString().toUpperCase();
		if(t === '') return 'mfi-pill-vacio';
		if(/RECHAZ|CANCEL|INACTIV|^NO$|PENDIENTE/.test(t)) return 'mfi-pill-bad';
		if(/ACEPTAD|CONFIRMAD|PAGAD|ACTIV|^SI$|CONCLUID|IGUAL O MAYOR/.test(t)) return 'mfi-pill-ok';
		return 'mfi-pill-neutral';
	}

	function renderFicha(folio, fila){

		var nombre = fila.trabajador || fila.nombre || '';
		document.getElementById('mfiTitulo').textContent =
			(nombre ? nombre.toUpperCase() + ' ' : '') + '(' + folio + ')';
		document.getElementById('mfiSubtitulo').textContent =
			[fila.estatus, fila.movimiento].filter(Boolean).join(' · ');

		/* Badge de categoría del cliente (ej. "Cotizante"), igual que en
		   seg_ventas.php -- ver .mfi-badge-categoria arriba. Solo se
		   muestra si el folio trae dato en movilidad.categoria_cliente. */
		var elCategoria = document.getElementById('mfiCategoria');
		if(fila.categoria_cliente){
			elCategoria.textContent = fila.categoria_cliente;
			elCategoria.style.display = 'inline-flex';
		} else {
			elCategoria.textContent = '';
			elCategoria.style.display = 'none';
		}

		function valorTxt(key){
			var v = fila[key];
			return (v !== null && v !== undefined && v !== '') ? v : '';
		}

		/* Campo normal: tarjeta blanca, label arriba + valor abajo. */
		function campoHtml(key, labelOverride){
			var meta  = CAMPOS_REPORTE[key];
			var label = labelOverride || (meta ? meta.label : key);
			var v     = valorTxt(key);
			var clase = 'mfi-valor' + (v === '' ? ' mfi-vacio' : '');
			return '<div class="mfi-campo"><label>' + escaparHtmlRV(label) + '</label>' +
					'<div class="' + clase + '">' + (v === '' ? '—' : escaparHtmlRV(v)) + '</div></div>';
		}

		/* Campo de dinero: misma tarjeta, pero el número sale grande y
		   de color (azul saldos/anticipos, verde si se pasa 'verde'),
		   igual que SALDO TOTAL/BONO del Detalle de Cumplimiento. */
		function campoMoneyHtml(key, labelOverride, verde){
			var meta  = CAMPOS_REPORTE[key];
			var label = labelOverride || (meta ? meta.label : key);
			var v     = valorTxt(key);
			var clase = 'mfi-campo mfi-campo-money' + (verde ? ' mfi-money-verde' : '');
			var claseVal = 'mfi-valor' + (v === '' ? ' mfi-vacio' : '');
			return '<div class="' + clase + '"><label>' + escaparHtmlRV(label) + '</label>' +
					'<div class="' + claseVal + '">' + (v === '' ? '—' : escaparHtmlRV(v)) + '</div></div>';
		}

		/* Campo de estatus: misma tarjeta, pero el valor es una pastilla
		   de color en vez de texto plano. */
		function campoPillHtml(key, labelOverride){
			var meta  = CAMPOS_REPORTE[key];
			var label = labelOverride || (meta ? meta.label : key);
			var v     = valorTxt(key);
			/* Sin dato: se ve como cualquier otro campo vacío (guioncito
			   gris en texto plano) -- una pastilla de color redondeada
			   solo para decir "—" se veía como un badge roto/hueco. */
			if(v === ''){
				return '<div class="mfi-campo"><label>' + escaparHtmlRV(label) + '</label><div class="mfi-valor mfi-vacio">—</div></div>';
			}
			return '<div class="mfi-campo"><label>' + escaparHtmlRV(label) + '</label>' +
					'<span class="mfi-pill ' + pillClase(v) + '">' + escaparHtmlRV(v) + '</span></div>';
		}

		/* Franja de estatus: mismo campoPillHtml pero dentro de
		   mfi-status-item (label + pastilla en línea, no apilados). */
		function statusHtml(key, labelOverride){
			var meta  = CAMPOS_REPORTE[key];
			var label = labelOverride || (meta ? meta.label : key);
			var v     = valorTxt(key);
			var clase = pillClase(v);
			return '<div class="mfi-status-item ' + clase + '-acc"><span class="mfi-status-label">' + escaparHtmlRV(label) + '</span>' +
					'<span class="mfi-pill ' + clase + '">' + (v === '' ? '—' : escaparHtmlRV(v)) + '</span></div>';
		}

		var html = '';

		/* Franja de estatus */
		html += '<div class="mfi-status-strip">';
		html += statusHtml('estatus', 'Estatus SF');
		html += statusHtml('estatus_pbi', 'Estatus Procesar');
		html += statusHtml('confirma', 'Confirmación');
		html += statusHtml('cierre_cap', 'Cierre captura');
		html += '</div>';

		/* Bloque principal: columnas de tarjetas + panel lateral.
		   Orden fijo pedido: 1) Identificación/Movimiento igual que
		   siempre, 2) AV, 3) Estructura comercial, 4) Observaciones,
		   5) Campaña y movilidad igual que siempre.
		   Identificación + AV + Observaciones van dentro de
		   .mfi-left-group (su propia mini-grid de 2 columnas) para que
		   Observaciones se pegue justo debajo de esas dos, sin esperar
		   a que termine Estructura comercial (que es más alta y vive
		   en su propia columna, fuera del grupo). */
		html += '<div class="mfi-main">';

		html += '<div class="mfi-left-group">';

		html += '<div class="mfi-col">';
		html += '<div class="mfi-col-grid">';
		html += campoHtml('movimiento');
		html += campoHtml('nss');
		html += campoHtml('curp');
		html += campoHtml('nap');
		html += campoHtml('no_oportunidad', 'Oportunidad');
		html += campoHtml('afore', 'Afore cedente');
		html += '</div>';
		html += '</div>';

		html += '<div class="mfi-col">';
		html += campoHtml('folio_av', 'Folio AV');
		html += campoPillHtml('estatus_av', 'Estatus AV');
		html += campoHtml('fec_envio', 'Fec. envío AV');
		html += '</div>';

		/* Observaciones va dentro del mismo grupo, mismo ancho que las
		   dos tarjetas de arriba (grid-column:1/3 en .mfi-col-wide). */
		html += '<div class="mfi-col mfi-col-wide">';
		html += '<div class="mfi-col-grid">';
		html += campoHtml('observa', 'Observaciones');
		html += campoHtml('descrip', 'Descripción (extemporáneo)');
		html += '</div>';
		html += '</div>';

		html += '</div>'; // .mfi-left-group

		html += '<div class="mfi-col">';
		html += campoHtml('sc', 'Socio Comercial');
		html += campoHtml('brocker', 'SubBrocker');
		html += campoHtml('gerencia');
		html += campoHtml('analista', 'Analista (captura)');
		html += campoHtml('nombre', 'Comisionista');
		html += '</div>';

		html += '<div class="mfi-side">';
		html += campoHtml('campana', 'Campaña');
		html += campoHtml('fec_traspaso', 'Fec. últ. traspaso');
		/* Ley y Cotiza (tabla movilidad), igual que en seg_ventas.php --
		   ahí se muestran dentro de esta misma tarjeta -- van en su
		   propia mini-grid de 2 columnas (.mfi-side-grid) para que
		   quepan lado a lado sin desbordarse fuera de la tarjeta
		   angosta. Van ARRIBA de Movilidad (pedido explícito). */
		html += '<div class="mfi-side-grid">';
		html += campoHtml('ley', 'Ley');
		html += campoPillHtml('cotiza', 'Cotiza');
		html += '</div>';
		html += campoPillHtml('movilidad', 'Movilidad');
		html += campoHtml('cap_movilidad', 'Actualizado al');
		html += '</div>';

		html += '</div>'; // .mfi-main

		/* Franja de dinero -- números grandes de color (azul anticipos,
		   verde saldos), igual que SALDO TOTAL/BONO. */
		html += '<div class="mfi-strip mfi-strip-money">';
		html += campoPillHtml('est_anticipo', 'Estatus anticipo');
		html += campoMoneyHtml('monto_anticipo', 'Anticipo');
		html += campoMoneyHtml('monto_seg_ant', 'Segundo ant.');
		html += campoMoneyHtml('saldo_estimado', 'Saldo estimado', true);
		html += campoMoneyHtml('saldo_real', 'Saldo real', true);
		html += campoMoneyHtml('saldo_pago', 'Saldo pago', true);
		html += campoHtml('mes', 'Mes pago');
		html += '</div>';

		/* Franja de fechas */
		html += '<div class="mfi-strip">';
		html += campoHtml('fec_captura', 'Captura');
		html += campoHtml('fec_anticipo', 'Anticipo');
		html += campoHtml('fec_ant2', 'Segundo ant.');
		html += campoHtml('fec_traspaso', 'Traspaso');
		html += campoHtml('fec_cierre', 'Cierre');
		html += campoHtml('fec_rechazo', 'Rechazo');
		html += campoHtml('fec_pago', 'Pago');
		html += campoHtml('fec_desc', 'Desc. incumpl.');
		html += '</div>';

		document.getElementById('mfiBody').innerHTML = html;

	}

	/* ============================================
	MODAL PERMISOS DE CAMPOS SENSIBLES (admin-only, ES_ADMIN)

	Flujo: primero se busca/elige al USUARIO (buscador con dropdown,
	igual patrón que los demás buscadores de la página), y una vez
	elegido se pintan TODOS los campos sensibles como checkboxes --
	cada uno se guarda al instante al marcarlo/desmarcarlo (sin botón
	de guardar aparte). Abajo queda un resumen de quién ya tiene
	alguna excepción activa, para no tener que ir usuario por usuario
	a adivinar. Todo pasa por seg_ventas_reporteador_api.php, que
	revalida nivel==12 en el servidor -- este modal ni siquiera se
	pinta en el HTML si $esAdmin es falso (ver PHP arriba).
	============================================ */

	var permUsuarioActual = null; // { clave, nombre }

	function abrirModalPermisos(){
		document.getElementById('modalPermisos').style.display = 'flex';
		document.getElementById('permUsuarioSeleccionado').style.display = 'none';
		document.getElementById('permUsuarioBuscador').value = '';
		permUsuarioActual = null;
		cargarResumenPermisos();
	}

	function cerrarModalPermisos(){
		document.getElementById('modalPermisos').style.display = 'none';
	}

	(function(){
		var timer = null;
		var input = document.getElementById('permUsuarioBuscador');
		var drop  = document.getElementById('permUsuarioDropdown');
		if(!input) return;

		input.addEventListener('input', function(){
			var termino = this.value.trim();
			clearTimeout(timer);
			if(termino.length < 1){ drop.style.display = 'none'; return; }
			timer = setTimeout(function(){ buscarUsuarioPermiso(termino, drop); }, 250);
		});

		input.addEventListener('blur', function(){
			setTimeout(function(){ drop.style.display = 'none'; }, 200);
		});
	})();

	async function buscarUsuarioPermiso(termino, drop){

		drop.innerHTML = '<div class="ac-empty">Buscando...</div>';
		drop.style.display = 'block';

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'buscarUsuarioPermiso', termino:termino })
			});
			var json = await res.json();

			if(json.status !== 'ok' || json.data.length === 0){
				drop.innerHTML = '<div class="ac-empty">Sin resultados</div>';
				return;
			}

			drop.innerHTML = '';
			json.data.forEach(function(u){
				var el = document.createElement('div');
				el.className = 'ac-item';
				el.textContent = u.nombre;
				el.addEventListener('mousedown', function(){
					drop.style.display = 'none';
					document.getElementById('permUsuarioBuscador').value = '';
					seleccionarUsuarioPermisos(u.clave, u.nombre);
				});
				drop.appendChild(el);
			});

		} catch(e){
			drop.innerHTML = '<div class="ac-empty">Error de búsqueda</div>';
		}

	}

	async function seleccionarUsuarioPermisos(clave, nombre){

		permUsuarioActual = { clave:clave, nombre:nombre };

		document.getElementById('permUsuarioSeleccionado').style.display = 'block';
		document.getElementById('permUsuarioNombre').textContent = nombre;

		var cont = document.getElementById('permCamposCheckbox');
		cont.innerHTML = '<span style="color:#94a3b8;font-size:13px;">Cargando...</span>';

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'getPermisosUsuario', clave:clave })
			});
			var json = await res.json();

			if(json.status !== 'ok'){
				cont.innerHTML = '<span style="color:#ef4444;font-size:13px;">' + escaparHtmlRV(json.message || 'Error al cargar') + '</span>';
				return;
			}

			pintarCheckboxPermisos(json.campos || []);

		} catch(e){
			cont.innerHTML = '<span style="color:#ef4444;font-size:13px;">Error: ' + escaparHtmlRV(e.message) + '</span>';
		}

	}

	function pintarCheckboxPermisos(camposActivos){

		var cont = document.getElementById('permCamposCheckbox');
		cont.innerHTML = '';

		CAMPOS_SENSIBLES.forEach(function(campoKey){

			var label = CAMPOS_REPORTE[campoKey] ? CAMPOS_REPORTE[campoKey].label : campoKey;
			var activo = camposActivos.indexOf(campoKey) !== -1;

			var lab = document.createElement('label');
			var chk = document.createElement('input');
			chk.type = 'checkbox';
			chk.checked = activo;
			chk.dataset.campo = campoKey;

			chk.addEventListener('change', function(){
				if(this.checked){ otorgarPermiso(campoKey, this); }
				else{ revocarPermiso(campoKey, this); }
			});

			lab.appendChild(chk);
			lab.appendChild(document.createTextNode(' ' + label));
			cont.appendChild(lab);

		});

	}

	async function otorgarPermiso(campo, checkboxEl){

		if(!permUsuarioActual) return;

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'otorgarPermisoCampo', campo:campo, clave:permUsuarioActual.clave })
			});
			var json = await res.json();

			if(json.status !== 'ok'){
				Swal.fire({ icon:'error', title:'Error', text:json.message || 'No se pudo otorgar el permiso' });
				if(checkboxEl) checkboxEl.checked = false;
				return;
			}

			cargarResumenPermisos();

		} catch(e){
			Swal.fire({ icon:'error', title:'Error', text:e.message });
			if(checkboxEl) checkboxEl.checked = false;
		}

	}

	async function revocarPermiso(campo, checkboxEl){

		if(!permUsuarioActual) return;

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'revocarPermisoCampo', campo:campo, clave:permUsuarioActual.clave })
			});
			var json = await res.json();

			if(json.status !== 'ok'){
				Swal.fire({ icon:'error', title:'Error', text:json.message || 'No se pudo revocar el acceso' });
				if(checkboxEl) checkboxEl.checked = true;
				return;
			}

			cargarResumenPermisos();

		} catch(e){
			Swal.fire({ icon:'error', title:'Error', text:e.message });
			if(checkboxEl) checkboxEl.checked = true;
		}

	}

	/* Resumen de abajo: quién tiene alguna excepción activa, agrupado
	   por usuario (listarPermisosCampos regresa agrupado por CAMPO, así
	   que aquí se invierte a agrupado por USUARIO para mostrarlo). Clic
	   en un renglón vuelve a abrir a esa persona arriba en los checkboxes. */
	async function cargarResumenPermisos(){

		var cont = document.getElementById('permResumenLista');
		cont.innerHTML = '<span style="color:#94a3b8;font-size:13px;">Cargando...</span>';

		try{

			var res  = await fetch(API_REPORTEADOR, {
				method:'POST',
				headers:{'Content-Type':'application/json'},
				body:JSON.stringify({ action:'listarPermisosCampos' })
			});
			var json = await res.json();

			if(json.status !== 'ok'){
				cont.innerHTML = '<span style="color:#ef4444;font-size:13px;">' + escaparHtmlRV(json.message || 'Error al cargar') + '</span>';
				return;
			}

			var porUsuario = {}; // clave -> { nombre, campos:[label,...] }

			CAMPOS_SENSIBLES.forEach(function(campoKey){
				var label = CAMPOS_REPORTE[campoKey] ? CAMPOS_REPORTE[campoKey].label : campoKey;
				(json.data[campoKey] || []).forEach(function(u){
					if(!porUsuario[u.clave]) porUsuario[u.clave] = { nombre:u.nombre, campos:[] };
					porUsuario[u.clave].campos.push(label);
				});
			});

			var claves = Object.keys(porUsuario);
			cont.innerHTML = '';

			if(claves.length === 0){
				cont.innerHTML = '<span style="color:#94a3b8;font-size:12px;">Nadie tiene excepciones activas todavía</span>';
				return;
			}

			claves.forEach(function(clave){
				var u = porUsuario[clave];
				var row = document.createElement('div');
				row.style.cssText = 'display:flex;justify-content:space-between;align-items:center;background:#f8fafc;border:1px solid #e2e8f0;border-radius:10px;padding:8px 12px;cursor:pointer;font-size:13px;';
				row.innerHTML = '<span><strong>' + escaparHtmlRV(u.nombre) + '</strong> — ' + escaparHtmlRV(u.campos.join(', ')) + '</span>';
				row.addEventListener('click', function(){ seleccionarUsuarioPermisos(clave, u.nombre); });
				cont.appendChild(row);
			});

		} catch(e){
			cont.innerHTML = '<span style="color:#ef4444;font-size:13px;">Error: ' + escaparHtmlRV(e.message) + '</span>';
		}

	}

</script>
</html>