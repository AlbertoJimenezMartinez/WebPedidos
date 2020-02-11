<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>STOCK PRODUCTOS DE UNA LINEA- Alberto</h1>
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
	
	$lineaProducto = obtenerMaximaLinea($conn);
	
?>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<div align="left">
	<label for="lineaProductos">Linea del Producto:</label>
	<select name="lineaProductos">
		<?php foreach($lineaProducto as $linea) : ?>
			<option> <?php echo $linea['productLine'] ?> </option>
		<?php endforeach; ?>
	</select>
	<br>
</div>
		
<?php
	echo '<div><input type="submit" value="Consultar"></div></form>';
	
	echo '<form action="pe_inicio.html" method="post"><input type="submit" value="Volver"></form><div>';
		
	if (isset($_POST) && !empty($_POST)) { 
		
		$linea = $_POST['lineaProductos'];
		verStockLinea($conn, $linea);

	}

?>

<?php

function verStockLinea($conn, $linea){
			
	//obtenemos el stock del producto
    $sqlCantidad="select productCode, productName, buyPrice, quantityInStock from products where productLine='$linea' order by quantityinstock desc";
    $resultado=mysqli_query($conn, $sqlCantidad); 
	if ($resultado) {
		$row=mysqli_fetch_assoc($resultado);
		echo "Las cantidades disponibles de los productos de la linea ".$linea." son: <br>"; 
		if (mysqli_num_rows($resultado)>0) {
				echo "<table border='1'>
						<tr>
							<th>Codigo del Producto</th>
							<th>Nombre del Producto</th>
							<th>Stock</th>
							<th>Precio por unidad</th>
						</tr>";
				while ($row = mysqli_fetch_assoc($resultado)) {
					
					echo "<tr>";
						echo "<td>".$row['productCode']."</td>";
						echo "<td>".$row['productName']."</td>";
						echo "<td>".$row['quantityInStock']."</td>";
						echo "<td>".$row['buyPrice']."</td>";
					echo "</tr>";
				}
				echo" </table>";
		} else {
			echo "La linea de producto, ".$linea.", no tiene ningun producto";
		}
		
	} else {
		trigger_error("Error: " . $sqlCantidad . "<br>" . mysqli_error($conn));
	}
		
	
}

?>



</body>

</html>