/**
 * @ngdoc directive
 * @name mandataire.directives:infosMandataire 
 * 
 * @restrict 'E' 
 *
 * @description
 * Showing mandataire informations
 * 
 *
 * @example
   <example>
        <file name="exemple.html">
            <div class="layout-content">
                <infos-mandataire></infos-mandataire> 
            </div>
        </file>
        <file name="script.js">
            function infosMandataire(mandataireService) {
                return {
                    restrict: 'E',
                    templateUrl: 'app/mandataire/views/directives/infosMandataire.html',
                    scope: {},
                    controller: function ($scope) {

                        function loadInfos() {
                            mandataireService.getMandataireInfos().then(function (data) {
                                $scope.contract = data.contract;
                                $scope.settlements = data.settlements;
                                $scope.corporate = data.corporate;
                            });
                        } 
                        $scope.$on('refreshInfoMandataire', function (event) {
                            loadInfos();
                        });
                    }
                };
            }

            return infosMandataire;
        </file>   
   </example>
 */
define([], function () {
    'use strict';
    infosMandataire.$inject = ['mandataireService'];
 
    function infosMandataire(mandataireService) {
        return {
            restrict: 'E',
            templateUrl: 'app/mandataire/views/directives/infosMandataire.html',
            scope: {},
            controller: function ($scope) {

                /**
                * @ngdoc method
                * @name loadProgramOperations
                * @methodOf mandataire.directives:infosMandataire
                * @description
                * Retrieve details of user's mandataire infos
                *
                * @return {object} Mandataire infos data
                */
                function loadInfos() {
                    mandataireService.getMandataireInfos().then(function (data) {
                        $scope.contract = data.contract;
                        $scope.settlements = data.settlements;
                        $scope.corporate = data.corporate;
                    });
                }

                loadInfos();

                /**
                 * Refresh mandataire information
                 */
                $scope.$on('refreshInfoMandataire', function (event) {
                    loadInfos();
                });
            }
        };
    }

    return infosMandataire;
});