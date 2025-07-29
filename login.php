<?php 
//iniciar la sesion
session_start();
if ($_POST){
        include("./bd.php");
        $sentencia=$conexion->prepare("SELECT *, count(*) as usuario_id FROM usuarios WHERE usuario=:usuario AND clave=:clave");
        $usuario=$_POST["usuario"];
        $clave=$_POST["clave"];
        $sentencia->bindParam(":usuario",$usuario);
        $sentencia->bindParam(":clave",$clave);
        $sentencia->execute();
        $registro=$sentencia->fetch(PDO::FETCH_LAZY);

        if($registro["usuario_id"]>0){
            $_SESSION['usuario']=$registro["usuario"];
            $_SESSION['logueado']=true;
            header("Location: ./index.php");
        }else{
            $mensaje="Error: Usuario o clave incorrectos";
        }
    }
?>

<!doctype html>
<html lang="es">
    <head>
        <title>Identificacion</title>
        <!-- Required meta tags -->
        <meta charset="utf-8" />
        <meta
            name="viewport"
            content="width=device-width, initial-scale=1, shrink-to-fit=no"
        />
        <!-- Bootstrap CSS v5.2.1 -->
        <link
            href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css"
            rel="stylesheet"
            integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN"
            crossorigin="anonymous"
        />
    </head>

    <body>
        <header>
            <!-- place navbar here -->
        </header>
        <main class="container">

        <div class="row">
            <div class="col-md-4" ></div>
            <div class="col-md-4">
                <br><br><br><br>
                <div class="card">
                    <div class="card-header">Iniciar Seción</div>
                    <div class="card-body">
                        <!--Agregar alerta para mostrar el error  -->
                        <?php 
                            if(isset($mensaje)){?>
                            <div class="alert alert-danger" role="alert">
                                <strong><?php echo $mensaje; ?></strong>

                           <?php }?>
                       <form action="" method="post">
                            <div class="mb-3">
                                <label for="usuario" id="" class="form-label" href="#" role="button" >Usuario: </label>
                                <input type="text" class="form-control"name="usuario" id="usuario" placeholder="Ingrese nombre de usuario">
                            </div>  
                             <div class="mb-3">
                                <label for="clave" id="" class="form-label" href="#" role="button" >Clave: </label>
                                <input type="password" class="form-control"name="clave" id="clave" placeholder="Ingrese su contraseña">
                            </div> 
                            <button type="submit" class="btn btn-outline-primary">Ingresar</button>   
                            <a href="#">Recuperar clave</a>          
                       </form>
                    </div>
                </div>
            </div>  
        </div>

        </main>
        <footer>
            <!-- place footer here -->
        </footer>
        <!-- Bootstrap JavaScript Libraries -->
        <script
            src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
            integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
            crossorigin="anonymous"
        ></script>

        <script
            src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.min.js"
            integrity="sha384-BBtl+eGJRgqQAUMxJ7pMwbEyER4l1g+O15P+16Ep7Q9Q+zqX6gSbd85u4mG4QzX+"
            crossorigin="anonymous"
        ></script>
    </body>
</html>
