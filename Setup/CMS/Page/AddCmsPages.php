<?php

namespace Magento\ContentInitialization\Setup\CMS\Page;

use Magento\ContentInitialization\Helper\FileParser;
use Magento\Framework\Setup\SetupInterface;
use Magento\ContentInitialization\Helper\Cms;
use Magento\Cms\Model\PageFactory;

/**
 * Class AddCmsPages
 * @package Magento\ContentInitialization\Setup\CMS\Page
 */
class AddCmsPages
{
    const PATH = 'cms-pages/cms-pages.json';
    const PAGE_LAYOUT = '1column';

    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var Cms
     */
    private $cmsHelper;

    /**
     * @var PageFactory
     */
    private PageFactory $pageFactory;

    /**
     * AddAboutUsPage constructor.
     *
     * @param FileParser $fileParser
     * @param Cms $cms
     * @param PageFactory $pageFactory
     */
    public function __construct(
        FileParser $fileParser,
        Cms $cms,
        PageFactory $pageFactory
    )
    {
        $this->fileParser = $fileParser;
        $this->cmsHelper = $cms;
        $this->pageFactory = $pageFactory;
    }

    /**
     * Applies migration.
     *
     * @param SetupInterface $setup
     * @throws \Magento\Framework\Exception\FileSystemException
     */
    public function apply(SetupInterface $setup = null)
    {
        foreach ($this->fileParser->getCMSBlockDataFromJson(self::PATH) as $data) {
            $page = $this->pageFactory->create();
            $page->load($data['identifier']);
            if (!$page->getId()) {
                $this->cmsHelper->updatePage(
                    $data['identifier'],
                    $data['content'],
                    [
                        'stores' => [$data['stores']],
                        'title' => $data['title'],
                        'content_heading' => $data['content_heading'],
                        'page_width' => $data['page_width'],
                        'page_layout' => self::PAGE_LAYOUT
                    ]
                );
            }
        }
    }

}
