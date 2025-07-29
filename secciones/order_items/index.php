<?php
include("../../bd.php");

$order_id = $_GET['order_id'] ?? null;
if (!$order_id) {
    echo "No se especificó la orden.";
    exit;
}

// Obtener datos de la orden junto con datos del cliente
$sentenciaCliente = $conexion->prepare("
    SELECT o.order_id, o.order_date, o.estado,
           c.customer_id, c.first_name, c.last_name, c.phone, c.email, c.street, c.city, c.state
    FROM orders o
    INNER JOIN customers c ON o.customer_id = c.customer_id
    WHERE o.order_id = :order_id
");
$sentenciaCliente->bindParam(":order_id", $order_id);
$sentenciaCliente->execute();
$ordenCliente = $sentenciaCliente->fetch(PDO::FETCH_ASSOC);

if (!$ordenCliente) {
    echo "Orden no encontrada.";
    exit;
}

// Obtener productos de la orden
$sentenciaItems = $conexion->prepare("
    SELECT oi.*, producto_name
    FROM order_items oi
    INNER JOIN products p ON oi.producto_id = p.producto_id
    WHERE oi.order_id = :order_id
");
$sentenciaItems->bindParam(":order_id", $order_id);
$sentenciaItems->execute();
$items = $sentenciaItems->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../../templates/header.php"); ?>

<h2>Detalle de la Orden #<?php echo htmlspecialchars($ordenCliente['order_id']); ?></h2>

<h4>Datos del Cliente</h4>
<ul>
    <li><strong>ID Cliente:</strong> <?php echo htmlspecialchars($ordenCliente['customer_id']); ?></li>
    <li><strong>Nombre:</strong> <?php echo htmlspecialchars($ordenCliente['first_name'] . ' ' . $ordenCliente['last_name']); ?></li>
    <li><strong>Teléfono:</strong> <?php echo htmlspecialchars($ordenCliente['phone']); ?></li>
    <li><strong>Email:</strong> <?php echo htmlspecialchars($ordenCliente['email']); ?></li>
    <li><strong>Dirección:</strong> <?php echo htmlspecialchars($ordenCliente['street'] . ', ' . $ordenCliente['city'] . ', ' . $ordenCliente['state']); ?></li>
</ul>

<h4>Productos de la Orden</h4>
<table class="table table-striped">
    <thead>
        <tr>
            <th>Producto</th>
            <th>Cantidad</th>
            <th>Precio $</th>
            <th>Descuento $</th>
            <th>Subtotal $</th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $total = 0;
        foreach ($items as $item):
            $subtotal = ($item['price'] - $item['discount']) * $item['quantity'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?php echo htmlspecialchars($item['producto_name']); ?></td>
            <td><?php echo htmlspecialchars($item['quantity']); ?></td>
            <td><?php echo number_format($item['price'], 2); ?></td>
            <td><?php echo number_format($item['discount'], 2); ?></td>
            <td><?php echo number_format($subtotal, 2); ?></td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="4" class="text-end"><strong>Total:</strong></td>
            <td><strong><?php echo number_format($total, 2); ?></strong></td>
        </tr>
    </tbody>
</table>

<a href="../orders" class="btn btn-outline-secondary">Volver a Órdenes</a>

<?php include("../../templates/footer.php"); ?>