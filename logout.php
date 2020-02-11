<?php
session_start();
//si no has iniciado sesion, volvemos al login
if (!isset($_SESSION['nombre'])){
	header("Location: index.php");
}
session_unset();
session_destroy();

echo "Has cerrado correctamente la sesion";
echo "<br><br><a href='index.php'>Ir a Inicio de Sesion</a>";  
?>