<?php
include('connection.php');
$initialUrl = 'https://au2api.jobadder.com/v2/candidates?limit=1000&offset=41000';



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
    
    $response_data = json_decode($response, true);
    echo "<pre>";
  //  print_r($response_data);

    foreach ($response_data['items'] as $candidate) {
        echo "<pre>";
        print_r($candidate);
       die();
        $firstName = $candidate['firstName'];
         $lastName = $candidate['lastName'];
        
        // Remove special characters using str_replace
        $special_characters = array("'", "(", ")", "-", "_"); // Add other special characters as needed
        $cleaned_name = str_replace($special_characters, '', $lastName);
        $cleaned_fname = str_replace($special_characters, '', $firstName);
        
        
        $name = $cleaned_fname . ' ' . $cleaned_name;
        $mobile = $candidate['mobile'];
      
        $checkQuery = "SELECT * FROM `candidate_list` WHERE candidate_name = '$name' AND candidate_mobile = '$mobile'";
        $result = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($result) == 0) {
            $insertQuery = "INSERT INTO `candidate_list` (candidate_name, candidate_mobile) VALUES ('$name', '$mobile')";
            mysqli_query($conn, $insertQuery);
           // echo "Record inserted: Name=$name, Mobile=$mobile<br>";
        } else {
           // echo "Record already exists: Name=$name, Mobile=$mobile<br>";
        }
     }
     
        

?>
