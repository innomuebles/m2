<?php
namespace Dfe\CrPayme\Block;
class Response extends \Magento\Framework\View\Element\Template
{
	function __construct(\Magento\Framework\View\Element\Template\Context $context)
	{
		parent::__construct($context);
	}

	function sayHello()
	{
		return __('Hello World');
	}
}