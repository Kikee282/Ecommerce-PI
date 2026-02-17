document.addEventListener('DOMContentLoaded', () => {
    cargarEstadoInicial();
});

async function cargarEstadoInicial() {
    if (typeof currentProductId === 'undefined') return;

    try {
        const uId = (typeof currentUser !== 'undefined' && currentUser.id) ? currentUser.id : '';
        const response = await fetch(`./api_likes.php?product_id=${currentProductId}&user_id=${uId}`);
        const data = await response.json();
        updateLikeUI(data.liked, data.count);
    } catch (error) {
        console.error("Error likes:", error);
    }
}

async function toggleLike() {
    if (typeof currentUser === 'undefined' || !currentUser.id) {
        alert("Has d'iniciar sessió per a donar like.");
        window.location.href = './auth/login.html';
        return;
    }

    try {
        const response = await fetch('./api_likes.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                product_id: currentProductId,
                user_id: currentUser.id
            })
        });
        const data = await response.json();
        if (data.success) {
            updateLikeUI(data.liked, data.count);
        }
    } catch (error) {
        console.error(error);
    }
}

function updateLikeUI(isLiked, count) {
    const btn = document.getElementById('btnLike');
    const icon = btn.querySelector('i');
    const counter = document.getElementById('likeCount');

    if(counter) counter.innerText = count;

    if (isLiked) {
        icon.className = 'fas fa-heart text-danger'; // Corazón lleno y rojo
    } else {
        icon.className = 'far fa-heart'; // Corazón vacío
    }
}