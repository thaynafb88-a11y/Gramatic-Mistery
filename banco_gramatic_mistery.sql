-- ═══════════════════════════════════════════════════
--  GRAMATIC MISTERY — BANCO DE DADOS
--  Tecnologia: MySQL
--  
--  Tabelas:
--    1. professores
--    2. turmas
--    3. alunos
--    4. categorias
--    5. palavras
--    6. dicas
--    7. sessoes
--    8. ranking
-- ═══════════════════════════════════════════════════

CREATE DATABASE IF NOT EXISTS gramatic_mistery
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE gramatic_mistery;


-- ═══════════════════════════════════════════════════
--  1. PROFESSORES
--  Guarda os dados de login e perfil dos professores.
--  Um professor pode ter várias turmas.
-- ═══════════════════════════════════════════════════
CREATE TABLE professores (
  id         INT          AUTO_INCREMENT PRIMARY KEY,
  nome       VARCHAR(100) NOT NULL,
  email      VARCHAR(150) NOT NULL UNIQUE,
  senha      VARCHAR(255) NOT NULL,         -- Sempre armazenar com hash (ex: bcrypt)
  avatar     VARCHAR(10)  DEFAULT '🧑‍🏫',
  criado_em  DATETIME     DEFAULT CURRENT_TIMESTAMP
);


-- ═══════════════════════════════════════════════════
--  2. TURMAS
--  Cada turma pertence a um professor.
--  Alunos são vinculados a uma turma.
-- ═══════════════════════════════════════════════════
CREATE TABLE turmas (
  id             INT          AUTO_INCREMENT PRIMARY KEY,
  professor_id   INT          NOT NULL,
  nome           VARCHAR(100) NOT NULL,     -- Ex: "Turma A - 2025"
  descricao      VARCHAR(255),
  criado_em      DATETIME     DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (professor_id) REFERENCES professores(id)
    ON DELETE CASCADE                       -- Ao deletar professor, deleta suas turmas
);


-- ═══════════════════════════════════════════════════
--  3. ALUNOS
--  Cada aluno pertence a uma turma e tem seu
--  próprio login, avatar e pontuação total.
-- ═══════════════════════════════════════════════════
CREATE TABLE alunos (
  id           INT          AUTO_INCREMENT PRIMARY KEY,
  turma_id     INT          NOT NULL,
  nome         VARCHAR(100) NOT NULL,
  email        VARCHAR(150) UNIQUE,         -- Opcional para alunos pequenos
  senha        VARCHAR(255) NOT NULL,       -- Hash bcrypt
  avatar       VARCHAR(10)  DEFAULT '🐻',
  total_estrelas INT        DEFAULT 0,      -- Pontuação acumulada de todas as sessões
  criado_em    DATETIME     DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (turma_id) REFERENCES turmas(id)
    ON DELETE CASCADE                       -- Ao deletar turma, deleta seus alunos
);


-- ═══════════════════════════════════════════════════
--  4. CATEGORIAS
--  Cada categoria é criada por um professor
--  e contém várias palavras.
--  Ex: Colors, Animals, Fruits...
-- ═══════════════════════════════════════════════════
CREATE TABLE categorias (
  id             INT         AUTO_INCREMENT PRIMARY KEY,
  professor_id   INT         NOT NULL,
  nome_en        VARCHAR(50) NOT NULL,      -- Nome em inglês (ex: "Colors")
  nome_pt        VARCHAR(50) NOT NULL,      -- Nome em português (ex: "Cores")
  icone          VARCHAR(10) DEFAULT '📂',  -- Emoji do ícone
  criado_em      DATETIME    DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (professor_id) REFERENCES professores(id)
    ON DELETE CASCADE
);


-- ═══════════════════════════════════════════════════
--  5. PALAVRAS
--  Cada palavra pertence a uma categoria.
--  Contém a palavra em inglês, tradução e emoji.
-- ═══════════════════════════════════════════════════
CREATE TABLE palavras (
  id           INT         AUTO_INCREMENT PRIMARY KEY,
  categoria_id INT         NOT NULL,
  palavra_en   VARCHAR(100) NOT NULL,       -- Palavra em inglês (ex: "Red")
  palavra_pt   VARCHAR(100) NOT NULL,       -- Tradução (ex: "Vermelho")
  emoji        VARCHAR(10)  DEFAULT '❓',   -- Emoji representativo
  criado_em    DATETIME     DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (categoria_id) REFERENCES categorias(id)
    ON DELETE CASCADE                       -- Ao deletar categoria, deleta as palavras
);


-- ═══════════════════════════════════════════════════
--  6. DICAS
--  Cada palavra pode ter até 3 dicas para o
--  Modo Mistério. Armazenadas separadamente
--  para facilitar adição e edição.
-- ═══════════════════════════════════════════════════
CREATE TABLE dicas (
  id         INT          AUTO_INCREMENT PRIMARY KEY,
  palavra_id INT          NOT NULL,
  ordem      TINYINT      NOT NULL DEFAULT 1, -- 1, 2 ou 3 (ordem de exibição)
  texto      VARCHAR(255) NOT NULL,            -- Texto da dica (ex: "Cor do tomate 🍅")

  FOREIGN KEY (palavra_id) REFERENCES palavras(id)
    ON DELETE CASCADE,                         -- Ao deletar palavra, deleta suas dicas

  UNIQUE KEY unica_ordem_por_palavra (palavra_id, ordem) -- Evita dicas duplicadas na mesma ordem
);


