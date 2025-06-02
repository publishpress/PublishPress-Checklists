<?php
// List of Pro checklist rules.
// Add a new rule here with its id, support group, label, and parameters.
return [
    //AIO SEO
    [
        'id'       => 'all_in_one_seo_headline_score',
        'type'     => 'counter',
        'support'  => 'editor',
        'group'    => 'all_in_one_seo',
        'label'    => 'All in One SEO Headline Score',
        'min'      => '',
        'max'      => '',
        'post_types' => ['product'],
    ],
    [
        'id'       => 'image_count',
        'type'     => 'counter',
        'support'  => 'editor',
        'label'    => 'At least 3 images',
        'min'      => '',
        'max'      => '',
    ],
    [
        'id'       => 'single_h1',
        'type'     => 'simple',
        'support'  => 'editor',
        'label'    => 'Exactly one H1 tag',
        'max'      => 1,
    ],
    [
        'id'        => 'publish_time_exact',
        'type'      => 'time',
        'support'   => 'editor',
        'group'     => 'publish_date_time',
        'label'     => 'Published at exact time',
        'field_key' => '_publish_time_exact',
    ],
    
];
