<?php

require_once('./include.php');

function test_input($data)
{
	$data = trim($data);
	$data = stripslashes($data);
	$data = htmlspecialchars($data);
	return $data;
}

function exception(){
	$htmlPage='
			<!DOCTYPE HTML>
			<html>
            <head>
            	<title>New User</title>
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
            	background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\');
            	height: 40px;
            	font-size: 30px;
            	color: yellow;
             }
              
            .content{
            	border: 2px solid black;
            	margin: 30px;
				min-width: 350px;
            }
            
            .contentinside{
            	font-size: 180%;
            }
            
            .footer{
            	background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\');
            	height: 60px;
             }
            </style>
            
            <div class="topbar">
            	<a style="margin-left:1.5%;">University of Toledo</a>
            </div>
            
            <div class="content">
            	<div class="contentinside">
            		<form action="new_account.php?s=1" method="post" style="margin-left: 10px">
            			Something went wrong...please check entered info.<br>
            			First Name: <input type="text" name="firstname" placeholder="John" style="margin:0 5px 5px 5px;" required><br>
            			Last Name: <input type="text" name="lastname" placeholder="Doe" style="margin:0 5px 5px 5px;" required><br>
            			Username: <input type="text" name="username" placeholder="Username" style="margin:0 5px 5px 5px;" required><br>
            			Email: <input type="text" name="email" placeholder="Email" style="margin:0 5px 5px 5px;" required><br>
            			Password: <input type="password" name="password" placeholder="Password" style="margin:0 5px 5px 5px;" required><br>
            			Class Name: <input type="text" name="class_name" placeholder="EECS 1000" style="margin:0 5px 5px 5px;" required><br>
            			<input class="input" type="submit">
            		</form>
            	</div>
            </div>
            
            <div class="footer" />
            
            </body>
            </html>
		';
		echo $htmlPage;
		exit();
}
	
if($_GET['s'] == 1){
	$firstname = test_input($_POST['firstname']);
	$lastname = test_input($_POST['lastname']);
	$username = test_input($_POST['username']);
	$email = test_input($_POST['email']);
	$password = password_hash(test_input($_POST['password']), PASSWORD_DEFAULT);
	$class = str_replace(' ', '', test_input($_POST['class_name']));
	//try to make new user
	try{
	//add user to userbase
	$statement = $pdo->prepare('INSERT INTO Login_info (username, password, email, class, firstname, lastname) VALUES (:username, :password, :email, :class, :firstname, :lastname)');
	
	$statement->bindParam(':firstname', $firstname);
	$statement->bindParam(':lastname', $lastname);
    $statement->bindParam(':username', $username);
    $statement->bindParam(':password', $password);
    $statement->bindParam(':email', $email);
	$statement->bindParam(':class', $class);
    
    $statement->execute();
	
	//retrive user id
	$statement = $pdo->prepare('SELECT * from Login_info where username = :username');
	$statement->bindParam(':username', $username);
	$statement->execute();
	$results = $statement->fetch();
	
	$id = $results['id'];
	
	//add user to class
	$query = "INSERT INTO " . $class . " (id) VALUES (" . $id . ")";
	$statement = $pdo->prepare($query);
	
	$statement->execute();
	}
	catch(PDOException $Exception){
		exception();
	}
	
	$htmlPage='
	<!DOCTYPE HTML>
			<html>
            <head>
            	<title>New User</title>
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
            	background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\');
            	height: 40px;
            	font-size: 30px;
            	color: yellow;
             }
              
            .content{
            	border: 2px solid black;
            	margin: 30px;
				min-width: 350px;
            }
            
            .contentinside{
            	font-size: 180%;
            }
            
            .footer{
            	background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\');
            	height: 60px;
             }
            </style>
            
            <div class="topbar">
            	<a style="margin-left:1.5%;">University of Toledo</a>
            </div>
            
            <div class="content">
            	<div class="contentinside">
					<form action="index.php">
						User ' . htmlentities($username) . ' has been created!<br>
						Please return to the login page <br>
						<input class="input" type="submit">
					</form>
            	</div>
            </div>
            
            <div class="footer" />
            
            </body>
            </html>
	';
}
else{
	$htmlPage='
	<!DOCTYPE HTML>
	<hml>
       <head>
       	<title>New User</title>
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
       	background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\');
       	height: 40px;
       	font-size: 30px;
       	color: yellow;
        }
         
       .content{
       	border: 2px solid black;
       	margin: 30px;
		min-width: 350px;
       }
       
       .contentinside{
       	font-size: 180%;
       }
       
       .footer{
       	background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\');
       	height: 60px;
        }
       </style>
       
       <div class="topbar">
       	<a style="margin-left:1.5%;">University of Toledo</a>
       </div>
       
       <div class="content">
       	<div class="contentinside">
       		<form action="new_account.php?s=1" method="post" style="margin-left: 10px">
       			First Name: <input type="text" name="firstname" placeholder="John" style="margin:0 5px 5px 5px;" required><br>
       			Last Name: <input type="text" name="lastname" placeholder="Doe" style="margin:0 5px 5px 5px;" required><br>
       			Username: <input type="text" name="username" placeholder="Username" style="margin:0 5px 5px 5px;" required><br>
       			Email: <input type="text" name="email" placeholder="Email" style="margin:0 5px 5px 5px;" required><br>
       			Password: <input type="password" name="password" placeholder="Password" style="margin:0 5px 5px 5px;" required><br>
       			Class Name: <input type="text" name="class_name" placeholder="EECS 1000" style="margin:0 5px 5px 5px;" required><br>
       			<input class="input" type="submit">
       		</form>
       	</div>
       </div>
       
       <div class="footer" />
       
       </body>
       </html>
	';
}
echo $htmlPage;
?>

