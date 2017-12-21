<?php

require_once('./include.php');
session_start();

$htmlPage =
'
<!DOCTYPE HTML>
<html>
<head>
	<title>Admin Page</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>
</head>

<body>
<div style="background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\'); height: 75px;">
	<div class="container" style="padding: 20px; margin-left:20px">
		<div class="dropdown">
			<button class="btn btn-primary dropdown-toggle" style="margin-bottom: 20px;" type="button" data-toggle="dropdown">Admin Options
			<span class="caret"></span></button>
			<ul class="dropdown-menu">
			<li><a href="admin_page.php">Home</a></li>
			<li><a href="admin_page.php?selection=1">Attendance Tracker</a></li>
			<li><a href="admin_page.php?selection=2">Setup Class</a></li>
			<li><a href="admin_page.php?selection=3">Manual Edit</a></li>
			<li><a href="admin_page.php?selection=4" style="border-top: solid black 3px; background-color:#ea4d31;"><b>Logout</b></a></li>
			</ul>
			<a style="margin-left:1.5%; font-size:30px; color:yellow;">University of Toledo</a>
		</div>
		
	</div>
</div>

<div style="margin: 3%; border-style: solid; border-width: 2px; height:100%">
	<div style="padding: 10px 15px 10px 15px;">
		::body::
	</div>
  
</div>

<div style="background-image:url(\'http://www.utoledo.edu/images/nav2015/header_bg.jpg\'); height: 60px;" />

</body>
</html>
';

$password_valid = $_SESSION['password_valid'];
$admin = $_SESSION['admin'];

