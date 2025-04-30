<?php

if (!defined('ABSPATH')) {
    exit;
}

class AT_Default_Featured_Image_Admin
{
    public function __construct()
    {
        add_action('admin_menu', array($this, 'add_settings_page'));
        add_action('admin_init', array($this, 'register_settings'));
        add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_scripts'));
    }

    public function enqueue_admin_scripts($hook)
    {
        if ('settings_page_at-default-featured-image' !== $hook) {
            return;
        }
        wp_enqueue_media();
    }

    public function add_settings_page()
    {
        add_options_page(
            'Default Featured Image Settings',
            'Default Featured Image',
            'manage_options',
            'at-default-featured-image',
            array($this, 'render_settings_page')
        );
    }

    public function register_settings()
    {
        register_setting(
            'at_default_featured_image_settings',
            'at_default_featured_image',
            array(
                'type' => 'string',
                'sanitize_callback' => 'esc_url_raw',
                'default' => AT_DEFAULT_IMAGE_PLACEHOLDER
            )
        );

        register_setting(
            'at_default_featured_image_settings',
            'at_default_featured_image_enabled',
            array(
                'type' => 'string',
                'sanitize_callback' => 'sanitize_text_field',
                'default' => 'yes'
            )
        );
    }

    public function render_settings_page()
    {
        if (!current_user_can('manage_options')) {
            return;
        }

        $image_url = get_option('at_default_featured_image', AT_DEFAULT_IMAGE_PLACEHOLDER);
        $is_enabled = get_option('at_default_featured_image_enabled', 'yes');
?>
        <div class="wrap">
            <h1><?php echo esc_html(get_admin_page_title()); ?></h1>
            <form method="post" action="options.php">
                <?php settings_fields('at_default_featured_image_settings'); ?>
                <table class="form-table" role="presentation">
                    <tr>
                        <th scope="row">Enable Default Featured Image</th>
                        <td>
                            <label>
                                <input type="checkbox"
                                    name="at_default_featured_image_enabled"
                                    value="yes"
                                    <?php checked($is_enabled, 'yes'); ?>>
                                Enable default featured image for posts without one
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row">Default Featured Image</th>
                        <td>
                            <div class="image-preview-wrapper">
                                <img id="image-preview"
                                    src="<?php echo esc_url($image_url); ?>"
                                    style="max-width: 300px; height: auto;">
                            </div>
                            <input type="hidden"
                                name="at_default_featured_image"
                                id="at_default_featured_image"
                                value="<?php echo esc_attr($image_url); ?>">
                            <input type="button"
                                class="button button-primary"
                                id="upload-image-button"
                                value="Choose Image">
                            <input type="button"
                                class="button"
                                id="reset-image-button"
                                value="Reset to Default">
                            <p class="description">Select an image or reset to the default placeholder.</p>
                        </td>
                    </tr>
                </table>
                <?php submit_button(); ?>
            </form>
        </div>
        <script type="text/javascript">
            jQuery(document).ready(function($) {
                $('#upload-image-button').click(function(e) {
                    e.preventDefault();
                    var frame = wp.media({
                        title: 'Select or Upload Default Featured Image',
                        library: {
                            type: 'image'
                        },
                        button: {
                            text: 'Use this image'
                        },
                        multiple: false
                    });

                    frame.on('select', function() {
                        var attachment = frame.state().get('selection').first().toJSON();
                        $('#at_default_featured_image').val(attachment.url);
                        $('#image-preview').attr('src', attachment.url).show();
                        $('#reset-image-button').show();
                    });

                    frame.open();
                });

                $('#reset-image-button').click(function(e) {
                    e.preventDefault();
                    $('#at_default_featured_image').val('<?php echo esc_js(AT_DEFAULT_IMAGE_PLACEHOLDER); ?>');
                    $('#image-preview').attr('src', '<?php echo esc_js(AT_DEFAULT_IMAGE_PLACEHOLDER); ?>');
                });
            });
        </script>
<?php
    }
}
