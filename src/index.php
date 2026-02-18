<?php
session_start();
$isLoggedIn = isset($_SESSION['user_id']);
$nombreUsuario = $isLoggedIn ? $_SESSION['user_real_name'] : '';
?>

<!DOCTYPE html>
<html lang="ca">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Per L'Art - Joieria</title>
    
    <link href="./styles/bootstrap.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/styleIndex.css?v=<?php echo time(); ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>
<body>

    <nav class="navbar navbar-expand-lg bg-white fixed-top shadow-sm py-3">
    <div class="container-fluid px-4">
        
        <a class="navbar-brand" href="index.php">
            <img src="./contenido/logoParteArriba.png" alt="Logo" style="height: 50px;">
        </a>

        <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menuPrincipal">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menuPrincipal">
            
            <ul class="navbar-nav mx-auto mb-2 mb-lg-0 text-center align-items-center">
                <li class="nav-item"><a class="nav-link" href="productos.php">Productes</a></li>
                <li class="nav-item"><a class="nav-link" href="sobre_nosaltres.php">Sobre nosaltres</a></li>
                <li class="nav-item"><a class="nav-link" href="contacte.php">Contacte</a></li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="fontSizeDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-font"></i> <span class="d-lg-none ms-2">Mida de la lletra</span>
                    </a>
                    <ul class="dropdown-menu text-center text-lg-start" aria-labelledby="fontSizeDropdown">
                        <li><button class="dropdown-item" onclick="setFontSize('small')">Petita</button></li>
                        <li><button class="dropdown-item" onclick="setFontSize('normal')">Normal</button></li>
                        <li><button class="dropdown-item" onclick="setFontSize('large')">Gran</button></li>
                        <li><button class="dropdown-item" onclick="setFontSize('xlarge')">Molt Gran</button></li>
                    </ul>
                </li>
            </ul>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 text-center align-items-center gap-2">
                
                <?php if ($isLoggedIn): ?>
                    <li class="nav-item">
                        <a class="nav-link text-danger" href="./auth/logout.php">
                            Tancar Sessió
                        </a>
                    </li>

                <?php endif; ?>

                <li class="nav-item d-flex align-items-center justify-content-center gap-3 mt-2 mt-lg-0">
                    
                    <?php if ($isLoggedIn): ?>
                        <a class="nav-link p-0" href="./auth/profile.php">
                            <i class="fas fa-user fs-5"></i>
                        </a>
                    <?php else: ?>
                        <a class="nav-link p-0" href="./auth/login.html">
                            <i class="fas fa-user fs-5"></i>
                        </a>
                    <?php endif; ?>

                    <a class="nav-link p-0" href="#">
                        <a class="nav-link p-0 position-relative" href="carret.php">
                        <i class="fas fa-shopping-basket fs-5"></i>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger cart-count" style="font-size: 0.6rem; display:none;">
                            0
                        </span>
                    </a>
                    </a>
                </li>

            </ul>
            
        </div>
    </div>
</nav>

    <main>
        <!--<section class="hero">
            <div class="hero-content text-center text-lg-start"> <h1></h1>
                <p></p>
                <a href="productos.php" class="btn btn-primary">Explora la Col·lecció</a>
            </div>
            <div class="hero-background-image"></div>
        </section>
                    -->
        <div id="heroCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-indicators">
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="0" class="active"></button>
                <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="1"></button>
            </div>

            <div class="carousel-inner">
                <div class="carousel-item active" data-bs-interval="5000">
                    <img src="contenido/hero_mod.jpg" class="d-block w-100" alt="Slide 1" style="height: 80vh; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h1>Descobreix Peces Úniques</h1>
                        <p>La nostra nova col·lecció inspirada en la cultura popular</p>
                        <a href="#" class="btn btn-primary">Saber més</a>
                    </div>
                </div>

                <div class="carousel-item" data-bs-interval="5000">
                    <img src="contenido/hero2.webp" class="d-block w-100" alt="Slide 2" style="height: 80vh; object-fit: cover;">
                    <div class="carousel-caption d-none d-md-block">
                        <h2>Cultura que es Porta</h2>
                        <p>Més que moda, és un homenatge</p>
                        <a href="#" class="btn btn-primary">Saber més</a>
                    </div>
                </div>
            </div>

            <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon"></span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon"></span>
            </button>
        </div>
        <section class="featured-products">
            <div class="container">
                <h2>Novetats</h2>
                
