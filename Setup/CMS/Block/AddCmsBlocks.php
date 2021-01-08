<?php

namespace Magento\ContentInitialization\Setup\CMS\Block;

use Magento\Cms\Model\BlockFactory;
use Magento\Cms\Model\ResourceModel\Block as ResourceBlock;
use Magento\ContentInitialization\Setup\UpgradeData;
use Magento\ContentInitialization\Helper\FileParser;
use Magento\Framework\Setup\SetupInterface;
use Magento\ContentInitialization\Helper\Cms;
use Magento\ContentInitialization\Helper\MediaMigration;

/**
 * Class AddCmsBlocks
 * @package Magento\ContentInitialization\Setup\CMS\Block
 */
class AddCmsBlocks
{
    const PATH = 'cms-blocks/cms-blocks.json';

    /**
     * @var Cms
     */
    private $cmsHelper;

    /**
     * @var MediaMigration
     */
    protected $mediaMigration;

    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var ResourceBlock
     */
    private ResourceBlock $resourceBlock;

    /**
     * @var BlockFactory
     */
    private BlockFactory $blockFactory;

    /**
     * @param Cms $cmsHelper
     * @param MediaMigration $mediaMigration
     * @param FileParser $fileParser
     * @param ResourceBlock $resourceBlock
     * @param BlockFactory $blockFactory
     */
    public function __construct(
        Cms $cmsHelper,
        MediaMigration $mediaMigration,
        FileParser $fileParser,
        ResourceBlock $resourceBlock,
        BlockFactory $blockFactory
    ){
        $this->cmsHelper = $cmsHelper;
        $this->mediaMigration = $mediaMigration;
        $this->fileParser = $fileParser;
        $this->resourceBlock = $resourceBlock;
        $this->blockFactory = $blockFactory;
    }

    /**
     * Applies migration.
     *
     * @param SetupInterface|null $setup
     * @throws \Magento\Framework\Exception\FileSystemException
     * @throws \Exception
     */
    public function apply(SetupInterface $setup = null)
    {
        $this->copyImages();

        foreach ($this->fileParser->getCMSBlockDataFromJson(self::PATH) as $data) {
            $block = $this->blockFactory->create();
            $this->resourceBlock->load($block, $data['identifier']);
            if (!$block->getId()) {
                $this->cmsHelper->updateBlock($data['identifier'], $data['content'], $data);
            }
        }
    }

    /**
     * Adds About us page images to wysiwyg folder
     * @return void
     */
    private function copyImages()
    {
        // add media for homepage
        $homepageMedia = [
            'B1.jpg',
            'B2.jpg',
            'B3.jpg',
            'B3.jpg',
            'B4.jpg',
            'B5.jpg',
            'B6.jpg',
            'B7.jpg'
        ];

        $this->mediaMigration->copyMediaFiles($homepageMedia, UpgradeData::MIGRATION_MODULE, 'wysiwyg/homepage');
    }
}
