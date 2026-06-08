<?php
session_start();
if (!isset($_SESSION['professor_id'])) {
    header('Location: login.php');
    exit;
}
require_once 'conexao.php';

$sucesso = '';
$erro    = '';

// Buscar turmas do professor logado
$stmt = $pdo->prepare("SELECT * FROM turmas WHERE professor_id = ? ORDER BY nome");
$stmt->execute([$_SESSION['professor_id']]);
$turmas = $stmt->fetchAll(PDO::FETCH_ASSOC);

$turma_selecionada = $_GET['turma_id'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome     = trim($_POST['nome']);
    $email    = trim($_POST['email']) ?: null;
    $senha    = trim($_POST['senha']);
    $turma_id = $_POST['turma_id'];
    $avatar   = $_POST['avatar'] ?? '🐻';

    if ($nome && $senha && $turma_id) {
        $hash = password_hash($senha, PASSWORD_BCRYPT);
        try {
            $stmt = $pdo->prepare("INSERT INTO alunos (turma_id, nome, email, senha, avatar) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$turma_id, $nome, $email, $hash, $avatar]);
            $sucesso = "Aluno '$nome' cadastrado com sucesso!";
        } catch (PDOException $e) {
            $erro = "Erro: e-mail já cadastrado.";
        }
    } else {
        $erro = "Nome, senha e turma são obrigatórios.";
    }
}

$avatares = ['🐻','🐶','🐱','🐰','🦊','🐼','🐨','🐯','🦁','🐸'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Cadastrar Aluno — Gramatic Mistery</title>
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet">
<style>
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body { font-family: 'Nunito', sans-serif; background: #0f172a; color: #f8fafc; min-height: 100vh; display: flex; align-items: center; justify-content: center; padding: 24px; }
  .card { background: #1e293b; border: 1px solid #334155; border-radius: 24px; padding: 40px; width: 100%; max-width: 500px; }
  h1 { font-size: 22px; font-weight: 800; margin-bottom: 24px; }
  .form-group { margin-bottom: 20px; }
  label { display: block; color: #cbd5e1; font-size: 14px; font-weight: 600; margin-bottom: 6px; }
  input, select {
    width: 100%; padding: 12px 16px;
    background: #0f172a; border: 1px solid #334155;
    border-radius: 12px; color: #f8fafc;
    font-family: 'Nunito', sans-serif; font-size: 15px; outline: none;
  }
  input:focus, select:focus { border-color: #6366f1; }
  select option { background: #1e293b; }
  .avatares { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
  .avatares label { cursor: pointer; }
  .avatares input[type=radio] { display: none; }
  .avatares input[type=radio] + span {
    display: inline-block; font-size: 28px; padding: 6px;
    border: 2px solid transparent; border-radius: 10px; transition: border-color 0.2s;
  }
  .avatares input[type=radio]:checked + span { border-color: #6366f1; background: #1e1b4b; }
  .sucesso { background: #052e16; border: 1px solid #16a34a; color: #86efac; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
  .erro { background: #450a0a; border: 1px solid #dc2626; color: #fca5a5; padding: 12px 16px; border-radius: 12px; margin-bottom: 20px; font-size: 14px; }
  button { width: 100%; padding: 14px; background: #6366f1; color: #fff; border: none; border-radius: 12px; font-family: 'Nunito', sans-serif; font-size: 16px; font-weight: 700; cursor: pointer; }
  button:hover { background: #4f46e5; }
  .back { display: block; text-align: center; margin-top: 16px; color: #6366f1; text-decoration: none; font-size: 14px; }
</style>
</head>
<body>
<div class="card">
  <h1>👦 Cadastrar Aluno</h1>
  <?php if ($sucesso): ?><div class="sucesso">✅ <?= htmlspecialchars($sucesso) ?></div><?php endif; ?>
  <?php if ($erro): ?><div class="erro">❌ <?= htmlspecialchars($erro) ?></div><?php endif; ?>
  <form method="POST">
    <div class="form-group">
      <label>Turma</label>
      <select name="turma_id" required>
        <option value="">Selecione a turma</option>
        <?php foreach ($turmas as $t): ?>
          <option value="<?= $t['id'] ?>" <?= $t['id'] == $turma_selecionada ? 'selected' : '' ?>>
            <?= htmlspecialchars($t['nome']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>
    <div class="form-group">
      <label>Nome do Aluno</label>
      <input type="text" name="nome" placeholder="Ex: João Silva" required>
    </div>
    <div class="form-group">
      <label>E-mail (opcional)</label>
      <input type="email" name="email" placeholder="aluno@email.com">
    </div>
    <div class="form-group">
      <label>Senha</label>
      <input type="password" name="senha" placeholder="Mínimo 6 caracteres" required>
    </div>
    <div class="form-group">
      <label>Escolha um avatar</label>
      <div class="avatares">
        <?php foreach ($avatares as $i => $av): ?>
          <label>
            <input type="radio" name="avatar" value="<?= $av ?>" <?= $i === 0 ? 'checked' : '' ?>>
            <span><?= $av ?></span>
          </label>
        <?php endforeach; ?>
      </div>
    </div>
    <button type="submit">Cadastrar Aluno 👦</button>
  </form>
  <a href="dashboard.php" class="back">← Voltar ao painel</a>
</div>
</body>
</html>
