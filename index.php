<?php
include 'dbconnection.php';      

session_start();
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = ""; 
}
if($username!=''){
	header("location: home.php");
}
if(isset($_POST['login'])) {
    try {
        $username = $_POST['username'];
        $password = $_POST['password'];
    
        $account = $conn->prepare("SELECT password FROM account WHERE username = ?");
        $account->bind_param("s", $username);
        $account->execute();
        $account->store_result();

        if ($account->num_rows > 0) {
            $account->bind_result($storedHash); 
            $account->fetch();
            if (password_verify($password, $storedHash)) {
                $_SESSION['username'] = $username;
                header("location: home.php");
                $_SESSION['success'] = "You are logged in";
            } else {
                $_SESSION['error'] = "<div class='text-red-500' role='alert'>Oh snap! Invalid login details.</div>";
            }
        } else {
            header("location: index.php");
            $_SESSION['error'] = "<div class='text-red-500' role='alert'>Oh snap! Invalid login details.</div>";
        }
    } catch(Exception $e) {
        echo "Transaction failed: " . $e->getMessage(); 
    }
    
}
?>
<!DOCTYPE html>
<html>
<head>

<script src="vendor/jquery/jquery-3.2.1.min.js"></script>

<script src="https://cdn.tailwindcss.com"></script>
<link rel="stylesheet"
    href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
        
</head>

<body>
    <div class="w-full h-screen bg-slate-900 text-white">
        <div class="w-full container mx-auto py-8 items-center justify-center">
        <?php if(isset($_SESSION['error'])){ echo $_SESSION['error']; }?>
            <form action=""  method="POST">
            <h2 class="text-center font-semibold leading-7 text-white">Login</h2>

            <div class="mt-6 flex items-center justify-center gap-x-6">
                    <input type="text" name="username" id="username" placeholder="Enter username" class="p-3 text-gray-700" />
                    <input type="password" name="password" id="password" placeholder="Enter password" class="p-3 text-gray-700" />
                </div>
                <div class="mt-6 flex items-center justify-center gap-x-6">
                    <a href="register.php" class="text-sm font-semibold leading-6 text-white">Register</a>
                    <button type="submit" name="login" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">Login </button>
                </div>
            </form>     

        </div>
    </div>
</body>
</html>