<?php

session_start();
if(!empty($_SESSION['login_email'])){
include('connection.php');

error_reporting(E_ALL);
ini_set('display_errors', 1);
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

 .container {
  border: 2px solid #dedede;
  background-color: #f1f1f1;
  border-radius: 5px;
  padding: 10px;
  margin: 10px 0;
}

.darker {
  border-color: #ccc;
  background-color: #ddd;
}

.container::after {
  content: "";
  clear: both;
  display: table;
}

.container img {
  float: left;
  max-width: 60px;
  width: 100%;
  margin-right: 20px;
  border-radius: 50%;
}

.container img.right {
  float: right;
  margin-left: 20px;
  margin-right:0;
}

.time-right {
  float: right;
  color: #aaa;
}

.time-left {
  float: left;
  color: #999;
}
.modal-body {
    overflow-y: scroll;
    height: 500px;
}
.message-type {
    width: 100%;
}

.message-card {
    margin-bottom: 10px;
 }
 
.modal-header {
     background-color: #007bff;
     color: white;
 }

.modal-footer {
    width: 100%;
    background-color: #f1f1f1;
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
                    <div class="col-md-12">

                        <table id="example" class="display">
                            <thead>
                            <tr>
                                <th> ID </th>
                                <th>From</th>
                                <th>To</th>
                                <th>Message</th>
                                <th>Status</th>
                                <th>Date</th>
                                <th>Time</th>
                                <th>Chat</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php
                            $sql = "SELECT * FROM `SMS_history`";
                            $result = mysqli_query($conn, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($row = mysqli_fetch_assoc($result)) {
                                    echo "<tr>";
                                    echo "<td>" . $row['id'] . "</td>";
                                    echo "<td>" . $row['from_number'] . "</td>";
                                    echo "<td>" . $row['to_number'] . "</td>";
                                    echo "<td>Unread</td>";
                                    echo "<td>" . $row['sms_status'] . "</td>";
                                   
                                    echo "<td>" .  $row['sms_date'] . "</td>";
                                    echo "<td>" .  $row['sms_time'] . "</td>";
                                    echo '<td><button type="button" data-phone="'.$row['to_number'].'"class="btn btn-primary" id="openModalBtn"> <i class="fa fa-eye" aria-hidden="true"></i> View Chat</button></td>';

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
</body>
</html>
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalLabel">Chat Messages</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                    mesggae
            </div>
            <div class="modal-footer">
                <div class="message-type"><textarea class="form-control" id="typeSMS" placeholder="Type here"></textarea></div>
                <div class="message-send"><button class="btn btn-primary" id="replySMS">Send</button></div>
            </div>
        </div>
    </div>
</div>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>

    <script>

    $(document).on('click','#openModalBtn',function(){
        $('#userModal').modal('show');
        var phoneNumber = $(this).attr('data-phone');
        $('#openModalBtn').removeClass('active');
        $(this).addClass('active');
        $.ajax({
           url : 'ajax-data.php',
           type : 'POST',
           data : {
               action : 'fetchChat',
               phoneNumber : phoneNumber
           },success : function(response){
               $('.modal-body').html(response);
              // alert(response);
           },error : function(err){
               //alert(err);
           }
        });
    });
    $(document).on('click','#replySMS',function(){
       var phone = $('button.active').attr('data-phone');
       var typeSMS = $('#typeSMS').val();
       var date = '<php echo date("H:i:s"); ?>';
        $.ajax({
           url : 'ajax-data.php',
           type : 'POST',
           data : {
               action : 'sendSMS',
               phoneNumber : phone,
               typeSMS : typeSMS
           },success : function(response){
                if(response.replace(/\s/g, '') == 'sent' ){
                   
                    $('#typeSMS').val('');
                    $('.modal-body').append('<div class="container"><img src="https://www.w3schools.com/w3images/bandmember.jpg" alt="Avatar" style="width:100%;"><p>'+typeSMS+'</p><span class="time-right">'+date+'</span></div>');
                    var div = $('.modal-body');
                    div.scrollTop(div.prop('scrollHeight'));
                }
           },error : function(err){
               //alert(err);
           }
        });
    });
    
    $('#example').DataTable({
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      ordering: false,
      "lengthMenu": [[100, 250, 500, 1000], [100, 250, 500, 1000]]
      
    }); 
       
    </script>
    
  <?php }else{ ?>
<script> window.location = 'login.php';</script>
<?php } ?>