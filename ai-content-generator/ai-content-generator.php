<?php
/**
 * Plugin Name: AI Content Generator
 * Description: Generate AI-powered content using various models and optimize for SEO.
 * Version: 1.0
 * Author: Your Name
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Include necessary files
require_once plugin_dir_path(__FILE__) . 'includes/class-ai-content-generator.php';
require_once plugin_dir_path(__FILE__) . 'admin/admin-menu.php';

// Initialize the plugin
function ai_content_generator_init() {
    $plugin = new AI_Content_Generator();
    $plugin->init();
}
add_action('plugins_loaded', 'ai_content_generator_init');

// Activation hook
register_activation_hook(__FILE__, 'ai_content_generator_activate');
function ai_content_generator_activate() {
    // Activation tasks (if any)
}

// Deactivation hook
register_deactivation_hook(__FILE__, 'ai_content_generator_deactivate');
function ai_content_generator_deactivate() {
    // Deactivation tasks (if any)
}