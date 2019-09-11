<?php
require 'includes/ConectarSubirFotos.php';
include ('includes/funciones.php');
$con=new conectar();
$con->conectar();
$con->query("SELECT * FROM tp_modelos_subidos WHERE tpc_cedula_modelosubido = '".$_POST['value1']."';");
if($con->num_rows() == 0){
	$con->query("INSERT INTO tp_modelos_subidos VALUES (NULL, '".$_POST['value1']."', '".$_POST['value2']."', '".date("Y-m-d H:i:s")."', '".$_POST['value3']."');");
}



$target_dir = "./archivosapp/" . $_POST['value1'] . '/';
if(!file_exists($target_dir)){
	mkdir($target_dir, 0777);copy('./archivosapp/index.html', $target_dir.'index.html');
}
$target_file = $target_dir . basename($_FILES["imagen"]["name"]);
move_uploaded_file($_FILES["imagen"]["tmp_name"], $target_file);
echo "http://" . $_SERVER['SERVER_NAME'] . "/" . $target_file;

?>