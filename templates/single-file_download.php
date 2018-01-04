<?php
get_header();

$link_text = get_post_meta($post->ID,'link_text',1);
$file_input = get_post_meta($post->ID,'file_input',1);
$file_dl_count = get_post_meta($post->ID,'dlc_'.$file_input,1);
if (empty($file_dl_count)) {
    $file_dl_count = '0';
}
$hash = md5(microtime().'1');
set_transient('file_'.$hash,$file_input,20*MINUTE_IN_SECONDS);
$file_link = '/download/'.$post->ID.'/'.$hash;

?>      
<h1><?php echo $post->post_title; ?></h1>
<div><?php echo $post->post_content; ?></div>

<?php if ($file_input) : ?>
<div>
    <a target="_blank" href="<?php echo $file_link; ?>"><?php echo $link_text; ?></a>
    <span>Скачано <?php echo $file_dl_count; ?> раз</span>
</div>
<?php endif; ?>

<?php get_footer(); ?>