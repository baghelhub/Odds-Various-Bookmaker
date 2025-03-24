<?php

class Odds_Gutenberg_Widget {

    // Constructor to initialize the class
    public function __construct() {
        // Register the Gutenberg block
        add_action('init', [$this, 'register_block']);
        // Enqueue block editor assets
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_assets']);
        // Enqueue frontend assets (currently commented out)
        // add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }

    // Function to register the custom Gutenberg block
    public function register_block() {
        register_block_type('odds-comparison/api-data-block', [
            'editor_script' => 'aoc-blocks-js', // Registering editor script
            'render_callback' => [$this, 'render_block'], // Callback for rendering the block
        ]);
    }

    // Function to enqueue JavaScript assets for the block editor
    public function enqueue_block_assets() {
        wp_enqueue_script(
            'aoc-blocks-js', 
            plugins_url('../access/js/gutunberg-block.js', __FILE__),
            ['wp-blocks', 'wp-element', 'wp-editor'], // Dependencies
            filemtime(plugin_dir_path(__FILE__) . '../access/js/gutunberg-block.js') // Versioning
        );
        
        // Fetch bookmakers from options and pass them to JavaScript
        $bookmakers = get_option('odds_bookmakers', true);
        $bookmarkers_js = array();
        foreach ($bookmakers as $key => $data) {
            $bookmarkers_js[] = $data['title'];
        }
        
        wp_localize_script('aoc-blocks-js', 'aoc_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'), // AJAX URL for WordPress
            'bookmakers' => $bookmarkers_js, // Bookmakers data
        ]);
    }

    // Function to enqueue CSS assets for frontend display
    public function enqueue_frontend_assets() {
        wp_enqueue_style('aoc-blocks-css', plugins_url('../access/css/block.css', __FILE__));
    }

    // Function to render the Gutenberg block dynamically
    public function render_block($attributes) {
        // Start output buffering
        ob_start();
        ?>
        <div class="odds-comparison">
            <h3>Live Odds Comparison</h3>
            <div class="odds-data" id="odds-data"></div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                fetch('<?php echo admin_url('admin-ajax.php?action=fetch_odds'); ?>')
                    .then(response => response.json())
                    .then(data => {
                        const oddsData = document.getElementById('odds-data');
                        if (data.success) {
                            const div = document.createElement('div');
                            const oddsDataDiv = document.getElementById('odds-data');
                            oddsDataDiv.innerHTML = data.data;
                        }
                    });
            });
        </script>
        <?php
        return ob_get_clean(); // Return the buffered output
    }
}
