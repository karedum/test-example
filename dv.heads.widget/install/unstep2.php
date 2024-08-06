<?php


use Bitrix\Main\Localization\Loc;

if (!check_bitrix_sessid()) {
    return;
}
if ($errorException = $APPLICATION->getException()) {
    CAdminMessage::showMessage(
        Loc::getMessage('DV_HEADS_WIDGET_ERROR_DELETE').': '.$errorException->GetString()
    );
} else {
    CAdminMessage::showNote(
        Loc::getMessage('DV_HEADS_WIDGET_SUCCESS_DELETE')
    );
}
?>
<form action="<?= $APPLICATION->getCurPage(); ?>">
    <?=bitrix_sessid_post()?>
    <input type="hidden" name="lang" value="<?= LANGUAGE_ID; ?>" />
    <input type="submit" value="<?=Loc::getMessage('DV_HEADS_WIDGET_BACK_TO_MODULES')?>">
</form>