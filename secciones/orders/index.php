<?php
include("../../bd.php");

// Manejo de anulación de orden
$mensaje = "";
if (isset($_GET['order_id'], $_GET['accion']) && $_GET['accion'] === 'anular') {
    $order_id = $_GET['order_id'];

    $sentenciaEstado = $conexion->prepare("SELECT estado FROM orders WHERE order_id = :order_id");
    $sentenciaEstado->bindParam(":order_id", $order_id);
    $sentenciaEstado->execute();
    $order = $sentenciaEstado->fetch(PDO::FETCH_ASSOC);

    if ($order) {
        if ($order['estado'] !== 'Anulada') {
            $sentencia = $conexion->prepare("UPDATE orders SET estado = 'Anulada' WHERE order_id = :order_id");
            $sentencia->bindParam(":order_id", $order_id);
            $sentencia->execute();
            $mensaje = "Orden anulada correctamente.";
        } else {
            $mensaje = "La orden ya está anulada.";
        }
    } else {
        $mensaje = "Orden no encontrada.";
    }

    header("Location: index.php?mensaje=" . urlencode($mensaje));
    exit;
}

// Consulta para obtener órdenes con ID del cliente
$sentencia = $conexion->prepare("
    SELECT order_id, customer_id, order_date, estado
    FROM orders
    ORDER BY order_id ASC
");
$sentencia->execute();
$lista_ordenes = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../../templates/header.php"); ?>

<h2>Lista de Órdenes</h2>

<?php if (isset($_GET['mensaje'])): ?>
<div class="alert alert-info">
    <?php echo htmlspecialchars(urldecode($_GET['mensaje'])); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <a class="btn btn-outline-primary" href="crear.php" role="button">Nueva Orden</a>
    </div>
    <div class="card-body">
       <div class="table-responsive">
        <table class="table table-primary" id="tabla_id">
            <thead>
                <tr>
                    <th>Id De Orden</th>
                    <th>Id De Cliente</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($lista_ordenes as $orden) : ?>
                <tr>
                    <td><?php echo htmlspecialchars($orden['order_id']); ?></td>
                    <td><?php echo htmlspecialchars($orden['customer_id']); ?></td>
                    <td><?php echo htmlspecialchars($orden['order_date']); ?></td>
                    <td>
                        <?php 
                            if ($orden['estado'] === 'Anulada') {
                                echo '<span class="badge bg-danger">Anulada</span>';
                            } else {
                                echo '<span class="badge bg-warning text-dark">Pendiente</span>';
                            }
                        ?>
                    </td>
                    <td>
                        <?php if ($orden['estado'] !== 'Anulada'): ?>
                            <a href="index.php?accion=anular&order_id=<?php echo $orden['order_id']; ?>" 
                               class="btn btn-outline-danger"
                               onclick="return confirm('¿Seguro que deseas anular esta orden?');">
                               Anular
                            </a>
                        <?php else: ?>
                            <button class="btn btn-outline-secondary" disabled>Anulada</button>
                        <?php endif; ?>

                        <a href="editar.php?txtID=<?php echo $orden['order_id']; ?>" 
                           class="btn btn-outline-primary <?php echo ($orden['estado'] === 'Anulada') ? 'disabled' : ''; ?>"
                           <?php echo ($orden['estado'] === 'Anulada') ? 'tabindex="-1" aria-disabled="true"' : ''; ?>>
                           Editar
                        </a>

                        <a href="../order_items/index.php?order_id=<?php echo $orden['order_id']; ?>" class="btn btn-outline-info">Detalle pedido</a> 

                        <a href="imprimir_order.php?order_id=<?php echo $orden['order_id']; ?>" 
                           class="btn btn-outline-success <?php echo ($orden['estado'] === 'Anulada') ? 'disabled' : ''; ?>"
                           target="_blank"
                           <?php echo ($orden['estado'] === 'Anulada') ? 'tabindex="-1" aria-disabled="true" onclick="return false;"' : ''; ?>>
                           Imprimir Pedido
                        </a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
       </div>
    </div>
</div>

<?php include("../../templates/footer.php"); ?>

