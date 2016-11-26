/**
 * @ngdoc controller
 * @name admin.controllers:giftOrderController
 * @description
 * This file defines the home controller for admin space
 */
define([], function () {
    'use strict';

    giftOrderController.$inject = ['$scope', 'giftOrderService', 'FileSaver'];

    function giftOrderController($scope, giftOrderService, FileSaver) {

        /**
        * @ngdoc method
        * @name loadGiftOrders
        * @methodOf admin.controllers:giftOrderController
        * @description
        * Get git orders
        */
        $scope.loadGiftOrders = function () {
            giftOrderService.getGiftOrders().then(function (data) {
                $scope.labelOperation = data.labelOperation;
                $scope.year = data.year;
                $scope.quarter = data.quarter;
                $scope.membersWithGiftsPending = data.membersWithGiftsPending;
                $scope.allGiftOrders = data.giftOrders;
            });
        };

        /**
        * @ngdoc method
        * @name commandGiftOrder
        * @methodOf admin.controllers:giftOrderController
        * @description
        * Create gift order
        */
        $scope.commandGiftOrder = function () {
            giftOrderService.postGiftOrder().then(function () {
                $scope.loadGiftOrders();
            });
        };

        /**
        * @ngdoc method
        * @name createGiftOrder
        * @methodOf admin.controllers:giftOrderController
        * @description
        * Confirm gift order
        *
        * @param {object} giftOrder A giftOrder
        */
        $scope.confirmGiftOrder = function (giftOrder) {
            var patch = [{op: 'confirm', path: '/'}];
            giftOrderService.patch(giftOrder.id, patch).then(function () {
                $scope.loadGiftOrders();
            });
        };


        /**
        * @ngdoc method
        * @name downloadGiftOrder
        * @methodOf admin.controllers:giftOrderController
        * @description
        * Download gift order in XLS format
        *
        * @param {object} giftOrder A giftOrder data
        * 
        * @return {object} giftOrder A giftOrder data
        */
        $scope.downloadGiftOrder = function (giftOrder) {
            giftOrderService.downloadGiftOrder(giftOrder.id).then(function (data) {
                var blob = new Blob([data], {type: "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet"});
                FileSaver.saveAs(blob, giftOrder.fileName);
            })
        };

        $scope.loadGiftOrders();

    }

    return giftOrderController;
});
