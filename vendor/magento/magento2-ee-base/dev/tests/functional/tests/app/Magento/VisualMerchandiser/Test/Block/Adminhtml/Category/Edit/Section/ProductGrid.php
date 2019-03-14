<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */

namespace Magento\VisualMerchandiser\Test\Block\Adminhtml\Category\Edit\Section;

use \Magento\VisualMerchandiser\Test\Block\Adminhtml\Category\AddProduct\NameTab;
use Magento\Mtf\Client\Locator;

class ProductGrid extends \Magento\Catalog\Test\Block\Adminhtml\Category\Edit\Section\ProductGrid
{
    /**
     * Filters array mapping.
     *
     * @var array
     */
    protected $filters = [
        'sku' => [
            'selector' => '#catalog_category_products_filter_sku',
        ],
        'name' => [
            'selector' => '#catalog_category_products_filter_name',
        ],
        'visibility' => [
            'selector' => '#catalog_category_products_filter_visibility',
            'input' => 'select',
        ],
        'status' => [
            'selector' => '#catalog_category_products_filter_status',
            'input' => 'select',
        ],
    ];

    /**
     * 'Add Product' dialog XPath locator
     *
     * @var string
     */
    protected $addProductDialog = '//aside[contains(@class,"show")]';

    /**
     * Search for item and select it
     *
     * @param array $filter
     * @throws \Exception
     */
    public function searchAndSelect(array $filter)
    {
        $dialog = $this->getAddProductDialog();
        $dialog->openDialog();

        /* @var \Magento\VisualMerchandiser\Test\Block\Adminhtml\Category\AddProduct\NameTab $nameTab */
        $nameTab = $dialog
            ->openTab(NameTab::NAME_TAB)
            ->getTab(NameTab::NAME_TAB);

        $grid = $nameTab->getDataGrid();
        $grid->waitLoader();
        $grid->searchByNameAndSelect($filter);

        $dialog->saveAndClose();
        $grid->waitLoader();
    }

    /**
     * @return \Magento\VisualMerchandiser\Test\Block\Adminhtml\Category\AddProduct
     */
    public function getAddProductDialog()
    {
        return $this->blockFactory->create(
            \Magento\VisualMerchandiser\Test\Block\Adminhtml\Category\AddProduct::class,
            ['element' => $this->browser->find($this->addProductDialog, Locator::SELECTOR_XPATH)]
        );
    }

    /**
     * Prepare data to perform search, fill in search filter
     *
     * @param array $filters
     * @throws \Exception
     */
    protected function prepareForSearch(array $filters)
    {
        unset($filters['in_category']);

        parent::prepareForSearch($filters);
    }
}
