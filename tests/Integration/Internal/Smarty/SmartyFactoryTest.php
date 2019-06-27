<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Integration\Internal\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContext;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyFactory;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyConfigurationFactory;

class SmartyFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider smartySettingsDataProvider
     *
     * @param bool  $securityMode
     * @param array $smartySettings
     */
    public function testSmartyPropertiesAreSetCorrect($securityMode, $smartySettings)
    {
        $smartyContext = $this->getSmartyContextMock($securityMode);
        $smartyFactory = new SmartyFactory(new SmartyConfigurationFactory($smartyContext));

        $smarty = $smartyFactory->getSmarty();

        foreach ($smartySettings as $varName => $varValue) {
            $this->assertTrue(isset($smarty->$varName), $varName . ' setting was not set');
            $this->assertEquals($varValue, $smarty->$varName, 'Not correct value of the smarts setting: ' . $varName);
        }
    }

    /**
     * @return array
     */
    public function smartySettingsDataProvider()
    {
        return [
            'security on' => [1, $this->getSmartySettingsWithSecurityOn()],
            'security off' => [0, $this->getSmartySettingsWithSecurityOff()]
        ];
    }

    private function getSmartySettingsWithSecurityOn(): array
    {
        $config = Registry::getConfig();
        $templateDirs = Registry::getUtilsView()->getTemplateDirs();
        return [
            'security' => true,
            'php_handling' => SMARTY_PHP_REMOVE,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => $templateDirs,
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'security_settings' => [
                'PHP_HANDLING' => false,
                'IF_FUNCS' =>
                    [
                        0 => 'array',
                        1 => 'list',
                        2 => 'isset',
                        3 => 'empty',
                        4 => 'count',
                        5 => 'sizeof',
                        6 => 'in_array',
                        7 => 'is_array',
                        8 => 'true',
                        9 => 'false',
                        10 => 'null',
                        11 => 'XML_ELEMENT_NODE',
                        12 => 'is_int',
                    ],
                'INCLUDE_ANY' => false,
                'PHP_TAGS' => false,
                'MODIFIER_FUNCS' =>
                    [
                        0 => 'count',
                        1 => 'round',
                        2 => 'floor',
                        3 => 'trim',
                        4 => 'implode',
                        5 => 'is_array',
                        6 => 'getimagesize',
                    ],
                'ALLOW_CONSTANTS' => true,
                'ALLOW_SUPER_GLOBALS' => true,
            ],
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartySettingsWithSecurityOff(): array
    {
        $config = Registry::getConfig();
        $templateDirs = Registry::getUtilsView()->getTemplateDirs();
        return [
            'security' => false,
            'php_handling' => $config->getConfigParam('iSmartyPhpHandling'),
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'caching' => false,
            'compile_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'cache_dir' => $config->getConfigParam('sCompileDir')."/smarty/",
            'compile_id' => Registry::getUtilsView()->getTemplateCompileId(),
            'template_dir' => $templateDirs,
            'debugging' => false,
            'compile_check' => $config->getConfigParam('blCheckTemplates'),
            'plugins_dir' => $this->getSmartyPlugins(),
        ];
    }

    private function getSmartyPlugins()
    {
        return array_merge(Registry::getUtilsView()->getSmartyPluginDirectories(), ['plugins']);
    }

    private function getSmartyContextMock($securityMode = false): SmartyContext
    {
        $config = Registry::getConfig();
        $config->setConfigParam('blDemoShop', $securityMode);

        return new SmartyContext($config, Registry::getUtilsView());
    }
}
