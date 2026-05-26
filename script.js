// ═══════════════════════════════════════════════════
//  ESTADO GLOBAL
//  Variáveis que guardam as informações do jogador
//  durante toda a sessão no navegador.
// ═══════════════════════════════════════════════════

let playerName = "Jogador";     // Nome digitado no login (padrão: "Jogador")
let selectedAvatar = "🐻";      // Avatar em seleção antes de confirmar o login
let playerAvatar = "🐻";        // Avatar confirmado após o login

let totalStars = 0;             // Total de estrelas acumuladas em todas as sessões


// ═══════════════════════════════════════════════════
//  LOGIN
// ═══════════════════════════════════════════════════

/**
 * Marca visualmente o avatar clicado como selecionado
 * e armazena o emoji correspondente em `selectedAvatar`.
 *
 * @param {HTMLElement} btn   - Botão do avatar que foi clicado
 * @param {string}      emoji - Emoji do avatar (ex: "🐱")
 */
function selectAvatar(btn, emoji){

  // Remove a marcação de seleção de todos os botões de avatar
  document
    .querySelectorAll(".avatar-btn")
    .forEach(button => {
      button.classList.remove("selected");
    });

  // Destaca apenas o botão clicado
  btn.classList.add("selected");

  // Salva o avatar escolhido (ainda não confirmado)
  selectedAvatar = emoji;

  playClick(); // Som de feedback ao selecionar
}

/**
 * Processa o envio do formulário de login:
 * - Lê o nome digitado (usa "Jogador" se estiver vazio)
 * - Confirma o avatar selecionado
 * - Atualiza os elementos visuais da tela Home
 * - Navega para a Home e toca som de confirmação
 */
function doLogin(){

  // Lê e limpa espaços do nome digitado
  const input =
    document
      .getElementById("player-name-input")
      .value
      .trim();

  // Se o campo estiver vazio, usa o nome padrão
  playerName = input || "Jogador";

  // Confirma o avatar que estava em seleção
  playerAvatar = selectedAvatar;

  // Atualiza o avatar exibido na tela Home
  document.getElementById("home-avatar").textContent =
    playerAvatar;

  // Atualiza a saudação com nome e avatar do jogador
  document.getElementById("greeting-name").textContent =
    `Olá, ${playerName}! ${playerAvatar}`;

  goTo("screen-home"); // Navega para a tela principal

  playCorrect(); // Som de boas-vindas ao entrar no jogo
}


// ═══════════════════════════════════════════════════
//  NAVEGAÇÃO ENTRE TELAS (SPA)
//  O jogo funciona como Single Page Application:
//  apenas uma tela fica visível por vez.
//  A classe CSS "active" controla qual tela aparece.
// ═══════════════════════════════════════════════════

/**
 * Troca a tela visível:
 * - Remove "active" de todas as telas
 * - Adiciona "active" apenas na tela alvo
 *
 * @param {string} id - ID da tela destino (ex: "screen-home")
 */
function goTo(id){

  // Oculta todas as telas removendo a classe ativa
  document
    .querySelectorAll(".screen")
    .forEach(screen => {
      screen.classList.remove("active");
    });

  // Exibe apenas a tela com o ID informado
  document
    .getElementById(id)
    .classList.add("active");

  playClick(); // Som de navegação
}


// ═══════════════════════════════════════════════════
//  POPUP DE FEEDBACK RÁPIDO
//  Exibe um popup centralizado brevemente após
//  acertos, erros ou ações importantes do jogador.
// ═══════════════════════════════════════════════════

/**
 * Mostra o popup de feedback e o fecha automaticamente.
 *
 * @param {string} emoji    - Emoji exibido no topo (ex: "🎉")
 * @param {string} text     - Texto principal (ex: "Acertou!")
 * @param {string} sub      - Subtexto opcional (ex: "+10 ⭐") — padrão: ""
 * @param {number} duration - Tempo em ms antes de fechar  — padrão: 1000
 */
