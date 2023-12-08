/**
 * Copyright © Magento, Inc. All rights reserved.
 * See COPYING.txt for license details.
 */
'use strict';
angular.module('elasticsearch', ['ngStorage'])
    .controller(
        'elasticsearchController',
        ['$scope', '$state', '$localStorage', '$http',
            function ($scope, $state, $localStorage, $http) {
                $scope.es = {
                    hostname: 'localhost',
                    nosqldb: 'opensearch',
                    port: '9200',
                    prefix: 'mageos',
                    timeout: 15,
                    enableAuth: false,
                    username: '',
                    password: ''
                };

                if ($localStorage.es) {
                    $scope.es = $localStorage.es;
                }

                $scope.testEsConnection = function () {
                    $http.post('index.php/elasticsearch-check', $scope.es)
                        .then(function successCallback(resp) {
                            $scope.testEsConnection.result = resp.data;

                            if ($scope.testEsConnection.result.success) {
                                $scope.nextState();
                            }
                        }, function errorCallback(resp) {
                            $scope.testEsConnection.failed = resp.data;
                        });
                };

                $scope.$on('nextState', function () {
                    $localStorage.es = $scope.es;
                });

                $scope.$on('validate-' + $state.current.id, function () {
                    $scope.validate();
                });

                $scope.useBasicAuth = function() {
                    return ($scope.es.enableAuth);
                };

                // Dispatch 'validation-response' event to parent controller
                $scope.validate = function () {
                    if ($scope.elasticsearch.$valid) {
                        $scope.$emit('validation-response', true);
                    } else {
                        $scope.$emit('validation-response', false);
                        $scope.elasticsearch.submitted = true;
                    }
                }

                // Update 'submitted' flag
                $scope.$watch(function() { return $scope.elasticsearch.$valid }, function(valid) {
                    if (valid) {
                        $scope.elasticsearch.submitted = false;
                    }
                });
            }
        ]
    );
