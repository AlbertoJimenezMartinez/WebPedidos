<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>PEDIDOS ENTRE FECHAS - Alberto</h1>
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
	echo '<div><input type="submit" value="Buscar Pedidos"></div>
	</form>';
	echo '<form action="pe_inicio.html" method="post"><input type="submit" value="Volver"></form><div>';
	
	if (isset($_POST) && !empty($_POST)) { 
		buscarPedidos($conn);
	}
?>

<?php
// Funciones utilizadas en el programa
function buscarPedidos($conn){
	
	$fecha=strtotime($_REQUEST['fechaIni']);
	$fechaInicio=date("Y-m-d",$fecha);
	
	$fecha=strtotime($_REQUEST['fechaFin']);
	$fechaFin=date("Y-m-d",$fecha);

	$nif=$_SESSION['id'];

    $pagos = array();

    if($fechaInicio=="1970-01-01" || $fechaFin=="1970-01-01"){ //esta fecha es la que devuelve el campo fecha cuando no seleccionamos una fecha
        //historico
        $sql="select checkNumber from payments where customerNumber='$nif' order by paymentDate DESC";
        $resultado=mysqli_query($conn, $sql);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $pagos[] = $row['checkNumber'];
            }
        }
		
        if(count($pagos)==0){
            echo "El cliente no ha realizado compras";
        }
        else{
            //seleccionar toda la informacion
            $total=0;
            foreach ($pagos as $pago){
                $sql="select * from payments where checkNumber='$pago' order by paymentDate";
                $resultado=mysqli_query($conn, $sql);
				if ($resultado) { 
					$row=mysqli_fetch_assoc($resultado);
					$checkNumber=$row['checkNumber'];
					$paymentDate=$row['paymentDate'];
					$amount=$row['amount'];
					$total=$total+$amount;
					
					echo "<pre>checkNumber: $checkNumber, paymentDate: $paymentDate, amount: $amount</pre>";
					
				} else {
					trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
				}
            }
            echo "Total: $total";
        }
    }
    else{
        //por fechas
        $sql="SELECT checkNumber FROM payments WHERE customerNumber=$nif AND paymentDate>='$fechaInicio' AND paymentDate<='$fechaFin' ORDER BY paymentDate DESC";
        $resultado=mysqli_query($conn, $sql);
        if ($resultado) {
            while ($row = mysqli_fetch_assoc($resultado)) {
                $pagos[] = $row['checkNumber'];
            }
        }
        if(count($pagos)==0){
            echo "El cliente no ha realizado pagos en esas fechas";
        }
        else{
            //seleccionar toda la informacion
			$total=0;
            foreach ($pagos as $pago){
                $sql="select * from payments where checkNumber='$pago' order by paymentDate";
                $resultado=mysqli_query($conn, $sql);
				if ($resultado) { 
					$row=mysqli_fetch_assoc($resultado);
					$checkNumber=$row['checkNumber'];
					$paymentDate=$row['paymentDate'];
					$amount=$row['amount'];
					$total=$total+$amount;
					
					echo "<pre>checkNumber: $checkNumber, paymentDate: $paymentDate, amount: $amount</pre>";
					
				} else {
					trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
				}
            }
            echo "Total: $total";
        }
    }
}