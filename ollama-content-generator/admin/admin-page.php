<div class="wrap">
    <h1>Ollama Content Generator</h1>
    <form id="ollama-content-form">
        <?php wp_nonce_field('ollama_content_generator_nonce', 'ollama_content_generator_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="prompt">Idea or Prompt</label></th>
                <td><input type="text" id="prompt" name="prompt" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="model">Ollama Model</label></th>
                <td>
                    <select id="model" name="model">
                        <?php
                        $default_model = get_option('default_model', 'llama2');
                        $models = array('llama2' => 'Llama 2', 'mistral' => 'Mistral', 'codellama' => 'CodeLlama');
                        foreach ($models as $value => $label) {
                            echo '<option value="' . esc_attr($value) . '" ' . selected($default_model, $value, false) . '>' . esc_html($label) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="word_count">Word Count</label></th>
                <td><input type="number" id="word_count" name="word_count" min="100" max="2000" value="<?php echo esc_attr(get_option('default_word_count', 500)); ?>"></td>
            </tr>
            <tr>
                <th><label for="language">Language</label></th>
                <td>
                    <select id="language" name="language">
                        <?php
                        $default_language = get_option('default_language', 'English');
                        $languages = array('English', 'Spanish', 'French', 'German', 'Romanian');
                        foreach ($languages as $lang) {
                            echo '<option value="' . esc_attr($lang) . '" ' . selected($default_language, $lang, false) . '>' . esc_html($lang) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="keywords">Keywords (comma-separated)</label></th>
                <td><input type="text" id="keywords" name="keywords" class="regular-text"></td>
            </tr>
            <tr>
                <th><label for="num_articles">Number of Articles</label></th>
                <td><input type="number" id="num_articles" name="num_articles" min="1" max="<?php echo esc_attr(get_option('max_articles', 5)); ?>" value="1"></td>
            </tr>
            <tr>
                <th><label for="style">Writing Style</label></th>
                <td>
                    <select id="style" name="style">
                        <?php
                        $default_style = get_option('default_writing_style', 'Informative');
                        $styles = array('Informative', 'Persuasive', 'Entertaining', 'Formal', 'Casual');
                        foreach ($styles as $style) {
                            echo '<option value="' . esc_attr($style) . '" ' . selected($default_style, $style, false) . '>' . esc_html($style) . '</option>';
                        }
                        ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="category">Category</label></th>
                <td>
                    <?php
                    wp_dropdown_categories(array(
                        'show_option_none' => 'Select Category',
                        'option_none_value' => '',
                        'name' => 'category',
                        'id' => 'category',
                        'hierarchical' => true,
                        'show_count' => true,
                        'hide_empty' => false,
                    ));
                    ?>
                </td>
            </tr>
            <tr>
                <th><label for="publishing_schedule">Publishing Schedule</label></th>
                <td>
                    <select id="publishing_schedule" name="publishing_schedule">
                        <option value="immediate">Publish Immediately</option>
                        <option value="hourly">Publish 1 Article per Hour</option>
                        <option value="custom">Custom Schedule</option>
                    </select>
                </td>
            </tr>
            <tr id="custom_schedule_row" style="display: none;">
                <th><label for="articles_per_day">Articles to Publish per Day</label></th>
                <td><input type="number" id="articles_per_day" name="articles_per_day" min="1" max="24" value="1"></td>
            </tr>
            <tr>
                <th><label for="include_media">Include Media</label></th>
                <td><input type="checkbox" id="include_media" name="include_media" value="1" <?php checked(get_option('include_media_by_default'), 1); ?>></td>
            </tr>
        </table>
        <p class="submit">
            <input type="submit" name="generate" id="generate" class="button button-primary" value="Generate Content">
        </p>
    </form>
    <div id="generated-content"></div>
</div>

<script>
jQuery(document).ready(function($) {
    $('#publishing_schedule').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#custom_schedule_row').show();
        } else {
            $('#custom_schedule_row').hide();
        }
    });

    $('#ollama-content-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: ollamaContentGenerator.ajaxurl,
            type: 'POST',
            data: formData + '&action=generate_content&nonce=' + ollamaContentGenerator.nonce,
            beforeSend: function() {
                $('#generate').prop('disabled', true).val('Generating...');
            },
            success: function(response) {
                if (response.success) {
                    var contentHtml = '';
                    response.data.forEach(function(article, index) {
                        contentHtml += '<h2>Article ' + (index + 1) + '</h2>';
                        contentHtml += '<div>' + article.content + '</div>';
                        contentHtml += '<p>Status: ' + article.status + '</p>';
                        if (article.scheduled_time) {
                            contentHtml += '<p>Scheduled for: ' + article.scheduled_time + '</p>';
                        }
                        contentHtml += '<hr>';
                    });
                    $('#generated-content').html(contentHtml);
                } else {
                    alert('Error generating content: ' + response.data.message);
                }
            },
            error: function() {
                alert('Error communicating with the server');
            },
            complete: function() {
                $('#generate').prop('disabled', false).val('Generate Content');
            }
        });
    });
});
</script>