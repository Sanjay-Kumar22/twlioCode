<?php

session_start();
if(!empty($_SESSION['login_email'])){

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
include('connection.php');
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Twilio</title>
  <link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/jquery.dataTables.min.css">
  <style>
         table {
            width: 100%;
            border-collapse: collapse;
        }

        tbody {
            display: block;
            max-height: 615px; /* Set your desired max height */
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
      span.numberss {
        background-color: darkblue;
        color: white;
        padding: 5px;
        font-size: 11px;
        border-radius: 10px;
    }
    #myModal {
      display: none;
      position: fixed;
      z-index: 1;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      overflow: auto;
      background-color: rgba(0,0,0,0.4);
      z-index: 999999999;
    }

    .modal-content {
      background-color: #fefefe;
      margin: 10% auto;
      padding: 20px;
      border: 1px solid #888;
      width: 50%;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      border-radius: 5px;
     
    }

    .selectedNumber {
        padding: 10px;
        border-radius: 10px;
        background-color: grey;
        margin-bottom: 15px;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    #messageTextArea {
      width: 100%;
      height: 100px;
      margin-bottom: 10px;
    }

    #sendMessageButton {
      background-color: #4CAF50;
      color: white;
      border: none;
      padding: 10px 20px;
      text-align: center;
      text-decoration: none;
      display: inline-block;
      font-size: 16px;
      border-radius: 5px;
      cursor: pointer;
    }

    #sendMessageButton:hover {
      background-color: #45a049;
    }
    
    #sendSelectedsms{
        background-color: #4CAF50;
        color: white;
        cursor: pointer;
        position: relative;
        left: 200px;
        top: 35px;
        z-index: 9999999;
    }
    .col-md-3.log-history {
        padding: 35px;
    }
    .row.justify-content-center {
        border: 1px solid;
        border-radius: 10px;
    }
    .msg-status {
        font-size: 12px;
        overflow-y: scroll;
        height: 680px;
    }
  </style>
</head>
<body>

<div class="container-fluid">
  <div class="row">
   
    <?php include('navbar.php'); ?>
   
    <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4" id="content">
      <?php include('header.php'); ?>
      <div class="container mt-5">
        <div class="row justify-content-center">
          <div class="col-md-9">
            
            <button type="button" class= "btn btn-success" id="sendSelectedsms"><i class="fa fa-commenting-o" aria-hidden="true"></i> Send SMS</button>
            <div id="myModal" class="modal">
              <div class="modal-content">
               <span class="close">&times;</span>
                <div class="selectedNumber" id="selectednumber"></div>
                <textarea id="messageTextArea" placeholder="Enter your message"></textarea>
                <button id="sendMessageButton"><i class="fa fa-commenting-o" aria-hidden="true"></i> Send SMS</button>
              </div>
            </div>
            
            <table id="example" class="display">
              <thead>
                <tr>
                  <th><input type="checkbox" id="selectAllCheckbox" class="selectAllCheckbox"> Select All</th>
                  <th>ID</th>
                  <th>Name</th>
                  <th>Mobile</th>
                </tr>
              </thead>
              <tbody>
              <?php 
              $sql = "SELECT * FROM candidate_list WHERE  `candidate_mobile` !='' LIMIT 500 ";
              $result = mysqli_query($conn, $sql);
              
              if($result && mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)){ ?>
                  <tr>
                    <td><input type="checkbox" class="rowCheckbox" value="+61<?php echo $row['candidate_mobile']; ?>" data-name="<?php echo $row['candidate_name']; ?>"></td>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['candidate_name']; ?></td>
                    <td><?php echo $row['candidate_mobile']; ?></td>
                  </tr>
                <?php }
              } ?>
              </tbody>
            </table>
          </div>
          <div class="col-md-3 log-history">
              <div Class="log-heading"><i class="fa fa-history" aria-hidden="true"></i> Sent SMS Logs</div>
              <div class="msg-status">
                  
              </div>
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
    var maxSelection = 100;

    var table = $('#example').DataTable({
      columnDefs: [{
        orderable: false,
        targets: 0
      }],
      select: {
        style: 'multi',
        selector: 'td:first-child'
      },
      ordering: false,
      "lengthMenu": [[100, 250, 500, 1000], [100, 250, 500, 1000]]
    });

    $('#selectAllCheckbox').on('change', function() {
        var checkAll = $('#selectAllCheckbox').prop('checked');
        if(checkAll === true){
            $('input.rowCheckbox').prop('checked',true);
        }else{
            $('input.rowCheckbox').prop('checked',false);
        }    
        
    });

    $('#example tbody').on('change', '.rowCheckbox', function() {
      updateSelection();
    });

    $('#sendSelectedsms').on('click', function() {
      var selectedCheckboxes = $('.rowCheckbox:checked', table.rows({ search: 'applied' }).nodes());
      if (selectedCheckboxes.length > 0) {
        $('#myModal').css('display', 'block');
            var allname = [];
            $(".rowCheckbox").each(function(){
                if($(this).prop('checked') ===  true){
                    var name = `<span class="numberss">${$(this).attr('data-name')}</span>`;
                    allname.push(name);
                   
                }
            });
            var sendName = allname.join(',');
            $('.selectedNumber').html(sendName);
  
      } else {
        alert('Please select at least one row.');
      }
    });

    $('.close').on('click', function() {
      $('#myModal').css('display', 'none');
    });

  
  
$('#sendMessageButton').on('click', function() {
    var message = $('#messageTextArea').val();
    var selectedName = $('#selectednumber').html();
    var selectedNumber = $('#selectednumber').val();
    var allnumber =[];
    $(".rowCheckbox").each(function(){
        if($(this).prop('checked') ===  true){
            var number = $(this).val();
           allnumber.push(number);
        }
    });
    var sendNumber = allnumber.join(',');
    console.log(sendNumber);
    if (message.trim() === '') {
        alert('Please enter a message.');
    } else {
        $.ajax({
           url : 'ajax-data.php',
           type : 'POST',
           data : {
               action : 'bulkSMS',
               phoneNumbers : sendNumber,
               message : message
           },success : function(response){
              //alert(response);
              //if(response == 1){
                //  alert('SMS sent successfully')
                   $('#myModal').css('display', 'none');
                   $('.msg-status').html(response); 
              //}
           },error : function(err){
               alert(err);
           }
        });

    }
});

    
    

    function updateSelection() {
      var selectedCheckboxes = $('.rowCheckbox:checked', table.rows({ search: 'applied' }).nodes());
      if (selectedCheckboxes.length > maxSelection) {
        selectedCheckboxes.slice(maxSelection).prop('checked', false);
        alert(`You can select a maximum of ${maxSelection} rows.`);
      }
       $('#selectAllCheckbox').prop('checked', selectedCheckboxes.length === table.rows({ search: 'applied' }).count());
       table.rows({ search: 'applied' }).select();
    }
    
    

  });
</script>
</body>
</html>
<?php }else{ ?>
<script> window.location = 'login.php';</script>
<?php } ?>
