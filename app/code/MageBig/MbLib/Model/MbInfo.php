<?php

namespace MageBig\MbLib\Model;

use Magento\Framework\App\ProductMetadataInterface;
use Magento\Framework\HTTP\Client\Curl;
use Magento\Store\Model\StoreManagerInterface;

class MbInfo
{
    /**
     * @var ProductMetadataInterface
     */
    private $metadata;

    /**
     * @var StoreManagerInterface
     */
    private $storeManager;

    /**
     * @var Curl $curl
     */
    private $curl;

    /**
     * @param ProductMetadataInterface $metadata
     * @param StoreManagerInterface $storeManager
     * @param Curl $curl
     */
    public function __construct(
        ProductMetadataInterface $metadata,
        StoreManagerInterface $storeManager,
        Curl $curl
    ) {
        $this->metadata = $metadata;
        $this->storeManager = $storeManager;
        $this->curl = $curl;
    }

    /**
     * Load data
     *
     * @param array $data
     * @return false|mixed
     */
    public function load(array $data)
    {
        try {
            $Hv16 = "\141\x70\160\154\151\x63\x61\164\151\x6f\156\x2f\152\163\157\x6e";
            $zWRE = ["\x43\x6f\x6e\x74\145\x6e\x74\x2d\x54\x79\160\x65" => $Hv16];
            $this->curl->setHeaders($zWRE);
            $data["\x65\144\151\x74\151\157\x6e"] = $this->metadata->getEdition();
            $Bw2c = "\150\164\x74\x70\163\x3a\x2f\x2f\x77\167\x77\56\x6d\141\147\145\x62\x69\147" .
                "\x2e\143\x6f\155\57\x72\145\163\164\x2f\x64\145\x66\141\165\154\164" .
                "\57\x56\x31\x2f\155\x61\x67\x65\x62\151\x67\57\x6c\151\x63\x65\156\x73\145";
            $qLlA = json_encode($data);
            $this->curl->post($Bw2c, $qLlA);
            $pwBG = $this->curl->getBody();
            return json_decode($pwBG, true);
        } catch (\Exception $e) {
            return false;
        }
    }
}
