<?php

defined('TYPO3_MODE') || die();

$GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'], [
    'WEBCOMPONENT' => \Sinso\Webcomponents\ContentObject\WebComponentContentObject::class,
]);
