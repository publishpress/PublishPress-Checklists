<?php

require __DIR__ . '/src/vendor/autoload.php';

/**
 * This is project's console commands configuration for Robo task runner.
 *
 * @see http://robo.li/
 */
class RoboFile extends PublishPress\Builder\AbstractTask
{

    public function __construct()
    {
        $this->plugin_name      = 'publishpress-content-checklist';
        $this->version_constant = 'PP_CONTENT_CHECKLIST_VERSION';

        parent::__construct();
    }
}
