<?php
/* =====================================================================
 *  vista/admin/reportes.php  -  Reportes y estadísticas (Ilustración 11 y 12)
 *  Gráficas: ventas diarias (líneas), ventas por categoría (pastel),
 *  productos más vendidos (barras) y pedidos por estado (barras horiz.)
 * ===================================================================== */
$tabActiva = 'reportes';
include 'vista/admin/header_admin.php';

$rep = new ReporteControlador();

$diarias    = $rep->ventasDiarias();
$porCat     = $rep->ventasPorCategoria();
$masVend    = $rep->masVendidos(5);
$porEstado  = $rep->pedidosPorEstado();

/* Preparamos los datos para Chart.js en formato JSON */
$labDiarias = array_column($diarias, '_id');
$valDiarias = array_map('floatval', array_column($diarias, 'total'));

$labCat = array_column($porCat, '_id');
$valCat = array_map('floatval', array_column($porCat, 'total'));

$labVend = array_column($masVend, 'nombre');
$valVend = array_map('intval', array_column($masVend, 'ventas'));

$labEst = array_column($porEstado, '_id');
$valEst = array_map('intval', array_column($porEstado, 'cantidad'));
?>

<div class="row g-4">
    <!-- Ventas diarias -->
    <div class="col-lg-6">
        <div class="border rounded-4 p-3 h-100">
            <h6 class="fw-bold mb-3">Ventas Diarias</h6>
            <canvas id="chartDiarias" height="180"></canvas>
        </div>
    </div>
    <!-- Ventas por categoría -->
    <div class="col-lg-6">
        <div class="border rounded-4 p-3 h-100">
            <h6 class="fw-bold mb-3">Ventas por Categoría</h6>
            <canvas id="chartCategoria" height="180"></canvas>
        </div>
    </div>
    <!-- Productos más vendidos -->
    <div class="col-lg-6">
        <div class="border rounded-4 p-3 h-100">
            <h6 class="fw-bold mb-3">Productos Más Vendidos</h6>
            <canvas id="chartVendidos" height="180"></canvas>
        </div>
    </div>
    <!-- Pedidos por estado -->
    <div class="col-lg-6">
        <div class="border rounded-4 p-3 h-100">
            <h6 class="fw-bold mb-3">Pedidos por Estado</h6>
            <canvas id="chartEstado" height="180"></canvas>
        </div>
    </div>
</div>

<script>
const rosa = '#ec4899', morado = '#a855f7', rosaClaro = '#f472b6', moradoClaro = '#c084fc';

// Ventas diarias (líneas)
new Chart(document.getElementById('chartDiarias'), {
    type: 'line',
    data: {
        labels: <?php echo json_encode($labDiarias); ?>,
        datasets: [{
            data: <?php echo json_encode($valDiarias); ?>,
            borderColor: rosa, backgroundColor: 'rgba(236,72,153,.1)',
            fill: true, tension: .35, pointBackgroundColor: rosa
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Ventas por categoría (pastel)
new Chart(document.getElementById('chartCategoria'), {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labCat); ?>,
        datasets: [{
            data: <?php echo json_encode($valCat); ?>,
            backgroundColor: [rosa, morado, rosaClaro, moradoClaro, '#f9a8d4']
        }]
    },
    options: { plugins: { legend: { position: 'right' } } }
});

// Productos más vendidos (barras)
new Chart(document.getElementById('chartVendidos'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labVend); ?>,
        datasets: [{ data: <?php echo json_encode($valVend); ?>, backgroundColor: morado, borderRadius: 6 }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

// Pedidos por estado (barras horizontales)
new Chart(document.getElementById('chartEstado'), {
    type: 'bar',
    data: {
        labels: <?php echo json_encode($labEst); ?>,
        datasets: [{ data: <?php echo json_encode($valEst); ?>, backgroundColor: rosa, borderRadius: 6 }]
    },
    options: { indexAxis: 'y', plugins: { legend: { display: false } }, scales: { x: { beginAtZero: true } } }
});
</script>

<?php include 'vista/admin/footer_admin.php'; ?>
