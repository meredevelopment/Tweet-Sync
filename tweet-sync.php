<?php

/**
 * Plugin Name: Tweet Sync
 * Plugin URI: https://github.com/dangates/Tweet-Sync
 * Description: Automatically grab your tweets from Twitter and save them as blog posts.
 * Version: 0.1
 * Author: By Robots
 * Author URI: http://www.by-robots.com
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
        add_action('admin_menu', array(&$this, 'adminMenu'));
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
     * Send the user off to Twitter for verification.
     *
     * @return void
     */
    public function verifyPage()
    {
        // TO DO
    }


    /**
     * Get the latest tweets.
     *
     * @return void
     */
    public function getTweets()
    {
        include_once 'twitter.php';

        $twitter = new Twitter;
        return $twitter->getTweets();
    }


    /**
     * Handles admin form input.
     *
     * @return void
     */
    private function _handleInput()
    {
        if (isset($_POST['tweetsync_consumer_key']))                                                             update_option('tweetsync_consumer_key', $_POST['tweetsync_consumer_key']);
        if (isset($_POST['tweetsync_consumer_secret']) and ! empty($_POST['tweetsync_consumer_secret']))         update_option('tweetsync_consumer_secret', $_POST['tweetsync_consumer_secret']);
        if (isset($_POST['tweetsync_access_token']))                                                             update_option('tweetsync_access_token', $_POST['tweetsync_access_token']);
        if (isset($_POST['tweetsync_access_token_secret']) and ! empty($_POST['tweetsync_access_token_secret'])) update_option('tweetsync_access_token_secret', $_POST['tweetsync_access_token_secret']);
        if (isset($_POST['tweetsync_screen_name']))                                                              update_option('tweetsync_screen_name', $_POST['tweetsync_screen_name']);
    }

}

new Tweet_Sync;