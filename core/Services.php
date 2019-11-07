<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core;

use Pimple\Container as Pimple;
use Pimple\ServiceProviderInterface;
use PublishPress\Checklists\Core\Legacy\LegacyPlugin;
use PublishPress\EDD_License\Core\Container as EDDContainer;
use PublishPress\EDD_License\Core\Services as EDDServices;
use PublishPress\EDD_License\Core\ServicesConfig as EDDServicesConfig;

defined('ABSPATH') or die('No direct script access allowed.');

/**
 * Class Services
 */
class Services implements ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Pimple $container A container instance
     *
     * @since 1.3.5
     *
     */
    public function register(Pimple $container)
    {
        $container['legacy_plugin'] = function ($c) {
            return new LegacyPlugin();
        };

        $container['module'] = function ($c) {
            $legacyPlugin = $c['legacy_plugin'];

            return $legacyPlugin->checklists;
        };

        $container['template_loader'] = function ($c) {
            return new TemplateLoader();
        };

        $container['LICENSE_KEY'] = function ($c) {
            $key = '';
            if (isset($c['module']->module->options->license_key)) {
                $key = $c['module']->module->options->license_key;
            }

            return $key;
        };

        $container['LICENSE_STATUS'] = function ($c) {
            $status = '';

            if (isset($c['module']->module->options->license_status)) {
                $status = $c['module']->module->options->license_status;
            }

            return $status;
        };

        $container['edd_container'] = function ($c) {
            $config = new EDDServicesConfig();
            $config->setApiUrl('https://publishpress.com');
            $config->setLicenseKey($c['LICENSE_KEY']);
            $config->setLicenseStatus($c['LICENSE_STATUS']);
            $config->setPluginVersion(PUBLISHPRESS_CHECKLISTS_VERSION);
            $config->setEddItemId(PUBLISHPRESS_CHECKLISTS_ITEM_ID);
            $config->setPluginAuthor('PublishPress');
            $config->setPluginFile(PUBLISHPRESS_CHECKLISTS_FILE);

            $services = new EDDServices($config);

            $eddContainer = new EDDContainer();
            $eddContainer->register($services);

            return $eddContainer;
        };
    }
}
