<?php
/**
 * @package     PublishPress\Content_checklist
 * @author      PublishPress <help@publishpress.com>
 * @copyright   Copyright (C) 2018 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Addon\Content_checklist;

use PP_Checklist;
use Pimple\ServiceProviderInterface;
use Pimple\Container as Pimple;
use PublishPress\EDD_License\Core\Container as EDDContainer;
use PublishPress\EDD_License\Core\ServicesConfig as EDDServicesConfig;
use PublishPress\EDD_License\Core\Services as EDDServices;

defined( 'ABSPATH' ) or die( 'No direct script access allowed.' );

/**
 * Class Services
 */
class Services implements ServiceProviderInterface {
    /**
     * @since 1.3.5
     * @var PP_Checklist
     */
    protected $module;

    /**
     * Services constructor.
     *
     * @since 1.3.5
     * @param PP_Checklist $module
     */
    public function __construct(PP_Checklist $module)
    {
        $this->module = $module;

    }

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @since 1.3.5
     * @param Pimple $container A container instance
     */
    public function register(Pimple $container) {
        $container['module'] = function ($c) {
            return $this->module;
        };

        $container['LICENSE_KEY'] = function ($c) {
            return $c['module']->module->options->license_key;
        };

        $container['LICENSE_STATUS'] = function ($c) {
            return $c['module']->module->options->license_status;
        };

        $container['edd_container'] = function ($c) {
            $config = new EDDServicesConfig();
            $config->setApiUrl('https://publishpress.com');
            $config->setLicenseKey($c['LICENSE_KEY']);
            $config->setLicenseStatus($c['LICENSE_STATUS']);
            $config->setPluginVersion(PUBLISHPRESS_CONTENT_CHECKLIST_VERSION);
            $config->setEddItemId(PP_CONTENT_CHECKLIST_ITEM_ID);
            $config->setPluginAuthor('PublishPress');
            $config->setPluginFile(PP_CONTENT_CHECKLIST_FILE);

            $services = new EDDServices($config);

            $eddContainer = new EDDContainer();
            $eddContainer->register($services);

            return $eddContainer;
        };
    }
}
