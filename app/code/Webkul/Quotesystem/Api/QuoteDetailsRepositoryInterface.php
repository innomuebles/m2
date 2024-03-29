<?php
/**
 * Quote Interface
 *
 * @category  Webkul
 * @package   Webkul_Quotesystem
 * @author    Webkul
 * @copyright Copyright(c)Webkul Software Private Limited(https://webkul.com)
 * @license   https://store.webkul.com/license.html
 */

namespace Webkul\Quotesystem\Api;

/**
 * quoteDetails interface.
 *
 * @api
 */
interface QuoteDetailsRepositoryInterface
{
    /**
     * Create or update a quote.
     *
     * @param  \Webkul\Quotesystem\Api\Data\QuoteDetailsInterface $quote
     * @return \Webkul\Quotesystem\Api\Data\QuoteDetailsInterface
     */
    public function save(\Webkul\Quotesystem\Api\Data\QuoteDetailsInterface $quote);

    /**
     * Get quote by quote Id
     *
     * @param  int $quoteId
     * @return \Webkul\Quotesystem\Api\Data\QuoteDetailsInterface
     */
    public function getById($quoteId);

    /**
     * Delete quote.
     *
     * @param  \Webkul\Quotesystem\Api\Data\QuoteDetailsInterface $quote
     * @return bool true on success
     */
    public function delete(\Webkul\Quotesystem\Api\Data\QuoteDetailsInterface $quote);

    /**
     * Delete quote by ID.
     *
     * @param  int $quoteId
     * @return bool true on success
     */
    public function deleteById($quoteId);

    /**
     * get Product Name by quote ID.
     *
     * @param  int $quoteId
     * @return bool true on success
     */
    public function getProductByQuoteId($quoteId);

    /**
     * Get Id of the customer of the qoute which you want to delete.
     *
     * @param  int $quoteId
     * @return int $customerId
     */
    public function getCustomerIdByQuoteId($quoteId);
}
