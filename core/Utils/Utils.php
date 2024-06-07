<?php
/**
 * @package     PublishPress\Checklists
 * @author      PublishPress <help@publishpress.com>
 * @copyright   copyright (C) 2019 PublishPress. All rights reserved.
 * @license     GPLv2 or later
 * @since       1.0.0
 */

namespace PublishPress\Checklists\Core\Utils;


class Utils
{
    /**
     * Load Pro Banner Right Sidebar
     */

    public static function ppch_pro_sidebar() {
        ?>
            <div class="pp-column-right">
                        <div class="ppch-advertisement-right-sidebar">
                            <div class="advertisement-box-content postbox ppch-advert">
                                <div class="postbox-header ppch-advert">
                                    <h3 class="advertisement-box-header hndle is-non-sortable">
                                        <span><?php echo esc_html__('Upgrade to PublishPress Checklists Pro', 'publishpress-checklists'); ?></span>
                                    </h3>
                                </div>

                                <div class="inside ppch-advert">
                                    <p><?php echo esc_html__('Enhance the power of PublishPress Checklists with the Pro version:', 'publishpress-checklists'); ?>
                                    </p>
                                    <ul>
                                        <li><?php echo esc_html__('Control height and width for featured images', 'publishpress-checklists'); ?></li>
                                        <li><?php echo esc_html__('Checklists for WooCommerce products', 'publishpress-checklists'); ?></li>
                                        <li><?php echo esc_html__('Remove PublishPress ads and branding', 'publishpress-checklists'); ?></li>
                                        <li><?php echo esc_html__('Fast, professional support', 'publishpress-checklists'); ?></li>
                                    </ul>
                                    <div class="upgrade-btn">
                                        <a href="https://publishpress.com/links/checklists-menu" target="__blank"><?php echo esc_html__('Upgrade to Pro', 'publishpress-checklists'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="advertisement-box-content postbox ppch-advert">
                                <div class="postbox-header ppch-advert">
                                    <h3 class="advertisement-box-header hndle is-non-sortable">
                                        <span><?php echo esc_html__('Need PublishPress Checklists Support?', 'publishpress-checklists'); ?></span>
                                    </h3>
                                </div>

                                <div class="inside ppch-advert">
                                    <p><?php echo esc_html__('If you need help or have a new feature request, let us know.', 'publishpress-checklists'); ?>
                                        <a class="advert-link" href="https://wordpress.org/plugins/publishpress-checklists/" target="_blank">
                                        <?php echo esc_html__('Request Support', 'publishpress-checklists'); ?> 
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="linkIcon">
                                                <path
                                                    d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
                                                ></path>
                                            </svg>
                                        </a>
                                    </p>
                                    <p>
                                    <?php echo esc_html__('Detailed documentation is also available on the plugin website.', 'publishpress-checklists'); ?> 
                                        <a class="advert-link" href="https://publishpress.com/docs-category/checklists/" target="_blank">
                                        <?php echo esc_html__('View Knowledge Base', 'publishpress-checklists'); ?> 
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" class="linkIcon">
                                                <path
                                                    d="M18.2 17c0 .7-.6 1.2-1.2 1.2H7c-.7 0-1.2-.6-1.2-1.2V7c0-.7.6-1.2 1.2-1.2h3.2V4.2H7C5.5 4.2 4.2 5.5 4.2 7v10c0 1.5 1.2 2.8 2.8 2.8h10c1.5 0 2.8-1.2 2.8-2.8v-3.6h-1.5V17zM14.9 3v1.5h3.7l-6.4 6.4 1.1 1.1 6.4-6.4v3.7h1.5V3h-6.3z"
                                                ></path>
                                            </svg>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div><!-- .pp-column-right -->
        <?php
    }

    /**
     * Check if Checklists Pro active
     */

     public static function isChecklistsProActive() {
        if (defined('PPCHPRO_VERSION')) {
            return true;
        }
        return false;
     }
}

