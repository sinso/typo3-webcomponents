<?php

$config = \TYPO3\CodingStandards\CsFixerConfig::create();
$config->addRules([
    'binary_operator_spaces' => true,
    'ordered_traits' => true,
]);
$config->getFinder()->in(__DIR__ . '/../Classes');
return $config;
