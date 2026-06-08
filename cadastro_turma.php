<?php
session_start();
if (!isset($_SESSION['professor_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexao.php';

$sucesso = '';
$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $descricao = trim($_POST['descricao']);

    if ($nome) {
        $stmt = $pdo->prepare("INSERT INTO turmas (professor_id, nome, descricao) VALUES (?, ?, ?)");
        $stmt->execute([$_SESSION['professor_id'], $nome, $descricao]);
        $sucesso = "Turma '$nome' cadastrada com sucesso!";
    } else {
        $erro = "O nome da turma é obrigatório.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Nova Turma — Gramatic Mistery</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Nunito', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; }
  .card { background: #1e293b; border: 1px solid #334155; border-radius: 24px; padding: 40px; width: 100%; max-width: 480px; }
  h1 { font-size: 22px; font-weight: 800; margin-bottom: 24px; }
  .form-group { margin-bottom: 20px; }
  label { display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
  input, textarea {
    width: 100%; padding: 12px 16px;
    background: #0f172a; border: 1px solid #334155;
    border-radius: 12px; color: #f8fafc;
    font-family: 'Nunito', sans-serif; font-size: 15px; outline: none;
  }
  input:focus, textarea:focus { border-color: #6366f1; }
  textarea { resize: vertical; min-height: 80px; }
  .sucesso { background: #052e16; border: 1px solid #16a34a; color: #86efac; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
  .erro { background: #450a0a; border: 1px solid #dc2626; color: #fca5a5; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
  button { width: 100%; padding: 14px; background: #6366f1; color: #fff; border: none; border-radius: 12px; font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 700; cursor: pointer; }
  button:hover { background: #4f46e5; }
  .back { display: block; text-align: center; margin-top: 16px; color: #6366f1; text-decoration: none; font-size: 14px; }
</style>
</head>
<body>
<div class="card">
  <h1>🏫 Nova Turma</h1>
  <?php if ($sucesso): ?><div class="sucesso">✅ <?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
  <?php if ($erro): ?><div class="erro">❌ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Nome da Turma</label>
      <input type="text" name="nome" placeholder="Ex: Turma A - 2025" required>
    </div>
    <div class="form-group">
      <label>Descrição (opcional)</label>
      <textarea name="descricao" placeholder="Ex: Turma do 3º ano manhã"></textarea>
    </div>
    <button type="submit">Criar Turma 🏫</button>
  </form>
  <a href="dashboard.php" class="back">← Voltar ao painel</a>
</div>
</body>
</html>
