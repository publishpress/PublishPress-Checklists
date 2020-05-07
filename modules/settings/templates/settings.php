<div class="wrap content_checklist-admin <?php echo $context['show_sidebar'] ? 'allex_container_with_sidebar checklists_with_sidebar container' : ''; ?>">
    <div class="allex-row">
        <div class="<?php echo $context['show_sidebar'] ? 'allex-col-3-4' : 'allex-col-1'; ?>">
            <?php if ($context['show_tabs']) : ?>
                <h2 class="nav-tab-wrapper">
                    <?php foreach ($context['modules'] as $module) : ?>
                        <?php if (!empty($context['module']['options_page']) && $context['module']->options->enabled === 'on') : ?>
                            <a
                                    href="?page=<?php echo $context['slug']; ?>&module=<?php echo $context['module']->settings_slug; ?>"
                                    class="nav-tab <?php echo ($context['settings_slug'] == $context['module']->settings_slug) ? 'nav-tab-active' : ''; ?>">

                                <?php echo $context['module']->title; ?>
                            </a>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </h2>
            <?php endif; ?>

            <div class="ppch-settings"><?php echo $context['module_output']; ?></div>
        </div>

        <?php if ($context['show_sidebar']) : ?>
            <div class="allex-col-1-4">
                <?php echo $context['sidebar_output']; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
