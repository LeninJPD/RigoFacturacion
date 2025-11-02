<?php 

    header('Content-Type: application/json; Charset=UTF-8');
	
	
    $codigo_ven='';
	$numero_tick='';
	$total=0;
	$hora=date('h:i:s a', time()); 
	$fecha=date('Y-m-d');
	$codigo_sucursal='1';
	$codigo_usuario='1';
	$codigo_cli=1;
	$codigo_tipo_ven=1;
	$codigo_tipo_documento=1;
	$pago_ven=0;
	$estado_ven="FACTURADA";
	$codigo_usuario=1;
	$medida="UNIDAD";
	$aplicar="SI";
	$num1="";
	$num2="";
	$num3="";
	$tipo_jugada="";
	$codigo_loteria=0;
	$monto_a_pagar=0;
	
     
	$pgcon= pg_connect("host=localhost port=5432 dbname=bdmuebleria user=postgres password=rch1004")
	or die("Error de Conexión".pg_last_error());
	

 
	
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
			
		//	abrir_loteria($fecha, $hora_pg_completa, $pgcon);
		//	cerrar_loteria($fecha, $hora_pg_completa, $pgcon);
			
	//**********************************************************************************************
	
	// INSERTAR VENTAS FACTURACION ***********************************************************************************
	
	if ($_POST["accion"]=="Insertar_Venta"){
		
				$codigo_cli=$_POST["codigo_cli"];
				$datos_venta = json_decode($_POST["datos"],true);
				$codigo_usuario=$_POST["codigo_usuario"];
				$monto_a_pagar=$_POST["monto_a_pagar"];
				$tipo_venta=$_POST["tipo_venta"];
				$descripcion_tipo_venta=$_POST["descripcion_tipo_venta"];
				$saldo_ven=0;
				$nombre_cliente_nuevo=$_POST["nombre_cliente_nuevo"];
				$tipo_ncf="SIN COMPROBANTE";
				$codigo_comprobante=1;
				$costo_venta=0;
				$codigo_estacion=$_POST["codigo_estacion"];
				$codigo_empresa=$_POST["codigo_empresa"];
				$total_exento=0;
				$total_gravado=0;
				$codigo_vendedor=$_POST["codigo_empleado"];
				
				
				if($descripcion_tipo_venta=="CREDITO"){
					$saldo_ven= $monto_a_pagar;
					}
					
			/*	 $sqlcaja="select codigo from cuadre_caja where caja_abierta=true";
				 $query2caja = pg_query($pgcon, $sqlcaja) or die("Problema al buscar caja abierta".pg_last_error());
				 $rowcaja =  pg_fetch_array($query2caja);
				 $codigo_cuadre = trim($rowcaja["codigo"]);*/
				 
				 
				
				 $sql="insert into facturacion.venta(codigo_cli,
				 codigo_tipo_ven,codigo_tipo_documento, 
				 fecha_venta,codigo_usuario, estado_ven,total_ven, pago_ven, saldo_ven,nombre_cliente_nuevo,
				 fecha_vencimiento, codigo_cuadre, tipo_ncf, codigo_comprobante, codigo_vendedor) 
					   values('".$codigo_cli."', '".$tipo_venta."', '".$codigo_tipo_documento."',
					   '".$fecha_pg."','".$codigo_usuario."','".$estado_ven."',
					   '".$monto_a_pagar."','".$monto_a_pagar."','".$saldo_ven."','".$nombre_cliente_nuevo."',
					   (now()::date + '1 month'::interval),'0','".$tipo_ncf."', '".$codigo_comprobante."', '".$codigo_vendedor."')";
				 $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Venta".pg_last_error());
				 $oid = pg_last_oid($query1);
				  
				 
				 $sql="select max(secuencia) as codigo_ven from facturacion.venta";
				 $query2 = pg_query($pgcon, $sql) or die("Problema al Insertar Venta".pg_last_error());
				 $row =  pg_fetch_array($query2);
				 $codigo_venta = trim($row["codigo_ven"]);
				 $secuencia= trim($row["codigo_ven"]);
				 
				  
				 foreach (  $datos_venta as $dato) {
					 
					   $codigo_art= $dato['codigo'];
					   $descripcion=$dato['nombre'];
					   $medida=$dato['medida'];
					   $cantidad=$dato['cantidad'];
					   $costo=$dato['costo'];
					   $precio=$dato['precio'];
					   $valor=$dato['total'];
					   $total_itebis=$dato['totalitbis'];
					   $descuento=0;
					   $porciento_descuento=0;
					   $porciento_itebis=$dato['porciento_itebis'];
					   
					    $costo_venta=$costo_venta+($costo*$cantidad);
						if($total_itebis>0){
							$total_gravado=$total_gravado+ $valor;
							}else{
								$total_exento=$total_exento+ $valor;
								}
					   
						$sql="insert into facturacion.venta_art(codigo_ven,codigo_art,secuencia,
						medida,totalitbis,descuento,precio_art,
						 cantidad_art,porciento_descu, unidad, costo_art) 
						values('".$codigo_venta."', '".$codigo_art."', '".$secuencia."',  '".$medida."',
						'".$total_itebis."','".$descuento."','".$precio."','".$cantidad."',
						'".$porciento_descuento."',  '".$medida."',  '".$costo."' )";
						
						$query = pg_query($pgcon, $sql) or die("Problema al Insertar Detalle Venta".pg_last_error());
						
						
					}
					
				 $sql_actualiza_venta="update facturacion.venta set costoventa='".$costo_venta."',
				  total_gravado='".$total_gravado."',  total_exento='".$total_exento."' where secuencia='".$secuencia."'";
				 $query2_actualiza_venta = pg_query($pgcon, $sql_actualiza_venta) or 
				 die("Problema al uactualizar Venta".pg_last_error());
				
				
				   $mensaje[0]= "Procesada";
				   $mensaje[1]= $codigo_venta;
				   $mensaje[2]= "00";
				   $mensaje[3]=$fecha_pg;
				   $mensaje[4]= $hora_pg_completa;
				   
				  pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
				  echo json_encode(array($mensaje));
				  return 0;
		   
	}
	
	//******FIN VENTA FACTURACION***************************************************************************************
	
	
	// INSERTAR COBRO FACTURACION ***********************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_COBRO"){
		
		 try {
					   // Iniciar la transacción
						pg_query($pgcon, "BEGIN");
		
				$codigo_cli=$_POST["codigo_cliente"];
				$datos_cobro = json_decode($_POST["datos"],true);
				$codigo_usuario=$_POST["usuario"];
				$monto_cobrado=$_POST["monto_cobrado"];
				$tipo_cobro=$_POST["tipo_cobro"];
				$forma_pago=$_POST["forma_pago"];
				
				foreach ($datos_cobro as $dato) {
					 
					   $secuencia_venta= $dato['secuencia'];
				}
				
				$descuento_cobros=0;
				
				 $sqlcaja="select COALESCE(codigo,0) as codigo_cuadre from public.cuadre_caja where caja_abierta=true ";
				 $query2caja = pg_query($pgcon, $sqlcaja) or die("Problema al seleccionar caja".pg_last_error());
				 
				 $rowcaja =  pg_fetch_array($query2caja);
				 $codigo_cuadre = trim($rowcaja["codigo_cuadre"]);
				 
				 
				$documento= "PAGO A FACTURA: ". $secuencia_venta ." CON:";
				
				
				 $sql="select COALESCE(max(secuencia),0) as codigo_grupo_cobro from cxc.grupo_cobro ";
				 $query2 = pg_query($pgcon, $sql) or die("Problema al seleccionar cobro".pg_last_error());
				 
				 $row =  pg_fetch_array($query2);
				 $codigo_grupo_cobro = trim($row["codigo_grupo_cobro"]);
				 $secuencia= trim($row["codigo_grupo_cobro"]);
				
				 $codigo_grupo_cobro =$codigo_grupo_cobro +1;
				 $secuencia= $secuencia+1;
				
				 $sql="insert into cxc.grupo_cobro(total,fecha, codigo_cliente, codigo_tipo_venta,
				  codigo_usuario, secuencia, forma_pago, codigo_cuadre, secuencia_venta, descuento, documento) 
					   values('".$monto_cobrado."', '".$fecha_pg."', '".$codigo_cli."',
					   '".$tipo_cobro."','".$codigo_usuario."','". $secuencia."','".$forma_pago."', '".$codigo_cuadre."'
					   ,'".$secuencia_venta."','".$descuento_cobros."', '".$documento."' )";
					   
				 $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Cobro".pg_last_error());
				 $oid = pg_last_oid($query1);
				  
				 
				  
				 foreach ($datos_cobro as $dato) {
					 
			
					   $codigo_ven= $dato['secuencia'];
					   $referencia_cob=$dato['descripcion'];
					   $fecha_cob=$fecha_pg;
					   $codigo_tipo_documento="2";
					   $secuencia_grupo= $secuencia;
					   $codigo_tipo_venta=$tipo_cobro;
					   $monto_cob=$dato['monto_cobro'];
					   
					   $monto_cobrado_cuota=$monto_cob;
					   
					   // Cuando es con la tabla cxc,con cuota o prestamos
										   
												   $sqlcuota="select * from cxc.cxc where saldo > 0.1 and 
															codigo_venta='".$codigo_ven."' order by fecha_cxc asc"; 
							
												   $querycuota = pg_query($pgcon, $sqlcuota) or 
												   die("Problema al buscar Cuotas ".pg_last_error());
													
													$saldo_cuota=0;
													$codigo_cuota=0;
													
													while ($rowcuota = pg_fetch_array($querycuota))
													{ 
															if($monto_cobrado_cuota==0){
																break;
															}
															
															$saldo_cuota  = $rowcuota["saldo"];
															$codigo_cuota = $rowcuota["codigo"];	
															
															if($monto_cobrado_cuota>=$saldo_cuota){
																
																$monto_aplicado=$saldo_cuota;
																
																$monto_cobrado_cuota=$monto_cobrado_cuota-$saldo_cuota;
																
																$sql="insert into cxc.cobros(codigo_ven, 
																		 referencia_cob, 
																			  fecha_cob, 
																  codigo_tipo_documento, 
																		secuencia_grupo, 
																	  codigo_tipo_venta, 
																			  monto_cob,secuencia_venta, codigo_cxc) 
																	  values('".$codigo_ven."', 
																		 'Saldo Cuota  Ref :".$codigo_cuota."', 
																			 '". $fecha_cob."',  
																  '".$codigo_tipo_documento."',
																		'".$secuencia_grupo."',
																	  '".$codigo_tipo_venta."',
																			  '".$monto_aplicado."', '".$codigo_ven."', 
																			  '".$codigo_cuota."' )";
																
																$query = pg_query($pgcon, $sql) or 
																die("Problema al Insertar Detalle Cobro".pg_last_error());
																 
															}else if($monto_cobrado_cuota<$saldo_cuota){
																
																$sql="insert into cxc.cobros(codigo_ven, 
																		 referencia_cob, 
																			  fecha_cob, 
																  codigo_tipo_documento, 
																		secuencia_grupo, 
																	  codigo_tipo_venta, 
																			  monto_cob,secuencia_venta, codigo_cxc) 
																		  values('".$codigo_ven."', 
																			 'Abono Cuota Ref : ".$codigo_cuota."', 
																				 '". $fecha_cob."',  
																	  '".$codigo_tipo_documento."',
																			'".$secuencia_grupo."',
																		  '".$codigo_tipo_venta."',
																				  '".$monto_cobrado_cuota."', '".$codigo_ven."', 
																				  '".$codigo_cuota."' )";
																	
																	$query = pg_query($pgcon, $sql) or 
																	die("Problema al Insertar Detalle Cobro".pg_last_error());
																 
																 $monto_cobrado_cuota=0;
															}
																		 
													}
												//***********************************************************************
										   
											   //Sin la tabla CXC
											   /*
					  
											   /*
												$sql="insert into cxc.cobros(codigo_ven, 
																		 referencia_cob, 
																			  fecha_cob, 
																  codigo_tipo_documento, 
																		secuencia_grupo, 
																	  codigo_tipo_venta, 
																			  monto_cob,secuencia_venta, codigo_cxc) 
													  values('".$codigo_ven."', 
														 '".$referencia_cob."', 
															 '". $fecha_cob."',  
												  '".$codigo_tipo_documento."',
														'".$secuencia_grupo."',
													  '".$codigo_tipo_venta."',
															  '".$monto_cob."', '".$codigo_ven."', 0 )";
												
												$query = pg_query($pgcon, $sql) or 
												die("Problema al Insertar Detalle Cobro".pg_last_error());
												
												*/
				}
				
				 pg_query($pgcon, "COMMIT");
						  //echo "Transacción completada correctamente.";

             } catch (Exception $e) {
                  //Si ocurre un error, deshacer todo
                    pg_query($pgcon, "ROLLBACK");
                    //echo "Error en la transacción: " . $e->getMessage();
             }

                            
					
				   $mensaje[0]= "Procesada";
				   $mensaje[1]=  $codigo_grupo_cobro;
				   $mensaje[2]= "00";
				   $mensaje[3]=$fecha_pg;
				   $mensaje[4]= $hora_pg_completa;
				   
				  pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
				  echo json_encode(array($mensaje));
				  return 0;
		   
	}
	
	//******FIN COBRO FACTURACION***************************************************************************************


