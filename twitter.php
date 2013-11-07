<?php

/**
 * Retrieves tweets via Twitter's implementation of the OAuth 1.1 API.
 *
 * Makes use of https://github.com/J7mbo/twitter-api-php for the actual OAuth calls.
 *
 * PHP version 5
 *
 * @category  TweetSync
 * @package   Wordpress
 * @author    Dan Gates <dan@by-robots.com>
 * @copyright 2013 NaN Bug/Dan Gates
 * @license   Usage strictly forbidden.
 * @link      https://github.com/dangates/Tweet-Sync
 */

/**
 * Twitter
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   Dan Gates <dan@by-robots.com>
 * @license  Usage strictly forbidden.
 * @link     https://github.com/dangates/Tweet-Sync
 */
class Twitter
{

    /**
     * Checks we have all the OAuth tokens we need.
     *
     * @param array $settings The settings we have been supplied.
     *
     * @return bool
     */
    private function _checkSettings($settings)
    {
        return empty($settings['consumer_key']) or empty($settings['consumer_secret']) or empty($settings['oauth_access_token']) or empty($settings['oauth_access_token_secret'])) ? false : true;
    }

    /**
     * Retrieves the tweets using settings set in Wordpress.
     *
     * @return object An object created from the JSON response.
     */
    public function getTweets()
    {
        // Build our variables
        $since         = get_option('tweetsync_last_tweet');
        $screenName    = get_option('tweetsync_screen_name');
        $settings      = array(
            'consumer_key'              => get_option('tweetsync_consumer_key'),
            'consumer_secret'           => get_option('tweetsync_consumer_secret'),
            'oauth_access_token'        => get_option('tweetsync_access_token'),
            'oauth_access_token_secret' => get_option('tweetsync_access_token_secret')
        );

        // Check we have everyhting
        if ( ! $this->_checkSettings($settings) or empty($screenName)) return;

        // Set the final variables now we do have everything
        $requestMethod = 'GET';
        $url           = 'https://api.twitter.com/1.1/statuses/user_timeline.json';
        $getFields     = '?screen_name=' . $screenName;

        // Make the request
        include_once 'TwitterAPIExchange.php';
        $twitter = new TwitterAPIExchange($settings);

        die('<pre>' . print_r($twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest(), 1));
    }

}
