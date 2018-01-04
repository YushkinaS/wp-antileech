<?php
function remote_filesize($url, $user = "", $pw = ""){
    ob_start();
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_NOBODY, 1);
    if(!empty($user) && !empty($pw))
    {
        $headers = array('Authorization: Basic ' .  base64_encode("$user:$pw"));
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    $ok = curl_exec($ch);
    curl_close($ch);
    $head = ob_get_contents();
    ob_end_clean();
    $regex = '/Content-Length:\s([0-9].+?)\s/';
    $count = preg_match($regex, $head, $matches);
    return isset($matches[1]) ? $matches[1] : "unknown";
}

error_reporting(0);
//get_header();
$hash = get_query_var('hash');
$file_post_id = get_query_var('fileid');
$file_post = get_post($file_post_id);
if ($file_post && $file_post->post_type=='file_download') {
    $file = get_transient('file_'.$hash);

    if ($file) {
        $dl_count = get_post_meta($file_post_id,'dlc_'.$file,true);
        $dl_count = $dl_count +1;
        update_post_meta( $file_post_id, 'dlc_'.$file, $dl_count);      
        
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $file);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        $content = curl_exec($curl);
        curl_close($curl);

        $fsize = remote_filesize($file);
        $fname = strrchr($file,'/');
        Header("HTTP/1.1 200 OK");
        Header("Connection: close");
        Header("Content-Type: application/octet-stream");
        Header("Accept-Ranges: bytes");
        Header("Content-Disposition: Attachment; filename=".$fname);
        Header("Content-Length: ".$fsize);
        echo $content;
        wp_die();
    }
    else {
        wp_redirect( get_permalink($file_post_id) ); 
        exit;
    }
}
else {
        wp_redirect( home_url() ); 
        exit;
    } 
?>