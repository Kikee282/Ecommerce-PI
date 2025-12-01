// src/js/likes.js

document.addEventListener('DOMContentLoaded', () => {
    actualitzarEstatLike();
});

async function actualitzarEstatLike() {
    const btn = document.getElementById('btnLike');
    const countSpan = document.getElementById('likeCount');
    
    try {
        const response = await fetch(`./api_likes.php?productId=${currentProductId}`);
        const data = await response.json();
        
        // Actualizar número
        countSpan.innerText = data.count;
        
        // Actualizar color botón
        if (data.liked) {
            btn.classList.add('liked');
            btn.innerHTML = '<i class="fas fa-heart"></i>'; // Corazón lleno
        } else {
            btn.classList.remove('liked');
            btn.innerHTML = '<i class="far fa-heart"></i>'; // Corazón vacío
        }
    } catch (e) {
        console.error("Error likes:", e);
    }
}

async function toggleLike() {
    // Si no está logueado (currentUser.id es null), avisar
    if (!currentUser.id) {
        alert("Has d'iniciar sessió per donar like!");
        return;
    }

    const btn = document.getElementById('btnLike');
    // Efecto visual inmediato (Optimistic UI)
    btn.classList.toggle('liked'); 
    
    try {
        await fetch('./api_likes.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ productId: currentProductId })
        });
        // Recargar datos reales para asegurar
        actualitzarEstatLike();
    } catch (e) {
        alert("Error de connexió");
    }
}