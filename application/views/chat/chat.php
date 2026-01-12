
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
                                            <a href="<?= base_url('chat/?chat_user='.md5('user-'.$chat['id'])); ?>" class="list-group-item d-flex align-items-center <?= $class ?> mx-4 my-2">
                                                <span class="icons"><i class="fa fa-user"></i></span> <?= $chat['name']; ?> 
                                                <?php 
                                                    $spanclass='d-none';
                                                    if(!empty($chat['count'])){ 
                                                        $spanclass='';
                                                    }
                                                ?>
                                                <span class="ms-auto badge bg-secondary bradius <?= $spanclass ?> count-span"><?= $chat['count']; ?></span>
                                            </a>
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
                            });
                        </script>