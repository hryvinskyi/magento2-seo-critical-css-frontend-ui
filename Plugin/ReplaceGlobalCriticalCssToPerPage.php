<?php
/**
 * Copyright (c) 2021. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoCriticalCssFrontendUi\Plugin;

use Hryvinskyi\SeoCriticalCssFrontendUi\Model\GetCriticalCssByRequestInterface;
use Magento\Framework\App\HttpRequestInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Theme\Block\Html\Header\CriticalCss;
use Psr\Log\LoggerInterface;

class ReplaceGlobalCriticalCssToPerPage
{
    /**
     * @var GetCriticalCssByRequestInterface
     */
    private $criticalCssByRequest;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param GetCriticalCssByRequestInterface $criticalCssByRequest
     * @param RequestInterface $request
     */
    public function __construct(
        GetCriticalCssByRequestInterface $criticalCssByRequest,
        RequestInterface $request,
        LoggerInterface $logger
    ) {
        $this->criticalCssByRequest = $criticalCssByRequest;
        $this->request = $request;
        $this->logger = $logger;
    }

    /**
     * @param CriticalCss $subject
     * @param $result
     *
     * @return mixed|string|null
     * @noinspection PhpUnusedParameterInspection
     */
    public function afterGetCriticalCssData(CriticalCss $subject, $result)
    {
        try {
            if ($this->request instanceof HttpRequestInterface
                && ($criticalCss = $this->criticalCssByRequest->execute($this->request))
            ) {
                $result = $criticalCss;
            }
        } catch (\Throwable $e) {
            $this->logger->critical($e->getMessage());
        }

        return $result;
    }
}
