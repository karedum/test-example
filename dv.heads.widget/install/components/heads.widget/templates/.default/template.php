<?php

use Bitrix\Main\Localization\Loc;

Loc::loadMessages(__FILE__);
?>
<div class="sidebar-widget sidebar-widget-heads">
    <div class="sidebar-widget-top">
        <div class="sidebar-widget-top-title">
            <?= Loc::getMessage('DV_HEADS_WIDGET_TITLE') ?>
        </div>
    </div>
    <div class="sidebar-widget-item-wrap sidebar-widget-item-heads">

        <?php if (isset($arResult['ERROR_MESSAGE'])) {
            echo $arResult['ERROR_MESSAGE'];
        } else {
            if (!empty($arResult['HEADS'])) {
                foreach ($arResult['HEADS'] as $head) { ?>
                    <a href="<?= $head['USER_LINK'] ?>" class="sidebar-widget-item --row widget-last-item">
                        <?php

                        $defaultImg = !$head['IMAGE_SRC'] ? 'user-default-avatar' : '';
                        $style = $head['IMAGE_SRC'] ? 'background: url('.$head['IMAGE_SRC'].') no-repeat center; background-size: cover;' : '';
                        ?>
                        <span class="user-avatar <?= $defaultImg ?>"
                              style="<?=$style?>"></span>
                        <span class="sidebar-user-info">
				<span><?= $head['FULL_NAME'] ?></span>
			</span>
                    </a>
                <?php }
            } else { ?>
                <div class="heads-empty"><?= Loc::getMessage('DV_HEADS_WIDGET_EMPTY') ?></div>
            <?php }
        } ?>
    </div>
</div>
