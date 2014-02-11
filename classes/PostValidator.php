<?php
/**
 * Validates a given post, deciding if it should be saved or not.
 *
 * PHP version 5.3+
 *
 * @category  TweetSync
 * @package   Wordpress
 * @author    By Robots
 * @copyright 2013 By Robots
 * @license   DBAD
 * @link      https://github.com/by-robots
 */

/**
 * PostValidator
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   By Robots
 * @license  DBAD
 * @link     https://github.com/by-robots
 */
class PostValidator
{
    public function __construct($linker)
    {
        $this->linker = $linker;
    }

    /**
     * Decides if the post should be saved.
     *
     * @param object $post The post to test
     *
     * @return bool
     */
    public function isValid($post)
    {
        if ($this->_postByTitle(array(
            'post_title'  => $this->linker->link($post->text),
            'post_status' => 'publish',
            'post_date'   => date("Y-m-d H:i:s", strtotime($post->created_at))
        ))) {
            return false;
        }

        // TO DO: Retweets
        return true;
    }

    /**
     * Check to see if a post already exists given a set of rules.
     *
     * @param array $fields The fields to filter by
     *
     * @return bool
     */
    private function _postByTitle($fields)
    {
        global $wpdb; // Yuck

        foreach ($fields as $key => $value) {
            if (!isset($sqlStr)) {
                $sqlStr  = "$key='" . mysql_real_escape_string($value) . "'";
            } else {
                $sqlStr .= " AND $key='" . mysql_real_escape_string($value) . "'";
            }
        }

        $res = $wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE $sqlStr AND post_type='post';");

        if (empty($res)) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check to see if the tweet is an old style retweet. (TO DO)
     *
     * @param object $post The tweet to check
     *
     * @return bool
     */
    private function _isRetweet($post)
    {
        return false;
    }
}
