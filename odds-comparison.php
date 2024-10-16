<?php
/**
 * @package Odds Comparison
 * @version 1.0.0
 */
/*
/**
 * Plugin Name: Odds Comparison
 * Description: A plugin to compare live odds from various bookmakers.
 * Version: 1.0.0
 * Author: Krishan Kaushik
 */

 defined( 'ABSPATH' ) || die( 'No script kiddies please!' );


// Define plugin constants
define('AOC_PLUGIN_DIR', plugin_dir_path(__FILE__));

// Include required files
require_once AOC_PLUGIN_DIR . 'includes/class-admin.php';
require_once AOC_PLUGIN_DIR . 'includes/class-odds-fetcher.php';
require_once AOC_PLUGIN_DIR . 'includes/class-gutenberg-block.php';

// Initialize the plugin
function aoc_init() {
    new AOC_Admin();
    new AOC_Odds_Fetcher();
    new AOC_Gutenberg_Block();
}
add_action('plugins_loaded', 'aoc_init');