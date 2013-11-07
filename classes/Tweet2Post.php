<?php

/**
 * Saves tweets as Wordpress posts.
 *
 * PHP version 5.3+
 *
 * @category  TweetSync
 * @package   Wordpress
 * @author    Dan Gates <dan@by-robots.com>
 * @copyright 2013 NaN Bug/Dan Gates
 * @license   Usage strictly forbidden.
 * @link      https://github.com/dangates/Tweet-Sync
 */

/**
 * Tweet2Post
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   Dan Gates <dan@by-robots.com>
 * @license  Usage strictly forbidden.
 * @link     https://github.com/dangates/Tweet-Sync
 */
class Tweet2Post
{

    public function __construct()
    {
        $this->categoryID = get_option('tweetsync_category_id');
    }

    /**
     * Saves a tweet (or tweets) as Wordpress posts.
     *
     * @param string $json A Json string (i.e. the reponse from the Twitter API).
     *
     * @return bool
     */
    public function saveAsPost($json)
    {
        $tweets = json_decode($json);
        if (isset($tweets->errors)) return false;

        foreach ($tweets as $tweet) {
            if ($this->_shouldSave($tweet)) {
                $post = wp_insert_post(array(
                    'post_title'    => $tweet->text,
                    'post_category' => array($this->categoryID)
                ));
            }
        }

        return true;
    }

    /**
     * Decide if this tweet should be added as a post or not. I'll actually do
     * something with this function once I've got the rest of the plug-in
     * running OK.
     *
     * @param object $tweet The tweet object.
     *
     * @return bool
     */
    private function _shouldSave($tweet)
    {
        return true;
    }

}
