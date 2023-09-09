<?php

namespace MageBig\MbLib\Cron;

use MageBig\MbLib\Model\MbInfo;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Config\Storage\WriterInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Store\Model\ScopeInterface;
use Magento\Store\Model\Store;
use Magento\Store\Model\StoreManagerInterface;

class ModuleData
{
    /**
     * @var MbInfo
     */
    private $mbInfo;

    /**
     * @var ResourceConnection
     */
    private $resource;

    /**
     * @var StoreManagerInterface
     */
    private $_storeManager;

    /**
     * @var WriterInterface
     */
    private $configWriter;

    /**
     * @var ScopeConfigInterface
     */
    private $_scopeConfig;

    /**
     * @param ResourceConnection $resource
     * @param MbInfo $info
     * @param StoreManagerInterface $storeManager
     * @param ScopeConfigInterface $scopeConfig
     * @param WriterInterface $configWriter
     */
    public function __construct(
        ResourceConnection $resource,
        MbInfo $info,
        StoreManagerInterface $storeManager,
        ScopeConfigInterface $scopeConfig,
        WriterInterface $configWriter
    ) {
        $this->resource = $resource;
        $this->mbInfo = $info;
        $this->_storeManager = $storeManager;
        $this->_scopeConfig = $scopeConfig;
        $this->configWriter = $configWriter;
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
     * Execute job
     *
     * @return void
     */
    public function execute()
    {
        $KOX = $this->resource->getConnection();
        $dBB = "\143\157\162\x65\137\143\157\156\x66\151\147\x5f\x64\141\164\141";
        $tae = $this->resource->getTableName($dBB);
        $EvT = "\x67\x65\156" . "\x65\162" . "\x61\154" . "\57" . "\151\163\137" .
            "\141\143\x74\x69\x76\x65\137\x6d\157\144\165\154\145";
        $WwO = $this->_storeManager->getStores();
        $WGb = "\163\164\x6f\162\x65\163";
        $Jm5 = [];
        $ysz = 0;
        foreach ($WwO as $N5J) {
            if ($N5J->getIsActive()) {
                continue;
            }
            $UbS = $this->_scopeConfig->getValue(
                "\167\x65\142\57\163\x65\143\x75\x72\145\x2f\142\141\163\145\x5f\x6c\151\x6e\x6b\x5f\x75\162\x6c",
                "\163\164\x6f\162\x65",
                $N5J->getCode()
            );
            if (!$UbS) {
                continue;
            }
            $h61 = $N5J->getId();
            $ZoU = [];
            $eyh = "\57\x5e\50\56\x2a\72\51\x5c\x2f\134\x2f\x28\x5b\x41\x2d\x5a\x61\x2d\x7a" .
                "\x30\55\71\x5c\x2d\134\56\135\x2b\x29\x28\x3a\133\60\x2d\x39\x5d\53\51\77\x28\56\52\51\x24\x2f";
            preg_match($eyh, $UbS, $ZoU);
            $jWT = $ZoU[2];
            $Jm5[$ysz]["\x75\162\x6c"] = $jWT;
            $Jm5[$ysz]["\143\157\144\145"] = $h61;
            $ysz++;
            $AKM = $KOX->select()
                ->from([$tae])->where("\160\x61\164\150\x20\x4c\x49\113\x45\40\77", "\x25" . $EvT)
                ->where("\x73\143\x6f\160\x65\137\x69\x64\40\75\40\x3f", $h61);
            $ozy = $KOX->fetchAll($AKM);
            foreach ($ozy as $E7d) {
                if (!$E7d["\x76\x61\154\x75\145"]) {
                    continue;
                }
                $KOs = false;
                preg_match(
                    "\57\x28\x2e\x2a\51\134\57" . str_replace("\57", "\x5c\x2f", $EvT) . "\57",
                    $E7d["\x70\141\x74\150"],
                    $KOs
                );
                if (empty($KOs[1])) {
                    continue;
                }
                $NWP = $KOs[1];
                $eMd = $this->getConfig($KOs[1], "\x6c\151\143\x65\156\163\x65", $WGb, $h61);
                $PPz["\x6c\x69\x63\x65\x6e\x73\x65\113\x65\x79"] = $eMd;
                $PPz["\x64\x6f\155\x61\151\156\163"] = json_encode([
                    ["\143\157\144\145" => $h61, "\x75\162\x6c" => $jWT]
                ]);
                $dDR = $this->mbInfo->load($PPz);
                if ($dDR) {
                    $vyf = $dDR[0]["\151\163\x5f\141\x63\164\x69\x76\145"];
                    if (!$vyf) {
                        $fCS = $NWP . "\57" . $EvT;
                        $xmr = $this->getPath($NWP, "\x6c\151\x63\145\156\163\x65");
                        $this->configWriter->save($fCS, 0, $WGb, $h61);
                        $this->configWriter->save($xmr, null, $WGb, $h61);
                    }
                }
            }
        }
    }
}
