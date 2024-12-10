<form method="post" id="pp-checklists-global">
    <?php wp_nonce_field('ppch-global-checklists'); ?>

    <div class="submit-top">
        <input type="submit" name="submit" id="submit-top" class="button button-primary"
            value="<?php echo esc_attr__('Save Changes', 'publishpress-checklists'); ?>">
    </div>
    <ul id="pp-checklists-post-type-filter" class="nav-tab-wrapper">
        <?php foreach ($context['post_types'] as $post_type_key => $post_type_label) : ?>
            <li class="nav-tab post-type-<?php echo esc_attr($post_type_key); ?>">
                <a href="#<?php echo esc_attr($post_type_key); ?>"><?php echo esc_html($post_type_label); ?></a>
            </li>
        <?php endforeach; ?>
    </ul>



    <div class="pressshack-admin-wrapper pp-checklists-tabs-wrapper">
        <div class="pp-checklists-tabs">
            <?php
            /**
             * Render field tabs
             */
            $i = 0;
            foreach ($context['post_types'] as $post_type_key => $post_type_label) { ?>
                <ul id="list-<?php echo esc_attr($post_type_key); ?>" class="pp-checklists-tabs-list <?php echo $i === 0 ? 'active' : ''; ?>">
                    <?php foreach ($context['tabs'][$post_type_key] as $key => $args) {
                        $has_requirements = array_filter($context['requirements'][$post_type_key], function ($requirement) use ($key) {
                            return $requirement->group === $key;
                        });

                        // Skip if the tab has no requirements or is not the custom tab
                        if (empty($has_requirements) && $key !== 'custom') continue;
                    ?>
                        <li class="<?php echo esc_attr($post_type_key); ?>">
                            <a data-tab="<?php echo esc_attr($key); ?>"
                                data-post-type="<?php echo esc_attr($post_type_key); ?>"
                                href="#">
                                <span class="<?php echo esc_attr($args['icon']); ?>">
                                    <?php if (isset($args['svg']) && !empty($args['svg'])) : echo $args['svg']; endif; ?>
                                </span>
                                <span class="item"><?php echo esc_html_e($args['label']); ?></span>
                            </a>
                        </li>
                    <?php }
                    $i++; ?>
                </ul>
            <?php } ?>
        </div>
        <div class="pp-checklists-content-wrapper wrapper-column">
            <table class="form-table pp-checklists-content-table fixed wp-list-table pp-checklists-requirements-settings" id="pp-checklists-requirements" role="presentation">
                <thead>
                    <tr>
                        <th><?php echo esc_html($context['lang']['description']); ?></th>
                        <th><?php echo esc_html($context['lang']['action']); ?></th>
                        <?php
                        /**
                         * @param string $html
                         * @param $requirement
                         *
                         * @return string
                         */
                        do_action('publishpress_checklists_tasks_list_th');
                        ?>
                        <th><?php echo esc_html($context['lang']['params']); ?></th>
                    </tr>
                </thead>
                <?php
                $i = 0;
                foreach ($context['post_types'] as $post_type_key => $post_type_label) : ?>
                    <tbody id="<?php echo 'pp-checklists-tab-body-' . esc_attr($post_type_key); ?>" class="pp-checklists-tab-body <?php echo $i === 0 ? 'active' : ''; ?>" style="display: none;">
                        <?php
                        foreach ($context['tabs'][$post_type_key] as $group => $tabInfo) :
                            foreach ($context['requirements'] as $post_type => $post_type_requirements) : ?>
                                <?php
                                // Skip if the post type is not the current one
                                if ($post_type !== $post_type_key) continue;
                                $group_has_requirements = false;
                                ?>
                                <?php foreach ($post_type_requirements as $requirement) : ?>
                                    <?php if ($requirement->group === $group) :
                                        $group_has_requirements = true;
                                    ?>
                                        <tr
                                            class="pp-checklists-requirement-row ppch-<?php echo esc_attr($requirement->group); ?>-group"
                                            style="display: none;"
                                            data-id="<?php echo esc_attr($requirement->name); ?>"
                                            data-post-type="<?php echo esc_attr($post_type); ?>">

                                            <td>
                                                <?php
                                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                echo $requirement->get_setting_title_html();
                                                ?>
                                            </td>
                                            <td>
                                                <?php
                                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                echo $requirement->get_setting_action_list_html(); ?>
                                            </td>
                                            <?php
                                            /**
                                             * @param string $html
                                             * @param $requirement
                                             *
                                             * @return string
                                             */
                                            do_action('publishpress_checklists_tasks_list_td', $requirement, $post_type);
                                            ?>
                                            <td class="pp-checklists-task-params">
                                                <?php
                                                // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
                                                echo $requirement->get_setting_field_html();
                                                ?>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                                <?php if ($post_type === $post_type_key && !$group_has_requirements) : ?>
                                    <tr class="pp-checklists-requirement-row ppch-<?php echo esc_attr($group); ?>-group" data-post-type="<?php echo esc_attr($post_type); ?>">
                                        <td colspan="4" id="empty-custom-rule">
                                            <?php echo esc_html_e(sprintf(__('No %s requirements for this post type.', 'publishpress-checklists'), $group)); ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endforeach; ?>
                    </tbody>
                <?php endforeach; ?>
            </table>
        </div>

    </div>



    <table class="wp-list-table striped pp-custom-checklists-table">
        <thead>
            <tr>
                <th><strong><?php esc_html_e('New Item', 'publishpress-checklists'); ?></strong></th>
                <th><strong><?php esc_html_e('Description', 'publishpress-checklists'); ?></strong></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <a id="pp-checklists-add-button" href="javascript:void(0);" class="button button-secondary">
                        <span class="dashicons dashicons-plus-alt"></span> <?php echo esc_html($context['lang']['add_custom_item']); ?>
                    </a>
                </td>
                <td>
                    <span class="pp-checklists-field-description">
                        <?php echo esc_html__('Custom tasks do not complete automatically. Users must check the box to show they have completed the task.', 'publishpress-checklists'); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <td>
                    <a id="pp-checklists-openai-promt-button" href="javascript:void(0);" class="button button-secondary">
                        <span class="dashicons dashicons-plus-alt"></span> <?php esc_html_e('Add OpenAI Prompt task', 'publishpress-checklists'); ?>
                    </a>
                </td>
                <td>
                    <span class="pp-checklists-field-description">

                    </span>
                    <p class="pp-checklists-field-description" style="margin-top: 0;"><?php echo esc_html__('The prompt should be in form of a question.', 'publishpress-checklists'); ?> <a target="_blank" href="<?php echo esc_url(admin_url('admin.php?page=ppch-settings')); ?>"><?php echo esc_html__('This feature requires an OpenAI API Key.', 'publishpress-checklists'); ?></a></p>
                </td>
            </tr>
        </tbody>
    </table>

    <input type="submit" name="submit" id="submit" class="button button-primary"
        value="<?php echo esc_attr__('Save Changes', 'publishpress-checklists'); ?>">
</form>