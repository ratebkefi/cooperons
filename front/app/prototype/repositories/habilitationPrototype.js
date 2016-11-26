/**
 * @ngdoc service
 * @name prototype.services:habilitationPrototype
 * @description
 * This file defines the habilitationPrototype service
 */
define([], function () {
    "use strict";

    /**
     * @ngdoc method
     * @name habilitationPrototype
     * @description
     * Return habilitationPrototype
     *
     * @param Restangular A service to handle Rest API Restful Resources properly and easily
     * @param $q A service used to throw exception when the request failed
     * @param prototypeModel A service to manipulate fictitious resources
     * @returns {object} habilitationPrototype
     */
    function habilitationPrototype($rootScope, config, $q, $timeout, prototypeModel) {

        // For Simulate loading
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

        return {

            /**
             * @ngdoc method
             * @name getHabilitations
             * @methodOf prototype.services:habilitationPrototype
             * @description  Get list of habilitations
             * @returns {array} Data response
             */
            getHabilitations: function () {
                // Simulate loading
                $rootScope.loading=true;
                $rootScope.pendingRequests++;
                return prototypeModel.getHabilitations()
                    .then(function (response) {
                        terminateRequest();
                        return response.data;
                    }, function (fallback) {
                        terminateRequest();
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getHabilitation
             * @methodOf prototype.services:habilitationPrototype
             * @description  Get habilitation by id
             * @param habilitationId Id of habilitation
             * @returns {array} Data response
             */
            getHabilitation: function (habilitationId) {
                $rootScope.loading=true;
                $rootScope.pendingRequests++;
                return prototypeModel.getHabilitation(habilitationId)
                    .then(function (response) {
                        terminateRequest();
                        return response.data;
                    }, function (fallback) {
                        terminateRequest();
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getHabilitationLegalDocuments
             * @methodOf prototype.services:habilitationPrototype
             * @description  Get list of legal documents for habilitation
             * @param habilitationId Id of habilitation
             * @returns {array} Data response
             */
            getHabilitationLegalDocuments: function (habilitationId) {
                $rootScope.loading=true;
                $rootScope.pendingRequests++;
                return prototypeModel.getHabilitationLegalDocuments(habilitationId)
                    .then(function (response) {
                        terminateRequest();
                        return response.data;
                    }, function (fallback) {
                        terminateRequest();
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name getHabilitationFile
             * @methodOf prototype.services:habilitationPrototype
             * @description  Get PDF file for habilitation
             * @param habilitationId Id of habilitation
             * @returns {object} Response
             */
            getHabilitationFile: function (habilitationId) {
                $rootScope.loading=true;
                $rootScope.pendingRequests++;
                return prototypeModel.getHabilitationFile(habilitationId)
                    .then(function (response) {
                        terminateRequest();
                        return response.data;
                    }, function (fallback) {
                        terminateRequest();
                        return $q.reject(fallback);
                    });
            },

            /**
             * @ngdoc method
             * @name postHabilitation
             * @methodOf prototype.services:habilitationPrototype
             * @description  Create new habilitation
             * @param data Legal document data
             * @returns {object} Response
             */
            postHabilitation: function (data) {
                $rootScope.loading=true;
                $rootScope.pendingRequests++;
                return prototypeModel.postHabilitation(data)
                    .then(function (response) {
                        terminateRequest();
                        return response;
                    }, function (fallback) {
                        terminateRequest();
                        return $q.reject(fallback);
                    });
            }

        };
    }

    return habilitationPrototype;
});