<?php 
include("../../bd.php");

$mensaje = "";

// Obtener clientes
$sentenciaClientes = $conexion->prepare("SELECT customer_id, first_name, last_name FROM customers");
$sentenciaClientes->execute();
$clientes = $sentenciaClientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener productos con precio
$sentenciaProductos = $conexion->prepare("SELECT producto_id, producto_name, price FROM products");
$sentenciaProductos->execute();
$products = $sentenciaProductos->fetchAll(PDO::FETCH_ASSOC);

//Obtener usuarios (prueba)
$sentenciaUsuarios = $conexion->prepare("SELECT usuario_id, usuario FROM usuarios");
$sentenciaUsuarios->execute();
$usuarios = $sentenciaUsuarios->fetchAll(PDO::FETCH_ASSOC);


if ($_POST) {
    $customer_id = $_POST["customer_id"] ?? "";
    $usuario_id = $_POST["usuario_id"] ?? "";
    $order_date = $_POST["order_date"] ?? "";
    $producto_id = $_POST["producto_id"] ?? "";
    $quantity = $_POST["quantity"] ?? 0;
    $discount = $_POST["discount"] ?? 0;

    // Validaciones básicas
    if (!strtotime($order_date)) {
        $mensaje = "La fecha no es válida.";
    } elseif (empty($customer_id)) {
        $mensaje = "Debe seleccionar un cliente.";
    } elseif (empty($producto_id)) {
        $mensaje = "Debe seleccionar un producto.";
    } elseif ($quantity <= 0) {
        $mensaje = "La cantidad debe ser mayor que cero.";
    } elseif ($discount < 0) {
        $mensaje = "El descuento no puede ser negativo.";
    }

    if ($mensaje === "") {
        try {
            $conexion->beginTransaction();

            // Insertar orden
            $sentencia = $conexion->prepare("INSERT INTO orders (customer_id, usuario_id, order_date) VALUES (:customer_id, :usuario_id, :order_date)");
            $sentencia->bindParam(":customer_id", $customer_id);
            $sentencia->bindParam(":usuario_id", $usuario_id);
            $sentencia->bindParam(":order_date", $order_date);
            $sentencia->execute();

            $order_id = $conexion->lastInsertId();

            // Obtener precio del producto seleccionado
            $sentenciaPrecio = $conexion->prepare("SELECT price FROM products WHERE producto_id = :producto_id");
            $sentenciaPrecio->bindParam(":producto_id", $producto_id);
            $sentenciaPrecio->execute();
            $producto = $sentenciaPrecio->fetch(PDO::FETCH_ASSOC);
            $price = $producto ? $producto['price'] : 0;

            // Insertar en orders_items con descuento
            $sentenciaItem = $conexion->prepare("INSERT INTO order_items (order_id, producto_id, quantity, price, discount) VALUES (:order_id, :producto_id, :quantity, :price, :discount)");
            $sentenciaItem->bindParam(":order_id", $order_id);
            $sentenciaItem->bindParam(":producto_id", $producto_id);
            $sentenciaItem->bindParam(":quantity", $quantity);
            $sentenciaItem->bindParam(":price", $price);
            $sentenciaItem->bindParam(":discount", $discount);
            $sentenciaItem->execute();

            $conexion->commit();

            header("Location:index.php?mensaje=" . urlencode("Orden agregada correctamente"));
            exit;
        } catch (Exception $e) {
            $conexion->rollBack();
            $mensaje = "Error al agregar la orden: " . $e->getMessage();
        }
    }
}
?>

<?php include("../../templates/header.php"); ?>

<?php if ($mensaje): ?>
<div class="alert alert-danger">
    <?php echo htmlspecialchars($mensaje); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Datos de la orden</div>
    <div class="card-body">
        <form action="" method="post" id="formOrden">
            
            <div class="mb-3">
                <label>Cliente</label>
                <select class="form-control" name="customer_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['customer_id']; ?>" <?php if(isset($_POST['customer_id']) && $_POST['customer_id'] == $cliente['customer_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($cliente['first_name'] . ' ' . $cliente['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="usuario_id">Usuario:</label>
                <select name="usuario_id" id="usuario_id" class="form-control" required>
                    <option value="">-- Seleccionar usuario --</option>
                    <?php foreach ($usuarios as $usuario) { ?>
                        <option value="<?= $usuario['usuario_id'] ?>">
                        <?= $usuario['usuario'] ?>
                        </option>
                    <?php } ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Fecha de la orden</label>
                <input type="date" class="form-control" name="order_date" value="<?php echo htmlspecialchars($_POST['order_date'] ?? ''); ?>" required />
            </div>

            <div class="mb-3">
                <label>Producto</label>
                <select class="form-control" name="producto_id" id="productoSelect" required>
                    <option value="" data-price="0">Seleccione un producto</option>
                    <?php foreach ($products as $producto): ?>
                        <option value="<?php echo $producto['producto_id']; ?>" data-price="<?php echo $producto['price']; ?>" <?php if(isset($_POST['producto_id']) && $_POST['producto_id'] == $producto['producto_id']) echo 'selected'; ?>>
                            <?php echo htmlspecialchars($producto['producto_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Precio $</label>
                <input type="text" class="form-control" id="precioProducto" value="<?php echo isset($_POST['producto_id']) ? number_format($productos[array_search($_POST['producto_id'], array_column($productos, 'producto_id'))]['price'], 2) : '0.00'; ?>" readonly />
            </div>

            <div class="mb-3">
                <label>Cantidad</label>
                <input type="number" class="form-control" name="quantity" min="1" value="<?php echo htmlspecialchars($_POST['quantity'] ?? 1); ?>" required />
            </div>

            <div class="mb-3">
                <label>Descuento $</label>
                <input type="number" class="form-control" name="discount" min="0" step="0.01" value="<?php echo htmlspecialchars($_POST['discount'] ?? 0); ?>" />
            </div>

            <a class="btn btn-outline-primary" href="index.php" role="button">Cancelar</a>
            <button type="submit" class="btn btn-outline-success">Agregar</button>
        </form>
    </div>
</div>

<script>
    const productoSelect = document.getElementById('productoSelect');
    const precioProducto = document.getElementById('precioProducto');

    productoSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || '0.00';
        precioProducto.value = parseFloat(price).toFixed(2);
    });
</script>

<?php include("../../templates/footer.php"); ?>