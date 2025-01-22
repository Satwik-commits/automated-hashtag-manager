<?php
/**
 * Plugin Name: Hashtag Manager
 * Description: Manage automated hashtags for your content
 * Version: 1.0.0
 * Author: Your Name
 */

// Prevent direct access
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

// Main plugin page HTML
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