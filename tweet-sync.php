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
class TweetSync
{

    /**
     * Adds our Wordpress actions.
     *
     * @return void
     */
    public function __construct()
    {
        require_once 'twitter-api-php/TwitterAPIExchange.php';
        require_once 'classes/Twitter.php';
        require_once 'classes/Tweet2Post.php';

        $this->twitter = new Twitter;
        $this->t2p     = new Tweet2Post;

        add_action('admin_menu', array(&$this, 'adminMenu'));
        register_activation_hook(__FILE__, array(&$this, 'activation'));
        register_deactivation_hook(__FILE__, array(&$this, 'deactivation'));
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
     */
    public function activation()
    {
        add_filter('cron_schedules', array(&$this, 'cron')); // Create the custom interval
        $res = wp_schedule_event(time(), 'tweetsync_interval', 'tweetsync_get_tweets'); // Schedule the event
        if ($res === false) die('Failed to schedule update.');

        add_action('tweetsync_get_tweets', array(&$this, 'getTweets')); // Create the event
    }

    /**
     * Plug-in deactivation - remove the scheduled updates.
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

        } else include 'screens/admin.php';
    }

    /**
     * Get the latest tweets.
     *
     * @return void
     */
    public function getTweets()
    {
        $this->t2p->saveAsPost($this->twitter->getTweets());
    }

    /**
     * Set up the cron execution interval
     */
    public function cron($schedules)
    {
        $schedules['tweetsync_interval'] = array(
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
        if ( ! isset($_POST['tweetsync_nonce']) or ! wp_verify_nonce($_POST['tweetsync_nonce'], 'update_tweetsync_settings')) exit; // No funny business

        if (isset($_POST['tweetsync_consumer_key']))                                                             update_option('tweetsync_consumer_key', $_POST['tweetsync_consumer_key']);
        if (isset($_POST['tweetsync_consumer_secret']) and ! empty($_POST['tweetsync_consumer_secret']))         update_option('tweetsync_consumer_secret', $_POST['tweetsync_consumer_secret']);
        if (isset($_POST['tweetsync_access_token']))                                                             update_option('tweetsync_access_token', $_POST['tweetsync_access_token']);
        if (isset($_POST['tweetsync_access_token_secret']) and ! empty($_POST['tweetsync_access_token_secret'])) update_option('tweetsync_access_token_secret', $_POST['tweetsync_access_token_secret']);
        if (isset($_POST['tweetsync_screen_name']))                                                              update_option('tweetsync_screen_name', $_POST['tweetsync_screen_name']);
        if (isset($_POST['tweetsync_category_id']))                                                              update_option('tweetsync_category_id', $_POST['tweetsync_category_id']);
        if (isset($_POST['tweetsync_last_tweet']))                                                               update_option('tweetsync_last_tweet', $_POST['tweetsync_last_tweet']);

        if (isset($_POST['tweetsync_refresh_rate']) and $_POST['tweetsync_refresh_rate'] != get_option('tweetsync_refresh_rate')) {
            update_option('tweetsync_refresh_rate', $_POST['tweetsync_refresh_rate']);

            // Clear the old refresh schedule, start the new one
            $this->deactivation();
            $this->activation();
        }
    }

}

new TweetSync;