function showFeedback(
  emoji,
  text,
  sub = "",
  duration = 1000
){

  // Preenche o conteúdo do popup
  document.getElementById("fbEmoji").textContent = emoji;
  document.getElementById("fbText").textContent  = text;
  document.getElementById("fbSub").textContent   = sub;

  // Exibe o overlay escurecido e o popup (animação via CSS)
  document
    .getElementById("fbOverlay")
    .classList.add("show");

  document
    .getElementById("fbPopup")
    .classList.add("show");

  // Fecha automaticamente após o tempo definido
  setTimeout(() => {

    document
      .getElementById("fbOverlay")
      .classList.remove("show");

    document
      .getElementById("fbPopup")
      .classList.remove("show");

  }, duration);
}


// ═══════════════════════════════════════════════════
//  MOTOR DE ÁUDIO (Web Audio API)
//  Gera todos os sons do jogo sinteticamente,
//  sem precisar de arquivos de áudio externos.
//  Usa osciladores e envelopes de ganho.
// ═══════════════════════════════════════════════════

let audioCtx = null; // Instância única do AudioContext (criada sob demanda)

/**
 * Retorna o AudioContext existente ou cria um novo.
 * A criação lazy evita o bloqueio que browsers impõem
 * sobre contextos de áudio criados antes de uma interação do usuário.
 *
 * @returns {AudioContext}
 */
function getAudioCtx(){

  if(!audioCtx){

    // Usa o prefixo webkit como fallback para browsers mais antigos
    audioCtx =
      new (
        window.AudioContext ||
        window.webkitAudioContext
      )();

  }

  return audioCtx;
}

/**
 * Toca um tom sintetizado com oscilador e envelope de volume.
 *
 * @param {number} freq   - Frequência em Hz (ex: 440 = Lá)
 * @param {number} dur    - Duração em segundos
 * @param {string} type   - Forma de onda: "sine" | "square" | "sawtooth" | "triangle"
 * @param {number} volume - Volume inicial de 0 a 1 (padrão: 0.2)
 */
function playTone(
  freq,
  dur,
  type = "sine",
  volume = 0.2
){

  try{

    const ctx = getAudioCtx();

    const osc  = ctx.createOscillator(); // Gerador de onda sonora
    const gain = ctx.createGain();       // Controle de volume

    // Cadeia de áudio: oscilador → ganho → saída do dispositivo
    osc.connect(gain);
    gain.connect(ctx.destination);

    // Define a forma de onda e frequência do oscilador
    osc.type = type;
    osc.frequency.setValueAtTime(freq, ctx.currentTime);

    // Envelope de volume: começa em `volume` e decai suavemente até quase zero
    // Isso evita o "clique" (pop) abrupto ao final do som
    gain.gain.setValueAtTime(volume, ctx.currentTime);
    gain.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + dur);

    osc.start();                        // Inicia o oscilador imediatamente
    osc.stop(ctx.currentTime + dur);    // Para após a duração definida

  }catch(e){} // Silencia erros (ex: tab em segundo plano, autoplay bloqueado)
}

/**
 * Som de clique suave para navegação e seleção de itens.
 * Usa onda triangular (timbre mais suave que seno).
 */
function playClick(){

  playTone(
    440,   // Lá (440 Hz)
    0.06,  // Muito curto (60ms)
    "triangle",
    0.12   // Volume baixo
  );
}

/**
 * Som de acerto: sequência ascendente de três notas
 * formando um acorde de Dó maior (Dó → Mi → Sol).
 * Cada nota entra com 110ms de intervalo.
 */
function playCorrect(){

  playTone(523, 0.1);  // Dó (C5)

  setTimeout(() => {
    playTone(659, 0.1); // Mi (E5)
  }, 110);

  setTimeout(() => {
    playTone(784, 0.18); // Sol (G5) — duração ligeiramente maior para finalizar
  }, 220);
}
