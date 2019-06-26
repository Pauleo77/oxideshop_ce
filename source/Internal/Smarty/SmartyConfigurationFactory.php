<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

use OxidEsales\EshopCommunity\Core\Registry;
use OxidEsales\EshopCommunity\Internal\Smarty\Extension\CacheResourcePlugin;

/**
 * Class SmartyConfigurationFactory
 * @package OxidEsales\EshopCommunity\Internal\Smarty
 */
class SmartyConfigurationFactory implements SmartyConfigurationFactoryInterface
{
    /**
     * @var SmartyContextInterface
     */
    private $context;

    /**
     * SmartyConfigurationFactory constructor.
     *
     * @param SmartyContextInterface $context
     */
    public function __construct(SmartyContextInterface $context)
    {
        $this->context = $context;
    }

    /**
     * Define basic smarty settings
     */
    private function getSettings()
    {
        $compilePath = $this->getTemplateCompilePath();
        return [
            'caching' => false,
            'left_delimiter' => '[{',
            'right_delimiter' => '}]',
            'compile_dir' => $compilePath,
            'cache_dir' => $compilePath,
            'template_dir' => $this->context->getTemplateDirectories(),
            'compile_id' => $this->getTemplateCompileId(),
            'default_template_handler_func' => [Registry::getUtilsView(), '_smartyDefaultTemplateHandler'],
            'debugging' => $this->context->getTemplateEngineDebugMode(),
            'compile_check' => $this->context->getTemplateCompileCheckMode()
        ];
    }

    /**
     * Define smarty security settings.
     */
    private function getSecuritySettings()
    {
        $configuration = [
            'php_handling' => (int) $this->context->getTemplatePhpHandlingMode(),
            'security' => false
        ];
        if ($this->context->getTemplateSecurityMode()) {
            $configuration = [
                'php_handling' => SMARTY_PHP_REMOVE,
                'security' => true,
                'secure_dir' => $this->context->getTemplateDirectories(),
                'security_settings' => [
                    'IF_FUNCS' => ['XML_ELEMENT_NODE', 'is_int'],
                    'MODIFIER_FUNCS' => ['round', 'floor', 'trim', 'implode', 'is_array', 'getimagesize'],
                    'ALLOW_CONSTANTS' => true,
                ]
            ];
        }
        return $configuration;
    }

    /**
     * Collect smarty plugins.
     */
    private function getPlugins()
    {
        return $this->context->getSmartyPluginDirectories();
    }

    /**
     * Sets an array of prefilters.
     */
    private function getPrefilterPlugin()
    {
        $prefilterPath = $this->getPrefilterPath();
        $prefilter['smarty_prefilter_oxblock'] = $prefilterPath . '/prefilter.oxblock.php';
        if ($this->context->showTemplateNames()) {
            $prefilter['smarty_prefilter_oxtpldebug'] = $prefilterPath . '/prefilter.oxtpldebug.php';
        }

        return $prefilter;
    }

    /**
     * Sets an array of resources.
     */
    private function getResources()
    {
        return [
            'ox' => [
                'ox_get_template',
                'ox_get_timestamp',
                'ox_get_secure',
                'ox_get_trusted'

            ]
        ];
    }

    /**
     * @return string
     */
    private function getPrefilterPath() : string
    {
        return $this->context->getSourcePath() . '/Core/Smarty/Plugin';
    }

    /**
     * Returns a full path to Smarty compile dir
     *
     * @return string
     */
    private function getTemplateCompilePath(): string
    {
        return $this->context->getTemplateCompileDirectory();
    }

    /**
     * Get template compile id.
     *
     * @return string
     */
    private function getTemplateCompileId(): string
    {
        return $this->context->getTemplateCompileId();
    }

    /**
     * Get properties for smarty:
     * [
     *   'settings' => 'smartyCommonSettings',
     *   'security_settings' => 'smartySecuritySettings',
     *   'plugins' => 'smartyPluginsToRegister',
     *   'prefilters' => 'smartyPreFiltersToRegister',
     *   'resources' => 'smartyResourcesToRegister',
     * ]
     *
     * @return array
     */
    public function getConfiguration()
    {
        return [
            'settings' => $this->getSettings(),
            'security_settings' => $this->getSecuritySettings(),
            'plugins' => $this->getPlugins(),
            'prefilters' => $this->getPrefilterPlugin(),
            'resources' => $this->getResources()
        ];
    }
}
