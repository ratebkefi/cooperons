/**
 * This file defines the back routes
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

    interceptor.$inject = ['$rootScope', '$timeout', 'config', 'common'];


    function interceptor($rootScope, $timeout, config, common) {

        var service = {
            interceptRequest: interceptRequest,
            interceptResponse: interceptResponse,
            interceptError: interceptError,
            onStateChangeStart: onStateChangeStart,
            onStateChangeSuccess: onStateChangeSuccess
        };

        return service;

        //////////////////////

        function interceptRequest(element, operation, what, url) {
            $rootScope.pendingRequests++;
            $rootScope.loading = $rootScope.pendingRequests>0;
            return element;
        }

        function interceptResponse(data, operation) {
            var extractedData = data;
            if (operation == 'getList') {
                extractedData = [];
                extractedData.status = data.status;
                extractedData.code = data.code;
                extractedData.message = data.message;
                extractedData.data = data.data;
            }

            terminateRequest();
            return extractedData;
        }

        function interceptError(response) {
            if (response.status === 401) {
                if ($rootScope.space !== 'public' && $rootScope.space !== 'user') {
                    common.logout();
                }
                terminateRequest();
                return response;
            }

            var data = response.data;

            if (data && data.status && data.code) {
                var message = apiMessages[data.code] ? apiMessages[data.code] : data.message;
                if (data.status === 'error') {
                    toastr.error(message, 'Erreur !');
                } else if (data.status === 'warning') {
                    toastr.warning(message, 'Attention !');
                }
            } else {
                toastr.error('Oups! une erreur s\'est produite', 'Erreur !');

            }
            terminateRequest();
            return response;
        }

        function onStateChangeStart() {
            $rootScope.pendingRequests = 0;
            $rootScope.showPage = false;
        }

        function onStateChangeSuccess() {
            $rootScope.spaceReady = true;
        }

        // internal functions

        function terminateRequest() {
            $rootScope.pendingRequests ? $rootScope.pendingRequests-- : null;
            if (!$rootScope.pendingRequests) {
                if ($rootScope.showPage) {
                    $rootScope.loading = false;
                } else {
                    $timeout(unlockPage, config.loading.timeOut);
                }
            }

            function unlockPage() {
                if (!$rootScope.pendingRequests && !$rootScope.showPage) {
                    $rootScope.showPage = true;
                    $rootScope.loading = false;
                }
            }
        }
    }

    return interceptor;
})
;