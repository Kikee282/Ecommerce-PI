// src/js/logicCarret.js

const CART_KEY = 'perLart_cart';

const Carrito = {
    // Obtener productos del LocalStorage
    getItems: () => {
        const items = localStorage.getItem(CART_KEY);
        return items ? JSON.parse(items) : [];
    },

    // Añadir producto
    add: (product) => {
        let items = Carrito.getItems();
        // Usamos == para comparar sin importar si es string o int
        const existing = items.find(item => item.id == product.id);

        if (existing) {
            existing.qty++;
        } else {
            product.qty = 1;
            items.push(product);
        }

        Carrito.save(items);
    },

    // Eliminar un producto específico
    remove: (id) => {
        let items = Carrito.getItems();
        // Filtramos para quitar el producto con ese ID
        items = items.filter(item => item.id != id);
        Carrito.save(items);
    },

    // Cambiar cantidad (+ o -)
    updateQty: (id, newQty) => {
        let items = Carrito.getItems();
        const item = items.find(i => i.id == id);

        if (item) {
            item.qty = parseInt(newQty);
            
            // Si la cantidad es 0 o menor, eliminamos el producto
            if (item.qty <= 0) {
                Carrito.remove(id);
                return; 
            }
        }
        
        Carrito.save(items);
    },

    // Vaciar todo el carrito
    clear: () => {
        localStorage.removeItem(CART_KEY);
        Carrito.updateIcon();
        // Disparamos evento para avisar a la página
        window.dispatchEvent(new Event('cartUpdated'));
    },

    // Función auxiliar para guardar y actualizar todo
    save: (items) => {
        localStorage.setItem(CART_KEY, JSON.stringify(items));
        Carrito.updateIcon();
        // Evento personalizado para que carret.php sepa que hubo cambios
        window.dispatchEvent(new Event('cartUpdated'));
    },

    // Actualizar el numerito rojo del header
    updateIcon: () => {
        const items = Carrito.getItems();
        const totalQty = items.reduce((acc, item) => acc + item.qty, 0);
        
        const badges = document.querySelectorAll('.cart-count');
        badges.forEach(badge => {
            badge.innerText = totalQty;
            badge.style.display = totalQty > 0 ? 'inline-block' : 'none';
        });
    },

    // Calcular precio total
    getTotal: () => {
        const items = Carrito.getItems();
        return items.reduce((total, item) => {
            // Limpiamos el precio de símbolos y comas
            let priceString = String(item.preu).replace('€', '').replace(',', '.').trim();
            let price = parseFloat(priceString) || 0;
            return total + (price * item.qty);
        }, 0);
    }
};

// Inicializar al cargar
document.addEventListener('DOMContentLoaded', Carrito.updateIcon);