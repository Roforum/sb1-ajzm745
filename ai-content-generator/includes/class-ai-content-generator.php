<?php
class AI_Content_Generator {
    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_generate_content', array($this, 'ajax_generate_content'));
    }

    public function add_admin_menu() {
        add_menu_page(
            'AI Content Generator',
            'AI Content',
            'manage_options',
            'ai-content-generator',
            array($this, 'display_admin_page'),
            'dashicons-edit'
        );
    }

    public function display_admin_page() {
        include plugin_dir_path(__FILE__) . '../admin/admin-page.php';
    }

    public function ajax_generate_content() {
        // Check nonce for security
        check_ajax_referer('ai_content_generator_nonce', 'nonce');

        $prompt = sanitize_text_field($_POST['prompt']);
        $model = sanitize_text_field($_POST['model']);
        $word_count = intval($_POST['word_count']);
        $language = sanitize_text_field($_POST['language']);
        $keywords = sanitize_text_field($_POST['keywords']);

        // Call the external API service (you need to implement this)
        $generated_content = $this->call_external_api($prompt, $model, $word_count, $language, $keywords);

        // Process the generated content (implement SEO optimization, formatting, etc.)
        $processed_content = $this->process_content($generated_content);

        wp_send_json_success($processed_content);
    }

    private function call_external_api($prompt, $model, $word_count, $language, $keywords) {
        // Implement the API call to your external service that interfaces with Ollama
        // Return the generated content
    }

    private function process_content($content) {
        // Implement content processing, SEO optimization, etc.
        return $content;
    }
}