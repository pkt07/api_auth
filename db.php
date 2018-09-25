<?php
session_start();
$username = "";
$email    = "";
$errors = array(); 

$db = mysqli_connect('localhost:3306', 'root', 'prabhat07', 'login');

if (isset($_POST['reg_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $email = mysqli_real_escape_string($db, $_POST['email']);
  $password_1 = mysqli_real_escape_string($db, $_POST['password_1']);
  $password_2 = mysqli_real_escape_string($db, $_POST['password_2']);

  if (empty($username)) { array_push($errors, "Username is required"); }
  if (empty($email)) { array_push($errors, "Email is required"); }
  if (empty($password_1)) { array_push($errors, "Password is required"); }
  if ($password_1 != $password_2) {
	array_push($errors, "The two passwords do not match");
  }

  $user_check_query = "SELECT * FROM mylogin WHERE username='$username' OR email='$email' LIMIT 1";
  $result = mysqli_query($db, $user_check_query);
  $user = mysqli_fetch_assoc($result);
  print_r($user);
  if ($user) { // if user exists
    if ($user['username'] === $username) {
      array_push($errors, "Username already exists");
    }

    if ($user['email'] === $email) {
      array_push($errors, "email already exists");
    }
  }

  if (count($errors) == 0) {
  	$salt = 'hello_1m_@_SaLT';
	$password = hash('sha256', $password_1 . $salt);

  	$query = "INSERT INTO mylogin (username, email, password) 
  			  VALUES('$username', '$email', '$password')";
  	mysqli_query($db, $query);
  	$_SESSION['username'] = $username;
  	$_SESSION['success'] = "You are now logged in";
  	header('location: login.php');
  }
}
if (isset($_POST['login_user'])) {
  $username = mysqli_real_escape_string($db, $_POST['username']);
  $password = mysqli_real_escape_string($db, $_POST['password']);

  if (empty($username)) {
  	array_push($errors, "Username is required");
  }
  if (empty($password)) {
  	array_push($errors, "Password is required");
  }

  if (count($errors) == 0) {
  	$salt = 'hello_1m_@_SaLT';
  	$password1 = hash('sha256', $password . $salt);
  	$query = "SELECT * FROM mylogin WHERE username='$username' AND password='$password1'";
  	$results = mysqli_query($db, $query);
  	if (mysqli_num_rows($results) == 1) {
  	
  	
  	while($row = mysqli_fetch_assoc($results))
			{
				$dbusername=$row['username'];
				$dbpassword=$row['password'];
				$dbid = $row['id'];
			}
			if($username == $dbusername && $password1 == $dbpassword)
			{
				// echo "asddd";
				session_start();
				$_SESSION['sess_user']=$username;
				$_SESSION['id']=$dbid;
				//Redirect Browser
				header("Location:untitled.php");
			}
  	}else {
  		array_push($errors, "Wrong username/password combination");
  	}
  }
}

?>

