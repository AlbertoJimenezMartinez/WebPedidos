<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Web Pedido</title>
    <link rel="stylesheet" href="bootstrap.min.css">
</head>

<body>
<h1>LOGIN - Alberto</h1>
<?php
session_start();
//si entra sin enviar un formulario se destruira la sesion
if (!isset($_POST) || empty($_POST)){
	session_unset();
	session_destroy(); 
}


require "conexion.php";

	/*Conexion a la Base de Datos*/
	$conn=conectarBD();
	// Establecemos la funcion que va a tratar los errores
	set_error_handler("errores");


/* Se muestra el formulario la primera vez */
if (!isset($_POST) || empty($_POST)) { 
	echo '<form action="" method="post">';
?>
<div class="container ">
<div class="card border-success mb-3" style="max-width: 30rem;">
<div class="card-body">
		<div class="form-group">
        Nombre Usuario &nbsp <input type="text" name="nombre" class="form-control" required>
        </div>
		<div class="form-group">
        Contraseña  &nbsp <input type="text" name="contraseña" class="form-control" required>
        </div>
		</br>
<?php
	echo '<div><input type="submit" value="Inicio de Sesion"></div>
	</form>';
} else {
	// Aquí va el código al pulsar submit
   iniciosesion($conn);
	
}
?>

<?php

function iniciosesion($conn) {

	$nombre=$_POST['nombre'];
	limpiar_campos($nombre); 
	$contraseña=$_POST['contraseña'];

	$sql = "select id from admin where username='$nombre' and passcode='$contraseña'";
	$resultado= mysqli_query($conn, $sql);
	if ($resultado) {
		if (mysqli_num_rows($resultado)>0) {
			$row = mysqli_fetch_assoc($resultado);
			$_SESSION['nombre'] = $nombre;
			$_SESSION['id'] = $row['id'];
			$_SESSION['cesta'] = array();
			
			echo "Has iniciado Sesion: ".$_SESSION['nombre'];
			echo "<br><br><a href='pe_inicio.html'>Ir a los distintos metodos</a>";
			echo "<br><br><a href='logout.php'>Cerrar Sesion</a>";
			
		} else {
			echo "Los datos introducidos no son correctos";
			echo "<br><br><a href='index.php'>Volver</a>";
		}
	} else {
		trigger_error("Error: " . $sql . "<br>" . mysqli_error($conn));
	}
	
	

}

?>



</body>

</html>