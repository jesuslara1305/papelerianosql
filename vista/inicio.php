<?php
/* =====================================================================
 *  vista/inicio.php  -  Página principal de la tienda
 *  (Ilustración 2: hero, Ilustración 4: categorías, Ilustración 5: destacados)
 * ===================================================================== */
include 'vista/header_gral.php';

$prodCtrl   = new ProductoControlador();
$destacados = $prodCtrl->destacados(3);

/* Categorías mostradas en la portada */
$categorias = [
    ['nombre' => 'Peluches',   'desc' => 'Adorables peluches de todos los personajes', 'icono' => 'fa-heart',        'img' => 'https://images.unsplash.com/photo-1607083206869-4c7672e72a8a?w=600&q=80'],
    ['nombre' => 'Accesorios', 'desc' => 'Bolsos, carteras y más',                       'icono' => 'fa-gift',         'img' => 'https://images.unsplash.com/photo-1584917865442-de89df76afd3?w=600&q=80'],
    ['nombre' => 'Papelería',  'desc' => 'Cuadernos, stickers y más',                     'icono' => 'fa-pen',          'img' => 'https://images.unsplash.com/photo-1531346878377-a5be20888e57?w=600&q=80'],
    ['nombre' => 'Decoración', 'desc' => 'Para tu cuarto kawaii',                         'icono' => 'fa-house',        'img' => 'https://images.unsplash.com/photo-1513519245088-0e12902e35ca?w=600&q=80'],
];
?>

<main>
<!-- ======================= HERO ======================= -->
<section class="container mt-4">
    <div class="hero">
        <div class="row align-items-center g-4">
            <div class="col-md-6">
                <h1>Descubre el Mundo<br>Mágico de Sanrio</h1>
                <p class="my-3">Encuentra todos tus personajes favoritos: Hello Kitty, My Melody,
                    Kuromi, Cinnamoroll y más. Productos kawaii oficiales con envío a todo el país.</p>
                <div class="d-flex gap-3">
                    <a href="<?php echo base('productos'); ?>" class="btn btn-light fw-semibold px-4">Ver Productos</a>
                    <a href="<?php echo base('inicio'); ?>#contacto" class="btn btn-outline-light fw-semibold px-4">Contactar</a>
                </div>
            </div>
            <div class="col-md-6">
                <img class="hero-img" src="https://images.unsplash.com/photo-1566576912321-d58ddd7a6088?w=900&q=80" alt="Productos Sanrio">
            </div>
        </div>
    </div>
</section>

<!-- ======================= CATEGORÍAS ======================= -->
<section class="container py-5">
    <h2 class="seccion-titulo">Nuestras Categorías</h2>
    <p class="seccion-sub">Explora nuestra amplia variedad de productos</p>
    <div class="row g-4">
        <?php foreach ($categorias as $cat): ?>
            <div class="col-6 col-lg-3">
                <a href="<?php echo base('productos?cat=' . urlencode($cat['nombre'])); ?>" class="text-decoration-none text-dark">
                    <div class="cat-card">
                        <div class="position-relative">
                            <img class="cat-img" src="<?php echo e($cat['img']); ?>" alt="<?php echo e($cat['nombre']); ?>">
                            <span class="cat-icon"><i class="fas <?php echo $cat['icono']; ?>"></i></span>
                        </div>
                        <div class="p-3">
                            <h6 class="fw-bold mb-1"><?php echo e($cat['nombre']); ?></h6>
                            <p class="small text-muted mb-0"><?php echo e($cat['desc']); ?></p>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
    </div>
</section>

