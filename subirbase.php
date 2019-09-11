<?php
require '../includes/Conectar.php';
include ('../includes/funciones.php');
session_start();
$mkid=$_SESSION["mkid"];
$mksesion=$_SESSION["mksesion"];
validar2($mkid, $mksesion);
$usuactual = reg('usuarios', 'id_usuario', $mkid);
$con=new conectar();
$con->conectar();
$con1=new conectar();
$con1->conectar();
if(isset($_FILES['archivo'])){// SI ES PARA SUBIR BASE
	//DATOS
	$tamano = $_FILES['archivo']['size'];
	$tipo = $_FILES['archivo']['type'];
	$archivo = $_FILES['archivo']['name'];
	$error = $_FILES['archivo']['error'];
	//*****************************
	if($archivo != "" && $tipo === "application/vnd.ms-excel"){
		$con->query("TRUNCATE TABLE productos_nuevos;");
		$lineas = file($_FILES['archivo']['tmp_name']);
 		$i = 1;
		$con->query("UPDATE productos SET visibilidad='HIDDEN'");
		while($lineas[$i])
		{
			$datos=explode(";",$lineas[$i]);
			$con->query("SELECT * FROM productos WHERE codigo_elt='".$datos[11]."';");
			if($con->num_rows() == 0){//SINO EXISTE SE INSERTA, SINO SE ACTUALIZA
				$con->query("INSERT INTO productos VALUES (NULL, '".$datos[0]."', '".$datos[1]."', '".$datos[2]."', '".$datos[3]."', '".$datos[4]."', '".$datos[5]."', '".$datos[6]."', '".$datos[7]."', '".$datos[8]."', '".$datos[9]."', '".$datos[10]."', '".$datos[11]."', '".trim($datos[12])."', '".trim($datos[13])."', '".$datos[14]."', '".$datos[15]."', '".strtoupper($datos[16])."', '".$datos[17]."', '".$datos[18]."', '".$datos[19]."', '".$datos[20]."', '".$datos[21]."', '".$datos[22]."', '".$datos[23]."', '".$datos[24]."', '".$datos[25]."', '".$datos[26]."', '".$datos[27]."', '".$datos[28]."', '".$datos[29]."', '".$datos[30]."', '".$datos[31]."', '".$datos[32]."', '".$datos[33]."', '".$datos[34]."', '".$datos[35]."', '".$datos[36]."');");
				$producto = reg('productos', 'codigo_elt', $datos[11]);
				$con->query("INSERT INTO productos_nuevos VALUES (NULL, '".$producto['id_producto']."', '".date("Y-m-d H:i:s")."');");
				if(trim($datos[1]) != '' && $datos[10] == ''){//SI HAY ALGO EN IMAGEN_PROV E IMAGEN_ELT VIENE VACIA
					$imagen = file_get_contents($datos[1]);
					if(file_exists('images/local/'.$datos[11].'.jpg')){
						unlink('images/local/'.$datos[11].'.jpg');
					}
					file_put_contents('images/local/'.$datos[11].'.jpg', $imagen);
					$con->query("UPDATE productos SET imagen_elt='http://www.eslotuyo.com.co/Administrativo/images/local/".$datos[11].".jpg' WHERE codigo_elt='".$producto['codigo_elt']."';");
				}
				
			}else{
				$con->query("UPDATE productos SET url_prov='".$datos[0]."', imagen_prov='".$datos[1]."', codigo_prov='".$datos[2]."', nombre_prov='".$datos[3]."', descripcion_prov='".$datos[4]."', precio_prov='".$datos[5]."', descuento_prov='".$datos[6]."', incremento_prov='".$datos[7]."', bodega_prov='".$datos[8]."', url_elt='".$datos[9]."', imagen_elt='".$datos[10]."', categoria_elt='".trim($datos[12])."', subcategoria_elt='".trim($datos[13])."', etiquetas_elt='".$datos[14]."', precio_elt='".$datos[15]."', visibilidad='".strtoupper($datos[16])."', color_1='".$datos[17]."', color_2='".$datos[18]."', color_3='".$datos[19]."', color_4='".$datos[20]."', color_5='".$datos[21]."', color_6='".$datos[22]."', color_7='".$datos[23]."', color_8='".$datos[24]."', color_9='".$datos[25]."', color_10='".$datos[26]."', stock_1='".$datos[27]."', stock_2='".$datos[28]."', stock_3='".$datos[29]."', stock_4='".$datos[30]."', stock_5='".$datos[31]."', stock_6='".$datos[32]."', stock_7='".$datos[33]."', stock_8='".$datos[34]."', stock_9='".$datos[35]."', stock_10='".$datos[36]."' WHERE codigo_elt='".$datos[11]."';");
			}
			$i++;
		}
		echo '<script type="text/javascript">alert("Actualizacion Correcta");</script>';
	}else{
		echo '<script type="text/javascript">alert("Error: Sube un archivo CSV");</script>';
	}
}
if(isset($_POST['ultprodsnuevos'])){// SI ES PARA EXPORTAR ULTIMO REGISTRO DE PRODUCTOS NUEVOS
	$filename = "productosnuevos".date("YmdHis");
	$sql = "SELECT * FROM productos INNER JOIN productos_nuevos ON id_producto=id_producto_productosnuevos ORDER BY codigo_elt ASC;";
	$con1->query($sql);
	if($con1->num_rows() > 0){
		header("Content-Type: application/xls");    
		header("Content-Disposition: attachment; filename=".$filename.".xls");  
		header("Pragma: no-cache"); 
		header("Expires: 0");
		$con->query("SHOW COLUMNS FROM productos;");
		echo "<table border='1'>
		<tr>";
		while($con->next_record()){
			echo "<td>".$con->f(0)."</td>";
		}
		echo "
		</tr>";
		print("\n");
		$con->query($sql);
		while($con->next_record())
		{
			$schema_insert = "<tr>";
			for($j=0; $j<38;$j++)//38=NUMERO DE COLUMNAS
			{
				if ($con->f($j) != ""){
					$schema_insert .= "<td>".$con->f($j)."</td>";
				}else{
					$schema_insert .= "<td></td>";
				}
			}
			$schema_insert .= "</tr>";
			$schema_insert = str_replace($sep."$", "", $schema_insert);
			echo $schema_insert;
		   // $schema_insert = preg_replace("/\r\n|\n\r|\n|\r/", " ", $schema_insert);
		}
		echo "</table>";
		echo '<script>location.href="subirbase.php";</script>';
	}else{
		echo '<script>alert("No se han encontrado registros de productos nuevos");location.href="subirbase.php";</script>';
	}
	exit();
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <!-- Required meta tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="au theme template">
    <meta name="author" content="Hau Nguyen">
    <meta name="keywords" content="au theme template">

    <!-- Title Page-->
    <title>Subir Base</title>

    <!-- Fontfaces CSS-->
    <link href="css/font-face.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-5/css/fontawesome-all.min.css" rel="stylesheet" media="all">
    <link href="vendor/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" media="all">
    <link href="vendor/mdi-font/css/material-design-iconic-font.min.css" rel="stylesheet" media="all">

    <!-- Bootstrap CSS-->
    <link href="vendor/bootstrap-4.1/bootstrap.min.css" rel="stylesheet" media="all">

    <!-- Vendor CSS-->
    <link href="vendor/animsition/animsition.min.css" rel="stylesheet" media="all">
    <link href="vendor/bootstrap-progressbar/bootstrap-progressbar-3.3.4.min.css" rel="stylesheet" media="all">
    <link href="vendor/wow/animate.css" rel="stylesheet" media="all">
    <link href="vendor/css-hamburgers/hamburgers.min.css" rel="stylesheet" media="all">
    <link href="vendor/slick/slick.css" rel="stylesheet" media="all">
    <link href="vendor/select2/select2.min.css" rel="stylesheet" media="all">
    <link href="vendor/perfect-scrollbar/perfect-scrollbar.css" rel="stylesheet" media="all">

    <!-- Main CSS-->
    <link href="css/theme.css" rel="stylesheet" media="all">

</head>

<body class="animsition">
    <div class="page-wrapper">
        <?php
			include("menu.php");
		?>
        <!-- PAGE CONTAINER-->
        <div class="page-container">
            <!-- HEADER DESKTOP-->
            <?php
				include("headerdesktop.php");
			?>

            <!-- MAIN CONTENT-->
            <div class="main-content">
                <div class="section__content section__content--p30">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-lg-12">
                                <div class="card">
                                    <div class="card-header">Subir Base</div>
                                    <div class="card-body">
                                        <div class="card-title">
                                            <h3 class="text-center title-2">Aqui pues subir tu base en CSV</h3>
                                        </div>
                                        <hr>
                                        <form action="subirbase.php" method="post" enctype="multipart/form-data">
                                            <div class="form-group">
                                                <label class="control-label mb-1">Orden del CSV (DEBES SUBIRLA CON LOS TITULOS EN LA HOJA CSV):</label><br>
												<label class="control-label mb-1">1. URL Proveedor</label><br>
												<label class="control-label mb-1">2. Imagen Proveedor</label><br>
												<label class="control-label mb-1">3. Codigo Proveedor</label><br>
												<label class="control-label mb-1">4. Nombre Proveedor</label><br>
												<label class="control-label mb-1">5. Descripci&oacute;n Proveedor</label><br>
												<label class="control-label mb-1">6. Precio Proveedor</label><br>
												<label class="control-label mb-1">7. Descuento Proveedor (%)</label><br>
												<label class="control-label mb-1">8. Incremento Proveedor (%)</label><br>
												<label class="control-label mb-1">9. Bodega</label><br>
												<label class="control-label mb-1">10. URL eslotuyo</label><br>
												<label class="control-label mb-1">11. Imagen eslotuyo</label><br>
												<label class="control-label mb-1">12. Codigo eslotuyo</label><br>
												<label class="control-label mb-1">13. Categoria eslotuyo</label><br>
												<label class="control-label mb-1">14. Subcategoria eslotuyo</label><br>
												<label class="control-label mb-1">15. Etiquetas eslotuyo</label><br>
												<label class="control-label mb-1">16. Precio eslotuyo</label><br>
												<label class="control-label mb-1">17. Visivilidad eslotuyo (VISIBLE o HIDDEN)</label><br>
												<label class="control-label mb-1">18. Columnas de Colores de color_1 a color_10</label><br>
												<label class="control-label mb-1">19. Columnas de Stock de stock_1 a stock_10</label><br>
                                                <input id="archivo" name="archivo" type="file" class="form-control" aria-required="true" aria-invalid="false" required="required">
                                            </div>
                                            <div>
                                                <button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block">
                                                    <i class="far fa-check-square"></i>&nbsp;
                                                    <span id="payment-button-amount">Subir Base</span>
                                                </button>
                                            </div>
                                        </form><br><br>
										<form action="subirbase.php" method="post">
											<input type="hidden" name="ultprodsnuevos" id="ultprodsnuevos" value="1">
											<button id="payment-button" type="submit" class="btn btn-lg btn-info btn-block">
												<span id="payment-button-amount">Bajar Excel Ultimos Productos Nuevos</span>
											</button>
										</form>
                                    </div>
                                </div>
                            </div>
                            
                        </div>
                        <div class="row">
                            <div class="col-md-12">
                                <div class="copyright">
                                    <p>Copyright © 2018 Colorlib. All rights reserved. Template by <a href="https://colorlib.com">Colorlib</a>.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Jquery JS-->
    <script src="vendor/jquery-3.2.1.min.js"></script>
    <!-- Bootstrap JS-->
    <script src="vendor/bootstrap-4.1/popper.min.js"></script>
    <script src="vendor/bootstrap-4.1/bootstrap.min.js"></script>
    <!-- Vendor JS       -->
    <script src="vendor/slick/slick.min.js">
    </script>
    <script src="vendor/wow/wow.min.js"></script>
    <script src="vendor/animsition/animsition.min.js"></script>
    <script src="vendor/bootstrap-progressbar/bootstrap-progressbar.min.js">
    </script>
    <script src="vendor/counter-up/jquery.waypoints.min.js"></script>
    <script src="vendor/counter-up/jquery.counterup.min.js">
    </script>
    <script src="vendor/circle-progress/circle-progress.min.js"></script>
    <script src="vendor/perfect-scrollbar/perfect-scrollbar.js"></script>
    <script src="vendor/chartjs/Chart.bundle.min.js"></script>
    <script src="vendor/select2/select2.min.js">
    </script>

    <!-- Main JS-->
    <script src="js/main.js"></script>

</body>

</html>
<!-- end document-->
