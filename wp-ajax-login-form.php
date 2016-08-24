<?php
/* ====================================
 * Plugin Name: WP AJAX Login Form
 * Description: ajax login form
 * Version: 1.0.1
 * Author: Leshgan
 * Author URI: http://www.procomp-blog.ru/
 * ==================================== */


add_action("admin_init", "wpalf_display_recaptcha_options");
add_action("admin_menu", "wpalf_no_captcha_recaptcha_menu");
add_action('init', 'wpalf_init' );
add_action('wp_footer', 'wp_ajax_login_form_head_info');

// Internationalize the text strings used.
add_action( 'plugins_loaded', 'wpalf_i18n');
add_filter('plugin_action_links_' . plugin_basename(__FILE__), 'add_action_links');

// add reCaptch to register form
add_action("register_form", "wpalf_display_register_captcha");
add_filter("registration_errors", "wpalf_verify_registration_captcha", 10, 3);

// check reCaptch when logging in
add_filter("wp_authenticate_user", "verify_login_captcha", 10, 2);


require_once( plugin_dir_path( __FILE__ ) . 'asset/wpalf-widget.php');


function wpalf_i18n() {
    load_plugin_textdomain( 'wpalf-plugin', false, dirname( plugin_basename( __FILE__ ) ) . '/lang/' );
}

function wp_ajax_login_form_head_info(){
	if (is_user_logged_in()) {
        return;
    }
	require_once( plugin_dir_path( __FILE__ ) . 'asset/wpalf-forms.php');
}

function wpalf_init() {
    if (is_user_logged_in()) {
        return;
    }

	// Register and link CSS:
	wp_register_style('wpalf_css', plugins_url( 'css/wpalf-style.css', __FILE__ ));
	wp_enqueue_style( 'wpalf_css');

	// Register and link JS:
	wp_register_script( 'wpalf-login-script', plugins_url( 'js/wpalf-main.js', __FILE__ ) );
	wp_enqueue_script( 'wpalf-login-script', false, array('jquery'), false, true);

    // reCaptch script
    wp_register_script("wpalf_recaptcha", "https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit");
    wp_enqueue_script("wpalf_recaptcha");

    $gsitekey = get_option('captcha_site_key');
	wp_localize_script( 'wpalf-login-script', 'wpalf_login_object', array( 
        'ajaxurl' => admin_url( 'admin-ajax.php' ),
        'redirecturl' => $_SERVER['REQUEST_URI'],
        'loadingmessage' => __('Sending user info, please wait...'),
        'gsitekey' => $gsitekey
    ));

    // Enable the user with no privileges to run ajax_login() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxlogin', 'ajax_login' );
    // Enable the user with no privileges to run ajax_register() in AJAX
    add_action( 'wp_ajax_nopriv_ajaxregister', 'ajax_register' );

}

/**
*    Проверка правильности капчи
*/
function verify_login_captcha($user, $password) {
    if (isset($_POST['g-recaptcha-response'])) {
        $recaptcha_secret = get_option('captcha_secret_key');
        $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $_POST['g-recaptcha-response']);
        file_put_contents(plugin_dir_path(__FILE__ ) . 'google_response.txt', "\n -----" . date("F j, Y, g:i a") . "----- \n", FILE_APPEND);
        file_put_contents(plugin_dir_path(__FILE__ ) . 'google_response.txt', $response, FILE_APPEND);
        file_put_contents(plugin_dir_path(__FILE__ ) . 'google_response.txt', "\n -----", FILE_APPEND);

        if (is_array($response)) {
            $response = json_decode($response["body"], true);
        } 

        if (is_wp_error($response)) {
            $response["success"] = false;
        }

        if (true == $response["success"]) {
            return $user;
        } else {
            return new WP_Error("Captcha Invalid", __("<strong>ERROR</strong>: You are a bot"));
        } 
    } else {
        return new WP_Error("Captcha Invalid", __("<strong>ERROR</strong>: You are a bot. If not then enable JavaScript"));
    }   
}


/**
*    Логин
*/
function ajax_login() {
    // First check the nonce, if it fails the function will break
    check_ajax_referer( 'ajax-login-nonce', 'security' );

    // Nonce is checked, get the POST data and sign user on
    $info = array();
    $info['user_login'] = $_POST['username'];
    $info['user_password'] = $_POST['password'];
    $info['remember'] = true;

    $user_signon = wp_signon( $info, false );
    if ( is_wp_error($user_signon) ){
        echo json_encode(array('loggedin'=>false, 'message'=>__('Wrong username or password.'), 'wp_message' => __($user_signon->get_error_message()), 'code' => 503 ) );
    } else {
        echo json_encode(array('loggedin'=>true, 'message'=>__('Login successful, redirecting...'), 'code' => 200));
    }
    die();
}

