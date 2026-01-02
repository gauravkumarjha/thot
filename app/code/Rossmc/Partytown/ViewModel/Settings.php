<?php
declare(strict_types=1);

namespace Rossmc\Partytown\ViewModel;

use Magento\Framework\View\Element\Block\ArgumentInterface;
use Rossmc\Partytown\Model\Config;

/**
 * Class Settings ViewModel
 */
class Settings implements ArgumentInterface
{
    /**
     * @param ConfigInterface $config
     */
    public function __construct(
        private Config $config
    ) {
    }

    /**
     * @return bool
     */
    public function isEnabled(): ?bool
    {
        return $this->config->isEnabled();
    }

    /**
     * @return bool
     */
    public function isDebugEnabled(): ?bool
    {
        return $this->config->isDebugEnabled();
    }

    /**
     * @return string|null
     */
    public function getForwarding(): ?string
    {
        return $this->config->getForwarding();
    }

    /**
     * @return string|null
     */
    public function getMainThreadScripts(): ?string
    {
        return $this->config->getMainThreadScripts();
    }

    /**
     * @return bool
     */
    public function isUnregisterSw(): ?bool
    {
        return $this->config->isUnregisterSw();
    }
}
