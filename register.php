<?php 
session_start();
if(isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
} else {
    $username = ""; 
}
if($username!=''){
	header("location: home.php");
}
$showAlert = false;  
$errorMessage = "";
$showError = false;  
$exists=false; 
$timeStamp =time();
if($_SERVER["REQUEST_METHOD"] == "POST") { 
    
    include 'dbconnection.php';    
    
    $username = $_POST["username"];  
    $password = $_POST["password"];  
    $confirmPassword = $_POST["confirmPassword"]; 
    $firstName = $_POST["firstName"];
    $lastName = $_POST["lastName"];
    $email = $_POST["email"];
    $address = $_POST["street"];
    $city = $_POST["city"];  
    $state = $_POST["state"];
    $province = $_POST["province"];
            
    
    $sql = "Select * from account where username='$username'"; 
    
    $result = mysqli_query($conn, $sql); 
    
    $num = mysqli_num_rows($result);  

    if($num == 0) { 
        if(($password == $confirmPassword) && $exists==false) { 
            try {

                $hash = password_hash($password, PASSWORD_DEFAULT); 
                $userId = "USER#" . "$timeStamp$username";
                
                $sqlUser = "INSERT INTO `users` (`id`, `first_name`, `last_name`, `email`, `status`) 
                            VALUES ('$userId', '$firstName', '$lastName', '$email', 'A')" ;
                $sql = "INSERT INTO `account` (`id`, `user_id`, `username`,  
                    `password`) VALUES ('ACCOUNT#$timeStamp$username', '$userId', '$username',  
                    '$hash')"; 
                $sqlAddress = "INSERT INTO `users_address` (`id`, `user_id`, `address`, `city`, `province`, `state`)
                                VALUES ('ADDRESS#$timeStamp$username', '$userId', '$address', '$city', '$province', '$state') ";
                $result = mysqli_query($conn, $sql); 
                $resultUser = mysqli_query($conn, $sqlUser); 
                $resultAdd = mysqli_query($conn, $sqlAddress); 

                mysqli_commit($conn);
                $_SESSION['username'] = $username;
                header("location: home.php");
            } catch (Exception $e) {
                $showAlert = true;  
                mysqli_rollback($conn);
                $errorMessage = "Transaction failed: " . $e->getMessage(); 
            }
    
        }  
        else {  
            $showError = true;  
            $errorMessage = "Passwords do not match";  
        }       
    }
    
   if($num>0)  
   { 
      $exists="Username not available";  
   }  
    
} 
    
?> 
    
<!doctype html> 
    
<html lang="en"> 
  
<head> 
    
    <meta charset="utf-8">  
    <meta name="viewport" content= 
        "width=device-width, initial-scale=1,  
        shrink-to-fit=no"> 
    <script src="https://cdn.tailwindcss.com"></script>

</head> 
    
<body> 
<div class="w-full h-screen"> 
    
    
    <div class="flex w-full text-white align-center content-center justify-center pb-5 bg-slate-900">
    <?php 
    
    if($showAlert) { 
    
        echo ' <div class="text-green-500" role="alert"> 
    
            <strong>Success!</strong> Your account is  
            now created and you can login.  
            <button type="button" class="close"
                data-dismiss="alert" aria-label="Close">  
                <span aria-hidden="true">×</span>  
            </button>  
        </div> ';  
    } 
    
    if($showError) { 
    
        echo ' <div class="text-red-500" role="alert">  
        <strong>Error!</strong> '. $errorMessage.'
    
       <button type="button" class="close" 
            data-dismiss="alert aria-label="Close"> 
            <span aria-hidden="true">×</span>  
       </button>  
     </div> ';  
   } 
        
    if($exists) { 
        echo ' <div class="text-red-500" role="alert"> 
    
        <strong>Error!</strong> '. $exists.'
        <button type="button" class="close" 
            data-dismiss="alert" aria-label="Close">  
            <span aria-hidden="true">×</span>  
        </button> 
       </div> ';  
     } 
   
?> 
    <form action="register.php" method="post"> 
        <p id="equal"></p>

        <h1 class="text-center text-2xl font-bold">Signup Here</h1>  
    
        <div class="form-group">  
            <label for="username" class="block mb-2 text-sm font-semibold">Username</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="username" placeholder="Enter username"
            name="username"  required>     
        </div> 
        
        <div class="grid gap-6 mb-6 md:grid-cols-2 mt-2">
            <div>
            <label for="password" class="block mb-2 text-sm font-semibold">Password</label>  
            <input type="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter password"
            id="password" name="password" required>  
            </div> 
            <div>
            <label for="confirmPassword" class="block mb-2 text-sm font-semibold">Confirm Password</label>  
            <input type="password" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" placeholder="Enter confirm password"
                id="confirmPassword" name="confirmPassword" required> 
    
            <span id="emailHelp" class="text-slate-600 text-sm"> 
            Make sure to type the same password 
            </span>  
            </div> 
        </div>       
        <h2>Personal Information</h2>
        
        <div class="grid gap-6 mb-6 md:grid-cols-2 mt-2">
            <div>
            <label for="firstName" class="block mb-2 text-sm font-semibold">First Name</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="firstName" placeholder="Enter first name"
            name="firstName" aria-describedby="firstName" required>     
        </div> 
    
        <div>  
            <label for="lastName" class="block mb-2 text-sm font-semibold">Last Name</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="lastName" placeholder="Enter last name"
            name="lastName"  required>     
            </div> 
        </div> 
    
        <div class="form-group">  
            <label for="email" class="block mb-2 text-sm font-semibold">Email</label>  
        <input type="email" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="email" placeholder="Enter email address"
            name="email" required>    
        </div> 
        <div class="grid gap-6 mb-6 md:grid-cols-2 mt-2">
    
        <div class="form-group">  
            <label for="street" class="block mb-2 text-sm font-semibold">Street</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="street"  placeholder="Enter street"
            name="street"  required>     
        </div> 
    
        <div class="form-group">  
            <label for="city" class="block mb-2 text-sm font-semibold">City</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="city" placeholder="Enter city"
            name="city"  required>     
        </div> 
    
        <div class="form-group">  
            <label for="state" class="block mb-2 text-sm font-semibold">State</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="state" placeholder="Enter state"
            name="state"  required>     
        </div> 
    
        <div class="form-group">  
            <label for="province" class="block mb-2 text-sm font-semibold">Province</label>  
        <input type="text" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full p-2.5" id="province" placeholder="Enter Province"
            name="province"  required>     
        </div> 
    
        <button type="submit" class="text-center bg-green-500 py-2 hover:bg-green-600"> 
        SignUp 
        </button>  
        <a href="index.php" class="border-2 border-slate-100 hover:bg-slate-700 py-2 text-center">Login</a>
        </div> 

    </form>  
    </div>
</div> 
    
<!-- Optional JavaScript -->  
<!-- jQuery first, then Popper.js, then Bootstrap JS --> 
    
<script src=" 
https://code.jquery.com/jquery-3.5.1.slim.min.js" 
    integrity=" 
sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" 
    crossorigin="anonymous"> 
</script> 
    
<script src=" 
https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" 
    integrity= 
"sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" 
    crossorigin="anonymous"> 
</script> 
    
<script src=" 
https://stackpath.bootstrapcdn.com/bootstrap/4.5.0/js/bootstrap.min.js"  
    integrity= 
"sha384-OgVRvuATP1z7JjHLkuOU7Xw704+h835Lr+6QL9UvYjZE3Ipu6Tp75j7Bh/kR0JKI"
    crossorigin="anonymous"> 
</script>  
<script src="js/signup.js"></script>
</body>  
</html> 