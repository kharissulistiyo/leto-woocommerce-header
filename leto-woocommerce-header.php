<?php

/**
 * Leto WooCommerce Header
 *
 * @package     Leto WooCommerce Header
 * @author      kharisblank
 * @copyright   2021 kharisblank
 * @license     GPL-2.0+
 *
 * @leto-woocommerce-header
 * Plugin Name: Leto WooCommerce Header
 * Plugin URI:  https://easyfixwp.com/
 * Description: This plugin adds option to enable header image on shop page of a website that is runnig Leto theme.
 * Version:     0.0.6
 * Author:      kharisblank
 * Author URI:  https://easyfixwp.com
 * Text Domain: leto-woocommerce-header
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 *
 */

//  Exit if accessed directly.
defined('ABSPATH') || exit;

define( 'EFW_LETO_WC_HEADER_FILE', __FILE__ );
define( 'EFW_LETO_WC_HEADER_TEXT_DOMAIN', dirname(__FILE__) );
define( 'EFW_LETO_WC_HEADER_DIRECTORY_URL', plugins_url( null, EFW_LETO_WC_HEADER_FILE ) );
define( 'EFW_LETO_WC_HEADER_DIR_PATH', untrailingslashit( plugin_dir_path( __FILE__ ) ) );

if ( !class_exists('EFW_Leto_WC_Header') ) :

  class EFW_Leto_WC_Header {

    public $textdomain = EFW_LETO_WC_HEADER_TEXT_DOMAIN;

    public function __construct() {
      add_action( 'customize_register', array($this, 'plugin_settings') );
      add_action( 'leto_after_header', array($this, 'leto_hero') );
    }


    /**
     * Check whether Leto theme is active or not
     * @return boolean true if either Leto or Leto Pro is active
     */
    function is_leto_active() {

      $theme  = wp_get_theme();
      $parent = wp_get_theme()->parent();

      if ( ($theme != 'Leto' ) && ($parent != 'Leto') ) {
        return false;
      }

      return true;

    }
    /**
     * Add customizer
     */
    public function plugin_settings( $wp_customize ) {

      if( !$this->is_leto_active() ) {
        return;
      }

      $prefix = 'efw_leto_wc_header_';

      // Plugin settings

      $wp_customize->add_panel(
          $prefix.'settings',
          array(
              'title' => __('Leto WC Header', $this->textdomain),
              'priority' => 9999,
          )
      );

      // Shop page

      $wp_customize->add_section(
          $prefix.'shop_page',
          array(
              'title' => __('Shop page', $this->textdomain),
              'priority' => 7,
              'panel' => $prefix.'settings'
          )
      );
      $wp_customize->add_setting(
          $prefix.'shop_page_image',
          array(
              'default' => '',
              'sanitize_callback' => 'esc_url_raw',
          )
      );
      $wp_customize->add_control(
          new WP_Customize_Cropped_Image_Control(
              $wp_customize,
              $prefix.'shop_page_image',
              array(
                 'label'          => __( 'Enable header image on shop page?', $this->textdomain ),
                 'type'           => 'image',
                 'width'          => 1920,
                 'height'         => 800,
                 'flex_width'     => true,
                 'flex_height'    => true,
                 'section'        => $prefix.'shop_page',
                 'settings'       => $prefix.'shop_page_image',
                 'priority'       => 5,
              )
          )
      );

    }

    /**
     * Page header
     * @param  string $image_url Image URL
     * @return string
     */
    public function page_header($image_url) {

      if( empty($image_url) || '' == $image_url ) {
        return;
      }

      $page_title = '';
      if( function_exists('woocommerce_page_title') ) {
        $page_title = woocommerce_page_title(false);
      }

      $image = get_header_image_tag(array(
        'src'       => $image_url,
        'width'     => 1920,
        'height'    => 800,
        'alt'       => $page_title,
      ));


      return sprintf('<div class="hero-area"><div id="wp-custom-header" class="wp-custom-header">%s</div></div>', $image );

    }


    /**
     * Show Leto hero on specific WC page
     * @return void
     */
    public function leto_hero() {

      if( !$this->is_leto_active() ) {
        return;
      }

      if( !function_exists('is_shop') ) {
        return;
      }

      $shop_page_image_url = get_theme_mod('efw_leto_wc_header_shop_page_image');

      if( is_shop() ) {
        echo $this->page_header($shop_page_image_url);
      }

    }

  }
  new EFW_Leto_WC_Header;

endif;
