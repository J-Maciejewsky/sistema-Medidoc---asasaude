<?php
$host = "localhost";      
$user = "root";            
$password = "";            
$database = "doctor";    

$conn = new mysqli($host, $user, $password, $database);

// Verificar a conexão
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
