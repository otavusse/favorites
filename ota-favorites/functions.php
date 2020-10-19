<?php

function ota_favorites_dashboard_widget() {
    wp_add_dashboard_widget( 'ota_favorites_dashboard', 'Ваш список Избранного', 'ota_show_dashboard_widget' );
}

function ota_show_dashboard_widget() {
    $user = wp_get_current_user();
    $favorites = get_user_meta( $user->ID, 'ota_favorites' );
    if ( !$favorites ) {
        echo 'Список пуст';
        return;
    }
    $img_src = plugins_url( '/img/loader.gif', __FILE__ );
    echo '<ul>';
    foreach ( $favorites as $favorite ) {
        echo '<li class="cat-item-' . $favorite . '">
        <a href="' . get_permalink( $favorite ) . '" target="_blank">' . get_the_title( $favorite ) . '</a>
        <span><a href="#" data-post="' . $favorite . '" class="ota-favorites-del">&#10008;</a></span>
        <span class="ota-favorites-hidden"><img src="' . $img_src . '" alt=""></span>
        </li>';
    }
    echo '</ul>';
    echo '<div class="ota-favorites-del-all"><button class="button" id="ota-favorites-del-all">Очистить список</button><span class="ota-favorites-hidden"><img src="' . $img_src . '" alt=""></span></div>';
}

function ota_favorites_content( $content ) {
    if ( !is_single() || !is_user_logged_in() ) return $content;
    $img_src = plugins_url( '/img/loader.gif', __FILE__ );

    global $post;
    if ( ota_is_favorites( $post->ID ) ) {
        return '<p class="ota-favorites-link"><span class="ota-favorites-hidden"><img src="' . $img_src . '" alt=""></span><a data-action="del" href="#">Удалить из Избранного</a></p>' . $content;
    }

    return '<p class="ota-favorites-link"><span class="ota-favorites-hidden"><img src="' . $img_src . '" alt=""></span><a data-action="add" href="#">Добавить в Избранное</a></p>' . $content;
}

function ota_favorites_admin_scripts( $hook ) {
    if ( $hook != 'index.php' ) {
        return;
    }
    wp_enqueue_script( 'ota-favorites-admin-scripts', plugins_url( '/js/ota-favorites-admin-scripts.js', __FILE__ ), array( 'jquery' ), null, true );
    wp_enqueue_style( 'ota-favorites-admin-style', plugins_url( '/css/ota-favorites-admin-style.css', __FILE__ ) );
    wp_localize_script ( 'ota-favorites-admin-scripts', 'otaFavorites', ['nonce' => wp_create_nonce( 'ota-favorites' )] );
}

function ota_favorites_scripts() {
    if ( !is_user_logged_in() ) return;
    //if ( !is_single() || !is_user_logged_in() ) return;
    wp_enqueue_script( 'ota-favorites-scripts', plugins_url( '/js/ota-favorites-scripts.js', __FILE__ ), array( 'jquery' ), null, true );
    wp_enqueue_style( 'ota-favorites-style', plugins_url( '/css/ota-favorites-style.css', __FILE__ ) );
    global $post;
    wp_localize_script ( 'ota-favorites-scripts', 'otaFavorites', ['url' => admin_url ( 'admin-ajax.php' ), 'nonce' => wp_create_nonce( 'ota-favorites' ), 'postId' => $post->ID] );
}

function wp_ajax_ota_add () {
    if ( !wp_verify_nonce ( $_POST['security'], 'ota-favorites' ) ) {
        wp_die ( 'Ошибка безопасности!' );

    }
    $post_id = ( int )$_POST['postId'];
    $user = wp_get_current_user();

    if ( ota_is_favorites( $post_id ) ) wp_die();

    if ( add_user_meta( $user->ID, 'ota_favorites', $post_id ) ) {
        wp_die( 'Добавлено' );

    }

    wp_die( 'Ошибка добавления' );
}

function wp_ajax_ota_del_all () {
    if ( !wp_verify_nonce ( $_POST['security'], 'ota-favorites' ) ) {
        wp_die ( 'Ошибка безопасности!' );
    }
    $user = wp_get_current_user();

    if ( delete_metadata( 'user', $user->ID, 'ota_favorites' ) ) {
        wp_die( 'Список очищен' );

    } else {
        wp_die( 'Ошибка удаления' );
    }

}

function wp_ajax_ota_del () {
    if ( !wp_verify_nonce ( $_POST['security'], 'ota-favorites' ) ) {
        wp_die ( 'Ошибка безопасности!' );
    }
    $post_id = ( int )$_POST['postId'];
    $user = wp_get_current_user();

    if ( !ota_is_favorites( $post_id ) ) wp_die();

    if ( delete_user_meta( $user->ID, 'ota_favorites', $post_id ) ) {
        wp_die( 'Удалено' );

    }

    wp_die( 'Ошибка удаления' );
}

function ota_is_favorites( $post_id ) {
    $user = wp_get_current_user();
    $favorites = get_user_meta( $user->ID, 'ota_favorites' );
    foreach ( $favorites as $favorite ) {
        if ( $favorite == $post_id ) return true;
    }
    return false;
}
