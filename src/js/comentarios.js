document.addEventListener('DOMContentLoaded', () => {
    carregarComentaris();
    const form = document.getElementById('formComentari');
    if (form) form.addEventListener('submit', enviarComentariDirecte);
});

async function carregarComentaris() {
    const container = document.getElementById('llista-comentaris');
    try {
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
            const rating = c.puntuacio || 0;
            const estrelles = '★'.repeat(rating) + '☆'.repeat(5 - rating);

            // --- LÒGICA DE PERMISOS ---
            let accions = '';
            const esMeu = currentUser.id && String(c.userId) === String(currentUser.id);
            const socAdmin = currentUser.role === 'admin';

            if (esMeu || socAdmin) {
                // Botó Esborrar (Amo o Admin)
                accions += `<button onclick="esborrar(${c.id})" style="color:red; border:none; background:none; cursor:pointer; margin-left:10px;"><i class="fas fa-trash"></i></button>`;
            }
            if (esMeu) {
                // Botó Editar (Només Amo)
                accions += `<button onclick="editar(${c.id}, '${c.text.replace(/'/g, "\\'")}')" style="color:blue; border:none; background:none; cursor:pointer; margin-left:10px;"><i class="fas fa-pen"></i></button>`;
            }

            const div = document.createElement('div');
            div.className = 'comment';
            div.innerHTML = `
                <div class="comment-header" style="display:flex; justify-content:space-between;">
                    <span>${c.nom_usuari} <span style="color:#f39c12;">${estrelles}</span></span>
                    <div>
                        <span class="comment-date">${dataFormatada}</span>
                        ${accions}
                    </div>
                </div>
                <div class="comment-body" id="body-${c.id}">${c.text}</div>
            `;
            container.appendChild(div);
        });

    } catch (error) { console.error(error); }
}

// --- FUNCIONS D'ACCIÓ ---

async function esborrar(id) {
    if (!confirm("Segur que vols esborrar aquest comentari?")) return;
    
    const response = await fetch(`./api_comentarios.php?id=${id}`, { method: 'DELETE' });
    
    if (response.ok) {
        carregarComentaris(); // Recargar lista
    } else {
        alert("Error: No tens permís o ha fallat la connexió.");
    }
}

async function editar(id, textActual) {
    // Decodificar comillas simples para que no rompa el prompt
    const textNet = textActual.replace(/\\'/g, "'");
    const nouText = prompt("Edita el teu comentari:", textNet);
    
    if (nouText === null || nouText === textNet) return; // Cancelado o igual

    const response = await fetch('./api_comentarios.php', {
        method: 'PUT',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, text: nouText })
    });

    if (response.ok) {
        carregarComentaris();
    } else {
        alert("Error en editar.");
    }
}


async function enviarComentariDirecte(e) {
    e.preventDefault();
    if (!currentUser.id) return alert("Error: No identificat");

    const text = document.getElementById('textComentari').value;
    const puntuacio = document.getElementById('puntuacio').value;

    const res = await fetch('./api_comentarios.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ productId: currentProductId, text, puntuacio })
    });

    if (res.ok) {
        document.getElementById('textComentari').value = '';
        carregarComentaris();
    } else {
        alert("Error enviant comentari");
    }
}