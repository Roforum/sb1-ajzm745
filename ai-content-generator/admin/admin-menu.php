<?php
// Funcționalitatea meniului de administrare pentru AI Content Generator
function ai_content_generator_admin_menu() {
    add_menu_page(
        'AI Content Generator',
        'AI Content',
        'manage_options',
        'ai-content-generator',
        'ai_content_generator_admin_page',
        'dashicons-edit'
    );
}

function ai_content_generator_admin_page() {
    include plugin_dir_path(__FILE__) . 'admin-page.php';
}

add_action('admin_menu', 'ai_content_generator_admin_menu');