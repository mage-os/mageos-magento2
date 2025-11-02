<?php
/**
 * PHPUnit 12 Migration Rules Template
 * 
 * This file contains automated rules and patterns for migrating Magento tests
 * from older PHPUnit versions to PHPUnit 12.
 */

namespace Magento\Test\Migration\PHPUnit12;

class MigrationRules
{
    /**
     * Rule: Replace addMethods() with Mock Classes
     * 
     * Pattern: $this->getMockBuilder(Interface::class)->addMethods(['method'])
     * Solution: Create Mock class implementing interface with the method
     */
    public const ADD_METHODS_RULE = [
        'pattern' => '/->addMethods\(\[([^\]]+)\]\)/',
        'solution' => 'create_mock_class',
        'description' => 'Replace addMethods() calls with dedicated Mock classes'
    ];

    /**
     * Rule: Fix willReturnSelf() with parameters
     * 
     * Pattern: ->willReturnSelf('value')
     * Solution: ->willReturn('value')
     */
    public const WILL_RETURN_SELF_RULE = [
        'pattern' => '/->willReturnSelf\(([^)]+)\)/',
        'solution' => 'replace_with_will_return',
        'description' => 'Replace willReturnSelf() with willReturn() when returning values'
    ];

    /**
     * Rule: Anonymous classes in parent test classes
     * 
     * Pattern: new class implements Interface { ... }
     * Solution: Create dedicated Mock class
     */
    public const ANONYMOUS_CLASS_RULE = [
        'pattern' => '/new class implements [^{]+{/',
        'solution' => 'create_mock_class',
        'description' => 'Replace anonymous classes with dedicated Mock classes'
    ];

