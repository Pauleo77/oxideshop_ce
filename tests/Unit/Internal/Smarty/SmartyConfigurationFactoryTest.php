<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Tests\Unit\Internal\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyContextInterface;
use OxidEsales\EshopCommunity\Internal\Smarty\SmartyConfigurationFactory;

class SmartyConfigurationFactoryTest extends \PHPUnit\Framework\TestCase
{
    public function testGetConfigurationWithSecuritySettingsOff()
    {
        $smartyContextMock = $this->getSmartyContextMock();

        $factory = new SmartyConfigurationFactory($smartyContextMock);
        $configuration = $factory->getConfiguration();

        $this->assertSettings($configuration);
        $this->assertSecuritySettingsOff($configuration);
        $this->assertPrefilters($configuration);
        $this->assertPlugins($configuration);
        $this->assertResources($configuration);
    }

    public function testGetConfigurationWithSecuritySettingsOn()
    {
        $smartyContextMock = $this->getSmartyContextMock(true);

        $factory = new SmartyConfigurationFactory($smartyContextMock);
        $configuration = $factory->getConfiguration();

        $this->assertSettings($configuration);
        $this->assertSecuritySettingsOn($configuration);
        $this->assertPrefilters($configuration);
        $this->assertPlugins($configuration);
        $this->assertResources($configuration);
    }

    private function assertSettings(array $configuration)
    {
        $settings = [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => 'testCompileDir',
            'cache_dir' => 'testCompileDir',
            'template_dir' => ['testTemplateDir'],
            'compile_id' => '7f96e0d92070fd4733296e5118fd5a01',
            'default_template_handler_func' => [Registry::getUtilsView(), '_smartyDefaultTemplateHandler'],
            'debugging' => true,
            'compile_check' => true
        ];

        $this->assertEquals($settings, $configuration['settings']);
    }

    private function assertSecuritySettingsOff(array $configuration)
    {
        $settings = [
            'php_handling' => 1,
            'security' => false
        ];
        $this->assertEquals($settings, $configuration['security_settings']);
    }

    private function assertSecuritySettingsOn(array $configuration)
    {
        $settings = [
            'php_handling' => SMARTY_PHP_REMOVE,
            'security' => true,
            'secure_dir' => ['testTemplateDir'],
            'security_settings' => [
                'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                'ALLOW_CONSTANTS' => true,
                ]
            ];

        $this->assertEquals($settings, $configuration['security_settings']);
    }

    private function assertResources(array $configuration)
    {
        $smartyContextMock = $this->getSmartyContextMock();
        $settings = ['ox' => [
            'ox_get_template',
            'ox_get_timestamp',
            'ox_get_secure',
            'ox_get_trusted'
            ]
        ];

        $this->assertEquals($settings, $configuration['resources']);
    }

    private function assertPrefilters(array $configuration)
    {
        $settings = [
            'smarty_prefilter_oxblock' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxblock.php',
            'smarty_prefilter_oxtpldebug' => 'testShopPath/Core/Smarty/Plugin/prefilter.oxtpldebug.php',
        ];

        $this->assertEquals($settings, $configuration['prefilters']);
    }

    private function assertPlugins(array $configuration)
    {
        $settings = ['testModuleDir', 'testShopPath/Core/Smarty/Plugin'];

        $this->assertEquals($settings, $configuration['plugins']);
    }

    private function getSmartyContextMock($securityMode = false): SmartyContextInterface
    {
        $smartyContextMock = $this
            ->getMockBuilder(SmartyContextInterface::class)
            ->getMock();

        $smartyContextMock
            ->method('getTemplateEngineDebugMode')
            ->willReturn('2');

        $smartyContextMock
            ->method('showTemplateNames')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplateSecurityMode')
            ->willReturn($securityMode);

        $smartyContextMock
            ->method('getTemplateCompileDirectory')
            ->willReturn('testCompileDir');

        $smartyContextMock
            ->method('getTemplateDirectories')
            ->willReturn(['testTemplateDir']);

        $smartyContextMock
            ->method('getTemplateCompileCheckMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplatePhpHandlingMode')
            ->willReturn(true);

        $smartyContextMock
            ->method('getTemplateCompileId')
            ->willReturn('7f96e0d92070fd4733296e5118fd5a01');

        $smartyContextMock
            ->method('getSmartyPluginDirectories')
            ->willReturn(['testModuleDir', 'testShopPath/Core/Smarty/Plugin']);

        $smartyContextMock
            ->method('getSourcePath')
            ->willReturn('testShopPath');

        return $smartyContextMock;
    }
}