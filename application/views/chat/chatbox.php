
                                <div class="card" id="chat-card">
                                    <div class="main-content-app pt-0">
                                        <div class="main-content-body main-content-body-chat h-100">
                                            <div class="main-chat-header pt-3 d-block d-sm-flex">
                                                <div class="main-img-user online"><img alt="avatar" src="<?= file_url('includes/images/users/1.jpg'); ?>"></div>
                                                <div class="main-chat-msg-name mt-2">
                                                    <h6 id="sender">Saul Goodmate</h6>
                                                    <span class="dot-label bg-success"></span><small class="me-3">online</small>
                                                </div>
                                                <nav class="nav">
                                                    <div class="dropdown">
                                                        <a class="nav-link" href="" data-bs-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><i class="fe fe-more-horizontal"></i></a>
                                                        <div class="dropdown-menu dropdown-menu-end" style="">
                                                            <a class="dropdown-item" href="<?= base_url('chat/'); ?>"><i class="fa fa-times me-1"></i> Close</a>
                                                        </div>
                                                    </div>
                                                </nav>
                                            </div>
                                            <!-- main-chat-header -->
                                            <div class="main-chat-body flex-2 ps ps--active-y" id="ChatBody">
                                                <div class="content-inner" id="chat-box">
                                                    <label class="main-chat-time"><span>2 days ago</span></label>
                                                    <div class="media flex-row-reverse chat-right">
                                                        <div class="main-img-user online"><img alt="avatar" src="<?= file_url('includes/images/users/21.jpg'); ?>"></div>
                                                        <div class="media-body">
                                                            <div class="main-msg-wrapper">
                                                                Nulla consequat massa quis enim. Donec pede justo, fringilla vel...
                                                            </div>
                                                            <div class="main-msg-wrapper">
                                                                rhoncus ut, imperdiet a, venenatis vitae, justo...
                                                            </div>
                                                            <div>
                                                                <span>9:48 am</span> <a href=""><i class="icon ion-android-more-horizontal"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="media chat-left">
                                                        <div class="main-img-user online"><img alt="avatar" src="<?= file_url('includes/images/users/1.jpg'); ?>"></div>
                                                        <div class="media-body">
                                                            <div class="main-msg-wrapper">
                                                                Lorem ipsum dolor sit amet, consectetuer adipiscing elit. Aenean commodo ligula eget dolor.
                                                            </div>
                                                            <div>
                                                                <span>9:32 am</span> <a href=""><i class="icon ion-android-more-horizontal"></i></a>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <div class="ps__rail-x" style="left: 0px; bottom: 0px;"><div class="ps__thumb-x" tabindex="0" style="left: 0px; width: 0px;"></div></div><div class="ps__rail-y" style="top: 0px; height: 536px; right: 0px;"><div class="ps__thumb-y" tabindex="0" style="top: 0px; height: 256px;"></div></div></div>
                                            <div class="main-chat-footer">
                                                <input class="form-control" placeholder="Type your message here..." type="text" id="message">
                                                <button type="button" class="btn btn-icon  btn-primary brround" id="send"><i class="fa fa-paper-plane-o"></i></button>
                                                <nav class="nav">
                                                </nav>
                                            </div>
                                        </div>
                                    </div>
                                </div>
