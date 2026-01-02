<?php
/**
 * Copyright © 2019 Wyomind. All rights reserved.
 * See LICENSE.txt for license details.
 */

namespace Wyomind\Watchlog\Model;

use Magento\Backend\Model\Auth\AuthenticationException;
use Magento\Backend\Model\Auth\PluginAuthenticationException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\HTTP\PhpEnvironment\Request;
use Magento\Framework\Stdlib\DateTime\DateTime;
use Wyomind\Framework\Helper\Module;
use Wyomind\Watchlog\Helper\Data;

/**
 * Class Auth
 * @package Wyomind\Watchlog\Model
 * 
 */
class Auth
{
    /**
     * @var DateTime|null
     */
    protected $datetime = null;

    /**
     * @var Request|null
     */
    protected $request = null;

    /**
     * @var null|AttemptsFactory
     */
    protected $attemptsFactory = null;

    /**
     * @var null|Module
     */
    protected $framework = null;

    /**
     * @var null|Data
     */
    protected $watchlogHelper = null;

    /**
     * @var null
     */
    protected $auth = null;

    /**
     * Auth constructor.
     * @param DateTime $datetime
     * @param Request $request
     * @param AttemptsFactory $attemptsFactory
     * @param Module $framework
     * @param Data $watchlogHelper
     */
    public function __construct(
        DateTime        $datetime,
        Request         $request,
        AttemptsFactory $attemptsFactory,
        Module          $framework,
        Data            $watchlogHelper
    ) {
    
        $this->datetime = $datetime;
        $this->request = $request;
        $this->attemptsFactory = $attemptsFactory;
        $this->framework = $framework;
        $this->watchlogHelper = $watchlogHelper;
    }

    /**
     * @param $ex
     */
    public function throwException($ex)
    {
        $this->auth->throwException($ex);
    }

    public function aroundLogin(
        \Magento\Backend\Model\Auth $auth,
        \Closure                    $closure,
        $login,
        $password
    ) {
    
        $this->auth = $auth;
        $exception = null;
        try {
            $closure($login, $password);
        } catch (PluginAuthenticationException $e) {
            $exception = $e;
        } catch (LocalizedException $e) {
            $exception = $e;
        } catch (AuthenticationException $e) {
            $exception = $e;
        }

        $this->addAttempt($login, $password, $exception);
        if ($exception != null) {
            throw $exception;
        }

        return null;
    }

    /**
     * @param $login
     * @param $password
     * @param null $e
     */
    public function addAttempt(
        $login,
        $password,
        $e = null
    ) {
    
        $data = [
            'login' => $login,
            'ip' => strtok($this->request->getClientIp(), ','),
            'date' => $this->datetime->gmtDate('Y-m-d H:i:s'),
            'status' => Data::SUCCESS,
            'message' => '',
            'url' => $this->request->getRequestUri()
        ];

        if ($e != null) { // failed
            $data['status'] = Data::FAILURE;
            $data['message'] = $e->getMessage();
        } else { // success
            $this->watchlogHelper->checkNotification();
        }

        $attempt = $this->attemptsFactory->create()->load(0);
        $attempt->setData($data);
        $attempt->save();
    }
}
