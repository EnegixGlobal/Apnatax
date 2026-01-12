<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Chat Application</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        #chat-box {
            border: 1px solid #ccc;
            padding: 10px;
            width: 500px;
            height: 300px;
            overflow-y: scroll;
        }
        #message-box {
            width: 500px;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div id="chat-box"></div>
    <div id="message-box">
        <input type="text" id="message" placeholder="Type a message" />
        <button id="send">Send</button>
    </div>

    <script>
        $(document).ready(function() {
            var sender_id = <?php echo json_encode($sender_id); ?>;
            var receiver_id = <?php echo json_encode($receiver_id); ?>;

            // Load chat messages
            function loadChats() {
                $.ajax({
                    url: '<?php echo base_url('chat/get_messages'); ?>',
                    method: 'GET',
                    data: {
                        sender_id: sender_id,
                        receiver_id: receiver_id
                    },
                    dataType: 'json',
                    success: function(data) {
                        var chatBox = $('#chat-box');
                        chatBox.html('');
                        data.forEach(function(chat) {
                            chatBox.append('<p><strong>User ' + chat.sender_id + ':</strong> ' + chat.message + ' <em>' + chat.timestamp + '</em></p>');
                        });
                    }
                });
            }

            loadChats();
            setInterval(loadChats, 3000);

            // Send message
            $('#send').on('click', function() {
                var message = $('#message').val();

                $.ajax({
                    url: '<?php echo base_url('chat/send_message'); ?>',
                    method: 'POST',
                    data: {
                        sender_id: sender_id,
                        receiver_id: receiver_id,
                        message: message
                    },
                    success: function(response) {
                        $('#message').val('');
                        loadChats();
                    }
                });
            });
        });
    </script>
</body>
</html>
