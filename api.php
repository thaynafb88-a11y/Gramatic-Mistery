<?php
session_start();
require_once 'conexao.php';
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

// LOGIN DO ALUNO
if ($action === 'login') {
    $nome  = trim($_POST['nome']  ?? '');
    $senha = trim($_POST['senha'] ?? '');
    $s = $pdo->prepare("SELECT * FROM alunos WHERE nome = ?");
    $s->execute([$nome]);
    $aluno = $s->fetch(PDO::FETCH_ASSOC);
    if ($aluno && password_verify($senha, $aluno['senha'])) {
        $_SESSION['aluno_id']     = $aluno['id'];
        $_SESSION['aluno_nome']   = $aluno['nome'];
        $avatar = (empty($aluno['avatar']) || $aluno['avatar'] === '?') ? '🐻' : $aluno['avatar'];
        $_SESSION['aluno_avatar'] = $avatar;
        echo json_encode(['ok'=>true, 'nome'=>$aluno['nome'], 'avatar'=>$avatar, 'estrelas'=>$aluno['total_estrelas']]);
    } else {
        echo json_encode(['ok'=>false, 'msg'=>'Nome ou senha incorretos!']);
    }
    exit;
}

// LOGOUT
if ($action === 'logout') {
    session_destroy();
    echo json_encode(['ok'=>true]);
    exit;
}

// CARREGAR DADOS (categorias + palavras + dicas + ranking)
if ($action === 'dados') {
    $stmt = $pdo->query("
        SELECT c.id as cat_id, c.nome_en, c.nome_pt, c.icone,
               p.id as palavra_id, p.palavra_en, p.palavra_pt, p.emoji,
               GROUP_CONCAT(d.texto ORDER BY d.ordem SEPARATOR '|||') as dicas
        FROM categorias c
        LEFT JOIN palavras p ON p.categoria_id = c.id
        LEFT JOIN dicas d ON d.palavra_id = p.id
        GROUP BY c.id, p.id
        ORDER BY c.id, p.id
    ");
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $categorias = [];
    foreach ($rows as $row) {
        $key = strtolower(str_replace(' ', '_', $row['nome_en']));
        if (!isset($categorias[$key])) {
            $categorias[$key] = [
                'id'      => $row['cat_id'],
                'nome_en' => $row['nome_en'],
                'nome_pt' => $row['nome_pt'],
                'icone'   => $row['icone'],
                'palavras'=> []
            ];
        }
        if ($row['palavra_id']) {
            $clues = $row['dicas'] ? explode('|||', $row['dicas']) : [];
            // Fallback de emojis caso o banco não suporte UTF-8 completo
            $emojiMap = [
                'Red'=>'🔴','Blue'=>'🔵','Yellow'=>'🟡','Green'=>'🟢','Pink'=>'🩷','Purple'=>'🟣',
                'Dog'=>'🐶','Cat'=>'🐱','Rabbit'=>'🐰','Bear'=>'🐻','Bird'=>'🐦','Fish'=>'🐟',
                'Apple'=>'🍎','Banana'=>'🍌','Grape'=>'🍇','Orange'=>'🍊','Strawberry'=>'🍓','Watermelon'=>'🍉',
                'One'=>'1️⃣','Two'=>'2️⃣','Three'=>'3️⃣','Four'=>'4️⃣','Five'=>'5️⃣','Six'=>'6️⃣',
                'Circle'=>'⭕','Square'=>'🟥','Triangle'=>'🔺','Star'=>'⭐','Heart'=>'❤️','Diamond'=>'💎',
                'Mother'=>'👩','Father'=>'👨','Sister'=>'👧','Brother'=>'👦','Baby'=>'👶','Grandma'=>'👵'
            ];
            $emoji = (empty($row['emoji']) || $row['emoji'] === '?') 
                ? ($emojiMap[$row['palavra_en']] ?? '❓') 
                : $row['emoji'];
            $categorias[$key]['palavras'][] = [
                'en'    => $row['palavra_en'],
                'pt'    => $row['palavra_pt'],
                'emoji' => $emoji,
                'clues' => $clues
            ];
        }
    }

    $ranking = $pdo->query("
        SELECT a.nome, a.avatar, r.total_estrelas
        FROM ranking r
        JOIN alunos a ON a.id = r.aluno_id
        ORDER BY r.total_estrelas DESC
        LIMIT 8
    ")->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['ok'=>true, 'categorias'=>$categorias, 'ranking'=>$ranking]);
    exit;
}

// SALVAR PONTUAÇÃO
if ($action === 'salvar' && isset($_SESSION['aluno_id'])) {
    $aluno_id  = $_SESSION['aluno_id'];
    $cat_id    = intval($_POST['categoria_id']);
    $modo      = $_POST['modo'];
    $pontuacao = intval($_POST['pontuacao']);
    $acertos   = intval($_POST['acertos']);
    $total_q   = intval($_POST['total_questoes']);

    $pdo->prepare("INSERT INTO sessoes (aluno_id,categoria_id,modo,pontuacao,total_questoes,acertos) VALUES (?,?,?,?,?,?)")
        ->execute([$aluno_id,$cat_id,$modo,$pontuacao,$total_q,$acertos]);
    $pdo->prepare("UPDATE alunos SET total_estrelas = total_estrelas + ? WHERE id = ?")
        ->execute([$pontuacao,$aluno_id]);
    $pdo->prepare("INSERT INTO ranking (aluno_id,total_estrelas) VALUES (?,?) ON DUPLICATE KEY UPDATE total_estrelas = total_estrelas + ?")
        ->execute([$aluno_id,$pontuacao,$pontuacao]);

    echo json_encode(['ok'=>true]);
    exit;
}

echo json_encode(['ok'=>false, 'msg'=>'Ação inválida']);
