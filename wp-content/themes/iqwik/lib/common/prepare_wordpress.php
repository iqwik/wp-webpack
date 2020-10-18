<?php
//убираем пробелы в коде
function sanitize_output($buffer) {
    $search = [
        '/\>[^\S ]+/s',     // strip whitespaces after tags, except space
        '/[^\S ]+\</s',     // strip whitespaces before tags, except space
        '/(\s)+/s',         // shorten multiple whitespace sequences
        '/<!--(.|\s)*?-->/' // Remove HTML comments
    ];
    $replace = ['>', '<', '\\1', ''];
    $buffer = preg_replace($search, $replace, $buffer);
    return $buffer;
}
/******************************************************** *************************************************************/
/***************************************** подготовим WORDPRESS - Optimization ****************************************/
/******************************************************** *************************************************************/
// отключение фоновых проверок обновлений для версии wp и plugins в админке - ускорение
if(is_admin()) {
    // отключим проверку обновлений при любом заходе в админку...
    remove_action('admin_init', '_maybe_update_core');
    remove_action('admin_init', '_maybe_update_plugins');
    remove_action('admin_init', '_maybe_update_themes');
    // отключим проверку обновлений при заходе на специальную страницу в админке...
    remove_action('load-plugins.php', 'wp_update_plugins');
    remove_action('load-themes.php', 'wp_update_themes');
    // оставим принудительную проверку при заходе на страницу обновлений...
    //remove_action('load-update-core.php', 'wp_update_plugins');
    //remove_action('load-update-core.php', 'wp_update_themes');
    // внутренняя страница админки "Update/Install Plugin" или "Update/Install Theme" - оставим не мешает...
    //remove_action('load-update.php', 'wp_update_plugins');
    //remove_action('load-update.php', 'wp_update_themes');
    // событие крона не трогаем, через него будет проверяться наличие обновлений - тут все отлично!
    //remove_action('wp_version_check', 'wp_version_check');
    //remove_action('wp_update_plugins', 'wp_update_plugins');
    //remove_action('wp_update_themes', 'wp_update_themes');
    /**
     * отключим проверку необходимости обновить браузер в консоли - мы всегда юзаем топовые браузеры!
     * эта проверка происходит раз в неделю...
     */
    add_filter('pre_site_transient_browser_'. md5($_SERVER['HTTP_USER_AGENT']), '__return_true');
}
remove_action('wp_head', 'wp_generator');  // запрет показа версии вордпресс
remove_action('wp_head', 'wlwmanifest_link'); // wlwmanifest
remove_action('wp_head', 'wp_shortlink_wp_head', 10); // короткая ссылка, ссылка без ЧПУ
remove_action('wp_head', 'print_emoji_detection_script', 7); //отключаем emoji
remove_action('admin_print_scripts', 'print_emoji_detection_script'); //отключаем emoji
remove_action('wp_print_styles', 'print_emoji_styles'); //отключаем emoji
remove_action('admin_print_styles', 'print_emoji_styles'); //отключаем emoji
//remove_action('wp_head', 'wp_oembed_add_discovery_links'); //wp-embed
//remove_action('wp_head', 'wp_oembed_add_host_js'); //wp-embed
remove_action('wp_head', 'rsd_link'); //xmlrpc EditUri
remove_action('wp_head', 'wp_resource_hints', 2); // rel=dns-prefetch href=//s.w.org
// Отключаем сам REST API
add_filter('rest_enabled', '__return_true'); //  если влючить REST API заменить на __return_true
// Отключаем фильтры REST API
remove_action('xmlrpc_rsd_apis', 'rest_output_rsd');
remove_action('wp_head', 'rest_output_link_wp_head', 10);
remove_action('template_redirect', 'rest_output_link_header', 11);
remove_action('auth_cookie_malformed', 'rest_cookie_collect_status');
remove_action('auth_cookie_expired', 'rest_cookie_collect_status');
remove_action('auth_cookie_bad_username', 'rest_cookie_collect_status');
remove_action('auth_cookie_bad_hash', 'rest_cookie_collect_status');
remove_action('auth_cookie_valid', 'rest_cookie_collect_status');
remove_filter('rest_authentication_errors', 'rest_cookie_check_errors', 100);
// Отключаем события REST API
remove_action('init', 'rest_api_init');
remove_action('rest_api_init', 'rest_api_default_filters', 10);
remove_action('parse_request', 'rest_api_loaded');
// Отключаем Embeds связанные с REST API
remove_action('rest_api_init', 'wp_oembed_register_route');
remove_filter('rest_pre_serve_request', '_oembed_rest_pre_serve_request', 10);
remove_action('wp_head', 'wp_oembed_add_discovery_links');
remove_action('wp_head', 'wp_oembed_add_host_js');
add_action('wp_footer', 'wp_oembed_add_discovery_links');
add_action('wp_footer', 'wp_oembed_add_host_js');
add_filter('use_block_editor_for_post', '__return_false'); // отключаем редактор gutenberg
remove_filter('the_content', 'wpautop'); // Отключаем автоформатирование в полном посте
remove_filter('the_excerpt', 'wpautop'); // Отключаем автоформатирование в кратком(анонсе) посте
// отключаем создание миниатюр файлов для указанных размеров
//add_filter('intermediate_image_sizes', 'delete_intermediate_image_sizes');
//function delete_intermediate_image_sizes($sizes){
//    // размеры которые нужно удалить
//    return array_diff($sizes, [
//        'medium_large',
//        'large',
//    ]);
//}
// отключение wp-block-library
function wpassist_remove_block_library_css() {
    wp_dequeue_style('wp-block-library');
}
add_action('wp_enqueue_scripts', 'wpassist_remove_block_library_css');

