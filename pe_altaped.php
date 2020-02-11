<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>COMPRAR PRODUCTOS - Alberto</h1>
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
	$productos = obtenerProductosConStock($conn);
	
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
	<div class="form-group">
	Cantidad  &nbsp <input type="number" name="cantidad" class="form-control">
	</div>
	<br>
</div>
		
<?php
	echo '<div><input type="submit" value="Añadir al Carrito"></div></form>';
	echo '<form action="pe_inicio.html" method="post"><div><input type="submit" value="Volver"></div></form>';
	echo "<h1>Cesta compras</h1>";
	
	if (!isset($_POST) || empty($_POST)) { //sin enviar el formulario
		mostrarCarrito($conn); 
	} else {
	
		$producto = $_POST['productos'];
		
		if (empty($_POST["cantidad"])) {
			trigger_error("La cantidad no puede estar vacia");
		}
		else {
		  $cantidad=$_POST['cantidad'];
		  limpiar_campos($cantidad);
		}
		
		añadirCarrito($conn, $producto, $cantidad);

		mostrarCarrito($conn);
	}
	


?>

<?php

function añadirCarrito($conn, $producto, $unidades){
    
    $idProducto=obtenerIdProducto($conn, $producto);
	
	//obtenemos el stock del producto
    $sqlCantidad="select quantityInStock from products where productCode='$idProducto'";
    $resultado=mysqli_query($conn, $sqlCantidad); 
	if ($resultado) {
		$row=mysqli_fetch_assoc($resultado);
		$resultadoCantidad=$row['quantityInStock']; 
	} else {
		trigger_error("Error: " . $sqlCantidad . "<br>" . mysqli_error($conn));
	}

    //hacer que sumen las unidades
    if(!empty($_SESSION['cesta'][$idProducto])){ //no deben de estar vacios al principios
        $unidades=$_SESSION['cesta'][$idProducto]+$unidades;
    }
	
	//comprobamos que la cantidad este disponible
    if($resultadoCantidad<$unidades){
        echo "Las unidades seleccionadas exceden el stock de almacen<br>";
		echo "Solo quedan ".$resultadoCantidad." unidades disponibles<br>";
    }
    else{
        //guardarlo en el array asotiativo
        $_SESSION['cesta'][$idProducto]=$unidades;
    }
	
    if(!empty($_SESSION['cesta'][$idProducto]) && $_SESSION['cesta'][$idProducto]<=0){ 
        //si las unidades son =0 borrarlo de la cesta
        unset($_SESSION['cesta'][$idProducto]);
    }
}


function mostrarCarrito($conn){
    if(count($_SESSION['cesta'])==0){
        echo "Cesta vacia";
    }
    else{
        foreach ($_SESSION['cesta'] as $idProducto => $unidades){
            $pvp=obtenerPVPProducto($conn, $idProducto);
			echo "ID_PRODUCTO=$idProducto, UNIDADES=$unidades, PVP=$pvp, PRECIO TOTAL=".($pvp*$unidades)." <br>";
			
        }
        echo '<br><form action="insertarCompra.php" method="post"><div><input type="submit" value="Comprar"></div></form>'; 
    }
}

?>



</body>

</html>