-- ═══════════════════════════════════════════════════
--  7. SESSÕES
--  Registra cada vez que um aluno joga.
--  Guarda modo, categoria, pontuação e data.
--  Serve como histórico detalhado de progresso.
-- ═══════════════════════════════════════════════════
CREATE TABLE sessoes (
  id           INT         AUTO_INCREMENT PRIMARY KEY,
  aluno_id     INT         NOT NULL,
  categoria_id INT         NOT NULL,
  modo         ENUM('learn','quiz','mystery') NOT NULL,  -- Modo jogado
  pontuacao    INT         DEFAULT 0,        -- Estrelas ganhas nesta sessão
  total_questoes TINYINT   DEFAULT 5,        -- Quantidade de perguntas da sessão
  acertos      TINYINT     DEFAULT 0,        -- Quantas o aluno acertou
  jogado_em    DATETIME    DEFAULT CURRENT_TIMESTAMP,

  FOREIGN KEY (aluno_id)     REFERENCES alunos(id)     ON DELETE CASCADE,
  FOREIGN KEY (categoria_id) REFERENCES categorias(id) ON DELETE CASCADE
);


-- ═══════════════════════════════════════════════════
--  8. RANKING
--  Armazena a melhor pontuação de cada aluno.
--  Atualizada ao final de cada sessão se o aluno
--  superar sua pontuação anterior.
-- ═══════════════════════════════════════════════════
CREATE TABLE ranking (
  id             INT  AUTO_INCREMENT PRIMARY KEY,
  aluno_id       INT  NOT NULL UNIQUE,       -- Um registro por aluno
  total_estrelas INT  DEFAULT 0,             -- Pontuação total acumulada
  atualizado_em  DATETIME DEFAULT CURRENT_TIMESTAMP
                      ON UPDATE CURRENT_TIMESTAMP, -- Atualiza a data automaticamente

  FOREIGN KEY (aluno_id) REFERENCES alunos(id)
    ON DELETE CASCADE
);


-- ═══════════════════════════════════════════════════
--  DADOS INICIAIS — CATEGORIAS PADRÃO
--  Inseridas com professor_id = 1 (primeiro professor
--  cadastrado no sistema, o administrador padrão).
-- ═══════════════════════════════════════════════════

-- Professor administrador padrão
-- ATENÇÃO: troque a senha antes de usar em produção!
INSERT INTO professores (nome, email, senha, avatar) VALUES
('Admin', 'admin@gramatic.com', '$2y$10$examplehashhere', '🧑‍🏫');

-- Categorias padrão
INSERT INTO categorias (professor_id, nome_en, nome_pt, icone) VALUES
(1, 'Colors',  'Cores',    '🎨'),
(1, 'Animals', 'Animais',  '🐾'),
(1, 'Fruits',  'Frutas',   '🍎'),
(1, 'Numbers', 'Números',  '🔢'),
(1, 'Shapes',  'Formas',   '🔷'),
(1, 'Family',  'Família',  '👨‍👩‍👧');

-- Palavras — Colors
INSERT INTO palavras (categoria_id, palavra_en, palavra_pt, emoji) VALUES
(1, 'Red',    'Vermelho', '🔴'),
(1, 'Blue',   'Azul',     '🔵'),
(1, 'Yellow', 'Amarelo',  '🟡'),
(1, 'Green',  'Verde',    '🟢'),
(1, 'Pink',   'Rosa',     '🩷'),
(1, 'Purple', 'Roxo',     '🟣');

-- Dicas — Red
INSERT INTO dicas (palavra_id, ordem, texto) VALUES
(1, 1, 'É a cor do tomate 🍅'),
(1, 2, 'Parece fogo 🔥'),
(1, 3, 'Cor de coração ❤️');

-- Dicas — Blue
INSERT INTO dicas (palavra_id, ordem, texto) VALUES
(2, 1, 'Cor do céu ☁️'),
(2, 2, 'Cor do mar 🌊'),
(2, 3, 'Cor do gelo ❄️');

-- Palavras — Animals
INSERT INTO palavras (categoria_id, palavra_en, palavra_pt, emoji) VALUES
(2, 'Dog',    'Cachorro', '🐶'),
(2, 'Cat',    'Gato',     '🐱'),
(2, 'Rabbit', 'Coelho',   '🐰'),
(2, 'Bear',   'Urso',     '🐻'),
(2, 'Bird',   'Pássaro',  '🐦'),
(2, 'Fish',   'Peixe',    '🐟');


-- ═══════════════════════════════════════════════════
--  VIEWS ÚTEIS
--  Consultas prontas para o painel do professor.
-- ═══════════════════════════════════════════════════

-- View: ranking geral com nome e turma do aluno
CREATE VIEW view_ranking AS
SELECT
  r.total_estrelas,
  a.nome        AS aluno,
  a.avatar,
  t.nome        AS turma,
  r.atualizado_em
FROM ranking r
JOIN alunos a ON a.id = r.aluno_id
JOIN turmas t ON t.id = a.turma_id
ORDER BY r.total_estrelas DESC;

-- View: histórico de sessões com detalhes
CREATE VIEW view_historico AS
SELECT
  s.jogado_em,
  a.nome        AS aluno,
  t.nome        AS turma,
  c.nome_pt     AS categoria,
  s.modo,
  s.acertos,
  s.total_questoes,
  s.pontuacao
FROM sessoes s
JOIN alunos     a ON a.id = s.aluno_id
JOIN turmas     t ON t.id = a.turma_id
JOIN categorias c ON c.id = s.categoria_id
ORDER BY s.jogado_em DESC;
