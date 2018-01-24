jQuery( document ).ready(function() {

    //Prevent when image src is foced from http to https, image can't display issue.
    jQuery('.image-stay-http').each(function(){
        var $url = jQuery(this).attr('src');
        var $cleanUrl = $url.split('://').pop();
        $newUrl = 'http://' + $cleanUrl;
        jQuery(this).attr('src', $newUrl);
    });

    //Prevent when link href is foced from http to https, image can't display issue.
    jQuery('.url-stay-http').each(function(){
        var $url = jQuery(this).attr('href');
        var $cleanUrl = $url.split('://').pop();
        $newUrl = 'http://' + $cleanUrl;
        jQuery(this).attr('href', $newUrl);
    });
    

});