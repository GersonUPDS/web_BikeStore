<?php 
include("../../bd.php");

// Eliminar usuario si se pasa el ID por GET
if (isset($_GET['txtID'])) {
    $txtID = $_GET['txtID'];

    // Borrar el registro del usuario
    $sentencia = $conexion->prepare("DELETE FROM usuarios WHERE usuario_id = :id");
    $sentencia->bindParam(":id", $txtID);
    $sentencia->execute();

    $mensaje = "Registro eliminado";
}

// Consultar lista de usuarios
$sentencia = $conexion->prepare("SELECT * FROM usuarios");
$sentencia->execute();
$lista_usuarios = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<?php include("../../templates/header.php"); ?>
<h2>Lista de Usuarios</h2>

<div class="card">
    <div class="card-header">
        <a class="btn btn-outline-primary" href="crear.php" role="button">Nuevo</a>
    </div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-primary" id="tabla_id">
                <thead>
                    <tr>
                        <th scope="col">ID Usuario</th>
                        <th scope="col">Nombre del usuario</th>
                        <th scope="col">Correo</th>
                        <th scope="col">Contraseña</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_usuarios as $registro) { ?>
                    <tr>
                        <td><?php echo $registro['usuario_id']; ?></td>
                        <td><?php echo $registro['usuario']; ?></td>
                        <td><?php echo $registro['correo']; ?></td>
                        <td>***********</td>
                        <td>
                            <a class="btn btn-outline-primary" href="editar.php?txtID=<?php echo $registro['usuario_id']; ?>">Editar</a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['usuario_id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>      
    </div>
</div>

<?php include("../../templates/footer.php"); ?>
