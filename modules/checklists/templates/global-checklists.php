<form method="post" id="pp-checklists-global">
    <?php wp_nonce_field('ppch-global-checklists'); ?>

    <div id="pp-checklists-post-type-filter">
        <?php foreach ($context['post_types'] as $post_type_key => $post_type_label) : ?>
            <a href="#<?php echo $post_type_key; ?>"><?php echo $post_type_label; ?></a>
        <?php endforeach; ?>
    </div>

    <table class="wp-list-table striped pp-checklists-requirements-settings" id="pp-checklists-requirements">
        <thead>
        <tr>
            <th><?php echo $context['lang']['description']; ?></th>
            <th><?php echo $context['lang']['action']; ?></th>
            <?php
            /**
             * @param string $html
             * @param $requirement
             *
             * @return string
             */
            do_action('publishpress_checklists_tasks_list_th');
            ?>
            <th><?php echo $context['lang']['params']; ?></th>
        </tr>
        </thead>

        <tbody>

        <?php foreach ($context['requirements'] as $post_type => $post_type_requirements) : ?>
            <?php foreach ($post_type_requirements as $requirement) : ?>
                <tr
                        class="pp-checklists-requirement-row"
                        data-id="<?php echo $requirement->name; ?>"
                        data-post-type="<?php echo $post_type; ?>">

                    <td><?php echo $requirement->get_setting_title_html(); ?></td>
                    <td><?php echo $requirement->get_setting_action_list_html(); ?></td>
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
                        <?php echo $requirement->get_setting_field_html(); ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
    <br>
    <div>
        <a id="pp-checklists-add-button" href="javascript:void(0);" class="button button-secondary">
            <span class="dashicons dashicons-plus-alt"></span> <?php echo $context['lang']['add_custom_item']; ?>
        </a>
        <span class="pp-checklists-field-description"><?php echo __('Custom tasks do not complete automatically. Users must check the box to show they have completed the task.', 'publishpress-checklists'); ?></span>
    </div>

    <input type="submit" name="submit" id="submit" class="button button-primary"
           value="<?php echo __('Save Changes', 'publishpress-checklists'); ?>">
</form>
