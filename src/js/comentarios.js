// src/js/comentarios.js

document.addEventListener('DOMContentLoaded', () => {
    // 1. Carregar comentaris existents
    carregarComentaris();

    // 2. Configurar el formulari (si existeix)
    const form = document.getElementById('formComentari');
    if (form) {
        form.addEventListener('submit', enviarComentariDirecte);
    }
});

// --- GET: Carregar comentaris ---
async function carregarComentaris() {
    const container = document.getElementById('llista-comentaris');
    
    try {
        // Correcte: Utilitzem el pont PHP per a llegir
        const response = await fetch(`./api_comentarios.php?productId=${currentProductId}`);
        
        if (!response.ok) throw new Error('Error de connexió');
        
        const comentaris = await response.json();

        container.innerHTML = '';

        if (comentaris.length === 0) {
            container.innerHTML = '<p style="color:#777; font-style:italic;">Sigues el primer a comentar!</p>';
            return;
        }

        comentaris.forEach(c => {
            const dateObj = new Date(c.data);
            const dataFormatada = dateObj.toLocaleDateString('ca-ES');
            
            // Gestió de la puntuació
            const rating = c.puntuacio || 0;
            const estrelles = '★'.repeat(rating) + '☆'.repeat(5 - rating);

            const div = document.createElement('div');
            div.className = 'comment';
            div.innerHTML = `
                <div class="comment-header">
                    <span>${c.nom_usuari} <span style="color:#f39c12; margin-left:5px;">${estrelles}</span></span>
                    <span class="comment-date">${dataFormatada}</span>
                </div>
                <div class="comment-body">
                    ${c.text}
                </div>
            `;
            container.appendChild(div);
        });

    } catch (error) {
        console.error('Error:', error);
        container.innerHTML = '<p style="color:red;">Error carregant comentaris.</p>';
    }
}

// --- POST: Enviar nou comentari ---
async function enviarComentariDirecte(e) {
    e.preventDefault();

    // Validació de seguretat bàsica (tot i que el PHP també ho comprova)
    if (!currentUser.id) {
        alert("Error: No estàs identificat.");
        return;
    }

    const text = document.getElementById('textComentari').value;
    const puntuacio = parseInt(document.getElementById('puntuacio').value);
    const btn = document.querySelector('.btn-submit-comment');

    btn.disabled = true;
    btn.innerText = "Enviant...";

    // Dades a enviar
    // Nota: El PHP s'encarregarà de verificar l'usuari i la data reals per seguretat.
    // Només cal enviar el text, la puntuació i l'ID del producte.
    const dadesEnviament = {
        productId: currentProductId,
        text: text,
        puntuacio: puntuacio
    };

    try {
        // CORRECCIÓ CLAU: Enviem al pont PHP, no directament al port 3000
        const response = await fetch('./api_comentarios.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(dadesEnviament)
        });

        if (!response.ok) {
            // Intentem llegir el missatge d'error del PHP
            const errorData = await response.json().catch(() => ({}));
            throw new Error(errorData.error || 'Error guardant el comentari');
        }

        // Èxit!
        document.getElementById('textComentari').value = ''; // Netejar camp
        alert('Comentari publicat!');
        carregarComentaris(); // Recarregar la llista a l'instant

    } catch (error) {
        console.error(error);
        alert('Error: ' + error.message);
    } finally {
        btn.disabled = false;
        btn.innerText = "Publicar Comentari";
    }
}