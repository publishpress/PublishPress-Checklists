<?php
/**
 * Static Pro checklist rules.
 *
 * This array defines the built-in Pro requirements, each entry containing:
 *   - id: unique identifier
 *   - type: requirement type (simple, counter, multiple, time)
 *   - support: WP post_type_supports key
 *   - group: requirement group/tab
 *   - label: displayed text in settings
 *   - post_type: post type need to be supported
 *   - optional params: min, max, post_type, field_key
 *
 * @since 1.0.0
 */
$static = [
    [
        'id'       => 'all_in_one_seo_headline_score',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'all_in_one_seo',
        'label'    => 'All in One SEO Headline Score',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'all_in_one_seo_score',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'all_in_one_seo',
        'label'    => 'All in One SEO Score',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'featured_image_height',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'featured_image',
        'label'    => 'Featured image height',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'featured_image_width',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'featured_image',
        'label'    => 'Featured image width',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'heading_in_hierarchy',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'accessibility',
        'label'    => 'Heading in hierarchy',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'image_count',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'images',
        'label'    => 'Number of images in content',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'approved_by_user',
        'type'     => 'multiple',
        'support'  => 'editor',
        'group'    => 'approval',
        'label'    => 'Approved by user',
    ],
    [
        'id'       => 'no_heading_tags',
        'type'     => 'multiple',
        'support'  => 'editor',
        'group'    => 'content',
        'label'    => 'Avoid heading tags in content',
    ],
    [
        'id'        => 'publish_time_exact',
        'type'      => 'time',
        'support'   => 'editor',
        'group'     => 'publish_date_time',
        'label'     => 'Published at exact time',
        'field_key' => '_publish_time_exact',
    ],
    [
        'id'        => 'publish_time_future',
        'type'      => 'time',
        'support'   => 'editor',
        'group'     => 'publish_date_time',
        'label'    => 'Publish time should be in the future',
        'field_key' => '_publish_time_future'
    ],
    [
        'id'       => 'rank_math_score',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'rank_math',
        'label'    => 'Rank Math SEO Score',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'single_h1_per_page',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'accessibility',
        'label'    => 'Only one H1 tag in content',
    ],
    [
        'id'       => 'table_header',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'accessibility',
        'label'    => 'Tables have a header row',
    ],
    [
        'id'       => 'backorder',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Check the "Allow backorders?" box',
        'post_type' => 'product',
    ],
    [
        'id'       => 'crosssell',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Select some products for "Cross-sells"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'discount',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Discount for the "Sale price"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'downloadable',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Check the "Downloadable" box',
        'post_type' => 'product',
    ],
    [
        'id'       => 'image',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'images',
        'label'    => 'Product image',
        'post_type' => 'product',
    ],
    [
        'id'       => 'manage_stock',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Check the "Manage stock?" box',
        'post_type' => 'product',
    ],
    [
        'id'       => 'regular_price',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Enter a "Regular price"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'sale_price',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Enter a "Sale price"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'sale_price_scheduled',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Schedule the "Sale price"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'sku',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Enter a "SKU"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'sold_individually',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Check the "Sold individually" box',
        'post_type' => 'product',
    ],
    [
        'id'       => 'upsell',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Select some products for "Upsells"',
        'post_type' => 'product',
    ],
    [
        'id'       => 'virtual',
        'type'     => 'simple',
        'support'  => 'editor',
        'group'    => 'woocommerce',
        'label'    => 'Check the "Virtual" box',
        'post_type' => 'product',
    ]
    
];

/**
 * Dynamically generate Pro requirement entries for each ACF field.
 *
 * - For 'text' and 'textarea' fields: uses 'counter' type to count characters.
 * - For other field types: uses 'simple' type to check if the field is filled.
 *
 * @since 1.0.0
 * @link https://www.advancedcustomfields.com/resources/acf_get_field_groups/ ACF Field Groups API
 */
$acf = [];
if ( function_exists('acf_get_field_groups') ) {
    $groups = acf_get_field_groups();
    foreach ( $groups as $group ) {
        $fields = acf_get_fields( $group );
        if ( empty( $fields ) ) {
            continue;
        }
        foreach ( $fields as $f ) {
            if ( in_array( $f['type'], ['text', 'textarea'] ) ) {
                $acf[] = [
                    'id'        => 'acf_' . $f['key'],
                    'type'      => 'counter',
                    'support'   => 'editor',
                    'group'     => 'advanced-custom-fields',
                    'label'     => sprintf(
                        __('Number of Characters in %s field', 'publishpress-checklists'),
                        $f['label']
                    ),
                    'name'      => $f['name'],
                    'field_key' => $f['key'],
                ];
            } else {
                $acf[] = [
                    'id'        => 'acf_' . $f['key'],
                    'type'      => 'simple',
                    'support'   => 'editor',
                    'group'     => 'advanced-custom-fields',
                    'label'     => sprintf(
                        __('%s is filled', 'publishpress-checklists'),
                        $f['label']
                    ),
                    'name'      => $f['name'],
                    'field_key' => $f['key'],
                ];
            }
        }
    }
}
return array_merge( $static, $acf );
