<?php
session_start();
if (!isset($_SESSION['professor_id'])) {
    header('Location: login.php');
    exit;
}
echo "Bem-vindo, " . $_SESSION['professor_nome'] . "! Login funcionando! 🎉";
echo '<br><a href="logout.php">Sair</a>';
?>