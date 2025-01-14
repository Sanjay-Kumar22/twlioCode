
<?php

$servername= "localhost";
$username= "root";
$password="";
$database= "twilio";

$conn = mysqli_connect($servername,$username,$password,$database);

if($conn){
    echo "";
} else {
    echo " connection fail".mysqli_connect_error($conn);
}

?>