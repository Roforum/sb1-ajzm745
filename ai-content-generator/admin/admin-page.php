<div class="wrap">
    <h1>AI Content Generator</h1>
    <form id="ai-content-form">
        <?php wp_nonce_field('ai_content_generator_nonce', 'ai_content_generator_nonce'); ?>
        <table class="form-table">
            <tr>
                <th><label for="prompt">Idea or Prompt</label></th>
                <td><input type="text" id="prompt" name="prompt" class="regular-text" required></td>
            </tr>
            <tr>
                <th><label for="model">AI Model</label></th>
                <td>
                    <select id="model" name="model">
                        <option value="gpt-3.5-turbo">GPT-3.5 Turbo</option>
                        <option value="llama2">Llama 2</option>
                        <!-- Add more model options -->
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="word_count">Word Count</label></th>
                <td><input type="number" id="word_count" name="word_count" min="100" max="2000" value="500"></td>
            </tr>
            <tr>
                <th><label for="language">Language</label></th>
                <td>
                    <select id="language" name="language">
                        <option value="en">English</option>
                        <option value="es">Spanish</option>
                        <!-- Add more language options -->
                    </select>
                </td>
            </tr>
            <tr>
                <th><label for="keywords">Keywords (comma-separated)</label></th>
                <td><input type="text" id="keywords" name="keywords" class="regular-text"></td>
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
    $('#ai-content-form').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        
        $.ajax({
            url: ajaxurl,
            type: 'POST',
            data: formData + '&action=generate_content',
            beforeSend: function() {
                $('#generate').prop('disabled', true).val('Generating...');
            },
            success: function(response) {
                if (response.success) {
                    $('#generated-content').html(response.data);
                } else {
                    alert('Error generating content');
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