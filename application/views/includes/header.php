    <!-- PAGE -->
    <div class="page">
        <div class="page-main">

            <!-- app-Header -->
            <div class="app-header header sticky">
                <div class="container-fluid main-container">
                    <div class="d-flex">
                        <a aria-label="Hide Sidebar" class="app-sidebar__toggle" data-bs-toggle="sidebar" href="javascript:void(0)"></a>
                        <!-- sidebar-toggle-->
                        <a class="logo-horizontal " href="<?= base_url('home/'); ?>">
                            <img src="<?= file_url('assets/images/logo.png'); ?>" class="header-brand-img desktop-logo" style="height:60px;" alt="logo">
                            <img src="<?= file_url('assets/images/logo.png'); ?>" class="header-brand-img light-logo1" style="height:60px;" alt="logo">
                        </a>
                        <!-- LOGO -->
                        <!--<div class="main-header-center ms-3 d-none d-lg-block">
                            <input class="form-control" placeholder="Search for results..." type="search">
                            <button class="btn px-0 pt-2"><i class="fe fe-search" aria-hidden="true"></i></button>
                        </div>-->
                        <div class="d-flex order-lg-2 ms-auto header-right-icons">
                            <div class="dropdown d-none">
                                <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown">
                                    <i class="fe fe-search"></i>
                                </a>
                                <div class="dropdown-menu header-search dropdown-menu-start">
                                    <div class="input-group w-100 p-2">
                                        <input type="text" class="form-control" placeholder="Search....">
                                        <div class="input-group-text btn btn-primary">
                                            <i class="fe fe-search" aria-hidden="true"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- SEARCH -->
                            <button class="navbar-toggler navresponsive-toggler d-lg-none ms-auto" type="button"
                                data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent-4"
                                aria-controls="navbarSupportedContent-4" aria-expanded="false"
                                aria-label="Toggle navigation">
                                <span class="navbar-toggler-icon fe fe-more-vertical"></span>
                            </button>
                            <div class="navbar navbar-collapse responsive-navbar p-0">
                                <div class="collapse navbar-collapse" id="navbarSupportedContent-4">
                                    <div class="d-flex order-lg-2">
                                        <?php /*?><div class="dropdown d-lg-none d-flex">
                                            <a href="javascript:void(0)" class="nav-link icon" data-bs-toggle="dropdown">
                                                <i class="fe fe-search"></i>
                                            </a>
                                            <div class="dropdown-menu header-search dropdown-menu-start">
                                                <div class="input-group w-100 p-2">
                                                    <input type="text" class="form-control" placeholder="Search....">
                                                    <div class="input-group-text btn btn-primary">
                                                        <i class="fa fa-search" aria-hidden="true"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="d-flex country">
                                            <a class="nav-link icon text-center" data-bs-target="#country-selector"
                                                data-bs-toggle="modal">
                                                <i class="fe fe-globe"></i><span
                                                    class="fs-16 ms-2 d-none d-xl-block">English</span>
                                            </a>
                                        </div>
                                        <!-- COUNTRY -->
                                        <div class="dropdown  d-flex shopping-cart">
                                            <a class="nav-link icon text-center" data-bs-toggle="dropdown">
                                                <i class="fe fe-shopping-cart"></i><span class="badge bg-secondary header-badge">4</span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading border-bottom">
                                                    <div class="d-flex">
                                                        <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark"> My Shopping Cart</h6>
                                                        <div class="ms-auto">
                                                            <span class="badge bg-danger-transparent header-badge text-danger fs-14">Hurry Up!</span>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="header-dropdown-list message-menu">
                                                    <div class="dropdown-item d-flex p-4">
                                                        <a href="cart.html" class="open-file"></a>
                                                        <span
                                                            class="avatar avatar-xl br-5 me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/pngs/4.jpg'); ?>"></span>
                                                        <div class="wd-50p">
                                                            <h5 class="mb-1">Flower Pot for Home Decor</h5>
                                                            <span>Status: <span class="text-success">In Stock</span></span>
                                                            <p class="fs-13 text-muted mb-0">Quantity: 01</p>
                                                        </div>
                                                        <div class="ms-auto text-end d-flex fs-16">
                                                            <span class="fs-16 text-dark d-none d-sm-block px-4">
                                                                $438
                                                            </span>
                                                            <a href="javascript:void(0)" class="fs-16 btn p-0 cart-trash">
                                                                <i class="fe fe-trash-2 border text-danger brround d-block p-2"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-item d-flex p-4">
                                                        <a href="cart.html" class="open-file"></a>
                                                        <span
                                                            class="avatar avatar-xl br-5 me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/pngs/6.jpg'); ?>"></span>
                                                        <div class="wd-50p">
                                                            <h5 class="mb-1">Black Digital Camera</h5>
                                                            <span>Status: <span class="text-danger">Out Stock</span></span>
                                                            <p class="fs-13 text-muted mb-0">Quantity: 06</p>
                                                        </div>
                                                        <div class="ms-auto text-end d-flex">
                                                            <span class="fs-16 text-dark d-none d-sm-block px-4">
                                                                $867
                                                            </span>
                                                            <a href="javascript:void(0)" class="fs-16 btn p-0 cart-trash">
                                                                <i class="fe fe-trash-2 border text-danger brround d-block p-2"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-item d-flex p-4">
                                                        <a href="cart.html" class="open-file"></a>
                                                        <span
                                                            class="avatar avatar-xl br-5 me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/pngs/8.jpg'); ?>"></span>
                                                        <div class="wd-50p">
                                                            <h5 class="mb-1">Stylish Rockerz 255 Ear Pods</h5>
                                                            <span>Status: <span class="text-success">In Stock</span></span>
                                                            <p class="fs-13 text-muted mb-0">Quantity: 05</p>
                                                        </div>
                                                        <div class="ms-auto text-end d-flex">
                                                            <span class="fs-16 text-dark d-none d-sm-block px-4">
                                                                $323
                                                            </span>
                                                            <a href="javascript:void(0)" class="fs-16 btn p-0 cart-trash">
                                                                <i class="fe fe-trash-2 border text-danger brround d-block p-2"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-item d-flex p-4">
                                                        <a href="cart.html" class="open-file"></a>
                                                        <span
                                                            class="avatar avatar-xl br-5 me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/pngs/1.jpg'); ?>"></span>
                                                        <div class="wd-50p">
                                                            <h5 class="mb-1">Women Party Wear Dress</h5>
                                                            <span>Status: <span class="text-success">In Stock</span></span>
                                                            <p class="fs-13 text-muted mb-0">Quantity: 05</p>
                                                        </div>
                                                        <div class="ms-auto text-end d-flex">
                                                            <span class="fs-16 text-dark d-none d-sm-block px-4">
                                                                $867
                                                            </span>
                                                            <a href="javascript:void(0)" class="fs-16 btn p-0 cart-trash">
                                                                <i class="fe fe-trash-2 border text-danger brround d-block p-2"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                    <div class="dropdown-item d-flex p-4">
                                                        <a href="cart.html" class="open-file"></a>
                                                        <span
                                                            class="avatar avatar-xl br-5 me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/pngs/3.jpg'); ?>"></span>
                                                        <div class="wd-50p">
                                                            <h5 class="mb-1">Running Shoes for men</h5>
                                                            <span>Status: <span class="text-success">In Stock</span></span>
                                                            <p class="fs-13 text-muted mb-0">Quantity: 05</p>
                                                        </div>
                                                        <div class="ms-auto text-end d-flex">
                                                            <span class="fs-16 text-dark d-none d-sm-block px-4">
                                                                $456
                                                            </span>
                                                            <a href="javascript:void(0)" class="fs-16 btn p-0 cart-trash">
                                                                <i class="fe fe-trash-2 border text-danger brround d-block p-2"></i>
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <div class="dropdown-footer">
                                                    <a class="btn btn-primary btn-pill w-sm btn-sm py-2" href="checkout.html"><i class="fe fe-check-circle"></i> Checkout</a>
                                                    <span class="float-end p-2 fs-17 fw-semibold">Total: $6789</span>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- CART -->
                                        <div class="dropdown  d-flex notifications">
                                            <a class="nav-link icon" data-bs-toggle="dropdown"><i
                                                    class="fe fe-bell"></i><span class=" pulse"></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading border-bottom">
                                                    <div class="d-flex">
                                                        <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">Notifications
                                                        </h6>
                                                    </div>
                                                </div>
                                                <div class="notifications-menu">
                                                    <a class="dropdown-item d-flex" href="notify-list.html">
                                                        <div class="me-3 notifyimg  bg-primary brround box-shadow-primary">
                                                            <i class="fe fe-mail"></i>
                                                        </div>
                                                        <div class="mt-1 wd-80p">
                                                            <h5 class="notification-label mb-1">New Application received
                                                            </h5>
                                                            <span class="notification-subtext">3 days ago</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="notify-list.html">
                                                        <div class="me-3 notifyimg  bg-secondary brround box-shadow-secondary">
                                                            <i class="fe fe-check-circle"></i>
                                                        </div>
                                                        <div class="mt-1 wd-80p">
                                                            <h5 class="notification-label mb-1">Project has been
                                                                approved</h5>
                                                            <span class="notification-subtext">2 hours ago</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="notify-list.html">
                                                        <div class="me-3 notifyimg  bg-success brround box-shadow-success">
                                                            <i class="fe fe-shopping-cart"></i>
                                                        </div>
                                                        <div class="mt-1 wd-80p">
                                                            <h5 class="notification-label mb-1">Your Product Delivered
                                                            </h5>
                                                            <span class="notification-subtext">30 min ago</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="notify-list.html">
                                                        <div class="me-3 notifyimg bg-pink brround box-shadow-pink">
                                                            <i class="fe fe-user-plus"></i>
                                                        </div>
                                                        <div class="mt-1 wd-80p">
                                                            <h5 class="notification-label mb-1">Friend Requests</h5>
                                                            <span class="notification-subtext">1 day ago</span>
                                                        </div>
                                                    </a>
                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a href="notify-list.html"
                                                    class="dropdown-item text-center p-3 text-muted">View all
                                                    Notification</a>
                                            </div>
                                        </div>
                                        <!-- NOTIFICATIONS -->
                                        <div class="dropdown  d-flex message">
                                            <a class="nav-link icon text-center" data-bs-toggle="dropdown">
                                                <i class="fe fe-message-square"></i><span class="pulse-danger"></span>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <div class="drop-heading border-bottom">
                                                    <div class="d-flex">
                                                        <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">You have 5
                                                            Messages</h6>
                                                        <div class="ms-auto">
                                                            <a href="javascript:void(0)" class="text-muted p-0 fs-12">make all unread</a>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="message-menu message-menu-scroll">
                                                    <a class="dropdown-item d-flex" href="chat.html">
                                                        <span
                                                            class="avatar avatar-md brround me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/users/1.jpg'); ?>"></span>
                                                        <div class="wd-90p">
                                                            <div class="d-flex">
                                                                <h5 class="mb-1">Peter Theil</h5>
                                                                <small class="text-muted ms-auto text-end">
                                                                    6:45 am
                                                                </small>
                                                            </div>
                                                            <span>Commented on file Guest list....</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="chat.html">
                                                        <span
                                                            class="avatar avatar-md brround me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/users/15.jpg'); ?>"></span>
                                                        <div class="wd-90p">
                                                            <div class="d-flex">
                                                                <h5 class="mb-1">Abagael Luth</h5>
                                                                <small class="text-muted ms-auto text-end">
                                                                    10:35 am
                                                                </small>
                                                            </div>
                                                            <span>New Meetup Started......</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="chat.html">
                                                        <span
                                                            class="avatar avatar-md brround me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/users/12.jpg'); ?>"></span>
                                                        <div class="wd-90p">
                                                            <div class="d-flex">
                                                                <h5 class="mb-1">Brizid Dawson</h5>
                                                                <small class="text-muted ms-auto text-end">
                                                                    2:17 pm
                                                                </small>
                                                            </div>
                                                            <span>Brizid is in the Warehouse...</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="chat.html">
                                                        <span
                                                            class="avatar avatar-md brround me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/users/4.jpg'); ?>"></span>
                                                        <div class="wd-90p">
                                                            <div class="d-flex">
                                                                <h5 class="mb-1">Shannon Shaw</h5>
                                                                <small class="text-muted ms-auto text-end">
                                                                    7:55 pm
                                                                </small>
                                                            </div>
                                                            <span>New Product Realease......</span>
                                                        </div>
                                                    </a>
                                                    <a class="dropdown-item d-flex" href="chat.html">
                                                        <span
                                                            class="avatar avatar-md brround me-3 align-self-center cover-image"
                                                            data-bs-image-src="<?= file_url('includes/images/users/3.jpg'); ?>"></span>
                                                        <div class="wd-90p">
                                                            <div class="d-flex">
                                                                <h5 class="mb-1">Cherry Blossom</h5>
                                                                <small class="text-muted ms-auto text-end">
                                                                    7:55 pm
                                                                </small>
                                                            </div>
                                                            <span>You have appointment on......</span>
                                                        </div>
                                                    </a>

                                                </div>
                                                <div class="dropdown-divider m-0"></div>
                                                <a href="javascript:void(0)" class="dropdown-item text-center p-3 text-muted">See all
                                                    Messages</a>
                                            </div>
                                        </div>
                                        <!-- MESSAGE-BOX -->
                                        <div class="dropdown d-flex header-settings">
                                            <a href="javascript:void(0);" class="nav-link icon"
                                                data-bs-toggle="sidebar-right" data-target=".sidebar-right">
                                                <i class="fe fe-align-right"></i>
                                            </a>
                                        </div>
                                        <!-- SIDE-MENU --><?php */ ?>
                                        <?php /*?><div class="d-flex country">
                                            <a class="nav-link icon theme-layout nav-link-bg layout-setting">
                                                <span class="dark-layout"><i class="fe fe-moon"></i></span>
                                                <span class="light-layout"><i class="fe fe-sun"></i></span>
                                            </a>
                                        </div><?php */ ?>
                                        <style>
                                            .notification-row-read .notification-label { font-weight: 400 !important; opacity: 0.92; }
                                            .notification-row-read .notification-subtext { opacity: 0.85; }
                                            .notification-row-unread .notification-label { font-weight: 600; }
                                            /* Read: icon circle explicit blue (unread keeps theme primary) */
                                            .notification-row-read .notifyimg.bg-primary {
                                                background-color: #0d6efd !important;
                                                box-shadow: 0 0.15rem 0.45rem rgba(13, 110, 253, 0.45) !important;
                                            }
                                            .notification-row-read .notifyimg i {
                                                color: #fff !important;
                                            }
                                        </style>
                                        <!-- NOTIFICATIONS -->
                                        <?php
                                        $notifications = getnotifications();
                                        $notification_badge = get_notification_badge_count();
                                        $notification_bell_mode = ($this->session->role === 'customer') ? 'customer' : 'admin';
                                        ?>
                                        <div class="dropdown d-flex notifications" data-bell-mode="<?= $notification_bell_mode; ?>">
                                            <a class="nav-link icon" data-bs-toggle="<?= count($notifications) > 0 ? 'dropdown' : '' ?>" aria-expanded="true"><i class="fe fe-bell"></i><sup class="notification-badge-sup"><?= $notification_badge > 0 ? (int) $notification_badge : ''; ?></sup>
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow" data-bs-popper="none">
                                                <div class="drop-heading border-bottom">
                                                    <div class="d-flex">
                                                        <h6 class="mt-1 mb-0 fs-16 fw-semibold text-dark">Notifications
                                                        </h6>
                                                    </div>
                                                </div>
                                                <div class="notifications-menu ps">
                                                    <?php
                                                    if (!empty($notifications)) {
                                                        foreach ($notifications as $notification) {
                                                            $text = $link = "";
                                                            if ($notification['type'] == 'Assign') {
                                                                if ($notification['order_id'] !== NULL) {
                                                                    $link = '';
                                                                } else {
                                                                    $link = base_url('orders/');
                                                                }
                                                            } elseif ($notification['type'] == 'New') {
                                                                if ($notification['order_id'] !== NULL) {
                                                                    $link = base_url('orders/');
                                                                } else {
                                                                    $link = base_url('orders/');
                                                                }
                                                            } elseif ($notification['type'] == 'Documents Uploaded') {
                                                                if ($notification['order_id'] !== NULL) {
                                                                    $link = base_url('orders/viewdocuments/' . md5($notification['order_id']));
                                                                } else {
                                                                    $link = base_url('orders/');
                                                                }
                                                            } elseif ($notification['type'] == 'kyc_pending') {
                                                                if (!empty($notification['order_id'])) {
                                                                    $link = base_url('customers/kycdetails/' . md5($notification['order_id']));
                                                                } else {
                                                                    $link = base_url('customers/');
                                                                }
                                                            } else {
                                                                $task_label = isset($notification['task_type']) ? $notification['task_type'] : 'Notification';
                                                                $text = $task_label . '';
                                                            }
                                                            $notification_row_class = 'notification-row-unread';
                                                            if ($notification_bell_mode === 'admin' && isset($notification['admin_status']) && (int) $notification['admin_status'] === 1) {
                                                                $notification_row_class = 'notification-row-read';
                                                            } elseif ($notification_bell_mode === 'customer' && isset($notification['user_status']) && (int) $notification['user_status'] === 1) {
                                                                $notification_row_class = 'notification-row-read';
                                                            }
                                                    ?>
                                                            <div class="dropdown-item d-flex align-items-stretch p-0 <?= $notification_row_class; ?>">
                                                                <a class="d-flex flex-grow-1 view-notification align-items-center text-decoration-none text-default p-2" href="<?= $link; ?>" data-value="<?= md5('notify-' . $notification['id']); ?>">
                                                                    <div class="me-3 notifyimg bg-primary brround box-shadow-primary">
                                                                        <i class="fe fe-mail"></i>
                                                                    </div>
                                                                    <div class="mt-1 wd-80p">
                                                                        <h5 class="notification-label mb-1">
                                                                            <?= $notification['message']; ?>
                                                                        </h5>
                                                                        <span class="notification-subtext"><?= $text ?></span>
                                                                    </div>
                                                                </a>
                                                                <button type="button" class="btn btn-link text-muted py-2 px-2 dismiss-notification border-0 align-self-start" title="Remove" data-value="<?= md5('notify-' . $notification['id']); ?>" aria-label="Remove notification">&times;</button>
                                                            </div>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </div>
                                            </div>
                                        </div>
                                        <!-- FULL-SCREEN -->
                                        <div class="dropdown d-flex">
                                            <a class="nav-link icon full-screen-link nav-link-bg">
                                                <i class="fe fe-minimize fullscreen-button"></i>
                                            </a>
                                        </div>
                                        <div class="dropdown d-flex profile-1">
                                            <a href="javascript:void(0)" data-bs-toggle="dropdown" class="nav-link leading-none d-flex">
                                                <?php
                                                $user = getuser();
                                                $user_name = !empty($user['name']) ? $user['name'] : 'User';
                                                $first_letter = strtoupper(substr($user_name, 0, 1));

                                                // Get user photo
                                                if (!empty($user['photo'])) {
                                                    $photo_url = file_url($user['photo']);
                                                } else {
                                                    // Use default placeholder with user's initial
                                                    $photo_url = base_url('profileimage/?letter=' . $first_letter);
                                                }
                                                ?>
                                                <img src="<?= $photo_url; ?>" alt="profile-user"
                                                    class="avatar  profile-user brround cover-image"
                                                    onerror="this.src='<?= base_url('profileimage/?letter=' . $first_letter); ?>'">
                                            </a>
                                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-arrow">
                                                <?php /*?><div class="drop-heading">
                                                    <div class="text-center">
                                                        <h5 class="text-dark mb-0 fs-14 fw-semibold"><?= $this->session->name; ?></h5>
                                                        <small class="text-muted"><?= $this->session->role ?></small>
                                                    </div>
                                                </div>
                                                <div class="dropdown-divider m-0"></div><?php */ ?>
                                                <a class="dropdown-item" href="#">
                                                    <?= $this->session->name; ?>
                                                </a>
                                                <?php if ($this->session->role == 'customer') { ?>
                                                    <a class="dropdown-item" href="<?= base_url('profile/'); ?>">
                                                        <i class="dropdown-icon fe fe-user"></i> Profile
                                                    </a>
                                                <?php } elseif ($this->session->role == 'admin' || $this->session->role == 'superadmin') { ?>
                                                    <a class="dropdown-item" href="<?= base_url('users/myprofile/'); ?>">
                                                        <i class="dropdown-icon fe fe-user"></i> My Profile
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('editpassword/'); ?>">
                                                        <i class="dropdown-icon fe fe-lock"></i> Edit Password
                                                    </a>
                                                <?php } elseif ($this->session->role == 'employee' || $this->session->role == 'ca') { ?>
                                                    <a class="dropdown-item" href="<?= base_url('employees/myprofile/'); ?>">
                                                        <i class="dropdown-icon fe fe-user"></i> My Profile
                                                    </a>
                                                    <a class="dropdown-item" href="<?= base_url('editpassword/'); ?>">
                                                        <i class="dropdown-icon fe fe-lock"></i> Edit Password
                                                    </a>
                                                <?php } else { ?>
                                                    <a class="dropdown-item" href="<?= base_url('editpassword/'); ?>">
                                                        <i class="dropdown-icon fe fe-lock"></i> Edit Password
                                                    </a>
                                                <?php } ?>
                                                <?php /*?><a class="dropdown-item" href="email-inbox.html">
                                                    <i class="dropdown-icon fe fe-mail"></i> Inbox
                                                    <span class="badge bg-danger rounded-pill float-end">5</span>
                                                </a>
                                                <a class="dropdown-item" href="lockscreen.html">
                                                    <i class="dropdown-icon fe fe-lock"></i> Lockscreen
                                                </a><?php */ ?>
                                                <a class="dropdown-item" href="<?= base_url('logout/'); ?>">
                                                    <i class="dropdown-icon fa fa-lock"></i> Log out
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /app-Header -->