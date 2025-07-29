<?php
include("../../bd.php");

if (!isset($_GET['order_id'])) {
    die("ID de orden no proporcionado.");
}

$order_id = $_GET['order_id'];

// Obtener detalles de la orden
$sentenciaOrden = $conexion->prepare("SELECT * FROM orders WHERE order_id = :order_id");
$sentenciaOrden->bindParam(":order_id", $order_id);
$sentenciaOrden->execute();
$orden = $sentenciaOrden->fetch(PDO::FETCH_ASSOC);

if (!$orden) {
    die("Orden no encontrada.");
}

// Obtener detalles del cliente
$sentenciaCliente = $conexion->prepare("SELECT * FROM customers WHERE customer_id = :id");
$sentenciaCliente->bindParam(":id", $orden['customer_id']);
$sentenciaCliente->execute();
$cliente = $sentenciaCliente->fetch(PDO::FETCH_ASSOC);

// Obtener ítems del pedido
$sentenciaItems = $conexion->prepare("
    SELECT oi.*, p.producto_name 
    FROM order_items oi 
    JOIN products
    p ON oi.producto_id = p.producto_id 
    WHERE oi.order_id = :order_id
");
$sentenciaItems->bindParam(":order_id", $order_id);
$sentenciaItems->execute();
$items = $sentenciaItems->fetchAll(PDO::FETCH_ASSOC);

// Procesar datos
$cliente_nombre = $cliente['first_name'] . " " . $cliente['last_name'];
$foto = $cliente['imagen'];
$total = 0;

ob_start();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Detalle del Pedido</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 15px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        .header { margin-bottom: 20px; }
    </style>
</head>
<body>
    <h1>Detalle del Pedido</h1>
    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($orden['order_date'])); ?></p>
    <p><strong>Estado:</strong> <?php echo $orden['estado']; ?></p>
    
    <h3>Datos del Cliente</h3>
    <p><strong>ID:</strong> <?php echo $cliente['customer_id']; ?></p>
    <p><strong>Nombre:</strong> <?php echo $cliente_nombre; ?></p>
    <p><strong>Teléfono:</strong> <?php echo $cliente['phone']; ?></p>
    <p><strong>Email:</strong> <?php echo $cliente['email']; ?></p>
    <p><strong>Dirección:</strong> <?php echo $cliente['street'] . ', ' . $cliente['city'] . ', ' . $cliente['state']; ?></p>
    <p><strong>Imagen:</strong><br>
        <img width="80" src="<?php echo $foto; ?>" alt="Foto del cliente">
    </p>

    <h3>Ítems del Pedido</h3>
    <table>
        <thead>
            <tr>
                <th>#</th>
                <th>Producto</th>
                <th>Cantidad</th>
                <th>Precio Unitario</th>
                <th>Subtotal</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($items as $i => $item): 
                $subtotal = $item['quantity'] * $item['price'];
                $total += $subtotal;
            ?>
            <tr>
                <td><?php echo $i + 1; ?></td>
                <td><?php echo htmlspecialchars($item['producto_name']); ?></td>
                <td><?php echo $item['quantity']; ?></td>
                <td><?php echo number_format($item['price'], 2); ?> Bs</td>
                <td><?php echo number_format($subtotal, 2); ?> Bs</td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <h3>Total del Pedido: <?php echo number_format($total, 2); ?> Bs</h3>
</body>
</html>

<?php
$html = ob_get_clean();
require_once("../../libs/autoload.inc.php");
use Dompdf\Dompdf;

$dompdf = new Dompdf();
$options = $dompdf->getOptions();
$options->set(["isRemoteEnabled" => true]);
$dompdf->setOptions($options);

$dompdf->loadHtml($html);
$dompdf->setPaper("letter");
$dompdf->render();
$dompdf->stream("detalle_pedido.pdf", ["Attachment" => false]);
?>