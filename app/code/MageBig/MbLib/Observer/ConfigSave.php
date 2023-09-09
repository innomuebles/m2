<?php

namespace MageBig\MbLib\Observer;

use MageBig\MbLib\Model\MbInfo;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Message\ManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class ConfigSave implements ObserverInterface
{
    /**
     * @var MbInfo
     */
    private $mbInfo;

    /**
     * @var ManagerInterface
     */
    private $messageManager;

    /**
     * @var RedirectFactory
     */
    protected $resultRedirectFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var ScopeConfigInterface
     */
    protected $_scopeConfig;

    /**
     * @var WriterInterface
     */
    protected $configWriter;

    /**
     * @var ObjectManager
     */
    protected $_objectManager;

    /**
     * @param MbInfo $mbInfo
     * @param ManagerInterface $messageManager
     * @param RedirectFactory $resultRedirectFactory
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     */
    public function __construct(
        MbInfo $mbInfo,
        ManagerInterface $messageManager,
        RedirectFactory $resultRedirectFactory,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter
    ) {
        $this->mbInfo = $mbInfo;
        $this->messageManager = $messageManager;
        $this->resultRedirectFactory = $resultRedirectFactory;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
        $this->_objectManager = ObjectManager::getInstance();
    }

    /**
     * Get module
     *
     * @param string $name
     * @param string $path
     * @return false|string
     */
    private function getModule(string $name, string $path)
    {
        $module = (string)$this->getConfig($name, $path);

        return $module ?: false;
    }

    /**
     * Get config
     *
     * @param string $name
     * @param string $path
     * @param string $scope
     * @param int|null $storeId
     * @return mixed
     */
    private function getConfig(
        string $name,
        string $path,
        string $scope = ScopeConfigInterface::SCOPE_TYPE_DEFAULT,
        int $storeId = null
    ) {
        return $this->_scopeConfig->getValue(
            $this->getPath($name, $path),
            $scope,
            $storeId
        );
    }

    /**
     * Get path
     *
     * @param string $name
     * @param string $field
     * @return string
     */
    private function getPath(string $name, string $field)
    {
        return implode('/', [$name, 'general', $field]);
    }

    /**
     * Show infor
     *
     * @param Http $a33
     * @param array $X4X
     * @param int $BCy
     * @return bool
     */
    private function showInfo(Http $a33, array $X4X, int $BCy) : bool
    {
        $q__ = "\151\x73\x5f\x61\143\x74\151\166\145\137\155\157\x64\x75\x6c\145";
        $Z2w = "\x6c\x69\143\145\156\163\x65";
        $uis = "\x66\151\x65\154\144\x73";
        $M6h = "\166\x61\x6c\x75\x65";
        $gix = $a33->getParam("\x73\164\157\x72\x65");
        $tZk = $a33->getParam("\147\x72\157\x75\x70\x73");
        $A9x = $a33->getParam("\x73\x65\143\164\151\157\156");
        $rSF = $this->getPath($A9x, $q__);
        $BM0 = $this->getPath($A9x, $Z2w);
        $lmN = 0;
        if ($BCy == 1) {
            $dDD = "\x50\x6c\x65\141\x73\145\x20\x61\x63\x74\x69\x76\x61\164\145" .
                "\40\171\157\165\162\40\144\157\x6d\x61\x69\x6e\40\156\x61\x6d\x65" .
                "\40\151\156\40\x79\157\165\x72\40\141\143\143\157\165\x6e\x74\x20\x66\x69\162\x73\164\56";
        } else {
            $dDD = "\x54\150\145\x20\x6c\x69\143\145\x6e\x73\x65" .
                "\40\153\x65\171\x20\x69\163\x20\151\x6e\x76\141\x6c\151\144\56";
        }
        if (!$gix) {
            $this->configWriter->save($rSF, $lmN);
            $this->configWriter->save($BM0, null);
        }
        foreach ($X4X as $dqR) {
            $this->configWriter->save($rSF, $lmN, "\x73\x74\x6f\162\x65\163", $dqR);
            $this->configWriter->save($BM0, null, "\163\164\x6f\162\145\163", $dqR);
            QnB:
        }
        fBy:
        $tZk["\x67\145\156\145\x72\141\154"][$uis][$q__][$M6h] = $lmN;
        $tZk["\147\145\x6e\x65\162\141\x6c"][$uis][$Z2w][$M6h] = '';
        $a33->setPostValue("\147\x72\x6f\x75\160\x73", $tZk);
        $this->messageManager->addErrorMessage($dDD);
        return false;
    }

    /**
     * Execute Observer
     *
     * @param Observer $observer
     * @return false|void
     */
    public function execute(Observer $observer)
    {
        $L0x = $observer->getEvent()->getRequest();
        $d7r = "\x73\x74\157\x72\145\163";
        $TF2 = "\x69\163\x5f\x61\x63\x74\151\x76" . "\145\137\155\157\x64\165\x6c\x65";
        $yvt = $L0x->getParam("\147\x72\157\165\x70\163");
        $pGZ = $L0x->getParam("\163\145\x63\x74\x69\x6f\156");
        $hFg = $this->getModule($pGZ, "\x6d\x62\x65\170\x74");
        if (!$hFg) {
            return false;
        }
        $bk7 = $yvt["\147\145\x6e\x65\x72\141\x6c"]["\146\x69\x65\154\x64\x73"];
        $sr2 = $bk7[$TF2]["\166\141\x6c\165\x65"];
        $Y10 = $L0x->getParam("\163\164\157\x72\145");
        $UUA = $this->_storeManager->getStores();
        $MU8 = $bk7["\154\151\143\x65\156\x73\x65"]["\166\x61\154\x75\145"];
        $q_Y = [];
        $Hm7 = [];
        $ljk = 0;
        $U7M = "\x77\145\142\57\x73\x65\143\x75\x72\x65" . "\57\x62\141\x73\145\137\154\x69\156\153\x5f\165\162\154";
        foreach ($UUA as $VAF) {
            if ($VAF->getIsActive()) {
                $OVo = $this->_scopeConfig->getValue($U7M, "\163\164\x6f\162\145", $VAF->getCode());
                if ($OVo) {
                    $QrK = [];
                    $fUZ = "\x2f\x5e\x28\x2e\52\72\x29\x5c\x2f\134\x2f\x28\x5b\101\55\x5a\141\x2d\172" .
                    "\60\x2d\x39\x5c\x2d\x5c\56\135\x2b\x29\50\72\x5b\60\x2d\71\135\53\51\77\x28\x2e\52\x29\x24\x2f";
                    preg_match($fUZ, $OVo, $QrK);
                    $noP = $QrK[2];
                    $q_Y[$ljk]["\x75\x72\x6c"] = $noP;
                    $q_Y[$ljk]["\143\157\x64\x65"] = $VAF->getId();
                    $Hm7[] = $VAF->getId();
                    $ljk++;
                }
            }
            JOz:
        }
        vQn:
        $DZi = $this->getPath($pGZ, $TF2);
        $BjN = $this->getPath($pGZ, "\154\x69\143\x65\x6e\163\x65");
        if ($sr2 == 0) {
            foreach ($Hm7 as $jSV) {
                $this->configWriter->save($DZi, 0, $d7r, $jSV);
                qnH:
            }
            vjt:
            return false;
        }
        $wjm["\154\x69\x63\x65\156\163\145\113\145\171"] = $MU8;
        $wjm["\x64\x6f\x6d\x61\151\x6e\163"] = json_encode($q_Y);
        $L3E = $this->mbInfo->load($wjm);
        if (!$L3E) {
            return $this->showInfo($L0x, $Hm7, 0);
        }
        if ($L3E[0] === 0) {
            return $this->showInfo($L0x, $Hm7, 1);
        }
        $Ak5 = 0;
        $Qwv = count($L3E);
        $A9B = $wjm;
        foreach ($L3E as $FpJ) {
            $N53 = $FpJ["\143\157\x64\x65"];
            $rJW = $FpJ["\165\162\x6c"];
            if ($Y10 && $N53 == $Y10 && $FpJ["\x69\x73\137\x61\x63\164\x69\166\145"]) {
                return false;
            }
            if ($Y10 && $N53 == $Y10 && !$FpJ["\x69\163\137\141\143\x74\x69\166\x65"]) {
                return $this->showInfo($L0x, $Hm7, 0);
            }
            $mxe = $FpJ["\151\x73\x5f\x61\x63\x74\151\x76\x65"];
            $yv7 = $this->getConfig($pGZ, "\154\151\143\x65\x6e\163\x65", $d7r, $N53);
            if ($yv7 && $yv7 != $MU8) {
                $A9B["\154\x69\143\x65\x6e\163\x65\x4b\x65\171"] = $yv7;
                $A9B["\x64\x6f\155\141\151\x6e\x73"] = json_encode([
                    ["\143\x6f\x64\x65" => $N53, "\165\x72\x6c" => $rJW]
                ]);
                $DNe = $this->mbInfo->load($A9B);
                if ($DNe) {
                    $mxe = $DNe[0]["\151\163\137\x61\143\x74\x69\166\145"];
                    $MU8 = $yv7;
                }
            }
            $abN = $mxe ? 1 : 0;
            $bmC = $mxe ? $MU8 : null;
            $this->configWriter->save($DZi, $abN, $d7r, $N53);
            $this->configWriter->save($BjN, $bmC, $d7r, $N53);
            if (!$FpJ["\x69\x73\x5f\x61\x63\164\x69\166\145"]) {
                $Ak5++;
            }
            EKq:
        }
        ucp:
        if (!$Y10 && $Ak5 == $Qwv) {
            $this->showInfo($L0x, $Hm7, 1);
        }
    }
}
