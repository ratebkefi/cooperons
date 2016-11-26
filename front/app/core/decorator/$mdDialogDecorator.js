/**
 * This file defines the decorator of $mdDialog service
 *
 * @category service
 * @package config
 * @author Fondative <devteam@fondative.com>
 * @copyright 2015-2016 Fondative
 * @version 0.0.1
 * @since File available since Release 0.0.1
 */

define([], function () {
    'use strict';

    $mdDialogDecorator.$inject = ['$delegate'];

    function $mdDialogDecorator($delegate) {

        $delegate.showConfirm = showConfirm;

        return $delegate;

        function showConfirm(params) {
            var confirm = $delegate.confirm({
                controller: DialogController,
                controllerAs: 'dlgCtr',
                templateUrl: params.template,
                parent: angular.element(document.body),
            });
            return $delegate.show(confirm);

            function DialogController() {
                var vm = this;

                if (params.data) {
                    angular.extend(vm, params.data);
                }

                vm.confirm = function (data) {
                    $delegate.hide();
                    return data;
                };

                vm.cancel = function () {
                    $delegate.cancel();
                };
            }
        }

    }

    return $mdDialogDecorator;
});