// INSERTAR COBRO DE SQLITE ***********************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_COBRO_2"){
		
		   try {
					   // Iniciar la transacción
						pg_query($pgcon, "BEGIN");
		 
				
						$secuencia_grupo=0;
						$datos_grupo_cobros = json_decode($_POST["datos_grupo_cobros"],true);
						$datos_cobros = json_decode($_POST["datos_cobros"],true);
		
						 
						foreach ($datos_grupo_cobros as $datos_gc) {
							 
		
								$codigo_cli= $datos_gc['codigo_cli'];
								$codigo_usuario=$datos_gc['codigo_usuario'];
								$codigo_tipo_documento=$datos_gc['tipo_cobro'];
								$tipo_cobro=$datos_gc['tipo_cobro'];
								$monto_cobrado=$datos_gc['monto_cobrado'];
								$forma_pago=$datos_gc['forma_pago'];
								$codigo_grupo_cobro=$datos_gc['codigo_gc'];
														
								$codigo_s_v_gc=$datos_gc['codigo_secuencia_venta_gc'];
								$codigo_cuadre_gc=$datos_gc['codigo_cuadre_gc'];
								$documento_gc=$datos_gc['documento_gc'];
								$descuento_gc=$datos_gc['descuento_gc'];
														
		
							
								$sql="select COALESCE(max(secuencia),0) as codigo_grupo_cobro from cxc.grupo_cobro ";
								$query2 = pg_query($pgcon, $sql) or die("Problema al seleccionar cobro".pg_last_error());
						 
								$row =  pg_fetch_array($query2);
								
								$secuencia= trim($row["codigo_grupo_cobro"]);
								$secuencia= $secuencia+1;
								$fecha_cob=$fecha_pg;	
						
								$sql="insert into cxc.grupo_cobro(total,fecha, codigo_cliente, codigo_tipo_venta, documento,
									   codigo_usuario, secuencia, forma_pago, codigo_cuadre, secuencia_venta, descuento) 
									   values('".$monto_cobrado."', '".$fecha_pg."', '".$codigo_cli."',
											   '".$tipo_cobro."','".$documento_gc ."','".$codigo_usuario."',
											   '". $secuencia."','".$forma_pago."','".$codigo_cuadre_gc."',
											   '".$codigo_s_v_gc."','".$descuento_gc."')";
							   
								$query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Grupo Cobro".pg_last_error());
								$oid = pg_last_oid($query1);
		
		
								$monto_cobrado_cuota=$monto_cobrado;
								
									   foreach ($datos_cobros as $dato) {
					
											$codigo_ven= $dato['secuencia'];
											$referencia_cob=$dato['descripcion'];
											$fecha_cob=$fecha_pg;
											$codigo_tipo_documento="2";
											$secuencia_grupo= $secuencia;
											$codigo_tipo_venta=$tipo_cobro;
											$monto_cob=$dato['monto_cobro'];
											$codigo_gc=$dato['numero_factura'];
											$codigo_secuencia_venta=$dato['codigo_numero_factura'];
		
							  
										   if ($codigo_gc == $codigo_grupo_cobro) {  
										   
										   
												 // Cuando es con la tabla cxc,con cuota o prestamos
										   
												   $sqlcuota="select * from cxc.cxc where saldo > 0.1 and 
															codigo_venta='".$codigo_ven."' order by fecha_cxc asc"; 
							
												   $querycuota = pg_query($pgcon, $sqlcuota) or 
												   die("Problema al buscar Cuotas ".pg_last_error());
													
													$saldo_cuota=0;
													$codigo_cuota=0;
													
													while ($rowcuota = pg_fetch_array($querycuota))
													{ 
															if($monto_cobrado_cuota==0){
																break;
															}
															
															$saldo_cuota  = $rowcuota["saldo"];
															$codigo_cuota = $rowcuota["codigo"];	
															
															if($monto_cobrado_cuota>=$saldo_cuota){
																
																$monto_aplicado=$saldo_cuota;
																
																$monto_cobrado_cuota=$monto_cobrado_cuota-$saldo_cuota;
																
																$sql="insert into cxc.cobros(codigo_ven, referencia_cob,
																			  fecha_cob, codigo_tipo_documento, secuencia_grupo, 
																			  codigo_tipo_venta, monto_cob, secuencia_venta, codigo_cxc) 
																			  values('".$codigo_ven."', 
																			  'Saldo Cuota Ref: ".$codigo_cuota."', 
																			  '". $fecha_cob."', '".$codigo_tipo_documento."',
																			  '".$secuencia_grupo."','".$codigo_tipo_venta."',
																			  '".$monto_aplicado."',
																			  '".$codigo_secuencia_venta."','".$codigo_cuota."')";
								
																 $query = pg_query($pgcon, $sql) or 
																 die("Problema al Insertar Detalle Cobro".pg_last_error());
															}else if($monto_cobrado_cuota<$saldo_cuota){
																
																$sql="insert into cxc.cobros(codigo_ven, referencia_cob,
																			  fecha_cob, codigo_tipo_documento, secuencia_grupo, 
																			  codigo_tipo_venta, monto_cob, secuencia_venta, codigo_cxc) 
																			  values('".$codigo_ven."', 
																			  'Abono Cuota Ref:".$codigo_cuota."', 
																			  '". $fecha_cob."', '".$codigo_tipo_documento."',
																			  '".$secuencia_grupo."','".$codigo_tipo_venta."',
																			  '".$monto_cobrado_cuota."','".$codigo_secuencia_venta."',
																			  '".$codigo_cuota."')";
								
																 $query = pg_query($pgcon, $sql) or 
																 die("Problema al Insertar Detalle Cobro".pg_last_error());
																 
																 $monto_cobrado_cuota=0;
															}
																		 
													}
												//***********************************************************************
										   
											   //Sin la tabla CXC
											   /*
												 $sql="insert into cxc.cobros(codigo_ven, referencia_cob,
																			  fecha_cob, codigo_tipo_documento, secuencia_grupo, 
																			  codigo_tipo_venta, monto_cob, secuencia_venta, codigo_cxc) 
																			  values('".$codigo_ven."', '".$referencia_cob."', 
																			  '". $fecha_cob."', '".$codigo_tipo_documento."',
																			  '".$secuencia_grupo."','".$codigo_tipo_venta."',
																			  '".$monto_cob."','".$codigo_secuencia_venta."',0)";
								
												   $query = pg_query($pgcon, $sql) or 
												   die("Problema al Insertar Detalle Cobro".pg_last_error());
												   
												*/
								
											}
									  }
								
								
						  }
						  
						   pg_query($pgcon, "COMMIT");
						  //echo "Transacción completada correctamente.";

                } catch (Exception $e) {
                  //Si ocurre un error, deshacer todo
                    pg_query($pgcon, "ROLLBACK");
                    //echo "Error en la transacción: " . $e->getMessage();
                 }

                                   
									
				   $mensaje[0]= "Procesada";
				   $mensaje[1]=  $secuencia_grupo;
				   $mensaje[2]= "00";
				   $mensaje[3]=$fecha_pg;
				   $mensaje[4]= $hora_pg_completa;
				   
				  pg_close($pgcon)or die("Problema al Cerrar Cobros ".pg_last_error());
				  echo json_encode(array($mensaje));
				  return 0;
		   
	}
	
	//******FIN COBRO SQLITE***************************************************************************************
	
	// ANULAR COBRO  ***********************************************************************************
	
	if ($_POST["accion"]=="ANULAR_COBRO"){
		
				$codigo_cobro=$_POST["codigo_cobro"];
			
				$sql_actualiza_cliente="update cxc.grupo_cobro set total=0 where secuencia='".$codigo_cobro."'";
				$query2_actualiza_cliente = pg_query($pgcon, $sql_actualiza_cliente) or die("Problema al uactualizar grupo cobro".pg_last_error());
			    
				
				$sql_actualiza_cliente1="update cxc.cobros set monto_cob=0 where secuencia_grupo='".$codigo_cobro."'";
				$query2_actualiza_cliente1 = pg_query($pgcon, $sql_actualiza_cliente1) or die("Problema al uactualizar cobro".pg_last_error());
			    pg_close($pgcon)or die("Problema al Cerrar ".pg_last_error());
					  
			    if( $query2_actualiza_cliente){
						  echo 0;
						  }else{
							  echo 1;
							  }
			    return 0;
	}
	
	//******FIN COBRO FACTURACION***************************************************************************************
	
	//Insertando Desenbolso**********************************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_DESEMBOLSO"){
		
		  
		       if($_POST["codigo_desembolso"]=="0"){
				      $sql="insert into facturacion.desembolso(concepto,monto,fecha,codigo_usuario,hora) 
					   values('".$_POST["concepto"]."', '".$_POST["monto"]."', '".$_POST["fecha"]."',
					   '".$_POST["codigo_usuario"]."','".$hora_pg.':'.$minuto_pg.':'.$segundo_pg."')";  
					   
				  }else{
					   $sql=" update desembolso set concepto='".$_POST["concepto"]."',
					    monto='".$_POST["monto"]."',fecha='".$_POST["fecha"]."' ,codigo_usuario='".$_POST["codigo_usuario"]."',
						hora= '".$hora_pg.':'.$minuto_pg.':'.$segundo_pg."'
						
						  where ";
					   $sql=$sql." codigo=".$_POST["codigo_desembolso"];
		
					  }
				
				 
				 $query = pg_query($pgcon, $sql) or die("Problema al Insertar DEsembolso".pg_last_error());
				
				 if ($query){
					 echo "0";

					 }else{
						 echo "1";
						 }	 
		   
	}
	
	// INSERTAR CLIENTE ***********************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_CLIENTE"){
		  
				if ($_POST["codigo"]=="0"){  
					
						$sql="insert into cxc.cliente
						(nombre_clie, correo_cli, direccion_cli, rnc) 
						 values('".$_POST["nombre"]."', '".$_POST["telefono"]."', '".$_POST["direccion"]."',
						   '".$_POST["cedula"]."')";
					     $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Cliente".pg_last_error());
					    
						 
						   if($query1){
						  echo 0;
						  }else{
							  echo 1;
							  }
					     
				
				}else{
					
				    $sql_actualiza_cliente="update cxc.cliente set nombre_clie='".$_POST["nombre"]."', 
					                                                        correo_cli='".$_POST["telefono"]."', 
																			direccion_cli='".$_POST["direccion"]."',
																			rnc='".$_POST["cedula"]."'
											where codigo_cli='".$_POST["codigo"]."'";
				    $query2_actualiza_cliente = pg_query($pgcon, $sql_actualiza_cliente) or 
				    die("Problema al uactualizar cliente".pg_last_error());
					
					
					  pg_close($pgcon)or die("Problema al Cerrar ".pg_last_error());
					  
					  if( $query2_actualiza_cliente){
						  echo 0;
						  }else{
							  echo 1;
							  }
				      
				
				}
				return 0;
		   
	}
	
	//******FIN CLIENTE***************************************************************************************
	
	// INSERTAR EMPLEADO ***********************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_EMPLEADO"){
		  
				if ($_POST["codigo"]=="0"){  
					
						$sql="insert into contabilidad.empleado
						(nombre, telefono, direccion, cedula, cargo) 
						 values('".$_POST["nombre"]."', '".$_POST["telefono"]."', '".$_POST["direccion"]."',
						   '".$_POST["cedula"]."', 'VENDEDOR')";
					     $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Empleado".pg_last_error());
					    
						 
						   if($query1){
						  echo 0;
						  }else{
							  echo 1;
							  }
					     
				
				}else{
					
				    $sql_actualiza_cliente="update contabilidad.empleado set nombre='".$_POST["nombre"]."', 
					                                                        telefono='".$_POST["telefono"]."', 
																			direccion='".$_POST["direccion"]."',
																			cedula='".$_POST["cedula"]."',
																			cargo='VENDEDOR'
											where codigo='".$_POST["codigo"]."'";
				    $query2_actualiza_cliente = pg_query($pgcon, $sql_actualiza_cliente) or 
				    die("Problema al uactualizar empleado".pg_last_error());
					
					
					  pg_close($pgcon)or die("Problema al Cerrar ".pg_last_error());
					  
					  if( $query2_actualiza_cliente){
						  echo 0;
						  }else{
							  echo 1;
							  }
				      
				
				}
				return 0;
		   
	}
	
	//******FIN EMPLEADO***************************************************************************************
	
	//BORRAR ENTRADA****************************************************************
		 if ($_POST["accion"]=="BORRAR_ENTRADA"){	
	
			
				  $sql="delete from inventario.entrada where codigo='".$_POST["codigo_articulo_entrada"]."' ";
					
			  
			$query = pg_query($pgcon, $sql) or die("Problema al borrar Articulos".pg_last_error());
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
				
			return 0;	
		}
					
	//**********************************************************************************************
	
	//"BORRAR_DESEMBOLSO****************************************************************
		 if ($_POST["accion"]=="BORRAR_DESEMBOLSO"){	
	
			
				  $sql="delete from facturacion.desembolso where codigo='".$_POST["codigo_desembolso"]."' ";
					
			  
			$query = pg_query($pgcon, $sql) or die("Problema al borrar Articulos".pg_last_error());
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
				
			return 0;	
		}
					
	//**********************************************************************************************
	
	//BORRAR ENTRADA****************************************************************
		 if ($_POST["accion"]=="BORRAR_SALIDA"){	
	
			
				  $sql="delete from inventario.salida where codigo='".$_POST["codigo_articulo_salida"]."' ";
					
			  
			$query = pg_query($pgcon, $sql) or die("Problema al borrar Articulos".pg_last_error());
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
				
			return 0;	
		}
					
	//**********************************************************************************************
	
	//BORRAR EMPLEADO****************************************************************
		 if ($_POST["accion"]=="BORRAR_EMPLEADO"){	
	
			
				  $sql="delete from contabilidad.empleado where codigo='".$_POST["codigo_empleado"]."' ";
					
			  
			$query = pg_query($pgcon, $sql) or die("Problema al borrar Empleado".pg_last_error());
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
			echo json_encode(array("respuesta"=>$buscar_entrada));
			}
				
			return 0;	
		}
					
	//**********************************************************************************************
	
	// INSERTAR ENTRADA ARTICULO ***********************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_ENTRADA"){
		  
				if ($_POST["codigo_articulo_entrada"]=="0"){  
					
						$sql="insert into inventario.entrada
						(codigo_art, costo_art, cantidad_art, total, fecha) 
						 values('".$_POST["codigo"]."', '".$_POST["costo"]."', '".$_POST["cantidad"]."',
						   '".$_POST["total"]."', '".$_POST["fecha"]."')";
					     $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Entrada".pg_last_error());
					    
						 
						   if($query1){
						  echo 0;
						  }else{
							  echo 1;
							  }
					     
				
				}else{
					
				    $sql_actualiza_cliente="update inventario.entrada  set costo_art='".$_POST["costo"]."', 
					                                                        cantidad_art='".$_POST["cantidad"]."', 
																			total='".$_POST["total"]."',
																			fecha='".$_POST["fecha"]."',
																			codigo_art='".$_POST["codigo"]."'
											where codigo='".$_POST["codigo_articulo_entrada"]."'";
				    $query2_actualiza_cliente = pg_query($pgcon, $sql_actualiza_cliente) or 
				    die("Problema al uactualizar ENTRADA".pg_last_error());
					
					
					  pg_close($pgcon)or die("Problema al Cerrar ".pg_last_error());
					  
					  if( $query2_actualiza_cliente){
						  echo 0;
						  }else{
							  echo 1;
							  }
				      
				
				}
				return 0;
		   
	}
	
	//******FIN ENTRADA ARTICULO***************************************************************************************
	
	// INSERTAR SALIDA ARTICULO ***********************************************************************************
	
	if ($_POST["accion"]=="GUARDAR_SALIDA"){
		  
				if ($_POST["codigo_articulo_salida"]=="0"){  
					
						$sql="insert into inventario.salida
						(codigo_art, costo_art, cantidad_art, total, fecha) 
						 values('".$_POST["codigo"]."', '".$_POST["costo"]."', '".$_POST["cantidad"]."',
						   '".$_POST["total"]."', '".$_POST["fecha"]."')";
					     $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Salida".pg_last_error());
					    
						 
						   if($query1){
						  echo 0;
						  }else{
							  echo 1;
							  }
					     
				
				}else{
					
				    $sql_actualiza_cliente="update inventario.salida  set costo_art='".$_POST["costo"]."', 
					                                                        cantidad_art='".$_POST["cantidad"]."', 
																			total='".$_POST["total"]."',
																			fecha='".$_POST["fecha"]."',
																			codigo_art='".$_POST["codigo"]."'
											where codigo='".$_POST["codigo_articulo_salida"]."'";
				    $query2_actualiza_cliente = pg_query($pgcon, $sql_actualiza_cliente) or 
				    die("Problema al uactualizar salida".pg_last_error());
					
					
					  pg_close($pgcon)or die("Problema al Cerrar ".pg_last_error());
					  
					  if( $query2_actualiza_cliente){
						  echo 0;
						  }else{
							  echo 1;
							  }
				      
				
				}
				return 0;
		   
	}
	
	//******FIN SALIDA ARTICULO***************************************************************************************
	
	// INSERTAR ARTICULOS ***********************************************************************************
	
	if ($_POST["accion"]=="Guardar_Articulos"){
		  
				if ($_POST["codigo_articulo"]=="0"){
					
					    $conitebis="FALSE";
					   if ($_POST["itebis"]>0){
						      $conitebis="TRUE";
						   }
					   
					
						$sql="insert into inventario.articulo
						(codigo_referencia, descripcion, codigocategoria, codigo_proveedor, porcientoitbis, es_servicio,
						 costo, precio1, conitebis) 
						 values('".$_POST["codigo_referencia"]."', '".$_POST["descripcion"]."', '".$_POST["codigo_categoria"]."',
						   '".$_POST["codigo_proveedor"]."','".$_POST["itebis"]."','".$_POST["es_servicio"]."',
						    '".$_POST["costo"]."','".$_POST["precio"]."', '".$conitebis."')";
					     $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Articulos".pg_last_error());
					     $oid = pg_last_oid($query1);
						 
						 $sql="select max(codigo_art) as codigo_art from inventario.articulo";
						 $query1 = pg_query($pgcon, $sql) or die("Problema al buscar articulos maximo".pg_last_error());
						 $row =  pg_fetch_array($query1);
				         $codigo_articulo = trim($row["codigo_art"]);
						 
						 $sql="insert into inventario.articulo_sucursal
						(codigo_articulo, codigo_sucursal, existencia, costo, precio1) 
						 values('".$codigo_articulo."', '".$_POST["codigo_sucursal"]."', 
						 '".$_POST["existencia"]."', '".$_POST["costo"]."','".$_POST["precio"]."')";
					     $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Sucursal Articulos".pg_last_error());
						 
						   if($query1){
						  echo 0;
						  }else{
							  echo 1;
							  }
					     
				
				}else{
					
					  $conitebis="FALSE";
					   if ($_POST["itebis"]>0){
						      $conitebis="TRUE";
						   }
					
				    $sql_actualiza_articulo="update inventario.articulo set codigo_referencia='".$_POST["codigo_referencia"]."', 
					                                                        descripcion='".$_POST["descripcion"]."', 
																			codigocategoria='".$_POST["codigo_categoria"]."', 
																			codigo_proveedor='".$_POST["codigo_proveedor"]."', 
																			porcientoitbis='".$_POST["itebis"]."', 
																			es_servicio='".$_POST["es_servicio"]."',
																			costo='".$_POST["costo"]."', 
																			precio1='".$_POST["precio"]."', 
																			conitebis='".$conitebis."'
											where codigo_art='".$_POST["codigo_articulo"]."'";
				    $query2_actualiza_articulo = pg_query($pgcon, $sql_actualiza_articulo) or 
				    die("Problema al uactualizar Venta".pg_last_error());
					
					
					 $sql_actualiza_articulo="update inventario.articulo_sucursal set 
																				  existencia='".$_POST["existencia"]."', 
																				  costo='".$_POST["costo"]."', 
																				  precio1='".$_POST["precio"]."'
											where codigo_articulo='".$_POST["codigo_articulo"]."'";
				    $query2_actualiza_articulo = pg_query($pgcon, $sql_actualiza_articulo) or 
				    die("Problema al uactualizar Venta".pg_last_error());
					
					  pg_close($pgcon)or die("Problema al Cerrar ".pg_last_error());
					  
					  if( $query2_actualiza_articulo){
						  echo 0;
						  }else{
							  echo 1;
							  }
				      
				
				}
				return 0;
		   
	}
	
	//******FIN ARTICULOS***************************************************************************************
	
	
	
	//Insertando Premios**********************************************************************************************
	
	if ($_POST["accion"]=="Guardar_Premios"){
		  
		        $sql_premio="Select * from facturacion.premios where codigo>0";
			   	$sql_premio=$sql_premio." and codigo_loteria=".$_POST["codigo_pro"];
				$sql_premio=$sql_premio." and fecha='".$_POST["fecha"]."'";
				$query = pg_query($pgcon, $sql_premio) or die("Problema al Buscar  Premios".pg_last_error());
				
				$existe=0;
				while ($row = pg_fetch_array($query))
				{ 
					$existe=1;
							 
				}
		  
		       if($existe==0){
				      $sql="insert into facturacion.premios(fecha,codigo_loteria,primera,segunda,tercera) 
					   values('".$_POST["fecha"]."', '".$_POST["codigo_pro"]."', '".$_POST["primer"]."',
					   '".$_POST["segundo"]."','".$_POST["tercer"]."')";
				  }else{
					   $sql=" update facturacion.premios set primera='".$_POST["primer"]."',
					    segunda='".$_POST["segundo"]."',tercera='".$_POST["tercer"]."' where ";
					   $sql=$sql." codigo_loteria=".$_POST["codigo_pro"];
						$sql=$sql." and fecha='".$_POST["fecha"]."'";
					  }
				 $query = pg_query($pgcon, $sql) or die("Problema al Insertar Premios".pg_last_error());
				 
				 
				$sql_premio="Select * from facturacion.premios pre, cxp.proveedor pro where codigo>0 and pre.codigo_loteria=pro.codigo_prov";
				$sql_premio=$sql_premio." and fecha='".$_POST["fecha"]."'";
				$query = pg_query($pgcon, $sql_premio) or die("Problema al Buscar  Premios".pg_last_error());
				
				 $clave=0;
					while ($row = pg_fetch_array($query))
					{ 
						$clave++;
						$premios[$clave]=$row;
								 
					}
		
			     echo json_encode(array("premios"=>$premios));
				 
		   
	}
	if ($_POST["accion"]=="Buscar_Premios"){
		  
				 
				$sql_premio="Select * from facturacion.premios pre, cxp.proveedor pro where codigo>0 and pre.codigo_loteria=pro.codigo_prov";
				$sql_premio=$sql_premio." and fecha='".$_POST["fecha"]."'";
				$query = pg_query($pgcon, $sql_premio) or die("Problema al Buscar  Premios".pg_last_error());
				
				 $clave=0;
					while ($row = pg_fetch_array($query))
					{ 
						$clave++;
						$premios[$clave]=$row;
								 
					}
					
					if ($clave==0){
						echo $clave;
						}else{
			              echo json_encode(array("premios"=>$premios));
						}
				 
		   
	}
	
	if ($_POST["accion"]=="Buscar_Premios_Fecha"){
		  
				
				$sql_premio="Select * from facturacion.premios pre, cxp.proveedor pro where codigo>0 and pre.codigo_loteria=pro.codigo_prov";
				$sql_premio=$sql_premio." and fecha='".$_POST["fecha"]."'";
				$query = pg_query($pgcon, $sql_premio) or die("Problema al Buscar  Premios".pg_last_error());
				
				 $clave=0;
					while ($row = pg_fetch_array($query))
					{ 
						$clave++;
						$premios[$clave]=$row;
								 
					}
					
					if ($clave==0){
						echo $clave;
						}else{
			             echo json_encode(array("premios"=>$premios));
						  
						}
				 
		   
	}
	//**********************************************************************************************************
	
	// FIN Premios
	
	
	            
	//Insertando Venta**********************************************************************************************
	
	if ($_POST["accion"]=="0"){
		  
		 $codigo_cli=$_POST["codigo_cli"];
		 $datos_venta = json_decode($_POST["datos"],true);
		 $codigo_usuario=$_POST["codigo_usuario"];
		 $guardar="SI";
		 $cancelada="";
	     $mensaje_c="";
		 
		 // Valor de venta en epresa 
		  $sql="select * from public.empresa";
		  $query = pg_query($pgcon, $sql) or die("Problema al  Buscar empresa".pg_last_error());
		  
			$quinielas_maxima=0;	
			$pale_maxima=0;	
			$tripleta_maxima=0;		
			
			while ($row = pg_fetch_array($query))
			{ 
				$quinielas_maxima=$row["valorgeneral_quiniela"];	
				$pale_maxima=$row["valorgeneral_pale"];	
				$tripleta_maxima=$row["valorgeneral_tripleta"];					 
	     	}
		 //*******************
		/* 
		// Validar Venta no sobrepase el motno de jugadas	
			$sql="select  trim(va.tipo_jugada)as tipo_jugada, 
										(CASE WHEN va.tipo_jugada = 'QUINIELAS' THEN sum (cantidad_art) ELSE 
										(CASE WHEN va.tipo_jugada = 'PALE' THEN sum (cantidad_art) ELSE 
										(CASE WHEN va.tipo_jugada = 'TRIPLETA' THEN sum (cantidad_art) ELSE 0 END) END) END)
										 AS total_jugado 
										 from facturacion.venta_art va where fecha_jugada='".$fecha_pg."'  
										 GROUP BY va.tipo_jugada
										 HAVING 
										(CASE WHEN va.tipo_jugada = 'QUINIELAS' THEN sum (cantidad_art) ELSE 
										(CASE WHEN va.tipo_jugada = 'PALE' THEN sum (cantidad_art) ELSE 
										(CASE WHEN va.tipo_jugada = 'TRIPLETA' THEN sum (cantidad_art) ELSE 0 END) END) END)>0";
			
			$query = pg_query($pgcon, $sql) or die("Problema al  Buscar Jugada total".pg_last_error());
			$clave=0;
			$pale_total_venta_art=0;
			$quinielas_total_venta_art=0;
			$tripleta_total_venta_art=0;
			
			
			while ($row = pg_fetch_array($query))
			{ 
		    	$clave++;
				$jugada_total[$row["tipo_jugada"]]=$row;
				
				if ($row["tipo_jugada"]=="QUINIELAS")
					$quinielas_total_venta_art=$row["total_jugado"];
					
				if ($row["tipo_jugada"]=="PALE")
					$pale_total_venta_art=$row["total_jugado"];
					
				if ($row["tipo_jugada"]=="TRIPLETA")	
					$tripleta_total_venta_art=$row["total_jugado"];				 
	     	
			}
			
			
			
			if ($_POST["quinielas"]>0 && $quinielas_total_venta_art >0){
				$resta=$quinielas_maxima-($_POST["quinielas"]+$jugada_total["QUINIELAS"]["total_jugado"]);
				$disponible=$quinielas_maxima-$jugada_total["QUINIELAS"]["total_jugado"];
				
				
				
				if($resta<=0){
					
					
					   $mensaje[0]= "Proceso Cancelado\nTotal Quinielas supera el Monto Maximo \n";
					   $mensaje[1]= "Maximo de Quinielas: ".$quinielas_maxima. "\n".
					   "Total Jugado: ".$_POST["quinielas"]."\n"."Total Vendido:".$jugada_total["QUINIELAS"]["total_jugado"]."\n Restan".
						$resta."\n\n Disponible: ".$disponible; 
					   $mensaje[2]= "1";
					   $mensaje[3]=$fecha_pg;
					   $mensaje[4]= $hora_pg_completa;
					   pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
					  
					  echo json_encode(array($mensaje));
					  return 0;
					}
			}
			
			
				
			if ($_POST["pale"]>0 && $pale_total_venta_art > 0){
				if( $pale_maxima-($_POST["pale"]+$jugada_total["PALE"]["total_jugado"])<=0){
					   $disponible=$quinielas_maxima-$jugada_total["PALE"]["total_jugado"];
					
					   $mensaje[0]= "Proceso Cancelado\nTotal Pale supera el Monto Maximo \n";
					   $mensaje[1]= "Maximo de Pale: ".$pale_maxima. "\n".
					   "Total Jugado: ".$_POST["pale"]."\n"."Total Vendido: ".$jugada_total["PALE"]["total_jugado"]."\n\n Disponible :".$disponible; 
					   $mensaje[2]= "1";
					   $mensaje[3]=$fecha_pg;
					   $mensaje[4]= $hora_pg_completa;
					   pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
					  
					  echo json_encode(array($mensaje));
					  return 0;
					}
			}
				
			if ($_POST["tripleta"]>0 && $tripleta_total_venta_art>0){
				
				if(  $tripleta_maxima-($_POST["tripleta"]+$jugada_total["TRIPLETA"]["total_jugado"])<=0){
					   $disponible=$quinielas_maxima-$jugada_total["TRIPLETA"]["total_jugado"];
					   $mensaje[0]= "Proceso Cancelado\nTotal Tripleta supera el Monto Maximo \n";
					   $mensaje[1]= "Maximo de Tripleta: ".$tripleta_maxima. "\n"."Total Jugado: ".$_POST["tripleta"]."\n"."Total Vendido: ".
					   $jugada_total["TRIPLETA"]["total_jugado"]."\n\n Disponible: ".$disponible; 
					   $mensaje[2]= "1";
					   $mensaje[3]=$fecha_pg;
					   $mensaje[4]= $hora_pg_completa;
					   pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
					  
					 echo json_encode(array($mensaje));
					  return 0;
					}
			
			}*/
			
		//***********************************
		 
	    //Validando Monto disponible Con Vista Rigo************************************************
		  $array_codigo_loteria=array();
          $array_tipo_jugada=array();     
		  $array_total=array();                                 
												  
		  $total=0;
		  $total_disponible=0;
		  $total_tickt=0;
		  $total_disponible_loteria=0;
		  $cont=0;
		  $total_jugada=0;
												  
		  foreach (  $datos_venta as $dato) {
			    	  
				$numero_tick= $dato['numero_tick'];
				$total_tickt=$dato['total_tickt'];
				$tipo_jugada=$dato['descripcion_numero'];
				$codigo_loteria=$dato['codigo_prov'];
				$nombre_loteria=$dato['nombre_prov']; 
				
				//$juagada= str_replace("-", "", $numero_tick);
				
				$num1="";
				$num2="";
				$num3="";
						
				if(strlen($numero_tick)==2){
					$num1= $numero_tick;  
				}
						
				if(strlen($numero_tick)==5){
					$num1=substr($numero_tick, 0, 2);
					$num2=substr($numero_tick, 3, 2);	
				}
						
				if(strlen($numero_tick)==8){
							  
					$num1=substr($numero_tick, 0, 2);
					$num2=substr($numero_tick, 3, 2);
					$num3=substr($numero_tick, 6, 2);
				}
					   
				$sql=" select sum(cantidad) as total   from facturacion.venta_numero_diario ";
		 
		 		$sql.=" WHERE codigo_cli='".$codigo_cli."' and codigo_loteria='".$codigo_loteria."'";
				
		 		$sql.=" and tipo_jugada like'%".$tipo_jugada."%'";
				
				$sql.=" and jugada like'%".$num1.$num2.$num3."%'";
		 
				
				$query = pg_query($pgcon, $sql) or die("Problema al buscar la fecha".pg_last_error());
			
				while ($row = pg_fetch_array($query))
				{ 
						$total=$row["total"];		 
				}
					
				if($tipo_jugada=="QUINIELAS"){
				   $total_disponible=$quinielas_maxima-($total+$total_tickt);
				   $total_disponible_loteria=$quinielas_maxima;
				}	
				
				if($tipo_jugada=="PALE"){
					$total_disponible=$pale_maxima-($total+$total_tickt);
					 $total_disponible_loteria=$pale_maxima;
				}
				
				if($tipo_jugada=="TRIPLETA"){
					$total_disponible=$tripleta_maxima-($total+$total_tickt);
					$total_disponible_loteria=$tripleta_maxima;
				}
				
				//Validando Loteria y Tipo_Jugada Repetida***********************************************
				if($cont>0){
					$total_jugada=0;
					for(  $i=0 ; $i<count($array_codigo_loteria); $i++) {
						
						if($array_codigo_loteria[$i]==$codigo_loteria && $array_tipo_jugada[$i] ==$tipo_jugada){
							$total_jugada=$total_jugada+ $array_total[$i];   
						}
				    }	
					$array_codigo_loteria[$cont]=$codigo_loteria;			  
					$array_tipo_jugada[$cont]=$tipo_jugada;
					$array_total[$cont]=$total_tickt;	
					$cont=$cont+1;			  
				}else{
					$array_codigo_loteria[$cont]=$codigo_loteria;			  
					$array_tipo_jugada[$cont]=$tipo_jugada;
					$array_total[$cont]=$total_tickt;	
					$cont=$cont+1;					  
				}
				//**************************************************************************************
				
				if(($total_disponible-$total_jugada)<0){
					   $mensaje[0]= "Proceso Cancelado\n".$nombre_loteria." \nTotal ".$tipo_jugada. " supera el Monto Maximo \n";
					   $mensaje[1]= "Maximo de  ".$tipo_jugada. ": ".$quinielas_maxima. "\n".
					   "Total Jugado: ".$total_tickt. "\n".
					   "Total Ya Vendido: ".$total."\n\n Disponible: ".$quinielas_maxima-$total; 
					   $mensaje[2]= "1";
					   $mensaje[3]=$fecha_pg;
					   $mensaje[4]= $hora_pg_completa;
					   pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
					  
					  echo json_encode(array($mensaje));
					  return 0;
					
				}
							
			}
		 
		//Fin**************************************************************************************
		 
		 //Validar Venta Loteria Cerrada******
			   
			   foreach (  $datos_venta as $dato1) {
				   $loteria=listar_loteria_por_codigo($fecha_pg, $pgcon);
				   if(   strtotime( $hora_pg_completa)>strtotime($loteria[$dato1['codigo_prov']]["hora_cierre"])){
					  //strtotime 
					   $guardar="NO";
					   $cancelada="Venta Cancelada por horas Sobrepasada: \n Loteria Afectadas: ".$hora_pg_completa;
					   $mensaje_c=$mensaje_c.$loteria[$dato1['codigo_prov']]["codigo_prov"].
					   " ".$loteria[$dato1['codigo_prov']]["nombre_prov"]."\n";
					   
					}
				   $mensaje[0]= $cancelada;
				   $mensaje[1]= $mensaje_c; 
				   $mensaje[2]= "1";
				   $mensaje[3]=$fecha_pg;
				   $mensaje[4]= $hora_pg_completa;
				 }
		 
		 ///****************
		 
		
		 
		 if ($guardar=="SI"){
			      
				 $monto_a_pagar=$_POST["monto_a_pagar"];
				 $sql="insert into facturacion.venta(codigo_cli,codigo_tipo_ven,codigo_tipo_documento, fecha_venta,codigo_usuario, estado_ven,
													 total_ven, pago_ven) 
					   values('".$codigo_cli."', '".$codigo_tipo_ven."', '".$codigo_tipo_documento."',
					   '".$fecha_pg."','".$codigo_usuario."','".$estado_ven."','".$monto_a_pagar."','".$monto_a_pagar."')";
				 $query1 = pg_query($pgcon, $sql) or die("Problema al Insertar Venta".pg_last_error());
				 $oid = pg_last_oid($query1);
				 
				 
				 $sql="select max(numero_factura) as codigo_ven from facturacion.venta";
				 $query2 = pg_query($pgcon, $sql) or die("Problema al Insertar Venta".pg_last_error());
				 $row =  pg_fetch_array($query2);
				 $codigo_venta = trim($row["codigo_ven"]);
				 
				  
				 foreach (  $datos_venta as $dato) {
					  
					   $numero_tick= $dato['numero_tick'];
					   $total=$dato['total_tickt'];
					   $tipo_jugada=$dato['descripcion_numero'];
					   $codigo_loteria=$dato['codigo_prov'];
					   $num1="";
					   $num2="";
					   $num3="";
						
						if(strlen($numero_tick)==2){
							$num1= $numero_tick;  
						}
						
						if(strlen($numero_tick)==5){
							$num1=substr($numero_tick, 0, 2);
							$num2=substr($numero_tick, 3, 2);
							
							$numero_pale=array($num1,$num2);
                            sort($numero_pale);
							$num1=$numero_pale[0];
							$num2=$numero_pale[1];
							
						}
						
						
						if(strlen($numero_tick)==8){
							  
							$num1=substr($numero_tick, 0, 2);
							$num2=substr($numero_tick, 3, 2);
							$num3=substr($numero_tick, 6, 2);
							
							$numero_tripleta=array($num1,$num2, $num3);
                            sort($numero_tripleta);
							$num1=$numero_tripleta[0];
							$num2=$numero_tripleta[1];
							$num3=$numero_tripleta[2];
						}
						
						
						
						
				
						$sql="insert into facturacion.venta_art(codigo_ven,cantidad_art,medida,aplicar,num1,num2,num3,
						tipo_jugada,codigo_loteria,fecha_jugada) 
						values('".$codigo_venta."', '".$total."', '".$medida."', '".$aplicar."', '".$num1."',
						'".$num2."','".$num3."','".$tipo_jugada."','".$codigo_loteria."','".$fecha."' )";
						
						$query = pg_query($pgcon, $sql) or die("Problema al Insertar Venta".pg_last_error());
						
						
					}
				
				
				   $mensaje[0]= "Procesada";
				   $mensaje[1]= $codigo_venta;
				   $mensaje[2]= "00";
				   $mensaje[3]=$fecha_pg;
				   $mensaje[4]= $hora_pg_completa;
				   
				  // echo json_encode(array($mensaje));
				   
				  // return 0;
				   
		}
		   
	}
	
				  
	//**********************************************************************************************************
	
	//*Actualizar**********************************************************************************************
