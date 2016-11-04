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

        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_style' ) );
        add_action( 'admin_menu', array( $this, 'create_options_menu' ) );
        add_action( 'admin_init', array( $this, 'config_settings' ) );
        add_shortcode( 'mgss-render', array($this, 'render_shortcode' ) );

    }


    // ============= METHODS =============
    function config_settings()
    {

        add_settings_section(
            'mgss_page_section',
            __( 'Please choose the Social Networks to share your content.', 'mgss' ),
            array($this, 'mgss_settings_section_callback'),
            'mgss_settings'
        );

        add_settings_field(
            'mgss_facebook_field',
            __( 'Use Facebook?', 'mgss' ),
            array($this, 'mgss_facebook_field_render' ),
            'mgss_settings',
            'mgss_page_section'
        );
        register_setting( 'mgss_settings', 'mgss_facebook' );

        add_settings_field(
            'mgss_twitter_field',
            __( 'Use Twitter?', 'mgss' ),
            array($this, 'mgss_twitter_field_render' ),
            'mgss_settings',
            'mgss_page_section'
        );
        register_setting( 'mgss_settings', 'mgss_twitter' );

        add_settings_field(
            'mgss_linkedin_field',
            __( 'Use LinkedIn?', 'mgss' ),
            array($this, 'mgss_linkedin_field_render' ),
            'mgss_settings',
            'mgss_page_section'
        );
        register_setting( 'mgss_settings', 'mgss_linkedin' );

        add_settings_field(
            'mgss_googleplus_field',
            __( 'Use Google Plus?', 'mgss' ),
            array($this, 'mgss_googleplus_field_render' ),
            'mgss_settings',
            'mgss_page_section'
        );
        register_setting( 'mgss_settings', 'mgss_googleplus' );

        add_settings_field(
            'mgss_email_field',
            __( 'Use Email?', 'mgss' ),
            array($this, 'mgss_email_field_render' ),
            'mgss_settings',
            'mgss_page_section'
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


    function mgss_settings_section_callback() {}


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
        wp_enqueue_style( 'mgss-fontawesome', plugins_url('css/font-awesome.min.css', __FILE__) , array(), 1.0 );
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

        $is_rrss_active = get_option('mgss_googleplus');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--gp" href="#" onclick="window.open(\'https://plus.google.com/share?url='.$permalink.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="fa fa-google-plus"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_linkedin');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--in" href="#" onclick="window.open(\'https://www.linkedin.com/shareArticle?mini=true&url='.$permalink.'&title='.$title.'&summary='.$summary.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="fa fa-linkedin"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_facebook');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--fb" href="#" onclick="window.open(\'https://www.facebook.com/sharer/sharer.php?u='.$permalink.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="fa fa-facebook"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_twitter');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--tw" href="#" onclick="window.open(\'https://twitter.com/share?url='.$permalink.'&amp;text='.$title.'\',\'MsgWindow\',\'width=640,height=400\');return false;" target="_blank"><i class="fa fa-twitter"></i></a></li>';
        endif;

        $is_rrss_active = get_option('mgss_email');
        if( !empty( $is_rrss_active ) ):
            $html.='<li><a class="mgss__item mgss__item--email" href="mailto:?Subject='.__('Quiero compartir este enlace contigo','mgss').'&Body='.__('Hola, quiero compartir contigo este enlace, espero te sea de utilidad:','mgss').' '.get_the_title().' '.get_permalink().'" target="_blank"><i class="fa fa-envelope-o"></i></a></li>';
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