<?php
include('connection.php');

if($_POST['action'] == 'fetchChat'){
    $phoneNumber = $_POST['phoneNumber'];
     $select = $conn->query("SELECT * FROM `SMS_history` WHERE `to_number`='$phoneNumber'");
    if ($select->num_rows > 0) {
        $getMsg = mysqli_fetch_assoc($select);
        $msgs = json_decode($getMsg['message'], true);
        $html = '';
        foreach($msgs as $msgData){
            if($msgData['type'] == 'admin'){
                $html .= '<div class="container">
            		  <img src="https://www.w3schools.com/w3images/bandmember.jpg" alt="Avatar" style="width:100%;">
            		  <p>'.$msgData['message_text'].'</p>
            		  <span class="time-right">'.$msgData['time'].'</span>
            		</div>';
            }elseif($msgData['type'] == 'user'){
                $html .= '<div class="container darker">
                		  <img src="https://www.w3schools.com/w3images/bandmember.jpg" alt="Avatar" class="right" style="width:100%;">
                		  <p>'.$msgData['message_text'].'</p>
                		  <span class="time-left">'.$msgData['time'].'</span>
                		</div>';
            }
        }
        echo $html;
    }else{
        echo 'No messsage available';
    }
}else if($_POST['action'] == 'sendSMS'){
   // print_r($_POST);
    $account_sid = '';
    $auth_token  = '';
    $twilio_phone_number = ''; 
    
    $tophone = $_POST['phoneNumber'];
    $message = $_POST['typeSMS'];
    
    $date = date("Y-m-d");
    $time = date("H:i:s");

    $curl = curl_init();

    curl_setopt_array($curl, array(
        CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/{$account_sid}/Messages.json", // Include Account SID
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'Body=' . urlencode($message) . '&MessagingServiceSid=&To=' . urlencode($tophone) . '',
        CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded',
            'Authorization: Basic ' . base64_encode("{$account_sid}:{$auth_token}")
        ),
    ));

    $response = curl_exec($curl);

    if (curl_errno($curl)) {
        echo 'Curl error: ' . curl_error($curl);
    }

    curl_close($curl);
    $response_data = json_decode($response, true);

  // echo "<pre>";
   //print_r($response_data); 
   // $response_data['status'] = 'accepted';
    if ($response_data['status'] == 'accepted') {
        echo $status = 'sent';
    } else {
        echo $status = 'failed';
    }
    
    $messages =  array(
        'message_id' => 'msg_'.uniqid(),
        'message_text' => $message,
        'time' => $time,
        'date' => $date,
        'type' => 'admin',
        'msg_status'=>$status    
    );

    $select = $conn->query("SELECT * FROM `SMS_history` WHERE `to_number`='$tophone'");
    if ($select->num_rows > 0) {

        $getMsg = mysqli_fetch_assoc($select);
        $msgs = json_decode($getMsg['message'], true); 

        array_push($msgs, $messages);

        $encoded_msgs = json_encode($msgs); 
        $query = "UPDATE SMS_history SET message=? WHERE to_number=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $encoded_msgs, $tophone);

        if ($stmt->execute()) {
            //echo "Updated successfully";
        } else {
            //echo "Not updated: " . $stmt->error;
        }

        $stmt->close();
    } else {

        $array = json_encode(array($messages));
        
        $query = "INSERT INTO SMS_history(from_number, to_number, message, sms_status, sms_date, sms_time) VALUES ('$twilio_phone_number', '$tophone', '$array', '$status', '$date', '$time')";
    
        $stmt = $conn->prepare($query);
    
        if ($stmt->execute()) {
            //echo "Inserted successfully";
        } else {
           // echo "Not inserted: " . $stmt->error;
        }

        $stmt->close();
    }   
}else if($_POST['action'] == 'bulkSMS'){
    
    
    $message = $_POST['message'];
    $tophone = explode(',',$_POST['phoneNumbers']);
    $account_sid = '';
    $auth_token = '';
    $twilio_phone_number = ''; 
    $date = date("Y-m-d");
    $time = date("H:i:s");
    
    $getStatus = [];
    foreach ($tophone as $tophones) {

        // Trim each phone number to remove leading/trailing spaces
         $tophone = trim($tophones);
    
         $curl = curl_init();
    
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.twilio.com/2010-04-01/Accounts/{$account_sid}/Messages.json", // Include Account SID
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => 'Body=' . urlencode($message) . '&MessagingServiceSid=&To=' . urlencode($tophone) . '',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/x-www-form-urlencoded',
                'Authorization: Basic ' . base64_encode("{$account_sid}:{$auth_token}")
            ),
        ));
    
        $response = curl_exec($curl);
    
        if (curl_errno($curl)) {
            echo 'Curl error: ' . curl_error($curl);
        }
    
        curl_close($curl);  
        $response_data = json_decode($response, true);
    
        //$response_data['status'] = 'accepted';
        if ($response_data['status'] == 'accepted') {
            $status = 'sent';
            echo '<i class="fa fa-check" style="color:green;"></i> Message Delivered on ' . $tophone .' ('.$time.')</br>';
        } else {
            echo '<i class="fa fa-close" style="color:red;"></i> Message Failed on ' . $tophone .' ('.$time.')</br>';
            $status = 'failed';
        }
        
        $messages =  array(
            'message_id' => 'msg_'.uniqid(),
            'message_text' => $message,
            'time' => $time,
            'date' => $date,
            'type' => 'admin',
            'msg_status'=>$status    
        );

        $select = $conn->query("SELECT * FROM `SMS_history` WHERE `to_number`='$tophone'");
        if ($select->num_rows > 0) {
          
            $getMsg = mysqli_fetch_assoc($select);
           
            $msgs = json_decode($getMsg['message'], true); 
     
            array_push($msgs, $messages);
           
            $encoded_msgs = json_encode($msgs);
            $query = "UPDATE SMS_history SET message='$encoded_msgs' WHERE to_number='$tophone'";
            $stmt = $conn->query($query);

        } else {
           
            $array = json_encode(array($messages));
            
            
           $query = "INSERT INTO SMS_history(from_number, to_number, message, sms_status, sms_date, sms_time) VALUES ('$twilio_phone_number', '$tophone', '$array', '$status', '$date', '$time')";
        
           $stmt = $conn->query($query);
        

        }
    }
    
    
}else if($_POST['action'] == 'candidateSMS'){

    print_r($_POST);

}

    

?>