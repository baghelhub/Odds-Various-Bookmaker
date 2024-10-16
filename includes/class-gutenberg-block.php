<?php

class AOC_Gutenberg_Block {
    public function __construct() {
        add_action('init', [$this, 'register_block']);
        add_action('enqueue_block_editor_assets', [$this, 'enqueue_block_assets']);
        //add_action('wp_enqueue_scripts', [$this, 'enqueue_frontend_assets']);
    }

    public function register_block() {
        register_block_type('odds-comparison/api-data-block', [
            'editor_script' => 'aoc-blocks-js',
            'render_callback' => [$this, 'render_block'],
        ]);
    }

    public function enqueue_block_assets() {
        wp_enqueue_script(
            'aoc-blocks-js',
            plugins_url('../assets/block.js', __FILE__),
            ['wp-blocks', 'wp-element', 'wp-editor'],
            filemtime(plugin_dir_path(__FILE__) . '../assets/block.js')
        );
        $bookmakers = get_option('odds_bookmakers', true);
        $bookmarkers_js = array();
        foreach ($bookmakers as $key => $data) {
            $bookmarkers_js[] = $data['title'];
        }
        wp_localize_script('aoc-blocks-js', 'aoc_ajax', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'bookmakers' => $bookmarkers_js,
        ]);
    }

    public function enqueue_frontend_assets() {
        wp_enqueue_style('aoc-blocks-css', plugins_url('../assets/block.css', __FILE__));
    }

    public function render_block($attributes) {
        // Fetch odds using AJAX
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
                            //data.data.forEach(odds => {
                                const div = document.createElement('div');
                                //console.log('jgjhj', data.data);
                                //div.innerHTML = `${data.data}`;
                                const oddsDataDiv = document.getElementById('odds-data');
                                oddsDataDiv.innerHTML = data.data;
                            //});
                        }
                    });
            });
        </script>
        <?php
        return ob_get_clean();
    }
}
