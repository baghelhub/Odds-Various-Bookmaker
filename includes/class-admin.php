<?php

class AOC_Admin {

    public $apiKey = 'e577318e8f3b183699f40816408ca6a6';
    public $url = 'https://api.the-odds-api.com/v4/sports?apiKey=e577318e8f3b183699f40816408ca6a6';

    public function __construct() {

        add_action('admin_menu', [$this, 'add_admin_menu']);
        add_action('admin_init', [$this, 'settings_init']);
        add_action('init', [$this, 'save_bookmakers_option_init']);
    }

    function save_bookmakers_option_init(){

        $saved_bookmakers = get_option('odds_bookmakers');
        $count = 0;
        $bookmakers_data = array();
        $group = array();
        if ($saved_bookmakers === false) {

            $response = wp_remote_get($this->url);
            
            if (is_wp_error($response)) {
                return 'Error: ' . $response->get_error_message();
            }

            $data = json_decode(wp_remote_retrieve_body($response), true);
            foreach ($data as $key => $data) {
                
                if($count <= 10 ){
                    
                    if(in_array($data['key'], $group) ){
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

    public function add_admin_menu() {
        add_menu_page(
            'Odds Settings',
            'Odds Settings',
            'manage_options',
            'odds_settings',
            [$this, 'settings_page'],
            'dashicons-chart-line'
        );
    }

    public function settings_init() {
        register_setting('oddsSettings', 'aoc_bookmakers');
        register_setting('oddsSettings', 'aoc_markets');
        register_setting('oddsSettings', 'aoc_links');

        add_settings_section(
            'aoc_settings_section',
            __('Select Bookmakers and Markets', 'advanced-odds-comparison'),
            null,
            'odds_settings'
        );

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

        // add_settings_field(
        //     'aoc_links',
        //     __('Bookmaker Links', 'advanced-odds-comparison'),
        //     [$this, 'links_render'],
        //     'odds_settings',
        //     'aoc_settings_section'
        // );
    }

    public function settings_page() {
        ?>
        <div class="wrap">
            <h1>Odds Settings</h1>
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

    public function bookmakers_render() {
        $options = get_option('aoc_bookmakers', []);
        // Example options, replace with actual bookmakers
        $bookmakers = get_option('odds_bookmakers', true);
        
        foreach ($bookmakers as $bookmaker) {
            echo '<label><input type="checkbox" name="aoc_bookmakers[]" value="' . esc_attr($bookmaker['key']) . '"' . (in_array($bookmaker['key'], $options) ? ' checked' : '') . '> ' . esc_html($bookmaker['title']) . '</label><br>';
        }
    }

    public function markets_render() {
        $options = get_option('aoc_markets', []);
        // Example options, replace with actual markets
        $markets = array('h2h', 'spreads', 'totals');

        foreach ($markets as $market) {
            echo '<label><input type="checkbox" name="aoc_markets[]" value="' . esc_attr($market) . '"' . (in_array($market, $options) ? ' checked' : '') . '> ' . esc_html($market) . '</label><br>';
        }
    }

    public function links_render() {
        $links = get_option('aoc_links', '');
        echo '<textarea name="aoc_links" rows="5" cols="50">' . esc_textarea($links) . '</textarea>';
    }
}
