<?php


class Odds_DataFetch {

    // API key for the odds API
    public $apiKey = '19c61225594292a513c4e45908a9f5d6';

    // Constructor method
    public function __construct() {
        // Hooking the AJAX actions for logged-in users
        add_action('wp_ajax_fetch_odds', [$this, 'fetch_odds']);
        // Hooking the AJAX actions for non-logged-in users
        add_action('wp_ajax_nopriv_fetch_odds', [$this, 'fetch_odds']);
    }

    // Function to fetch odds data
    public function fetch_odds() {
        // Get the list of bookmakers from WordPress options
        $bookmakers = get_option('odds_bookmakers', true);

        // Check if the odds data is already cached
        $odds = get_transient('odds_data_html');
        if ($odds == false) { // If cached data doesn't exist

            $odds = ''; // Initialize the odds variable
            ob_start(); // Start output buffering
            
            // Loop through each bookmaker
            foreach ($bookmakers as $bookmark) {
                // Construct the API request URL
                $url = 'https://api.the-odds-api.com/v4/sports/'.$bookmark['key'].'/odds?regions=us&oddsFormat=american&apiKey=' . $this->apiKey;
        
                // Make a request to the external API
                $response = wp_remote_get($url);
                
                // Check for any errors in the request
                if (is_wp_error($response)) {
                    continue; // Skip this iteration if there's an error
                }
        
                // Retrieve the HTTP response code
                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code !== 200) {
                    continue; // Skip this iteration if the response is not successful
                }
        
                // Decode the JSON response into an associative array
                $data = json_decode(wp_remote_retrieve_body($response), true);
                
                $g_count = 0; // Initialize game count
                foreach ($data as $key => $single_game) {
                    
                    // Limit the number of games displayed to 10
                    if($g_count >= 10 ){
                        continue;
                    }

                    // Ensure both teams exist in the response
                    if(!empty($single_game['home_team']) && !empty($single_game['away_team']) ){
                        // Construct the game title
                        $title = $single_game['home_team'].' vs '.$single_game['away_team'];
                        $odds .= '<h5>'.$title.'</h5>';
                        
                        // Loop through the bookmakers' data
                        foreach ($single_game['bookmakers'] as $key => $bookmakers) {
                           
                            // Display bookmaker title and last update time
                            $odds .= '<p>'.$bookmakers['title'].'</p>';
                            $odds .= '<p>Last Update - '.$bookmakers['last_update'].'</p>';

                            // Loop through the markets data
                            foreach ($bookmakers['markets'] as $key => $value) {
                                // Extract team names and their respective odds
                                $team_name = $value['outcomes'][0]['name'];
                                $team_price = $value['outcomes'][0]['price'];

                                $team_name2 = $value['outcomes'][1]['name'];
                                $team_price2 = $value['outcomes'][1]['price'];

                                // Display odds for both teams
                                $odds .= '<p>Team1 - '.$team_name. ', Price '.$team_price.'</p>';
                                $odds .= '<p>Team2 - '.$team_name2. ', Price '.$team_price2.'</p>';
                            }
                        }

                        $g_count++; // Increment the game count
                    }
                }

                $odds .= '<hr>'; // Add a separator between games
            }

            // Cache the data using WordPress transient API for 24 hours
            set_transient('odds_data_html', $odds, 86400);
        }

        // Clear the output buffer (prevent empty output issues)
        ob_get_contents();
        ob_clean();

        // Send the data as a JSON response
        wp_send_json_success($odds);
        wp_die(); // Terminate script execution after AJAX response
    }
}
