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
    <title>El teu Carret - Per L'Art</title>
    
    <link href="./styles/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/styleIndex.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .cart-container { max-width: 900px; margin: 40px auto; padding: 20px; }
        .cart-item img { width: 80px; height: 80px; object-fit: cover; border-radius: 8px; }
        .cart-summary { background: #f8f9fa; padding: 20px; border-radius: 10px; margin-top: 30px; }
        .btn-qty { width: 30px; height: 30px; padding: 0; display: inline-flex; align-items: center; justify-content: center; }
    </style>
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
    <div class="cart-container">
        <h1 class="mb-4">El teu Carret</h1>
        
        <div class="table-responsive">
            <table class="table align-middle" id="cartTable">
                <thead>
                    <tr>
                        <th>Producte</th>
                        <th>Preu</th>
                        <th>Quantitat</th>
                        <th>Total</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody id="cartBody">
                    </tbody>
            </table>
        </div>

        <div id="emptyMessage" class="text-center py-5" style="display:none;">
            <h3>El carret està buit</h3>
            <a href="productos.php" class="btn btn-primary mt-3">Tornar a la tenda</a>
        </div>

        <div class="cart-summary text-end" id="cartSummary">
            <h4>Total: <span id="cartTotal">0,00</span> €</h4>
            <button class="btn btn-footer btn-lg mt-3" onclick="processCheckout()">Tramitar Comanda</button>
            <button class="btn btn-outline-danger mt-3 ms-2" onclick="clearCart()">Buidar Carret</button>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script src="./js/logicCarret.js"></script>
<script src="./js/bootstrap.bundle.min.js"></script>
<script src="./js/logicCarret.js?v=<?php echo time(); ?>"></script>

<script>
    // Referencias a elementos del DOM
    const cartBody = document.getElementById('cartBody');
    const emptyMsg = document.getElementById('emptyMessage');
    const summary = document.getElementById('cartSummary');
    const table = document.getElementById('cartTable');
    const totalElement = document.getElementById('cartTotal');

    // Función principal para PINTAR el carrito
    function renderCart() {
        const items = Carrito.getItems();
        
        // Limpiamos la tabla
        cartBody.innerHTML = '';

        // Si no hay productos, mostrar mensaje de vacío
        if (items.length === 0) {
            table.style.display = 'none';
            summary.style.display = 'none';
            emptyMsg.style.display = 'block';
            return;
        }

        // Si hay productos, mostrar tabla
        table.style.display = 'table';
        summary.style.display = 'block';
        emptyMsg.style.display = 'none';

        // Generar filas
        items.forEach(item => {
            // Calcular subtotal de la fila
            let priceNum = parseFloat(String(item.preu).replace('€', '').replace(',', '.').trim());
            let subtotal = (priceNum * item.qty).toFixed(2).replace('.', ',');

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>
                    <div class="d-flex align-items-center">
                        <img src="${item.img}" alt="${item.nom}" class="rounded" style="width: 60px; height: 60px; object-fit: cover; margin-right: 15px;">
                        <div>
                            <h6 class="mb-0"><a href="detall_producte.php?id=${item.id}" class="text-decoration-none text-dark">${item.nom}</a></h6>
                        </div>
                    </div>
                </td>
                <td class="align-middle">${item.preu} €</td>
                <td class="align-middle">
                    <div class="input-group input-group-sm" style="width: 100px;">
                        <button class="btn btn-outline-secondary" type="button" onclick="restarCantidad(${item.id}, ${item.qty})">-</button>
                        <input type="text" class="form-control text-center" value="${item.qty}" readonly>
                        <button class="btn btn-outline-secondary" type="button" onclick="sumarCantidad(${item.id}, ${item.qty})">+</button>
                    </div>
                </td>
                <td class="align-middle fw-bold">${subtotal} €</td>
                <td class="align-middle text-end">
                    <button class="btn btn-link text-danger p-0" onclick="eliminarProducto(${item.id})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            cartBody.appendChild(tr);
        });

        // Actualizar Total General
        const total = Carrito.getTotal().toFixed(2).replace('.', ',');
        totalElement.innerText = total;
    }

    // --- FUNCIONES DE LOS BOTONES ---

    function sumarCantidad(id, currentQty) {
        Carrito.updateQty(id, currentQty + 1);
        renderCart(); // Repintar
    }

    function restarCantidad(id, currentQty) {
        Carrito.updateQty(id, currentQty - 1);
        renderCart(); // Repintar
    }

    function eliminarProducto(id) {
        if(confirm('¿Segur que vols eliminar aquest producte?')) {
            Carrito.remove(id);
            renderCart();
        }
    }

    function clearCart() {
        if(confirm('¿Segur que vols buidar tot el carret?')) {
            Carrito.clear();
            renderCart();
        }
    }

    function processCheckout() {
        alert("Passar a caixa (Sprint 3)");
    }

    // Cargar al inicio
    document.addEventListener('DOMContentLoaded', renderCart);

    // Escuchar cambios desde otras pestañas o eventos
    window.addEventListener('cartUpdated', renderCart);
</script>
</body>
</html>