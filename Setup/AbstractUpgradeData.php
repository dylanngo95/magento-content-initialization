<?php

namespace Magento\ContentInitialization\Setup;

use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\UpgradeDataInterface;

/**
 * Class AbstractUpgradeData
 * @package Magento\ContentInitialization\Setup
 */
abstract class AbstractUpgradeData implements UpgradeDataInterface
{
    use UpgradeTrait;

    /**
     * @inheritDoc
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $this->run($setup, $context);
    }
}
