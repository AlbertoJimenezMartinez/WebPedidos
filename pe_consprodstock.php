<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>STOCK PRODUCTOS - Alberto</h1>
<?php
session_start();
//si no has iniciado sesion, volvemos al login
if (!isset($_SESSION['nombre'])){
	header("Location: index.php");
}

require "conexion.php";

	/*Conexion a la Base de Datos*/
	$conn=conectarBD();
	// Establecemos la funcion que va a tratar los errores
	set_error_handler("errores");

	echo "Usuario: ".$_SESSION['nombre'];
	
	$productos = obtenerProductos($conn);
	
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<div align="left">
	<label for="productos">Productos:</label>
	<select name="productos">
		<?php foreach($productos as $producto) : ?>
			<option> <?php echo $producto['productName'] ?> </option>
		<?php endforeach; ?>
	</select>
	<br><br>
</div>
		
<?php
	echo '<div><input type="submit" value="Consultar"></div></form>';
	
	echo '<form action="pe_inicio.html" method="post"><input type="submit" value="Volver"></form><div>';
		
	if (isset($_POST) && !empty($_POST)) { 
		
		$producto = $_POST['productos'];
		verStock($conn, $producto);

	}

?>

<?php

function verStock($conn, $producto){
	
	
	$idProducto=obtenerIdProducto($conn, $producto);
		
	//obtenemos el stock del producto
    $sqlCantidad="select quantityInStock from products where productCode='$idProducto'";
    $resultado=mysqli_query($conn, $sqlCantidad); 
	if ($resultado) {
		$row=mysqli_fetch_assoc($resultado);
		$stock=$row['quantityInStock'];
		echo "La cantidade disponible del producto es: ".$stock; 
		
	} else {
		trigger_error("Error: " . $sqlCantidad . "<br>" . mysqli_error($conn));
	}
		
	
}

?>



</body>

</html>