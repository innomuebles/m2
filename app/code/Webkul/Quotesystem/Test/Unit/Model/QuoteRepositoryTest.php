<?php
/**
 * Quotes Repository
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright (c) Webkul Software Private Limited (https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */
namespace Webkul\Quotesystem\Test\Unit\Model;

use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;

class QuoteRepositoryTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var Webkul\Quotesystem\Model\QuoteRepository
     */
    protected $_quoteRepository;
    /**
     * @var Webkul\Quotesystem\Model\QuotesFactory
     */
    protected $_quoteFactoryMock;
    /**
     * @var Webkul\Quotesystem\Model\Quotes
     */
    protected $_quoteMock;
    /**
     * @var \Magento\Framework\TestFramework\Unit\Helper\ObjectManager
     */
    protected $objectManager;

    protected function setUp()
    {
        $this->objectManager = new ObjectManager($this);
        $this->_quoteFactoryMock = $this->getMock(
            'Webkul\Quotesystem\Model\QuotesFactory',
            ['create'],
            [],
            '',
            false
        );
        $this->_quoteMock = $this->getMock(
            'Webkul\Quotesystem\Model\Quotes',
            [
                'getEntityId',
                'load',
                'setData'
            ],
            [],
            '',
            false
        );
        $this->_quoteRepository = $this->objectManager->getObject(
            'Webkul\Quotesystem\Model\QuoteRepository',
            [
                'quoteFactory' => $this->_quoteFactoryMock
            ]
        );
    }
    /**
     * Test the delete by id
     *
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function testDeleteById()
    {
        $quoteId = 1;
        $sampleMock = $this->getMock(
            '\Webkul\Quotesystem\Model\Quotes',
            [],
            [],
            '',
            false
        );

        $this->_quoteFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($sampleMock));
        $sampleMock->expects($this->once())->method('load')
            ->with($quoteId)
            ->will($this->returnSelf());
        $sampleMock->expects($this->any())->method('getEntityId')
            ->will($this->returnValue($quoteId));
        $sampleMock->expects($this->once())->method('delete');

        $this->assertTrue($this->_quoteRepository->deleteById($quoteId));
    }
    /**
     * [testDelete delete a quote
     *
     * @return \Magento\Framework\Exception\StateException
     */
    public function testDelete()
    {
        $sampleId = 178;
        $entity = $this->getMockBuilder('\Webkul\Quotesystem\Model\Quotes')
            ->disableOriginalConstructor()
            ->getMock();
        $entity->expects($this->once())
            ->method('getEntityId')
            ->willReturn(178);
        $entity->expects($this->once())->method('delete');

        $this->assertTrue($this->_quoteRepository->delete($entity));
    }

    public function testGetById()
    {
        $this->_quoteFactoryMock->expects($this->once())->method('create')
            ->will($this->returnValue($this->_quoteMock));
        $this->_quoteMock->expects($this->once())->method('load')->with('30');
        $this->_quoteMock->expects($this->once())->method('getEntityId')->willReturn(30);
        $this->assertEquals($this->_quoteMock, $this->_quoteRepository->getById(30));
    }
}
