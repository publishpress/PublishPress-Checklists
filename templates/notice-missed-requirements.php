<?php

defined('ABSPATH') or die('No direct script access allowed.');
?>
<div class="notice notice-error is-dismissible">
    <?php echo sprintf(
        __('PublishPress Content Checklist requires %s %s or later. Please, update.',
            'publishpress-content-checklist'),
        '<a href="https://wordpress.org/plugins/publishpress" target="_blank">' . __('PublishPress',
            'publishpress-content-checklist') . '</a>',
        PP_CONTENT_CHECKLIST_MIN_PARENT_VERSION
    );
    ?>
</div>
