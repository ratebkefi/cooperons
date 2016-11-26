/**
 * @ngdoc controller
 * @name home.controllers:ShellController
 * @description
 * This file defines the home controller for home module
 */
define([], function () {
    'use strict';

    ShellController.$inject = ['$scope', 'common'];

    function ShellController($scope, common) {

        // Parameters
        $scope.menuReduced = common.getMenuStatus();
        $scope.tokenPlus = common.getMemberData().tokenPlus;

        // Fonctions
        $scope.reduceMenu = reduceMenu;
        $scope.logout = common.logout;

        $scope.FAQ = common.redirectToFAQ;

        /**
         * @ngdoc method
         * @name reduceMenu
         * @methodOf home.
         *
         * ShellController
         * @description
         * Reduce/Showing left menu
         */
        function reduceMenu() {
            $scope.menuReduced = !$scope.menuReduced;
            common.saveMenuStatus($scope.menuReduced);
        }

    }

    return ShellController;
});