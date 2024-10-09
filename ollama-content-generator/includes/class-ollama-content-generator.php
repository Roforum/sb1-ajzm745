<?php
class Ollama_Content_Generator {
    private $ollama_api_url;

    public function __construct() {
        $this->ollama_api_url = get_option('ollama_api_url', 'http://localhost:11434/api/generate');
    }

    public function init() {
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('wp_ajax_generate_content', array($this, 'ajax_generate_content'));
        add_action('admin_init', array($this, 'register_settings'));
    }

    public function register_settings() {
        register_setting('ollama_content_generator_settings', 'ollama_api_url');
        register_setting('ollama_content_generator_settings', 'default_model');
        register_setting('ollama_content_generator_settings', 'default_word_count');
        register_setting('ollama_content_generator_settings', 'default_language');
        register_setting('ollama_content_generator_settings', 'max_articles');
        register_setting('ollama_content_generator_settings', 'default_writing_style');
        register_setting('ollama_content_generator_settings', 'include_media_by_default');
    }

    public function add_admin_menu() {
        add_menu_page(
            'Ollama Content Generator',
            'Ollama Content',
            'manage_options',
            'ollama-content-generator',
            array($this, 'display_admin_page'),
            'dashicons-edit'
        );
        add_submenu_page(
            'ollama-content-generator',
            'Settings',
            'Settings',
            'manage_options',
            'ollama-content-generator-settings',
            array($this, 'display_settings_page')
        );
    }

    public function display_admin_page() {
        include plugin_dir_path(__FILE__) . '../admin/admin-page.php';
    }

    public function display_settings_page() {
        include plugin_dir_path(__FILE__) . '../admin/settings-page.php';
    }

    public function ajax_generate_content() {
        check_ajax_referer('ollama_content_generator_nonce', 'nonce');

        $prompt = sanitize_text_field($_POST['prompt']);
        $model = sanitize_text_field($_POST['model']);
        $word_count = intval($_POST['word_count']);
        $language = sanitize_text_field($_POST['language']);
        $keywords = sanitize_text_field($_POST['keywords']);
        $num_articles = intval($_POST['num_articles']);
        $style = sanitize_text_field($_POST['style']);
        $include_media = isset($_POST['include_media']) ? true : false;
        $category = intval($_POST['category']);
        $publishing_schedule = sanitize_text_field($_POST['publishing_schedule']);
        $articles_per_day = intval($_POST['articles_per_day']);

        $generated_articles = array();

        for ($i = 0; $i < $num_articles; $i++) {
            $full_prompt = $this->create_full_prompt($prompt, $word_count, $language, $keywords, $style);
            $content = $this->call_ollama_api($full_prompt, $model);
            if ($content === false) {
                wp_send_json_error(array('message' => 'Failed to generate content from Ollama API.'));
                return;
            }
            $processed_content = $this->process_content($content, $include_media);
            
            $post_data = $this->create_post_data($processed_content, $prompt, $keywords, $category);
            $scheduled_time = $this->get_scheduled_time($publishing_schedule, $articles_per_day, $i, $num_articles);
            
            if ($scheduled_time) {
                $post_data['post_date'] = $scheduled_time;
                $post_data['post_status'] = 'future';
                $status = 'Scheduled';
            } else {
                $status = 'Published';
            }
            
            $post_id = wp_insert_post($post_data);
            
            if ($post_id) {
                wp_set_post_tags($post_id, explode(',', $keywords));
                $generated_articles[] = array(
                    'content' => $processed_content,
                    'post_id' => $post_id,
                    'status' => $status,
                    'scheduled_time' => $scheduled_time
                );
            } else {
                wp_send_json_error(array('message' => 'Failed to create post.'));
                return;
            }
        }

        wp_send_json_success($generated_articles);
    }

    private function create_full_prompt($prompt, $word_count, $language, $keywords, $style) {
        $full_prompt = "Write a {$style} article in {$language} about '{$prompt}' using the following keywords: {$keywords}. ";
        $full_prompt .= "The article should be approximately {$word_count} words long. ";
        $full_prompt .= "Optimize the content for high SEO performance, following the latest SEO trends and best practices. ";
        $full_prompt .= "Structure the article with appropriate headings (H2, H3), subheadings, and paragraphs. ";
        $full_prompt .= "Include a compelling introduction and a strong conclusion. ";
        $full_prompt .= "Use transition words to improve readability and flow. ";
        $full_prompt .= "Ensure proper keyword density and placement. ";
        $full_prompt .= "Include relevant internal and external linking opportunities. ";
        return $full_prompt;
    }

    private function call_ollama_api($prompt, $model) {
        $response = wp_remote_post($this->ollama_api_url, array(
            'body' => json_encode(array(
                'model' => $model,
                'prompt' => $prompt,
            )),
            'headers' => array('Content-Type' => 'application/json'),
            'timeout' => 60, // Increase timeout for longer generations
        ));

        if (is_wp_error($response)) {
            error_log('Ollama API Error: ' . $response->get_error_message());
            return false;
        }

        $body = wp_remote_retrieve_body($response);
        $data = json_decode($body, true);

        return isset($data['response']) ? $data['response'] : false;
    }

    private function process_content($content, $include_media) {
        // Basic Markdown to HTML conversion
        $content = $this->markdown_to_html($content);

        // Add schema markup
        $content = $this->add_schema_markup($content);

        // Optimize content structure for SEO
        $content = $this->optimize_content_structure($content);

        if ($include_media) {
            $content = $this->add_media_to_content($content);
        }

        return $content;
    }

