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
 * @license   Usage strictly forbidden.
 * @link      https://github.com/by-robots
 */

/**
 * PostValidator
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   By Robots
 * @license  Usage strictly forbidden.
 * @link     https://github.com/by-robots
 */
class PostValidator
{
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
            'post_title'  => $post->text,
            'post_status' => 'publish',
            'post_date'   => date("Y-m-d H:i:s", strtotime($post->created_at))
        ))) return false;

        // More rules here...
        return true;
    }

    /**
     * Check to see if a post already exists given a set of rules.
     *
     * @param array $fields The fields to filter by
     *
     * @return object | false
     */
    private function _postByTitle($fields)
    {
        global $wpdb; // Yuck

        foreach ($fields as $key => $value) {
            if ( ! isset($sqlStr)) $sqlStr = "$key='" . mysql_real_escape_string($value) . "'";
            else                   $sqlStr .= " AND $key='" . mysql_real_escape_string($value) . "'";
        }

        if ($wpdb->get_results("SELECT ID FROM $wpdb->posts WHERE $sqlStr AND post_type='post';")) return true;
        return false;
    }
}