/**
*  Валидация данных и запуск регистрации пользователя
*/
function ajax_register() {
    if ( 'POST' == $_SERVER['REQUEST_METHOD'] ) {
        echo "123";
        $email = $_POST['email'];
        $login = sanitize_text_field( $_POST['username'] );

        $errors = new WP_Error();
       
        if (! is_email($email) ) {
            $errors->add('email', __('Wrong e-mail'));
            return $errors;
        }

        if ( username_exists( $login ) ) {
            $errors->add('login', __('Login already exists'));
            return $errors;
        }

        if ( email_exists( $email ) ) {
            $errors->add('email_exists', __('E-mail already exists'));
            return $errors;
        }

        // Формирование пароля, который будет отправлен на почту пользователя...
        $password = wp_generate_password( 12, false );

        $user_data = array(
            'user_login' => $login,
            'user_email' => $email,
            'user_pass' => $password,
        );

        $user_id = wp_insert_user( $user_data );
        wp_new_user_notification( $user_id );

        if (! is_wp_error($user_id) ) {
            echo json_encode( array('code' => 200) );
        }

        // return $user_id;
        
    }
    die();
}


/**
    Добавляем в настройки админки пункт "AJAX Login Form" 
*/
function wpalf_no_captcha_recaptcha_menu() {
    add_options_page("WPALF Options", "AJAX Login Form", "manage_options", "wpalf-options", 
        "wpalf_recaptcha_options_page", "", 100);
}

/**
* Отображение страницы настроек плагина
*/
function wpalf_recaptcha_options_page() { ?>
    <div class="wrap">
        <h1>reCaptcha Options</h1>
        <form method="post" action="options.php">
        <?php settings_fields("header_section");
            do_settings_sections("wpalf-options");
            submit_button(); ?>          
        </form>
    </div>
<?php }


function wpalf_display_recaptcha_options() {
    add_settings_section("header_section", "Keys", "wpalf_display_recaptcha_content", "wpalf-options");
    add_settings_field("captcha_site_key", __("Site Key"), "wpalf_display_captcha_site_key_element", "wpalf-options", "header_section");
    add_settings_field("captcha_secret_key", __("Secret Key"), "wpalf_display_captcha_secret_key_element", "wpalf-options", "header_section");
    register_setting("header_section", "captcha_site_key");
    register_setting("header_section", "captcha_secret_key");
}

function wpalf_display_recaptcha_content() {
    echo __('<p>You need to <a href="https://www.google.com/recaptcha/admin" rel="external">register you domain</a> and get keys to make this plugin work.</p>');
    echo __("Enter the key details below");
}

function wpalf_display_captcha_site_key_element() { ?>
    <input type="text" name="captcha_site_key" id="captcha_site_key" value="<?php echo get_option('captcha_site_key'); ?>" />
<?php }

function wpalf_display_captcha_secret_key_element() { ?>
    <input type="text" name="captcha_secret_key" id="captcha_secret_key" value="<?php echo get_option('captcha_secret_key'); ?>" />
<?php }


function wpalf_display_register_captcha() { 
?>
    <div class="g-recaptcha" data-sitekey="<?php echo get_option('captcha_site_key'); ?>"></div>
<?php 
}

function wpalf_verify_registration_captcha($errors, $sanitized_user_login, $user_email) {
    if (isset($_POST['g-recaptcha-response'])) {
        $recaptcha_secret = get_option('captcha_secret_key');
        $response = wp_remote_get("https://www.google.com/recaptcha/api/siteverify?secret=". $recaptcha_secret ."&response=". $_POST['g-recaptcha-response']);

        file_put_contents(plugin_dir_path(__FILE__ ) . 'google_response.txt', "\n registration: -----" . date("F j, Y, g:i a") . "----- \n", FILE_APPEND);
        file_put_contents(plugin_dir_path(__FILE__ ) . 'google_response.txt', $response, FILE_APPEND);
        file_put_contents(plugin_dir_path(__FILE__ ) . 'google_response.txt', "\n -----", FILE_APPEND);

        if (is_array($response)) {
            $response = json_decode($response["body"], true);
        } 

        if (is_wp_error($response)) {
            $response["success"] = false;
        }

        if (true == $response["success"]) {
            return $errors;
        } else {
            $errors->add("Captcha Invalid", __("<strong>ERROR</strong>: You are a bot"));
        }
    } else {
        $errors->add("Captcha Invalid", __("<strong>ERROR</strong>: You are a bot. If not then enable JavaScript"));
    }
    return $errors;
}

/**
* Добавлять ссылку "Настрйки" для плагина в списке всех плагинов
*/
function add_action_links($links) {
    if ( !current_user_can('manage_options') ) {
            return $links;
        }
    return array_merge(
        $links,
        array('<a href="' . admin_url( 'options-general.php?page=wpalf-options' ) . '">' . __('Settings') . '</a>')
    );
}


/* 
В функции регистрации сдеать проверки:
$username   =   sanitize_user( $_POST['username'] );
$password   =   esc_attr( $_POST['password'] );
$email      =   sanitize_email( $_POST['email'] );
$nickname   =   sanitize_text_field( $_POST['nickname'] );

*/

