<?php

    $spl = SELECT * FROM course;
    
    $db_server = "localhost";
		$db_name = "course_management_t";
		$db_user = "root";
		$db_passwd = "fdd396906f5054060122311cf8b0eb2da0cfe7a437501152";
		
		$con=mysqli_connect($db_server, $db_user, $db_passwd) or die("資料庫登入錯誤");
		if(mysqli_connect_errno($con)){
			echo "ERROR1";
		}
		
		print_r(con);
		mysqli_query($con,"SET NAMES utf8");
		mysqli_select_db($con,$db_name) or die("資料庫連結錯誤");
		
		$row = mysqli_query($con,$sql);
		mysqli_close($con);
		return $row;

?>
