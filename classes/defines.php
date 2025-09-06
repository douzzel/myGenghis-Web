<?php
// * Define devis
define('DEVIS_DRAFT', 0);
define('DEVIS_SAVED', 1);
define('DEVIS_VALIDATED', 30);
define('DEVIS_LOST', 31);
define('DEVIS_AVENANT', 32);

// * Define facture
define('FACT_DELIVERY', 2);
define('FACT_RUNNING', 3);
define('FACT_TERMINATED', 4);
define('FACT_DRAFT', 5);

// * Define CLICK_AND_COLLECT
define('CLICK_WAIT_PAYMENT', 0);
define('CLICK_TO_PREPARE', 1);
define('CLICK_PREPARATION', 2);
define('CLICK_SENT', 3);
define('CLICK_AVAILABLE', 4);
define('CLICK_RETRIEVED', 5);
define('CLICK_RETURN_MANUF_WARANTLY', 20);
define('CLICK_RETURN_PROVID_WARANTLY', 21);
define('CLICK_RETURN_NO_WARANTLY', 22);
define('CLICK_ERROR_STOCK', 40);
define('CLICK_ERROR_PAY', 41);
define('CLICK_ERROR_PRODUCT', 42);
define('CLICK_CANCEL', 60);
define('CLICK_CANCEL_AFTER_EXP', 61);
define('CLICK_CANCEL_NOT_DELIVERED', 62);

// * Define Product TYPE
define('PRODUCT_SELL_PURCHASE', 0);
define('PRODUCT_SELL', 1);
define('PRODUCT_PURCHASE', 2);
