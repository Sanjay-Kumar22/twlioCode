<?php 
include('connection.php');
file_put_contents('twilioResponse.txt',json_encode($_POST));
$data  = json_decode(file_get_contents('twilioResponse.txt'));
//echo '<pre>';
//print_r($data);


    $date = date("Y-m-d");
    $time = date("H:i:s"); 
    $body = $data->Body;
    $tophone = $data->To;
    $from = $data->From;
    $status= $data->SmsStatus;     


    $messages =  array(
        'message_id' => 'msg_'.uniqid(),
        'message_text' => $body,
        'time' => $time,
        'date' => $date,
        'type' => 'user',
        'msg_status'=>$status    
    );
    $select = $conn->query("SELECT * FROM `SMS_history` WHERE `to_number`='$tophone'");
    if ($select->num_rows > 0) {
        $getMsg = mysqli_fetch_assoc($select);
        $msgs = json_decode($getMsg['message'], true); // Add true to decode as an associative array

        array_push($msgs, $messages);
        print_r($msgs);

        $encoded_msgs = json_encode($msgs); // Convert back to JSON after modification
        $query = "UPDATE SMS_history SET message=? WHERE to_number=?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("ss", $encoded_msgs, $tophone);

        if ($stmt->execute()) {
            echo "Updated successfully";
        } else {
            echo "Not updated: " . $stmt->error;
        }

        $stmt->close();
    }else{
        $array = json_encode(array($messages));
        print_r($array);
        $query = "INSERT INTO SMS_history(from_number, to_number, message, sms_status, sms_date, sms_time) VALUES ('$from', '$tophone', '$array', '$status', '$date', '$time')";
    
        $stmt = $conn->prepare($query);
    
        if ($stmt->execute()) {
            echo "Inserted successfully";
        } else {
            echo "Not inserted: " . $stmt->error;
        }
    }