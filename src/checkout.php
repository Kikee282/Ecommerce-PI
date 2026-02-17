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
    <title>Pasarel·la de Pagament - Per L'Art</title>
    
    <link href="./styles/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./styles/common.css">
    <link rel="stylesheet" href="./styles/styleIndex.css"> 
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        .checkout-container { max-width: 1100px; margin: 40px auto; padding: 20px; }
        .summary-card { background: #f8f9fa; border-radius: 10px; padding: 20px; position: sticky; top: 100px; }
        .payment-form { background: #fff; padding: 25px; border-radius: 10px; border: 1px solid #dee2e6; }
        .product-mini-img { width: 50px; height: 50px; object-fit: cover; border-radius: 5px; }
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

<main class="checkout-container mt-5 pt-5">
    <div class="row">
        <div class="col-lg-7">
            <h2 class="mb-4">Dades de l'enviament i Pagament</h2>
            <div class="payment-form">
                <form id="formPago">
                    <h5 class="mb-3">Adreça d'enviament</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-12">
                            <label class="form-label">Nom complet</label>
                            <input type="text" class="form-control" value="<?php echo $nombreUsuario; ?>" required>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Adreça</label>
                            <input type="text" class="form-control" placeholder="Carrer, número, pis..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Codi Postal</label>
                            <input type="text" class="form-control" required>
                        </div>
                    </div>

                    <h5 class="mb-3">Mètode de pagament</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label">Número de targeta</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-credit-card"></i></span>
                                <input type="text" class="form-control" placeholder="0000 0000 0000 0000" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Caducitat</label>
                            <input type="text" class="form-control" placeholder="MM/AA" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">CVV</label>
                            <input type="text" class="form-control" placeholder="123" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-footer btn-lg w-100 mt-4">Finalitzar Compra</button>
                </form>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="summary-card">
                <h4 class="mb-4">Resum de la comanda</h4>
                <div id="checkoutItems" class="mb-3">
                    </div>
                <hr>
                <div class="d-flex justify-content-between mb-2">
                    <span>Subtotal</span>
                    <span><span id="subtotalPrice">0,00</span> €</span>
                </div>
                <div class="d-flex justify-content-between mb-2">
                    <span>Enviament</span>
                    <span class="text-success">Gratis</span>
                </div>
                <div class="d-flex justify-content-between mt-3">
                    <h5><strong>Total</strong></h5>
                    <h5><strong id="totalPrice">0,00</strong> <strong>€</strong></h5>
                </div>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

<script src="./js/logicCarret.js"></script>
<script src="./js/bootstrap.bundle.min.js"></script>

<script>
    function renderCheckoutSummary() {
        const items = Carrito.getItems();
        const container = document.getElementById('checkoutItems');
        const totalElement = document.getElementById('totalPrice');
        const subtotalElement = document.getElementById('subtotalPrice');

        if (items.length === 0) {
            window.location.href = 'productos.php';
            return;
        }

        container.innerHTML = '';
        items.forEach(item => {
            const div = document.createElement('div');
            div.className = 'd-flex align-items-center mb-3';
            div.innerHTML = `
                <img src="${item.img}" class="product-mini-img me-3">
                <div class="flex-grow-1">
                    <div class="small fw-bold">${item.nom}</div>
                    <div class="small text-muted">Quantitat: ${item.qty}</div>
                </div>
                <div class="small fw-bold">${(parseFloat(String(item.preu).replace(',', '.')) * item.qty).toFixed(2).replace('.', ',')} €</div>
            `;
            container.appendChild(div);
        });

        const total = Carrito.getTotal().toFixed(2).replace('.', ',');
        totalElement.innerText = total;
        subtotalElement.innerText = total;
    }

    document.getElementById('formPago').addEventListener('submit', function(e) {
        e.preventDefault();
        alert('Compra realitzada correctament!!');
        Carrito.clear();
        window.location.href = 'index.php';
    });

    document.addEventListener('DOMContentLoaded', renderCheckoutSummary);
</script>
</body>
</html>