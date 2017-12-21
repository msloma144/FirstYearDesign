<?php

	require_once('./include.php');
	session_start();

	function test_input($data)
	{
		$data = trim($data);
		$data = stripslashes($data);
		$data = htmlspecialchars($data);
		return $data;
	}
	
	$usernameQuery = test_input($_POST['username']);

	//query db using id and grab user data
	$statement = $pdo->prepare('SELECT * from Login_info where username = :username');
	$statement->bindParam(':username', $usernameQuery);
	$statement->execute();
	$results = $statement->fetch();
	
	$id = $results['id'];
	$firstname = $results['firstname'];
	$lastname = $results['lastname'];
	$username = $results['username'];
	$password = $results['password'];
	$admin = $results['admin'];
	$class = $results['class'];
	
	//testing if passwords match
	if(!($username == '')){ //check if username is set
		$password_valid = password_verify(test_input($_POST['password']), $password);
	}
	else;

	//declare session variables
	$_SESSION['id'] = $id;
	$_SESSION['username'] = $username;
	$_SESSION['password_valid'] = $password_valid;
	$_SESSION['admin'] = $admin;
	
	
	//test if result was found
	if($password_valid == 1){
	//if admin, direct to admin page
		if($admin == 1){
			header('Location: admin_page.php');
			exit();
		}
		else{
		//show they were logged in and recorded
			try{
				$query = "SELECT * FROM Classes WHERE class_name=\"" . $class . "\"";
				$statement = $pdo->prepare($query);
				$statement->execute();
				$results = $statement->fetch();
				
				$current_time = date("g:i a");
				$start_time = $results['time_start'];
				$end_time = $results['time_end'];
				
				$time1 = DateTime::createFromFormat('H:i a', $current_time);
				$time2 = DateTime::createFromFormat('H:i a', $start_time);
				$time3 = DateTime::createFromFormat('H:i a', $end_time);
				
				if ($time1 > $time2 && $time1 < $time3)
				{
					//insert 1 into date slot
					$query = "UPDATE " . $class . " SET `" . date("m/d/Y") . "`=1 WHERE id=" . $id;
					$statement = $pdo->prepare($query);
					$statement->execute();
					
					$loginContent =
					'
					<form action = "logout.php">
						Welcome ' . htmlentities($firstname) . ' ' . htmlentities($lastname) . ' , you have been recorded!<br>
						Logout: <input type="submit"></input>
					</form>
					';
				}
				elseif($time1 < $time2 || $time1 > $time3){
					$loginContent=
					'
					<form action = "index.php">
					You have logged in outside of class time...<br>
					Current time: ::current_time::
					Class Starts: ::start_time::
					Class Ends: ::end_time::
					Please return to login page<br>
					<input type="submit"></input>
					</form>
					';
					$loginContent = str_replace('::current_time::', $current_time, $loginContent);
					$loginContent = str_replace('::start_time::', $start_time, $loginContent);
					$loginContent = str_replace('::end_time::', $end_time, $loginContent);
					session_unset();
					session_destroy();
				}
				else{
					$loginContent=
					'
					<form action = "index.php">
						There is no class today...<br>
						Please return to login page<br>
						<input type="submit"></input>
					</form>
					';
					session_unset();
					session_destroy();
				}
				
			}
			catch(PDOException $Exception){
				
			}
		}
	}
	//if no result found, direct to login page
	else{
		$loginContent=
		'
		<form action = "index.php">
			Login Failed!<br>
			Please return to login page<br>
			<input type="submit"></input>
		</form>
		
		';
		session_unset();
		session_destroy();
	}



$htmlPage =
'
<!DOCTYPE HTML>
<html>
<head>
  <title>Welcome</title>
  <style>
  body {
    margin-top: 0px;
    margin-right: 0px;
    margin-bottom: 0px;
    margin-left: 0px
  } 
  </style>
</head>

<body>
<style>
.topbar{
  background-image:url("http://www.utoledo.edu/images/nav2015/header_bg.jpg");
  height: 40px;
  font-size: 30px;
  color: yellow;
  }
  
.content{
  border: 2px solid black;
  margin: 20%;
}

.contentinside{
  margin: 10%;
  font-size: 180%;
}

.footer{
  background-image:url("http://www.utoledo.edu/images/nav2015/header_bg.jpg");
  height: 60px;
  }
</style>

<div class="topbar">
	<a style="margin-left:1.5%;">University of Toledo</a>
</div>
<div class="content">
	<div class="contentinside">
		::body::
	</div>
</div>
<div class="footer" />
</body>
</html>

';

$htmlPage = str_replace('::body::', $loginContent, $htmlPage);
echo $htmlPage;

	
?>

