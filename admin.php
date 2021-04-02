<?php 

session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header('Location: login.php');
    return;
}

require_once("./Models/ModelFactory.php");
require_once("./Models/User.php");

$user = unserialize($_SESSION["user"]);

if(isset($_GET['d'])){
    $product = ModelFactory::Product();
    $product->id = $_GET['d'];
    $product->Delete();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        section{
            width: 80%;
            display: block;
            margin: auto;
            background-color: white;
            padding: 15px;
        }

        section div{
            display: grid;
            grid-template-columns: 1fr 2fr 1fr 1fr;
        }

        .options a{
            padding: 5px 15px;
            background-color: red;
            color: white;
            text-decoration: none;
            margin: 0 10px;
        }

        .options a:first-child{
            background-color: lightseagreen;
        }
        
        #create-product{
            position: absolute;
            top: -4px;
            padding: 10px 15px;
            left: calc( 50vw + 96px);
            background-color: lightseagreen;
            color: white;
            text-decoration: none;
        }

        div{
            position: relative;
        }
    </style>
</head>
<body>
    <nav>
        <h1>Product Store</h1>
        <ul>
            <li><a href='index.php'>Home</a></li>
            <li><a href='products.php'>Products</a></li>
            <li>Admin Area</li>
        </ul>
        <a id="logout" href="index.php?q=logout">Log out</a>
    </nav>

    <div>
        <h2>Products List</h2>
        <?php 

            if(intval($user->is_admin) === 1) {
                echo '<a id="create-product" href="addProduct.php">Create Product</a>';
            }
        ?>
        <section>
            <div>
                <h4>ID</h4>
                <h4>Name</h4>
                <h4>Price</h4>
                <h4></h4>
                </tr>
            </div>
            <div>
                <?php 
                
                    $product = ModelFactory::Product();
                    $products = $product->Select([], ['id', 'name', 'price']);

                    foreach($products as $prod){
                        echo '<p>' . $prod['id'] . '</p>';
                        echo '<p>' . $prod['name'] . '</p>';
                        echo '<p>' . $prod['price'] . '</p>';
                        echo '<p class="options"><a href="addProduct.php?edit=' . $prod['id'] . '">Edit</a><a href="" class="delete" onClick="Delete(' . $prod['id'] . ', \'' . $prod['name'] . '\')">Delete</a></p>';
                    }
                
                ?>
            </div>
        </table>
    </div>
    <script>
        let buttons = document.querySelectorAll(".options a.delete");
        buttons.forEach(button => {
            button.addEventListener('click', e => e.preventDefault());
        });

        function Delete(id, name){
            if(confirm(`Are you sure you want to delete ${name} (id ${id})?`)){
                window.location = `${location.pathname}?d=${id}`;
            }
        }
    </script>
</body>
</html>