if($password_valid == 1){
    if($admin == 1){
        //functions for selections
        function attendanceTracker($htmlPage){
            $replacement=
            '
			<p style="margin: 10px 0 0 10px; font-size:20px;">Please select a date</p>
			<form action="admin_page.php?selection=5" method="post"><br>
				<input type="text" name="class_name" placeholder="EECS 1000" style="margin:0 5px 5px 5px;" required>
				<input type="text" name="date" placeholder="12/31/2016" style="margin:0 5px 5px 5px;" required>
				<input type="submit">
			</form>
			';
            $htmlPage = str_replace('::body::', $replacement, $htmlPage);
            return $htmlPage;
        }
        
        function setupClass($htmlPage){
            $replacement=
                '
				<p style="font-size:20px;">Setup Class</p>
				<form action="admin_page.php?q=1" method="post">
					<div style="border-top: solid black 2px; margin: 0;" >
					
						<div style="margin:20px 0 0 20px;">
							Class Name<br>
							<div style="margin-top:10px;">
								<input type="text" name="class_name" placeholder="EECS 1000"; required>
							</div>
						</div>
						
						<div style="margin:20px 0 0 20px;">
						Class Range<br>
							<div style="margin-top:10px;">
								Start: <input type="text" name="start_date" placeholder="12/31/2016" style="margin-left:10px;" required>
								End: <input type="text" name="end_date" placeholder="12/31/2016" style="margin-left:10px;" required>
							</div>
						</div>
				
						<div style="margin:20px 0 0 20px;">
							Days of the week
							<input type="checkbox" name="check_list[]" value="Monday" style="margin-left:30px;">
							<input type="checkbox" name="check_list[]" value="Tuesday" style="margin-left:30px;">
							<input type="checkbox" name="check_list[]" value="Wednesday" style="margin-left:30px;">
							<input type="checkbox" name="check_list[]" value="Thursday" style="margin-left:30px;">
							<input type="checkbox" name="check_list[]" value="Friday" style="margin-left:30px;">
							<pre>                 M     T      W     Th     F</pre>
							<br>
						</div>
						
						<div style="margin:20px 0 0 20px;">
							Time Range<br>
							<div style="margin-top:10px;">
								Start: <input type="text" name="start_time" placeholder="8:00 am" style="margin-left:10px;" required>
								End: <input type="text" name="end_time" placeholder="3:00 pm" style="margin-left:10px;" required>
							</div>
						</div>
						<br>
						<input type="submit" style="margin:10px;">
					</div>
				</form>
			';
            $htmlPage = str_replace('::body::', $replacement, $htmlPage);
            return $htmlPage;
        }
        function test_input($data)
        {
            $data = trim($data);
            $data = stripslashes($data);
            $data = htmlspecialchars($data);
            return $data;
        }
		
		function generateDates($start_date, $end_date, $check_list){
			// Set timezone
			date_default_timezone_set('UTC');
			$date_string="";
			foreach($check_list as $check_value){
				
				$check_value = "next " . $check_value;
				$date = $start_date; //reset date
				
				//generate dates for days of week
				while (strtotime($date) <= strtotime($end_date)) {
					$date = date ("m/d/Y", strtotime($check_value, strtotime($date)));
					$date_string .= "`" . $date . "` INT NOT NULL, ";
				}
			}
			//$date_string = substr($date_string, 0, -2);
			return $date_string;
		}
        
        function logout(){
            session_unset();
            session_destroy();
            header('Location: logout.php');
			exit();
		}
        
        if(isset($_GET['selection'])){
			
            if($_GET['selection'] == 1)
                $htmlPage = attendanceTracker($htmlPage);
        
            elseif($_GET['selection'] == 2)
                $htmlPage = setupClass($htmlPage);
        
			//manual edit
            elseif($_GET['selection'] == 3){
				$replacement='
				<div class="container" style="padding: 20px; margin-left:20px">
					<div class="dropdown">
						<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Edit Options
						<span class="caret"></span></button>
						<ul class="dropdown-menu">
						<li><a href="admin_page.php?edit=1">Modify User Attendance</a></li>
						<li><a href="admin_page.php?edit=2">Delete A Class</a></li>
						<li><a href="admin_page.php?edit=3">See Users In Class</a></li>
						<li><a href="admin_page.php?edit=4">Admin Promotions</a></li>
						</ul>
					</div>
				</div>
				';
				$htmlPage = str_replace('::body::', $replacement, $htmlPage);
			}	
            elseif($_GET['selection'] == 4)
                logout();
			
			//for displaying the table that is generated
			elseif($_GET['selection'] == 5){
				//class for building the table displayed to admin
				class TableRows extends RecursiveIteratorIterator { 
					function __construct($it) { 
						parent::__construct($it, self::LEAVES_ONLY); 
					}
				
					function current() {
						return "<td style='width: 150px; border: 1px solid black; padding:5px;'>" . parent::current(). "</td>";
					} 
				} 
				
				//displaying users who attend class on a given day
				$table_body = '';
				$class_name_clear = str_replace(' ', '', test_input($_POST['class_name']));
				$date = str_replace(' ', '', test_input($_POST['date']));
				try {
					$query = 'SELECT Login_info.firstname, Login_info.lastname FROM Login_info INNER JOIN ' . $class_name_clear . ' ON Login_info.id=' . $class_name_clear . '.id WHERE ' . $class_name_clear . '.`' . $date . '`=1';
					$statement = $pdo->prepare($query); 
					$statement->execute();
				
					// set the resulting array to associative
					$result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
				
					$counter = 0;
					foreach(new TableRows(new RecursiveArrayIterator($statement->fetchAll())) as $k=>$v) { 
						if($counter == 1){
							$table_body .= $v . "</tr>";
							$counter = 0;
						}
						else{
							$table_body .= "<tr>" . $v;
							$counter += 1;
						}
					}
				}
				catch(PDOException $Exception) {
					echo "Error: " . $Exception->getMessage();
				}
				
				$replacement=
				'
					<p style="font-size:20px;">Attended Class</p>
					<table style=\'border: solid 1px black;\'>
						<tr><th style=\'padding:5px;\'>First Name</th><th style=\'padding:5px;\'>Last Name</th></tr>
						::table_body::
					</table>
				';
				
				$replacement = str_replace('::table_body::', $table_body, $replacement);
				$htmlPage = str_replace('::body::', $replacement, $htmlPage);
			}
            else;
        }
		//for when submitted to make new class
        elseif(isset($_GET['q'])){
        
            if($_GET['q'] == 1)
            {
				//find days of the week the class occurs on
                $days_of_week ='';
				if(!empty($_POST['check_list'])){
					foreach($_POST['check_list'] as $check_value){
						$days_of_week .= (" " . $check_value);
					}
				}
				else;
				$class_name_clear = str_replace(' ', '', test_input($_POST['class_name'])); //take out spaces
				
				//prep query for making a class's table
				$dates_string = generateDates(test_input($_POST['start_date']), test_input($_POST['end_date']), $_POST['check_list']);
				$query_string = "CREATE TABLE " . $class_name_clear . " (id INT(11), " . $dates_string . "PRIMARY KEY(id))";
				
				//statement for making class in Class table
				$class_name = $class_name_clear;
				$start_date = test_input($_POST['start_date']);
				$end_date = test_input($_POST['end_date']);
				$days_of_week = test_input($days_of_week);
				$start_time = test_input($_POST['start_time']);
				$end_time = test_input($_POST['end_time']);
				
				//put class into Classes table
				$statement = $pdo->prepare('INSERT INTO Classes (class_name, date_start, date_end, days_of_week, time_start, time_end, username) VALUES (:class_name, :date_start, :date_end, :days_of_week, :time_start, :time_end, :username)');
				
				$statement->bindParam(':class_name', $class_name);
				$statement->bindParam(':date_start', $start_date);
				$statement->bindParam(':date_end', $end_date);
				$statement->bindParam(':days_of_week', $days_of_week);
				$statement->bindParam(':time_start', $start_time);
				$statement->bindParam(':time_end', $end_time);
				$statement->bindParam(':username', $_SESSION['username']);
				
				$statement->execute();
				
				//statement for making a class's table
				$statement = $pdo->prepare($query_string);
				
				$statement->execute();
				
				$replacement='
				Class created!
				';
				$htmlPage = str_replace('::body::', $replacement, $htmlPage);
            }
        
            else;
        
        }
		elseif(isset($_GET['edit'])){
			//Modify User Attendance
			$replacement=
			'
			<div class="container" style="padding: 20px; margin-left:20px">
				<div class="dropdown">
					<button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">Edit Options
					<span class="caret"></span></button>
					<ul class="dropdown-menu">
					<li><a href="admin_page.php?edit=1">Modify User Attendance</a></li>
					<li><a href="admin_page.php?edit=2">Delete A Class</a></li>
					<li><a href="admin_page.php?edit=3">See Users In Class</a></li>
					<li><a href="admin_page.php?edit=4">Admin Promotions</a></li>
					</ul>
					
					<div>
						::manual_edit::
					</div>
				</div>
			</div>
			';
			
			if($_GET['edit'] == 1){
				if(isset($_POST['firstname'])){
					$class_name_clear = str_replace(' ', '', test_input($_POST['class_name']));
					$date_clear = str_replace(' ', '', test_input($_POST['date']));
					$firstname = str_replace(' ', '', test_input($_POST['firstname']));
					$lastname = str_replace(' ', '', test_input($_POST['lastname']));
					
					//get id
					$statement = $pdo->prepare('SELECT id FROM Login_info WHERE firstname=:firstname AND lastname=:lastname'); 
					
					$statement->bindParam(':firstname', $firstname);
					$statement->bindParam(':lastname', $lastname);
					
					$statement->execute();
					$results = $statement->fetch();
					
					//check if result was found.
					if(isset($results['id']))
					{
						$id = $results['id'];
						
						$query = "UPDATE " . $class_name_clear . " SET `" . $date_clear . "`=1 WHERE id=" . $id;
						$statement = $pdo->prepare($query); 
						$statement->execute();
						
						$manual_edit='
						Users attendence has been updated!
						';
						$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
					}
					else
					{
						$manual_edit='
						No result Found!
						';
						$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
					}
				}
				else{
					$manual_edit='
					<br>
					<form action="admin_page.php?edit=1" method="post">
						First Name: <input type="text" name="firstname" placeholder="John" style="margin:0 5px 5px 5px;" required>
						Last Name: <input type="text" name="lastname" placeholder="Doe" style="margin:0 5px 5px 5px;" required>
						Class Name: <input type="text" name="class_name" placeholder="EECS 1000" style="margin:0 5px 5px 5px;" required>
						Date: <input type="text" name="date" placeholder="12/31/2016" style="margin:0 5px 5px 5px;" required>
						<input class="input" type="submit">
					</form>
					';
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
			}
			//Delete A Class
            elseif($_GET['edit'] == 2)
                if(isset($_POST['class_name'])){
					$class_name_clear = str_replace(' ', '', test_input($_POST['class_name']));
					
					$query = 'DROP TABLE IF EXISTS ' . $class_name_clear;
					$statement = $pdo->prepare($query);
					$statement->execute();
					
					$query = 'DELETE FROM Classes WHERE class_name=\'' . $class_name_clear . '\'';
					$statement = $pdo->prepare($query);
					$statement->execute();
					
					$manual_edit = 'Class Deleted';
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
				else{
					$manual_edit='
					<p style="font-size:20px;">Delete A Class</p>
					<form action="admin_page.php?edit=2" method="post">
						Class Name: <input type="text" name="class_name" placeholder="EECS 1000" style="margin:0 5px 5px 5px;" required>
						<input class="input" type="submit">
					</form>
					';
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
			
			//See Users In Class
			elseif($_GET['edit'] == 3){
				if(isset($_POST['class_name'])){
					//class for building the table displayed users admin
					class TableRows extends RecursiveIteratorIterator { 
						function __construct($it) { 
							parent::__construct($it, self::LEAVES_ONLY); 
						}
					
						function current() {
							return "<td style='width: 150px; border: 1px solid black; padding:5px;'>" . parent::current(). "</td>";
						} 
					} 
					
					//displaying users who are in set class
					$table_body = '';
					$class_name_clear = str_replace(' ', '', test_input($_POST['class_name']));
					try {
						$query = 'SELECT Login_info.firstname, Login_info.lastname FROM Login_info INNER JOIN ' . $class_name_clear . ' ON Login_info.id=' . $class_name_clear . '.id';
						$statement = $pdo->prepare($query); 
						$statement->execute();
					
						// set the resulting array to associative
						$result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
					
						$counter = 0;
						foreach(new TableRows(new RecursiveArrayIterator($statement->fetchAll())) as $k=>$v) { 
							if($counter == 1){
								$table_body .= $v . "</tr>";
								$counter = 0;
							}
							else{
								$table_body .= "<tr>" . $v;
								$counter += 1;
							}
						}
					}
					catch(PDOException $Exception) {
						echo "Error: " . $Exception->getMessage();
					}
					
					$manual_edit=
					'
						Attended Class
						<table style="border: solid 1px black;">
							<tr><th style=\'padding:5px;\'>First Name</th><th style=\'padding:5px;\'>Last Name</th></tr>
							::table_body::
						</table>
					';
					
					$manual_edit = str_replace('::table_body::', $table_body, $manual_edit);
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
				else{
					$manual_edit='
					<p style="font-size:20px;">See Users In Class</p>
					<form action="admin_page.php?edit=3" method="post">
						Class Name: <input type="text" name="class_name" placeholder="EECS 1000" style="margin:0 5px 5px 5px;" required>
						<input class="input" type="submit">
					</form>
					';
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
			}
				
			//Admin Promotions
			elseif($_GET['edit'] == 4)
				if(isset($_POST['firstname'])){
					$firstname = str_replace(' ', '', test_input($_POST['firstname']));
					$lastname = str_replace(' ', '', test_input($_POST['lastname']));
					
					$query = 'UPDATE Login_info SET admin=1 WHERE firstname=:firstname AND lastname=:lastname';
					$statement = $pdo->prepare($query);
					$statement->bindParam(':firstname', $firstname);
					$statement->bindParam(':lastname', $lastname);
					$statement->execute();
					
					$manual_edit = test_input($_POST['firstname']) . ' ' . test_input($_POST['lastname']) . ' has been promoted to admin!';
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
				else{
					$manual_edit='
					<p style="font-size:20px;">Promote User to Admin</p>
					<form action="admin_page.php?edit=4" method="post">
						First Name: <input type="text" name="firstname" placeholder="John" style="margin:0 5px 5px 5px;" required>
						Last Name: <input type="text" name="lastname" placeholder="Doe" style="margin:0 5px 5px 5px;" required>
						<input class="input" type="submit">
					</form>
					';
					$replacement = str_replace('::manual_edit::', $manual_edit, $replacement);
				}
				
			
			$htmlPage = str_replace('::body::', $replacement, $htmlPage);
		}
        else{
			//for defualt login screen for admin, display classes
            //class for building the table displayed to admin
				class TableRows extends RecursiveIteratorIterator { 
					function __construct($it) { 
						parent::__construct($it, self::LEAVES_ONLY); 
					}
				
					function current() {
						return "<td style='width: 150px; border: 1px solid black; padding:5px;'>" . parent::current(). "</td>";
					}
				} 

				$table_body = '';
				//query to find classes that user made
				try {
					$statement = $pdo->prepare('SELECT class_name FROM Classes WHERE username=:username');
					$statement->bindParam(':username', $_SESSION['username']);
					$statement->execute();
				
					// set the resulting array to associative
					$result = $statement->setFetchMode(PDO::FETCH_ASSOC); 
				
					foreach(new TableRows(new RecursiveArrayIterator($statement->fetchAll())) as $k=>$v) { 
							$table_body .= "<tr>" . $v . "</tr>";
					}
				}
				catch(PDOException $Exception) {
					echo "Error: " . $Exception->getMessage();
				}
				
				$replacement=
				'
					<table style=\'border: solid 1px black;\'>
						<tr><th style=\'padding:5px;\'>Your Classes</th></tr>
						::table_body::
					</table>
				';
				
				$replacement = str_replace('::table_body::', $table_body, $replacement);
				$htmlPage = str_replace('::body::', $replacement, $htmlPage);
        }
		
		echo $htmlPage;
    }
    //for admin
    else;
}
//for if logged in
else
{
    $loginContent=
    '
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
				<form action = "index.php">
				Access Denied<br>
				Please return to login page<br>
				<input type="submit">
				</form>
			</div>
		</div>
		<div class="footer" />

		</body>
		</html>
	';
    echo $loginContent;
    session_unset();
    session_destroy();
    exit();
}
?>