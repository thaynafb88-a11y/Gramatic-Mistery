<?php
require_once 'conexao.php';

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome  = trim($_POST['nome']);
    $email = trim($_POST['email']);
    $senha = trim($_POST['senha']);

    if ($nome && $email && $senha) {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO professores (nome, email, senha) VALUES (?, ?, ?)");
            $stmt->execute([$nome, $email, $hash]);
            $sucesso = "Professor '$nome' cadastrado! Agora pode fazer login.";
        } catch (PDOException $e) {
            $erro = "E-mail já cadastrado.";
        }
    } else {
        $erro = "Todos os campos são obrigatórios.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Cadastro de Professor</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Nunito', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .card { background: #1e293b; border: 1px solid #334155; border-radius: 24px; padding: 40px; width: 100%; max-width: 420px; }
  h1 { font-size: 22px; font-weight: 800; margin-bottom: 24px; }
  .form-group { margin-bottom: 20px; }
  label { display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
  input { width: 100%; padding: 12px 16px; background: #0f172a; border: 1px solid #334155; border-radius: 12px; color: #f8fafc; font-family: 'Nunito', sans-serif; font-size: 15px; outline: none; }
  input:focus { border-color: #6366f1; }
  .sucesso { background: #052e16; border: 1px solid #16a34a; color: #86efac; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
  .erro { background: #450a0a; border: 1px solid #dc2626; color: #fca5a5; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
  button { width: 100%; padding: 14px; background: #6366f1; color: #fff; border: none; border-radius: 12px; font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 700; cursor: pointer; }
  button:hover { background: #4f46e5; }
  .back { display: block; text-align: center; margin-top: 16px; color: #6366f1; text-decoration: none; font-size: 14px; }
</style>
</head>
<body>
<div class="card">
  <h1>🧑‍🏫 Cadastro de Professor</h1>
  <?php if ($sucesso): ?><div class="sucesso">✅ <?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
  <?php if ($erro): ?><div class="erro">❌ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Nome</label>
      <input type="text" name="nome" placeholder="Seu nome completo" required>
    </div>
    <div class="form-group">
      <label>E-mail</label>
      <input type="email" name="email" placeholder="seu@email.com" required>
    </div>
    <div class="form-group">
      <label>Senha</label>
      <input type="password" name="senha" placeholder="Mínimo 6 caracteres" required>
    </div>
    <button type="submit">Cadastrar 🧑‍🏫</button>
  </form>
  <a href="login.php" class="back">← Ir para o login</a>
</div>
</body>
</html>
