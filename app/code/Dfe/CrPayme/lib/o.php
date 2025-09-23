<?php
use Dfe\CrPayme\Model\Session as S;

/**
 * 2025-09-23 Dmitrii Fediuk https://upwork.com/fl/mage2pro
 * "Refactor the `Alignet_Paymecheckout` module": https://github.com/innomuebles/m2/issues/10
 */
function dfe_cr_payme_sess() {return df_o(S::class);}