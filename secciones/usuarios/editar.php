<?php
include("../../bd.php");

$usuario = $correo = $clave = "";
$txtID = "";

if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];
    $sentencia = $conexion->prepare("SELECT * FROM usuarios WHERE usuario_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();
    $registro = $sentencia->fetch(PDO::FETCH_LAZY);

    if ($registro) {
        $usuario = $registro["usuario"];
        $correo = $registro["correo"];
        $clave = $registro["clave"];
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $txtID = $_POST["txtID"];
    $usuario = $_POST["usuario"];
    $correo = $_POST["correo"];
    $clave = $_POST["clave"];

    if (isset($_FILES["foto"]["name"]) && $_FILES["foto"]["name"] != "") {
        $fecha_ = new DateTime();
        $nombreArchivo_foto = $fecha_->getTimestamp() . "_" . $_FILES["foto"]["name"];
        $tmp_foto = $_FILES["foto"]["tmp_name"];
        move_uploaded_file($tmp_foto, "./" . $nombreArchivo_foto);

        // Actualizar imagen
        $sentencia = $conexion->prepare("UPDATE usuarios SET foto = :foto WHERE usuario_id = :id");
        $sentencia->bindParam(":foto", $nombreArchivo_foto);
        $sentencia->bindParam(":id", $txtID);
        $sentencia->execute();
    }

    // Actualizar otros campos
    $sentencia = $conexion->prepare("UPDATE usuarios SET
        usuario = :usuario,
        correo = :correo,
        clave = :clave
        WHERE usuario_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->bindParam(":usuario", $usuario);
    $sentencia->bindParam(":correo", $correo);
    $sentencia->bindParam(":clave", $clave);
    $sentencia->execute();

    header("Location: index.php?mensaje=Producto actualizado");
    exit();
}
?>

<?php include("../../templates/header.php"); ?>
<h2>Editar Usuarios</h2>
<div class="card">
    <div class="card-header">Datos del Usuarios</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <input type="hidden" value="<?php echo $txtID; ?>" name="txtID">

            <div class="mb-3">
                <input type="text" class="form-control" name="usuario"
                    value="<?php echo $usuario; ?>"
                    placeholder="Editar nombre del Usuario" />
                <small class="form-text text-muted">Ingrese el nombre del usuario</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="correo"
                    value="<?php echo $correo; ?>"
                    placeholder="Editar Correo electronico" />
                <small class="form-text text-muted">Ingrese el correo electroncio del usuario</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="clave"
                    value="<?php echo $clave; ?>"
                    placeholder="Editar contraseña" />
                <small class="form-text text-muted">Ingrese la nueva contraseña</small>
            </div>

            <button type="submit" class="btn btn-outline-success">Actualizar registro</button>
            <a class="btn btn-outline-primary" href="index.php">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php");?>