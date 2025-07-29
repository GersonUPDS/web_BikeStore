<?php
include("../../bd.php");

$producto_name = $model_year = $price = $foto = $quantity = "";
$txtID = "";

if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];
    $sentencia = $conexion->prepare("SELECT * FROM products WHERE producto_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if ($registro) {
        $producto_name = $registro["producto_name"];
        $model_year = $registro["model_year"];
        $price = $registro["price"];
        $foto = $registro["foto"];
        $quantity = $registro["quantity"];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $txtID = $_POST["txtID"];
    $producto_name = $_POST["producto_name"];
    $model_year = $_POST["model_year"];
    $price = $_POST["price"];
    $quantity = $_POST["quantity"];

    if (isset($_FILES["foto"]["name"]) && $_FILES["foto"]["name"] != "") {
        $fecha_ = new DateTime();
        $nombreArchivo_foto = $fecha_->getTimestamp() . "_" . $_FILES["foto"]["name"];
        $tmp_foto = $_FILES["foto"]["tmp_name"];
        move_uploaded_file($tmp_foto, "./" . $nombreArchivo_foto);

        // Actualizar imagen
        $sentencia = $conexion->prepare("UPDATE products SET foto = :foto WHERE producto_id = :id");
        $sentencia->bindParam(":foto", $nombreArchivo_foto);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
    }

    // Actualizar otros campos
    $sentencia = $conexion->prepare("UPDATE products SET
        producto_name = :producto_name,
        model_year = :model_year,
        price = :price,
        quantity = :quantity
        WHERE producto_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->bindParam(":producto_name", $producto_name);
    $sentencia->bindParam(":model_year", $model_year);
    $sentencia->bindParam(":price", $price);
    $sentencia->bindParam(":quantity", $quantity);
    $sentencia->execute();

    header("Location: index.php?mensaje=Producto actualizado");
    exit();
}
?>

<?php include("../../templates/header.php"); ?>
<h2>Editar producto</h2>
<div class="card">
    <div class="card-header">Datos del producto</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" value="<?php echo $txtID; ?>" name="txtID">

            <div class="mb-3">
                <input type="text" class="form-control" name="producto_name"
                    value="<?php echo $producto_name; ?>"
                    placeholder="Nombre del producto" />
                <small class="form-text text-muted">Ingrese el nombre del producto</small>
            </div>

            <div class="mb-3">
                <label for="foto">Foto actual:</label><br>
                <?php if ($foto): ?>
                    <img src="./<?php echo $foto; ?>" width="100" class="img-thumbnail" alt="foto">
                <?php endif; ?><br><br>
                <input type="file" class="form-control" name="foto" id="foto" />
                <small class="form-text text-muted">Cambiar foto del producto (opcional)</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="model_year"
                    value="<?php echo $model_year; ?>"
                    placeholder="Modelo" />
                <small class="form-text text-muted">Ingrese el modelo del producto</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="price"
                    value="<?php echo $price; ?>"
                    placeholder="Precio" />
                <small class="form-text text-muted">Ingrese el precio del producto</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="quantity"
                    value="<?php echo $quantity; ?>"
                    placeholder="Cantidad" />
                <small class="form-text text-muted">Ingrese la cantidad del producto</small>
            </div>

            <button type="submit" class="btn btn-outline-success">Actualizar registro</button>
            <a class="btn btn-outline-primary" href="index.php">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php");?>