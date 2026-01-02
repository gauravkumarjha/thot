<?php

namespace V4U\CmsWatcher\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Backend\Model\Auth\Session as AdminSession;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\Registry;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State as AppState;

class CmsBlockSaveAfter implements ObserverInterface
{
    protected $transportBuilder;
    protected $adminSession;
    protected $remoteAddress;
    protected $registry;
    protected $logger;
    protected $request;
    protected $appState;

    public function __construct(
        TransportBuilder $transportBuilder,
        AdminSession $adminSession,
        RemoteAddress $remoteAddress,
        Registry $registry,
        LoggerInterface $logger,
        RequestInterface $request,
        AppState $appState
    ) {
        $this->transportBuilder = $transportBuilder;
        $this->adminSession = $adminSession;
        $this->remoteAddress = $remoteAddress;
        $this->registry = $registry;
        $this->logger = $logger;
        $this->request = $request;
        $this->appState = $appState;
    }

    public function execute(Observer $observer)
    {
        try {
            $block = $observer->getEvent()->getObject();

            $title = $block->getTitle();
            $identifier = $block->getIdentifier();
            $newContent = $block->getContent();
            $blockId = $block->getId();

            $key = $block->getData('v4u_old_content_key');
            $oldContent = null;
            if ($key && $this->registry->registry($key) !== null) {
                $oldContent = $this->registry->registry($key);
                if (method_exists($this->registry, 'unregister')) {
                    $this->registry->unregister($key);
                }
            }

            $adminUser = $this->adminSession->getUser();
            $username = $adminUser ? $adminUser->getUserName() : 'N/A';

            $ipAddress = $this->remoteAddress->getRemoteAddress();
            $ipAddress = $this->request->getServer('HTTP_CF_CONNECTING_IP')
                ?: $this->request->getServer('HTTP_X_FORWARDED_FOR')
                ?: $this->request->getServer('HTTP_X_REAL_IP')
                ?: $this->remoteAddress->getRemoteAddress();
 
            $area = 'N/A';
            $fullAction = 'N/A';
            $requestUri = 'N/A';
            $httpMethod = 'N/A';
            $authHeader = null;
            try {
                $area = $this->appState->getAreaCode();
            } catch (\Exception $ex) {
                $area = 'unknown';
            }

            try {
                $module = $this->request->getModuleName();
                $controller = $this->request->getControllerName();
                $action = $this->request->getActionName();
                $fullAction = trim($module . '_' . $controller . '_' . $action, '_');

                $requestUri = $this->request->getRequestUri() ?: $this->request->getPathInfo();
                $httpMethod = $this->request->getMethod();

                $authHeader = $this->request->getHeader('Authorization') ?: $this->request->getServer('HTTP_AUTHORIZATION');
            } catch (\Throwable $t) {
                // ignore
            }

            $source = 'unknown';
            if ($area === 'adminhtml') {
                $source = 'Admin Panel (adminhtml)';
            } elseif (strpos((string)$area, 'webapi') !== false || strpos((string)$requestUri, '/rest/') === 0) {
                $source = 'REST API';
            } elseif (strpos((string)$area, 'crontab') !== false || php_sapi_name() === 'cli') {
                $source = 'CLI/CRON/Script';
            } else {
                if (!empty($authHeader)) {
                    $source = 'API (auth header detected)';
                } else {
                    $source = ($area ?: 'unknown') . ' (request: ' . $requestUri . ')';
                }
            }

            $safeOld = $oldContent === null ? '[not available]' : (strlen($oldContent) > 3000 ? substr($oldContent, 0, 3000) . "\n\n...[truncated]" : $oldContent);
            $safeNew = $newContent === null ? '[empty]' : (strlen($newContent) > 3000 ? substr($newContent, 0, 3000) . "\n\n...[truncated]" : $newContent);

            $templateVars = [
                'title' => $title,
                'identifier' => $identifier,
                'block_id' => $blockId,
                'username' => $username ?: 'N/A',
                'ip' => $ipAddress,
                'old_content' => $safeOld,
                'new_content' => $safeNew,
                'area' => $area,
                'source' => $source,
                'request_uri' => $requestUri,
                'http_method' => $httpMethod,
                'auth_header_present' => $authHeader ? 'yes' : 'no',
                'full_action' => $fullAction
            ];

            // recipient hardcoded as requested
            $toEmail = 'gaurav@digitalimpression.in';
            $from = ['email' => 'info@thehouseofthings.com', 'name' => 'The House of Things'];

            $transport = $this->transportBuilder
                ->setTemplateIdentifier('v4u_cms_block_change_template')
                ->setTemplateOptions(['area' => \Magento\Framework\App\Area::AREA_ADMINHTML, 'store' => 0])
                ->setTemplateVars($templateVars)
                ->setFrom($from)
                ->addTo("gaurav@digitalimpressions.in")
                ->setReplyTo('info@thehouseofthings.com')
                ->getTransport();

            $this->logger->info('V4U_CmsWatcher: preparing to send email for block ' . $title);
            $transport->sendMessage();
        } catch (\Throwable $e) {
            $this->logger->error('V4U_CmsWatcher email error: ' . $e->getMessage());
        }
    }
}
