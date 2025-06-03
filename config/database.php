<?php
// Configurações do banco de dados
$host = 'localhost';
$db_name = 'db_investimentos';
$username = 'root'; 
$password = '';     

try {
    // ATENÇÃO: Adicione 'port=3312' na string DSN aqui!
    $conn = new PDO("mysql:host={$host};port=3312;dbname={$db_name};charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // echo "Conexão bem-sucedida!"; 
} catch (PDOException $e) {
    die("Erro de conexão: " . $e->getMessage());
}
?>