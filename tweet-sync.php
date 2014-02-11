<?php
/**
 * Plugin Name: Tweet Sync
 * Plugin URI: https://github.com/by-robots/Tweet-Sync
 * Description: Automatically grab your tweets from Twitter and syncs them as blog posts in Wordpress.
 * Version: 1.0
 * Author: By Robots
 * Author URI: https://github.com/by-robots
 * License: DBAD
 */
require_once __DIR__ . '/twitter-api-php/TwitterAPIExchange.php';
require_once __DIR__ . '/classes/Twitter.php';
require_once __DIR__ . '/classes/Tweet2Post.php';
require_once __DIR__ . '/classes/PostValidator.php';
require_once __DIR__ . '/classes/LinkUp.php';

/**
 * TweetSync
 *
 * @category Plugins
 * @package  Wordpress
 * @author   By Robots
 * @license  DBAD
 * @link     https://github.com/by-robots
 */
class TweetSync
{
    /**
     * Adds our Wordpress actions.
     *
     * @return void
     */
    public function __construct()
    {
        add_action('admin_menu', array($this, 'adminMenu'));
        add_action('tweetsync_get_tweets', array($this, 'getTweets')); // Create the event

        add_filter('cron_schedules', array($this, 'cron')); // Create the custom interval

        register_activation_hook(__FILE__, array($this, 'activation'));
        register_deactivation_hook(__FILE__, array($this, 'deactivation'));
    }

    /**
     * Set up the admin area.
     *
     * @return void
     */
    public function adminMenu()
    {
        add_menu_page('Tweet Sync', 'Tweet Sync', 'manage_options', 'tweet_sync', array($this, 'settingsPage'));
    }

    /**
     * Plugin activation. Schedule auto-updates.
     *
     * @return void
     */
    public function activation()
    {
        if (wp_next_scheduled('tweetsync_get_tweets') !== false) {
            $this->deactivation();
        }

        wp_schedule_event(time(), 'tweetsync', 'tweetsync_get_tweets'); // Schedule the event
    }

    /**
     * Plug-in deactivation - remove the scheduled updates.
     *
     * @return void
     */
    public function deactivation()
    {
        wp_clear_scheduled_hook('tweetsync_get_tweets');
    }

    /**
     * Display the admin settings page.
     *
     * @return void
     */
    public function settingsPage()
    {
        if (isset($_POST['tweetsync_submitted'])) {
            $this->_handleInput();
            include 'screens/admin_saved.php';

        } elseif (isset($_GET['exec']) and $_GET['exec'] == 'now') {
            $this->_log('Manually retrieving tweets.');

            $this->getTweets();
            include 'screens/admin_updated.php';

        } else {
            include 'screens/admin.php';
        }
    }

    /**
     * Get the latest tweets.
     *
     * @return void
     */
    public function getTweets()
    {
        $twitter = new Twitter;
        $t2p     = new Tweet2Post(
            new PostValidator(new LinkUp),
            new LinkUp
        );

        if (!$t2p->saveAsPost($twitter->getTweets())) {
            $this->_log('Error retrieving tweets.');
            $this->_log($twitter->result);
        }
    }

    /**
     * Set up the cron execution interval
     *
     * @param array $schedules An array of Wordpress cron schedules. We add our new
     *                         schedule interval to this before giving it back to WP
     *
     * @return array Wordpress' schedules array.
     */
    public function cron($schedules)
    {
        $schedules['tweetsync'] = array(
            'interval' => get_option('tweetsync_refresh_rate') === false ? 3600 : get_option('tweetsync_refresh_rate'),
            'display'  =>  __('Once Every ' . get_option('tweetsync_refresh_rate') === false ? 3600 : get_option('tweetsync_refresh_rate') . ' seconds')
        );

        return $schedules;
    }

    /**
     * Handles admin form input.
     *
     * @return void
     */
    private function _handleInput()
    {
        if (!isset($_POST['tweetsync_nonce']) or ! wp_verify_nonce($_POST['tweetsync_nonce'], 'update_tweetsync_settings')) {
            exit; // No funny business
        }

        // If the refresh rate has changed update the scedule
        if (isset($_POST['tweetsync_refresh_rate']) and $_POST['tweetsync_refresh_rate'] != get_option('tweetsync_refresh_rate')) {
            $this->_updateRefreshRate();
        }

        // Toggle retweets
        if (isset($_POST['tweetsync_include_retweets']) and !get_option('tweetsync_include_retweets')) {
            update_option('tweetsync_include_retweets', true);
        } elseif (!isset($_POST['tweetsync_include_retweets']) and get_option('tweetsync_include_retweets')) {
            update_option('tweetsync_include_retweets', false);
        }

        // Valid keys to save from $_POST
        $validKeys = array(
            'tweetsync_consumer_key',
            'tweetsync_consumer_secret',
            'tweetsync_access_token',
            'tweetsync_access_token_secret',
            'tweetsync_screen_name',
            'tweetsync_category_id',
            'tweetsync_last_tweet'
        );

        foreach ($_POST as $key => $post) {
            if (in_array($key, $validKeys)) {
                if ($key == 'tweetsync_last_tweet') {
                    update_option($key, $post);
                } elseif (!empty($post)) {
                    update_option($key, $post);
                }
            }
        }
    }

    /**
     * Updates the refresh rate, refreshes the update schedule
     *
     * @return void
     */
    private function _updateRefreshRate()
    {
        update_option('tweetsync_refresh_rate', $_POST['tweetsync_refresh_rate']);

        $this->deactivation();
        $this->activation();
    }

    /**
     * A quick function for logging issues. Used for debugging. I like to add
     * define('WP_DEBUG_LOG', true); to wp-config.php so the logs are quickly
     * available in wp-content/debug.log.
     *
     * http://fuelyourcoding.com/simple-debugging-with-wordpress/
     *
     * @param mixed $message The message to write to the log. Will write a string
     *                       directly, or print_r an array or object.
     *
     * @return void
     */
    private function _log($message)
    {
        if (WP_DEBUG === true) {
            if (is_array($message) or is_object($message)) {
                error_log(print_r($message, 1));
            } else {
                error_log($message);
            }
        }
    }
}

new TweetSync;
