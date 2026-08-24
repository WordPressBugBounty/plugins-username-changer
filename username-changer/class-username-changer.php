<?php
/**
 * Plugin Name: Username Changer
 * Plugin URI: https://www.digitalme.cc
 * Description: Change usernames easily
 * Author: DigitalME
 * Author URI: https://www.digitalme.cc
 * Version: 3.3.0
 * Text Domain: username-changer
 * Domain Path: languages
 * Tested up to: 7.1
 * Requires at least: 3.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


// ─── WPRankLab promotional banner ────────────────────────────────────────────

function userchanger_should_show_notice() {
    if ( ! current_user_can( 'install_plugins' ) ) {
        return false;
    }
    if ( get_user_meta( get_current_user_id(), 'userchanger_promo_dismissed', true ) ) {
        return false;
    }
    return true;
}

add_action( 'all_admin_notices', function () {
    if ( ! userchanger_should_show_notice() ) {
        return;
    }
    if ( ! empty( $GLOBALS['_promo_banner_shown'] ) ) {
        return;
    }
    $GLOBALS['_promo_banner_shown'] = true;
    $nonce    = wp_create_nonce( 'userchanger_dismiss_notice' );
    $logo_url = plugin_dir_url( __FILE__ ) . 'assets/images/digitalme-logo.png';
    $ajax_url = admin_url( 'admin-ajax.php' );
    ?>
    <div id="userchanger-promo-banner" style="
        background:#fff;
        border-left:4px solid #2e5fa3;
        border-radius:4px;
        box-shadow:0 1px 4px rgba(0,0,0,0.12);
        padding:16px 20px;
        margin:5px 20px 15px 2px;
        display:flex;
        align-items:flex-start;
        gap:18px;
        flex-wrap:wrap;
        font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;
        position:relative;
    ">
        <div style="flex-shrink:0;display:flex;align-items:center;">
            <img src="<?php echo esc_url( $logo_url ); ?>"
                 alt="DigitalME"
                 style="height:64px;width:auto;display:block;" />
        </div>
        <div style="flex:1;min-width:220px;">
            <p style="margin:0 0 6px;font-size:14px;color:#3c434a;line-height:1.6;">
                We've just launched a new plugin called <strong>WPRankLab</strong>, and we'd love for you to try it.
                To get you started, we're offering <strong>6 months of WPRankLab Pro &mdash; completely free</strong>.
            </p>
            <p style="margin:0 0 4px;font-weight:600;color:#1d2327;font-size:13px;">Discover:</p>
            <ul style="margin:0 0 14px 18px;padding:0;color:#3c434a;font-size:13px;line-height:1.7;">
                <li>Your AI Visibility Score</li>
                <li>AI crawler detection &amp; tracking</li>
                <li>Weekly AI visibility reports</li>
                <li>Content optimization recommendations</li>
                <li>Advanced AI ranking insights</li>
                <li>6 months free &mdash; no risk, just visibility</li>
            </ul>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <a href="https://wpranklab.com/checkout/?add-to-cart=2874" target="_blank" style="
                    background:#2e5fa3;color:#fff;border-radius:4px;
                    padding:9px 16px;font-size:13px;font-weight:600;
                    text-decoration:none;display:inline-block;">
                    Claim your 6 free months
                </a>
                <button id="userchanger-open-setup" style="
                    background:#fff;color:#2e5fa3;border:1px solid #2e5fa3;border-radius:4px;
                    padding:9px 16px;font-size:13px;font-weight:600;
                    cursor:pointer;display:inline-block;">
                    Install other plugins
                </button>
                <button id="userchanger-dismiss-btn" style="
                    background:none;color:#888;border:none;
                    font-size:13px;cursor:pointer;padding:9px 4px;text-decoration:underline;">
                    Dismiss
                </button>
            </div>
        </div>
        <button id="userchanger-close-btn" style="
            position:absolute;top:10px;right:12px;
            background:none;border:none;font-size:20px;
            cursor:pointer;color:#aaa;line-height:1;padding:2px 6px;"
            title="Close">&times;</button>
    </div>
    <script>
    (function(){
        var NONCE   = <?php echo json_encode( $nonce ); ?>;
        var AJAXURL = <?php echo json_encode( $ajax_url ); ?>;
        function hideBanner() {
            var el = document.getElementById('userchanger-promo-banner');
            if ( el ) el.style.display = 'none';
        }
        function dismissBanner() {
            hideBanner();
            var fd = new FormData();
            fd.append('action', 'userchanger_dismiss_notice');
            fd.append('nonce', NONCE);
            fetch( AJAXURL, { method:'POST', credentials:'same-origin', body: fd } );
        }
        document.addEventListener('DOMContentLoaded', function(){
            var dismissBtn = document.getElementById('userchanger-dismiss-btn');
            var closeBtn   = document.getElementById('userchanger-close-btn');
            var openBtn    = document.getElementById('userchanger-open-setup');
            if ( dismissBtn ) dismissBtn.addEventListener('click', dismissBanner);
            if ( closeBtn )   closeBtn.addEventListener('click', hideBanner);
            if ( openBtn ) openBtn.addEventListener('click', function(e){
                e.preventDefault();
                var overlay = document.getElementById('userchanger-modal-overlay');
                if ( overlay ) {
                    overlay.style.display = 'flex';
                    document.body.style.overflow = 'hidden';
                }
            });
        });
    })();
    </script>
    <?php
} );

add_action( 'wp_ajax_userchanger_dismiss_notice', function () {
    check_ajax_referer( 'userchanger_dismiss_notice', 'nonce' );
    update_user_meta( get_current_user_id(), 'userchanger_promo_dismissed', 1 );
    wp_send_json_success();
} );


// ─── ActiveMemb promotional banner ───────────────────────────────────────────

if ( ! function_exists( 'activememb_should_show_notice' ) ) {
    function activememb_should_show_notice() {
        if ( ! current_user_can( 'manage_options' ) ) {
            return false;
        }
        if ( get_user_meta( get_current_user_id(), 'activememb_promo_notice_dismissed', true ) ) {
            return false;
        }
        return true;
    }
}

add_action( 'all_admin_notices', function () {
    if ( ! activememb_should_show_notice() ) {
        return;
    }
    if ( ! empty( $GLOBALS['_promo_banner_shown'] ) ) {
        return;
    }
    $GLOBALS['_promo_banner_shown'] = true;
    $nonce     = wp_create_nonce( 'activememb_dismiss_notice' );
    $claim_url = 'https://activewoo.com/checkout/?add-to-cart=13671';
    $logo_url  = plugin_dir_url( __FILE__ ) . 'assets/images/activewoo-promo.png';
    $ajax_url  = admin_url( 'admin-ajax.php' );
    ?>
    <div id="activememb-promo-banner" style="background:#fff;border-left:4px solid #2e5fa3;border-radius:4px;box-shadow:0 1px 4px rgba(0,0,0,0.12);padding:16px 20px;margin:5px 20px 15px 2px;display:flex;align-items:flex-start;gap:18px;flex-wrap:wrap;font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;position:relative;">
        <div style="flex-shrink:0;display:flex;align-items:center;"><img src="<?php echo esc_url( $logo_url ); ?>" alt="ActiveMemb" style="height:64px;width:auto;display:block;" /></div>
        <div style="flex:1;min-width:220px;">
            <p style="margin:0 0 6px;font-size:14px;color:#3c434a;line-height:1.6;">We've just launched a new plugin called <strong>ActiveMemb</strong>, and we'd love for you to try it. To get you started, we're offering <strong>6 months of ActiveMemb Pro &mdash; completely free</strong>.</p>
            <p style="margin:0 0 4px;font-weight:600;color:#1d2327;font-size:13px;">Discover:</p>
            <ul style="margin:0 0 14px 18px;padding:0;color:#3c434a;font-size:13px;line-height:1.7;"><li>Passwordless, email-based login</li><li>Tag-based content access with ActiveCampaign</li><li>Simple membership &amp; page protection</li><li>Optional 2FA for sensitive pages</li><li>And more powerful membership features</li></ul>
            <div style="display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
                <a href="<?php echo esc_url( $claim_url ); ?>" target="_blank" style="background:#2e5fa3;color:#fff;border-radius:4px;padding:9px 16px;font-size:13px;font-weight:600;text-decoration:none;display:inline-block;">Claim your 6 free months</a>
                <button id="activememb-dismiss-btn" style="background:none;color:#888;border:none;font-size:13px;cursor:pointer;padding:9px 4px;text-decoration:underline;">Dismiss</button>
            </div>
        </div>
        <button id="activememb-close-btn" style="position:absolute;top:10px;right:12px;background:none;border:none;font-size:20px;cursor:pointer;color:#aaa;line-height:1;padding:2px 6px;" title="Close">&times;</button>
    </div>
    <script>(function(){
        var NONCE=<?php echo json_encode( $nonce ); ?>,AJAXURL=<?php echo json_encode( $ajax_url ); ?>;
        function hide(){var e=document.getElementById('activememb-promo-banner');if(e)e.style.display='none';}
        function dismiss(){hide();var fd=new FormData();fd.append('action','activememb_dismiss_notice');fd.append('nonce',NONCE);fetch(AJAXURL,{method:'POST',credentials:'same-origin',body:fd});}
        document.addEventListener('DOMContentLoaded',function(){
            var d=document.getElementById('activememb-dismiss-btn'),c=document.getElementById('activememb-close-btn');
            if(d)d.addEventListener('click',dismiss);if(c)c.addEventListener('click',hide);
        });
    })();</script>
    <?php
} );

if ( ! has_action( 'wp_ajax_activememb_dismiss_notice' ) ) {
    add_action( 'wp_ajax_activememb_dismiss_notice', function () {
        check_ajax_referer( 'activememb_dismiss_notice', 'nonce' );
        update_user_meta( get_current_user_id(), 'activememb_promo_notice_dismissed', 1 );
        wp_send_json_success();
    } );
}

// ─── Setup wizard ─────────────────────────────────────────────────────────────

function userchanger_pack_folders() {
    return array(
        'affiliatewp-activecampaign',
        'comment-controller',
        'discord-display',
        'easy-digital-downloads-coinpayments-gateway',
        'easy-digital-downloads-htaccess-editor',
        'easy-digital-downloads-pantheon-compat',
        'easy-digital-downloads-payment-icons-widget',
        'easy-digital-downloads-pricing-select',
        'easy-digital-downloads-store-hours',
        'easy-digital-downloads-variable-defaults',
        'edd-external-products',
        'edd-geckoboard',
        'no-category-base-wpml',
        'remove-administrators',
        'simple-attribution',
        'toggle-admin-menu',
        'username-changer',
        'woocommerce-active-campaign',
        'xkcd-embed',
    );
}

function userchanger_plugin_wporg_slugs() {
    return array(
        'affiliatewp-activecampaign'                  => 'affiliatewp-activecampaign',
        'comment-controller'                          => 'comment-controller',
        'discord-display'                             => 'discord-display',
        'easy-digital-downloads-coinpayments-gateway' => 'easy-digital-downloads-coinpayments-gateway',
        'easy-digital-downloads-htaccess-editor'      => 'easy-digital-downloads-htaccess-editor',
        'easy-digital-downloads-pantheon-compat'      => 'easy-digital-downloads-pantheon-compat',
        'easy-digital-downloads-payment-icons-widget' => 'easy-digital-downloads-payment-icons-widget',
        'easy-digital-downloads-pricing-select'       => 'easy-digital-downloads-pricing-select',
        'easy-digital-downloads-store-hours'          => 'easy-digital-downloads-store-hours',
        'easy-digital-downloads-variable-defaults'    => 'easy-digital-downloads-variable-defaults',
        'edd-external-products'                       => 'edd-external-products',
        'edd-geckoboard'                              => 'edd-geckoboard',
        'no-category-base-wpml'                       => 'no-category-base-wpml',
        'remove-administrators'                       => 'remove-administrators',
        'simple-attribution'                          => 'simple-attribution',
        'toggle-admin-menu'                           => 'toggle-admin-menu',
        'username-changer'                            => 'username-changer',
        'woocommerce-active-campaign'                 => '', // private/paid plugin
        'xkcd-embed'                                  => 'xkcd-embed',
    );
}

function userchanger_collect_pack_entries() {
    require_once ABSPATH . 'wp-admin/includes/plugin.php';

    $allowed = array_fill_keys( userchanger_pack_folders(), true );
    $plugins = get_plugins();
    $entries = array();

    foreach ( $plugins as $plugin_file => $plugin_data ) {
        $dir = dirname( $plugin_file );
        if ( $dir === '.' || empty( $allowed[ $dir ] ) ) {
            continue;
        }
        $base  = basename( $plugin_file, '.php' );
        $score = 0;
        if ( $base === basename( $dir ) ) { $score += 20; }
        if ( ! empty( $plugin_data['Description'] ) ) { $score += 2; }
        if ( ! empty( $plugin_data['Name'] ) ) { $score += 1; }

        if ( ! isset( $entries[ $dir ] ) || $score > $entries[ $dir ]['score'] ) {
            $entries[ $dir ] = array(
                'score'       => $score,
                'folder'      => $dir,
                'plugin_file' => $plugin_file,
                'name'        => ! empty( $plugin_data['Name'] ) ? $plugin_data['Name'] : $dir,
                'description' => ! empty( $plugin_data['Description'] ) ? $plugin_data['Description'] : '',
            );
        }
    }

    $slugs = userchanger_plugin_wporg_slugs();
    foreach ( userchanger_pack_folders() as $folder ) {
        if ( isset( $entries[ $folder ] ) ) { continue; }
        $entries[ $folder ] = array(
            'score'       => -1,
            'folder'      => $folder,
            'plugin_file' => '',
            'name'        => $folder,
            'description' => '',
            'installed'   => is_dir( trailingslashit( WP_PLUGIN_DIR ) . $folder ),
            'wporg_slug'  => isset( $slugs[ $folder ] ) ? $slugs[ $folder ] : '',
        );
    }

    uasort( $entries, function ( $a, $b ) {
        return strcasecmp( (string) $a['name'], (string) $b['name'] );
    } );

    return array_values( $entries );
}

function userchanger_extract_first_href( $html ) {
    if ( ! is_string( $html ) || $html === '' ) { return ''; }
    if ( preg_match( '/href=("|\")([^"\"]+)("|\")/i', $html, $m ) ) { return $m[2]; }
    if ( preg_match( "/href=(')([^']+)(')/i", $html, $m ) ) { return $m[2]; }
    return '';
}

function userchanger_folder_url_map() {
    return array(
        'woocommerce-active-campaign'                  => 'admin.php?page=wc-settings&tab=integration&section=active-woo',
        'affiliatewp-activecampaign'                   => 'admin.php?page=affiliate-wp-settings&tab=integrations',
        'comment-controller'                           => 'admin.php?page=comment_controller-settings',
        'simple-attribution'                           => 'admin.php?page=simple_attribution-settings',
        'username-changer'                             => 'admin.php?page=username_changer-settings',
        'easy-digital-downloads-coinpayments-gateway'  => 'edit.php?post_type=download&page=edd-settings&tab=gateways',
        'easy-digital-downloads-htaccess-editor'       => 'edit.php?post_type=download&page=edd-tools&tab=general',
        'easy-digital-downloads-pantheon-compat'       => 'edit.php?post_type=download&page=edd-settings',
        'easy-digital-downloads-payment-icons-widget'  => 'widgets.php',
        'easy-digital-downloads-pricing-select'        => 'edit.php?post_type=download&page=edd-settings',
        'easy-digital-downloads-store-hours'           => 'edit.php?post_type=download&page=edd-settings&tab=extensions',
        'easy-digital-downloads-variable-defaults'     => 'edit.php?post_type=download&page=edd-settings&tab=extensions',
        'edd-external-products'                        => 'edit.php?post_type=download&page=edd-settings',
        'edd-geckoboard'                               => 'edit.php?post_type=download&page=edd-settings',
        'no-category-base-wpml'                        => 'options-permalink.php',
        'remove-administrators'                        => 'users.php',
        'toggle-admin-menu'                            => 'plugins.php',
        'xkcd-embed'                                   => 'options-general.php',
        'discord-display'                              => 'plugins.php',
    );
}

function userchanger_guess_setup_url( $plugin_file, $plugin_data ) {
    $folder = dirname( $plugin_file );
    $map    = userchanger_folder_url_map();
    if ( isset( $map[ $folder ] ) ) {
        return admin_url( $map[ $folder ] );
    }
    $actions = apply_filters( "plugin_action_links_{$plugin_file}", array(), $plugin_file, $plugin_data, 'all' );
    $actions = apply_filters( 'plugin_action_links', $actions, $plugin_file, $plugin_data, 'all' );
    if ( is_array( $actions ) ) {
        foreach ( $actions as $action_html ) {
            $href = userchanger_extract_first_href( $action_html );
            if ( $href && ( strpos( $href, 'admin.php?page=' ) !== false || strpos( $href, 'options-general.php?page=' ) !== false || strpos( $href, 'edit.php?post_type=' ) !== false ) ) {
                return strpos( $href, 'http' ) === 0 ? $href : admin_url( ltrim( $href, '/' ) );
            }
        }
    }
    return admin_url( 'plugins.php' );
}

add_action( 'admin_menu', function () {
    add_submenu_page(
        'tools.php',
        'Install other useful plugins',
        'Install other useful plugins',
        'install_plugins',
        'userchanger-setup-wizard',
        'userchanger_render_setup_wizard'
    );
} );

add_action( 'admin_post_userchanger_open_plugin', function () {
    if ( ! current_user_can( 'activate_plugins' ) ) {
        wp_die( 'Sorry, you are not allowed to do that.' );
    }
    $plugin_file = isset( $_GET['plugin'] ) ? sanitize_text_field( wp_unslash( $_GET['plugin'] ) ) : '';
    check_admin_referer( 'userchanger_open_plugin_' . $plugin_file );
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $plugins = get_plugins();
    if ( empty( $plugin_file ) || empty( $plugins[ $plugin_file ] ) ) {
        wp_safe_redirect( admin_url( 'plugins.php' ) );
        exit;
    }
    $folder = dirname( $plugin_file );
    if ( $folder === '.' || ! in_array( $folder, userchanger_pack_folders(), true ) ) {
        wp_safe_redirect( admin_url( 'plugins.php' ) );
        exit;
    }
    $plugin_data = $plugins[ $plugin_file ];
    if ( ! is_plugin_active( $plugin_file ) ) {
        $activate = activate_plugin( $plugin_file );
        if ( is_wp_error( $activate ) ) {
            wp_safe_redirect( admin_url( 'plugins.php?s=' . rawurlencode( wp_strip_all_tags( $plugin_data['Name'] ) ) ) );
            exit;
        }
    }
    $setup_url = userchanger_guess_setup_url( $plugin_file, $plugin_data );
    wp_safe_redirect( $setup_url );
    exit;
} );

add_action( 'admin_footer', function () {
    if ( ! current_user_can( 'install_plugins' ) ) {
        return;
    }
    if ( ! userchanger_should_show_notice() ) {
        return;
    }
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $entries  = userchanger_collect_pack_entries();
    $logo_url = plugin_dir_url( __FILE__ ) . 'assets/images/digitalme-logo.png';
    ?>
    <style>
        #userchanger-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            z-index: 999999;
            background: rgba(0,0,0,0.55);
            align-items: center;
            justify-content: center;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        #userchanger-modal-box {
            background: #fff;
            border-radius: 14px;
            box-shadow: 0 24px 80px rgba(0,0,0,0.35);
            width: 680px;
            max-width: calc(100vw - 40px);
            max-height: calc(100vh - 60px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
            animation: userchangerModalIn 0.22s ease;
        }
        @keyframes userchangerModalIn {
            from { opacity:0; transform:scale(0.94) translateY(12px); }
            to   { opacity:1; transform:scale(1) translateY(0); }
        }
        #userchanger-modal-head {
            background: linear-gradient(135deg,#667eea 0%,#764ba2 100%);
            padding: 28px 28px 24px;
            text-align: center;
            flex-shrink: 0;
            position: relative;
        }
        #userchanger-modal-head h2 {
            margin: 0 0 6px;
            font-size: 22px;
            font-weight: 700;
            color: #fff;
        }
        #userchanger-modal-head p {
            margin: 0;
            font-size: 14px;
            color: rgba(255,255,255,0.88);
            line-height: 1.5;
        }
        #userchanger-modal-close {
            position: absolute;
            top: 12px;
            right: 14px;
            background: rgba(255,255,255,0.2);
            border: none;
            border-radius: 50%;
            width: 30px;
            height: 30px;
            cursor: pointer;
            color: #fff;
            font-size: 18px;
            line-height: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background 0.15s;
        }
        #userchanger-modal-close:hover { background: rgba(255,255,255,0.35); }
        #userchanger-modal-body {
            padding: 20px;
            overflow-y: auto;
            flex: 1;
            background: #f8f9fb;
        }
        #userchanger-modal-body::-webkit-scrollbar { width: 6px; }
        #userchanger-modal-body::-webkit-scrollbar-track { background: #eee; border-radius: 8px; }
        #userchanger-modal-body::-webkit-scrollbar-thumb { background: #c5c5c5; border-radius: 8px; }
        .userchanger-item {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 14px 16px;
            background: #fff;
            border-radius: 10px;
            margin: 0 0 10px;
            text-decoration: none;
            box-shadow: 0 1px 4px rgba(0,0,0,0.07);
            transition: box-shadow 0.2s, transform 0.2s;
            position: relative;
            overflow: hidden;
        }
        .userchanger-item:hover {
            box-shadow: 0 6px 20px rgba(102,126,234,0.18);
            transform: translateY(-1px);
        }
        .userchanger-item::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: linear-gradient(180deg,#667eea,#764ba2);
            opacity: 0;
            transition: opacity 0.2s;
        }
        .userchanger-item:hover::before { opacity: 1; }
        .userchanger-icon-wrap {
            flex-shrink: 0;
            width: 38px; height: 38px;
            border-radius: 8px;
            background: linear-gradient(135deg,#667eea,#764ba2);
            display: flex; align-items: center; justify-content: center;
        }
        .userchanger-icon-wrap .dashicons { color:#fff; font-size:20px; width:20px; height:20px; }
        .userchanger-info { flex: 1; min-width: 0; }
        .userchanger-name { margin: 0 0 2px; font-size: 14px; font-weight: 600; color: #1a202c; }
        .userchanger-desc { margin: 0; font-size: 12px; color: #6b7280; line-height: 1.45; }
        .userchanger-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .userchanger-badge {
            display: inline-flex; align-items: center; gap: 4px;
            padding: 4px 10px; border-radius: 20px;
            font-size: 11px; font-weight: 600;
        }
        .userchanger-badge.on  { background: #d1fae5; color: #065f46; }
        .userchanger-badge.off { background: #e5e7eb; color: #6b7280; }
        .userchanger-arrow { color: #d1d5db; font-size: 18px; transition: color 0.2s, transform 0.2s; }
        .userchanger-item:hover .userchanger-arrow { color: #667eea; transform: translateX(3px); }
    </style>

    <div id="userchanger-modal-overlay">
        <div id="userchanger-modal-box">
            <div id="userchanger-modal-head">
                <button id="userchanger-modal-close" title="Close">&times;</button>
                <img src="<?php echo esc_url( $logo_url ); ?>" alt="DigitalME" style="height:48px;width:auto;display:block;margin:0 auto 14px;" />
                <h2>&#x1F680; Complete Your Setup</h2>
                <p>Click a plugin below to activate it and open its settings.</p>
            </div>
            <div id="userchanger-modal-body">
                <?php foreach ( $entries as $entry ) :
                    $plugin_file = $entry['plugin_file'];
                    $name        = $entry['name'];
                    $desc        = $entry['description'];
                    if ( $plugin_file ) :
                        $is_active = is_plugin_active( $plugin_file );
                        $open_url  = wp_nonce_url(
                            admin_url( 'admin-post.php?action=userchanger_open_plugin&plugin=' . rawurlencode( $plugin_file ) ),
                            'userchanger_open_plugin_' . $plugin_file
                        );
                ?>
                <a class="userchanger-item" href="<?php echo esc_url( $open_url ); ?>">
                    <div class="userchanger-icon-wrap"><span class="dashicons dashicons-admin-plugins"></span></div>
                    <div class="userchanger-info">
                        <p class="userchanger-name"><?php echo esc_html( $name ); ?></p>
                        <?php if ( $desc !== '' ) : ?>
                        <p class="userchanger-desc"><?php echo esc_html( wp_strip_all_tags( $desc ) ); ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="userchanger-right">
                        <span class="userchanger-badge <?php echo $is_active ? 'on' : 'off'; ?>">
                            <?php if ( $is_active ) : ?><span class="dashicons dashicons-yes" style="font-size:13px;width:13px;height:13px;"></span>&nbsp;<?php endif; ?>
                            <?php echo $is_active ? 'Active' : 'Inactive'; ?>
                        </span>
                        <span class="dashicons dashicons-arrow-right-alt2 userchanger-arrow"></span>
                    </div>
                </a>
                <?php else :
                    $wporg_slug = ! empty( $entry['wporg_slug'] ) ? $entry['wporg_slug'] : '';
                    if ( $wporg_slug ) {
                        $install_url    = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $wporg_slug ) ), 'install-plugin_' . $wporg_slug );
                        $install_target = '';
                    } else {
                        $install_url    = 'https://activewoo.com';
                        $install_target = ' target="_blank" rel="noopener"';
                    }
                ?>
                <a class="userchanger-item" href="<?php echo esc_url( $install_url ); ?>"<?php echo $install_target; ?>>
                    <div class="userchanger-icon-wrap"><span class="dashicons dashicons-admin-plugins"></span></div>
                    <div class="userchanger-info">
                        <p class="userchanger-name"><?php echo esc_html( $name ); ?></p>
                        <?php if ( $desc !== '' ) : ?><p class="userchanger-desc"><?php echo esc_html( wp_strip_all_tags( $desc ) ); ?></p><?php endif; ?>
                    </div>
                    <div class="userchanger-right">
                        <span class="userchanger-badge off" style="background:#fef9c3;color:#854d0e;">&#8595;&nbsp;Install</span>
                        <span class="dashicons dashicons-arrow-right-alt2 userchanger-arrow"></span>
                    </div>
                </a>
                <?php endif; ?>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <script>
    (function(){
        var overlay  = document.getElementById('userchanger-modal-overlay');
        var closeBtn = document.getElementById('userchanger-modal-close');
        function openModal()  { overlay.style.display = 'flex'; document.body.style.overflow = 'hidden'; }
        function closeModal() { overlay.style.display = 'none';  document.body.style.overflow = ''; }
        if ( closeBtn ) closeBtn.addEventListener('click', closeModal);
        overlay.addEventListener('click', function(e){ if ( e.target === overlay ) closeModal(); });
        document.addEventListener('keydown', function(e){ if ( e.key === 'Escape' ) closeModal(); });
        var openBtn = document.getElementById('userchanger-open-setup');
        if ( openBtn ) openBtn.addEventListener('click', function(e){ e.preventDefault(); openModal(); });
    })();
    </script>
    <?php
} );

function userchanger_render_setup_wizard() {
    if ( ! current_user_can( 'install_plugins' ) ) {
        return;
    }
    require_once ABSPATH . 'wp-admin/includes/plugin.php';
    $entries = userchanger_collect_pack_entries();
    echo '<div class="wrap">';
    echo '<h1>Install other useful plugins</h1>';
    echo '<p>Select a plugin to open its setup/settings. If it is not active yet, it will be activated first.</p>';
    echo '<table class="widefat striped"><tbody>';
    foreach ( $entries as $entry ) {
        $plugin_file = $entry['plugin_file'];
        $is_active   = $plugin_file ? is_plugin_active( $plugin_file ) : false;
        $name        = $entry['name'];
        $desc        = $entry['description'];
        if ( $plugin_file ) {
            $open_url   = wp_nonce_url(
                admin_url( 'admin-post.php?action=userchanger_open_plugin&plugin=' . rawurlencode( $plugin_file ) ),
                'userchanger_open_plugin_' . $plugin_file
            );
            $title_html = '<a class="row-title" href="' . esc_url( $open_url ) . '">' . esc_html( $name ) . '</a>';
        } else {
            $wporg_slug = ! empty( $entry['wporg_slug'] ) ? $entry['wporg_slug'] : '';
            if ( $wporg_slug ) {
                $install_url = wp_nonce_url( self_admin_url( 'update.php?action=install-plugin&plugin=' . rawurlencode( $wporg_slug ) ), 'install-plugin_' . $wporg_slug );
                $title_html  = '<a class="row-title" href="' . esc_url( $install_url ) . '">' . esc_html( $name ) . '</a>';
            } else {
                $title_html = '<a class="row-title" href="https://activewoo.com" target="_blank" rel="noopener">' . esc_html( $name ) . '</a>';
            }
        }
        echo '<tr>';
        echo '<td>';
        echo $title_html;
        if ( $desc !== '' ) {
            echo '<div class="description">' . esc_html( wp_strip_all_tags( $desc ) ) . '</div>';
        }
        echo '</td>';
        echo '<td style="width:120px;white-space:nowrap;">';
        if ( $plugin_file ) {
            echo $is_active ? '<span class="dashicons dashicons-yes"></span> Active' : 'Inactive';
        } else {
            echo '<span style="color:#92400e;">&#8595; Install</span>';
        }
        echo '</td>';
        echo '</tr>';
    }
    echo '</tbody></table></div>';
}

// ─────────────────────────────────────────────────────────────────────────────


if ( ! class_exists( 'Username_Changer' ) ) {


	/**
	 * Main Username_Changer class
	 *
	 * @access      public
	 * @since       2.0.0
	 */
	final class Username_Changer {


		/**
		 * The one true Username_Changer
		 *
		 * @access      private
		 * @since       2.0.0
		 * @var         Username_Changer $instance The one true Username_Changer
		 */
		private static $instance;


		/**
		 * The settings object
		 *
		 * @access      public
		 * @since       3.0.0
		 * @var         object $settings The settings object
		 */
		public $settings;


		/**
		 * The template tags object
		 *
		 * @access      public
		 * @since       3.0.0
		 * @var         object $template_tags The template tags object
		 */
		public $template_tags;


		/**
		 * Get active instance
		 *
		 * @access      public
		 * @since       2.0.0
		 * @static
		 * @return      object self::$instance The one true Username_Changer
		 */
		public static function instance() {
			if ( ! isset( self::$instance ) && ! ( self::$instance instanceof Username_Changer ) ) {
				self::$instance = new Username_Changer();
				self::$instance->setup_constants();
				self::$instance->hooks();
				self::$instance->includes();
				self::$instance->template_tags = new Username_Changer_Template_Tags();
			}

			return self::$instance;
		}


		/**
		 * Throw error on object clone
		 *
		 * The whole idea of the singleton design pattern is that there is
		 * a single object. Therefore, we don't want the object to be cloned.
		 *
		 * @access      protected
		 * @since       1.0.0
		 * @return      void
		 */
		public function __clone() {
			_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', 'username-changer' ), '1.0.0' );
		}


		/**
		 * Disable unserializing of the class
		 *
		 * @access      protected
		 * @since       1.0.0
		 * @return      void
		 */
		public function __wakeup() {
			_doing_it_wrong( __FUNCTION__, esc_attr__( 'Cheatin&#8217; huh?', 'username-changer' ), '1.0.0' );
		}


		/**
		 * Setup plugin constants
		 *
		 * @access      private
		 * @since       2.0.0
		 * @return      void
		 */
		private function setup_constants() {
			// Plugin version.
			if ( ! defined( 'USERNAME_CHANGER_VER' ) ) {
			define( 'USERNAME_CHANGER_VER', '3.3.0' );
			}

			// Plugin path.
			if ( ! defined( 'USERNAME_CHANGER_DIR' ) ) {
				define( 'USERNAME_CHANGER_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin URL.
			if ( ! defined( 'USERNAME_CHANGER_URL' ) ) {
				define( 'USERNAME_CHANGER_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin file.
			if ( ! defined( 'USERNAME_CHANGER_FILE' ) ) {
				define( 'USERNAME_CHANGER_FILE', __FILE__ );
			}
		}


		/**
		 * Run plugin base hooks
		 *
		 * @access      private
		 * @since       3.2.0
		 * @return      void
		 */
		private function hooks() {
			add_action( 'plugins_loaded', array( self::$instance, 'load_textdomain' ) );
		}


		/**
		 * Include necessary files
		 *
		 * @access      private
		 * @since       1.0.0
		 * @return      void
		 */
		private function includes() {
			global $username_changer_options;

			// Load settings handler if necessary.
			if ( ! class_exists( 'Simple_Settings' ) ) {
				require_once USERNAME_CHANGER_DIR . 'vendor/widgitlabs/simple-settings/class-simple-settings.php';
			}

			require_once USERNAME_CHANGER_DIR . 'includes/admin/settings/register-settings.php';

			self::$instance->settings = new Simple_Settings( 'username_changer', 'settings' );
			$username_changer_options = self::$instance->settings->get_settings();

			require_once USERNAME_CHANGER_DIR . 'includes/misc-functions.php';
			require_once USERNAME_CHANGER_DIR . 'includes/scripts.php';
			require_once USERNAME_CHANGER_DIR . 'includes/class-username-changer-template-tags.php';

			if ( is_admin() ) {
				require_once USERNAME_CHANGER_DIR . 'includes/admin/actions.php';
			}
		}


		/**
		 * Load plugin language files
		 *
		 * @access      public
		 * @since       2.0.0
		 * @return      void
		 */
		public function load_textdomain() {
			// Set filter for language directory.
			$lang_dir = dirname( plugin_basename( __FILE__ ) ) . '/languages/';
			$lang_dir = apply_filters( 'username_changer_languages_directory', $lang_dir );

			// WordPress plugin locale filter.
			$locale = apply_filters( 'plugin_locale', get_locale(), 'username-changer' );
			$mofile = sprintf( '%1$s-%2$s.mo', 'username-changer', $locale );

			// Setup paths to current locale file.
			$mofile_local  = $lang_dir . $mofile;
			$mofile_global = WP_LANG_DIR . '/username-changer/' . $mofile;
			$mofile_core   = WP_LANG_DIR . '/plugins/username-changer/' . $mofile;

			if ( file_exists( $mofile_global ) ) {
				// Look in global /wp-content/languages/username-changer folder.
				load_textdomain( 'username-changer', $mofile_global );
			} elseif ( file_exists( $mofile_local ) ) {
				// Look in local /wp-content/plugins/username-changer/languages/ folder.
				load_textdomain( 'username-changer', $mofile_local );
			} elseif ( file_exists( $mofile_core ) ) {
				// Look in core /wp-content/languages/plugins/username-changer/ folder.
				load_textdomain( 'username-changer', $mofile_core );
			} else {
				// Load the default language files.
				load_plugin_textdomain( 'username-changer', false, $lang_dir );
			}
		}
	}
}


/**
 * The main function responsible for returning the one true Username_Changer
 * instance to functions everywhere.
 *
 * Use this function like you would a global variable, except without
 * needing to declare the global.
 *
 * Example: <?php $username_changer = Username_Changer(); ?>
 *
 * @since       2.0.0
 * @return      Username_Changer The one true Username_Changer
 */
function username_changer() {
	return Username_Changer::instance();
}


/**
 * Check if the Pro license is active.
 *
 * @since  4.0.0
 * @return bool
 */
function username_changer_is_pro_active() {
	return (bool) apply_filters( 'username_changer_is_pro_active', false );
}


// Get things started.
Username_Changer();

/* Opt-in */
require_once dirname( __FILE__ ) . '/includes/class-optin.php';
add_action( 'plugins_loaded', function() {
	UC_Optin::instance();
} );
register_activation_hook( __FILE__, function() {
	require_once dirname( __FILE__ ) . '/includes/class-optin.php';
	UC_Optin::instance()->on_activation();
} );
add_action( 'upgrader_process_complete', 'uc_optin_on_upgrade', 10, 2 );

function uc_optin_on_upgrade( $upgrader, $hook_extra ) {
	if ( empty( $hook_extra['action'] ) || 'update' !== $hook_extra['action'] ) {
		return;
	}

	if ( empty( $hook_extra['type'] ) || 'plugin' !== $hook_extra['type'] ) {
		return;
	}

	$updated_plugins = array();
	if ( ! empty( $hook_extra['plugins'] ) && is_array( $hook_extra['plugins'] ) ) {
		$updated_plugins = $hook_extra['plugins'];
	} elseif ( ! empty( $hook_extra['plugin'] ) ) {
		$updated_plugins = array( $hook_extra['plugin'] );
	}

	if ( in_array( plugin_basename( __FILE__ ), $updated_plugins, true ) ) {
		require_once dirname( __FILE__ ) . '/includes/class-optin.php';
		UC_Optin::instance()->on_activation();
	}
}
