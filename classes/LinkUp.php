<?php
/**
 * Turns plaintext links (including hashtags and Twitter handles) into HTML links.
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
 * LinkUp
 *
 * @category TweetSync
 * @package  Wordpress
 * @author   By Robots
 * @license  Usage strictly forbidden.
 * @link     https://github.com/by-robots
 */
class LinkUp
{
    /**
     * Put links in a tweet.
     *
     * @param string $tweet The tweet to parse and add links to.
     *
     * @return string
     */
    public function link($tweet)
    {
        $tweet = $this->_linkURLs($tweet);
        $tweet = $this->_linkHashtag($tweet);
        $tweet = $this->_linkMentions($tweet);
    }

    /**
     * Turn #hastags into links.
     *
     * @param string $tweet The tweet to work with.
     *
     * @return string
     */
    private function _linkHashtag($tweet)
    {
        return preg_replace("/#([a-z_0-9]+)/i", "<a href=\"http://twitter.com/search/$1\">$0</a>", $tweet);
    }

    /**
     * Turn @mentions in to links.
     *
     * @param string $tweet The tweet to work with.
     *
     * @return string
     */
    private function _linkMentions($tweet)
    {
        return preg_replace("/@(\w+)/i", "<a href=\"http://twitter.com/$1\">$0</a>", $tweet);
    }

    /**
     * Turns normal URLs into HTML links.
     *
     * @param string $tweet The tweet to work with.
     *
     * @return string
     */
    private function _linkURLs($tweet)
    {
        return preg_replace("/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/g", '<a href="$0">$0</a> ', $tweet);
    }
}
