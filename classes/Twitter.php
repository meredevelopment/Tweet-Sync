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
 * @author    By Robots
 * @copyright 2013 By Robots
 * @license   DBAD
 * @link      https://github.com/by-robots
 */

/**
 * Twitter
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   By Robots
 * @license  DBAD
 * @link     https://github.com/dangates/Tweet-Sync
 */
class Twitter
{
    public function __construct()
    {
        // Build our variables
        $this->since      = get_option('tweetsync_last_tweet');
        $this->screenName = get_option('tweetsync_screen_name');
        $this->includeRTs = get_option('tweetsync_include_retweets');
        $this->settings   = array(
            'consumer_key'              => get_option('tweetsync_consumer_key'),
            'consumer_secret'           => get_option('tweetsync_consumer_secret'),
            'oauth_access_token'        => get_option('tweetsync_access_token'),
            'oauth_access_token_secret' => get_option('tweetsync_access_token_secret')
        );
    }

    /**
     * Checks we have all the OAuth tokens we need.
     *
     * @param array $settings The settings we have been supplied.
     *
     * @return bool
     */
    private function _checkSettings($settings)
    {
        return empty($settings['consumer_key']) or
               empty($settings['consumer_secret']) or
               empty($settings['oauth_access_token']) or
               empty($settings['oauth_access_token_secret']) ? false : true;
    }

    /**
     * Build the API request string.
     *
     * @return string
     */
    private function _buildRequestString()
    {
        $getField = '?screen_name=' . $this->screenName . '&trim_user=true';
        if (!$this->includeRTs) {
            $getField .= '&include_rts=false';
        }

        if ($this->since !== false and ! empty($this->since)) {
            $getField .= '&since_id=' . $this->since;
        }

        return $getField;
    }

    /**
     * Retrieves the tweets using settings set in Wordpress.
     *
     * @return string The JSON response.
     */
    public function getTweets()
    {
        if (!$this->_checkSettings($this->settings) or empty($this->screenName)) {
            return;
        }

        // Make the request
        $twitter = new TwitterAPIExchange($this->settings);

        $this->result = $twitter->setGetfield($this->_buildRequestString())
                                ->buildOauth('https://api.twitter.com/1.1/statuses/user_timeline.json', 'GET')
                                ->performRequest();

        return $this->result;
    }
}
