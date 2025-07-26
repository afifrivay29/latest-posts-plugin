<?php
/*
Plugin Name: Latest Posts Plugin
Description: Menampilkan posting terbaru menggunakan shortcode [latest_posts]
Version: 1.0
Author: Afif Rivaykusnanto
*/

if (!class_exists('LatestPostsPlugin')) {
    class LatestPostsPlugin {
        private $option_name = 'latest_posts_count';

        public function __construct() {
            add_shortcode('latest_posts', [$this, 'render_latest_posts']);
            add_action('admin_menu', [$this, 'add_plugin_menu']);
            add_action('admin_init', [$this, 'register_settings']);
        }

        public function render_latest_posts() {
            $count = get_option($this->option_name, 5);

            $args = [
                'numberposts' => $count,
                'post_status' => 'publish',
            ];
            $recent_posts = wp_get_recent_posts($args);

            if (empty($recent_posts)) return '<p>Tidak ada posting terbaru.</p>';

            $output = '<ul>';
            foreach ($recent_posts as $post) {
                $title = esc_html($post['post_title']);
                $date = date_i18n(get_option('date_format'), strtotime($post['post_date']));
                $link = get_permalink($post['ID']);

                $output .= "<li><a href='{$link}'>{$title}</a> - <small>{$date}</small></li>";
            }
            $output .= '</ul>';

            return $output;
        }

        public function add_plugin_menu() {
            add_options_page(
                'Pengaturan Latest Posts',
                'Latest Posts Settings',
                'manage_options',
                'latest-posts-plugin',
                [$this, 'settings_page']
            );
        }

        public function register_settings() {
            register_setting('latest_posts_group', $this->option_name, [
                'type' => 'integer',
                'sanitize_callback' => 'absint',
                'default' => 5,
            ]);
        }

        public function settings_page() {
            ?>
            <div class="wrap">
                <h1>Pengaturan Plugin Latest Posts</h1>
                <form method="post" action="options.php">
                    <?php
                    settings_fields('latest_posts_group');
                    do_settings_sections('latest_posts_group');
                    $value = get_option($this->option_name, 5);
                    ?>
                    <table class="form-table">
                        <tr valign="top">
                            <th scope="row">Jumlah posting yang ditampilkan</th>
                            <td><input type="number" name="<?php echo $this->option_name; ?>" value="<?php echo esc_attr($value); ?>" min="1" max="20"/></td>
                        </tr>
                    </table>
                    <?php submit_button(); ?>
                </form>
            </div>
            <?php
        }
    }

    new LatestPostsPlugin();
}
