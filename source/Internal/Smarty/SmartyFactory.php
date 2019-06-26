<?php declare(strict_types=1);
/**
 * Copyright © OXID eSales AG. All rights reserved.
 * See LICENSE file for license details.
 */

namespace OxidEsales\EshopCommunity\Internal\Smarty;

/**
 * Creates and configures the Smarty object.
 */
class SmartyFactory implements SmartyFactoryInterface
{
    /**
     * @var \Smarty
     */
    private $smarty;

    /**
     * @var array
     */
    private $configuration;

    /**
     * SmartyFactory constructor.
     *
     * @param SmartyConfigurationFactoryInterface $configuration
     */
    public function __construct(SmartyConfigurationFactoryInterface $configuration)
    {
        $this->configuration = $configuration->getConfiguration();
    }

    /**
     * @return \Smarty
     */
    public function getSmarty() : \Smarty
    {
        if (!isset($this->smarty)) {
            $this->buildSmarty();
        }

        return $this->smarty;
    }

    /**
     * @param \Smarty $smarty
     */
    public function setSmarty(\Smarty $smarty)
    {
        $this->smarty = $smarty;
    }

    /**
     * Create new smarty instance and configure it
     */
    private function buildSmarty()
    {
        $this->smarty = new \Smarty();
        $this->setSettings();
        $this->setSecuritySettings();
        $this->registerPlugins();
        $this->registerPrefilters();
        $this->registerResources();
    }

    /**
     * Sets properties of smarty object.
     */
    private function setSettings()
    {
        if (isset($this->configuration['settings'])) {
            $settings = $this->configuration['settings'];
            foreach ($settings as $key => $value) {
                $this->smarty->$key = $value;
            }
        }
    }

    /**
     * Sets security options of smarty object.
     */
    private function setSecuritySettings()
    {
        if (isset($this->configuration['security_settings'])) {
            $settings = $this->configuration['security_settings'];
            foreach ($settings as $key => $value) {
                if (is_array($value)) {
                    foreach ($value as $subKey => $subValue) {
                        if (is_array($subValue) ) {
                            $this->smarty->{$key}[$subKey] = $this->getMergeSettings($this->smarty->{$key}[$subKey], $subValue);
                        } else {
                            $this->smarty->{$key}[$subKey] = $subValue;
                        }
                    }
                } else {
                    $this->smarty->$key = $value;
                }
            }
        }
    }

    private function getMergeSettings($originalSettings, $newSettings)
    {
        return array_merge($originalSettings, $newSettings);
    }

    /**
     * Registers a resource of smarty object.
     */
    private function registerResources()
    {
        if (isset($this->configuration['resources'])) {
            $resourcesToRegister = $this->configuration['resources'];
            foreach ($resourcesToRegister as $key => $resources) {
                $this->smarty->register_resource($key, $resources);
            }
        }
    }

    /**
     * Register prefilters of smarty object.
     */
    private function registerPrefilters()
    {
        if (isset($this->configuration['prefilters'])) {
            $prefilters = $this->configuration['prefilters'];
            foreach ($prefilters as $prefilter => $path) {
                if (file_exists($path)) {
                    include_once $path;
                    $this->smarty->register_prefilter($prefilter);
                }
            }
        }
    }

    /**
     * Register plugins of smarty object.
     */
    private function registerPlugins()
    {
        if (isset($this->configuration['plugins'])) {
            $this->smarty->plugins_dir = array_merge(
                $this->configuration['plugins'],
                $this->smarty->plugins_dir
            );
        }
    }
}
