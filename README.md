# 🐻 Gramatic Mistery

> Aprenda inglês brincando com quizzes, cards interativos e o desafio do Modo Mistério!

---

## 📖 Sobre o Projeto

**Gramatic Mistery** é um jogo educativo web voltado para crianças aprenderem vocabulário em inglês de forma divertida. O jogador escolhe uma categoria, explora as palavras com emojis e áudio, e pode se desafiar em quizzes ou no modo especial de adivinhação por dicas.


## ✨ Funcionalidades

- 🔐 **Login personalizado** — nome do jogador e escolha de avatar (emoji)
- 📚 **Modo Aprender** — cards com emoji, palavra em inglês, tradução e pronúncia por TTS
- ❓ **Modo Quiz** — 5 perguntas por sessão com 4 opções de resposta
- 🔮 **Modo Mistério** — adivinhe a palavra usando dicas progressivas (bônus maior com menos dicas!)
- 🏆 **Ranking** — placar dos 8 melhores jogadores da sessão
- 🔊 **Áudio sintetizado** — sons de clique, acerto, erro e melodia de fundo gerados via Web Audio API
- 🗣️ **Pronúncia (TTS)** — leitura das palavras em inglês americano via SpeechSynthesis
- ⚙️ **Configurações** — toggle de som, música, TTS, idioma da interface e troca de jogador

---

## 🗂️ Categorias Disponíveis

| Emoji | Categoria | Conteúdo |
|-------|-----------|----------|
| 🎨 | Colors | Red, Blue, Yellow, Green, Pink, Purple |
| 🐾 | Animals | Dog, Cat, Rabbit, Bear, Bird, Fish |
| 🍎 | Fruits | Apple, Banana, Grape, Orange, Strawberry, Watermelon |
| 🔢 | Numbers | One, Two, Three, Four, Five, Six |
| 🔷 | Shapes | Circle, Square, Triangle, Star, Heart, Diamond |
| 👨‍👩‍👧 | Family | Mother, Father, Sister, Brother, Baby, Grandma |

---

## 🎮 Como Jogar

1. Abra o arquivo `index.html` no navegador
2. Digite seu nome e escolha um avatar
3. Clique em **🎮 JOGAR!** para acessar o menu principal
4. Escolha uma categoria e selecione o modo:
   - **📚 Aprender** → toque nos cards para ver e ouvir cada palavra
   - **❓ Quiz** → responda 5 perguntas e acumule estrelas
   - **🔮 Modo Mistério** → use as dicas para descobrir a palavra secreta

---

## 🏅 Sistema de Pontuação

### Quiz
| Resultado | Pontos |
|-----------|--------|
| Acerto | +10 ⭐ por pergunta |
| Máximo por sessão | 50 ⭐ |

### Modo Mistério
A pontuação é inversamente proporcional ao número de dicas usadas:

| Dicas usadas | Bônus |
|-------------|-------|
| 1 dica | +30 ⭐ |
| 2 dicas | +22 ⭐ |
| 3 dicas | +14 ⭐ |
| Mínimo | +10 ⭐ |

### Estrelas de desempenho
| Percentual de acertos | Estrelas |
|----------------------|----------|
| ≥ 90% | ⭐⭐⭐ |
| ≥ 50% | ⭐⭐ |
| < 50% | ⭐ |

---

## 🗃️ Estrutura de Arquivos

```
gramatic-mistery/
│
├── index.html       # Estrutura HTML de todas as telas
├── style.css        # Estilização completa (variáveis, animações, layout)
└── script.js        # Lógica do jogo (áudio, navegação, quiz, mistério, ranking)
```

> A versão `gramatic-mistery.html` é uma build single-file com tudo embutido (HTML + CSS + JS).

---

## 🛠️ Tecnologias

| Tecnologia | Uso |
|------------|-----|
| HTML5 | Estrutura e telas do jogo |
| CSS3 | Animações, variáveis e layout responsivo |
| JavaScript (ES6+) | Lógica, áudio e navegação |
| Web Audio API | Geração de sons sintetizados |
| SpeechSynthesis API | Pronúncia das palavras em inglês |
| Google Fonts | Baloo 2 (títulos) e Nunito (corpo) |

---

## 📐 Arquitetura do CSS

O projeto usa **Design Tokens** via variáveis CSS em `:root`:

```css
--yellow, --pink, --blue, --green
--purple, --orange, --red
--white, --gray-light, --gray-dark
--shadow, --shadow-btn
```

As telas funcionam como um **SPA** simples: todas ficam ocultas (`display:none`) e apenas a tela com classe `.active` é exibida (`display:flex`).

---

## 🔊 Motor de Áudio

Todos os sons são sintetizados com **Web Audio API**, sem arquivos externos:

| Função | Som |
|--------|-----|
| `playClick()` | Clique suave de navegação |
| `playCorrect()` | Acorde ascendente Dó → Mi → Sol |
| `playWrong()` | Dois tons graves com onda sawtooth |
| `playReveal()` | Dois tons agudos de revelação |
| `playVictory()` | Fanfarra de 4 notas |
| `playMystery()` | Escala mística ascendente |
| `playClue()` | Tom suave ao revelar dica |
| `startMusic()` | Melodia de fundo em loop |

---

## 🚀 Próximas Melhorias (ideias)

- [ ] Persistência do ranking com `localStorage`
- [ ] Mais categorias (Clothes, Food, Body Parts...)
- [ ] Nível de dificuldade (fácil / médio / difícil)
- [ ] Animações de transição entre telas
- [ ] Suporte a temas de cor (claro / escuro)
- [ ] Carregar categorias de um arquivo JSON externo

---

## 👩‍💻 Autora

Feito com 💜 como projeto de aprendizado de inglês para crianças.