    /**
     * Rule: AbstractBlock with addMethods for form field methods
     * 
     * Pattern: ->addMethods(['addFieldMap', 'addFieldDependence'])
     * Solution: Create AbstractBlockMock extending AbstractBlock
     */
    public const ABSTRACT_BLOCK_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]addFieldMap[\'"],\s*[\'"]addFieldDependence[\'"]\]\)/',
        'solution' => 'create_abstract_block_mock',
        'description' => 'Replace AbstractBlock addMethods with AbstractBlockMock'
    ];

    /**
     * Rule: Collection with addMethods for filtering methods
     * 
     * Pattern: ->addMethods(['addIdFilter'])
     * Solution: Create CollectionMock extending Collection
     */
    public const COLLECTION_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]addIdFilter[\'"]\]\)/',
        'solution' => 'create_collection_mock',
        'description' => 'Replace Collection addMethods with CollectionMock'
    ];

    /**
     * Rule: CategoryInterface with addMethods for category-specific methods
     * 
     * Pattern: ->addMethods(['getIsAnchor'])
     * Solution: Create CategoryInterfaceMock implementing CategoryInterface
     */
    public const CATEGORY_INTERFACE_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]getIsAnchor[\'"]\]\)/',
        'solution' => 'create_category_interface_mock',
        'description' => 'Replace CategoryInterface addMethods with CategoryInterfaceMock'
    ];

    /**
     * Rule: QueryInterface with addMethods for query-specific methods
     * 
     * Pattern: ->addMethods(['getMust', 'getShould'])
     * Solution: Create QueryInterfaceMock implementing QueryInterface
     */
    public const QUERY_INTERFACE_RULE = [
                'pattern' => '/->addMethods\(\[[\'"]getMust[\'"],\s*[\'"]getShould[\'"]\]\)/',
        'solution' => 'create_query_interface_mock',
        'description' => 'Replace QueryInterface addMethods with QueryInterfaceMock'
    ];
    /**
     * Rule: IndexerInterface with addMethods for magic methods
     * 
     * Pattern: ->addMethods(['__wakeup'])
     * Solution: Create IndexerInterfaceMock implementing IndexerInterface
     */
    public const INDEXER_INTERFACE_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]__wakeup[\'"]\]\)/',
        'solution' => 'create_indexer_interface_mock',
        'description' => 'Replace IndexerInterface addMethods with IndexerInterfaceMock'
    ];

    /**
     * Rule: SearchCriteriaBuilder with addMethods for request methods
     * 
     * Pattern: ->addMethods(['setRequestName'])
     * Solution: Create SearchCriteriaBuilderMock extending SearchCriteriaBuilder
     */
    public const SEARCH_CRITERIA_BUILDER_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]setRequestName[\'"]\]\)/',
        'solution' => 'create_search_criteria_builder_mock',
        'description' => 'Replace SearchCriteriaBuilder addMethods with SearchCriteriaBuilderMock'
    ];

    /**
     * Rule: AbstractCollection with addMethods for collection methods
     * 
     * Pattern: ->addMethods(['addIdFilter'])
     * Solution: Create AbstractCollectionMock extending AbstractCollection
     */
    public const ABSTRACT_COLLECTION_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]addIdFilter[\'"]\]\)/',
        'solution' => 'create_abstract_collection_mock',
        'description' => 'Replace AbstractCollection addMethods with AbstractCollectionMock'
    ];

    /**
     * Rule: Category with addMethods for URL methods
     * 
     * Pattern: ->addMethods(['setUrlPath', 'unsUrlPath', 'setUrlKey'])
     * Solution: Create CategoryMock extending Category
     */
    public const CATEGORY_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]setUrlPath[\'"],\s*[\'"]unsUrlPath[\'"],\s*[\'"]setUrlKey[\'"]\]\)/',
        'solution' => 'create_category_mock',
        'description' => 'Replace Category addMethods with CategoryMock'
    ];

    /**
     * Rule: AbstractModel with addMethods for model methods
     * 
     * Pattern: ->addMethods(['getStoreIds', 'getWebsiteId'])
     * Solution: Create AbstractModelMock extending AbstractModel
     */
    public const ABSTRACT_MODEL_RULE = [
        'pattern' => '/->addMethods\(\[[\'"]getStoreIds[\'"],\s*[\'"]getWebsiteId[\'"]\]\)/',
        'solution' => 'create_abstract_model_mock',
        'description' => 'Replace AbstractModel addMethods with AbstractModelMock'
    ];

    /**
     * Rule: Create Mock classes ONLY for addMethods() scenarios
     * 
     * Pattern: ->addMethods([...]) - methods that don't exist in original class
     * Solution: Create Mock class extending/implementing original class with additional methods
     * 
     * DO NOT create Mock classes for:
     * - onlyMethods() - these mock existing methods
     * - createMock() - these work with existing methods
     */
    public const MOCK_CLASS_CREATION_RULE = [
        'pattern' => '/->addMethods\(/',
        'solution' => 'create_mock_class_for_addmethods',
        'description' => 'Create Mock class ONLY when addMethods() is used to add non-existent methods'
    ];

    /**
     * Common Mock classes that need to be created
     */
    public const REQUIRED_MOCK_CLASSES = [
        'RequestInterfaceMock' => [
            'interface' => 'Magento\Framework\App\RequestInterface',
            'additional_methods' => ['getPostValue', 'has'],
            'location' => 'app/code/Magento/Catalog/Test/Unit/Mock/RequestInterfaceMock.php'
        ],
        'LayoutInterfaceMock' => [
            'interface' => 'Magento\Framework\View\LayoutInterface',
            'additional_methods' => ['initMessages', 'getMessagesBlock'],
            'location' => 'app/code/Magento/Catalog/Test/Unit/Mock/LayoutInterfaceMock.php'
        ],
        'ProductAttributeInterfaceMock' => [
            'interface' => 'Magento\Catalog\Api\Data\ProductAttributeInterface',
            'additional_methods' => ['getBackendTypeByInput', 'getDefaultValueByInput', 'addData'],
            'location' => 'app/code/Magento/Catalog/Test/Unit/Mock/ProductAttributeInterfaceMock.php'
        ],
        'ResultRedirectMock' => [
            'interface' => 'Magento\Backend\Model\View\Result\Redirect',
            'additional_methods' => ['setData'],
            'location' => 'app/code/Magento/Catalog/Test/Unit/Mock/ResultRedirectMock.php'
        ],
        'FilterManagerMock' => [
            'interface' => 'Magento\Framework\Filter\FilterManager',
            'additional_methods' => ['stripTags'],
            'location' => 'app/code/Magento/Catalog/Test/Unit/Mock/FilterManagerMock.php'
        ],
        'AbstractBlockMock' => [
            'interface' => 'Magento\Framework\View\Element\AbstractBlock',
            'additional_methods' => ['addFieldMap', 'addFieldDependence'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/AbstractBlockMock.php'
        ],
        'LayoutMock' => [
            'interface' => 'Magento\Framework\View\Result\Layout',
            'additional_methods' => ['getUpdate'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/LayoutMock.php'
        ],
        'RequestMock' => [
            'interface' => 'Magento\Framework\App\Console\Request',
            'additional_methods' => ['getQueryValue'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/RequestMock.php'
        ],
        'CollectionMock' => [
            'interface' => 'Magento\Framework\Data\Collection',
            'additional_methods' => ['addIdFilter'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/CollectionMock.php'
        ],
        'CategoryInterfaceMock' => [
            'interface' => 'Magento\Catalog\Api\Data\CategoryInterface',
            'additional_methods' => ['getIsAnchor'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/CategoryInterfaceMock.php'
        ],
        'QueryInterfaceMock' => [
            'interface' => 'Magento\Framework\Search\Request\QueryInterface',
            'additional_methods' => ['getMust', 'getShould'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/QueryInterfaceMock.php'
        ],
        'IndexerInterfaceMock' => [
            'interface' => 'Magento\Framework\Indexer\IndexerInterface',
            'additional_methods' => ['__wakeup'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/IndexerInterfaceMock.php'
        ],
        'SearchCriteriaBuilderMock' => [
            'class' => 'Magento\Framework\Api\Search\SearchCriteriaBuilder',
            'additional_methods' => ['setRequestName'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/SearchCriteriaBuilderMock.php'
        ],
        'AttributeResourceModelMock' => [
            'class' => 'Magento\Catalog\Model\ResourceModel\Eav\Attribute',
            'additional_methods' => ['getSearchWeight'],
            'location' => 'app/code/Magento/CatalogSearch/Test/Unit/Mock/AttributeResourceModelMock.php'
        ],
        'AbstractCollectionMock' => [
            'class' => 'Magento\Catalog\Model\ResourceModel\Collection\AbstractCollection',
            'additional_methods' => ['addIdFilter'],
            'location' => 'app/code/Magento/CatalogUrlRewrite/Test/Unit/Mock/AbstractCollectionMock.php'
        ],
        'CategoryMock' => [
            'class' => 'Magento\Catalog\Model\Category',
            'additional_methods' => ['setUrlPath', 'unsUrlPath', 'setUrlKey'],
            'location' => 'app/code/Magento/CatalogUrlRewrite/Test/Unit/Mock/CategoryMock.php'
        ],
        'AbstractModelMock' => [
            'class' => 'Magento\Framework\Model\AbstractModel',
            'additional_methods' => ['getStoreIds', 'getWebsiteId'],
            'location' => 'app/code/Magento/CatalogUrlRewrite/Test/Unit/Mock/AbstractModelMock.php'
        ]
    ];

    /**
     * Migration steps for each test file
     */
    public static function getMigrationSteps(): array
    {
        return [
            'step1' => [
                'action' => 'scan_for_addmethods',
                'command' => 'grep -r "addMethods" app/code/Magento/Catalog/Test/Unit/',
                'description' => 'Find all files using addMethods()'
            ],
            'step2' => [
                'action' => 'create_mock_classes',
                'description' => 'Create Mock classes for interfaces/classes using addMethods()'
            ],
            'step3' => [
                'action' => 'update_test_files',
                'description' => 'Replace addMethods() with onlyMethods() and Mock classes'
            ],
            'step4' => [
                'action' => 'fix_test_assertions',
                'description' => 'Fix willReturnSelf() and other assertion issues'
            ],
            'step5' => [
                'action' => 'run_tests',
                'command' => 'php vendor/bin/phpunit [test-file] --testdox',
                'description' => 'Verify all tests pass'
            ]
        ];
    }

    /**
     * Template for creating Mock classes
     */
    public static function getMockClassTemplate(): string
    {
        return '<?php
/**
 * Copyright 2016 Adobe
 * All Rights Reserved.
 */
declare(strict_types=1);

namespace Magento\Catalog\Test\Unit\Mock;

use [INTERFACE_CLASS];

/**
 * Mock class for [INTERFACE_NAME] with additional methods
 */
class [MOCK_CLASS_NAME] implements [INTERFACE_NAME]
{
    /**
     * Mock method for [ADDITIONAL_METHOD]
     *
     * @param [PARAM_TYPE] $param
     * @return [RETURN_TYPE]
     */
    public function [ADDITIONAL_METHOD]([PARAM_DEFINITION]): [RETURN_TYPE]
    {
        return [DEFAULT_VALUE];
    }

    // Required methods from [INTERFACE_NAME]
    [INTERFACE_METHODS]
}';
    }

    /**
     * Common fixes for test assertions
     */
    public static function getCommonFixes(): array
    {
        return [
            'willReturnSelf_with_value' => [
                'pattern' => '/->willReturnSelf\(([^)]+)\)/',
                'replacement' => '->willReturn($1)',
                'description' => 'Replace willReturnSelf(value) with willReturn(value)'
            ],
            'expects_method_calls' => [
                'pattern' => '/\$this->[a-zA-Z]+Mock->expects\(\$this->[a-zA-Z]+\(\)\)/',
                'replacement' => 'Use getMockBuilder() with onlyMethods()',
                'description' => 'Ensure mock objects support expects() method'
            ]
        ];
    }

    /**
     * Validation rules for migrated tests
     */
    public static function getValidationRules(): array
    {
        return [
            'no_addmethods' => [
                'check' => 'grep -c "addMethods" [file]',
                'expected' => 0,
                'description' => 'No addMethods() calls should remain'
            ],
            'mock_classes_exist' => [
                'check' => 'ls app/code/Magento/Catalog/Test/Unit/Mock/',
                'description' => 'All required Mock classes should exist'
            ],
            'tests_pass' => [
                'check' => 'php vendor/bin/phpunit [file] --testdox',
                'expected' => 'exit code 0',
                'description' => 'All tests should pass'
            ],
            'no_lint_errors' => [
                'check' => 'php -l [file]',
                'expected' => 'exit code 0',
                'description' => 'No PHP syntax errors'
            ]
        ];
    }
}
