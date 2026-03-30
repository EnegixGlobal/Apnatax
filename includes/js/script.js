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

    function notificationBadgeCount($sup){
        var t=($sup.text()||'').trim();
        var n=parseInt(t,10);
        return isNaN(n)?0:n;
    }
    function setNotificationBadge($sup,n){
        $sup.text(n>0?String(n):'');
    }
    function syncNotificationDropdownToggle(){
        var hasRows=$('.notifications .notifications-menu .dropdown-item').length>0;
        if(hasRows){
            $('.notifications .nav-link.icon').attr('data-bs-toggle','dropdown');
        }else{
            $('.notifications .nav-link.icon').removeAttr('data-bs-toggle');
        }
    }

    $('body').on('click','.view-notification',function(e){
        e.preventDefault();
        var $a=$(this);
        var $row=$a.closest('.dropdown-item');
        var id=$a.data('value');
        var dest=($a.attr('href')||'').trim();
        var hasNav=dest.length>0 && dest!=='#' && !/^javascript:/i.test(dest);
        var $sup=$('.notifications .notification-badge-sup');
        $.ajax({
            type:"POST",
            url:url,
            data:{id:id,action:'read'},
            success:function(){
                if(hasNav){
                    window.location.assign(dest);
                    return;
                }
                /* keep row, mark read; badge only counts unread (customer + admin) */
                if($row.hasClass('notification-row-unread')){
                    $row.removeClass('notification-row-unread').addClass('notification-row-read');
                    setNotificationBadge($sup,notificationBadgeCount($sup)-1);
                }
            }
        });
    });
    $('body').on('click','.dismiss-notification',function(e){
        e.preventDefault();
        e.stopPropagation();
        var $row=$(this).closest('.dropdown-item');
        var id=$(this).data('value');
        var wasUnread=$row.hasClass('notification-row-unread');
        var $sup=$('.notifications .notification-badge-sup');
        $.ajax({
            type:"POST",
            url:url,
            data:{id:id,action:'delete'},
            success:function(){
                $row.remove();
                if(wasUnread){
                    setNotificationBadge($sup,notificationBadgeCount($sup)-1);
                }
                syncNotificationDropdownToggle();
            }
        });
    });
});