    private function markdown_to_html($content) {
        // Basic Markdown to HTML conversion
        $content = preg_replace('/^# (.*?)$/m', '<h1>$1</h1>', $content);
        $content = preg_replace('/^## (.*?)$/m', '<h2>$1</h2>', $content);
        $content = preg_replace('/^### (.*?)$/m', '<h3>$1</h3>', $content);
        $content = preg_replace('/\*\*(.*?)\*\*/', '<strong>$1</strong>', $content);
        $content = preg_replace('/\*(.*?)\*/', '<em>$1</em>', $content);
        $content = preg_replace('/\[(.*?)\]\((.*?)\)/', '<a href="$2">$1</a>', $content);

        // Convert newlines to paragraphs
        $content = '<p>' . str_replace("\n\n", '</p><p>', $content) . '</p>';
        
        return $content;
    }

    private function add_schema_markup($content) {
        // Add basic Article schema
        $schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'Article',
            'headline' => wp_strip_all_tags(get_the_title()),
            'datePublished' => get_the_date('c'),
            'dateModified' => get_the_modified_date('c'),
            'author' => array(
                '@type' => 'Person',
                'name' => get_the_author()
            )
        );

        $schema_script = '<script type="application/ld+json">' . json_encode($schema) . '</script>';
        return $content . $schema_script;
    }

    private function optimize_content_structure($content) {
        // Add table of contents
        $toc = $this->generate_table_of_contents($content);
        $content = $toc . $content;

        // Add FAQ schema if applicable
        $content = $this->add_faq_schema($content);

        // Improve internal linking
        $content = $this->add_internal_links($content);

        return $content;
    }

    private function generate_table_of_contents($content) {
        // Extract headings and create a table of contents
        preg_match_all('/<h([2-3])>(.*?)<\/h[2-3]>/i', $content, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            return '';
        }

        $toc = '<h2>Table of Contents</h2><ul>';
        foreach ($matches as $match) {
            $level = $match[1];
            $title = $match[2];
            $anchor = sanitize_title($title);
            $toc .= "<li class='toc-level-$level'><a href='#$anchor'>$title</a></li>";
            $content = str_replace($match[0], "<h$level id='$anchor'>$title</h$level>", $content);
        }
        $toc .= '</ul>';

        return $toc;
    }

    private function add_faq_schema($content) {
        // Extract potential FAQs from the content
        preg_match_all('/<h3>(.*?)<\/h3>\s*<p>(.*?)<\/p>/is', $content, $matches, PREG_SET_ORDER);
        
        if (empty($matches)) {
            return $content;
        }

        $faq_schema = array(
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array()
        );

        foreach ($matches as $match) {
            $question = $match[1];
            $answer = $match[2];
            $faq_schema['mainEntity'][] = array(
                '@type' => 'Question',
                'name' => $question,
                'acceptedAnswer' => array(
                    '@type' => 'Answer',
                    'text' => $answer
                )
            );
        }

        $faq_script = '<script type="application/ld+json">' . json_encode($faq_schema) . '</script>';
        return $content . $faq_script;
    }

    private function add_internal_links($content) {
        // Get a list of popular posts
        $popular_posts = $this->get_popular_posts();

        foreach ($popular_posts as $post) {
            $post_title = $post->post_title;
            $post_url = get_permalink($post->ID);
            $content = preg_replace('/\b' . preg_quote($post_title, '/') . '\b/i', "<a href='$post_url'>$0</a>", $content, 1);
        }

        return $content;
    }

    private function get_popular_posts($limit = 5) {
        return get_posts(array(
            'numberposts' => $limit,
            'meta_key' => 'post_views_count',
            'orderby' => 'meta_value_num',
            'order' => 'DESC'
        ));
    }

    private function add_media_to_content($content) {
        // Extract keywords from content
        $keywords = $this->extract_keywords($content);

        // Search for relevant images
        $images = $this->search_images($keywords);

        // Insert images into content
        if (!empty($images)) {
            $image_html = '<img src="' . esc_url($images[0]) . '" alt="' . esc_attr($keywords[0]) . '">';
            $content = $image_html . $content;
        }

        return $content;
    }

    private function extract_keywords($content) {
        // Simple keyword extraction (can be improved)
        $words = str_word_count(strip_tags($content), 1);
        $word_count = array_count_values($words);
        arsort($word_count);
        return array_slice(array_keys($word_count), 0, 5);
    }

    private function search_images($keywords) {
        // Implement image search using a free API or scraping (respecting copyright)
        // This is a placeholder and should be implemented properly
        return array('https://example.com/placeholder-image.jpg');
    }

    private function create_post_data($content, $title, $keywords, $category) {
        return array(
            'post_title'    => $title,
            'post_content'  => $content,
            'post_status'   => 'publish',
            'post_author'   => get_current_user_id(),
            'post_type'     => 'post',
            'post_category' => array($category)
        );
    }

    private function get_scheduled_time($publishing_schedule, $articles_per_day, $current_index, $total_articles) {
        switch ($publishing_schedule) {
            case 'immediate':
                return null;
            case 'hourly':
                return date('Y-m-d H:i:s', strtotime("+{$current_index} hours"));
            case 'custom':
                $interval = 24 / $articles_per_day;
                $hours = floor($current_index * $interval);
                $minutes = floor(($current_index * $interval - $hours) * 60);
                return date('Y-m-d H:i:s', strtotime("+{$hours} hours +{$minutes} minutes"));
            default:
                return null;
        }
    }
}