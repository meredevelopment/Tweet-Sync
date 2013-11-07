            <form name="tweetsync_form" method="post" action="<?= str_replace('%7E', '~', $_SERVER['REQUEST_URI']) ?>">
                <p>Last checked:
                    <?php if (get_option('tweetsync_last_checked') !== false) echo ' ' . date("r", get_option('tweetsync_last_checked'));
                          else                                                echo ' Not checked yet.'; ?>
                </p>

                <p>
                    <label>
                        Consumer Key:
                        <?php $key = get_option('tweetsync_consumer_key') ?>
                        <input type="text" name="tweetsync_consumer_key" value="<?= $key ?>">
                    </label>
                </p>

                <p>
                    <label>
                        Consumer Secret:
                        <?php $secret = get_option('tweetsync_consumer_secret') ?>
                        <input type="text" name="tweetsync_consumer_secret"<?php echo ! empty($secret) ? ' placeholder="I won\'t tell..."' : '' ?>>
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
                        <?php $atSecret = get_option('tweetsync_access_token_secret') ?>
                        <input type="text" name="tweetsync_access_token_secret"<?php echo ! empty($atSecret) ? ' placeholder="...it\'s a secret."' : '' ?>>
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

                <p>
                    <label>
                        Since ID:
                        <input type="text" name="tweetsync_last_tweet" value="<?= get_option('tweetsync_last_tweet') ?>">
                    </label>
                </p>

                <input type="hidden" name="tweetsync_submitted" value="Y">
                <?php wp_nonce_field('update_tweetsync_settings', 'tweetsync_nonce') ?>
                <input type="submit" value="Save">
            </form>
