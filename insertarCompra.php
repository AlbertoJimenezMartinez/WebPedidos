<?php
session_start();
//si no has iniciado sesion, volvemos al login
if (count($_SESSION['cesta'])<=0 && isset($_SESSION['nombre'])){
	header("Location: pe_inicio.html");
} else if (!isset($_SESSION['nombre'])){
	header("Location: index.php");
}
include "conexion.php";

set_error_handler("errores"); 

$conn=conectarBD();
?>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web compras</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>ESTADO DE LA COMPRA - Alberto</h1>
<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="post">
<div align="left">
<div class="form-group">
	Numero del Pedido  &nbsp <input type="informacion" name="informacion" class="form-control" placeholder="AA99999">
</div>

<?php

echo '<div><input type="submit" value="Comprar"></div></form>';
	
	if (!isset($_POST) || !empty($_POST)) { //sin enviar el formulario

		if (empty($_POST["informacion"])) {
			trigger_error("La informacion no puede estar vacia");
		}
		else if (strlen($_POST["informacion"])==7){
			
			$informacion=$_POST['informacion'];
			$formato=preg_match('/^[A-Za-zñÑ]{2}[0-9]{5}/', $informacion);
			if($formato!=1){//devulve 1 si es correcto
				trigger_error("El formato de la informacion del pedido no es correcta");
			}
			
		} else {
			trigger_error("La informacion del pedido no tiene 7 caracteres");
		}

		$nif=$_SESSION['id'];
		
		$sql = "select checkNumber from payments where checkNumber='$informacion'";
		$resultado=mysqli_query($conn, $sql); 
		if ($resultado) {
			$row=mysqli_fetch_assoc($resultado);	
			if ($row['checkNumber']==""){ //si el chekNumber no existe
				$cantidadTotal=0;
				
				$sqlMax = "select max(orderNumber) as 'maximo' from orders";
				$resultado=mysqli_query($conn, $sqlMax); 
				if ($resultado) {
					$row=mysqli_fetch_assoc($resultado);	
					if ($row['maximo']==""){ //si no existe ningun pedido
						$orderNumber=1;
					} else {
						$orderNumber=$row['maximo']+1;
					}
					
					
					
					//insertamos los datos en la tabla de orders
					$sql = "insert into orders (orderNumber, orderDate, requiredDate, shippedDate, status, comments, customerNumber) values ('$orderNumber', sysdate(), sysdate(),NULL,'In Process',NULL, '$nif')";
					if (mysqli_query($conn, $sql)) {
						echo "Orders: Datos insertados correctamente<br>";					
					} else {
						trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
					}
					
					$orderLineNumber=1;
					//insertamos en la tabla de orderdetails
					foreach ($_SESSION['cesta'] as $idProducto => $cantidad){
						$cantidadTotal+=$cantidad;
						$pvp=obtenerPVPProducto($conn, $idProducto);
						$sql = "insert into orderdetails (orderNumber, productCode, quantityOrdered, priceEach, orderLineNumber) values ('$orderNumber','$idProducto','$cantidad','$pvp','$orderLineNumber')";
						
						if (mysqli_query($conn, $sql)) {
							echo "OrderDetails ".$orderLineNumber.": Datos insertados correctamente<br>";					
						} else {
							trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
						}
						
						$orderLineNumber+=1;
						
						//actualizar la cantidad en lproductos
						$sql="update products set quantityInStock=quantityInStock-$cantidad where productCode='$idProducto'";
						if(!mysqli_query($conn, $sql)){
							trigger_error("Error: ".$sql."<br>".mysqli_error($conn));
						}
					}
					
					//insertamos los datos en la tabla de payments
					$sql = "insert into payments (customerNumber, checkNumber, paymentDate, amount) values ('$nif','$informacion',sysdate(),'$cantidadTotal')";
					if (mysqli_query($conn, $sql)) {
						echo "Payments: Datos insertados correctamente<br>";					
					} else {
						trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
					}
					
					echo '<br><form action="pe_inicio.html" method="post"><input type="submit" value="Ir a metodos"></form><div>';
					
					echo "<br><a href='logout.php'>Cerrar sesion</a>";
					
					$_SESSION['cesta']=array(); 
					
					
				
				} else {
					trigger_error("Error: " . $sqlMax . "<br>" . mysqli_error($conn));
				}	

			} else {
				echo "El numero de pedido ya existe";
			}
	
		} else {
			trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
		}
		
		
	
	}
?>
