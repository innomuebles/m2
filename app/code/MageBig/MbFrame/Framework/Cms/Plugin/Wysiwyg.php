<?php

namespace MageBig\MbFrame\Framework\Cms\Plugin;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\DataObject;
use Magento\Framework\View\Asset\Repository;
use Magento\Ui\Block\Wysiwyg\ActiveEditor;
use Magento\Ui\Component\Wysiwyg\ConfigInterface;

class Wysiwyg
{
    /**
     * @var ActiveEditor
     */
    protected $activeEditor;

    /**
     * @var RequestInterface
     */
    private $request;
    protected $assetRepo;

    /**
     * @param Repository $assetRepo
     * @param $activeEditor
     * @param RequestInterface|null $request
     */
    public function __construct(
        Repository $assetRepo,
        $activeEditor = null,
        RequestInterface $request = null
    ) {
        $this->assetRepo = $assetRepo;
        try {
            /* Fix for Magento 2.1.x & 2.2.x that does not have this class and plugin should not work there */
            if (class_exists(ActiveEditor::class)) {
                $this->activeEditor = $activeEditor
                    ?: ObjectManager::getInstance()->get(ActiveEditor::class);
            }
        } catch (\Exception $e) {
        }

        $this->request = $request ?: ObjectManager::getInstance()->get(\Magento\Framework\App\RequestInterface::class);
    }

    /**
     * Enable variables & widgets on product edit page
     *
     * @param ConfigInterface $configInterface
     * @param array $data
     * @return array
     */
    public function beforeGetConfig(
        ConfigInterface $configInterface,
        $data = []
    ) {
        if (!$this->activeEditor) {
            return [$data];
        }

        if ($this->request->getFullActionName() === 'catalog_product_edit') {
            $data['add_variables'] = true;
            $data['add_widgets'] = true;
        }

        return [$data];
    }

    /**
     * Return WYSIWYG configuration
     *
     * @param ConfigInterface $configInterface
     * @param DataObject $result
     * @return DataObject
     */
    public function afterGetConfig(
        ConfigInterface $configInterface,
        DataObject $result
    ) {
        if (!$this->activeEditor) {
            return $result;
        }

        // Get current wysiwyg adapter's path
        $editor = $this->activeEditor->getWysiwygAdapterPath();

        if (strpos($editor, 'tinymce4Adapter')) {
            $plugins = $result->getData('plugins');

            if (isset($plugins[0]) && $plugins[0]['name'] == 'image') {
                $plugins[0]['src'] = $this->assetRepo->getUrl('MageBig_MbFrame::js/tiny_mce_4/plugins/image/plugin.min.js');
                $plugins[] = [
                    'name' => 'imagetools',
                    'src' => $this->assetRepo->getUrl('MageBig_MbFrame::js/tiny_mce_4/plugins/imagetools/plugin.min.js')
                ];
                $result->setData('plugins', $plugins);

                $settings = $result->getData('settings');
                $mainCss = $result->getData('tinymce4')['content_css'];
                if (!is_array($mainCss)) {
                    $mainCss = explode(',', $mainCss);
                }
                $mainCss[] = $this->assetRepo->getUrl('MageBig_MbFrame::css/tiny_mce/content.min.css');
                $settings['content_css'] = $mainCss;

                $result->setData('settings', $settings);
            }
        } elseif (strpos($editor, 'tinymce5Adapter')) {
            $plugins = $result->getData('plugins');

            if (isset($plugins[0]) && $plugins[0]['name'] == 'image') {
                $plugins[0]['src'] = $this->assetRepo->getUrl('MageBig_MbFrame::js/tiny_mce_5/plugins/image/plugin.js');
                $result->setData('plugins', $plugins);

                $settings = $result->getData('settings');
                $mainCss = $result->getData('tinymce')['content_css'];

                if (!is_array($mainCss)) {
                    $mainCss = explode(',', $mainCss);
                }

                $mainCss[] = $this->assetRepo->getUrl('MageBig_MbFrame::css/tiny_mce/content.min.css');
                $settings['content_css'] = $mainCss;

                // Disable warning TinyMce 6
                $settings['deprecation_warnings'] = false;

                $result->setData('settings', $settings);
            }
        }

        return $result;
    }
}
