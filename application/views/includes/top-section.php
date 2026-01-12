<!doctype html>
<html lang="en" dir="ltr" class="studio-nine-color">

<head>

    <!-- META DATA -->
    <meta charset="UTF-8">
    <meta name='viewport' content='width=device-width, initial-scale=1.0, user-scalable=0'>
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    
    <!-- FAVICON -->
    <link rel="shortcut icon" href="<?= file_url('assets/images/fav.png'); ?>" type="image/png">

    <!-- TITLE -->
    <title><?= $title.' | '.PROJECT_NAME; ?></title>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Cardo:ital,wght@0,400;0,700;1,400&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,400&display=swap" rel="stylesheet">

    <!-- BOOTSTRAP CSS -->
    <link id="style" href="<?= file_url('includes/plugins/bootstrap/css/bootstrap.min.css'); ?>" rel="stylesheet" />

    <!-- STYLE CSS -->
    <link href="<?= file_url('includes/css/style.css'); ?>" rel="stylesheet" />
    <link href="<?= file_url('includes/css/dark-style.css'); ?>" rel="stylesheet" />
    <link href="<?= file_url('includes/css/transparent-style.css'); ?>" rel="stylesheet">
    <link href="<?= file_url('includes/css/skin-modes.css'); ?>" rel="stylesheet" />

    <!--- FONT-ICONS CSS -->
    <link href="<?= file_url('includes/css/icons.css'); ?>" rel="stylesheet" />

    <!-- COLOR SKIN CSS -->
    <link id="theme" rel="stylesheet" type="text/css" media="all" href="<?= file_url('includes/colors/color1.css'); ?>" />
    <?php
        if(!empty($styles)){
            foreach($styles as $key=>$style){
                if($key=="link"){
                    if(is_array($style)){
                        foreach($style as $single_style){
                            echo "<link rel='stylesheet' href='$single_style'>\n\t";
                        }
                    }
                    else{
                        echo "<link rel='stylesheet' href='$style'>\n\t";
                    }
                }
                elseif($key=="file"){
                    if(is_array($style)){
                        foreach($style as $single_style){
                            echo "<link rel='stylesheet' href='".file_url("$single_style")."'>\n\t";
                        }
                    }
                    else{
                        echo "<link rel='stylesheet' href='".file_url("$style")."'>\n\t";
                    }
                }
            }
        }
    ?>   
        <!-- Custom style -->
        <link rel="stylesheet" href="<?= file_url('includes/css/custom.css'); ?>">  
        <!-- Custom style -->
        <link rel="stylesheet" href="<?= file_url('includes/css/customs.css'); ?>">  
    <!-- JQUERY JS -->
    <script src="<?= file_url('includes/js/jquery.min.js'); ?>"></script>
    <?php
        if(!empty($top_script)){
            foreach($top_script as $key=>$script){
                if($key=="link"){
                    if(is_array($script)){
                        foreach($script as $single_script){
                            echo "<script src='$single_script'></script>\n\t";
                        }
                    }
                    else{
                        echo "<script src='$script'></script>\n\t";
                    }
                }
                elseif($key=="file"){
                    if(is_array($script)){
                        foreach($script as $single_script){
                            echo "<script src='".file_url("$single_script")."'></script>\n\t";
                        }
                    }
                    else{
                        echo "<script src='".file_url("$script")."'></script>\n\t";
                    }
                }
            }
        }
    ?>
</head>

<body class="app sidebar-mini ltr <?= !empty($sidebartoggle)?$sidebartoggle:''; ?>">
    <?php
        if(isset($page) && $page=='login'){
    ?>
    <!-- BACKGROUND-IMAGE -->
    <div class="login-img">

    <?php
        }
    ?>
    <!-- GLOBAL-LOADER -->
    <div id="global-loader">
        <img src="<?= file_url('includes/images/loader.svg'); ?>" class="loader-img" alt="Loader">
    </div>
    <!-- /GLOBAL-LOADER -->
    <?php
        $notify=$msg=$nDataType='';
        if($this->session->flashdata('msg')!==NULL){
            $msg=$this->session->flashdata('msg');
            $nDataType="success";
            $notify='notify-btn';
        }
        if($this->session->flashdata('err_msg')!==NULL){
            $msg=$this->session->flashdata('err_msg');
            $nDataType="error";
            $notify='notify-btn';
        }
    ?>


    <div class="notifications d-none">
        <button class="<?= $notify ?>" data-type="<?= $nDataType ?>" onClick="notifyMsg('<?= $msg; ?>','<?= $nDataType; ?>')"><?= $msg; ?></button>
    </div>
