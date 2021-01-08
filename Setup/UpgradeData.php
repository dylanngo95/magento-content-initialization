<?php

namespace Magento\ContentInitialization\Setup;

use Magento\ContentInitialization\Setup\Categories\CreateCategories;
use Magento\ContentInitialization\Setup\CMS\Block\AddCmsBlocks;
use Magento\ContentInitialization\Setup\CMS\Page\AddCmsPages;

/**
 * Class UpgradeData
 * @package Magento\ContentInitialization\Setup
 */
class UpgradeData extends AbstractUpgradeData
{
    const MIGRATION_MODULE = 'Magento_ContentInitialization';

    protected array $migrations = [
        '0.0.1' => CreateCategories::class,
        '0.0.2' => AddCmsBlocks::class,
        '0.0.3' => AddCmsPages::class
    ];
}
