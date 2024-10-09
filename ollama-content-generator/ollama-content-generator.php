<?php
/**
 * Plugin Name: Ollama Content Generator
 * Description: Generate AI-powered content using Ollama models and optimize for SEO.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-ollama-content-generator.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';

// Initialize the plugin
function ollama_content_generator_init() {
    $plugin = new Ollama_Content_Generator();
    $plugin->init();
}
add_action('plugins_loaded', 'ollama_content_generator_init');

// Activation hook
register_activation_hook(__FILE__, 'ollama_content_generator_activate');
function ollama_content_generator_activate() {
    // Activation tasks (if any)
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'ollama_content_generator_deactivate');
function ollama_content_generator_deactivate() {
    // Deactivation tasks (if any)
}