// JavaScript Document
$(document).ready(function(){
    $('.notify-btn').trigger('click');
    if($('#fileuploader').length>0){
        $('#fileuploader').FancyFileUpload({
            params: {
                action: 'fileuploader'
            },
            maxfilesize: 1000000
        });
    }
});


function notifyMsg(message,type) {
    notif({
        msg: message,
        type: type,
        position: "center"
    });
}
