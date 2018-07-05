<?php
	$json_str = file_get_contents('php://input'); //接收request的body
	$json_obj = json_decode($json_str); //轉成json格式
	echo "CC";
	$myfile = fopen("log.txt", "w+") or die("Unable to open file!"); //設定一個log.txt來印訊息
	fwrite($myfile, "\xEF\xBB\xBF"."hello"); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
	
	$sender_userid = $json_obj->events[0]->source->userId; //取得訊息發送者的id
	$sender_txt = $json_obj->events[0]->message->text; //取得訊息內容
	$sender_replyToken = $json_obj->events[0]->replyToken; //取得訊息的replyToken
	$sender_type = $json_obj->events[0]->type; //取得訊息的type
	
	echo "CC".$sender_userid.$sender_txt.$myfile;

	if($sender_type == "postback"){ //訊息的type為postback(選單)
		$postback_data = $json_obj->events[0]->postback->data; //取得postback的data
		if($postback_data == "applyCourse"){ //postback的data是applyCourse的話，產生申請課程的postback json檔
			$response = array (
				"replyToken" => $sender_replyToken,
				"messages" => array (
			      		apply()
			    	)
			);
		}
	} else {
		//若訊息type不為postback，代表是剛開始與linebot對談，產生「歡迎來到龍鳳行銷」的postback
		$response = array (
			"replyToken" => $sender_replyToken,
			"messages" => array (
			      welcome()
			    )
		);
	}
	//fwrite($myfile, "\xEF\xBB\xBF".json_encode($response)); //在字串前面加上\xEF\xBB\xBF轉成utf8格式
	$header[] = "Content-Type: application/json";
	//輸入line 的 Channel access token
	$header[] = "Authorization: Bearer m6MPT8cTtAs+flzA0qnYSoTH+9yhHjlJNAwjpnLHD8+VUv08fEhMgMfwiDPYqpCkADPD1dW6RO8eA4WCRyEZ0TSJURNCniQ16jY/71L/RpRlbGZFUkQsximuFeBSqIyjY8QLG9hAMs+o/FaczDA9rgdB04t89/1O/w1cDnyilFU=";
	$ch = curl_init("https://api.line.me/v2/bot/message/reply");
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($response));                                                                  
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);                                                                      
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);                                                                                                   
	$result = curl_exec($ch);
	curl_close($ch);
	
	
	
  
	function welcome(){
		$json = '{
		  "type": "template",
		  "altText": "this is a buttons template",
		  "template": {
			"type": "buttons",
			"actions": [
			  {
				"type": "postback",
				"label": "報名課程",
				"data": "applyCourse"
			  },
			  {
				"type": "postback",
				"label": "我的課程",
				"data": "myCourse"
			  }
			],
			"title": "歡迎來到龍鳳行銷",
			"text": "您可以報名新課程或查看已報名的課程"
		  }
		}';
		return json_decode($json);
	}
	
	function apply(){
		$json_str = '{
  			"type": "template",
  			"altText": "this is a carousel template",
  			"template": {
				"type": "carousel",
				"columns": []
  			}
		}';
		$json = json_decode($json_str);
		$sql = "SELECT * FROM course";
		$result = sql_select_fetchALL($sql);
		$course_name = "";
		foreach($result as $a){
			$course_obj = array (
				"title" => $a['course_name'],
				"text" => $a['course_name'],
				"actions" => array (
					array (
						"type" => "postback",
						"label" => "課程報名",
						"data" => "apply=".$a['course_id']
					),
					array (
						"type" => "postback",
						"label" => "課程介紹",
						"data" => "intro=".$a['course_id']
					)
				)
		      	);
			$json -> template -> columns[] = $course_obj;
		}
		return $json;
	}



	function sql_select_fetchALL($sql)
	{ 
		$db_server = "localhost";
		$db_name = "dpcleader_course";
		$db_user = "dpcleader_sup";
		$db_passwd = "xxyoKcK?MhX}_Ut&L]";
		
		$con=mysqli_connect($db_server, $db_user, $db_passwd) or die("資料庫登入錯誤".mysqli_connect_errno().mysqli_connect_error());
		if(mysqli_connect_errno($con)){
			echo "ERROR1";
		}
		mysqli_query($con,"SET NAMES utf8");
		mysqli_select_db($con,$db_name) or die("資料庫連結錯誤");
		
		$row = mysqli_query($con,$sql);
		mysqli_close($con);
		echo "完成資料庫登入與連接！";
		return $row;
	}
?>
