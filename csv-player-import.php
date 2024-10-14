<?php
/*
Plugin Name: CSV Player Import
Description: A plugin to import player points from CSV files for different games.
Version: 1.0
Author: MD ARAFAT Rahman
*/

if (!defined('ABSPATH')) exit;

// Activation Hook: Create the database table
register_activation_hook(__FILE__, 'cpi_create_table');
function cpi_create_table() {
    global $wpdb;
    $table_name = $wpdb->prefix . 'player_points';
    $charset_collate = $wpdb->get_charset_collate();

    $sql = "CREATE TABLE $table_name (
        id BIGINT(20) NOT NULL AUTO_INCREMENT,
        player_name VARCHAR(255) NOT NULL,
        points INT NOT NULL,
        game_id VARCHAR(100) NOT NULL,
        PRIMARY KEY (id),
        UNIQUE KEY player_game (player_name, game_id)
    ) $charset_collate;";

    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    dbDelta($sql);
}

// Add menu page for CSV upload
add_action('admin_menu', 'cpi_add_menu_page');
function cpi_add_menu_page() {
    add_menu_page(
        'CSV Player Import', 
        'CSV Player Import', 
        'manage_options', 
        'cpi_import', 
        'cpi_import_page'
    );
}

function cpi_import_page() {
    ?>
    <div class="wrap">
        <h1>Import Player Points</h1>
        <form method="post" enctype="multipart/form-data">
            <input type="file" name="csv_file" accept=".csv" required>
            <select name="game_id" required>
                <option value="">Select Game</option>
                <option value="game_1">Game 1</option>
                <option value="game_2">Game 2</option>
                <!-- Add more games as needed -->
            </select>
            <input type="submit" name="upload_csv" value="Upload CSV" class="button button-primary">
        </form>
    </div>
    <?php

    if (isset($_POST['upload_csv'])) {
        cpi_handle_csv_upload();
    }
}
