<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
declare(strict_types=1);

return [
    'entity_without_required_name' => [
        '<?xml version="1.0"?><config><entity  label="test" model="test" behaviorModel="test" /></config>',
        [
            "Element 'entity': The attribute 'name' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity label=\"test\" model=\"test\" " .
            "behaviorModel=\"test\"/></config>\n2:\n"
        ],
    ],
    'entity_without_required_label' => [
        '<?xml version="1.0"?><config><entity name="test_name" model="test" behaviorModel="test" /></config>',
        [
            "Element 'entity': The attribute 'label' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity name=\"test_name\" model=\"test\" " .
            "behaviorModel=\"test\"/></config>\n2:\n"
        ],
    ],
    'entity_without_required_behaviormodel' => [
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" model="test" /></config>',
        [
            "Element 'entity': The attribute 'behaviorModel' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity name=\"test_name\" label=\"test_label\" " .
            "model=\"test\"/></config>\n2:\n"
        ],
    ],
    'entity_without_required_model' => [
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" behaviorModel="test" /></config>',
        [
            "Element 'entity': The attribute 'model' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity name=\"test_name\" label=\"test_label\" " .
            "behaviorModel=\"test\"/></config>\n2:\n"
        ],
    ],
    'entity_with_notallowed_atrribute' => [
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" ' .
        'model="test" behaviorModel="test" notallowed="text" /></config>',
        [
            "Element 'entity', attribute 'notallowed': The attribute 'notallowed' is not allowed.\nLine: 1\n" .
            "The xml was: \n0:<?xml version=\"1.0\"?>\n1:<config><entity name=\"test_name\" label=\"test_label\" " .
            "model=\"test\" behaviorModel=\"test\" notallowed=\"text\"/></config>\n2:\n"
        ],
    ],
    'entity_model_with_invalid_value' => [
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" model="34afwer" ' .
        'behaviorModel="test" /></config>',
        [
            "Element 'entity', attribute 'model': [facet 'pattern'] The value '34afwer'" .
            " is not accepted by the pattern '([\\\\]?[a-zA-Z_][a-zA-Z0-9_]*)+'.\n" .
            "Line: 1\nThe xml was: \n0:<?xml version=\"1.0\"?>\n1:<config><entity name=\"test_name\" " .
            "label=\"test_label\" model=\"34afwer\" behaviorModel=\"test\"/></config>\n2:\n"
        ],
    ],
    'entity_behaviorModel_with_invalid_value' => [
        '<?xml version="1.0"?><config><entity name="test_name" label="test_label" model="test" behaviorModel="666" />' .
        '</config>',
        [
            "Element 'entity', attribute 'behaviorModel': [facet 'pattern'] The value '666'" .
            " is not accepted by the pattern '([\\\\]?[a-zA-Z_][a-zA-Z0-9_]*)+'.\n" .
            "Line: 1\nThe xml was: \n0:<?xml version=\"1.0\"?>\n1:<config><entity " .
            "name=\"test_name\" label=\"test_label\" model=\"test\" behaviorModel=\"666\"/></config>\n2:\n"
        ],
    ]
];
