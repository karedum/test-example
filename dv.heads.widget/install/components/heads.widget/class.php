<?php

use Bitrix\Iblock\Model\Section;
use Bitrix\Main\Config\Option;
use Bitrix\Main\FileTable;
use Bitrix\Main\Loader;
use Bitrix\Main\Localization\Loc;
use Bitrix\Main\ORM\Objectify\Collection;
use Bitrix\Main\UserTable;
use Bitrix\Main\Web\Uri;
use Dv\Heads\Widget\Main\Helper;

Loc::loadMessages(__FILE__);

class HeadsWidget extends CBitrixComponent
{

    public const MODULES = [
        'iblock',
        'dv.heads.widget'
    ];

    public function executeComponent()
    {
        try {
            $this->includeModules();
            $this->arResult['HEADS'] = $this->getHeads();

        } catch (Throwable $e) {
            $this->arResult['ERROR_MESSAGE'] = Loc::getMessage('DV_HEADS_WIDGET_ANY_ERROR', [
                '#ERROR#' => $e->getMessage()
            ]);
        }
        $this->IncludeComponentTemplate();
    }

    public function includeModules(): void
    {
        foreach (self::MODULES as $moduleName) {
            if (!Loader::includeModule($moduleName)) {
                throw new Exception(Loc::getMessage('DV_HEADS_WIDGET_ERROR_INCLUDE_MODULE', [
                    '#MODULE_NAME#' => $moduleName
                ]));
            }
        }
    }

    public function getHeads()
    {
        $iblockId = Option::get('intranet', 'iblock_structure');
        $sectionEntity = Section::compileEntityByIblock($iblockId);
        $departments = $sectionEntity::query()
            ->setSelect(["ID", "UF_HEAD",
                'UF_HEAD_NAME' => 'UF_HEAD_USER.NAME',
                'UF_HEAD_LAST_NAME' => 'UF_HEAD_USER.LAST_NAME',
                'UF_HEAD_SECOND_NAME' => 'UF_HEAD_USER.SECOND_NAME',
                'UF_HEAD_LOGIN' => 'UF_HEAD_USER.LOGIN',
                'UF_HEAD_PERSONAL_PHOTO' => 'UF_HEAD_USER.PERSONAL_PHOTO'
            ])
            ->registerRuntimeField(
                'UF_HEAD_USER',
                [
                    'data_type' => UserTable::getEntity(),
                    'reference' => [
                        '=this.UF_HEAD' => 'ref.ID',
                    ],
                    'join_type' => "INNER"
                ]
            )->exec();

        while ($dep = $departments->fetch()) {
            $arImage = [];
            if (!empty($dep['UF_HEAD_PERSONAL_PHOTO'])) {
                $arImage = CIntranetUtils::InitImage($dep['UF_HEAD_PERSONAL_PHOTO'], 100);
            }

            $headId = $dep['UF_HEAD'];
            if (!isset($heads[$headId])) {
                $heads[$headId] = [
                    'FULL_NAME' => \CUser::formatName(
                        \CSite::getNameFormat(null), [
                        'NAME' => $dep['UF_HEAD_NAME'],
                        'LOGIN' => $dep['UF_HEAD_LOGIN'],
                        'SECOND_NAME' => $dep['UF_HEAD_SECOND_NAME'],
                        'LAST_NAME' => $dep['UF_HEAD_LAST_NAME'],
                    ], true, false
                    ),
                    'IMAGE_SRC' => Uri::urnEncode($arImage['CACHE']['src']),
                    'USER_ID' => $headId,
                    'USER_LINK' => CComponentEngine::MakePathFromTemplate(
                        COption::GetOptionString("intranet", "path_user"),
                        ['USER_ID' => $headId]
                    ),
                ];
            }
        }
        return $heads ?? [];

    }

}






