/*	if ($_POST["accion"]==1){
		
		   $codigo_ven=$_POST["codigo_ven"];
			$numero_tick=$_POST["numero_tick"];
    		$total=$_POST["total"];
		
	     $sql="update facturacion.venta set numero_tick ='".$numero_tick."',
		  total= '".$total."', hora='".$hora."', fecha='".$fecha."',
		   codigo_sucursal='".$codigo_sucursal."',
		    codigo_usuario='".$codigo_usuario."' where codigo_ven='".$codigo_ven."'";
			
		  $mensaje="Venta Actualizada con Exito";
	}*/
	//************************************************************************************************************
	//*Eliminar***********************************************************************************************
	if ($_POST["accion"]=="2"){
		  
		  $numero_tick=$_POST["numero_tick"];// En Anular, Esto es el número de Factura
	
	     //$sql="delete from facturacion.venta where codigo_ven='".$codigo_ven."'";
		  //$mensaje="Venta Eliminada con Exito";
		  
		  $sql="update facturacion.venta set total_ven = 0 where secuencia='".$numero_tick."'";
		//  $sql.=" and date(hora_ven)='".$fecha_pg."'";
		//  $sql.=" and '".$hora_pg.$minuto_pg."'-(extract(hour from  hora_ven ) ||''|| extract(minute from  hora_ven ))::int<6";
		  
		  $query = pg_query($pgcon, $sql) or die("Problema al Anular Venta".pg_last_error());
		  
		  if(pg_affected_rows($query)>0){
			 echo "Venta Anulada con Exito";  
		  }else{
			  
			 echo "ESTA VENTA NO PUEDE SER ANULADA. \n\n"; 
			 
		  }
	}
	////////////**********************************************************************************************
	
	
	
	//Cerrar conexion******************************************************************************
	 pg_close($pgcon)or die("Problema al Cerrar Venta".pg_last_error());
	 
	 if ($_POST["accion"]=="0"){
		 echo json_encode(array($mensaje));
	  } 
	  
	  
	  //Espacio de Funciones************************************
	  
	  function dia_en_letra($fecha) {
		$dias = array('Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado');
		$dia = $dias[date('w', strtotime($fecha))];
		return $dia;
    }
	
	 function listar_loteria_por_codigo($fecha,$pgcon){
		 
		 switch (date('w', strtotime($fecha))) {
				case 0:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and domingo = true and nombre_prov is not null";
					break;
				case 1:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and lunes = true and nombre_prov is not null";
					break;
				case 2:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and martes = true and nombre_prov is not null";
					break;
				case 3:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and miercoles = true and nombre_prov is not null";
					break;
				case 4:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and jueves = true and nombre_prov is not null";
					break;
				case 5:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and viernes = true and nombre_prov is not null";
					break;
				case 6:
					$sql_loteria="Select * from cxp.proveedor where codigo_prov>0 
					      and habilitada = true and status = 'ABIERTO' and sabado = true and nombre_prov is not null";
					break;
			}
			
			
			$query_loteria = pg_query($pgcon, $sql_loteria) or die("Problema al Insertar Buscar el Proveedor".pg_last_error());
	
			while ($row_loteria = pg_fetch_array($query_loteria))
			{ 
				$proveedor_loteria[$row_loteria["codigo_prov"]]=$row_loteria;	
						 
	     	}
			return $proveedor_loteria;
			
	    }
		
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