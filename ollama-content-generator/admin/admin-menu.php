<?php
// Funcționalitatea meniului de administrare pentru Ollama Content Generator
function ollama_content_generator_admin_menu() {
    add_menu_page(
        'Ollama Content Generator',
        'Ollama Content',
        'manage_options',
        'ollama-content-generator',
        'ollama_content_generator_admin_page',
        'dashicons-edit'
    );
    add_submenu_page(
        'ollama-content-generator',
        'Settings',
        'Settings',
        'manage_options',
        'ollama-content-generator-settings',
        'ollama_content_generator_settings_page'
    );
}

function ollama_content_generator_admin_page() {
    include plugin_dir_path(__FILE__) . 'admin-page.php';
}

function ollama_content_generator_settings_page() {
    include plugin_dir_path(__FILE__) . 'settings-page.php';
}

add_action('admin_menu', 'ollama_content_generator_admin_menu');