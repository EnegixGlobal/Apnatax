// JavaScript Document
$(document).ready(function(){
    var baseUrl = window.location.protocol + "//" + window.location.host;
    if(baseUrl=='http://localhost'){
       baseUrl=$('#base_url').val();
    }
    else{
        baseUrl+='/';
    }
    url=baseUrl+'updatenotification/';
    $('body').on('click','.view-notification',function(){
        var id=$(this).data('value');
        $.ajax({
            type:"POST",
            url:url,
            data:{id:id},
            success:function(){
                
            }
        });
    });
});