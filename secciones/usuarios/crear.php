<?php include("../../bd.php");
if($_POST){
    //Recolectamos los datos del metodo POST
    $usuario=(isset($_POST["usuario"])?$_POST["usuario"]:"");
    $correo=(isset($_POST["correo"])?$_POST["correo"]:"");
    $clave=(isset($_POST["clave"])?$_POST["clave"]:"");
    
    //Preparamos la insercion de los datos
    $sentencia=$conexion->prepare("INSERT INTO usuarios(usuario_id,usuario,correo,clave)
    
    VALUES(null,:usuario,:correo,:clave)");
    //Asignar los valores que tienen uso de :variable
    $sentencia->bindParam(":usuario",$usuario);
    $sentencia->bindParam(":correo",$correo);
    $sentencia->bindParam(":clave",$clave);
    $sentencia->execute();
    $mensaje="Registro agregado";
    //Redireccionar a index.php
    header("Location:index.php?mensaje=".$mensaje);
}
?>
<?php include("../../templates/header.php");?>
<br>
<div class="card">
    <div class="card-header">Datos del Usuario</div>
    <div class="card-body">
        <form action="" method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <input type="text" class="form-control" name="usuario"
                    id="usuario" aria-describedby="helpId"
                    placeholder="Nombre del usuario"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el nombre completo del usuario</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="correo"
                    id="correo" aria-describedby="helpId"
                    placeholder="Correo"
                />
                <small id="helpId" class="form-text text-muted">Ingrese el correo electronico del usuario</small>
            </div>

            <div class="mb-3">
                <input type="text" class="form-control" name="clave"
                    id="clave" aria-describedby="helpId"
                    placeholder="clave"
                />
                <small id="helpId" class="form-text text-muted">Ingrese la contraseña del usuario</small>
            </div>

            <button type="submit" class="btn btn-outline-success">Agregar registro</button>
            <a name="" id="" class="btn btn-outline-primary" href="index.php" role="button">Cancelar</a>
        </form>
    </div>
</div>
<?php include("../../templates/footer.php");?>