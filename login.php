<?php

    session_start();

    if(isset($_SESSION['logged_in']) && $_SESSION['logged_in'] == true) {
        header('Location: index.php');
    }

    $hideRegister = false;
           if(!isset($_GET["type"]) || $_GET['type'] == 'login'){
               $hideRegister = true;
            }
        ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="style.css" />
    <style>

        p{
            text-align: center;
        }

        .error{
            color: red;
        }
    </style>
</head>
<body>
    <div id="login" style="<?php echo (!$hideRegister) ? 'display: none' : ''; ?>">
        <form action="index.php?type=login" method="POST">
            <div>
                <label for="email">Email</label>
                <input type="text" require name="email">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password" require>
            </div>
            <?php 
                if(isset($_GET['code']) && isset($_GET['type']) && $_GET['type'] == 'login'){
                    switch ($_GET['code']){
                        case 1:
                            echo '<p class=\'error\'>Username and password can\'t be empty</p>';
                            break;
                        case 2:
                            echo '<p class=\'error\'>Username or password is incorrect</p>';
                            break;
                        default: break;
                    }
                }
            ?>
            <input type="submit" value="Login">
        </form>
        <p>Don't have an account yet? <button onClick="ShowRegister()">Register</button> now</p>
    </div>
    <div id="register" style="<?php echo ($hideRegister) ? 'display: none' : ''; ?>">
        <form action="index.php?type=register" method="POST">
            <div>
                <label for="name">Name</label>
                <input type="text" name="name">
            </div>
            <div>
                <label for="phone">Phone Number</label>
                <input type="text" maxlength="8" maxlength="8" name="phone">
            </div>
            <div>
                <label for="address">Address</label>
                <input type="text" name="address">
                </div>
            <div>
                <label for="email">E-mail</label>
                <input type="text" name="email">
            </div>
            <div>
                <label for="password">Password</label>
                <input type="password" name="password">
            </div>
            <input type="submit" value="Register">
            <?php 
                if(isset($_GET['code']) && isset($_GET['type']) && $_GET['type'] == 'register'){
                    switch ($_GET['code']){
                        case 3:
                            echo '<p class=\'error\'>One or more fields are empty</p>';
                            break;
                        case 4:
                            if(!isset($_GET['field']))
                                echo '<p class=\'error\'>One or more fields have invalid formats</p>';
                            else 
                                echo '<p class=\'error\'>' . $_GET['field'] .' is invalid</p>';
                            break;
                        case 5:
                            echo '<p class=\'error\'>' . $_GET['message'] . '</p>';
                            break;
                        default: break;
                    }
                }
            ?>
        </form>
        <p>Already have an account? <button onClick="ShowLogin()">Login</button> now</p>
    </div>
    
    <script>
        function ShowRegister(){
            document.querySelector("#login").style.display = "none";
            document.querySelector("#register").style.display = "block";
        }

        function ShowLogin(){
            document.querySelector("#login").style.display = "block";
            document.querySelector("#register").style.display = "none";
        }
    </script>
</body>
</html>