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
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\View\Asset\File\NotFoundException;
use Magento\Framework\View\Asset\Repository;

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
     * @var Repository
     */
    private $assetRepo;

    /**
     * @param ConfigInterface $config
     * @param CheckPatternInterface $checkPattern
     * @param GetBaseUrlInterface $baseUrl
     */
    public function __construct(
        ConfigInterface $config,
        CheckPatternInterface $checkPattern,
        GetBaseUrlInterface $baseUrl,
        Repository $assetRepo
    ) {
        $this->config = $config;
        $this->checkPattern = $checkPattern;
        $this->baseUrl = $baseUrl;
        $this->assetRepo = $assetRepo;
    }

    /**
     * @inheritDoc
     */
    public function execute(HttpRequestInterface $request): ?string
    {
        $content = null;
        $fullAction = $request->getFullActionName();
        
        try {
            $type = $request->getFullActionName('_');
            $asset = $this->assetRepo->createAsset('css/' . $type . '_' . 'critical.css', ['_secure' => 'false']);
            $content = $asset->getContent();
        } catch (LocalizedException | NotFoundException $e) {
        }

        if ($this->config->isEnabled() === false) {
            return $content;
        }
        
        try {
            $criticalCss = $this->config->getCriticalCss();
            foreach ($criticalCss as $robot) {
                if (
                    $this->checkPattern->execute($fullAction, $robot['pattern'])
                    || $this->checkPattern->execute($this->baseUrl->execute(), $robot['pattern'])
                ) {
                    $content = $robot['critical_css'];
                }
            }
        } catch (\Throwable $e) {
        }

        return $content;
    }
}
