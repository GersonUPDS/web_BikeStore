<?php include("../../bd.php");
//Envio de parametros en la URL o en el metodo GET
if(isset($_GET['txtID'])){
    $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
    //Buscar el archivo relacionado con el cliente
    $sentencia=$conexion->prepare("SELECT foto FROM products 
        WHERE producto_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $registro_recuperado=$sentencia->fetch(PDO::FETCH_LAZY);

    //Buscar el archivo imagen y eliminar
    if(isset($registro_recuperado["foto"]) && $registro_recuperado["foto"]!=""){
        if(file_exists("./".$registro_recuperado["foto"])){
            unlink("./".$registro_recuperado["foto"]);
        }
    }
    //Borra los datos del cliente
    $sentencia=$conexion->prepare("DELETE FROM products WHERE producto_id=:id");
    $sentencia->bindParam(":id",$txtID);
    $sentencia->execute();
    $mensaje="Registro eliminado";
}
//Consulta para clientes para mostrar como unico registro
$sentencia=$conexion->prepare("SELECT * FROM products");
$sentencia->execute();
$lista_producto=$sentencia->fetchAll(PDO::FETCH_ASSOC);
//print_r($lista_clientes);
?>
<?php include("../../templates/header.php");?>
<h2>Lista de productos</h2>
<div class="card">
    <div class="card-header">
        <a name="" id="" class="btn btn-outline-primary" href="crear.php" role="button">
            Nuevo</a></div>
    <div class="card-body">
        <div class="table-responsive-sm">
            <table class="table table-primary" id="tabla_id">
                <thead>
                    <tr>
                        <th scope="col">ID</th>
                        <th scope="col">Nombre de producto</th>                   
                        <th scope="col">Foto</th>
                        <th scope="col">Modelo</th>
                        <th scope="col">Precio $</th>
                        <th scope="col">Cantidad</th>
                        <th scope="col">Acciones</th>

                        
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($lista_producto as $registro) { ?>
                    <tr class="">
                        <td scope="row"><?php echo $registro['producto_id']; ?></td>
                        <td><?php echo $registro['producto_name']; ?></td>
                           
                        </td>
                        <td><img width="50"
                            src="<?php echo $registro['foto']; ?>"
                            class="img-fluid rounded" alt="Foto del producto"/>
                        </td>
                        <td><?php echo $registro['model_year']; ?></td>
                        <td><?php echo $registro['price']; ?></td>
                        <td><?php echo $registro['quantity']; ?></td>
                        
                        <td><a class="btn btn-outline-primary" href="editar.php?txtID=<?php echo $registro['producto_id']; ?>" role="button">Editar</a>
                            <a class="btn btn-outline-danger" href="index.php?txtID=<?php echo $registro['producto_id']; ?>" role="button">Eliminar</a>
                        </td>
                    </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>      
    </div>
</div>

<?php include("../../templates/footer.php");?>