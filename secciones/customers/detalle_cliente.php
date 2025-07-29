<?php
    include("../../bd.php");
    if(isset($_GET['txtID'])){
        $txtID=(isset($_GET['txtID']))?$_GET['txtID']:"";
        $sentencia=$conexion->prepare("SELECT * FROM customers WHERE customers_id=:id");
        $sentencia->bindParam(":id",$txtID);
        $sentencia->execute();

        $registro=$sentencia->fetch(PDO::FETCH_LAZY);
        $customers_id=$registro["customers_id"];
        $first_name=$registro["first_name"];
        $last_name=$registro["last_name"];
        $cliente_nombre=$first_name."  ".$last_name;
        $foto=$registro['imagen'];
        $telefono=$registro['phone'];
        $email=$registro['email'];
        $calle=$registro['street'];
        $ciudad=$registro['city'];
        $departamento=$registro['state'];
    }

    ob_start();
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Cliente</title>
</head>
<body>
    <head>
        <strong><h1>Detalle del Cliente</h1></strong>
    </head>
   <h4>Santa Cruz, Bolivia <strong><?php echo date('d M Y'); ?></strong></h4> 
   <br></br>

   <p><strong>ID: </strong><?php echo $customers_id?></p>
   <p><strong>Nombre del Cliente: </strong> <?php echo $cliente_nombre?></p>
   <p><strong>Imagen:</strong> 
        <img width="50"
        src="<?php echo $foto?>"
        class="img-fluid rounded" alt="Foto del cliente"/>
   </p>
   <p><strong>Tel&eacute;fono: </strong><?php echo $telefono?></p>
   <p><strong>Correo Electronico: </strong><?php echo $email ?></p>
   <p><strong>Direcci&oacute;n: </strong><?php echo $calle?></p>
   <p><strong>Ciudad: </strong><?php echo $ciudad?></p>
   <p><strong>Departamento: </strong><?php echo $departamento ?></p>
   <br>
</body>
</html>
<?php
    $HTML=ob_get_clean();

    require_once("../../libs/autoload.inc.php");
    use Dompdf\Dompdf;
    $dompdf=new Dompdf();
    $opciones=$dompdf->getOptions();
    $opciones->set(array("isRemoteEnabled"=>true));
    $dompdf->setOptions($opciones);

    $dompdf->loadHtml($HTML);
    $dompdf->setPaper('letter');
    $dompdf->render();
    $dompdf->stream("detalle_cliente.pdf", array("Attachment"=>false));

?>