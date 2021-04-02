<?php 

session_start();

if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header('Location: login.php');
    return;
}

require_once("./Models/ModelFactory.php");
require_once("./Models/User.php");
require_once("./Models/Item.php");
require_once("./Models/Product.php");

$user = unserialize($_SESSION["user"]);
$cart = isset($_SESSION['cart']) ? unserialize($_SESSION['cart']) : array();

if(isset($_POST['id']) && isset($_POST['quantity'])){
    $product = ModelFactory::Product();
    $product->Get($_POST['id']);
    $cart[] = new Item($product, $_POST['quantity']);
    $_SESSION['cart'] = serialize($cart);
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
        .body{
            display: grid;
            grid-template-columns: 1fr 60% 1fr;

        }

        .grid{
            grid-column-start: 2;
            width: 100%;
            grid-template-columns: 25% 25% 25% 25%;
        }

        form{
            width: unset;
            padding: 0;
            margin: auto;
        }

        form input{
            max-width: 100%;
        }

        form input[type='number']{
            width: 40px;
        }

        .sidebar{
            padding-top: 15px;
            background-color: white;
        }

        .sidebar article{
            max-width: 90%;
            margin: auto;
            font-size: 0.8em;
        }
    </style>
</head>
<body>
    <nav>
        <h1>Product Store</h1>
        <ul>
            <li><a href='index.php'>Home</a></li>
            <li><a href='products.php'>Products</a></li>
            <?php if($user->is_admin): ?> <li><a href='admin.php'>Admin Area</a></li> <?php endif; ?>
        </ul>
        <a id="logout" href="index.php?q=logout">Log out</a>
    </nav>

    <?php
        $category = ModelFactory::Category();
        $category->name = "Products";
    if(isset($_GET["c"]) && is_numeric($_GET["c"])){
        $category->Get($_GET["c"]);
    }

    ?>
    <section>
        <h2>Our <?php echo $category->name; ?></h2>
    </section>
    <section class="body">
        <section class="grid">
                <?php 

                    $product = ModelFactory::Product();
                    $products = (isset($_GET['c'])) ? $product->Select(["category_id" => $_GET['c']]) : $product->Select();

                    foreach($products as $prod){
                    ?>
                        <article>
                            <img src="<?php echo $prod["image"]; ?>"/>
                            <h3><?php echo $prod["name"]; ?></h3>
                            <p><?php echo $prod["price"]; ?></p>
                            <form action='products.php' method="POST">
                                <input type='hidden' name='id' value='<?php echo $prod['id'] ?>' />
                                <input type='number' value='1' name='quantity'/>
                                <input type='submit' value='Add to cart' />
                            </form>
                        </article>
                    <?php
                    
                    }
                ?>
        </section>
        <section class='sidebar'>
        <?php 
                    $total = 0;
                    foreach($cart as $prod){
                        $total += $prod->product->price;
                        ?>
                            <article>
                                <img src='<?php echo $prod->product->image; ?>' />
                                <h3><?php echo $prod->product->name; ?> x<?php echo $prod->quantity?></h3>
                            </article>
                        <?php
                    }
        
        ?>
        <h2>Total: <?php echo $total; ?>€</h2>
        </section>
    </section>

</body>
</html>