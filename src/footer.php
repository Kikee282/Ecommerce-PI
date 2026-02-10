<footer class="main-footer">
    <div class="container footer-grid">
        <div class="footer-logo">
            <a href="index.php"><img src="./contenido/log_blanc.png" alt="Logo"></a>
        </div>

        <div class="footer-column">
            <h4>Informació</h4>
            <a href="#">Informació legal</a>
            <a href="#">Política de devolucions</a>
            <a href="#">Política de cookies</a>
            
            <button id="btn-accesibilidad" onclick="toggleAccessibility()">
                <i class="fas fa-universal-access"></i> Mode Llegible
            </button>
        </div>

        <div class="footer-column">
            <h4>Contacte</h4>
            <p>Telèfon: 122 884 2887</p>
            <a href="contacte.php">Contacta amb nosaltres</a>
        </div>

        <div class="footer-column">
            <h4>Segueix-nos</h4>
            <div class="social-icons">
                <a href="#" aria-label="TikTok"><i class="fab fa-tiktok"></i></a>
                <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
    if (localStorage.getItem('accessibilityMode') === 'active') {
        document.body.classList.add('accessibility-mode');
    }

    function toggleAccessibility() {
        const body = document.body;
        body.classList.toggle('accessibility-mode');
        
        if (body.classList.contains('accessibility-mode')) {
            localStorage.setItem('accessibilityMode', 'active');
        } else {
            localStorage.removeItem('accessibilityMode');
        }
    }

    function setFontSize(size) {
        const html = document.documentElement;
        let percentage = '100%';
        switch (size) {
            case 'small': percentage = '85%'; break;
            case 'normal': percentage = '100%'; break;
            case 'large': percentage = '120%'; break;
            case 'xlarge': percentage = '140%'; break;
        }
        html.style.fontSize = percentage;
        localStorage.setItem('userFontSize', size);
    }
    
    const savedSize = localStorage.getItem('userFontSize');
    if (savedSize) setFontSize(savedSize);
</script>