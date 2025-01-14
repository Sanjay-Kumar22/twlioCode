<?php


// // Retrieve chat messages based on the ID from the URL
// $chatId = isset($_GET['id']) ? $_GET['id'] : 0;
// $chatMessages = array(); // Fetch messages based on $chatId

// // ... (fetch messages from the database based on $chatId)

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Chat</title>
    <!-- Include Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container">
    <div class="row">
        <div class="col-md-12 mt-5">
            <!-- Button to trigger the modal -->
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#chatModal">
                Open Chat
            </button>

            <!-- Modal -->
            <div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
                <div class="modal-dialog" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="chatModalLabel">Chat</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            
                            
                            <!-- Chatbox UI goes here -->
                            
                            <?php
                            
                            include('connection.php');
                            //file_put_contents('twilioResponse.txt',json_encode($_POST));
                            $data  = json_decode(file_get_contents('twilioResponse.txt'));
                            // echo '<pre>';
                            // print_r($data);
                            
                            $body = $data->Body;
                            echo $body;
                            
                            ?>
                            
                            
                            <div id="chatbox">
                                <!-- Render chat messages here -->
                                <?php
                                
                                
                                foreach ($chatMessages as $msg) {
                                    $msgText = $msg['message_text'];
                                    $msgType = $msg['type'];

                                    echo "<div class='$msgType'>$msgText</div>";
                                    
                                
                                }
                                ?>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Include Bootstrap JS and jQuery -->
<script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>
</html>
