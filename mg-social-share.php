<?php
/*
Plugin Name: MG Social Share
Plugin URI:  https://developer.wordpress.org/plugins/mg-social-share/
Description: Share your content through the famous social networks and email.
Version:     1.0
Author:      Mauricio Gelves
Author URI:  https://maugelves.com
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
Text Domain: mgss
Domain Path: /languages
*/


class MG_Social_Share {

    /**
     * @var Singleton The reference to *Singleton* instance of this class
     */
    private static $instance;
    private $plugin_url;

    /**
     * Returns the *Singleton* instance of this class.
     *
     * @return Singleton The *Singleton* instance.
     */
    public static function getInstance()
    {
        if (null === static::$instance) {
            static::$instance = new static();
        }

        return static::$instance;
    }

    /**
     * Protected constructor to prevent creating a new instance of the
     * *Singleton* via the `new` operator from outside of this class.
     */
    protected function __construct(){}

    function init(){

        $this->plugin_url = plugins_url() . "/mg-social-share";

        add_action( 'init', array($this, 'enqueue_style' ) );
        add_action( 'admin_menu', array( $this, 'create_options_menu' ) );
        add_action( 'admin_init', array( $this, 'config_settings' ) );
        add_shortcode( 'mgss-render', array($this, 'render_shortcode' ) );

    }


    // ============= METHODS =============
    function config_settings()
    {

        // SOCIAL NETWORK PICKER
        add_settings_section(
            'fass_sn_picker',
            __( 'Please choose the Social Networks to share your content.', 'fass' ),
            array($this, 'fass_settings_sn_cb'),
            'fass_settings'
        );

        add_settings_field(
            'fass_facebook_field',
            __( 'Use Facebook?', 'fass' ),
            array($this, 'fass_facebook_field_render' ),
            'fass_settings',
            'fass_sn_picker'
        );
        register_setting( 'fass_settings', 'fass_facebook' );

        add_settings_field(
            'fass_twitter_field',
            __( 'Use Twitter?', 'fass' ),
            array($this, 'fass_twitter_field_render' ),
            'fass_settings',
            'fass_sn_picker'
        );
        register_setting( 'fass_settings', 'fass_twitter' );

        add_settings_field(
            'fass_whatsapp_field',
            __( 'Use WhatsApp?', 'fass' ),
            array($this, 'fass_whatsapp_field_render' ),
            'fass_settings',
            'fass_sn_picker'
        );
        register_setting( 'fass_settings', 'fass_whatsapp' );

        add_settings_field(
            'fass_linkedin_field',
            __( 'Use LinkedIn?', 'fass' ),
            array($this, 'fass_linkedin_field_render' ),
            'fass_settings',
            'fass_sn_picker'
        );
        register_setting( 'fass_settings', 'fass_linkedin' );

        add_settings_field(
            'fass_googleplus_field',
            __( 'Use Google Plus?', 'fass' ),
            array($this, 'fass_googleplus_field_render' ),
            'fass_settings',
            'fass_sn_picker'
        );
        register_setting( 'fass_settings', 'fass_googleplus' );

        add_settings_field(
            'fass_email_field',
            __( 'Use Email?', 'fass' ),
            array($this, 'fass_email_field_render' ),
            'fass_settings',
            'fass_sn_picker'
        );
        register_setting( 'fass_settings', 'fass_email' );



    }


    /**
     * Render Facebook Field
     */
    function fass_facebook_field_render(  ) {

        $fass_item = get_option( 'fass_facebook' );
        ?>
        <input type='checkbox' name='fass_facebook' <?php checked( $fass_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Twitter Field
     */
    function fass_twitter_field_render(  ) {

        $fass_item = get_option( 'fass_twitter' );
        ?>
        <input type='checkbox' name='fass_twitter' <?php checked( $fass_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Linkedin Field
     */
    function fass_linkedin_field_render(  ) {

        $fass_item = get_option( 'fass_linkedin' );
        ?>
        <input type='checkbox' name='fass_linkedin' <?php checked( $fass_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Google Plus Field
     */
    function fass_googleplus_field_render(  ) {

        $fass_item = get_option( 'fass_googleplus' );
        ?>
        <input type='checkbox' name='fass_googleplus' <?php checked( $fass_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Email Field
     */
    function fass_email_field_render(  ) {

        $fass_item = get_option( 'fass_email' );
        ?>
        <input type='checkbox' name='fass_email' <?php checked( $fass_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render WhatsApp Field
     */
    function fass_whatsapp_field_render(  ) {

        $fass_item = get_option( 'fass_whatsapp' );
        ?>
        <input type='checkbox' name='fass_whatsapp' <?php checked( $fass_item, 1 ); ?> value='1'>
        <?php

    }


    function fass_settings_sn_cb() {}


    /**
     * Create the page for the options variables
     */
    function create_options_menu()
    {
        add_submenu_page(
            'options-general.php',
            __('MG Social Share', 'fass'),
            __('MG Social Share', 'fass'),
            'manage_options',
            'mg-social-share',
            array($this, 'create_options_page_cb')
        );
    }



    function create_options_page_cb()
    {

        ?>
        <form action='options.php' method='post'>

            <h2>MG Social Share</h2>

            <?php
            settings_fields( 'fass_settings' );
            do_settings_sections( 'fass_settings' );
            submit_button();
            ?>

        </form>
        <?php

    }



    /**
     * Enqueue Font Awesome with the specific icons for the RRSS links
     */
    function enqueue_style()
    {
        wp_enqueue_style( 'fass-style', $this->plugin_url . '/mgstyle.css' , array(), false );
    }


    /**
     * Render the RRSS links to share.
     * Use this function inside the loop (https://codex.wordpress.org/The_Loop).
     *
     * @author  Mauricio Gelves <yo@maugelves.com>
     * @version 1.0
     */
    function render()
    {

        echo '<ul class="mg-social-share">';

        $html="";
        $permalink=urlencode(get_permalink());
        $title=urlencode(get_the_title());
        $summary=urlencode(get_the_excerpt());


        $is_rrss_active = get_option('fass_facebook');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--fb" href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u='.$permalink.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-facebook"></i></a></li>';
        endif;

        $is_rrss_active = get_option('fass_twitter');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--tw" href="#" onclick="window.open(\'https://twitter.com/share?url='.$permalink.'&amp;text='.$title.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-twitter"></i></a></li>';
        endif;

        $is_rrss_active = get_option('fass_googleplus');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--gp" href="#" onclick="window.open(\'https://plus.google.com/share?url='.$permalink.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-google-plus"></i></a></li>';
        endif;

        $is_rrss_active = get_option('fass_linkedin');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--in" href="#" onclick="window.open(\'https://www.linkedin.com/shareArticle?mini=true&url='.$permalink.'&title='.$title.'&summary='.$summary.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-linkedin"></i></a></li>';
        endif;

        $is_rrss_active = get_option('fass_email');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--email" href="mailto:?Subject='.__('Quiero compartir este enlace contigo','mgss').'&Body='.__('Hola, quiero compartir contigo este enlace, espero te sea de utilidad:','mgss').' '.get_the_title().' '.get_permalink().'" target="_blank"><i class="mg-icon-envelope"></i></a></li>';
        endif;

        echo $html;

        echo '</ul>';

    }



    /**
     * Config [mgss-render] shortcode
     *
     *  @author     Mauricio Gelves <yo@maugelves.com>
     *  @version    1.0
     */
    function render_shortcode( $atts ){
        $this->render();
    }


}


// Initialize the class
MG_Social_Share::getInstance()->init();