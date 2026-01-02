<?php

namespace V4U\CmsWatcher\Plugin;

use Magento\Framework\App\State;
use Magento\Framework\Exception\LocalizedException;

class DisableCmsPageApi
{
    protected $appState;

    public function __construct(State $appState)
    {
        $this->appState = $appState;
    }

    public function beforeSave()
    {
        if ($this->isApiRequest()) {
            throw new LocalizedException(__('API access denied: Cannot create or update CMS Pages.'));
        }
    }

    public function beforeDeleteById()
    {
        if ($this->isApiRequest()) {
            throw new LocalizedException(__('API access denied: Cannot delete CMS Pages.'));
        }
    }

    private function isApiRequest()
    {
        try {
            return $this->appState->getAreaCode() === 'webapi_rest';
        } catch (\Exception $e) {
            return false;
        }
    }
}
