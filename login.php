<?php
session_start();
require_once 'conexao.php';

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    $stmt = $pdo->prepare("SELECT * FROM professores WHERE email = ?");
    $stmt->execute([$email]);
    $professor = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($professor && password_verify($senha, $professor['senha'])) {
        $_SESSION['professor_id']   = $professor['id'];
        $_SESSION['professor_nome'] = $professor['nome'];
        header('Location: dashboard.php');
        exit;
    } else {
        $erro = 'E-mail ou senha incorretos.';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Gramatic Mistery — Login</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'Nunito', sans-serif;
    background: #0f172a;
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
  }
  .card {
    background: #1e293b;
    border: 1px solid #334155;
    border-radius: 24px;
    padding: 48px 40px;
    width: 100%;
    max-width: 420px;
    box-shadow: 0 25px 50px rgba(0,0,0,0.5);
  }
  .logo { text-align: center; margin-bottom: 32px; }
  .logo .emoji { font-size: 48px; display: block; margin-bottom: 8px; }
  .logo h1 { font-size: 26px; font-weight: 800; color: #f8fafc; }
  .logo span { color: #6366f1; }
  .logo p { color: #94a3b8; font-size: 14px; margin-top: 4px; }
  .form-group { margin-bottom: 20px; }
  label { display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
  input {
    width: 100%; padding: 12px 16px;
    background: #0f172a; border: 1px solid #334155;
    border-radius: 12px; color: #f8fafc;
    font-family: 'Nunito', sans-serif; font-size: 15px; outline: none;
    transition: border-color 0.2s;
  }
  input:focus { border-color: #6366f1; }
  .erro { background: #450a0a; border: 1px solid #dc2626; color: #fca5a5; padding: 12px 16px; border-radius: 12px; font-size: 14px; margin-bottom: 20px; }
  button {
    width: 100%; padding: 14px; background: #6366f1; color: #fff;
    border: none; border-radius: 12px; font-family: 'Nunito', sans-serif;
    font-size: 16px; font-weight: 700; cursor: pointer; transition: background 0.2s;
  }
  button:hover { background: #4f46e5; }
</style>
</head>
<body>
<div class="card">
  <div class="logo">
    <span class="emoji">🔮</span>
    <h1>Gramatic <span>Mistery</span></h1>
    <p>Acesse o painel do professor</p>
  </div>
  <?php if ($erro): ?>
    <div class="erro">❌ <?= htmlspecialchars($erro) ?></div>
  <?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>E-mail</label>
      <input type="email" name="email" placeholder="seu@email.com" required>
    </div>
    <div class="form-group">
      <label>Senha</label>
      <input type="password" name="senha" placeholder="••••••••" required>
    </div>
    <button type="submit">Entrar 🚀</button>
  </form>
</div>
</body>
</html>
