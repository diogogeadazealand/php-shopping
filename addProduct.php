<?php 
   session_start();

   if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
       header('Location: login.php');
       return;
   }

   require_once("./Models/ModelFactory.php");
   require_once("./Models/User.php");

   $user = unserialize($_SESSION["user"]);
   $result;
   $product = ModelFactory::Product();

   if(intval($user->is_admin) !== 1){
    header('Location: index.php');
    return;
   }

   if(isset($_GET['edit'])){

        $product->Get($_GET['edit']);

   } else if(isset($_POST['id']) && !empty($_POST['id'])){

        $product->Format($_POST);
        $result = $product->validate();

        if($result->success === true){

            if($product->Update() === true){
                header("Location: admin.php");
            }
            
        }

   } 
   else if(count($_POST) > 0){
        $product->Format($_POST);
        $result = $product->validate();

        if($result->success === true){

           if( $product->Insert() === false ){
            $result->SetSuccess(false);
           }
       }

   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product</title>
    <link rel="stylesheet" href="style.css" />
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
<h2><?php echo !isset($product->id) ? 'Create New' : 'Update'; ?> Product</h2>
<?php 
    if(isset($result) && $result->success === true){
        echo '<p style="color: green; text-align: center;">Product was added successfully</p>';
    } else if(isset($result) && $result->success === false){
        if(gettype($result->text) === "array"){
            foreach($result->text as $msg){
                echo '<p style="color: red; text-align: center">' . $msg . '</p>';
            }
        } else {
            echo '<p style="color: red; text-align: center">An error occoured</p>';
        }
    }
?>
    <form method="POST" action="addProduct.php">
        <input type="hidden" name="id" value='<?php echo !empty($product->id) ?  $product->id : ''; ?>'>
        <div>
            <label for='name'>Name</label>
            <input type="text" name="name" value='<?php echo !empty($product->name) ?  $product->name : ''; ?>'/>
        </div>
        <div>
            <label for='description'>Description</label>
            <textarea name="description" value='<?php echo !empty($product->description) ?  $product->description : ''; ?>'></textarea>
        </div>
        <div>
            <label for='image'>Image</label>
            <input type="text" name="image" value='<?php echo !empty($product->image) ?  $product->image : ''; ?>'/>
        </div>
        <div>
            <label for='price'>Price</label>
            <input type="number" min="0" step="0.01" name="price" value='<?php echo !empty($product->price) ?  $product->price : ''; ?>'/>
        </div>
        <div>
            <label for="category">Category</label>
            <select name="category_id">
            <?php 
                $categories = ModelFactory::Category()->Fetch();
            
                if(!isset($product->category_id)){
                    echo '<option selected value="-1"></option>';
                }

                foreach($categories as $category){
                    echo '<option disabled>' . $category['name'] . '</option>';
                    foreach($category["children"] as $child){
                        if(isset($product->category_id) && $product->category_id === $child['id'])
                            echo '<option selected value="'. $child['id'].'">' . $child['name'] . '</option>';
                        else
                            echo '<option value="'. $child['id'].'">' . $child['name'] . '</option>';
                    }
                }
            ?>
            </select>
        </div>
        <input type="submit" value="<?php echo isset($product->id) ? 'Update' : 'Create';?>"/>
    </form>
</body>
</html>