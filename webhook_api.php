<?php

$channelAccessToken = "KXk24X9A1zVRzd7VPjj5xUVQaif049AW/9K+qoIt64hdbbxbpK7k3fEapYFagg6LCcpkYLijqUUC+Ixyajmyu7r0i+XBqfz9oeoTwz5c1E4DAZElpKu6GT1zJ83+x3oUoB9rQpjU/xgkdFk1ES7o3wdB04t89/1O/w1cDnyilFU="; // Access Token ค่าที่เราสร้างขึ้น

$request = file_get_contents('php://input');   // Get request content

$request_json = json_decode($request, true);   // Decode JSON request

foreach ($request_json['events'] as $event)
{
	if ($event['type'] == 'message') 
	{
		if($event['message']['type'] == 'text')
		{
			$text = $event['message']['text'];
			
			$texts = explode(" ", $text);
			
			if($text == "@บอท" || $texts[0] == "@บอท"){
				$reply_message .= "ฉันมีบริการให้คุณสั่งได้ ดังนี้...\n";
				$reply_message .= "พิมพ์ว่า \"แสดงรายชื่อผู้เข้ารับการอบรม หลักสูตร Chatbot-Ai\"\n";
				$reply_message .= "พิมพ์ว่า \"ขายสุรายาสูบและไพ่\"\n";
				$reply_message .= "พิมพ์ว่า \"พิกัดอัตราภาษีสรรพสามิต\"\n";
				$reply_message .= "พิมพ์ว่า \"อัตราภาษีสรรพสามิตรถยนต์\"\n";
				$reply_message .= "พิมพ์ว่า \"ขั้นตอนการขอใบอนุญาตขายสุรา\"\n";
				$reply_message .= "พิมพ์ว่า \"กำหนดประเภทสินค้าตามพิกัดอัตราภาษีสรรพสามิต (ฉบับที่ 2) พ.ศ. 2564\"\n";
				$reply_message .= "พิมพ์ว่า \"การต่อใบอนุญาตขายสุรา ยาสูบ ไพ่ (สำหรับรายเดิม)\"\r\n";
			}
			if($text == "พิกัดอัตราภาษีสรรพสามิต"){
				$reply_message = mySQL_selectAll('http://bot.kantit.com/json_select_users.php');
			}else if($text == "ขายสุรายาสูบและไพ่"){
				$reply_message = "http://www.kantit.com/download/excise.jpg";
			}else if($text == "แสดงรายชื่อผู้เข้ารับการอบรม หลักสูตร Chatbot-Ai" || 
				 $text == "แสดงรายชื่อผู้เข้ารับการอบรม" ||
				 $text == "แสดงรายชื่อผู้อบรม" ||
				 $text == "รายชื่ออบรม" ||
				 $text == "รายชื่อผู้เข้ารับการอบรม" ||
				 $text == "แสดงรายชื่อ" ||
				 $text == "ขอรายชื่อผู้เข้ารับการอบรม" ||
				 $text == "ขอรายชื่อผู้อบรม" ||
				 $text == "ผู้เข้ารับการอบรม"){
				$reply_message = mySQL_selectAll('http://bot.kantit.com/json_select_users.php');
			}else if($text == "อัตราภาษีสรรพสามิตรถยนต์"){
				$reply_message = mySQL_selectAll('http://bot.kantit.com/json_select_users.php?sid='.$texts[1]);
			}else if($text == "ขั้นตอนการขอใบอนุญาตขายสุรา"){
				$reply_message = mySQL_selectFTP('http://bot.kantit.com/json_select_ftp.php?sid='.$texts[1]);					
			}else if($text == "กำหนดประเภทสินค้าตามพิกัดอัตราภาษีสรรพสามิต (ฉบับที่ 2) พ.ศ. 2564"){
				$reply_message = "https://webdev.excise.go.th/act2560/ministerial-regulations/685-2-256";
			}else if($text == "การต่อใบอนุญาตขายสุรา ยาสูบ ไพ่ (สำหรับรายเดิม)" ||
				$text == "การต่อใบอนุญาตขายสุรา" ||
				$text == "การต่อใบอนุญาตขายยาสูบ" || 
				$text == "การต่อใบอนุญาตขายไพ่" ||
				$text == "สุรา" ||
				$text == "ยาสูบ" ||
				$text == "ไพ่"){
				$reply_message = "https://www.excise.go.th/cs/groups/public/documents/document/dwnt/nde1/~edisp/uatucm415648.jpg";
			}
			
		} else {
			//$reply_message = 'ฉันได้รับ "'.$event['message']['type'].'" ของคุณแล้ว!';
		}		
	} else {
		$reply_message = 'ฉันได้รับ Event "' . $event['type'] . '" ของคุณแล้ว!';
	}
	
	//if($reply_message == null || $reply_message == ""){ $reply_message =  'ขออภัยฉันไม่สามารถตอบกลับข้อความ "'. $text . '" ของคุณ!'; }
		
	// reply message
	$post_header = array('Content-Type: application/json', 'Authorization: Bearer ' . $channelAccessToken);	
	
	$data = ['replyToken' => $event['replyToken'], 'messages' => [['type' => 'text', 'text' => $reply_message]]];	
	
	$post_body = json_encode($data);	
	
	// reply method type-1 vs type-2
	$send_result = reply_message_1('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);
	//$send_result = reply_message_2('https://api.line.me/v2/bot/message/reply', $post_header, $post_body);	
}

function mySQL_selectAll($url)
{
	$result = file_get_contents($url);
	
	$result_json = json_decode($result, true); //var_dump($result_json);
	
	$data = "ผลลัพธ์:\r\n";
		
	foreach($result_json as $values) {
		$data .= $values["user_stuid"] . " " . $values["user_firstname"] . " " . $values["user_lastname"] . "\r\n";
	}
	
	return $data;
}

function mySQL_selectFTP($url)
{
	$result = file_get_contents($url);
	
	$result_json = json_decode($result, true); //var_dump($result_json);
	
	$data = "ผลลัพธ์:\r\n";
		
	foreach($result_json as $values) {
		$data .= $values["user_password"] . "\r\n";
	}
	
	return "รหัส FTP ของคุณคือ ".$data;
}

function mySQL_SET($url)
{
	$result = file_get_contents($url);
	
	$result_json = json_decode($result, true); //var_dump($result_json);
	
	$data = "ผลลัพธ์:\r\n";
		
	foreach($result_json as $values) {
		$data .= $values["results"] . "\r\n";
	}
	
	return "cmd=".$data;
}


function reply_message_1($url, $post_header, $post_body)
{
        $context = stream_context_create([
            'http' => [
                'method' => 'POST',
                'header' => $post_header,
                'content' => $post_body,
            ],
        ]);
	
	$result = file_get_contents($url, false, $context);
	
	return $result;
}

function reply_message_2($url, $post_header, $post_body)
{
	$ch = curl_init($url);	
	curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $post_header);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $post_body);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	
	$result = curl_exec($ch);
	
	curl_close($ch);
	
	return $result;
}

?>
