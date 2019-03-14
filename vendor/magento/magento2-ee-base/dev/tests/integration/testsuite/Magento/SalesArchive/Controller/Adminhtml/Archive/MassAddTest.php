<?php
/***
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\SalesArchive\Controller\Adminhtml\Archive;

use Magento\Framework\App\Request\Http as HttpRequest;
use Magento\Framework\App\RequestInterface;
use Magento\TestFramework\Helper\Bootstrap;

class MassAddTest extends \Magento\TestFramework\TestCase\AbstractBackendController
{
    public function setUp()
    {
        $this->resource = 'Magento_SalesArchive::add';
        $this->uri = 'backend/sales/archive/massadd';
        $this->_request = Bootstrap::getObjectManager()
            ->get(RequestInterface::class);
        parent::setUp();
    }

    public function testAclHasAccess()
    {
        /** @var HttpRequest $request */
        $request = $this->_request;
        $request->setMethod(HttpRequest::METHOD_POST);

        parent::testAclHasAccess();
    }
}
