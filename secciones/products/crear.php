<?php
include("../../bd.php");

if ($_POST) {
    // Recopilar los datos del método POST
    $producto_name = (isset($_POST["producto_name"]) ? $_POST["producto_name"] : "");
    $model_year = (isset($_POST["model_year"]) ? $_POST["model_year"] : "");
    $price = (isset($_POST["price"]) ? $_POST["price"] : "");
    $quantity = (isset($_POST["quantity"]) ? $_POST["quantity"] : "");
    $foto = (isset($_FILES["foto"]['name']) ? $_FILES["foto"]['name'] : "");

    // Preparar la inserción de los datos
    $sentencia = $conexion->prepare("INSERT INTO products 
        (producto_name, foto, model_year, price, quantity)
        VALUES (:producto_name, :foto, :model_year, :price, :quantity)");

    // Asignar los valores
    $sentencia->bindParam(":producto_name", $producto_name);

    // Adjuntar la foto con un nombre distinto de archivo
    $fecha_ = new DateTime();
    $nombreArchivo_foto = ($foto != '') ? $fecha_->getTimestamp() . "_" . $_FILES["foto"]['name'] : "";
    $tmp_foto = $_FILES["foto"]['tmp_name'];
    if ($tmp_foto != '') {
        move_uploaded_file($tmp_foto, "./" . $nombreArchivo_foto);
    }
    $sentencia->bindParam(":foto", $nombreArchivo_foto);

    $sentencia->bindParam(":model_year", $model_year);
    $sentencia->bindParam(":price", $price);
    $sentencia->bindParam(":quantity", $quantity);

    // Ejecutar y redirigir
    $sentencia->execute();
    $mensaje = "Registro agregado";
    header("Location: index.php?mensaje=" . $mensaje);
}

include("../../templates/header.php");
?>
<br>
<div class="card">
    <div class="card-header">Datos del producto</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control" name="producto_name"
                    id="producto_name" aria-describedby="helpId"
                    placeholder="Nombre del producto"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el nombre del producto</small>
            </div>
            <div class="mb-3">
                <input type="file" class="form-control" name="foto"
                    id="foto" aria-describedby="helpId"
                    placeholder="Foto"
                />
                <small id="helpId" class="form-text text-muted">Ingrese la foto del producto</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="model_year"
                    id="model_year" aria-describedby="helpId"
                    placeholder="Modelo"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el modelo del producto</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="price"
                    id="price" aria-describedby="helpId"
                    placeholder="Precio"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el precio del producto</small>
            </div>
            <div class="mb-3">
                <input type="text" class="form-control" name="quantity"
                    id="quantity" aria-describedby="helpId"
                    placeholder="Cantidad"
                />
                <small id="helpId" class="form-text text-muted">Ingrese la cantidad del producto</small>
            </div>
            <button type="submit" class="btn btn-outline-success">Agregar registro</button>
            <a name="" id="" class="btn btn-outline-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php");?>