<?php
require 'includes/ConectarSubirFotos.php';
include ('includes/funciones.php');
$con=new conectar();
$con1=new conectar();
$con->conectar();
$con1->conectar();
if(isset($_GET['opc']) || isset($_POST['opc'])){
	if(isset($_GET['opc'])){$opc=$_GET['opc'];}else{$opc=$_POST['opc'];}
	switch($opc){
		case 1://INICIO DE SESION DE USUARIO
			$con->query("SELECT * FROM usuarios INNER JOIN tp_licencia ON tpc_codigo_licencia=tpc_licencia AND tpc_fechainicio_licencia <= '".date("Y-m-d H:i:s")."' AND tpc_fechafin_licencia >= '".date("Y-m-d H:i:s")."' AND usuario='".$_GET['nombredeusuario']."' AND clave='".md5($_GET['clave'])."' AND estado='1';");
			if($con->num_rows() > 0){
				$con->next_record();
				echo $con->f("id_usuario");
			}else{
				echo 'Error';
			}
		break;
		case 2://CREAR MODELO EN BASE DE DATOS PARA LUEGO CREAR CARPETA
			$con->query("SELECT * FROM tp_modelos_subidos WHERE tpc_cedula_modelosubido = '".$_GET['ncedula']."';");
			if($con->num_rows() == 0){
				$con->query("INSERT INTO tp_modelos_subidos VALUES (NULL, '".$_GET['ncedula']."', '".$_GET['nombremodelo']."', '".date("Y-m-d H:i:s")."', '".$_GET['iduser']."');");
				echo $con->f("id_usuario");
			}else{
				echo 'Error';
			}
		break;
		case 3:
			$con->query("SELECT * FROM tp_modelos_subidos WHERE tpc_cedula_modelosubido = '".$_POST['ncedula']."';");
			if($con->num_rows() == 0){
				$con->query("INSERT INTO tp_modelos_subidos VALUES (NULL, '".$_POST['ncedula']."', '".$_POST['nombremodelo']."', '".date("Y-m-d H:i:s")."', '".$_POST['iduser']."');");
			}
			$target_dir = "./archivosapp/" . $_POST['ncedula'] . '/';
			if(!file_exists($target_dir)){
				mkdir($target_dir, 0777);copy('./archivosapp/index.html', $target_dir.'index.html');
			}
			
			$baseFromJavascript = $_POST['imgfrontal'];
			$base_to_php = explode(',', $baseFromJavascript);
			$data = base64_decode($base_to_php[1]);
			$filepath = $target_dir . "frontal.png";
			file_put_contents($filepath, $data);
			$rotar = imagerotate($filepath, 90, 0);
			
			$baseFromJavascript = $_POST['imgfrontal2'];
			$base_to_php = explode(',', $baseFromJavascript);
			$data = base64_decode($base_to_php[1]);
			$filepath = $target_dir . "posterior.png";
			file_put_contents($filepath, $data);
			
			$baseFromJavascript = $_POST['imgfrontal3'];
			$base_to_php = explode(',', $baseFromJavascript);
			$data = base64_decode($base_to_php[1]);
			$filepath = $target_dir . "total.png";
			file_put_contents($filepath, $data);
		break;
	}
}
?>