<?php
/**
 * Author: Artem Zubarev *
 **/

/******************************************************** *************************************************************/
/****************************************************** CONSTs ********************************************************/
/******************************************************** *************************************************************/
define('TEMPLATE_URL', get_bloginfo('template_url'));
define('SITE_URL', get_site_url());
define('SITE_NAME', get_bloginfo('name'));
define('ADMIN_EMAIL', get_option('admin_email'));
include_once 'version.php';
// добавить в wp-config.php
//define( 'WP_POST_REVISIONS', 0 ); // отключение ревизий
//// SMTP
//define( 'SMTP_USER', 'example@mail.ru' );
//define( 'SMTP_PASS', '123456' );
//define( 'SMTP_HOST', 'ssl://smtp.mail.ru' );
//define( 'SMTP_FROM', 'example@mail.ru' );
//define( 'SMTP_NAME', 'El Bonic' );
//define( 'SMTP_PORT', 465 );
//define( 'SMTP_SECURE', 'ssl' );
//define( 'SMTP_AUTH', true ); // Включение/отключение шифрования
//define( 'SMTP_DEBUG', 0 ); // Режим отладки (0, 1, 2)

/* ******************************************* Настройка SMTP **********************************************************
function send_smtp_phpmailer( PHPMailer $phpmailer ) {
    $phpmailer->isSMTP();
    $phpmailer->Host       = SMTP_HOST;
    $phpmailer->SMTPAuth   = SMTP_AUTH;
    $phpmailer->Port       = SMTP_PORT;
    $phpmailer->Username   = SMTP_USER;
    $phpmailer->Password   = SMTP_PASS;
    $phpmailer->SMTPSecure = SMTP_SECURE;
    $phpmailer->From       = SMTP_FROM;
    $phpmailer->FromName   = SMTP_NAME;
    $phpmailer->SMTPDebug  = SMTP_DEBUG;
}
add_action( 'phpmailer_init', 'send_smtp_phpmailer' );
/***************************************** подготовим WORDPRESS *******************************************************/
add_action('wp_enqueue_scripts', function() {
    // отменяем зарегистрированный jQuery
    wp_deregister_script('jquery-core');
    wp_deregister_script('jquery');
    wp_enqueue_script('main', get_template_directory_uri() . '/assets/public/js/main.js', [], BUNDLE_VERSION, true);
});
//add_action('wp', function()
//{
//    add_action('wp_enqueue_scripts', function()
//    {
//        if (is_category() || is_search() || is_page('cabinet') || is_single())
//            wp_enqueue_script('episodes-js', get_template_directory_uri() . '/assets/js/episodes.min.js', ['jquery'], null);
//        elseif (is_page('cabinet'))
//            wp_enqueue_script('cabinet-js', get_template_directory_uri() . '/assets/js/cabinet.min.js', ['jquery'], filemtime(get_theme_file_path('assets/js/cabinet.min.js')));
//    });
//});
add_action('after_setup_theme', function() {
    remove_action('wp_head','wp_print_scripts');
    remove_action('wp_head','wp_print_head_scripts',9);
    remove_action('wp_head','wp_enqueue_scripts',1);
    add_action('wp_footer','wp_print_scripts',5);
    add_action('wp_footer','wp_print_head_scripts',5);
    add_action('wp_footer','wp_enqueue_scripts',5);
});
# удаляет версию WP из преданного URL у скриптов и стилей
add_filter( 'script_loader_src', 'remove_wp_version_from_src' );
add_filter( 'style_loader_src', 'remove_wp_version_from_src' );
function remove_wp_version_from_src($src) {
    global $wp_version;
    parse_str(parse_url($src, PHP_URL_QUERY), $query);
    if (! empty($query['ver']) && $query['ver'] === $wp_version) {
        $src = remove_query_arg('ver', $src);
    }
    return $src;
}
include_once TEMPLATEPATH . '/lib/common/prepare_wordpress.php'; // очищаем от лишнего мусора wordpress
// регистрируем основную ссылку на ajax
add_action('wp_footer', function() {
    ?><script type="text/javascript">window.wp_data = <?php echo json_encode(['ajax_url' => admin_url('admin-ajax.php')]);?>;</script><?php
});
