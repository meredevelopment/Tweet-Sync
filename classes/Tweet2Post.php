<?php
/**
 * Saves tweets as Wordpress posts.
 *
 * PHP version 5.3+
 *
 * @category  TweetSync
 * @package   Wordpress
 * @author    By Robots
 * @copyright 2013 By Robots
 * @license   Usage strictly forbidden.
 * @link      https://github.com/by-robots
 */

/**
 * Tweet2Post
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   By Robots
 * @license  Usage strictly forbidden.
 * @link     https://github.com/by-robots
 */
class Tweet2Post
{
    public function __construct($validator, $linker)
    {
        $this->validator  = $validator;
        $this->linker     = $linker;
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
        if (isset($tweets->errors)) {
            return false;
        }

        foreach ($tweets as $tweet) {
            if ($this->validator->isValid($tweet)) {
                wp_insert_post(array(
                    'post_title'    => $this->linker($tweet->text),
                    'post_category' => array($this->categoryID),
                    'post_status'   => 'publish',
                    'post_date'     => date("Y-m-d H:i:s", strtotime($tweet->created_at))
                ));
            }

            // Update the last tweet retrived value for caching if it's more recent than the one we currently have stored
            if (!get_option('tweetsync_last_tweet') or $tweet->id > get_option('tweetsync_last_tweet')) {
                update_option('tweetsync_last_tweet', $tweet->id);
            }
        }

        return true;
    }
}
