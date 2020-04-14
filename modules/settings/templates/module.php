<?php if (!empty($context['url'])) : ?>
<a href="<?php echo $context['url']; ?>">
    <?php endif; ?>

    <div
            class="publishpress-module module-enabled <?php echo $context['has_config_link'] ? 'has-configure-link' : ''; ?>"
            id="<?php echo $context['url']; ?>">

        <?php if (!empty($context['icon_class'])) : ?>
            <span class="<?php echo $context['icon_class']; ?> float-right module-icon"></span>
        <?php endif; ?>

        <form
                method="GET"
                action="<?php echo $context['form_action']; ?>">

            <h4><?php echo $context['title']; ?></h4>
            <p><?php echo $context['description']; ?></p>
        </form>
    </div>

    <?php if (!empty($context['url'])) : ?>
</a>
<?php endif; ?>
