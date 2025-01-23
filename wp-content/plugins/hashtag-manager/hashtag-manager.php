<?php
/**
 * Plugin Name: Hashtag Manager
 * Description: Manage automated hashtags for your content
 * Version: 1.0.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit;
}

// Create database table on plugin activation
function hashtag_manager_activate() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hashtag_sets';
    
    $charset_collate = $wpdb->get_charset_collate();
    
    $sql = "CREATE TABLE IF NOT EXISTS $table_name (
        id mediumint(9) NOT NULL AUTO_INCREMENT,
        keyword varchar(255) NOT NULL,
        tag1 varchar(255),
        tag2 varchar(255),
        tag3 varchar(255),
        tag4 varchar(255),
        PRIMARY KEY  (id)
    ) $charset_collate;";
    
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}
register_activation_hook(__FILE__, 'hashtag_manager_activate');

// Add menu item to WordPress admin
function hashtag_manager_menu() {
    add_menu_page(
        'Hashtag Manager',
        'Hashtag Manager',
        'manage_options',
        'hashtag-manager',
        'hashtag_manager_page',
        'dashicons-tag'
    );
}
add_action('admin_menu', 'hashtag_manager_menu');

// Enqueue necessary styles
function hashtag_manager_enqueue_styles() {
    wp_enqueue_style('hashtag-manager-styles', plugins_url('css/style.css', __FILE__));
}
add_action('admin_enqueue_scripts', 'hashtag_manager_enqueue_styles');

// Add meta box to post editor
function add_hashtag_automation_meta_box() {
    add_meta_box(
        'hashtag_automation_meta_box',
        'Hashtag Automation',
        'render_hashtag_automation_meta_box',
        'post',
        'side',
        'high'
    );
}
add_action('add_meta_boxes', 'add_hashtag_automation_meta_box');

// Render meta box content
function render_hashtag_automation_meta_box($post) {
    $enable_automation = get_post_meta($post->ID, '_enable_hashtag_automation', true);
    // Default to checked if meta value doesn't exist
    $enable_automation = ($enable_automation === '') ? '1' : $enable_automation;
    
    wp_nonce_field('hashtag_automation_meta_box', 'hashtag_automation_meta_box_nonce');
    ?>
    <div class="hashtag-automation-wrapper">
        <label>
            <input type="checkbox" name="enable_hashtag_automation" value="1" <?php checked($enable_automation, '1'); ?>>
            Enable automatic hashtags
        </label>
        <div id="hashtag-preview"></div>
    </div>
    <?php
}

// Save meta box data
function save_hashtag_automation_meta($post_id) {
    if (!isset($_POST['hashtag_automation_meta_box_nonce'])) {
        return;
    }
    
    if (!wp_verify_nonce($_POST['hashtag_automation_meta_box_nonce'], 'hashtag_automation_meta_box')) {
        return;
    }
    
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    $enable_automation = isset($_POST['enable_hashtag_automation']) ? '1' : '0';
    update_post_meta($post_id, '_enable_hashtag_automation', $enable_automation);
}
add_action('save_post', 'save_hashtag_automation_meta');

// Function to process content and add hashtags
function process_content_for_hashtags($post_id) {
    // Check if hashtag automation is enabled for this post
    $enable_automation = get_post_meta($post_id, '_enable_hashtag_automation', true);
    if ($enable_automation !== '1') {
        return;
    }
    
    global $wpdb;
    $table_name = $wpdb->prefix . 'hashtag_sets';
    
    // Get post data
    $post = get_post($post_id);
    $title = $post->post_title;
    $content = wp_strip_all_tags($post->post_content);
    $subtitle = get_post_meta($post_id, '_subtitle', true); // Adjust based on your subtitle meta key
    
    // Combine first 100 words of content with title and subtitle
    $words = str_word_count($content, 1);
    $first_100_words = implode(' ', array_slice($words, 0, 100));
    $search_text = $title . ' ' . $subtitle . ' ' . $first_100_words;
    
    // Get all hashtag sets
    $hashtag_sets = $wpdb->get_results("SELECT * FROM $table_name");
    $tags_to_add = array();
    
    // Check each keyword and collect tags
    foreach ($hashtag_sets as $set) {
        if (stripos($search_text, $set->keyword) !== false) {
            for ($i = 1; $i <= 4; $i++) {
                $tag_field = 'tag' . $i;
                if (!empty($set->$tag_field)) {
                    $tags_to_add[] = $set->$tag_field;
                }
            }
        }
    }
    
    // Add unique tags to the post
    if (!empty($tags_to_add)) {
        $tags_to_add = array_unique($tags_to_add);
        wp_set_post_tags($post_id, $tags_to_add, true);
    }
}

// Hook into post save/update to process hashtags
function handle_post_save($post_id) {
    // Skip autosaves
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    
    // Skip revisions
    if (wp_is_post_revision($post_id)) {
        return;
    }
    
    // Only process posts
    if (get_post_type($post_id) !== 'post') {
        return;
    }
    
    process_content_for_hashtags($post_id);
}
add_action('save_post', 'handle_post_save', 20); // Priority 20 to ensure it runs after the meta box is saved

function hashtag_manager_page() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'hashtag_sets';
    
    // Handle form submissions
    if (isset($_POST['add_hashtag_set'])) {
        $wpdb->insert($table_name, array(
            'keyword' => sanitize_text_field($_POST['keyword']),
            'tag1' => sanitize_text_field($_POST['tag1']),
            'tag2' => sanitize_text_field($_POST['tag2']),
            'tag3' => sanitize_text_field($_POST['tag3']),
            'tag4' => sanitize_text_field($_POST['tag4'])
        ));
    }
    
    // Get all hashtag sets
    $hashtag_sets = $wpdb->get_results("SELECT * FROM $table_name");
    
    ?>
    <div class="wrap">
        <h1>Hashtag Manager</h1>
        
        <!-- Add New Hashtag Set Form -->
        <div class="hashtag-form">
            <h2>Add New Hashtag Set</h2>
            <form method="post">
                <input type="text" name="keyword" placeholder="Keyword" required>
                <input type="text" name="tag1" placeholder="Tag 1">
                <input type="text" name="tag2" placeholder="Tag 2">
                <input type="text" name="tag3" placeholder="Tag 3">
                <input type="text" name="tag4" placeholder="Tag 4">
                <input type="submit" name="add_hashtag_set" class="button button-primary" value="Add Hashtag Set">
            </form>
        </div>
        
        <!-- Hashtag Sets Table -->
        <table class="wp-list-table widefat fixed striped">
            <thead>
                <tr>
                    <th>Keyword</th>
                    <th>Tag 1</th>
                    <th>Tag 2</th>
                    <th>Tag 3</th>
                    <th>Tag 4</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($hashtag_sets as $set): ?>
                <tr>
                    <td><?php echo esc_html($set->keyword); ?></td>
                    <td><?php echo esc_html($set->tag1); ?></td>
                    <td><?php echo esc_html($set->tag2); ?></td>
                    <td><?php echo esc_html($set->tag3); ?></td>
                    <td><?php echo esc_html($set->tag4); ?></td>
                    <td>
                        <a href="?page=hashtag-manager&action=edit&id=<?php echo $set->id; ?>" class="button">Edit</a>
                        <a href="?page=hashtag-manager&action=delete&id=<?php echo $set->id; ?>" class="button" onclick="return confirm('Are you sure?')">Delete</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php
}
