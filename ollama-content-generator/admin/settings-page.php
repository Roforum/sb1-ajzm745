<div class="wrap">
    <h1>Ollama Content Generator Settings</h1>
    <form method="post" action="options.php">
        <?php
        settings_fields('ollama_content_generator_settings');
        do_settings_sections('ollama_content_generator_settings');
        ?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Ollama API URL</th>
                <td><input type="text" name="ollama_api_url" value="<?php echo esc_attr(get_option('ollama_api_url', 'http://localhost:11434/api/generate')); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Default Model</th>
                <td>
                    <select name="default_model">
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
            <tr valign="top">
                <th scope="row">Default Word Count</th>
                <td><input type="number" name="default_word_count" value="<?php echo esc_attr(get_option('default_word_count', 500)); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Default Language</th>
                <td>
                    <select name="default_language">
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
            <tr valign="top">
                <th scope="row">Maximum Articles per Generation</th>
                <td><input type="number" name="max_articles" value="<?php echo esc_attr(get_option('max_articles', 5)); ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Default Writing Style</th>
                <td>
                    <select name="default_writing_style">
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
            <tr valign="top">
                <th scope="row">Include Media by Default</th>
                <td><input type="checkbox" name="include_media_by_default" value="1" <?php checked(get_option('include_media_by_default'), 1); ?> /></td>
            </tr>
        </table>
        <?php submit_button(); ?>
    </form>
</div>