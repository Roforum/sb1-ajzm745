jQuery(document).ready(function($) {
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
                        contentHtml += '<p>Saved as draft with ID: ' + article.post_id + '</p>';
                        contentHtml += '<hr>';
                    });
                    $('#generated-content').html(contentHtml);
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