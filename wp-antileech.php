<?php
/*
Plugin Name: WP Antileech
Author: Svetlana Yushkina
Author URI: https://github.com/YushkinaS
*/

class File_Download {

    function __construct() {
        add_action( 'init',                               array( $this, 'register_post_types' ), 99 );
        add_action( 'init',                               array( $this, 'add_rewrite_rules' ), 99 );
        add_filter( 'template_include',                   array( $this, 'use_custom_template' ), 99 );
        add_filter( 'rwmb_meta_boxes',                    array( $this, 'set_metaboxes' ) );    
        if ( is_admin() ) {
			register_activation_hook( __FILE__,           array( $this, 'activate' ) );
		}
    }
    
    function activate() {
        $download_page = get_page_by_path('download');
        if (!$download_page) {
            $post_data = array(
                'post_title'    => 'download',
                'post_content'  => '',
                'post_status'   => 'publish',
                'post_type'     => 'page'
            );
            $post_id = wp_insert_post( $post_data );
        }
    }   

    function set_metaboxes($meta_boxes){
        $meta_boxes[] = array(
            'title'      => 'Ссылка на файл',
            'post_types' => 'file_download',
            'fields'     => array(

                array(
                    'id'   => 'link_text',
                    'name' => 'Текст ссылки',
                    'type' => 'text',
                    'size' => '60'
                ),
                            
                array(
                    'id'   => 'file_input',
                    'name' => 'Ссылка',
                    'type' => 'file_input',
                ),

            ),
        );
        return $meta_boxes;
    }

    function use_custom_template( $template ) {
        if ( is_singular( 'file_download' ) ) {
            $template = plugin_dir_path( __FILE__ ) . '/templates/single-file_download.php';
        }

        if ( is_page( 'download' ) ) {
            $template = plugin_dir_path( __FILE__ ) . '/templates/download.php';
        }
        
        return $template;
    }
  
    function register_post_types() {
        register_post_type( 'file_download', array(
            'labels'       => array(
                'name'               => 'Файлы', // основное название для типа записи
                'singular_name'      => 'Файл', // название для одной записи этого типа
                'add_new'            => 'Добавить новый', // для добавления новой записи
                'add_new_item'       => 'Добавить новый файл', // заголовка у вновь создаваемой записи в админ-панели.
                'edit_item'          => 'Редактировать файл', // для редактирования типа записи
                'new_item'           => 'Новый файл', // текст новой записи
                'view_item'          => 'Просмотреть файл', // для просмотра записи этого типа.
                'search_items'       => 'Поиск файла', // для поиска по этим типам записи
                'not_found'          => 'Файл не найден', // если в результате поиска ничего не было найдено
                'not_found_in_trash' => 'Файл не найден в корзине', // если не было найдено в корзине
                'menu_name'          => 'Файлы', // название меню

            ),
            'show_in_menu' => true,
            'show_ui'      => true,
            'public'       => true,
            'hierarchical' => true,
            'has_archive'  => 'files',
            'supports'     => array( 'title','editor','author','thumbnail','excerpt','comments' ),
        ) );
    }
        
    function add_rewrite_rules() {
        add_rewrite_tag('%fileid%', '([^&]+)');
        add_rewrite_tag('%hash%', '([^&]+)');
        add_rewrite_rule( '^download/([^/]*)/([^/]*)/?$', 'index.php?pagename=download&fileid=$matches[1]&hash=$matches[2]', 'top' );
    }
}

$file_download = new File_Download;