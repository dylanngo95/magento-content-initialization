<?php

namespace Magento\ContentInitialization\Setup\Categories;

use Magento\ContentInitialization\Setup\UpgradeData;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Setup\SetupInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Catalog\Model\Category;
use Magento\Catalog\Model\CategoryFactory;
use Magento\ContentInitialization\Helper\FileParser;
use Magento\ContentInitialization\Helper\MediaMigration;

/**
 * Class CreateCategories
 * @package Magento\ContentInitialization\Setup\Categories
 */
class CreateCategories
{
    const PATH = 'categories/categories.json';

    /**
     * @var FileParser
     */
    private $fileParser;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Category
     */
    private $category;

    /**
     * @var CategoryFactory
     */
    private $categoryFactory;

    /**
     * @var MediaMigration
     */
    private $mediaHelper;

    /**
     * @param FileParser $fileParser
     * @param StoreManagerInterface $storeManager
     * @param Category $category
     * @param CategoryFactory $categoryFactory
     * @param MediaMigration $mediaHelper
     */
    public function __construct(
        FileParser $fileParser,
        StoreManagerInterface $storeManager,
        Category $category,
        CategoryFactory $categoryFactory,
        MediaMigration $mediaHelper
    ){
        $this->fileParser = $fileParser;
        $this->storeManager = $storeManager;
        $this->category = $category;
        $this->categoryFactory = $categoryFactory;
        $this->mediaHelper = $mediaHelper;
    }

    /**
     * Applies migration.
     *
     * @param SetupInterface|null $setup
     * @throws FileSystemException
     * @throws NoSuchEntityException
     */
    public function apply(SetupInterface $setup = null)
    {
        $store = $this->storeManager->getStore();
        $storeId = $store->getStoreId();
        $rootCategoryId = $store->getRootCategoryId();
        $rootCategory = $this->category->load($rootCategoryId);

        $files = [
        ];

        foreach ($this->fileParser->getJSONContent(self::PATH) as $data) {
            $categoryTmp = $this->categoryFactory->create();
            $url = strtolower($data['name']);
            $cleanUrl = trim(preg_replace('/ +/', '', preg_replace('/[^A-Za-z0-9 ]/', '', urldecode(html_entity_decode(strip_tags($url))))));
            // Check url is exists
            if ($categoryTmp->loadByAttribute('url_key', $cleanUrl) !== false) {
                continue;
            }

            $files[] = $data['image'];
            $categoryTmp->setName($data['name']);
            $categoryTmp->setIsActive(true);
            $categoryTmp->setUrlKey($cleanUrl);
            $categoryTmp->setDescription($data['description']);
            $categoryTmp->setParentId($rootCategory->getId());
            $categoryTmp->setStoreId($storeId);
            $mediaAttribute =['image', 'small_image', 'thumbnail'];
            $categoryTmp->setImage($data['image'], $mediaAttribute, true, false);
            $categoryTmp->setPath($rootCategory->getPath());
            $categoryTmp->setDisplayMode('PRODUCTS_AND_PAGE');
            $categoryTmp->save();
        }

        $this->mediaHelper
            ->copyMediaFiles(
                $files,
                UpgradeData::MIGRATION_MODULE,
                'catalog/category'
            );

    }
}
