<?php

// Class for managing admin dashboard settings related to odds
class Odds_AdminDash {

    // API URL for fetching bookmakers data
    public $url = "https://api.the-odds-api.com/v4/sports/?apiKey=19c61225594292a513c4e45908a9f5d6";

    // Constructor to initialize WordPress hooks
    public function __construct() {
        add_action('admin_menu', [$this, 'add_admin_menu']); // Adds an admin menu page
        add_action('admin_init', [$this, 'settings_init']); // Initializes settings
        add_action('init', [$this, 'save_bookmakers_option_init']); // Fetches and saves bookmakers data
    }

    // Function to fetch and save bookmakers data in WordPress options
    function save_bookmakers_option_init() {
        $saved_bookmakers = get_option('odds_bookmakers');
        $count = 0;
        $bookmakers_data = array();
        $group = array();

        // If bookmakers data is not already saved, fetch from API
        if ($saved_bookmakers === false) {
            $response = wp_remote_get($this->url);
            
            // Check for API errors
            if (is_wp_error($response)) {
                return 'Error: ' . $response->get_error_message();
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);

            // Process and store up to 10 unique bookmakers
            foreach ($data as $key => $data) {
                if ($count <= 10) {
                    if (in_array($data['key'], $group)) {
                        continue;
                    }
                    $bookmakers_data[] = $data;
                    $group[] = $data['key'];
                }
                $count++;
            }
            update_option('odds_bookmakers', $bookmakers_data);
        }
    }

    // Function to add a custom menu page in the WordPress admin panel
    public function add_admin_menu() {
        add_menu_page(
            'Odds Settings',
            'Odds Settings',
            'manage_options',
            'dds_various_bookmakers',
            [$this, 'settings_page'],
            'dashicons-yes-alt'
        );
    }

    // Function to register settings and add fields to the settings page
    public function settings_init() {
        register_setting('oddsSettings', 'aoc_bookmakers');
        register_setting('oddsSettings', 'aoc_markets');
        register_setting('oddsSettings', 'aoc_links');

        // Add a settings section
        add_settings_section(
            'aoc_settings_section',
            __('Bookmakers and Markets', 'advanced-odds-comparison'),
            null,
            'odds_settings'
        );

        // Add settings fields
        add_settings_field(
            'aoc_bookmakers',
            __('Bookmakers', 'advanced-odds-comparison'),
            [$this, 'bookmakers_render'],
            'odds_settings',
            'aoc_settings_section'
        );

        add_settings_field(
            'aoc_markets',
            __('Markets', 'advanced-odds-comparison'),
            [$this, 'markets_render'],
            'odds_settings',
            'aoc_settings_section'
        );
    }

    // Function to render the settings page
    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Multiple Mark Odds Setting</h1>
            <form action="options.php" method="post">
                <?php
                settings_fields('oddsSettings');
                do_settings_sections('odds_settings');
                submit_button();
                ?>
            </form>
        </div>
        <?php
    }

    // Function to render bookmaker checkboxes
    public function bookmakers_render() {
        $options = get_option('aoc_bookmakers', []);
        $bookmakers = get_option('odds_bookmakers', true);
        
        foreach ($bookmakers as $bookmaker) {
            echo '<label>
             <input type="checkbox" name="aoc_bookmakers[]" value="' . esc_attr($bookmaker['key']) . '" ' . 
             (is_array($options) && in_array($bookmaker['key'], $options) ? 'checked' : '') . '> ' . 
             esc_html($bookmaker['title']) . 
            '</label><br>';
        }
    }

    // Function to render market checkboxes
    public function markets_render() {
        $options = get_option('aoc_markets', []);
        $markets = array('h2h', 'spreads', 'totals'); // Example markets

        foreach ($markets as $market) {
            echo '<label>
            <input type="checkbox" name="aoc_markets[]" value="' . esc_attr($market) . '" ' . 
            (is_array($options) && in_array($market, $options) ? 'checked' : '') . '> ' . 
            esc_html($market) .'</label><br>';
        }
    }

    // Function to render a textarea for links
    public function links_render() {
        $links = get_option('aoc_links', '');
        echo '<textarea name="aoc_links" rows="5" cols="50">' . esc_textarea($links) . '</textarea>';
    }
}
