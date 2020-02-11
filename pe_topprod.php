<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>STOCK PRODUCTOS ENTRE FECHAS - Alberto</h1>
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
	
	echo '<form action="" method="post">';
?>
<div align="left">
		<label for="fechaIni">Introduzca la fecha desde la que empezar a buscar:&nbsp &nbsp  </label><input type='date' name='fechaIni'><br>
		<label for="fechaFin">Introduzca la fecha para terminar de buscar:&nbsp &nbsp </label><input type='date' name='fechaFin'><br>
</div>
		</br>
<?php
	echo '<div><input type="submit" value="Buscar Compras"></div>
	</form>';
	echo '<form action="pe_inicio.html" method="post"><input type="submit" value="Volver"></form><div>';
	
	if (isset($_POST) && !empty($_POST)) { 
		buscarCompras($conn);
	}
		

?>

<?php

function buscarCompras($conn) {
	
	if (empty($_POST["fechaIni"])) {
		trigger_error("La fecha de inicio no puede estar vacia");
	}
	else {
	  $fecha=strtotime($_REQUEST['fechaIni']);
	  $fechaInicio=date("Y-m-d",$fecha);
	} 
	if (empty($_POST["fechaFin"])) {
		trigger_error("La fecha de fin no puede estar vacia");
	}
	else {
	  $fecha=strtotime($_REQUEST['fechaFin']);
	  $fechaFin=date("Y-m-d",$fecha);
	} 
	
	$sql = "select productCode, sum(quantityOrdered) from orderdetails, orders where orderdetails.orderNumber=orders.orderNumber and (orderDate>='$fechaInicio' and orderDate<='$fechaFin') group by productCode";
	
	$resultado= mysqli_query($conn, $sql);
	if ($resultado) {
		if (mysqli_num_rows($resultado)>0) {
			echo "<table border='1'>";
			echo "Esta es la cantidad comprada de todos los productos en dos fechas<br><br>";
			echo "<tr>";
				echo "<th>Id del producto</th>";
				echo "<th>Cantidad Comprada</th>";
				echo "</tr>";
			while ($row = mysqli_fetch_assoc($resultado)) {
				echo "<tr>";
				echo "<td>".$row['productCode']."</td>";
				echo "<td>".$row['sum(quantityOrdered)']."</td>";
				echo "</tr>";
			}
			echo "</table>";
		} else {
			echo "El cliente no hizo ninguna compra entre esas fechas";
		}
	} else {
		trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
	}

}

?>



</body>

</html>