<div class="row row-cols-2 row-cols-md-3 g-3 g-lg-4 justify-content-center">                    
                    <div class="col">
                        <div class="product-card mx-auto" style="max-width: 350px;"> 
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/anell_mod.jpg" alt="Anell Espiral">
                                <h3>Anell Espiral</h3>
                            </a>
                            <p class="price">14,00 €</p>
                            <button class="btn btn-icon" aria-label="Afegir al carret Anell Espiral"><i class="fas fa-shopping-cart"></i></button>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="product-card mx-auto" style="max-width: 350px;">
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/collarlibelula_mod.jpg" alt="Collaret Llibèl·lula">
                                <h3>Collaret Llibèl·lula</h3>
                            </a>
                            <p class="price">12,00 €</p>
                            <button class="btn btn-icon" aria-label="Afegir al carret Collaret Llibèl·lula"><i class="fas fa-shopping-cart"></i></button>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="product-card mx-auto" style="max-width: 350px;">
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/brasalet_mod.jpg" alt="Braçalet Dorat">
                                <h3>Braçalet Dorat</h3>
                            </a>
                            <p class="price">54,00 €</p>
                            <button class="btn btn-icon" aria-label="Afegir al carret Braçalet Dorat"><i class="fas fa-shopping-cart"></i></button>
                        </div>
                    </div>

                </div>
            </div>
        </section>

        <section class="featured-categories">
            <div class="container">
                <h2>Categories Destacades</h2>
                
                <div class="row row-cols-2 row-cols-md-3 g-3 g-lg-4 justify-content-center">
                    
                    <div class="col">
                        <div class="category-item">
                            <a class="d-block text-decoration-none text-reset">
                                <img src="./contenido/anellcoleccio_mod.jpg" alt="Veure col·lecció d'Anells">
                                <h3>Anells</h3>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="category-item">
                            <a class="d-block text-decoration-none text-reset">
                                <img src="./contenido/piercingcoleccio_mod.jpg" alt="Veure col·lecció de Piercings">
                                <h3>Piercings</h3>
                            </a>
                        </div>
                    </div>
                    
                    <div class="col">
                        <div class="category-item">
                            <a  class="d-block text-decoration-none text-reset">
                                <img src="./contenido/pulserescoleccio_mod.jpg" alt="Veure col·lecció de Pulseres">
                                <h3>Pulseres</h3>
                            </a>
                        </div>
                    </div>

                </div>
            </div>
        </section>
    </main>

    </main> <?php include 'footer.php'; ?>
    <script src="./js/logicCarret.js"></script>
    <div id="chat-circle" onclick="toggleChat()">
    <span>💬</span>
</div>

<div id="chat-box">
    <div id="chat-header">
        <span>Asistente de Pádel</span>
        <span onclick="toggleChat()" style="cursor:pointer;">×</span>
    </div>
    <div id="chat-logs"></div>
    <div class="chat-input-area">
        <input type="text" id="chat-input" placeholder="Pregúntame algo...">
        <button class="btn-send" onclick="sendMessage()">Enviar</button>
    </div>
</div>

<style>

#chat-circle {
    position: fixed;
    bottom: 30px;
    right: 30px;
    background: #2a402a; 
    width: 65px;
    height: 65px;
    border-radius: 50%;
    color: white;
    text-align: center;
    line-height: 65px;
    font-size: 30px;
    cursor: pointer;
    box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    z-index: 1000;
    transition: transform 0.3s ease;
}

#chat-circle:hover {
    transform: scale(1.1);
}

#chat-box {
    position: fixed;
    bottom: 110px;
    right: 30px;
    width: 350px;
    max-width: 85vw;
    height: 500px;
    background: var(--color-white);
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    display: none; /* Se activa por JS */
    flex-direction: column;
    overflow: hidden;
    font-family: var(--font-main);
    z-index: 1000;
    border: 1px solid #eee;
}

#chat-header {
    background: #2a402a;
    color: white;
    padding: 18px;
    font-weight: 700;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

#chat-logs {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #f9f9f9;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

/* Burbujas de mensaje */
.chat-msg {
    max-width: 80%;
    padding: 10px 15px;
    border-radius: 15px;
    font-size: 0.95rem;
    line-height: 1.4;
}

.chat-msg.user {
    align-self: flex-end;
    background: #2a402a;
    color: white;
    border-bottom-right-radius: 2px;
}

.chat-msg.bot {
    align-self: flex-start;
    background: #e9e9e9;
    color: var(--color-text);
    border-bottom-left-radius: 2px;
}

.chat-input-area {
    padding: 15px;
    background: white;
    display: flex;
    gap: 10px;
    border-top: 1px solid #eee;
}

/* Input de texto */
#chat-input {
    flex: 1;
    border: 1px solid #ddd;
    padding: 10px 15px;
    border-radius: 20px; /* Bordes redondeados estilo "píldora" */
    outline: none;
    font-family: var(--font-main);
    font-size: 0.9rem;
    transition: border-color 0.3s;
}

#chat-input:focus {
    border-color: var(--color-primary);
}

/* Botón Enviar con estilo de tu web */
.btn-send {
    background-color: #2a402a;
    color: white;
    border: none;
    padding: 10px 18px;
    border-radius: 20px; /* Redondeado a juego con el input */
    font-weight: 700;
    font-family: var(--font-main);
    cursor: pointer;
    transition: background-color 0.3s ease, transform 0.2s ease;
}

.btn-send:hover {
    background-color: #2a375a; /* El tono oscuro que usas en .btn-primary:hover */
    transform: translateY(-2px);
}

.btn-send:active {
    transform: translateY(0);
}
</style>
<script>
    const N8N_WEBHOOK_URL = 'http://localhost:5678/webhook/07438223-671d-4afc-af51-266e608f9027/chat';

function toggleChat() {
    const chatBox = document.getElementById('chat-box');
    chatBox.style.display = chatBox.style.display === 'none' ? 'flex' : 'none';
}

// Generamos un ID de sesión único al cargar la página (o puedes guardarlo en localStorage)
const sessionId = "session-" + Math.random().toString(36).substr(2, 9);

async function sendMessage() {
    const input = document.getElementById('chat-input');
    const logs = document.getElementById('chat-logs');
    const message = input.value;
    if (!message) return;

    // Mensaje del Usuario
    logs.innerHTML += `<div class="chat-msg user">${message}</div>`;
    input.value = '';
    logs.scrollTop = logs.scrollHeight;

    const response = await fetch(N8N_WEBHOOK_URL, {
        method: 'POST',
        body: JSON.stringify({ chatInput: message, sessionId: sessionId }),
        headers: { 'Content-Type': 'application/json' }
    });

    const data = await response.json();
    const botResponse = data.output || "Lo siento, hubo un error.";

    // Mensaje del Bot
    logs.innerHTML += `<div class="chat-msg bot">${botResponse}</div>`;
    logs.scrollTop = logs.scrollHeight;
}

</script>
</body>
</html>
</html>