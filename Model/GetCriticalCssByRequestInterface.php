<?php
/**
 * Copyright (c) 2021. All rights reserved.
 * @author: Volodymyr Hryvinskyi <mailto:volodymyr@hryvinskyi.com>
 */

declare(strict_types=1);

namespace Hryvinskyi\SeoCriticalCssFrontendUi\Model;

use Magento\Framework\App\HttpRequestInterface;

interface GetCriticalCssByRequestInterface
{
    /**
     * @param HttpRequestInterface $request
     *
     * @return string
     */
    public function execute(HttpRequestInterface $request): ?string;
}
