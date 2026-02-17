
                        <div class="card-body">
                            <div class="row">
                                <div class="col-xl-4 col-lg-4 col-md-5">
                                    <div class="card">
                                        <div class="mt-4 mb-4 mx-4 text-center">
                                            <?php if($this->session->role!='customer'){ ?>
                                            <a href="#" class="btn btn-primary btn-lg d-grid" data-bs-toggle="modal" data-bs-target="#myModal">New Chat</a>
                                            <?php } ?>
                                        </div>
                                        <?php
                                            if(!empty($chats)){
                                                foreach($chats as $chat){
                                                    $class='';
                                                    if($this->input->get('chat_user')==md5('user-'.$chat['id'])){
                                                        $class='active active-chat-user';
                                                    }
                                        ?>
                                        <div class="list-group list-group-transparent mb-0 mail-inbox  pb-3">
                                            <div class="list-group-item d-flex align-items-center <?= $class ?> mx-4 my-2">
                                                <a href="<?= base_url('chat/?chat_user='.md5('user-'.$chat['id'])); ?>" class="d-flex align-items-center flex-grow-1 text-decoration-none">
                                                    <span class="icons"><i class="fa fa-user"></i></span> <?= $chat['name']; ?> 
                                                    <?php 
                                                        $spanclass='d-none';
                                                        if(!empty($chat['count'])){ 
                                                            $spanclass='';
                                                        }
                                                    ?>
                                                    <span class="ms-auto badge bg-secondary bradius <?= $spanclass ?> count-span"><?= $chat['count']; ?></span>
                                                </a>
                                                <?php if($this->session->role=='admin'){ ?>
                                                <button type="button" class="btn btn-sm btn-danger ms-2 delete-user-chats-btn" data-user-id="<?= md5('user-'.$chat['id']); ?>" data-user-name="<?= htmlspecialchars($chat['name']); ?>" title="Delete all chats for this user">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                                <?php } ?>
                                            </div>
                                        </div>
                                        <?php
                                                }
                                            }
                                        ?>
                                    </div>
                                </div>
                                <div class="col-xl-8 col-lg-8 col-md-7">
                                    <?php include('chatbox.php'); ?>
                                </div>
                            </div>
                        </div>

        <div class="modal fade" id="myModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="exampleModalLabel">Select User</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-12">
                                <div class="table-responsive">
                                    <table class="table table-condensed">
                                        <thead>
                                            <tr>
                                                <th>Sl.No.</th>
                                                <th>Name</th>
                                                <th>Role</th>
                                                <th>Chat</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            if(!empty($users)){
                                                $i=0;
                                                foreach($users as $user){
                                                    $i++;
                                                    $role=($user['role']=='customer')?'Customer':'Employee';
                                            ?>
                                            <tr>
                                                <td><?= $i; ?></td>
                                                <td><?= $user['name']; ?></td>
                                                <td><?= $role; ?></td>
                                                <td>
                                                    <a href="<?= base_url('chat/?chat_user='.md5('user-'.$user['id'])); ?>" class="btn btn-sm btn-info" ><i class="fa fa-send"></i></a>
                                                    <?php if($this->session->role=='admin'){ ?>
                                                    <button type="button" class="btn btn-sm btn-danger delete-user-chats-btn" data-user-id="<?= md5('user-'.$user['id']); ?>" data-user-name="<?= htmlspecialchars($user['name']); ?>" title="Delete all chats for this user">
                                                        <i class="fa fa-trash"></i>
                                                    </button>
                                                    <?php } ?>
                                                </td>
                                            </tr>
                                            <?php
                                                }
                                            }
                                            ?>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
                        <script>
                            var interval;
                            $(document).ready(function() {
                                $('body').on('keyup','#message',function(e){
                                    if(e.which==13){
                                        $('#send').click();   
                                    }
                                });
                                var receiver_id = '<?= $this->input->get('chat_user')!==NULL?$this->input->get('chat_user'):''; ?>';
                                
                                // Load chat messages
                                function loadChats() {
                                    $.ajax({
                                        url: '<?= base_url('chat/get_messages'); ?>',
                                        method: 'GET',
                                        data: {
                                            receiver_id: receiver_id
                                        },
                                        dataType: 'json',
                                        success: function(data) {
                                            var chatBox = $('#chat-box');
                                            chatBox.html('');
                                            var user=data['user'];
                                            var count=data['count'];
                                            if(count>0){
                                                $('.active-chat-user').find('.count-span').text(count);
                                                $('.active-chat-user').find('.count-span').removeClass('d-none');
                                            }
                                            else{
                                                $('.active-chat-user').find('.count-span').text('');
                                                $('.active-chat-user').find('.count-span').addClass('d-none');
                                            }
                                            $('#sender').text(user);
                                            data=data['chat'];
                                            if(user=='' && data.length==0){
                                                $('#chat-card').hide();
                                                return false;
                                            }
                                            var prev_date='';
                                            var prev_time='';
                                            var prev_sender='';
                                            data.forEach(function(chat) {
                                                var chatBlock='';
                                                if(prev_date!=chat.date){
                                                    chatBlock+='<label class="main-chat-time"><span>';
                                                    chatBlock+=chat.date;
                                                    chatBlock+='</span></label>';
                                                }
                                                chatBlock+='<div class="media ';
                                                if(chat.enc_sender_id=='<?= $this->session->user ?>'){
                                                    chatBlock+='flex-row-reverse chat-right';
                                                }
                                                else{
                                                    chatBlock+='chat-left';   
                                                }
                                                chatBlock+='">';
                                                chatBlock+='<div class="main-img-user online">';
                                                chatBlock+='<img alt="avatar" src="<?= file_url('includes/images/users/21.jpg'); ?>">';
                                                chatBlock+='</div><div class="media-body">';
                                                chatBlock+='<div class="main-msg-wrapper">';
                                                chatBlock+=chat.message;
                                                chatBlock+='</div>';
                                                chatBlock+='<div><span>'+chat.time+'</span>';
                                                chatBlock+='<a href=""><i class="icon ion-android-more-horizontal"></i></a>';
                                                chatBlock+='</div>';
                                                chatBlock+='</div>';
                                                chatBlock+='</div>';
                                                chatBox.append(chatBlock);
                                                prev_date=chat.date;
                                                prev_time=chat.time;
                                                prev_sender=chat.enc_sender_id;
                                            });
                                            chatBox.scrollTop(chatBox[0].scrollHeight);
                                        }
                                    });
                                }

                                loadChats();
                                interval=setInterval(loadChats, 3000);

                                // Send message
                                $('body').on('click','#send', function() {
                                    var message = $('#message').val();

                                    $.ajax({
                                        url: '<?php echo base_url('chat/send_message'); ?>',
                                        method: 'POST',
                                        data: {
                                            receiver_id: receiver_id,
                                            message: message
                                        },
                                        success: function(response) {
                                            $('#message').val('');
                                            loadChats();
                                            clearInterval(interval);
                                            interval=setInterval(loadChats, 3000);
                                        }
                                    });
                                });

                                // Delete conversation
                                $('body').on('click','#delete-conversation-btn', function() {
                                    if(confirm('Are you sure you want to delete this entire conversation? This action cannot be undone.')) {
                                        $.ajax({
                                            url: '<?php echo base_url('chat/delete_conversation'); ?>',
                                            method: 'POST',
                                            data: {
                                                receiver_id: receiver_id
                                            },
                                            dataType: 'json',
                                            success: function(response) {
                                                if(response.status === 'success') {
                                                    alert('Conversation deleted successfully');
                                                    window.location.href = '<?php echo base_url('chat/'); ?>';
                                                } else {
                                                    alert('Error: ' + response.message);
                                                }
                                            },
                                            error: function() {
                                                alert('An error occurred while deleting the conversation');
                                            }
                                        });
                                    }
                                });

                                // Delete all chats
                                $('body').on('click','#delete-all-chats-btn', function() {
                                    if(confirm('WARNING: Are you sure you want to delete ALL chats from the database? This action cannot be undone and will delete all chat messages for all users.')) {
                                        var confirmText = prompt('Type "yes" to confirm deletion of all chats:');
                                        if(confirmText === 'yes') {
                                            $.ajax({
                                                url: '<?php echo base_url('chat/delete_all_chats'); ?>',
                                                method: 'POST',
                                                data: {
                                                    confirm: 'yes'
                                                },
                                                dataType: 'json',
                                                success: function(response) {
                                                    if(response.status === 'success') {
                                                        alert('All chats deleted successfully');
                                                        window.location.href = '<?php echo base_url('chat/'); ?>';
                                                    } else {
                                                        alert('Error: ' + response.message);
                                                    }
                                                },
                                                error: function() {
                                                    alert('An error occurred while deleting all chats');
                                                }
                                            });
                                        } else {
                                            alert('Deletion cancelled. You must type "yes" to confirm.');
                                        }
                                    }
                                });

                                // Delete all chats for a specific user
                                $('body').on('click','.delete-user-chats-btn', function() {
                                    var userId = $(this).data('user-id');
                                    var userName = $(this).data('user-name');
                                    if(confirm('Are you sure you want to delete ALL chats for user "' + userName + '"? This will delete all messages where this user is either sender or receiver. This action cannot be undone.')) {
                                        $.ajax({
                                            url: '<?php echo base_url('chat/delete_user_chats'); ?>',
                                            method: 'POST',
                                            data: {
                                                user_id: userId
                                            },
                                            dataType: 'json',
                                            success: function(response) {
                                                if(response.status === 'success') {
                                                    alert('All chats for user "' + userName + '" deleted successfully');
                                                    window.location.href = '<?php echo base_url('chat/'); ?>';
                                                } else {
                                                    alert('Error: ' + response.message);
                                                }
                                            },
                                            error: function() {
                                                alert('An error occurred while deleting user chats');
                                            }
                                        });
                                    }
                                });

                                // Delete all chats for current user from chatbox dropdown
                                $('body').on('click','#delete-user-chats-from-chatbox-btn', function() {
                                    if(receiver_id && receiver_id !== '') {
                                        if(confirm('Are you sure you want to delete ALL chats for this user? This will delete all messages where this user is either sender or receiver. This action cannot be undone.')) {
                                            $.ajax({
                                                url: '<?php echo base_url('chat/delete_user_chats'); ?>',
                                                method: 'POST',
                                                data: {
                                                    user_id: receiver_id
                                                },
                                                dataType: 'json',
                                                success: function(response) {
                                                    if(response.status === 'success') {
                                                        alert('All chats for this user deleted successfully');
                                                        window.location.href = '<?php echo base_url('chat/'); ?>';
                                                    } else {
                                                        alert('Error: ' + response.message);
                                                    }
                                                },
                                                error: function() {
                                                    alert('An error occurred while deleting user chats');
                                                }
                                            });
                                        }
                                    } else {
                                        alert('Please select a user first');
                                    }
                                });
                            });
                        </script>