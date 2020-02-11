<?php

function conectarBD() {
	/* Conexión BD */
define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', 'rootroot');
define('DB_DATABASE', 'pedidos08');
$conn = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE);
   
   if (!$conn) {
		die("Error conexión: " . mysqli_connect_error());
	}
	
	return $conn;
}

function limpiar_campos($data) {
  $data = trim($data);
  $data = stripslashes($data);
  $data = htmlspecialchars($data);
  return $data;
}

// Definicion funcion error_function
function errores ($error_level,$error_message,$error_file, $error_line, $error_context) {
  echo "<b> Codigo error: </b> $error_level  <br><b> Mensaje: </b> $error_message  <br><b> Fichero: </b> $error_file <br><b> Linea: </b>$error_line<br> ";
  //echo "<b>Array--> </b> <br>";
  //var_dump($error_context);
  echo "<br>";
  die();  

}

function obtenerProductosConStock($conn) {
	$productos = array();
	
	$sql = "SELECT productName FROM products where quantityInStock>0 ";
	
	$resultado = mysqli_query($conn, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$productos[] = $row;
		}
	}
	
	return $productos;
}

function obtenerIdProducto($conn, $nombreProducto) {
	$idProducto = null;
	
	$sql = "SELECT productCode FROM products WHERE productName = '$nombreProducto'";
	$resultado = mysqli_query($conn, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$idProducto = $row['productCode'];
		}
	}
	
	return $idProducto;
	
}

function obtenerPVPProducto($conn, $idProducto) {
	$sqlPrecio="select buyPrice from products where productCode='$idProducto'";
    $resultado=mysqli_query($conn, $sqlPrecio); 
	if ($resultado) {
		$row=mysqli_fetch_assoc($resultado);
		$pvp=$row['buyPrice']; 
	} else {
		trigger_error("Error: " . $sqlPrecio . "<br>" . mysqli_error($conn));
	}
	
	return $pvp;
	
}

function obtenerMaximaLinea($conn) {
	
	$lineaProducto = array();
	
	$sql = "select productLine from products group by productLine";
	
	$resultado = mysqli_query($conn, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$lineaProducto[] = $row;
		}
	}
	
	return $lineaProducto;
}

function obtenerProductos($conn) {
	$productos = array();
	
	$sql = "SELECT productName FROM products ";
	
	$resultado = mysqli_query($conn, $sql);
	if ($resultado) {
		while ($row = mysqli_fetch_assoc($resultado)) {
			$productos[] = $row;
		}
	}
	
	return $productos;
}

?>
