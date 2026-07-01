
<!-- Footer general -->
<footer class="footer-sanrio mt-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <div class="d-flex align-items-center gap-2 mb-3">
                    <img src="<?php echo base('public/img/logo.png'); ?>" alt="Sanrio Shop"
                         style="width:46px;height:46px;border-radius:50%;object-fit:cover;">
                    <h5 class="mb-0 text-white">Sanrio Shop</h5>
                </div>
                <p class="small">Tu tienda de productos kawaii oficiales. Hello Kitty, My Melody,
                   Kuromi, Cinnamoroll y más, con envío a todo el país.</p>
            </div>
            <div class="col-md-2">
                <h6 class="text-white mb-3">Tienda</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><a href="<?php echo base('inicio'); ?>">Inicio</a></li>
                    <li><a href="<?php echo base('productos'); ?>">Productos</a></li>
                    <li><a href="<?php echo base('carrito'); ?>">Carrito</a></li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white mb-3">Contacto</h6>
                <ul class="list-unstyled small d-grid gap-2">
                    <li><i class="fas fa-phone me-2"></i>+123 456 789</li>
                    <li><i class="fas fa-envelope me-2"></i>info@sanrioshop.com</li>
                    <li><i class="fas fa-map-marker-alt me-2"></i>Veracruz, México</li>
                </ul>
            </div>
            <div class="col-md-3">
                <h6 class="text-white mb-3">Síguenos</h6>
                <div class="d-flex gap-3 fs-5">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-tiktok"></i></a>
                </div>
            </div>
        </div>
        <hr class="border-secondary my-4">
        <p class="text-center small mb-0">© <?php echo date('Y'); ?> Sanrio Shop · Proyecto ERP · Instituto Tecnológico de Veracruz</p>
    </div>
</footer>
</body>
</html>
