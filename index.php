<?php 

require_once('./Models/User.php');
session_start();

if(isset($_GET['q']) && $_GET['q'] == 'logout'){
    session_destroy();
    header('Location: login.php');
}

require_once('./Models/ModelFactory.php');

if(isset($_GET['type']) && $_GET['type'] == 'login'){

    if(!isset($_POST['email']) || !isset($_POST['password'])){
        header('Location: login.php?code=1&type=login');
        return;
    } else if (empty($_POST['email']) || empty($_POST['password']) || trim($_POST['email'], ' ') === ''){
        header('Location: login.php?code=1&type=login');
        return;
    }

    $user = ModelFactory::User();
    if(!$user->Login($_POST['email'], $_POST['password'])){
        header('Location: login.php?code=2&type=login');
        return;
    }

} else if(isset($_POST['name'])){
    
    $patterns = array( 
        'name' => "/^[A-Za-z\s]*$/", 
        'phone' => "/^[0-9]{8}$/",
        'address' => "/^[A-Za-z0-9,\s]*$/",
        'email' => "/^[A-Za-z-_.0-9]*@[A-Za-z0-9.]*.[A-Za-z]{2,3}$/",
        'password' => "/^[A-Za-z0-9-\.\_\,\s\!\?\#]*$/"
    );

    try{
        $data = array();
        foreach($patterns as $key => $pattern){
            
            $data[$key] = $_POST[$key];
            
            if(!preg_match($pattern, $data[$key])){
                header('Location: login.php?code=4&type=register&field='.$key);
                return;
            }
        }

        $conn = mysqli_connect('localhost', 'root', '', 'phpStart');

        $query = sprintf('INSERT INTO users(name, phone, address, email, password) values(\'%s\', %b, \'%s\', \'%s\', \'%s\');', 
                            $data["name"],
                            $data["phone"],
                            $data["address"],
                            $data["email"],
                            $data["password"]
                         );
        $result = $conn->query($query);

        if($conn->affected_rows <= 0){
            header('Location: login.php?code=5&type=register&message=' . $conn->error);
            return;
        }

        header('Location: login.php');
        return;
        
    } catch (Exception $e){
        header('Location: login.php?code=3&type=register');
        return;
    }

} else if(!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] == false) {
    header('Location: login.php');
    return;
} else {
    $user = unserialize($_SESSION['user']);
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>
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
    
    <h2>Our Sections</h2>
    <section class="grid">
    <?php 
    
        $conn = mysqli_connect('localhost', 'root', '', 'phpStart');
        $result = $conn->query("SELECT * from categories;");
        $categories = array();

        while($row = $result->fetch_assoc() ){

            if($row["parent_id"] == null){
                $row["children"] = array();
                $categories[$row["id"]] = $row;

            } else {
                $categories[$row["parent_id"]]["children"][] = $row;
            }
        }

        foreach($categories as $category){
    
    ?>
        <article>
            <h3><?php echo $category["name"] ?></h3>
            <ul>
            <?php 
            foreach($category["children"] as $child) 
                echo '<li><a href=\'products.php?c=' . $child["id"] . '\'> ' . $child["name"] . '</a></li>';
            ?>
            </ul>
        </article>
        <?php } ?>
    </section>
</body>
</html>