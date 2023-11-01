<?php
/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Magento\Setup\Controller;

use Magento\Framework\App\SetupInfo;
use Magento\Framework\Config\ConfigOptionsListConstants;
use Laminas\Mvc\Controller\AbstractActionController;
use Laminas\View\Model\ViewModel;

class Elasticsearch extends AbstractActionController
{
    /**
     * Displays web configuration form
     *
     * @return array|ViewModel
     */
    public function indexAction()
    {
        $view = new ViewModel();
        $view = new ViewModel(
            [
                'nosqldb'   => [
                    ConfigOptionsListConstants::NOSQL_DB_OPENSEARCH,
                    ConfigOptionsListConstants::NOSQL_DB_ELASTICSEARCH,
                ],
            ]
        );

        $view->setTerminal(true);
        return $view;
    }
}
