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
class Tweet_Sync
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

        // Add the menu tab so settings can be edited
        add_action('admin_menu', array(&$this, 'adminMenu'));

        // Run the plugin code after the page has loaded
        add_action('shutdown', array(&$this, 'getTweets'));
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
        // Before we do anything check that we are outside the check limit
        if ( ! $this->_doUpdate()) return;

        $this->t2p->saveAsPost($this->twitter->getTweets());

        // Update when we last checked for updates
        $this->_markUpdated();
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
        if (isset($_POST['tweetsync_refresh_rate']))                                                             update_option('tweetsync_refresh_rate', $_POST['tweetsync_refresh_rate']);
    }

    /**
     * Should we check for updates?
     *
     * @return bool
     */
    private function _doUpdate()
    {
        if (get_option('tweetsync_last_checked') !== false and
            get_option('tweetsync_last_checked') + get_option('tweetsync_refresh_rate') > time()) return false;

        return true;
    }

    /**
     * Update the last_checked field.
     *
     * @return void
     */
    private function _markUpdated()
    {
        update_option('tweetsync_last_checked', time());
    }

}

new Tweet_Sync;
