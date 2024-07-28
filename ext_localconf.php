<?php

defined('TYPO3') or die();

$GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] = array_merge($GLOBALS['TYPO3_CONF_VARS']['FE']['ContentObjects'] ?? [], [
    'WEBCOMPONENT' => \Sinso\Webcomponents\ContentObject\WebcomponentContentObject::class,
]);
