<?php
declare(strict_types=1);

namespace Rossmc\Partytown\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Partytown configuration model.
 */
class Config
{
    private const XML_PARTYTOWN_MODULE = 'rossmc_partytown';

    private const XML_PARTYTOWN_ENABLED = self::XML_PARTYTOWN_MODULE . '/general/enabled';
    private const XML_PARTYTOWN_DEBUG_ENABLED = self::XML_PARTYTOWN_MODULE . '/general/debug_enabled';
    private const XML_PARTYTOWN_FORWARDING = self::XML_PARTYTOWN_MODULE . '/general/forwarding';
    private const XML_PARTYTOWN_MAIN_THREAD_SCRIPTS = self::XML_PARTYTOWN_MODULE . '/general/main_thread_scripts';
    private const XML_PARTYTOWN_UNREGISTER_SW = self::XML_PARTYTOWN_MODULE . '/general/unregister_sw';

    /**
     * @param ScopeConfigInterface $scopeConfig
     * @param SerializerInterface $serializer
     */
    public function __construct(
        private readonly ScopeConfigInterface $scopeConfig
    ) {
    }

    /**
     * Check if partytown module is enabled in configuration.
     *
     * @return bool
     */
    public function isEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(static::XML_PARTYTOWN_ENABLED);
    }

    /**
     * Check if partytown debug featyre is enabled in configuration.
     *
     * @return bool
     */
    public function isDebugEnabled(): bool
    {
        return $this->scopeConfig->isSetFlag(static::XML_PARTYTOWN_DEBUG_ENABLED);
    }

    /**
     * Get configuration for partytown forwarding.
     *
     * @return string|null
     */
    public function getForwarding(): ?string
    {
        return $this->scopeConfig->getValue(static::XML_PARTYTOWN_FORWARDING) ?: null;
    }

    /**
     * Get main thread scripts
     *
     * @return string|null
     */
    public function getMainThreadScripts(): ?string
    {
        return $this->scopeConfig->getValue(static::XML_PARTYTOWN_MAIN_THREAD_SCRIPTS) ?: null;
    }

    /**
     * Check if partytown unregister service worker is enabled in configuration.
     *
     * @return bool
     */
    public function isUnregisterSw(): bool
    {
        return $this->scopeConfig->isSetFlag(static::XML_PARTYTOWN_UNREGISTER_SW);
    }
}
