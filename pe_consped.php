<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>CONSULTAR COMPRAS - Alberto</h1>
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
	
	echo '<form action="pe_inicio.html" method="post"><input type="submit" value="Volver"></form>';
		
	verPedidos($conn);

?>

<?php

function verPedidos($conn){
	
	
	$nif=$_SESSION['id'];
		
		$sql = "select orders.orderNumber, orderDate, status, orderLineNumber, quantityOrdered, priceEach from orderdetails, orders where orders.orderNumber=orderdetails.orderNumber and customerNumber='$nif' order by orderdetails.orderNumber asc, orderLineNumber ASC";
		$resultado=mysqli_query($conn, $sql); 
		if ($resultado) {
			if (mysqli_num_rows($resultado)>0) {
				echo "<table border='1'>
						<tr>
							<th>Numero del Pedido</th>
							<th>Fecha del Pedido</th>
							<th>Estado del Pedido</th>
							<th>Numero de Linea</th>
							<th>Nombre del Producto</th>
							<th>Stock</th>
							<th>Precio por unidad</th>
						</tr>";
				while ($row = mysqli_fetch_assoc($resultado)) {
					//sacamos el nombre del producto
					$numeroPedido=$row['orderNumber'];
					$sqlNombre = "select productName from products, orderdetails where orderdetails.productCode=products.productCode and orderNumber='$numeroPedido'";
					$nombrePorducto;
					$resultadoNombre=mysqli_query($conn, $sqlNombre); 
					if ($resultadoNombre) {
						$row2=mysqli_fetch_assoc($resultadoNombre);
						$nombrePorducto=$row2['productName'];
					} else {
						trigger_error("Error: " . $sqlNombre . "<br>" . mysqli_error($conn));
					}
					echo "<tr>";
						echo "<td>".$row['orderNumber']."</td>";
						echo "<td>".$row['orderDate']."</td>";
						echo "<td>".$row['status']."</td>";
						echo "<td>".$row['orderLineNumber']."</td>";
						echo "<td>".$nombrePorducto."</td>";
						echo "<td>".$row['quantityOrdered']."</td>";
						echo "<td>".$row['priceEach']."</td>";
					echo "</tr>";
				}
				echo" </table>";
				
			} else {
				echo "El cliente no tiene asignado ningun pedido";
			}
	
		} else {
			trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
		}
		
	
}

?>



</body>

</html>