/**
 * @ngdoc service
 * @name program.services:mailInvitationService
 * @description
 * This file defines the mailInvitationService
 */
define([], function () {
        'use strict';
        mailInvitationService.$inject = ['$q', 'Restangular'];
 
        function mailInvitationService($q, Restangular) {
            return {
 
                /**
                * @ngdoc method
                * @name getProgramMailInvitations
                * @methodOf program.services:mailInvitationService
                * @description
                * Load mail invitations for program
                *  
                * @param {number} programId Id of program 
                * 
                * @returns {object} Program mail invitations
                */
                getProgramMailInvitations: function (programId) {
                    return Restangular.one('programs', programId).getList('mailinvitations')
                        .then(function (response) {
                            return response.data;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                },
 
                /**
                * @ngdoc method
                * @name postProgramMailInvitation
                * @methodOf program.services:mailInvitationService
                * @description
                * Create new mail invitation template
                *  
                * @param {number} programId Id of program 
                * @param {object} data Program mail invitation data
                * 
                * @returns {object} Program mail invitations
                */
                postProgramMailInvitation: function (programId, data) {
                    return Restangular.one('programs', programId)
                        .post('mailinvitations', data)
                        .then(function (response) {
                            return response;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                },
 
                /**
                * @ngdoc method
                * @name putProgramMailInvitation
                * @methodOf program.services:mailInvitationService
                * @description
                * Update mail invitation template
                *  
                * @param {number} programId Id of program 
                * @param {number} codeMail Code of mail
                * @param {object} data Program mail invitation data
                * 
                * @returns {object} Program mail invitation
                */
                putProgramMailInvitation: function (programId, codeMail, data) {
                    return Restangular.one('programs', programId).one('mailinvitations', codeMail).customPUT(data)
                        .then(function (response) {
                            return response;
                        }, function (response) {
                            return $q.reject(response);
                        });
                },
 
                /**
                * @ngdoc method
                * @name deleteProgramMailInvitation
                * @methodOf program.services:mailInvitationService
                * @description
                * Delete mail invitation template
                *  
                * @param {number} programId Id of program 
                * @param {number} codeMail Code of mail 
                * 
                * @returns {object} Program mail invitation
                */
                deleteProgramMailInvitation: function (programId, codeMail) {
                    return Restangular.one('programs', programId).one('mailinvitations', codeMail).remove()
                        .then(function (response) {
                            return response;
                        }, function (fallback) {
                            return $q.reject(fallback);
                        });
                }
            };
        }

        return mailInvitationService;
    }
);
