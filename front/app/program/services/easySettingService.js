/**
 * @ngdoc service
 * @name program.services:easySettingService
 * @description
 * This file defines the easySettingService
 */
define([], function () {
        'use strict';
        easySettingService.$inject = ['$q', 'Restangular'];
        
        function easySettingService($q, Restangular) {
            return {
 
                /**
                * @ngdoc method
                * @name getProgramEasySetting
                * @methodOf program.services:easySettingService
                * @description
                * Load easy setting for program
                * 
                * @param {number} programId Id of program 
                * 
                * @returns {object} Program easy setting
                */
                getProgramEasySetting: function (programId) {
                    return Restangular.one('programs', programId).one('easysetting').get()
                        .then(function (response) {
                            return response.data;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                },
 
                /**
                * @ngdoc method
                * @name patchProgramEasySetting
                * @methodOf program.services:easySettingService
                * @description
                * Update easy setting data
                * 
                * @param {number} programId Id of program 
                * @param {object} patch data to patching 
                * 
                * @returns {object} Program easy setting
                */
                patchProgramEasySetting: function (programId, patch) {
                    return Restangular.one('programs', programId).one('easysetting').patch(patch)
                        .then(function (response) {
                            return response;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                },
 
                /**
                * @ngdoc method
                * @name getEasySettingDocument
                * @methodOf program.services:easySettingService
                * @description
                * Get presentation document
                * 
                * @param {number} programId Id of program  
                * 
                * @returns {object} Easy setting document
                */
                getEasySettingDocument: function (programId) {
                    return Restangular.one('programs', programId).one('easysetting').one('document')
                        .withHttpConfig({responseType: 'blob'}).get()
                        .then(function (response) {
                            return response;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                },
 
                /**
                * @ngdoc method
                * @name postEasySettingDocument
                * @methodOf program.services:easySettingService
                * @description
                * Upload program presentation document
                * 
                * @param {number} programId Id of program  
                * @param {object} document A document data
                * 
                * @returns {object} Easy setting document
                */
                postEasySettingDocument: function (programId, document) {
                    var formData = new FormData();
                    formData.append('document', document);
                    return Restangular.one('programs', programId).one('easysettings').one('documents')
                        .withHttpConfig({transformRequest: angular.identity})
                        .customPOST(formData, '', undefined, {'Content-Type': undefined})
                        .then(function (response) {
                            return response;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                }
            };
        }

        return easySettingService;
    }
);
