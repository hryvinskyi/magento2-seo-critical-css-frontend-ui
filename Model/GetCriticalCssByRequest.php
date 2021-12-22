<?php
/**
 * Copyright (c) 2021. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoCriticalCssFrontendUi\Model;

use Hryvinskyi\SeoCriticalCss\Model\ConfigInterface;
use Hryvinskyi\SeoApi\Api\CheckPatternInterface;
use Hryvinskyi\SeoApi\Api\GetBaseUrlInterface;
use Magento\Framework\App\HttpRequestInterface;

class GetCriticalCssByRequest implements GetCriticalCssByRequestInterface
{
    /**
     * @var ConfigInterface
     */
    private $config;

    /**
     * @var CheckPatternInterface
     */
    private $checkPattern;
    /**
     * @var GetBaseUrlInterface
     */
    private $baseUrl;

    /**
     * @param ConfigInterface $config
     * @param CheckPatternInterface $checkPattern
     * @param GetBaseUrlInterface $baseUrl
     */
    public function __construct(
        ConfigInterface $config,
        CheckPatternInterface $checkPattern,
        GetBaseUrlInterface $baseUrl
    ) {
        $this->config = $config;
        $this->checkPattern = $checkPattern;
        $this->baseUrl = $baseUrl;
    }

    /**
     * @inheritDoc
     */
    public function execute(HttpRequestInterface $request): ?string
    {
        $fullAction = $request->getFullActionName();
        $criticalCss = $this->config->getCriticalCss();

        foreach ($criticalCss as $robot) {
            if ($this->checkPattern->execute($fullAction, $robot['pattern'])
                || $this->checkPattern->execute($this->baseUrl->execute(), $robot['pattern'])
            ) {
                return $robot['critical_css'];
            }
        }

        return null;
    }
}
