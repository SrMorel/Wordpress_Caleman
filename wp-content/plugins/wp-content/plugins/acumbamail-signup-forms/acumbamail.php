<?php

/*
   Plugin Name: Acumbamail
   Plugin URI: https://acumbamail.com/en/integrations/wordpress/
   Description: Integrate your Acumbamail forms in your Wordpress pages
   Version: 2.0.15
   Author: Acumbamail
   Author URI: https://acumbamail.com
   Text Domain: acumbamail-signup-forms
   Domain Path: /languages
   License: GPLv3
   License URI: https://www.gnu.org/licenses/gpl.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

require('api/acumbamail.class.php');
require('acumbamail_widget.php');

add_action( 'init', 'acumbamail_load_textdomain' );
add_action('admin_menu', 'acumbamail_configuration');
add_action('admin_init', 'acumbamail_admin_init');
add_action('widgets_init', 'register_acumbamail_widget');

if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins')))) {
    add_filter('woocommerce_checkout_fields', 'acumbamail_woocommerce_add_subscription_check_field');
    add_action('woocommerce_checkout_update_order_meta', 'acumbamail_woocommerce_add_subscription_field_to_order');
    add_action('woocommerce_order_status_processing', 'acumbamail_woocommerce_subscribe_client');
}

function acumbamail_load_textdomain() {
    load_plugin_textdomain( 'acumbamail-signup-forms', true, dirname(plugin_basename(__FILE__)) . '/languages' );
}

function register_acumbamail_widget() {
    register_widget('Acumbamail_Widget');
}

function acumbamail_configuration() {
    // Don't delete the following two lines, so that plugin description translations are not removed
    __('Integrate your Acumbamail forms in your Wordpress pages', 'acumbamail-signup-forms');
    __('Show your Acumbamail signup forms easily in your Wordpress pages through a widget.', 'acumbamail-signup-forms');
    add_menu_page(
        __('Manage your subscriptions with Acumbamail', 'acumbamail-signup-forms'),
        'Acumbamail',
        'manage_options',
        'acumbamail',
        'acumbamail_options_page',
        plugin_dir_url(dirname(__FILE__) . '/acumbamail.php').'assets/logo.png'
    );

    if (in_array('woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins')))) {
        add_submenu_page('acumbamail',
                         __('Set up the form to be displayed on your Wordpress pages', 'acumbamail-signup-forms'),
                         'Woocommerce',
                         'manage_options',
                         'acumbamail_woocommerce',
                         'acumbamail_woocommerce_options_page');
    }
}

function acumbamail_options_page() {
    $acumbamail_settings_section = 'acumbamail';
    require('inc/admin_page.php');
}

function acumbamail_woocommerce_options_page() {
    $acumbamail_settings_section = 'acumbamail_woocommerce';
    require('inc/admin_page.php');
}

function acumbamail_register_settings_section($api, $lists, $forms) {
    $options = get_option('acumbamail_options');
    add_settings_section('acumbamail_main',
                         __('Integrate your Acumbamail forms into your Wordpress pages', 'acumbamail-signup-forms'),
                         'acumbamail_options_text',
                         'acumbamail'
    );
    acumbamail_show_auth_token_textbox('acumbamail', 'acumbamail_main');

    if (isset($options['auth_token']) and $lists) {
        $additional_args['field_name'] = 'list_id';
	$additional_args['lists'] = $lists;

        add_settings_field('acumbamail_list_id',
                            __('List', 'acumbamail-signup-forms') . ': ',
                           'acumbamail_list_id_field',
                           'acumbamail',
                           'acumbamail_main',
                           $additional_args
        );
    }

    if ($forms) {
    	$additional_args['forms'] = $forms;
        add_settings_field('acumbamail_form_id',
                            __('Form', 'acumbamail-signup-forms') . ': ',
                           'acumbamail_form_id_field',
                           'acumbamail',
                           'acumbamail_main',
			   $additional_args
        );
    }
}

function acumbamail_register_woocommerce_settings_section($api, $lists) {
    add_settings_section('acumbamail_woocommerce',
                         __('Configure the Acumbamail list to which your customers will be automatically subscribed', 'acumbamail-signup-forms'),
                         'acumbamail_options_text',
                         'acumbamail_woocommerce');

    acumbamail_show_auth_token_textbox('acumbamail_woocommerce', 'acumbamail_woocommerce');

    if ($lists) {
        $additional_args['field_name'] = 'woocommerce_list_id';
        $additional_args['lists'] = $lists;

        add_settings_field('acumbamail_woocommerce_list_id',
                            __('List', 'acumbamail-signup-forms') . ': ',
                           'acumbamail_list_id_field',
                           'acumbamail_woocommerce',
                           'acumbamail_woocommerce',
                           $additional_args
        );
        add_settings_field('acumbamail_woocommerce_subscription_sentence',
                           __('Checkbox text', 'acumbamail-signup-forms') . ': ',
                           'acumbamail_subscription_sentence_field',
                           'acumbamail_woocommerce',
                           'acumbamail_woocommerce'
        );
    }
}

function acumbamail_show_auth_token_textbox($page, $section) {
    add_settings_field('acumbamail_auth_token',
                       __('Auth Token', 'acumbamail-signup-forms') . ': ',
                       'acumbamail_auth_token_field',
                       $page,
                       $section
    );
}

function acumbamail_admin_init() {
    $options = get_option('acumbamail_options');
    $auth_token = empty($options) ? '' : $options['auth_token'];
    $api = new AcumbamailAPI('', $auth_token);
    $lists = $api->getLists();
    $forms = [];

    if (isset($options['list_id']) and $options['list_id'] != -1) {
        $forms = $api->getForms($options['list_id']);
    }
    
    register_setting('acumbamail_options', 'acumbamail_options', 'acumbamail_options_validate');
    
    acumbamail_register_settings_section($api, $lists, $forms);
    acumbamail_register_woocommerce_settings_section($api, $lists);
}

function compose_options_for_select_html_field($options, $selected_value) {
    foreach ($options as $key => $value) {
        $selected = '';
        if ($selected_value == $key) {
            $selected = 'selected';
        }
        echo "<option value=" . $key . " " . $selected . ">" . $value['name'] . "</option>";
    }
}

function acumbamail_get_form_details() {
    $options = get_option('acumbamail_options');
    $api = new AcumbamailAPI('', $options['auth_token']);
    $form_details = $api->getFormDetails($options['form_id']);

    return $form_details;
}

function acumbamail_options_validate($input) {
    if (isset($_POST['reset'])) {
        $output = var_export($_POST, true);
        return array();
    }

    $options = get_option('acumbamail_options');
    foreach ($input as $key => $value) {
        $options[$key] = $value;
    }
    return $options;
}

function acumbamail_auth_token_field() {
    $options = get_option('acumbamail_options');
    $auth_token = empty($options) ? '' : $options['auth_token'];
    echo "<input id='acumbamail_auth_token' name='acumbamail_options[auth_token]' size=20 type='text' value='{$auth_token}'>";
}

function acumbamail_subscription_sentence_field() {
    $options = get_option('acumbamail_options');
    echo "<input id='subscription_sentence_field' name='acumbamail_options[subscription_sentence]' size=20 type='text' value='{$options['subscription_sentence']}'>";
}

function acumbamail_list_id_field($additional_args) {
    $options = get_option('acumbamail_options');
    $lists = $additional_args['lists'];

    if (!count($lists)) {
        echo "<p>" . __("Your lists could not be retrieved", 'acumbamail-signup-forms') . ". " . __("Check that you have created lists and that your hosting allows incoming traffic from Acumbamail", 'acumbamail-signup-forms') .". </p>";
    }
    else {
        echo "<select id='acumbamail_'" . $additional_args['field_name'] . " name='acumbamail_options[" . $additional_args['field_name'] . "]'>";
        echo "<option value=-1>-- " . __("Select a list", 'acumbamail-signup-forms') . "--</option>";
        compose_options_for_select_html_field($lists, $options[$additional_args['field_name']]);
        echo '</select>';
    }
}

function acumbamail_form_id_field($additional_args) {
    $options = get_option('acumbamail_options');
    $api = new AcumbamailAPI('', $options['auth_token']);
    $forms = $additional_args['forms'];

    echo "<select id='acumbamail_form_id' name='acumbamail_options[form_id]'>";
    echo "<option value=-1>-- " . __("Select a form", 'acumbamail-signup-forms') . "--</option>";
    compose_options_for_select_html_field($forms, $options['form_id']);
    echo "</select>";
}

function acumbamail_options_text() {
}

function acumbamail_woocommerce_add_subscription_check_field($fields) {
    $options = get_option('acumbamail_options');
    $subscription_sentence = __('Would you like to subscribe to our mailing list?', 'acumbamail-signup-forms');

    if ($options['subscription_sentence']) {
        $subscription_sentence = $options['subscription_sentence'];
    }

    if ($options['woocommerce_list_id']) {
        $fields['billing']['acumba_subscribe'] = array(
            'type' => 'checkbox',
            'label' => $subscription_sentence,
            'class' => array('form-row-wide'),
            'clear' => true,
            'priority' => 1000
        );
    }

    return $fields;
}

function acumbamail_woocommerce_add_subscription_field_to_order($order_id) {
    if ($_POST['acumba_subscribe']) {
        update_post_meta($order_id, 'acumba_subscribe', $_POST['acumba_subscribe']);
    }
}

function acumbamail_woocommerce_subscribe_client($order_id) {
    // Retrieving email from order object
    $order = new WC_Order($order_id);
    $acumba_subscribe = get_post_meta($order_id, 'acumba_subscribe', true);
    if ($acumba_subscribe) {
        $subscriber_fields = array();
        $subscriber_fields['email'] = $order->get_billing_email();
        $options = get_option('acumbamail_options');
        $api = new AcumbamailAPI('', $options['auth_token']);
        $api->addSubscriber($options['woocommerce_list_id'], $subscriber_fields);
    }
}
