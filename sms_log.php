<?php

session_start();
if(!empty($_SESSION['login_email'])){
include('connection.php');

$curl = curl_init();

curl_setopt_array($curl, array(
  CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts//Messages.json',
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => '',
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 0,
  CURLOPT_FOLLOWLOCATION => true,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => 'GET',
  CURLOPT_POSTFIELDS => 'To=$phone&From=%2B19386665461&Body=hello',
  CURLOPT_HTTPHEADER => array(
    'Content-Type: application/x-www-form-urlencoded',
    'Authorization: Basic =='
  ),
));

$response = curl_exec($curl);

curl_close($curl);
$response_data = json_decode($response, true);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Twilio Messages</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.10.22/css/jquery.dataTables.min.css" />
</head>
<style>
table {
  width: 100%;
  border-collapse: collapse;
   }
 tbody {
    display: block;
    max-height: 615px; 
    overflow-y: auto;
    width: 100%;
  }

 tr {
   display: flex;
 }

td, th {
  flex: 1;
  text-align: left;
 }
 
 .row.justify-content-center {
   border: 1px solid;
   border-radius: 10px;
    }
    
#example_wrapper{
    padding:10px;
  }    
    
</style>
<body>

<div class="container-fluid">
  <div class="row">
    <!-- Sidebar -->
    <?php include('navbar.php'); ?>
    <!-- Main Content -->
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4" id="content">
      <?php include('header.php'); ?>
      <div class="container mt-5">
        <div class="row justify-content-center">
          <div class="col-md-12" style = { padding :10px}>
            <table id="example" class="display" >
                <thead>
              <tr>
                <th>From</th>
                <th>To</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th>Time</th>
              </tr>
              </thead>
              <tbody> 
              <?php
              if (isset($response_data['messages'])) {
                foreach ($response_data['messages'] as $message) {
                  echo "<tr>";
                  echo "<td>" . $message['from'] . "</td>";
                  echo "<td>" . $message['to'] . "</td>";
                  echo "<td>" . $message['body'] . "</td>";
                  echo "<td>" . $message['status'] . "</td>";
                  
                  // Inserting current date and time
                 
                  $timestamp = strtotime($message['date_sent']);
                  echo "<td>" . date('Y-m-d', $timestamp) . "</td>";
                  echo "<td>" . date('H:i:s', $timestamp) . "</td>";

                  
                  echo "</tr>";
                }
              } 
              ?>
              </tbody> 
            </table>
          </div>
        </div>
      </div>
    </main>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

 <script>
  $(document).ready(function() { 
  $('#example').DataTable({
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      ordering: false,
      "lengthMenu": [[100, 250, 500, 1000], [100, 250, 500, 1000]]
      
  }); 
        });

  </script> 

</body>
</html>
<?php }else{ ?>
<script> window.location = 'login.php';</script>
<?php } ?>