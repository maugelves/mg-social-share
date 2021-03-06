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
            'mgss_sn_picker',
            __( 'Please choose the Social Networks to share your content.', 'mgss' ),
            array($this, 'mgss_settings_sn_cb'),
            'mgss_settings'
        );

        add_settings_field(
            'mgss_facebook_field',
            __( 'Use Facebook?', 'mgss' ),
            array($this, 'mgss_facebook_field_render' ),
            'mgss_settings',
            'mgss_sn_picker'
        );
        register_setting( 'mgss_settings', 'mgss_facebook' );

        add_settings_field(
            'mgss_twitter_field',
            __( 'Use Twitter?', 'mgss' ),
            array($this, 'mgss_twitter_field_render' ),
            'mgss_settings',
            'mgss_sn_picker'
        );
        register_setting( 'mgss_settings', 'mgss_twitter' );

        add_settings_field(
            'mgss_whatsapp_field',
            __( 'Use WhatsApp?', 'mgss' ),
            array($this, 'mgss_whatsapp_field_render' ),
            'mgss_settings',
            'mgss_sn_picker'
        );
        register_setting( 'mgss_settings', 'mgss_whatsapp' );

        add_settings_field(
            'mgss_linkedin_field',
            __( 'Use LinkedIn?', 'mgss' ),
            array($this, 'mgss_linkedin_field_render' ),
            'mgss_settings',
            'mgss_sn_picker'
        );
        register_setting( 'mgss_settings', 'mgss_linkedin' );

        add_settings_field(
            'mgss_googleplus_field',
            __( 'Use Google Plus?', 'mgss' ),
            array($this, 'mgss_googleplus_field_render' ),
            'mgss_settings',
            'mgss_sn_picker'
        );
        register_setting( 'mgss_settings', 'mgss_googleplus' );

        add_settings_field(
            'mgss_email_field',
            __( 'Use Email?', 'mgss' ),
            array($this, 'mgss_email_field_render' ),
            'mgss_settings',
            'mgss_sn_picker'
        );
        register_setting( 'mgss_settings', 'mgss_email' );



    }


    /**
     * Render Facebook Field
     */
    function mgss_facebook_field_render(  ) {

        $mgss_item = get_option( 'mgss_facebook' );
        ?>
        <input type='checkbox' name='mgss_facebook' <?php checked( $mgss_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Twitter Field
     */
    function mgss_twitter_field_render(  ) {

        $mgss_item = get_option( 'mgss_twitter' );
        ?>
        <input type='checkbox' name='mgss_twitter' <?php checked( $mgss_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Linkedin Field
     */
    function mgss_linkedin_field_render(  ) {

        $mgss_item = get_option( 'mgss_linkedin' );
        ?>
        <input type='checkbox' name='mgss_linkedin' <?php checked( $mgss_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Google Plus Field
     */
    function mgss_googleplus_field_render(  ) {

        $mgss_item = get_option( 'mgss_googleplus' );
        ?>
        <input type='checkbox' name='mgss_googleplus' <?php checked( $mgss_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render Email Field
     */
    function mgss_email_field_render(  ) {

        $mgss_item = get_option( 'mgss_email' );
        ?>
        <input type='checkbox' name='mgss_email' <?php checked( $mgss_item, 1 ); ?> value='1'>
        <?php

    }


    /**
     * Render WhatsApp Field
     */
    function mgss_whatsapp_field_render(  ) {

        $mgss_item = get_option( 'mgss_whatsapp' );
        ?>
        <input type='checkbox' name='mgss_whatsapp' <?php checked( $mgss_item, 1 ); ?> value='1'>
        <?php

    }


    function mgss_settings_sn_cb() {}


    /**
     * Create the page for the options variables
     */
    function create_options_menu()
    {
        add_submenu_page(
            'options-general.php',
            __('MG Social Share', 'mgss'),
            __('MG Social Share', 'mgss'),
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
            settings_fields( 'mgss_settings' );
            do_settings_sections( 'mgss_settings' );
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
        wp_enqueue_style( 'mg-social-fonts', $this->plugin_url . '/mgstyle.css' , array(), false );
    }


    /**
     * Render the RRSS links to share.
     * Use this function inside the loop (https://codex.wordpress.org/The_Loop).
     *
     * @author  Mauricio Gelves <yo@maugelves.com>
     * @param   $output_later   bool    if true return the HTML as string.
     * @return  void|string
     * @version 1.0
     */
    function render( $output_later = false )
    {

        // Variable
        $html="";

        $html .= '<ul class="mg-social-share">';

        $permalink=urlencode(get_permalink());
        $title=urlencode(get_the_title());
        $summary=urlencode(get_the_excerpt());


        $is_rrss_active = get_option('mgss_whatsapp');
        if( !empty( $is_rrss_active ) && wp_is_mobile() ):
        $html.='<li class="mgss__item"><a class="mgss__link mgss__link--whatsapp" href="whatsapp://send?text=' . $title . ' - ' . $permalink . '" data-text="Take a look at this awesome website:"><i class="mg-icon-whatsapp"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_facebook');
        if( !empty( $is_rrss_active ) ):
            $html.='<li class="mgss__item"><a class="mgss__link mgss__link--fb" href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u='.$permalink.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-facebook"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_twitter');
        if( !empty( $is_rrss_active ) ):
            $html.='<li class="mgss__item"><a class="mgss__link mgss__link--tw" href="#" onclick="window.open(\'https://twitter.com/share?url='.$permalink.'&amp;text='.$title.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-twitter"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_googleplus');
        if( !empty( $is_rrss_active ) ):
            $html.='<li class="mgss__item"<a class="mgss__link mgss__link--gp" href="#" onclick="window.open(\'https://plus.google.com/share?url='.$permalink.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-google"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_linkedin');
        if( !empty( $is_rrss_active ) ):
            $html.='<li class="mgss__item"><a class="mgss__link mgss__link--in" href="#" onclick="window.open(\'https://www.linkedin.com/shareArticle?mini=true&url='.$permalink.'&title='.$title.'&summary='.$summary.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="mg-icon-linkedin"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_email');
        if( !empty( $is_rrss_active ) ):
            $html.='<li class="mgss__item"><a class="mgss__link mgss__item--email" href="mailto:?Subject='.__('Quiero compartir este enlace contigo','mgss').'&Body='.__('Hola, quiero compartir contigo este enlace, espero te sea de utilidad:','mgss').' '.get_the_title().' '.get_permalink().'" target="_blank"><i class="mg-icon-mail"></i></a></li>';
        endif;

        $html .= '</ul>';

        if( $output_later ) {
            return $html;
        }
        else{
            echo $html;
        }

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