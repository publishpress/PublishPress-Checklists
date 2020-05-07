<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.4.8
 */

namespace PublishPress\Checklists\Core;


class ErrorHandler
{
    public function add($errorMessage, $errorData = null)
    {
        if (!empty($errorData)) {
            if (!is_string($errorData)) {
                $errorData = print_r($errorData, true);
            }

            $errorMessage .= ': ' . $errorData;
        }

        error_log($errorMessage);
    }
}
