<?php

if (!defined('ABSPATH')) {
    exit;
}

class AT_Default_Featured_Image
{
    private $doing_default_image = false;

    public function __construct()
    {
        add_filter('post_thumbnail_html', array($this, 'maybe_add_default_thumbnail'), 10, 5);
        add_filter('get_post_metadata', array($this, 'filter_thumbnail_id'), 10, 4);
    }

    public function maybe_add_default_thumbnail($html, $post_id, $thumbnail_id, $size, $attr)
    {
        if ($this->doing_default_image || !empty($html)) {
            return $html;
        }

        // Check if feature is enabled
        if (get_option('at_default_featured_image_enabled', 'yes') !== 'yes') {
            return $html;
        }

        $this->doing_default_image = true;
        $default_image = get_option('at_default_featured_image', AT_DEFAULT_IMAGE_PLACEHOLDER);

        if (!empty($default_image)) {
            $html = sprintf(
                '<img src="%s" alt="%s" class="default-featured-image wp-post-image" />',
                esc_url($default_image),
                esc_attr(get_the_title($post_id))
            );
        }

        $this->doing_default_image = false;
        return $html;
    }

    public function filter_thumbnail_id($value, $object_id, $meta_key, $single)
    {
        if ($this->doing_default_image || '_thumbnail_id' !== $meta_key) {
            return $value;
        }

        $this->doing_default_image = true;
        $has_thumbnail = get_post_meta($object_id, '_thumbnail_id', true);
        $this->doing_default_image = false;

        if (empty($has_thumbnail) && get_option('at_default_featured_image')) {
            return 'default';
        }

        return $value;
    }
}
