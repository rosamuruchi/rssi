    <?php
    // Initialize the session
    session_start();
     
    // If session variable is not set it will redirect to login page
    if($_SESSION['access_level'] != 3){
      header("location: login.php");
      exit;
    }
    ?>

    <?php

    // Include config file

    require_once 'config.php';

     

    // Define variables and initialize with empty values

    $username = $password = $confirm_password = "";

    $username_err = $password_err = $confirm_password_err = "";

     

    // Processing form data when form is submitted

    if($_SERVER["REQUEST_METHOD"] == "POST"){

     

        // Validate username

        if(empty(trim($_POST["username"]))){

            $username_err = "Please enter a username.";

        } else{

            // Prepare a select statement

            $sql = "SELECT id FROM users WHERE username = ?";

            

            if($stmt = mysqli_prepare($link, $sql)){

                // Bind variables to the prepared statement as parameters

                mysqli_stmt_bind_param($stmt, "s", $param_username);

                

                // Set parameters

                $param_username = trim($_POST["username"]);

                

                // Attempt to execute the prepared statement

                if(mysqli_stmt_execute($stmt)){

                    /* store result */

                    mysqli_stmt_store_result($stmt);

                    

                    if(mysqli_stmt_num_rows($stmt) == 1){

                        $username_err = "This username is already taken.";

                    } else{

                        $username = trim($_POST["username"]);

                    }

                } else{

                    echo "Oops! Something went wrong. Please try again later.";

                }

            }

             

            // Close statement

            mysqli_stmt_close($stmt);

        }

        

        // Validate password

        if(empty(trim($_POST['password']))){

            $password_err = "Por favor, ingrese una clave.";     

        } elseif(strlen(trim($_POST['password'])) < 6){

            $password_err = "La clave tiene que tener por lo menos 6 caracteres.";

        } else{

            $password = trim($_POST['password']);

        }

        

        // Validate confirm password

        if(empty(trim($_POST["confirm_password"]))){

            $confirm_password_err = 'Por favor confirma clave.';     

        } else{

            $confirm_password = trim($_POST['confirm_password']);

            if($password != $confirm_password){

                $confirm_password_err = 'No se pudo confirmar la clave. Intente nuevamente.';

            }

        }

        

        // Check input errors before inserting in database

        if(empty($username_err) && empty($password_err) && empty($confirm_password_err)){

            

            // Prepare an insert statement
            $user_access = $_POST['access_level'];
            $sql = "INSERT INTO users (username, password, access_level) VALUES (?, ?, ?)";

             

            if($stmt = mysqli_prepare($link, $sql)){

                // Bind variables to the prepared statement as parameters

                mysqli_stmt_bind_param($stmt, "ssi", $param_username, $param_password, $param_user_access);

                

                // Set parameters

                $param_username = $username;

                $param_password = password_hash($password, PASSWORD_DEFAULT); // Creates a password hash
                if($user_access == 'Administrador') {
                    $param_user_access = 3;
                } 
                elseif ($user_access == 'Operador') {
                    $param_user_access = 2;
                }
                else {
                    $param_user_access = 1;
                }

                // Attempt to execute the prepared statement

                if(mysqli_stmt_execute($stmt)){

                    // Redirect to login page

                    header("location: nuevo_usuario.php");
                } else{

                    echo "Algo salio mal. Intenta mas tarde.";

                }

            }

             

            // Close statement

            mysqli_stmt_close($stmt);

        }

        

        // Close connection

        mysqli_close($link);

    }

    ?>

     

    <!DOCTYPE html>

    <html lang="en">

    <head>

        <meta charset="UTF-8">

        <title>Sign Up</title>

        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">

        <style type="text/css">

            body{ font: 14px sans-serif; }

            .wrapper{ width: 350px; padding: 20px; }

        </style>

    </head>

    <body>

        <div class="wrapper">
            <p><a href="welcome.php" class="btn btn-danger">Volver a Inicio</a></p>

            <h2>Registrar</h2>

            <p>Rellenar campos para crear usuario.</p>

            <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">

                <div class="form-group <?php echo (!empty($username_err)) ? 'has-error' : ''; ?>">

                    <label>Nombre de usuario</label>

                    <input type="text" name="username"class="form-control" value="<?php echo $username; ?>">

                    <span class="help-block"><?php echo $username_err; ?></span>

                </div>    

                <div class="form-group <?php echo (!empty($password_err)) ? 'has-error' : ''; ?>">

                    <label>Clave</label>

                    <input type="password" name="password" class="form-control" value="<?php echo $password; ?>">

                    <span class="help-block"><?php echo $password_err; ?></span>

                </div>

                <div class="form-group <?php echo (!empty($confirm_password_err)) ? 'has-error' : ''; ?>">

                    <label>Confirma clave</label>

                    <input type="password" name="confirm_password" class="form-control" value="<?php echo $confirm_password; ?>">

                    <span class="help-block"><?php echo $confirm_password_err; ?></span>

                </div>
                  <select name='access_level'>
                        <option value="ALT">Regular</option>
                        <option value="NO">Operador</option>
                        <option value="ES">Administrador</option>
                  </select>
                  <br />
                <br>
                <div class="form-group">

                    <input type="submit" class="btn btn-primary" value="Submit">

                    <input type="reset" class="btn btn-default" value="Reset">

                </div>

            </form>

        </div>    

    </body>

    </html>

