<?php
/**
 * Copyright 2015 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

return [
    'fileFormat_node_with_required_attribute' => [
        '<?xml version="1.0"?><config><fileFormat label="name_one" model="model"/><fileFormat name="name_one" ' .
        'model="model"/><fileFormat name="name" label="model"/></config>',
        [
            "Element 'fileFormat': The attribute 'name' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><fileFormat label=\"name_one\" model=\"model\"/><fileFormat " .
            "name=\"name_one\" model=\"model\"/><fileFormat name=\"name\" label=\"model\"/></config>\n2:\n",
            "Element 'fileFormat': The attribute 'label' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><fileFormat label=\"name_one\" model=\"model\"/><fileFormat " .
            "name=\"name_one\" model=\"model\"/><fileFormat name=\"name\" label=\"model\"/></config>\n2:\n",
            "Element 'fileFormat': The attribute 'model' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><fileFormat label=\"name_one\" model=\"model\"/><fileFormat " .
            "name=\"name_one\" model=\"model\"/><fileFormat name=\"name\" label=\"model\"/></config>\n2:\n"
        ],
    ],
    'entity_node_with_required_attribute' => [
        '<?xml version="1.0"?><config><entity label="name_one" model="model" entityAttributeFilterType="name_one"/>' .
        '<entity name="name_one" model="model" entityAttributeFilterType="name_two"/>' .
        '<entity label="name" name="model" entityAttributeFilterType="name_three"/>' .
        '<entity label="name" name="model_two" model="model"/></config>',
        [
            "Element 'entity': The attribute 'name' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity label=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_one\"/><entity name=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_two\"/><entity label=\"name\" name=\"model\" " .
            "entityAttributeFilterType=\"name_three\"/><entity label=\"name\" name=\"model_two\" " .
            "model=\"model\"/></config>\n2:\n",
            "Element 'entity': The attribute 'label' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity label=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_one\"/><entity name=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_two\"/><entity label=\"name\" name=\"model\" " .
            "entityAttributeFilterType=\"name_three\"/><entity label=\"name\" name=\"model_two\" " .
            "model=\"model\"/></config>\n2:\n",
            "Element 'entity': The attribute 'model' is required but missing.\nLine: 1\nThe xml was: \n" .
            "0:<?xml version=\"1.0\"?>\n1:<config><entity label=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_one\"/><entity name=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_two\"/><entity label=\"name\" name=\"model\" " .
            "entityAttributeFilterType=\"name_three\"/><entity label=\"name\" name=\"model_two\" " .
            "model=\"model\"/></config>\n2:\n",
            "Element 'entity': The attribute 'entityAttributeFilterType' is required but missing.\nLine: 1\n" .
            "The xml was: \n0:<?xml version=\"1.0\"?>\n1:<config><entity label=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_one\"/><entity name=\"name_one\" model=\"model\" " .
            "entityAttributeFilterType=\"name_two\"/><entity label=\"name\" name=\"model\" " .
            "entityAttributeFilterType=\"name_three\"/><entity label=\"name\" name=\"model_two\" " .
            "model=\"model\"/></config>\n2:\n"
        ],
    ]
];
