<?php
/**
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\Catalog\Test\TestCase\Product;

use Magento\Catalog\Test\Fixture\Category;
use Magento\Catalog\Test\Fixture\CatalogProductSimple;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductIndex;
use Magento\Catalog\Test\Page\Adminhtml\CatalogProductNew;
use Magento\Mtf\Fixture\FixtureFactory;
use Magento\Mtf\ObjectManager;
use Magento\Mtf\TestCase\Injectable;

/**
 * Test Flow:
 * 1. Login to the backend.
 * 2. Navigate to Products > Catalog.
 * 3. Start to create simple product.
 * 4. Fill in data according to data set.
 * 5. Save Product.
 * 6. Perform appropriate assertions.
 *
 * @group Products_(CS)
 * @ZephyrId MAGETWO-23414
 */
class CreateSimpleProductEntityTest extends Injectable
{
    /* tags */
    const TEST_TYPE = 'acceptance_test';
    const MVP = 'yes';
    const DOMAIN = 'MX';
    /* end tags */

    /**
     * Configuration setting.
     *
     * @var string
     */
    protected $configData;

    /**
     * Category fixture.
     *
     * @var Category
     */
    protected $category;

    /**
     * Product page with a grid.
     *
     * @var CatalogProductIndex
     */
    protected $productGrid;

    /**
     * Page to create a product.
     *
     * @var CatalogProductNew
     */
    protected $newProductPage;

    /**
     * Prepare data.
     *
     * @param Category $category
     * @return array
     */
    public function __prepare(Category $category)
    {
        $category->persist();

        return [
            'category' => $category
        ];
    }

    /**
     * Injection data.
     *
     * @param Category $category
     * @param CatalogProductIndex $productGrid
     * @param CatalogProductNew $newProductPage
     * @return void
     */
    public function __inject(
        Category $category,
        CatalogProductIndex $productGrid,
        CatalogProductNew $newProductPage
    ) {
        $this->category = $category;
        $this->productGrid = $productGrid;
        $this->newProductPage = $newProductPage;
    }

    /**
     * Run create product simple entity test.
     *
     * @param string $configData
     * @param array $productData
     * @param Category $category
     * @param FixtureFactory $fixtureFactory
     * @return array
     */
    public function testCreate($configData, array $productData, Category $category, FixtureFactory $fixtureFactory)
    {
        $this->configData = $configData;

        // Preconditions
        ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->configData]
        )->run();
        $product = $fixtureFactory->createByCode('catalogProductSimple',
            [
                'data' => array_merge($productData, ['category_ids' => ['category' => $category]])
            ]
        );

        // Steps
        $this->productGrid->open();
        $this->productGrid->getGridPageActionBlock()->addProduct('simple');
        $this->newProductPage->getProductForm()->fill($product, null, $category);
        $this->newProductPage->getFormPageActions()->save();

        return ['product' => $product];
    }

    /**
     * Clean data after running test.
     *
     * @return void
     */
    public function tearDown()
    {
        ObjectManager::getInstance()->create(
            'Magento\Core\Test\TestStep\SetupConfigurationStep',
            ['configData' => $this->configData, 'rollback' => true]
        )->run();
    }
}
