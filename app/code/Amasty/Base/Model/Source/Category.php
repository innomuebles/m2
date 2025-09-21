<?php

declare(strict_types=1);

/**
 * @author Amasty Team
 * @copyright Copyright (c) Amasty (https://www.amasty.com)
 * @package Magento 2 Base Package
 */

namespace Amasty\Base\Model\Source;

use Magento\Framework\Data\OptionSourceInterface;

/**
 * Category Tree represented by single dimension array
 *
 * For variants - create virtualType of this class or OptionBuilder
 *
 * @api
 * @since 1.15.2
 */
class Category implements OptionSourceInterface
{
    private const ROOT_LEVEL = 0;

    public const EMPTY_OPTION_ID = 0;

    /**
     * @var Category\OptionsBuilder
     */
    private $optionsBuilder;

    /**
     * @var string|null
     */
    private $caption;

    /**
     * @var array|array[]
     */
    private $filters;

    /**
     * @param Category\OptionsBuilder $optionsBuilder
     * @param string|null $caption null if a constant option isn't needed, any string value will be used as a label
     * @param array $filters where key is column/attribute name and value is filter params for collection
     */
    public function __construct(
        Category\OptionsBuilder $optionsBuilder,
        ?string $caption = null,
        array $filters = ['level' => ['gt' => self::ROOT_LEVEL]]
    ) {
        $this->optionsBuilder = $optionsBuilder;
        $this->caption = $caption;
        $this->filters = $filters;
    }

    public function toOptionArray(): array
    {
        $optionArray = [];
        $arr = $this->toArray();
        foreach ($arr as $value => $label) {
            $optionArray[] = [
                'value' => $value,
                'label' => $label
            ];
        }

        return $optionArray;
    }

    /**
     * Get options in "key-value" format
     */
    public function toArray(): array
    {
        foreach ($this->filters as $filterName => $filterParams) {
            $this->optionsBuilder->addFilter($filterName, $filterParams);
        }

        $options = $this->optionsBuilder->build();
        if ($this->caption) {
            $options = [self::EMPTY_OPTION_ID => $this->caption] + $options;
        }

        return $options;
    }
}
