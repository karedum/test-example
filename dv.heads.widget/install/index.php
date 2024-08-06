<?php
if (!defined("B_PROLOG_INCLUDED") || B_PROLOG_INCLUDED !== true) die();


use Bitrix\Main\Config\Option;
use Bitrix\Main\EventManager;
use Bitrix\Main\HttpApplication;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ModuleManager;


Loc::loadMessages(__FILE__);

class dv_heads_widget extends CModule
{

    var $MODULE_ID = 'dv.heads.widget';
    var $MODULE_NAME;
    var $MODULE_DESCRIPTION;
    var $MODULE_VERSION;
    var $MODULE_VERSION_DATE;
    public $eventManager;

    public function __construct()
    {
        $arModuleVersion = [];

        include_once(__DIR__ . '/version.php');

        $this->MODULE_NAME = Loc::getMessage('DV_HEADS_WIDGET_MODULE_NAME');
        $this->MODULE_DESCRIPTION = Loc::getMessage('DV_HEADS_WIDGET_MODULE_DESCRIPTION');
        $this->MODULE_VERSION = $arModuleVersion['VERSION'];
        $this->MODULE_VERSION_DATE = $arModuleVersion['VERSION_DATE'];


        $this->eventManager = EventManager::getInstance();
    }

    public function UnInstallFiles()
    {
        if (is_dir($_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/dv/heads.widget")) {
            if (!DeleteDirFilesEx("/bitrix/components/dv/heads.widget")) {
                throw new Exception(Loc::getMessage('DV_HEADS_WIDGET_DELETE_ERROR'));
            }
        }
        return true;
    }

    public function InstallFiles()
    {
        if (!CopyDirFiles(__DIR__ . "/components",
            $_SERVER["DOCUMENT_ROOT"] . "/bitrix/components/dv", true, true)) {
            throw new Exception(Loc::getMessage('DV_HEADS_WIDGET_COPY_ERROR'));
        }
        return true;
    }

    public function registerEvents()
    {
        $this->eventManager->registerEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\Dv\Heads\Widget\Main\Events',
            'onProlog'
        );
    }

    public function unregisterEvents()
    {
        $this->eventManager->unRegisterEventHandler(
            'main',
            'OnProlog',
            $this->MODULE_ID,
            '\Dv\Heads\Widget\Main\Events',
            'onProlog'
        );
    }

    public function DoInstall()
    {
        global $APPLICATION;
        try {
            if (!CheckVersion(ModuleManager::getVersion("main"), "14.00.00")) {
                throw new Exception(Loc::getMessage('DV_HEADS_WIDGET_VERSION_ERROR'));
            }
            $this->InstallFiles();
            $this->registerEvents();
            ModuleManager::registerModule($this->MODULE_ID);
            Loader::includeModule($this->MODULE_ID);
        } catch (Throwable $e) {
            $APPLICATION->ThrowException($e->getMessage());
            $this->unInstall();
        }
        $APPLICATION->IncludeAdminFile($this->MODULE_NAME, __DIR__ . '/step.php');
    }

    public function unInstall()
    {
        $this->unregisterEvents();
        $this->UnInstallFiles();
        ModuleManager::unRegisterModule($this->MODULE_ID);
    }


    public function DoUninstall()
    {
        global $APPLICATION, $step;
        try {
            $step = intval($step);
            if ($step < 2) {
                $APPLICATION->IncludeAdminFile($this->MODULE_NAME, __DIR__ . '/unstep1.php');
            } elseif ($step == 2) {

                $requestData = HttpApplication::getInstance()->getContext()->getRequest()->getValues();

                if ($requestData['delete']) {
                    Option::delete($this->MODULE_ID);
                }

                $this->unInstall();
            }
        } catch (Throwable $e) {
            $APPLICATION->ThrowException($e->getMessage());
        }
        $APPLICATION->IncludeAdminFile($this->MODULE_NAME, __DIR__ . '/unstep2.php');
    }
}
