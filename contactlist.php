<?php
include('connection.php');
$initialUrl = 'https://au2api.jobadder.com/v2/contacts?limit=1000&offset=5000';

$curl = curl_init();

curl_setopt_array($curl, array(
    CURLOPT_URL => $initialUrl,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => '',
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => 'GET',
    CURLOPT_HTTPHEADER => array(
        'Authorization: Bearer 91b67a17516c0949daf74a4d2091dc13'
    ),
));

$response = curl_exec($curl);

curl_close($curl);

$response_data = json_decode($response, true);
  echo "<pre>";
  print_r($response_data);


if (isset($response_data['items'])) {
    foreach ($response_data['items'] as $contact) {
      
      $firstName = $contact['firstName'];
      $lastName = $contact['lastName'];
      
       $special_characters = array("'", "(", ")", "-", "_"); 
        $cleaned_lname = str_replace($special_characters, '', $lastName);
        $cleaned_fname = str_replace($special_characters, '', $firstName);

        $name = $cleaned_fname . ' ' . $cleaned_lname;
        $mobile = $contact['mobile'];
        $phone = $contact['phone'];
        
        if (!empty($mobile)) {
            $storedData = $mobile;
        } elseif (!empty($phone)) {
            $storedData = $phone;
        } else {
            $storedData = $phone;
        }
                
        
        $checkQuery = "SELECT * FROM `contact_list` WHERE contact_name = '$name' AND contact_mobile = '$storedData'";
        $result = mysqli_query($conn, $checkQuery);
        
        
        if (mysqli_num_rows($result) == 0) {
            $insertQuery = "INSERT INTO `contact_list` (contact_name, contact_mobile) VALUES ('$name', '$storedData')";
            mysqli_query($conn, $insertQuery);
          //  echo "Record inserted: Name=$name, phone=$storedData<br>";
        } else {
          //  echo "Record already exists: Name=$name, phone=$storedData<br>";
        }
     }
        
}

       

?>

