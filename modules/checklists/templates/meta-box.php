<?php $show_required_legend = false; ?>

<div id="<?php echo esc_attr($context['metadata_taxonomy']); ?>-meta-box">
    <input type="hidden" name="<?php echo esc_attr($context['metadata_taxonomy']); ?>_nonce"
           value="<?php echo $context['nonce']; ?>"/>

    <ul id="pp-checklists-req-box">
        <?php if (empty($context['requirements'])) : ?>
            <p>
                <em><?php echo $context['lang']['to_use_checklists']; ?> <a
                            href="<?php echo $context['configure_link']; ?>"
                            class=""><?php echo $context['lang']['please_choose_req']; ?></a></em>
            </p>
        <?php else : ?>
            <?php foreach ($context['requirements'] as $key => $req) : ?>
                <li
                        id="pp-checklists-req-<?php echo $key; ?>"
                        class="pp-checklists-req <?php echo $req['rule']; ?> status-<?php echo $req['status'] ? 'yes' : 'no'; ?> <?php echo $req['is_custom'] ? 'pp-checklists-custom-item' : ''; ?>"
                        data-id="<?php echo $key; ?>"
                        data-type="<?php echo $req['type']; ?>">

                    <?php if ($req['is_custom']) : ?>
                        <input type="hidden" name="_PPCH_custom_item[<?php echo $req['id']; ?>]"
                               value="<?php echo $req['status'] ? 'yes' : 'no'; ?>"/>
                    <?php endif; ?>

                    <span class="dashicons dashicons-<?php echo $req['status'] ? 'yes' : 'no'; ?>"></span>
                    <span class="status-label"><?php echo $req['label']; ?></span>

                    <span>
                        <?php if ($req['rule'] === 'block') : ?>
                            *
                            <?php $show_required_legend = true; ?>
                        <?php endif; ?>
                    </span>
                </li>
            <?php endforeach; ?>
        <?php endif; ?>
    </ul>

    <?php if ($show_required_legend) : ?>
        <em>(*) <?php echo $context['lang']['required']; ?></em>
    <?php endif; ?>
</div>

<?php # Modal Windows; ?>
<div class="remodal" data-remodal-id="pp-checklists-modal-alert"
     data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
    <div id="pp-checklists-modal-alert-content"></div>
    <br>
    <button data-remodal-action="cancel" class="remodal-cancel"><?php echo $context['lang']['dont_publish']; ?></button>
</div>

<div class="remodal" data-remodal-id="pp-checklists-modal-confirm"
     data-remodal-options="hashTracking: false, closeOnOutsideClick: false">
    <div id="pp-checklists-modal-confirm-content"></div>
    <br>
    <button data-remodal-action="cancel" class="remodal-cancel"><?php echo $context['lang']['dont_publish']; ?></button>
    <button data-remodal-action="confirm"
            class="remodal-confirm"><?php echo $context['lang']['yes_publish']; ?></button>
</div>
