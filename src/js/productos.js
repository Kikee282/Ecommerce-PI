// src/js/productos.js

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('lista-productos');

    // Función asíncrona para cargar datos
    async function cargarProductos() {
        try {
            // Petición al JSON Server (desde el navegador del cliente es localhost)
            const response = await fetch('./api_productes.php');
            
            if (!response.ok) {
                throw new Error('Error al conectar con la API');
            }

            const productos = await response.json();

            // Limpiamos el mensaje de "Cargando..."
            container.innerHTML = '';

            if (productos.length === 0) {
                container.innerHTML = '<p style="text-align:center; grid-column: 1/-1;">No hi ha productes disponibles.</p>';
                return;
            }

            // Generamos el HTML por cada producto
            productos.forEach(prod => {
                // Creamos el div de la tarjeta
                const card = document.createElement('div');
                card.className = 'producte-minimal';

                // Usamos las comillas invertidas (``) para el template string
                // Fíjate que replicamos EXACTAMENTE tu estructura HTML anterior
                card.innerHTML = `
                    <a href="detall_producte.php?id=${prod.id}">
                        <img src="${prod.img || './contenido/image.png'}" alt="${prod.nom}">
                    </a>
                    
                    <div class="prod-row-top">
                        <a href="detall_producte.php?id=${prod.id}" class="prod-name">
                            ${prod.nom}
                        </a>
                        <button class="btn-cart-icon" onclick="afegirAlCarret(${prod.id})">
                            <i class="fas fa-shopping-cart"></i>
                        </button>
                    </div>

                    <div class="prod-price">${prod.preu}€</div>
                `;

                // Añadimos la tarjeta al contenedor
                container.appendChild(card);
            });

        } catch (error) {
            console.error('Error:', error);
            container.innerHTML = '<p style="text-align:center; color:red; grid-column: 1/-1;">Error carregant el catàleg. Assegura\'t que el JSON Server està funcionant.</p>';
        }
    }

    // Ejecutamos la carga
    cargarProductos();
});

// Función global para el carrito (necesaria porque se llama desde el onclick del HTML generado)
function afegirAlCarret(id) {
    alert("Producte " + id + " afegit!");
}