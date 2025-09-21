<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

use Amasty\Base\Model\SysInfo\Command\LicenceService\ProcessLicenseValidationResponse;
use Amasty\Base\Utils\Http\Response\ResponseFactory;
use Magento\TestFramework\Helper\Bootstrap;

$objectManager = Bootstrap::getObjectManager();
$responseFactory = $objectManager->get(ResponseFactory::class);
$responseProcessor = $objectManager->get(ProcessLicenseValidationResponse::class);

$validResponse = (string)file_get_contents(__DIR__ . '/responses/valid_ping_response.json');
$validResponseArray = json_decode($validResponse, true);

$responseObject = $responseFactory->create('test', $validResponseArray);
$responseObject->setData('code', 200);

$responseProcessor->process($responseObject);
