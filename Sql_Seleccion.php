<?php 



//echo  "HOLAAAAAAAAAAAAAAA";
//return 0;

    header('Content-Type: application/json; Charset=UTF-8');
	//$feha_con_zona=date_default_timezone_set('America/New_York');
	$hora=date('h:i:s a', time()); 
	$fecha=date('Y-m-d');
	//$feha_con_zona=date_default_timezone_get();
	
	function dia_en_letra($fecha) {
		$dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
		$dia = $dias[date('w', strtotime($fecha))];
		return $dia;
    }
	
 
	$pgcon = pg_connect(
    "host=" . getenv('DB_HOST') .
    " port=5432" .
    " dbname=" . getenv('DB_NAME') .
    " user=" . getenv('DB_USER') .
    " password=" . getenv('DB_PASS') .
    " sslmode=" . getenv('DB_SSLMODE')
) or die("Error de conexión: " . pg_last_error());
	
	//Tiempo desde PosgreeeSQL****************************************************************
			$sql="select current_date as fecha, extract(hour from  current_timestamp) as hora,
				  extract(minute from  current_timestamp ) as minuto,
				  extract(second  from  current_timestamp) as segundo"; 
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar la fecha".pg_last_error());
			
			while ($row = pg_fetch_array($query))
					{ 
		
						$fecha_pg=$row["fecha"];
						$hora_pg=$row["hora"];	
						$minuto_pg=$row["minuto"];
						$segundo_pg=$row["segundo"];				 
					}
			$hora_pg_completa=$hora_pg.":".$minuto_pg.":".strstr($segundo_pg, ".", true);	
			
			
			//abrir_loteria($fecha, $hora_pg_completa, $pgcon);
			//cerrar_loteria($fecha, $hora_pg_completa, $pgcon);
			
	
	
			
			
	//**********************************************************************************************
	
	
	
	//buscar articulos inventario****************************************************************
		 if ($_POST["accion"]=="Buscar_Articulos_Inventario"){	
	
			//$codigo_sucursal=$_POST["codigo_sucursal"];
			//$nombre_articulo=$_POST["nombre"];
			//$codigo_categoria=$_POST["codigo_categoria"];
			//$codigo_usuario=$_POST["codigo_usuario"];
		 
			$sql="select sa.existencia,a.descripcion, 
					sa.costo,(sa.costo::numeric *existencia)as costo_total, 
					sa.precio1, 
					(sa.precio1::numeric *sa.existencia)as precio_total, 
					(sa.precio1::numeric *sa.existencia)-(sa.costo::numeric *existencia)as beneficios
					from 
					inventario.articulo a, 
					articulo_sucursal sa
					where 
					a.codigo_art=sa.codigo_articulo and 
					sa.precio1>0 and 
					sa.costo>0 "; 
				/*	
					if(empty($codigo_sucursal)==false or $codigo_sucursal!=0){
				      $sql=$sql." and sa.codigo_sucursal=".$codigo_sucursal;
			        }
		
			       if(empty($codigo_categoria)==false or $codigo_categoria!=0){
				    $sql=$sql." and a.codigocategoria='".$codigo_categoria."'";
			       }
				   
				    if(empty($codigo_usuario)==false or $codigo_usuario!=0){
				    $sql=$sql." and a.codigo_usuario='".$codigo_usuario."'";
			       }
				   
				    if(empty($nombre_articulo)==false){
				    $sql=$sql." and a.descripcion  like '%".$nombre_articulo."%'";
			       }*/
					
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			
			$clave=0;
			$articulo_inventario =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$articulo_inventario[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("articulo_inventario"=>$articulo_inventario));
			}
		}
					
	//**********************************************************************************************
	
	  
         			
		//buscar cuota****************************************************************
		 if ($_POST["accion"]=="Buscar_Cuota"){	
	
			$codigo_venta=$_POST["codigo_venta"];
		
		 
			$sql="select * from cxc.cxc where saldo > 0.1 and codigo_venta='".$codigo_venta."' order by fecha_cxc asc"; 
				
					
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Cuotas".pg_last_error());
			
			$clave=0;
			$cuotas =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cuotas[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("cuotas"=>$cuotas));
			}
		}
					
	//**********************************************************************************************
		
	
	//Cuadre****************************************************************
		 if ($_POST["accion"]=="Cuadre"){	
	
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
				   
		  /* 
		  $sql="select *
					from 
					cuadre_caja_ where caja_abierta=false ";
					
						   
	
			
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
			    }else{
				  $sql=$sql." and fecha='".$fecha."'";
				}
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			$clave=0;
			$cuadre =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cuadre[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("cuadre"=>$cuadre));
			}
				}*/
				
				  $sql3="select *
					from 
					facturacion.desembolso where monto > 0  ";
				    $sql3=$sql3." and fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
			 
	
					$query3 = pg_query($pgcon, $sql3) or die("Problema al buscar Articulos".pg_last_error());
					$clave3=0;
					$cuadre3 =null;
					while ($row = pg_fetch_array($query3))
					{ 
						$clave3++;
						$cuadre3[$clave3]=$row;					 
					}
				
				
				
				
				
				  $sql="select fecha_venta, 
				               coalesce(total_venta,0.0::numeric) as total_venta,  
							   coalesce(efectivo,0.0::numeric) as efectivo,  
							   coalesce(credito,0.0::numeric) as credito, 
							   coalesce(tarjeta,0.0::numeric)as tarjeta,  
							   coalesce(cheque,0.0::numeric) as cheque,  
							   coalesce(abonos_efectivo,0.0::numeric) as abonos_efectivo,  
							   COALESCE(NULLIF(desembolso_efectivo, ''), 0.0::numeric) as desembolso_efectivo
					from 
					vista_cuadre_general where total_venta>0 ";
					
				  $sql=$sql." and fecha_venta BETWEEN '".$fecha."' and '".$fecha2."' ";
			 
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			$clave=0;
			$cuadre =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cuadre[$clave]=$row;					 
	     	}
			
			
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("cuadre"=>$cuadre, "desembolso"=>$cuadre3));
			}
				
				
		}
					
	//**********************************************************************************************
	
	
	
	//CBUSSCAR COBROS****************************************************************
		 if ($_POST["accion"]=="BUSCAR_FACTURA_COBRADA"){	
		
	
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
			$cliente=$_POST["cliente"];
				  
				
				  $sql="select * from cxc.grupo_cobro gc, cxc.cliente c 
                        where c.codigo_cli=gc.codigo_cliente and gc.total>0  ";
					
				 if(empty($fecha)==false){
				
					if(empty($fecha2)==false){
					  $sql=$sql." and gc.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
					}else{
					  $sql=$sql." and gc.fecha='".$fecha."'";
					}
			    }
				
				 if(empty($cliente)==false){
				
					  $sql=$sql." and nombre_clie like '%".$cliente."%' ";
			     }
			 
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Cobro".pg_last_error());
			$clave=0;
			$cobro =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cobro[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("factura_cobrada"=>$cobro));
			}
				
				
		}
					
	//**********************************************************************************************
	
	//CBUSSCAR COBROS FATURAS DETALLADA****************************************************************
		 if ($_POST["accion"]=="VER_FACTURA_COBRADA"){	
		
	
			$codigo_cobro=$_POST["codigo_cobro"];
		
				  
				
				  $sql="select * from cxc.cobros c , grupo_cobro gc, cliente cl
         				where cl.codigo_cli= gc.codigo_cliente and 
         				gc.secuencia=c.secuencia_grupo and  gc.secuencia='".$codigo_cobro."'  ";
					

			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Cobro en la tabla cobros".pg_last_error());
			$clave=0;
			$cobro =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cobro[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("factura_cobrada"=>$cobro));
			}
				
				
		}
					
	//**********************************************************************************************
	
	//BUSCAR ENTRADA****************************************************************
		 if ($_POST["accion"]=="Buscar_Entrada_Modificar"){	
	
			$fecha=$_POST["fecha1"];
			$fecha2=$_POST["fecha2"];
				   
	
				
				  $sql="select e.fecha, e.total, e.costo_art, e.cantidad_art, e.codigo_art, e.codigo, a.descripcion
					from 
					inventario.entrada e , inventario.articulo a where e.codigo_art=a.codigo_art and e.cantidad_art > 0 ";
					
				  $sql=$sql." and e.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
			 
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			$clave=0;
			$buscar_entrada =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_entrada[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_entrada"=>$buscar_entrada));
			}
				
				
		}
					
	//**********************************************************************************************
	
	//BUSCAR SALIDA****************************************************************
		 if ($_POST["accion"]=="Buscar_Salida_Modificar"){	
	
			$fecha=$_POST["fecha1"];
			$fecha2=$_POST["fecha2"];
				   
	
				
				  $sql="select e.fecha, e.total, e.costo_art, e.cantidad_art, e.codigo_art, e.codigo, a.descripcion
					from 
					inventario.salida e , inventario.articulo a where e.codigo_art=a.codigo_art and e.cantidad_art > 0 ";
					
				  $sql=$sql." and e.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
			 
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar salida".pg_last_error());
			$clave=0;
			$buscar_entrada =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_entrada[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_salida"=>$buscar_entrada));
			}
				
				
		}
					
	//**********************************************************************************************
	
	//BUSCAR CLIENTE PARA COBRAR****************************************************************
		 if ($_POST["accion"]=="Buscar_Cliente_Con_Venta_Credito"){	
		  
		 $sql=" select * from cxc.cliente where balance>0 ";
		
		 if ($_POST["clientes_en_cobro_o_en_venta_por_cliente"]=="1")
		 {
			  $sql=" select * from cxc.cliente ";
		 }
		 
		   
				  
				  if(empty($_POST["datos_cliente"])==false){
					    $sql=$sql." and nombre_clie like '%".strtoupper($_POST["datos_cliente"])."%' ";
					  }
				
			$query = pg_query($pgcon, $sql) or die("Problema al buscar cliente a credito".pg_last_error());
			$clave=0;
			$buscar_cliente =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_cliente[$clave]=$row;					 
	     	}
			
			
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_cliente_credito"=>$buscar_cliente));
			}
			return 0;	
				
		}
					
	//**********************************************************************************************
	
	//BUSCAR VENTA A CREDITO PARA COBRAR****************************************************************
		 if ($_POST["accion"]=="Buscar_Venta_Credito_Cobrar"){	
		   
		  
				  $sql=" select * from venta where codigo_tipo_ven=2 and saldo_ven>0  ";
				  
				  
				   if ($_POST["tipo_venta"]=="1"){
					    $sql=" select * from venta where codigo_tipo_ven=1 and total_ven>0";
					   }	
					   
				    if ($_POST["tipo_venta"]=="3"){
						 $sql=" select * from venta where  total_ven>0 and codigo_tipo_ven=2 and saldo_ven=0   ";
						}	
				   
				   if ($_POST["tipo_venta"]=="4"){
						 $sql=" select * from venta where  total_ven>0 and codigo_tipo_ven=2 and saldo_ven>0   ";
						}	
						
						 if(empty($_POST["fecha_desde"])==false){
						  $sql=$sql." and fecha_venta BETWEEN '".$_POST["fecha_desde"]."' and '".$_POST["fecha_hasta"]."' ";
						}
						
					
						
				    $sql=$sql." and codigo_cli = '".$_POST["codigo_cliente"]."' ";
				  
				    $sql=$sql." order by fecha_venta desc ";
				  
				 
				
			$query = pg_query($pgcon, $sql) or die("Problema al buscar cliente a credito".pg_last_error());
			$clave=0;
			$buscar_venta_crediro =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_venta_crediro[$clave]=$row;					 
	     	}
			
			
			
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_venta_credito"=>$buscar_venta_crediro));
			}
			return 0;	
				
		}
					
	//**********************************************************************************************
	
	//BUSCAR ENTRADA VER****************************************************************
		 if ($_POST["accion"]=="Buscar_Entrada_Ver"){	
	
			$fecha=$_POST["fecha1"];
			$fecha2=$_POST["fecha2"];
				   
	
				
				  $sql="select 
						avg( extract(second  from  e.fecha)) as fecha , 
						sum(e.total) as total, 
						sum(e.costo_art) as  costo_art, 
						sum(e.cantidad_art) as cantidad_art, 
						sum(e.codigo_art) as codigo_art, 
						sum(e.codigo) as codigo, 
						a.descripcion
											
						from 				
						inventario.entrada e, 
						inventario.articulo a 
						
						where 
						e.codigo_art=a.codigo_art  ";
					
				  
				  
				  $sql=$sql." and e.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
				  $sql=$sql." group by e.codigo_art, a.descripcion ";
			 
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			$clave=0;
			$buscar_entrada =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_entrada[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_entrada"=>$buscar_entrada));
			}
				
				
		}
					
	//**********************************************************************************************
	
	//BUSCAR SALIDA VER****************************************************************
		 if ($_POST["accion"]=="Buscar_Salida_Ver"){	
	
			$fecha=$_POST["fecha1"];
			$fecha2=$_POST["fecha2"];
				   
	
				
				  $sql="select 
						avg( extract(second  from  e.fecha)) as fecha , 
						sum(e.total) as total, 
						sum(e.costo_art) as  costo_art, 
						sum(e.cantidad_art) as cantidad_art, 
						sum(e.codigo_art) as codigo_art, 
						sum(e.codigo) as codigo, 
						a.descripcion
											
						from 				
						inventario.salida e, 
						inventario.articulo a 
						
						where 
						e.codigo_art=a.codigo_art  ";
					
				  
				  
				  $sql=$sql." and e.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
				  $sql=$sql." group by e.codigo_art, a.descripcion ";
			 
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar salida grupos".pg_last_error());
			$clave=0;
			$buscar_entrada =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_entrada[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_salida"=>$buscar_entrada));
			}
				
				
		}
					
	//**********************************************************************************************
	
	//Operacion Venta Actual****************************************************************
		 if ($_POST["accion"]=="Acceder_Operaciones_Venta_Actual"){	
				 
				 // Esta forma es para la tabla cuadre caja  
		  /* $sql=' select 
					sum(case when codigo_tipo_ven=1 then total_ven else 0 end) as "efectivo",
					sum(case when codigo_tipo_ven=2 then total_ven else 0 end) as "credito",
					sum(case when codigo_tipo_ven=3 then total_ven else 0 end) as "targeta",
					
					(select sum(case when gc.codigo_tipo_venta=1 then gc.total else 0 end) 
					from grupo_cobro gc where gc.codigo_cuadre in 
					(select codigo from cuadre_caja where caja_abierta=TRUE)) as "cobrada_efectivo",
					
					(select sum(case when gc.codigo_tipo_venta=2 then gc.total else 0 end) 
					from grupo_cobro gc where gc.codigo_cuadre in 
					(select codigo from cuadre_caja where caja_abierta=TRUE)) as "cobrada_credito",
					
					(select sum(case when gc.codigo_tipo_venta=3 then gc.total else 0 end) 
					from grupo_cobro gc where gc.codigo_cuadre in 
					(select codigo from cuadre_caja where caja_abierta=TRUE)) as "cobrada_targeta",
					
					(select sum(total) 
					from grupo_cobro gc where gc.codigo_cuadre in 
					(select codigo from cuadre_caja where caja_abierta=TRUE)) as "total_cobrada",
					
					sum(case when codigo_tipo_ven=1 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=2 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=3 then total_ven else 0 end) as "total_venta",
					
					sum(case when codigo_tipo_ven=1 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=2 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=3 then total_ven else 0 end)+
					(select sum(total) from grupo_cobro where codigo_cuadre in 
					(select codigo from cuadre_caja where caja_abierta=TRUE)) as "total_general",
					
					(select monto from desembolso where codigo_cuadre in 
					(select codigo_cuadre from cuadre_caja where caja_abierta=TRUE)) as "total_desembolso"
					
				from 
					facturacion.venta 
				where 
					codigo_cuadre 
				in 
					(select codigo from cuadre_caja where caja_abierta=TRUE) ';*/
					
					// esta forma es solo con fecha
					
					  $sql3="select fecha, monto, concepto, codigo_usuario, hora, codigo
					from 
					facturacion.desembolso where monto > 0  ";
				    $sql3=$sql3." and fecha ='".$fecha_pg."' ";
			 
	
					$query3 = pg_query($pgcon, $sql3) or die("Problema al buscar desembolso".pg_last_error());
					$clave3=0;
					$cuadre3 =null;
					while ($row = pg_fetch_array($query3))
					{ 
						$clave3++;
						$cuadre3[$clave3]=$row;					 
					}
					
				
				
					
					
				$sql='select 
					sum(case when codigo_tipo_ven=1 then total_ven else 0 end) as "efectivo",
					sum(case when codigo_tipo_ven=2 then total_ven else 0 end) as "credito",
					sum(case when codigo_tipo_ven=3 then total_ven else 0 end) as "targeta",
					
					coalesce((select sum(case when gc.codigo_tipo_venta=1 then gc.total else 0 end)
					from grupo_cobro gc where gc.fecha='."'".$fecha_pg."'".'   ),0) as "cobrada_efectivo"  ,
					
					coalesce((select sum(case when gc.codigo_tipo_venta=2 then gc.total else 0 end) 
					from grupo_cobro gc where gc.fecha='."'".$fecha_pg."'".' ),0)as "cobrada_credito" ,
					
					coalesce((select sum(case when gc.codigo_tipo_venta=3 then gc.total else 0 end)
					from grupo_cobro gc where gc.fecha='."'".$fecha_pg."'".'  ),0)as "cobrada_targeta" ,
					
					coalesce((select sum(total) 
					from grupo_cobro gc where gc.fecha='."'".$fecha_pg."'".' ),0)as "total_cobrada"  ,
					
					coalesce(sum(case when codigo_tipo_ven=1 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=2 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=3 then total_ven else 0 end),0) as "total_venta",
					
					coalesce(sum(case when codigo_tipo_ven=1 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=2 then total_ven else 0 end) +
					sum(case when codigo_tipo_ven=3 then total_ven else 0 end)+
					(select sum(total)  from grupo_cobro where fecha='."'".$fecha_pg."'".' ),0) as "total_general",
					
					coalesce((select sum(monto)  from desembolso where fecha='."'".$fecha_pg."'".' ),0) as "total_desembolso" 
					
				from    
					facturacion.venta fv
				where 
					fv.fecha_venta='."'".$fecha_pg."'".' ';	
	
		
			  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			$clave=0;
			$operaciones_venta_actual =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$operaciones_venta_actual[$clave]=$row;					 
	     	}
			
			
			echo json_encode(array("operaciones_venta_actual"=>$operaciones_venta_actual));
		
		}
					
	//**********************************************************************************************
	
	
	
	//Desembolso en Operacion Venta Actual****************************************************************
		 if ($_POST["accion"]=="Lista_Desembolso_Operaciones"){	
				 
			
					  $sql3="select fecha, monto, concepto, codigo_usuario, hora, codigo
					from 
					facturacion.desembolso where monto > 0  ";
				    $sql3=$sql3." and fecha ='".$fecha_pg."' ";
			 
	
					$query3 = pg_query($pgcon, $sql3) or die("Problema al buscar desembolso".pg_last_error());
					$clave3=0;
					$cuadre3 =null;
					while ($row = pg_fetch_array($query3))
					{ 
						$clave3++;
						$cuadre3[$clave3]=$row;					 
					}
					
			
			if($clave3==0){
				echo 0;
				}else{
			     echo json_encode(array("desembolso"=>$cuadre3));
				}
		
		}
					
	//**********************************************************************************************
	
	//buscar CXC Clientes****************************************************************
		 if ($_POST["accion"]=="Buscar_cxc_cliente"){	
	
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
				   
		   $sql="select c.codigo_cli, c.nombre_clie,
		   			sum(v.saldo_ven) as saldo, correo_cli, c.direccion_cli
					from 
					venta v, cliente c 
					where 
					v.codigo_cli=c.codigo_cli and
					v.total_ven>0 and v.saldo_ven>0  ";
	
			
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and v.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."' ";
			    }else{
				  $sql=$sql." and v.fecha_venta='".$fecha."'";
				}
			}
			 $sql=$sql." group by  c.codigo_cli";
			
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			
			$clave=0;
			$cxc_cliente =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cxc_cliente[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("cxc_cliente"=>$cxc_cliente));
			}
		}
					
	//**********************************************************************************************
	
	//buscar Clientes****************************************************************
		 if ($_POST["accion"]=="Buscar_Cliente"){	
	
			$cliente=$_POST["cliente"];
			
				   
		   $sql="select  *
                 from cliente where codigo_cli>1   ";
	
			
			if(empty($cliente)==false){
				  $sql=$sql." and (lower(REPLACE(trim
				  				(coalesce(nombre_clie,'') ||''|| 
								coalesce(contacto_cli,'') ||''|| 
								coalesce(rnc,'') ||''|| 
								codigo_cli),' ',''))) like '%".str_replace(" ","",trim(strtolower($cliente)))."%'";
			}
			
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar cliente".pg_last_error());
			
			$clave=0;
			$buscar_cliente =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_cliente[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_cliente"=>$buscar_cliente));
			}
		}
					
	//**********************************************************************************************
	
		//buscar Empleado****************************************************************
		 if ($_POST["accion"]=="Buscar_empleado"){	
	
			$empleado=$_POST["empleado"];
			
				   
		   $sql="select  *
                 from empleado where activo=true  ";
	
			
			if(empty($empleado)==false){
				  $sql=$sql." and (lower(REPLACE(trim
				  				(coalesce(nombre,'') ||''|| 
								coalesce(cargo,'') ||''|| 
								coalesce(cedula,'') ||''|| 
								codigo),' ',''))) like '%".str_replace(" ","",trim(strtolower($empleado)))."%'";
			}
			
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar cliente".pg_last_error());
			
			$clave=0;
			$buscar_empleado =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_empleado[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_empleado"=>$buscar_empleado));
			}
		}
					
	//**********************************************************************************************
	
	
	
	//buscar articulos****************************************************************
		 if ($_POST["accion"]=="Buscar_Articulos"){	
	
			$articulos=$_POST["articulos"];
			
				   
		   $sql="select (a.presentacion) as pre,* from articulo a, articulo_sucursal ars
				where a.codigo_art=ars.codigo_articulo and 
				descripcion is not null and ars.codigo_sucursal is not null   ";
	
			
			if(empty($articulos)==false){
				  $sql=$sql." and (lower(REPLACE(trim
				  				(a.descripcion),' ',''))) like '%".str_replace(" ","",trim(strtolower($articulos)))."%'";
			}
			$sql=$sql." order by a.descripcion asc  limit 50";
			
			
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			
			$clave=0;
			$buscar_articulos =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_articulos[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_articulos"=>$buscar_articulos));
			}
		}
					
	//**********************************************************************************************
	
	//buscar lista venta vendedor****************************************************************
		 if ($_POST["accion"]=="Buscar_Lista_Venta_Vendedor"){	
	
			
			$fecha1=$_POST["fecha1"];
			$fecha2=$_POST["fecha2"];
			$codigo_empleado=$_POST["codigo_empleado"];
			
			if ($_POST["tipo_busqueda"]==2){
				   
				   $sql="select 
								e.nombre,
								a.descripcion,
								sum (va.cantidad_art) as cantidad_vendido,
								avg (va.precio_art) as precio_art,
								sum(va.precio_art*va.cantidad_art) as total         		
						 From 
							   contabilidad.empleado e, 
							   facturacion.venta v,
							   venta_art va,
							   articulo a  
						 where
							   e.codigo=v.codigo_vendedor and
							   va.secuencia=v.secuencia  and
							   a.codigo_art=va.codigo_art
						  ";
			
					
					$sql=$sql." and v.fecha_venta BETWEEN '".$fecha1."' and '".$fecha2."' ";
					
					if(empty($codigo_empleado)==false){
						  $sql=$sql." and e.codigo ='".$codigo_empleado."' ";
					}
					
			
				
					$sql=$sql." group by  a.codigo_art, e.codigo";
			  }else{ 
			     
				 $sql="select 
								e.nombre,
								a.descripcion,
								va.cantidad_art as cantidad_vendido,
								va.precio_art as precio_art,
								va.precio_art*va.cantidad_art as total         		
						 From 
							   contabilidad.empleado e, 
							   facturacion.venta v,
							   venta_art va,
							   articulo a  
						 where
							   e.codigo=v.codigo_vendedor and
							   va.secuencia=v.secuencia  and
							   a.codigo_art=va.codigo_art
						  ";
			
					
					$sql=$sql." and v.fecha_venta BETWEEN '".$fecha1."' and '".$fecha2."' ";
					
					if(empty($codigo_empleado)==false){
						  $sql=$sql." and e.codigo ='".$codigo_empleado."' ";
					}
			  
			  }
			
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Lista Venta Vendedor".pg_last_error());
			
			$clave=0;
			$buscar_articulos =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_articulos[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("buscar_venta_vendedor"=>$buscar_articulos));
			}
		}
					
	//**********************************************************************************************
	
	//buscar resumen producion****************************************************************
		 if ($_POST["accion"]=="Buscar_Resumen_Produccion"){	
	
			
			$fecha1=$_POST["fecha1"];
			$fecha2=$_POST["fecha2"];
			//$codigo_empleado=$_POST["codigo_empleado"];
			
	//*************************************************************************************************************************************		     
				 $sql="SELECT *
						FROM (     
								select 'Entrada' as Entrada_Salida, 
									    a.descripcion as produccion, sum(e.cantidad_art) as cantidad_art,
									   sum(e.costo_art) as costo_art, sum(e.costo_art*e.cantidad_art) as valor_entrada
									from 
									  entrada e, 
									  articulo a 
								   where  
									  e.codigo_art=a.codigo_art and e.fecha BETWEEN '".$fecha1."' and '".$fecha2."' 
									  group by  a.codigo_art
								union 
								   select 'Salida' as Salida_Entrada,
									   a.descripcion as usados, sum(s.cantidad_art) as cantidad_art,
									  sum(s.costo_art) as costo_art, sum(s.costo_art*s.cantidad_art) as valor_salida
									   
								   from 
									  salida s,
									  articulo a 
								   where  
									  s.codigo_art=a.codigo_art and s.fecha BETWEEN '".$fecha1."' and '".$fecha2."' 
									  group by  a.codigo_art
						) datos order by Entrada_Salida asc 
					  ";
			
				
			
				  
					$query = pg_query($pgcon, $sql) or die("Problema al Resumen Producion".pg_last_error());
					
					$clave=0;
					$buscar_articulos =null;
					while ($row = pg_fetch_array($query))
					{ 
						$clave++;
						$buscar_articulos[$clave]=$row;					 
					}
					if($clave==0){ 
					 echo "0";
					}else{ 
					echo json_encode(array("resumen_produccion"=>$buscar_articulos));
					}
		}
					
	//**********************************************************************************************
	
		//buscar Categoria****************************************************************
		 if ($_POST["accion"]=="Categoria"){	
	
				   
		   $sql="select  *
                 from categoria  ";
	
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Categoria".pg_last_error());
			
			$clave=0;
			$buscar_categoria =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$buscar_categoria[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("categoria"=>$buscar_categoria));
			}
		}
					
	//**********************************************************************************************
	
	//Tipo Venta****************************************************************
		 if ($_POST["accion"]=="Tipo_Venta"){	
	
				   
		   $sql="select*from tipo_compra_venta order by codigo_tipo   ";
	
		
			$query = pg_query($pgcon, $sql) or die("Problema al buscar tipo venta".pg_last_error());
			
			$clave=0;
			$tipoventa =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$tipoventa[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("tipoventa"=>$tipoventa));
			}
		}
					
	//**********************************************************************************************
	
	
	
	//buscar Lista de cobros****************************************************************
		 if ($_POST["accion"]=="BuscarCobros"){	
	
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
				   
		 /*  $sql="select * 
					from 
					cxc.cobros co, cxc.grupo_cobro gc, cxc.cliente cl
					where gc.secuencia=co.secuencia_grupo and 
					gc.codigo_cliente=cl.codigo_cli  ";*/
					
					  $sql="select * 
					from 
					 cxc.grupo_cobro gc, cxc.cliente cl
					where
					gc.codigo_cliente=cl.codigo_cli  ";
	
			
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and gc.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
			    }else{
				  $sql=$sql." and gc.fecha='".$fecha."'";
				}
			}
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			
			$clave=0;
			$cxc_cobros =null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cxc_cobros[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("lista_cobros"=>$cxc_cobros));
			}
		}
					
	//**********************************************************************************************
	
	//Buscar lista Desembolso****************
	 if ($_POST["accion"]=="Lista_desembolso"){
		 	        
					$codigo_usuario=$_POST["codigo_usuario"];
					$codigo_sucursal=$_POST["codigo_sucursal"];
					$concepto=$_POST["concepto"];
					$monto=$_POST["Monto"];
					$fecha=$_POST["Fecha"];
		  
			$sql="select 

					d.fecha, d.concepto, d.monto, d.codigo,
					to_char(d.hora, 'HH12:MI:SS') as hora, u.codigo_usuario, u.nombre_usuario
					
					from desembolso d,  usuario u
					
					where u.codigo_usuario=d.codigo_usuario "; 
					
				
			if(empty($codigo_usuario)==false){
				$sql=$sql." and u.codigo_usuario='".$codigo_usuario."'";
			}
		
			
			if(empty($concepto)==false){
				$sql=$sql." and d.concepto='".$concepto."'";
			}
		
			if(empty($monto)==false){
				$sql=$sql." and d.monto='".$monto."'";
			}
			
			if(empty($fecha)==false){
				$sql=$sql." and d.fecha='".$fecha."'";
			}
					
			$query = pg_query($pgcon, $sql) or die("Problema al buscar desembolso".pg_last_error());
			$clave=0;
			
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$lista_desembolso[$clave]=$row;					 
	     	}
			
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("Lista_Desembolso"=>$lista_desembolso));
			}
			
		 }
	
	//buscar Lista de desembolso****************************************************************
		 if ($_POST["accion"]=="BuscarDesembolso"){	
	
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
				   
		 /*  $sql="select * 
					from 
					cxc.cobros co, cxc.grupo_cobro gc, cxc.cliente cl
					where gc.secuencia=co.secuencia_grupo and 
					gc.codigo_cliente=cl.codigo_cli  ";*/
					
					  $sql="select * 
							from 
 							desembolso d, usuario u
								where 
								d.codigo_usuario=u.codigo_usuario and monto>0  ";
	
			
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and d.fecha BETWEEN '".$fecha."' and '".$fecha2."' ";
			    }else{
				  $sql=$sql." and d.fecha='".$fecha."'";
				}
			}
				  
			$query = pg_query($pgcon, $sql) or die("Problema al buscar Articulos".pg_last_error());
			
			$clave=0;
			$cxc_desembolso=null;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$cxc_desembolso[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("lista_desembolso"=>$cxc_desembolso));
			}
		}
					
	//**********************************************************************************************
	
	
	//Buscar Empresa****************
	
	 if ($_POST["accion"]=="Empresa"){
		 
		  if ($_POST["sql"]=="Buscar"){
			$sql="Select * from public.empresa"; 
			$query = pg_query($pgcon, $sql) or die("Problema al buscar la fecha".pg_last_error());
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$empresa[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("empresa"=>$empresa));
			}
		 
		 }
		 
		  if ($_POST["sql"]=="Actualizar"){
			  
			$sql="update public.empresa set nombre='".$_POST["nombre"]."', direccion='".$_POST["direccion"]."', 
			rnc='".$_POST["rnc"].", ncf='".$_POST["moneda"].", etalle_factura='".$_POST["monto_maximo_quiniela"].", 	            ciudad='".$_POST["monto_maximo_pale"].", email='".$_POST["monto_maximo_triplea"].",            emailsistema='".$_POST["monto_quiniela"].", claveemailsistema='".$_POST["monto_pale"].",smtp='".$_POST["monto_tripleta"].", pie_ticket1='".$_POST["ensaje1"].", 
			pie_ticket2='".$_POST["mensaje2"].", pie_ticket3='".$_POST["mensaje3"].", tipoimpresora='".$_POST["impresora"]."'";
			$query = pg_query($pgcon, $sql) or die("Problema al Actualizar Empresa".pg_last_error());
			
			$sql="Select * from public.empresa"; 
			$query = pg_query($pgcon, $sql) or die("Problema al buscar la fecha".pg_last_error());
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$empresa[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("empresa"=>$empresa));
			}
		 
		 }
	}
	
	//****************
	
	///** Buscar Proveedor Loteria por listados
	    if ($_POST["accion"]=="Proveedor-L"){
		 	
					$sql="Select * from cxp.proveedor where nombre_prov is not null";
			        $sql=$sql." order by codigo_prov asc ";
	
			$query = pg_query($pgcon, $sql) or die("Problema al Insertar Buscar el Proveedor".pg_last_error());
	       
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$proveedor[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("proveedor"=>$proveedor));
			}
	    }
	
	
	///** Buscar Proveedor Loteria
	    if ($_POST["accion"]=="Proveedor"){
			
			$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and nombre_prov is not null";
		  /*
		 	switch (date('w', strtotime($fecha_pg))) {
				case 0:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and domingo = true and nombre_prov is not null";
					break;
				case 1:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and lunes = true and nombre_prov is not null";
					break;
				case 2:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and martes = true and nombre_prov is not null";
					break;
				case 3:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and miercoles = true and nombre_prov is not null";
					break;
				case 4:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and jueves = true and nombre_prov is not null";
					break;
				case 5:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and viernes = true and nombre_prov is not null";
					break;
				case 6:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					     and habilitada = true and status = 'ABIERTO' and sabado = true and nombre_prov is not null";
					  
					break;
			}
			
			if($_POST["datos"]=="Todo_Premio"){
				
				switch (date('w', strtotime($fecha_pg))) {
				case 0:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and domingo = true and nombre_prov is not null";
					break;
				case 1:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and lunes = true and nombre_prov is not null";
					break;
				case 2:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and martes = true and nombre_prov is not null";
					break;
				case 3:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and  miercoles = true and nombre_prov is not null";
					break;
				case 4:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and jueves = true and nombre_prov is not null";
					break;
				case 5:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and viernes = true and nombre_prov is not null";
					break;
				case 6:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and sabado = true and nombre_prov is not null";
					break;
			}
		  }
			
			if($_POST["datos"]=="Solo_Tarde"){
				
				switch (date('w', strtotime($fecha_pg))) {
				case 0:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and domingo = true and nombre_prov is not null";
					break;
				case 1:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and lunes = true and nombre_prov is not null";
					break;
				case 2:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and martes = true and nombre_prov is not null";
					break;
				case 3:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and  miercoles = true and nombre_prov is not null";
					break;
				case 4:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and jueves = true and nombre_prov is not null";
					break;
				case 5:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and viernes = true and nombre_prov is not null";
					break;
				case 6:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and sabado = true and nombre_prov is not null";
					break;
			}
			
			$sql=$sql." and (EXTRACT(hour FROM hora_cierre)*60*60 
								 + EXTRACT(minute FROM hora_cierre)*60 
								 + EXTRACT(second FROM hora_cierre)) < 68800";
		}
			
			if($_POST["datos"]=="Solo_Noche"){
				
				switch (date('w', strtotime($fecha_pg))) {
				case 0:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and domingo = true and nombre_prov is not null";
					break;
				case 1:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and lunes = true and nombre_prov is not null";
					break;
				case 2:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and martes = true and nombre_prov is not null";
					break;
				case 3:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and  miercoles = true and nombre_prov is not null";
					break;
				case 4:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and jueves = true and nombre_prov is not null";
					break;
				case 5:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and viernes = true and nombre_prov is not null";
					break;
				case 6:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and sabado = true and nombre_prov is not null";
					break;
			}
			
			$sql=$sql." and (EXTRACT(hour FROM hora_cierre)*60*60 
								 + EXTRACT(minute FROM hora_cierre)*60 
								 + EXTRACT(second FROM hora_cierre)) > 68800";
			
		}
		
		if($_POST["datos"]=="Tarde_Noche"){
				
				switch (date('w', strtotime($fecha_pg))) {
				case 0:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and domingo = true and nombre_prov is not null";
					break;
				case 1:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and lunes = true and nombre_prov is not null";
					break;
				case 2:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and martes = true and nombre_prov is not null";
					break;
				case 3:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and  miercoles = true and nombre_prov is not null";
					break;
				case 4:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and jueves = true and nombre_prov is not null";
					break;
				case 5:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and viernes = true and nombre_prov is not null";
					break;
				case 6:
					$sql="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true  and sabado = true and nombre_prov is not null";
					break;
			}
			
		}
	*/
			if(empty($codigo_prov)==false){
				$sql=$sql."and codigo_prov=".$codigo_prov;
			}
		
			if(empty($nombre_prov)==false){
				$sql=$sql."and nombre_prov='".$nombre_prov."'";
			}
			/*
			if($_POST["datos"]=="Todo"){
				
				$sql=$sql." and (EXTRACT(hour FROM hora_cierre)*60*60 
								 + EXTRACT(minute FROM hora_cierre)*60 
								 + EXTRACT(second FROM hora_cierre)) > 
								(EXTRACT(hour FROM current_timestamp)*60*60 
								 + EXTRACT(minute FROM current_timestamp)*60 
								 + EXTRACT(second FROM current_timestamp)) ";
			}
			
			*/
			
			$sql=$sql." order by codigo_prov asc ";
	
			$query = pg_query($pgcon, $sql) or die("Problema al Insertar Buscar el Proveedor".pg_last_error());
	       
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$proveedor[$clave]=$row;					 
	     	}
			if($clave==0){ 
			 echo "0";
			}else{ 
			echo json_encode(array("proveedor"=>$proveedor));
			}
	    }
	//*****************************
	
	// Acceder**********************
	 	if ($_POST["accion"]=="Acceder"){
			
			$nombre_usuario=$_POST["usuario"];
			$clave_usuario=$_POST["clave"];
			
			$empleado=null;
			$sql_empleado="select*from contabilidad.empleado ";
			$query_empleado = pg_query($pgcon, $sql_empleado) or die("Problema al Acceder a empleado".pg_last_error());
			$clave_empleado=0;
			while ($row = pg_fetch_array($query_empleado))
			{ 
		    	$clave_empleado++;
				$empleado[$clave_empleado]=$row;					 
	     	}
			
			
			$sucursal=null;
			$sql_sucursal="select*from contabilidad.sucursal ";
			$query_sucursal = pg_query($pgcon, $sql_sucursal) or die("Problema al Acceder a Sucursal".pg_last_error());
			$clave_sucursal=0;
			while ($row = pg_fetch_array($query_sucursal))
			{ 
		    	$clave_sucursal++;
				$sucursal[$clave_sucursal]=$row;					 
	     	}
			
			
		    $categoria=null;
			$sql_categoria="select*from inventario.categoria ";
			$query_categoria = pg_query($pgcon, $sql_categoria) or die("Problema al Acceder a categoria".pg_last_error());
			$clave_categoria=0;
			while ($row = pg_fetch_array($query_categoria))
			{ 
		    	$clave_categoria++;
				$categoria[$clave_categoria]=$row;					 
	     	}
			
			//$sql="Select * from public.usuario u, contabilidad.sucursal c where 
			//u.codigo_usuario>0 and u.activo = TRUE and
            // c.codigo_cli=u.codigo_sucursal ";
			 
			 $sql="Select * from public.usuario u where 
			u.codigo_usuario>0 and u.activo = TRUE  ";
			
	
			if(empty($clave_usuario)==false){
				$sql=$sql." and u.clave='".$clave_usuario."'";
			}
		
			if(empty($nombre_usuario)==false){
				$sql=$sql." and u.nombre_usuario='".$nombre_usuario."'";
			}
	
			$query = pg_query($pgcon, $sql) or die("Problema al Acceder".pg_last_error());
	
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$usuario[$clave]=$row;					 
	     	}
			
			
			$sql_empresa="Select * from public.empresa";
			$query_empresa = pg_query($pgcon, $sql_empresa) or die("Problema al Acceder".pg_last_error());
			$clave_empresa=0;
			
			while ($row = pg_fetch_array($query_empresa))
			{ 
		    	$clave_empresa++;
				$empresa[$clave_empresa]=$row;					 
	     	}
			if($clave==0){
				 echo $clave;
				}else{
			echo json_encode(array("usuario"=>$usuario,"empresa"=>$empresa,"sucursal"=>$sucursal,"categoria"=>$categoria, "empleado"=>$empleado));
				}
			//echo json_encode(array("empresa"=>$empresa));
	 	}

 // Acceder Importar**********************
	 	if ($_POST["accion"]=="AccederImportar"){
			
			$sql=" Select * from public.usuario ";
			
			$query = pg_query($pgcon, $sql) or die("Problema al Acceder Importar".pg_last_error());
	
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	   $clave++;
			   $usuario[$clave]=$row;					 
	     	        }
			
			
			
			echo json_encode(array("usuario"=>$usuario));
                        
		}
			
		
		///** Buscar Venta
	    if ($_POST["accion"]=="Buscar Venta"){
			
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
			
		
			$codigo_usuario=$_POST["codigo_usuario"];
			$codigo_sucursal=$_POST["codigo_sucursal"];
			
			//Linea Original
		 /*	$sql="Select * from facturacion.venta v, public.usuario u where numero_factura>0 and estado_ven <> 'ANULADAS' and 
			       v.codigo_usuario=u.codigo_usuario";*/
				   
		   $sql="Select (select descripcion_tipo from tipo_compra_venta where codigo_tipo=codigo_tipo_ven),*
					from facturacion.venta v, public.usuario u
					where  total_ven>0 and
					v.codigo_usuario=u.codigo_usuario  ";
	
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and v.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."' ";
				 // $sql=$sql." and v.fecha_venta='".$fecha."'";
				 
			    }else{
				  $sql=$sql." and v.fecha_venta='".$fecha."'";
				}
			}
			
			if(empty($codigo_usuario)==false or $codigo_usuario!=0){
				$sql=$sql." and u.codigo_usuario='".$codigo_usuario."'";
			}
			/*
			if(empty($codigo_sucursal)==false or $codigo_sucursal!=0){
				$sql=$sql." and v.codigo_cli='".$codigo_sucursal."'";
			}*/
			
	            
			$query = pg_query($pgcon, $sql) or die("Problema al Buscar la venta".pg_last_error());
	
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$venta[$clave]=$row;
						 
	     	}
		if($clave==0){
			echo $clave;
						}else{
			echo  json_encode(array("venta"=>$venta));
		}
			
	    }
	//*****************************


       ///** Buscar Venta Importar
       ///**
	    if ($_POST["accion"]=="BuscarVentaImportar"){
			
	
				   
		    $sql=" select * from facturacion.venta v where  total_ven>0 and saldo_ven>0 ";
		            
		    $query = pg_query($pgcon, $sql) or die("Problema al Buscar la venta".pg_last_error());
	
		    $clave=0;
		    while ($row = pg_fetch_array($query))
		    { 
		    	$clave++;
			$venta[$clave]=$row;			 
	     	    }
				
				
				 $sql2=" select * from cxc.cxc v where   saldo>0 ";
		            
		    $query2 = pg_query($pgcon, $sql2) or die("Problema al Buscar la venta".pg_last_error());
	
		    $clave2=0;
		    while ($row2 = pg_fetch_array($query2))
		    { 
		    	$clave2++;
			$cxc[$clave2]=$row2;			 
	     	    }
				
		   
                    if($clave==0){
			echo $clave;
		     }else{
			echo  json_encode(array("venta"=>$venta,"cxc"=>$cxc));
		     }	
	      }
	//*****************************
	


	///** Buscar Articulos Vendidos
	    if ($_POST["accion"]=="Buscar_Articulos_Vendido"){
			
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
			
		
			$codigo_usuario=$_POST["codigo_usuario"];
			$codigo_sucursal=$_POST["codigo_sucursal"];
			$codigo_empleado=$_POST["codigo_empleado"];
		
				   
		   /*$sql="select venta_art.cantidad_art as cantidad,  venta_art.totalitbis,
						articulo.descripcion as nombre, 
						venta_art.precio_art as precio,
						(select precio_art*cantidad_art 
						from venta_art va 
						where va.codigo_primario=venta_art.codigo_primario) as total
						from venta_art, articulo, venta where
						venta_art.codigo_art=articulo.codigo_art and
						venta.secuencia=venta_art.secuencia and
						 total_ven>0 and venta_art.cantidad_art>0  ";*/
						 
		/*   $sql="select 

				sum(venta_art.cantidad_art) as cantidad,  
				sum(venta_art.totalitbis) totalitbis,
				articulo.descripcion as nombre, 
				venta_art.precio_art as precio,
				
				sum((select precio_art*cantidad_art 
				from venta_art va 
				where va.codigo_primario=venta_art.codigo_primario)) as total
										
				from venta_art, articulo, venta where
				venta_art.codigo_art=articulo.codigo_art and
				venta.secuencia=venta_art.secuencia and
				total_ven>0 and venta_art.cantidad_art>0 
				
				 ";*/
				 
				   $sql="select 

				avg(venta_art.costo_art) as costo_art,
				sum(venta_art.cantidad_art) as cantidad,  
				sum(venta_art.totalitbis) totalitbis,
				articulo.descripcion as nombre, 
				AVG(venta_art.precio_art) as precio,
				
				sum((select precio_art*cantidad_art 
				from venta_art va 
				where va.codigo_primario=venta_art.codigo_primario)) as total,
				
				floor(((AVG(venta_art.precio_art))*(sum(venta_art.cantidad_art)))-
                ((AVG(venta_art.costo_art))*(sum(venta_art.cantidad_art)))::numeric) as beneficios
										
				from venta_art, articulo, venta where
				venta_art.codigo_art=articulo.codigo_art and
				venta.secuencia=venta_art.secuencia and
				total_ven>0 and venta_art.cantidad_art>0 
				
				 ";
	
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and venta.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."' ";
			    }else{
				  $sql=$sql." and venta.fecha_venta='".$fecha."'";
				}
			}
			
			if(empty($codigo_empleado)==false or $codigo_empleado!=0){
				$sql=$sql." and venta.codigo_vendedor='".$codigo_empleado."'";
			}
			
			if(empty($codigo_usuario)==false or $codigo_usuario!=0){
				$sql=$sql." and u.codigo_usuario='".$codigo_usuario."'";
			}
			
			if(empty($codigo_sucursal)==false or $codigo_sucursal!=0){
				$sql=$sql." and venta.codigo_local='".$codigo_sucursal."'";
			}
		//	$sql=$sql." group by articulo.codigo_art, venta_art.precio_art";
		$sql=$sql." group by articulo.codigo_art";
			
			
			
	            
			$query = pg_query($pgcon, $sql) or die("Problema al Buscar la venta".pg_last_error());
	
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$venta[$clave]=$row;
						 
	     	}
		if($clave==0){
			echo $clave;
						}else{
			echo  json_encode(array("venta"=>$venta));
		}
			
	    }
	//*****************************
	
	///** Buscar Venta Sucursal
	    if ($_POST["accion"]=="Buscar Venta Sucursal"){
			
			
			$fecha=$_POST["fecha"];
			$fecha2=$_POST["fecha2"];
			$codigo_usuario=$_POST["codigo_usuario"];
			$codigo_loteria=$_POST["codigo_loteria"];
			
			//Linea Original
		 /*	$sql="Select * from facturacion.venta v, public.usuario u where numero_factura>0 and estado_ven <> 'ANULADAS' and 
			       v.codigo_usuario=u.codigo_usuario";*/
				   
		/*   $sql="select 
						c.nombre_clie as sucursal, sum(v.total_ven) as total, 
						sum(va.monto_ganado) as sacado,  
						sum(v.total_ven)- sum(va.monto_ganado) as beneficios
						from 
						facturacion.venta v,  facturacion.venta_art va, cxc.cliente c
						where 
						v.total_ven>0 and 
						v.secuencia=va.secuencia and 
						v.codigo_cli=c.codigo_cli ";*/
						
								   
		 /*  $sql="select sucursal, sum(total)as total,sum(sacado) as sacado, 
		         sum(beneficios)as beneficios  from v_sucursal_total_sacado_beneficios where total>0 ";*/
				 
				/* $sql="select 
c.nombre_clie as sucursal, sum(v.total_ven) as total ,
(select sum(monto_ganado) from venta_art where venta_art.secuencia in (select vv.secuencia from venta vv where
vv.total_ven>0 and 
vv.codigo_cli=c.codigo_cli and 
vv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vv.secuencia )) as sacado,
sum(v.total_ven)-(select sum(monto_ganado) from venta_art where venta_art.secuencia in (select vv.secuencia from venta vv where
vv.total_ven>0 and 
vv.codigo_cli=c.codigo_cli and 
vv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vv.secuencia )) as beneficios
from 
facturacion.venta v,  cxc.cliente c
where 
v.total_ven>0 and 
v.codigo_cli=c.codigo_cli  ";*/
	
				$sql="select 
c.nombre_clie as sucursal, sum(v.total_ven) as total ,

--Calcular total sacada---------------------------------------------------------------------------------------------
(select sum(monto_ganado) from venta_art where venta_art.secuencia in (select vv.secuencia from venta vv where
vv.total_ven>0 and 
vv.codigo_cli=c.codigo_cli and 
vv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vv.secuencia )) as sacado, 

--Calcular total quinielas y porciento quiniela---------------------------------------------------------------------------------------------
(select COALESCE((sum(vva.cantidad_art)*c.porcieto_quiniela)/100,0)
	AS total_quiniela_comision  
from venta_art vva where vva.secuencia in (select vvv.secuencia from venta vvv where
vvv.total_ven>0 and 
vvv.codigo_cli=c.codigo_cli and 
vvv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vvv.secuencia) and tipo_jugada='QUINIELAS'),

--Calcular total pale y porciento pale---------------------------------------------------------------------------------------------
(select COALESCE((sum(vva.cantidad_art)*c.porciento_pale)/100,0)
	AS total_pale_comision 
from venta_art vva where vva.secuencia in (select vvv.secuencia from venta vvv where
vvv.total_ven>0 and 
vvv.codigo_cli=c.codigo_cli and 
vvv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vvv.secuencia) and tipo_jugada='PALE'),

--Calcular total tripleta y porciento tripleta---------------------------------------------------------------------------------------------
(select COALESCE((sum(vva.cantidad_art)*c.porciento_tripleta)/100,0)
	AS total_tripleta_comision
from venta_art vva where vva.secuencia in (select vvv.secuencia from venta vvv where
vvv.total_ven>0 and 
vvv.codigo_cli=c.codigo_cli and 
vvv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vvv.secuencia) and tipo_jugada='TRIPLETA'),

--Calcular total beneficios-----------------------------------------------------------------------------------------
sum(v.total_ven)-(select sum(monto_ganado) from venta_art where venta_art.secuencia in (select vv.secuencia from venta vv where
vv.total_ven>0 and 
vv.codigo_cli=c.codigo_cli and 
vv.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."'
group by  c.codigo_cli, vv.secuencia )) as beneficios
from 
facturacion.venta v,  cxc.cliente c
where 
v.total_ven>0 and 
v.codigo_cli=c.codigo_cli "; 
				 
	
			if(empty($fecha)==false){
				
				if(empty($fecha2)==false){
				  $sql=$sql." and v.fecha_venta BETWEEN '".$fecha."' and '".$fecha2."' ";
				 // $sql=$sql." and v.fecha_venta='".$fecha."'";
				 
			    }else{
				  $sql=$sql." and v.fecha_venta='".$fecha."'";
				}
			}
			
			
			
			if(empty($codigo_usuario)==false or $codigo_usuario!=0){
				$sql=$sql." and v.codigo_usuario='".$codigo_usuario."'";
			}
			
			if(empty($codigo_loteria)==false or $codigo_loteria!=0){
			//	$sql=$sql." and codigo_loteria='".$codigo_loteria."'";
			}
			
			$sql=$sql." group by  c.codigo_cli";
			
		//Solo par ala vista
			//$sql=$sql." group by  codigo_cli, sucursal";
		
	            
			$query = pg_query($pgcon, $sql) or die("Problema al Buscar la venta".pg_last_error());
	
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$venta[$clave]=$row;
						 
	     	}
		if($clave==0){
			echo $clave;
						}else{
			echo  json_encode(array("venta"=>$venta));
		}
			
	    }
	
	///** Buscar Venta Articulos
	    if ($_POST["accion"]=="Venta_Articulos"){
			
			$secuencia_venta=$_POST["secuencia"];
		  
		 	$sql="select venta_art.cantidad_art as cantidad,  venta_art.totalitbis,
						articulo.descripcion as nombre, 
						venta_art.precio_art as precio,
						(select precio_art*cantidad_art 
						from venta_art va 
						where va.codigo_primario=venta_art.codigo_primario) as total
						from venta_art, articulo, venta where
						venta_art.codigo_art=articulo.codigo_art and
						venta.secuencia=venta_art.secuencia and
						 total_ven>0 ";
	
		
			if(empty($secuencia_venta)==false){
				$sql=$sql." and venta_art.secuencia='".$secuencia_venta."'";
			}
			
	
			$query = pg_query($pgcon, $sql) or die("Problema al Buscar la venta".pg_last_error());
	
			$clave=0;
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$venta_art[$clave]=$row;
						 
	     	}
		    if($clave==0){ 
			   echo "0";
			}else{ 
			   echo json_encode(array("venta_art"=>$venta_art));
			}
	    }
		/// Buscar numero Ganadores
		
	    if ($_POST["accion"]=="Buscar_Numeros_Ganadores"){
			
						$fecha_ng=$_POST["fecha"];
						$numero_ng=$_POST["numero"];
						$codigo_usuario=$_POST["codigo_usuario"];
						$codigo_cli=$_POST["codigo_sucursal"];
				
				
						$sql="Select * from facturacion.venta v, facturacion.venta_art va, public.usuario u, cxp.proveedor pro
						where v.numero_factura>0 and v.estado_ven <> 'ANULADAS' and v.secuencia=va.secuencia and va.monto_ganado>0
						and u.codigo_usuario=v.codigo_usuario and pro.codigo_prov=va.codigo_loteria ";
				
					
						if(empty($numero_ng)==false){
							$sql=$sql." and v.numero_factura='".$numero_ng."'";
						}
						
						if(empty($fecha_ng)==false){
							$sql=$sql." and v.fecha_venta='".$fecha_ng."'";
						}
						
						if($codigo_usuario>0){
							$sql=$sql." and v.codigo_usuario='".$codigo_usuario."'";
						}
						
						if($codigo_cli>0){
							$sql=$sql." and v.codigo_cli='".$codigo_cli."'";
						}
					
				
						$query = pg_query($pgcon, $sql) or die("Problema al Buscar Número Ganadores".pg_last_error());
				
						$clave=0;
						while ($row = pg_fetch_array($query))
						{ 
							$clave++;
							$venta[$clave]=$row;
									 
						}
						
						if ($clave==0){
							  echo $clave;
							}else{
						      echo  json_encode(array("venta"=>$venta));
							}
			
	    }
		
	//*****************************
	
	
	
		//Cerrar conexion******************************************************************************
	 	pg_close($pgcon)or die("Problema al Cerrar Conexión".pg_last_error());
	 
	 
	  function abrir_loteria($fecha, $hora, $pgcon){
		 
		 switch (date('w', strtotime($fecha))) {
				case 0:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					      and  domingo = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
				case 1:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					       and lunes = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
				case 2:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					      and martes = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
				case 3:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					       and miercoles = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
				case 4:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					       and jueves = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
				case 5:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					       and viernes = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
				case 6:
					$sql_loteria="update cxp.proveedor set status='ABIERTO' where codigo_prov>0 
					       and sabado = true and nombre_prov is not null and hora_cierre>'".$hora."'";
					break;
			}
			
			
			$query_loteria = pg_query($pgcon, $sql_loteria) or die("Problema al actualizar el Proveedor".pg_last_error());
			
			return $query_loteria;
			
	    }
		
		 function cerrar_loteria($fecha, $hora, $pgcon){
		 
		 switch (date('w', strtotime($fecha))) {
				case 0:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					      and  domingo = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
				case 1:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					       and lunes = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
				case 2:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					      and martes = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
				case 3:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					       and miercoles = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
				case 4:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					       and jueves = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
				case 5:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					       and viernes = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
				case 6:
					$sql_loteria="update cxp.proveedor set status='CERRADO' where codigo_prov>0 
					       and sabado = true and nombre_prov is not null and hora_cierre<'".$hora."'";
					break;
			}
			
			
			$query_loteria = pg_query($pgcon, $sql_loteria) or die("Problema al actualizar el Proveedor".pg_last_error());
			
			return $query_loteria;
			
	    }
?>
