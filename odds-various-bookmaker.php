<?php
/*

   * Plugin Name: Odds Various Bookmakers
   * Description: Description: A powerful WordPress plugin that fetches and compares real-time betting odds from multiple bookmakers, providing users with up-to-date sports odds data.
   * Version: 1.1.0
   * Author: Amit Baghel
   * Text Domain: odds-various-bookmakers
   * Domain Path: /languages

 */



// Prevent direct script access for security
defined( 'ABSPATH' ) || die( 'No script kiddies please!' );

// Define plugin constants for better file handling
define( 'AOC_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );



/**
 * Include required classes for Admin panel settings Fetch live odds from API and Gutenberg block integration files.
 */
     $files = [
         'class-admin.php',
         'class-odds-fetcher.php',
         'class-gutenberg-block.php'
      ];

    foreach ($files as $file) {
        require_once AOC_PLUGIN_DIR . 'classes/' . $file;
    }


/**
 * Initialize the plugin by loading core classes
 */

function odds_class_controller() {
    
             new Odds_AdminDash();
             new Odds_DataFetch();
             new Odds_Gutenberg_Widget();

}

// Hook the initialization function to WordPress
  add_action( 'plugins_loaded', 'odds_class_controller' );
