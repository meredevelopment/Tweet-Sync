            <form name="tweetsync_form" method="post" action="<?= str_replace('%7E', '~', $_SERVER['REQUEST_URI']) ?>">
                <p>Get the following four values from the <a href="https://dev.twitter.com/">Twitter Developers area</a>.</p>

                <p>
                    <label>
                        Consumer Key:
                        <input type="text" name="tweetsync_consumer_key" value="<?= get_option('tweetsync_consumer_key') ?>">
                    </label>
                </p>

                <p>
                    <label>
                        Consumer Secret:
                        <input type="text" name="tweetsync_consumer_secret"<?php echo get_option('tweetsync_consumer_secret') !== false ? ' placeholder="I won\'t tell..."' : '' ?>>
                    </label>
                </p>

                <p>
                    <label>
                        Access token:
                        <input type="text" name="tweetsync_access_token" value="<?= get_option('tweetsync_access_token') ?>">
                    </label>
                </p>

                <p>
                    <label>
                        Access token secret:
                        <input type="text" name="tweetsync_access_token_secret"<?php echo get_option('tweetsync_access_token_secret') !== false ? ' placeholder="...it\'s a secret."' : '' ?>>
                    </label>
                </p>

                <p>
                    <label>
                        Twitter name (without the @):
                        <input type="text" name="tweetsync_screen_name" value="<?= get_option('tweetsync_screen_name') ?>">
                    </label>
                </p>

                <p>
                    <label>
                        Post category ID:
                        <input type="text" name="tweetsync_category_id" value="<?= get_option('tweetsync_category_id') ?>">
                    </label>
                </p>

                <p>
                    <label>
                        Refresh after
                        <input type="text" name="tweetsync_refresh_rate" value="<?= get_option('tweetsync_refresh_rate') ?>"> seconds.
                    </label>
                </p>

                <p><strong>Note:</strong> Leaving this field blank will reset it to the default value (1 hour).</p>

                <p>
                    <label>
                        Since ID:
                        <input type="text" name="tweetsync_last_tweet" value="<?= get_option('tweetsync_last_tweet') ?>">
                    </label>
                </p>

                <p><strong>Important:</strong> Emptying this field will cause tweets that have already been retrieved to be fetched and stored again.</p>

                <input type="hidden" name="tweetsync_submitted" value="Y">
                <?php wp_nonce_field('update_tweetsync_settings', 'tweetsync_nonce') ?>
                <input type="submit" value="Save">
            </form>

            <p><a href="<?= str_replace('%7E', '~', $_SERVER['REQUEST_URI']) ?>&amp;exec=now">Update now</a>.</p>
