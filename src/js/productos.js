// src/js/productos.js

document.addEventListener('DOMContentLoaded', () => {
    // Asegurarse de cargar el icono del carrito si logicCarret.js está incluido en el HTML
    if (typeof Carrito !== 'undefined') Carrito.updateIcon();

    const container = document.getElementById('lista-productos');

    async function cargarProductos() {
        try {
            const response = await fetch('./api_productes.php');
            if (!response.ok) throw new Error('Error API');
            const productos = await response.json();

            container.innerHTML = '';

            if (productos.length === 0) {
                container.innerHTML = '<p class="text-center w-100">No hi ha productes.</p>';
                return;
            }

            productos.forEach(prod => {
                const card = document.createElement('div');
                card.className = 'producte-minimal';
                
                // PREPARAMOS LOS DATOS PARA ENVIARLOS A LA FUNCIÓN (escapamos comillas simples en el nombre)
                const safeName = prod.nom.replace(/'/g, "\\'");
                const safeImg = prod.img || './contenido/image.png';

                card.innerHTML = `
                    <a href="detall_producte.php?id=${prod.id}">
                        <img src="${safeImg}" alt="${prod.nom}">
                    </a>
                    
                    <div class="prod-row-top">
                        <a href="detall_producte.php?id=${prod.id}" class="prod-name">
                            ${prod.nom}
                        </a>
                        <button class="btn-cart-icon" 
                            onclick="afegirAlCarretGlobal(${prod.id}, '${safeName}', '${prod.preu}', '${safeImg}')">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>

                    <div class="prod-price">${prod.preu}€</div>
                `;
                container.appendChild(card);
            });

        } catch (error) {
            console.error(error);
            container.innerHTML = '<p>Error carregant productes.</p>';
        }
    }
    cargarProductos();
});

// Función global (fuera del DOMContentLoaded) para que el HTML onclick la encuentre
function afegirAlCarretGlobal(id, nom, preu, img) {
    if (typeof Carrito === 'undefined') {
        alert("Error: logicCarret.js no cargado");
        return;
    }
    
    Carrito.add({
        id: id,
        nom: nom,
        preu: preu,
        img: img
    });

    alert("Producte afegit al carret!");
}