<!-- ======================= PRODUCTOS DESTACADOS ======================= -->
<section class="bg-white py-5" id="productos">
    <div class="container">
        <h2 class="seccion-titulo">Productos Destacados</h2>
        <p class="seccion-sub">Los más vendidos de nuestra tienda</p>
        <div class="row g-4">
            <?php if (empty($destacados)): ?>
                <div class="col-12 text-center text-muted py-4">
                    <i class="fas fa-box-open fa-2x mb-2"></i>
                    <p>Aún no hay productos disponibles por el momento. Vuelve pronto.</p>
                </div>
            <?php else: ?>
                <?php foreach ($destacados as $p): ?>
                    <div class="col-md-4">
                        <div class="prod-card">
                            <div class="position-relative">
                                <img class="prod-img" src="<?php echo e(imagenSrc($p['imagen'])); ?>" alt="<?php echo e($p['nombre']); ?>">
                                <span class="prod-badge"><?php echo e($p['categoria']); ?></span>
                            </div>
                            <div class="p-3 d-flex flex-column flex-grow-1">
                                <h6 class="fw-bold mb-1"><?php echo e($p['nombre']); ?></h6>
                                <div class="stars mb-2">
                                    <?php for ($i = 0; $i < 5; $i++): ?>
                                        <i class="fa<?php echo $i < round($p['rating']) ? 's' : 'r'; ?> fa-star"></i>
                                    <?php endfor; ?>
                                    <span class="text-muted ms-1">(<?php echo number_format($p['rating'], 1); ?>)</span>
                                </div>
                                <span class="prod-precio mb-3"><?php echo precio($p['precio']); ?></span>
                                <?php if (AuthControlador::esAdmin()): ?>
                                    <a href="<?php echo base('admin/reabastecer'); ?>" class="btn btn-outline-sanrio w-100 mt-auto">
                                        <i class="fas fa-truck-loading me-1"></i>Gestionar Inventario
                                    </a>
                                <?php else: ?>
                                    <form method="post" action="<?php echo base(''); ?>" class="mt-auto">
                                        <input type="hidden" name="accion" value="agregar_carrito">
                                        <input type="hidden" name="no_producto" value="<?php echo $p['no_producto']; ?>">
                                        <input type="hidden" name="volver" value="inicio">
                                        <button class="btn btn-sanrio w-100">
                                            <i class="fas fa-shopping-cart me-1"></i>Agregar al Carrito
                                        </button>
                                    </form>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="<?php echo base('productos'); ?>" class="btn btn-outline-sanrio px-4">Ver todos los productos</a>
        </div>
    </div>
</section>

<!-- ======================= SOBRE NOSOTROS ======================= -->
<section class="container py-5" id="sobre">
    <div class="row align-items-center g-4">
        <div class="col-md-6">
            <img src="https://images.unsplash.com/photo-1513201099705-a9746e1e201f?w=800&q=80"
                 class="img-fluid rounded-4 shadow-sm" alt="Sobre nosotros">
        </div>
        <div class="col-md-6">
            <h2 class="fw-bold mb-3">Sobre <span class="grad-text">Nosotros</span></h2>
            <p class="text-muted">Sanrio Shop nació como un pequeño emprendimiento y hoy es tu papelería
               kawaii de confianza. Digitalizamos nuestra operación con un sistema ERP para ofrecerte
               inventario en tiempo real, atención profesional y envíos seguros.</p>
            <div class="row g-3 mt-2">
                <div class="col-4 text-center">
                    <h3 class="grad-text fw-bold mb-0">+50</h3><small class="text-muted">Clientes</small>
                </div>
                <div class="col-4 text-center">
                    <h3 class="grad-text fw-bold mb-0">24/7</h3><small class="text-muted">Disponible</small>
                </div>
                <div class="col-4 text-center">
                    <h3 class="grad-text fw-bold mb-0">100%</h3><small class="text-muted">Oficial</small>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ======================= CONTACTO ======================= -->
<section class="py-5" id="contacto" style="background:var(--grad-suave);">
    <div class="container text-center">
        <h2 class="fw-bold mb-2">¿Tienes alguna <span class="grad-text">duda</span>?</h2>
        <p class="text-muted mb-4">Escríbenos y con gusto te atendemos</p>
        <div class="d-flex justify-content-center gap-4 flex-wrap">
            <span><i class="fas fa-phone text-rosa me-2"></i>+123 456 789</span>
            <span><i class="fas fa-envelope text-rosa me-2"></i>info@sanrioshop.com</span>
            <span><i class="fab fa-whatsapp text-rosa me-2"></i>Pedidos por WhatsApp</span>
        </div>
    </div>
</section>
</main>

<?php include 'vista/footer_gral.php'; ?>
