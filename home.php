<?php
session_start();
include 'dbconnection.php';    
$username = $_SESSION['username'];
if($username==''){
	header("location: index.php");
}

$timeStamp =time();

$showMessage = "";
$isError = false;


$userData = $conn->prepare("SELECT user_id FROM account where username = ? ");
$userData->bind_param("s", $username);
$userData->execute();
$userData->store_result();
$userData->bind_result($userId);
$userData->fetch();

if (isset($_POST['deleteId'])) {
	$idToDelete = $_POST['deleteId'];
	$sqlDelete = $conn->prepare("DELETE FROM users_address where user_id = ?");
	$sqlDelete->bind_param('s', $idToDelete);
	if ($sqlDelete->execute()) {
		$isError = false;
		$showMessage = "Record deleted successfully";
	} else {
		$isError = true;
		$showMessage = "Error deleting record";
	}
}
if (isset($_POST['add'])) {
	
    $address = $_POST["street"];
    $city = $_POST["city"];  
    $state = $_POST["state"];
    $province = $_POST["province"];
	$addId= "ADDRESS#$timeStamp$username";
	$status = "A";
	$sqlAddress = "INSERT INTO `users_address` (`id`, `user_id`, `address`, `city`, `province`, `state`, `status`)
                VALUES (?, ?, ?, ?, ?, ?, ?) ";
                
	$stmt = $conn->prepare($sqlAddress);
	$stmt->bind_param("sssssss", $addId, $userId, $address, $city, $province, $state, $status);

	$stmt->execute();

	if ($stmt->affected_rows > 0) {
		$isError = false;
	} else {
		$isError = true;

	}
}

$data = $conn->prepare("SELECT address, city, state, province FROM users_address where user_id = ?");
$data->bind_param("s", $userId);
$data->execute();
$data->store_result();
$data->bind_result($address, $city, $state, $province);
$data->fetch();
?>

<html>
<head>
<title>Dashboard</title>
<script src="https://cdn.tailwindcss.com"></script>

<style>
.container{
	width:50%;
	height:30%;
	padding:20px;
}
</style>
</head>
<body>
<nav class="bg-white border-gray-200 dark:bg-gray-900">
  <div class="max-w-screen-xl flex flex-wrap items-center justify-between mx-auto p-4">
    <a href="#" class="flex items-center space-x-3 rtl:space-x-reverse">
        <span class="self-center text-2xl text-white font-semibold whitespace-nowrap"><?php echo $username ?></span>
    </a>
    <div class="hidden w-full md:block md:w-auto" id="navbar-default">
      <ul class="font-medium flex flex-col p-4 md:p-0 mt-4 border border-gray-100 rounded-lg bg-gray-50 md:flex-row md:space-x-8 rtl:space-x-reverse md:mt-0 md:border-0 md:bg-white dark:bg-gray-800 md:dark:bg-gray-900 dark:border-gray-700">
        <li>
          <a href="logout.php" class="block py-2 px-3 text-gray-900 rounded hover:bg-gray-100 md:hover:bg-transparent md:border-0 md:hover:text-blue-700 md:p-0 dark:text-white md:dark:hover:text-blue-500 dark:hover:bg-gray-700 dark:hover:text-white md:dark:hover:bg-transparent">Logout</a>
        </li>
      </ul>
    </div>
  </div>
</nav>
<div class="w-full h-screen">
<?php
if($showMessage) { 
	if ($isError) {
		?> 
		<div class="text-red-500 w-full p-3 bg-slate-100 rounded">Transaction failed. Please try again later</div>
		<?php
	} else {
		?> 
		<div class="text-green-500 w-full p-3 bg-slate-100 rounded"><?php echo $showMessage; ?></div>
		<?php
	}
} 
?>
<div class="flex w-full container py-8 items-center content-center justify-center">

	<div class="w-full">
		<h2 class="my-2" align="center">Welcome <?php echo $_SESSION['username'];?></h2>
		<?php 
			if ($data->num_rows > 0) {
				?>  
					<table class="table-auto w-full border-2 text-center">
						<thead class="bg-slate-400 hover:bg-slate-600">
							<tr class="">
								<th>Street</th>
								<th>City</th>
								<th>State/Province</th>
								<th>Zip/Post Code</th>
								<th>Action</th>
							</tr>
						</thead>
						<tbody>
							<tr>
								<td>
								<?php echo $address ?>
								</td>
								<td><?php echo $city ?></td>
								<td><?php echo $province ?></td>
								<td><?php echo $state ?></td>
								<td class="pt-2">
									<!-- <form method='post'>
										<input type='hidden' id="id" name='id' value='<?php echo $userId ?>'>
										<button type='button' onclick='confirmDelete(<?php echo $user ?>)' name='delete' value='Delete' class='bg-red-700 text-white cursor-pointer py-2 px-3 hover:bg-slate-300 hover:text-slate-700'>

										</form> -->
									<form id='deleteForm' method='post'>
										<input type='hidden' id="deleteId" name='deleteId' value='<?php echo $userId ?>'>
										<input type='button' onclick='confirmDelete("<?php echo $userId ?>")' name='delete' value='Delete' class='bg-red-700 text-white cursor-pointer py-2 px-3 hover:bg-slate-300 hover:text-slate-700' />
									</form>
								</td>
							</tr>
						</tbody>
					</table>
				<?php
			} else {
				?>
				<form method="post" class="">
				<div class="w-full flex mx-auto items-center content-center justify-center mt-8">
				<input type="text" name="street" id="street" class="border p-3 mx-1" placeholder="Enter street name" required/>
				<input type="text" name="city" id="city" class="border p-3 mx-1" placeholder="Enter city" required/>
				<input type="text" name="province" id="province" class="border p-3 mx-1" placeholder="Enter state/province " required/>
				<input type="number" name="state" id="state" class="border p-3 mx-1" placeholder="Enter zip/post code" required/>
				</div>
				<div  class="w-full flex mx-auto items-center content-center justify-center mt-8">
				<input type="submit" name="add" id="add" class="px-8 py-2 bg-green-400 hover:bg-green-500" value="Add" />
				</div>
				</form>
				<?php
			}
		?>
       
		</div>
</div>
</div>
<script>
    function confirmDelete(id) {
        if (confirm("Are you sure you want to delete this record?")) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
</body>
</html>