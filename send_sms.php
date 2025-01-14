<?php
session_start();
if (!empty($_SESSION['login_email'])) {
    include('connection.php');
    $queryCandidate = "SELECT * FROM candidate_list LIMIT 500";
    $resultCandidate = mysqli_query($conn, $queryCandidate);
    
    $queryClient = "SELECT * FROM contact_list LIMIT 500";
    $resultClient = mysqli_query($conn, $queryClient);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/css/multi-select-tag.css">

    <style>
    
.container {
    margin-top: 20px;
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}
.center-label {
 text-align: center;
 margin: 20px 0; 
 font-size: 18px; 
 color: #333;
 }
        
 #candidateMobile {
  display: none;
}

#clientMobile{
   display: none;
}
.msg-status {
    font-size: 12px;
    overflow-y: scroll;
    height: 280px;
}
.col-md-3.log-history {
    padding: 35px;
}
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <?php include('navbar.php'); ?>
        <!-- Main Content -->
        <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4" id="content">
            <?php include('header.php'); ?>
            <div class="container mt-5 ">
                <div class="row justify-content-center">
                    <div class="col-md-6">
                        <?php if (isset($_GET['status'])) {
                            if ($_GET['status'] == 'success') { ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <strong>Hi!</strong> <?php echo "message sent successfully"; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php } elseif ($_GET['status'] == 'error') { ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <strong>Hi!</strong> <?php echo "message not sent"; ?>
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                        <span aria-hidden="true">&times;</span>
                                    </button>
                                </div>
                            <?php }
                        } ?>
                        <form action="" method="POST">
                            <div class="form-group">
                                <label for="phoneType"><i class="fa fa-users" aria-hidden="true"></i> Candidate List
                                    <small style="color:red;">(You can select multiple candidates)</small>
                                </label>
                                <select id="candidateMobile" class="form-control" name="candidateMobile[]" multiple>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($resultCandidate)) {
                                        if (!empty($row['candidate_mobile'])) {
                                            $mobile = $row['candidate_mobile'];
                                            $name = $row['candidate_name'];
                                            $formattedMobile = '+61' . $mobile;
                                            echo "<option value=\"$formattedMobile\">$formattedMobile($name)</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>

                            <center>Or</center>
                            
                             <div class="form-group">
                                <label for="phoneType"><i class="fa fa-users" aria-hidden="true"></i> Client List
                                    <small style="color:red;">(You can select multiple client)</small>
                                </label>
                                <select id="clientMobile" class="form-control" name="clientMobile[]" multiple>
                                    <?php
                                    while ($row = mysqli_fetch_assoc($resultClient)) {
                                        if (!empty($row['contact_mobile'])) {
                                            $mobile = $row['contact_mobile'];
                                            $name = $row['contact_name'];
                                            $formattedMobile = '+61' . $mobile;
                                            echo "<option value=\"$formattedMobile\">$formattedMobile($name)</option>";
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            
                            <center>Or</center>

                            <div class="form-group">
                                <label for="phoneNumber"><i class="fa fa-mobile" aria-hidden="true"></i> Type Manually
                                    Mobile Number</label>
                                <input type="tel" name="phone" class="form-control" id="phoneNumber"
                                       placeholder="Enter your phone number">
                            </div>
                            <div class="form-group">
                                <label for="message"><i class="fa fa-commenting-o" aria-hidden="true"></i> Message</label>
                                <textarea class="form-control" id="message" name="message" rows="4"
                                          placeholder="Type your message" required></textarea>
                            </div>
                            <button type="submit" id="sendSMS" name="submit" class="btn btn-primary"><i
                                        class="fa fa-commenting-o" aria-hidden="true"></i> Send Message
                            </button>
                        </form>
                    </div>
                    <div class="col-md-3 log-history">
                        <div class="log-heading"><i class="fa fa-history" aria-hidden="true"></i> Sent SMS Logs
                        </div>
                        <div class="msg-status"></div>
                    </div>

                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/gh/habibmhamadi/multi-select-tag@2.0.1/dist/js/multi-select-tag.js"></script>
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
<script>
    $(document).ready(function () {
    new MultiSelectTag('candidateMobile');
    new MultiSelectTag('clientMobile');

    $('#sendSMS').on('click', function (e) {
        e.preventDefault();

        var message = $('#message').val().trim();
        var selectedNumber = $('#candidateMobile').val();
        var selectClient = $('#clientMobile').val();
        var phoneNumber;

        if (selectedNumber && selectedNumber.length > 0) {
            phoneNumber = selectedNumber;
        } else if (selectClient && selectClient.length > 0) {
            phoneNumber = selectClient;
        } else {
            var manualNumber = $('#phoneNumber').val();
            if (manualNumber && manualNumber.trim().length > 0) {
               
                phoneNumber = [manualNumber];
            } else {
                alert('Please fill at least one field from Candidate List or Client List or Manual Mobile Number');
                return false;
            }
        }

        if (message === '') {
            alert('Please enter a message.');
        } else {
            $.ajax({
                url: 'ajax-data.php',
                type: 'POST',
                data: {
                    action: 'bulkSMS',
                    phoneNumbers: phoneNumber.join(','),
                    message: message
                },
                success: function (response) {
                    $('.msg-status').html(response);
                    $('#phoneNumber').val('');
                },
                error: function (err) {
                    console.error('AJAX error:', err);
                }
            });
        }
    });
});


</script>

</body>
</html>
<?php
} else {
    echo "<script>window.location = 'login.php';</script>";
}
?>