// Админка | Удаление виджетов из Консоли WordPress
add_action('wp_dashboard_setup', 'clear_wp_dash');
function clear_wp_dash() {
    $dash_side   = & $GLOBALS['wp_meta_boxes']['dashboard']['side']['core'];
    $dash_normal = & $GLOBALS['wp_meta_boxes']['dashboard']['normal']['core'];
    unset($dash_side['dashboard_quick_press']);       // Быстрая публикация
    unset($dash_side['dashboard_recent_drafts']);     // Последние черновики
    unset($dash_side['dashboard_primary']);           // Блог WordPress
    unset($dash_side['dashboard_secondary']);         // Другие Новости WordPress
    unset($dash_normal['dashboard_incoming_links']);  // Входящие ссылки
    unset($dash_normal['dashboard_right_now']);       // Прямо сейчас
    unset($dash_normal['dashboard_recent_comments']); // Последние комментарии
    unset($dash_normal['dashboard_plugins']);         // Последние Плагины
    unset($dash_normal['dashboard_activity']);        // Активность
}
// Админка | Удаление виджета "Добро пожаловать"
remove_action('welcome_panel', 'wp_welcome_panel');
// включение поддержки миниатюр у произвольных типов записей
if (function_exists('add_theme_support'))
    add_theme_support('post-thumbnails',['post', 'page']);

//удаляем лишние колонки в админке для постов
//add_filter('manage_post_posts_columns', 'drop_unwanted_columns_filter');
//function drop_unwanted_columns_filter($columns) {
//    unset($columns['author']);
//    unset($columns['tags']);
//    unset($columns['comments']);
//    return $columns;
//
//}
// Удаление файлов license.txt и readme.html для защиты
if (is_admin() && ! defined('DOING_AJAX')) {
    $license_file = ABSPATH .'/license.txt';
    $readme_file = ABSPATH .'/readme.html';
    if (file_exists($license_file) && current_user_can('manage_options')) {
        $deleted = unlink($license_file) && unlink($readme_file);
        if (!$deleted) {
            $GLOBALS['readmedel'] = 'Не удалось удалить файлы: license.txt и readme.html из папки `'. ABSPATH .'`. Удалите их вручную!';
        } else {
            $GLOBALS['readmedel'] = 'Файлы: license.txt и readme.html удалены из из папки `'. ABSPATH .'`.';
        }
        add_action('admin_notices', function() {
            echo '<div class="error is-dismissible"><p>'. $GLOBALS['readmedel'] .'</p></div>';
        });
    }
}
// удаление комментариев на стр редактирования в админке
function remove_comments_in_admin() {
    remove_meta_box('commentstatusdiv' , 'post' , 'normal');
//    remove_meta_box('commentstatusdiv' , 'menu_cpt' , 'normal');
    remove_meta_box('commentstatusdiv' , 'page' , 'normal');
    remove_meta_box('commentsdiv' , 'post' , 'normal');
//    remove_meta_box('commentsdiv' , 'menu_cpt' , 'normal');
    remove_meta_box('commentsdiv' , 'page' , 'normal');
}
add_action('admin_menu' , 'remove_comments_in_admin');
/** общие | подготовка WP */
// удалим название сайта для всех страниц в <title> (для seo)
add_filter('document_title_parts', function($parts) {
    if (isset($parts['site'])) unset($parts['site']);
    return $parts;
});
// удалим описание из <title> (для seo) для главной страницы
add_filter('document_title_parts', function($title) {
    if (isset($title['tagline'])) unset($title['tagline']);
    return $title;
});
// меняем "Записи" на "Подкасты" (название раздела в меню админки)
//function edit_admin_menus() {
//    global $menu, $submenu;
//    $menu[5][0] = 'Подкасты';
//    $menu[5][6] = 'dashicons-megaphone';
//    $submenu['edit.php'][5][0] = 'Все подкасты';
//    $submenu['edit.php'][10][0] = 'Добавить подкаст';;
//}
//add_action('admin_menu', 'edit_admin_menus');
