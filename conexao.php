<?php
$host    = 'ec2-3-131-141-8.us-east-2.compute.amazonaws.com';
$banco   = 'ads_3d_grupo5';
$usuario = 'usr_3d_g5';
$senha   = 'g5D@123';

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$banco;charset=utf8",
        $usuario,
        $senha
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
