<?php

namespace MageBig\MbFrame\Block;

use MageBig\MbFrame\Helper\Data;
use Magento\Framework\View\Element\Template\Context;

class Template extends \Magento\Framework\View\Element\Template
{
    /**
     * @var Data
     */
    public $mbHelper;

    /**
     * Constructor
     *
     * @param Context $context
     * @param Data $mbHelper
     * @param array $data
     */
    public function __construct(
        Context $context,
        Data $mbHelper,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->mbHelper = $mbHelper;
    }

    /**
     * Get critical svg logo
     *
     * @return string
     */

    public function getSvgLogo()
    {
        $svg = (string) $this->mbHelper->getConfig('mbconfig/general/logo_loading');

        if (!$svg) {
            return $svg;
        }

        $tags = [ 'script', 'iframe', 'a', 'embed'];
        $svg = preg_replace('#<(' . implode('|', $tags) . ')>.*?<\/$1>#s', '', $svg);
        $pattern = '/(<.+?)(?<=\s)on[a-z]+\s*=\s*(?:([\'"])(?!\2).+?\2|\S+?\(.*?\)(?=[\s>]))(.*?>)/i';

        return (string) preg_replace($pattern, "$1 $3", $svg);
    }
}
