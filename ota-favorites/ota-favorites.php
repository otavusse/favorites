<?php
/*
Plugin Name: Добавление статей в Избранное
Plugin URI: http://test.com
Description: Плагин добавляет для авторизированных пользователей ссылку к статьям, которая позволяет добавить статьи в список избранных статей
Version: 1.0
Author: Александр Блощинский
Author URI: http://копер.рф
*/

require __DIR__ . '/functions.php';
require __DIR__ . '/OTA_Favorites_Widget.php';

add_filter( 'the_content', 'ota_favorites_content' );
add_action( 'wp_enqueue_scripts', 'ota_favorites_scripts' );
add_action( 'wp_ajax_ota_add', 'wp_ajax_ota_add' );
add_action( 'wp_ajax_ota_del', 'wp_ajax_ota_del' );
add_action( 'wp_dashboard_setup', 'ota_favorites_dashboard_widget' );
add_action( 'admin_enqueue_scripts', 'ota_favorites_admin_scripts' );
add_action( 'wp_ajax_ota_del_all', 'wp_ajax_ota_del_all' );

add_action('widgets_init', 'ota_favorites_widget');
function ota_favorites_widget(){
    register_widget('OTA_Favorites_Widget');
}