<?php

return [
    'ctrl' => [
        'title' => 'InlineItemsHelper fixture parent',
        'label' => 'uid',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'delete' => 'deleted',
        'sortby' => 'sorting',
        'versioningWS' => true,
        'languageField' => 'sys_language_uid',
        'transOrigPointerField' => 'l10n_parent',
        'transOrigDiffSourceField' => 'l10n_diffsource',
        'translationSource' => 'l10n_source',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'security' => [
            'ignorePageTypeRestriction' => true,
        ],
    ],
    'columns' => [
        'inline_2' => [
            'label' => 'Inline items',
            'config' => [
                'type' => 'inline',
                'foreign_table' => 'tx_inlineitemshelperfixture_child',
                'foreign_field' => 'parentid',
                'foreign_table_field' => 'parenttable',
            ],
        ],
    ],
    'types' => [
        '0' => [
            'showitem' => 'inline_2',
        ],
    ],
];
