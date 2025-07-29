<?php 
include("../../bd.php");

// Activar modo de errores PDO para excepciones
$conexion->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$mensaje = "";
$order_id = "";
$customer_id = "";
$order_date = "";
$producto_id = "";
$quantity = 1;
$discount = 0;
$price = 0;

// Obtener lista de clientes
$sentenciaClientes = $conexion->prepare("SELECT customer_id, first_name, last_name FROM customers");
$sentenciaClientes->execute();
$clientes = $sentenciaClientes->fetchAll(PDO::FETCH_ASSOC);

// Obtener lista de productos con precio
$sentenciaProductos = $conexion->prepare("SELECT producto_id, producto_name, price FROM products");
$sentenciaProductos->execute();
$products = $sentenciaProductos->fetchAll(PDO::FETCH_ASSOC);

// Cargar datos de la orden y producto si viene txtID
if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];
    try {
        // Orden
        $sentencia = $conexion->prepare("SELECT * FROM orders WHERE order_id = :id");
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
        $registro = $sentencia->fetch(PDO::FETCH_ASSOC);

        if (!$registro) {
            throw new Exception("Orden no encontrada.");
        }

        $order_id = $registro["order_id"];
        $customer_id = $registro["customer_id"];
        $order_date = $registro["order_date"];

        // Producto y cantidad (asumiendo 1 producto por orden)
        $sentenciaItem = $conexion->prepare("SELECT producto_id, quantity, price, discount FROM order_items WHERE order_id = :order_id LIMIT 1");
        $sentenciaItem->bindParam(":order_id", $order_id);
        $sentenciaItem->execute();
        $item = $sentenciaItem->fetch(PDO::FETCH_ASSOC);

        if ($item) {
            $producto_id = $item['producto_id'];
            $quantity = $item['quantity'];
            $price = $item['price'];
            $discount = $item['discount'];
        }
    } catch (Exception $e) {
        $mensaje = $e->getMessage();
    }
}

// Procesar formulario POST para actualizar
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $txtID = $_POST["txtID"];
        $customer_id = $_POST["customer_id"];
        $order_date = $_POST["order_date"];
        $producto_id = $_POST["producto_id"];
        $quantity = (int)$_POST["quantity"];
        $discount = $_POST["discount"];

        if (!strtotime($order_date)) {
            throw new Exception("La fecha no es válida.");
        }

        if (empty($producto_id) || $quantity <= 0) {
            throw new Exception("Debe seleccionar un producto y una cantidad válida.");
        }

        if ($discount < 0) {
            throw new Exception("El descuento no puede ser negativo.");
        }

        $conexion->beginTransaction();

        // Actualizar orden
        $sentencia = $conexion->prepare("UPDATE orders SET customer_id = :customer_id, order_date = :order_date WHERE order_id = :id");
        $sentencia->bindParam(":customer_id", $customer_id);
        $sentencia->bindParam(":order_date", $order_date);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();

        // Obtener precio actual del producto
        $sentenciaPrecio = $conexion->prepare("SELECT price FROM products WHERE producto_id = :producto_id");
        $sentenciaPrecio->bindParam(":producto_id", $producto_id);
        $sentenciaPrecio->execute();
        $producto = $sentenciaPrecio->fetch(PDO::FETCH_ASSOC);
        $price = $producto ? $producto['price'] : 0;

        // Actualizar orders_items
        $sentenciaItem = $conexion->prepare("UPDATE order_items SET producto_id = :producto_id, quantity = :quantity, price = :price, discount = :discount WHERE order_id = :order_id");
        $sentenciaItem->bindParam(":producto_id", $producto_id);
        $sentenciaItem->bindParam(":quantity", $quantity);
        $sentenciaItem->bindParam(":price", $price);
        $sentenciaItem->bindParam(":discount", $discount);
        $sentenciaItem->bindParam(":order_id", $txtID);
        $sentenciaItem->execute();

        $conexion->commit();

        header("Location: index.php?mensaje=" . urlencode("Orden actualizada correctamente"));
        exit;
    } catch (Exception $e) {
        if ($conexion->inTransaction()) {
            $conexion->rollBack();
        }
        $mensaje = "Error al actualizar la orden: " . $e->getMessage();
    }
}
?>

<?php include("../../templates/header.php"); ?>

<h2>Editar Orden</h2>

<?php if (!empty($mensaje)): ?>
<div class="alert alert-danger">
    <?php echo htmlspecialchars($mensaje); ?>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header">Datos de la orden</div>
    <div class="card-body">
        <form action="" method="post" id="formOrdenEditar">
            <input type="hidden" name="txtID" value="<?php echo htmlspecialchars($order_id); ?>" />

            <div class="mb-3">
                <label for="customer_id" class="form-label">Cliente</label>
                <select class="form-control" name="customer_id" id="customer_id" required>
                    <option value="">Seleccione un cliente</option>
                    <?php foreach ($clientes as $cliente): ?>
                        <option value="<?php echo $cliente['customer_id']; ?>" <?php echo ($cliente['customer_id'] == $customer_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($cliente['first_name'] . ' ' . $cliente['last_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label for="order_date" class="form-label">Fecha de la orden</label>
                <input type="date" class="form-control" name="order_date" id="order_date" value="<?php echo htmlspecialchars($order_date); ?>" required />
            </div>

            <div class="mb-3">
                <label for="producto_id" class="form-label">Producto</label>
                <select class="form-control" name="producto_id" id="productoSelect" required>
                    <option value="" data-price="0">Seleccione un producto</option>
                    <?php foreach ($products as $producto): ?>
                        <option value="<?php echo $producto['producto_id']; ?>" data-price="<?php echo $producto['price']; ?>" <?php echo ($producto['producto_id'] == $producto_id) ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($producto['producto_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Precio $</label>
                <input type="text" class="form-control" id="precioProducto" value="<?php echo number_format($price, 2); ?>" readonly />
            </div>

            <div class="mb-3">
                <label for="quantity" class="form-label">Cantidad</label>
                <input type="number" class="form-control" name="quantity" id="quantity" value="<?php echo htmlspecialchars($quantity); ?>" min="1" required />
            </div>

            <div class="mb-3">
                <label for="discount" class="form-label">Descuento $</label>
                <input type="number" class="form-control" name="discount" id="discount" min="0" step="0.01" value="<?php echo htmlspecialchars($discount); ?>" />
            </div>

            <a class="btn btn-outline-primary" href="index.php" role="button">Cancelar</a>
            <button type="submit" class="btn btn-outline-success">Actualizar</button>
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
