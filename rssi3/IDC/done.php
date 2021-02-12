<?php
// Initialize the session
session_start();
 
// If session variable is not set it will redirect to login page
if(!isset($_SESSION['username']) || empty($_SESSION['username'])){
  header("location: login.php");
  exit;
}
?>
 
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Welcome</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">
    <style type="text/css">
        body{ font: 14px sans-serif; text-align: center; }
    </style>
</head>
<body>
    <div class="page-header">
        <h1>Hi, <b><?php echo $_SESSION['username']; ?></b>. La informacion fue ingresada a la base de datos con exito.</h1>
    </div>
    <p><a href="logout.php" class="btn btn-danger">Cerrar sesion</a></p>
    <p><a href="agregar_ip.php" class="btn btn-danger">Agregar datos GANANCIA</a></p>
    <?php 
    if($_SESSION['access_level'] == 3) {
    echo "<p><a href='register.php' class='btn btn-danger'>Register new account</a></p>";
    }
    else {
        echo "";
    }
    ?>
</body>
</html>