<?php
/**
 * Copyright © Pronko Consulting (https://www.pronkoconsulting.com)
 * See LICENSE for the license details.
 */
declare(strict_types=1);

namespace Pronko\SelectiveCache\Cron;

use Psr\Log\LoggerInterface;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;

/**
 * Class FlushInvalidatedCacheTypes flushes invalidated cache types by cronjob
 */
class FlushInvalidatedCacheTypes
{
    /**
     * @var TypeListInterface
     */
    private TypeListInterface $cacheTypeList;

    /**
     * @var ScopeConfigInterface
     */
    private ScopeConfigInterface $scopeConfig;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @param TypeListInterface $cacheTypeList
     * @param ScopeConfigInterface $scopeConfig
     * @param LoggerInterface $logger
     */
    public function __construct(
        TypeListInterface $cacheTypeList,
        ScopeConfigInterface $scopeConfig,
        LoggerInterface $logger
    ) {
        $this->cacheTypeList = $cacheTypeList;
        $this->scopeConfig = $scopeConfig;
        $this->logger = $logger;
    }

    /**
     * Method execute flushes invalidated Cache types
     *
     * @return void
     */
    public function execute(): void
    {
        if (!$this->scopeConfig->isSetFlag('selectivecache/cron/enabled')) {
            return;
        }

        foreach ($this->cacheTypeList->getInvalidated() as $invalidatedType) {
            $this->cacheTypeList->cleanType($invalidatedType->getData('id'));
            $cacheLabels[] = $invalidatedType->getData('cache_type');
        }

        //TODO add configuration setting to enable/disable logging
        if (!empty($cacheLabels)) {
            $this->logger->info(
                sprintf("Cache types cleared automatically: %s", implode(', ', $cacheLabels))
            );
        }
    }
}
