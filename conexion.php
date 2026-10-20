<?php

// Compara la ruta del archivo ejecutado en la URL ($_SERVER['SCRIPT_FILENAME'])
// con la ruta absoluta de este archivo en el servidor (__FILE__).
if (realpath($_SERVER['SCRIPT_FILENAME'] ?? '') === __FILE__) {
  // Si coinciden, significa que el usuario abrió el archivo directamente en el navegador.
  // Se requiere el script auxiliar usando __DIR__ (directorio actual del archivo).
  require_once __DIR__ . '/alerta-inicio.php';
  
  // Se ejecuta la función que despliega un mensaje de error y redirige al usuario al inicio.
  alertaYRedirigirInicio('No puedes abrir directamente este archivo.');
}

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "cyberkit";

$conexion = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conexion -> connect_error) {
  echo "Failed to connect to MySQL: " . $conexion -> connect_error;
  exit();
}
?>