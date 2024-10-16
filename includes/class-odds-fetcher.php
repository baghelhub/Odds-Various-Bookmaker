<?php

class AOC_Odds_Fetcher {

    public $apiKey = 'e577318e8f3b183699f40816408ca6a6';

    public function __construct() {

        add_action('wp_ajax_fetch_odds', [$this, 'fetch_odds']);
        add_action('wp_ajax_nopriv_fetch_odds', [$this, 'fetch_odds']);
    }

    public function fetch_odds() {
        // Example of fetching odds (replace with actual logic)
        $bookmakers = get_option('odds_bookmakers', true);

        // $odds = get_transient('odds_data_html');
        // if ($odds == false) {

        
            $odds = '';
            ob_start();
            
            foreach ($bookmakers as $bookmark) {
                $url = 'https://api.the-odds-api.com/v4/sports/'.$bookmark['key'].'/odds?regions=us&oddsFormat=american&apiKey=' . $this->apiKey;
        
                $response = wp_remote_get($url);
                
                if (is_wp_error($response)) {
                    continue;
                }
        
                $response_code = wp_remote_retrieve_response_code($response);
                if ($response_code !== 200) {
                    continue;
                }
        
                $data = json_decode(wp_remote_retrieve_body($response), true);
                
                $g_count = 0;
                foreach ($data as $key => $single_game) {
                    
                    if($g_count >= 10 ){
                        continue;
                    }

                    if(!empty($single_game['home_team']) && !empty($single_game['away_team']) ){
                        $title = $single_game['home_team'].' vs '.$single_game['away_team'];
                        $odds .= '<h5>'.$title.'</h5>';
                        
                        foreach ($single_game['bookmakers'] as $key => $bookmakers) {
                           
                            $odds .= '<p>'.$bookmakers['title'].'</p>';
                            $odds .= '<p>Last Update - '.$bookmakers['last_update'].'</p>';

                            foreach ($bookmakers['markets'] as $key => $value) {
                                $team_name = $value['outcomes'][0]['name'];
                                $team_price = $value['outcomes'][0]['price'];

                                $team_name2 = $value['outcomes'][1]['name'];
                                $team_price2 = $value['outcomes'][1]['price'];

                                $odds .= '<p>Team1 - '.$team_name. ', Price '.$team_price.'</p>';
                                $odds .= '<p>Team2 - '.$team_name2. ', Price '.$team_price2.'</p>';
                            }
                        }

                        $g_count++;
                    }
                }
                $odds .= '<hr>';
            }
            set_transient('odds_data_html', $odds, 86400);
        //}
        ob_get_contents();
        ob_clean();
        wp_send_json_success($odds);
        wp_die();
    }
}
