/**
 * @ngdoc service
 * @name program.services:programService
 * @description
 * This file defines the programService
 */
define([], function () {
    'use strict';
    programService.$inject = ["$q", 'Restangular'];
    
    function programService($q, Restangular) {
        return {
            basePrograms: Restangular.all('programs'),
            
            /**
            * @ngdoc method
            * @name getPrograms
            * @methodOf program.services:programService
            * @description
            * Get all programs 
            * 
            * @returns {object} Programs data 
            */
            getPrograms: function () {
                return Restangular.all('programs').getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgram
            * @methodOf program.services:programService
            * @description
            * Get program by id 
            * 
            * @param {number} programId Id of program
            * 
            * @returns {object} Program data 
            */
            getProgram: function (programId) {
                return Restangular.one('programs', programId).get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramSubscriptionOrder
            * @methodOf program.services:programService
            * @description
            * Get program subscriptionorder
            * 
            * @param {number} programId Id of program
            * 
            * @returns {object} Program subscriptionorder data 
            */
            getProgramSubscriptionOrder: function (programId) {
                return Restangular.one('programs', programId).one('subscriptionorder').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name newProgram
            * @methodOf program.services:programService
            * @description
            * Get collaborators for new program
            *  
            * @returns {object} Program data 
            */
            newProgram: function () {
                return Restangular.all('programs').one('new').getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name postProgram
            * @methodOf program.services:programService
            * @description
            * Create/Edit program
            * 
            * @param {object} program A program data
            * 
            * @returns {object} Program data
            */
            postProgram: function (program) {
                var fd = new FormData();
                fd.append('isEasy', program.isEasy);
                fd.append('label', program.label);
                fd.append('image', program.image);
                fd.append('collaborator', program.collaborator);
                return Restangular.one('programs', program.id)
                    .customPOST(fd, '', undefined, {'Content-Type': undefined})
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name deleteProgram
            * @methodOf program.services:programService
            * @description
            * Delete program
            * 
            * @param {number} programId Id of program
            *  
            */
            deleteProgram: function (programId) {
                return Restangular.one('programs', programId).remove()
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramPointsHistory
            * @methodOf program.services:programService
            * @description
            * Get program history points
            * 
            * @param {number} programId Id of program
            * 
            * @returns {object} Program account points history data
            */
            getProgramPointsHistory: function (programId) {
                return Restangular.one('programs', programId).getList('accountpointshistory')
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramMandataire
            * @methodOf program.services:programService
            * @description
            * Get program mandataire
            * 
            * @param {number} programId Id of program
            * 
            * @returns {object} Program mandataire data
            */
            getProgramMandataire: function (programId) {
                return Restangular.one('programs', programId).one('mandataire').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name editProgram
            * @methodOf program.services:programService
            * @description
            * Create program copie
            * 
            * @param {number} programId Id of program
            * 
            * @returns {object} Program data
            */
            editProgram: function (programId) {
                return Restangular.one('programs', programId).one('edit').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name patchProgram
            * @methodOf program.services:programService
            * @description
            * Update program data
            * 
            * @param {number} programId Id of program
            * @param {object} patch Data to patching
            * 
            * @returns {object} Program data
            */
            patchProgram: function (programId, patch) {
                return Restangular.one('programs', programId).patch(patch)
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramCorporate
            * @methodOf program.services:programService
            * @description
            * Get corporate
            * 
            * @param {number} programId Id of program 
            * 
            * @returns {object} Program corporate data
            */
            getProgramCorporate: function (programId) {
                return Restangular.one('programs', programId).one('corporate').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramCollaborator
            * @methodOf program.services:programService
            * @description
            * Get collaborator
            * 
            * @param {number} programId Id of program 
            * 
            * @returns {object} Program collaborator data
            */
            getProgramCollaborator: function (programId) {
                return Restangular.one('programs', programId).one('collaborator').get()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramInvitations
            * @methodOf program.services:programService
            * @description
            * Load invitations for program
            * 
            * @param {number} id Id of program 
            * @param {string} search Seraching key
            * 
            * @returns {object} Searching label
            */
            getProgramInvitations: function (id, search) {
                return Restangular.one('programs', id).all('invitations').getList({search: search})
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramJournals
            * @methodOf program.services:programService
            * @description
            * Load journals of program
            * 
            * @param {number} programId Id of program  
            * 
            * @returns {object} Program journals
            */
            getProgramJournals: function (programId) {
                return Restangular.one('programs', programId).getList('journals')
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
  
            /**
            * @ngdoc method
            * @name getProgramStatusLabel
            * @methodOf program.services:programService
            * @description
            * Set status description
            * 
            * @param {object} program A program data
            * 
            * @returns {object} Program status label
            */
            getProgramStatusLabel: function (program) {
                var statusLabel;
                if (program.oldProgram) {
                    statusLabel = 'En cours de modification';
                    if (program.status === 'preprod') {
                        statusLabel += '(Pré-production)';
                    }
                } else {
                    switch (program.status) {
                        case 'preprod':
                            statusLabel = 'Pré-production';
                            break;
                        case 'prod':
                            statusLabel = 'En production';
                            break;
                        case 'cancel':
                            statusLabel = 'Résilié';
                            break;
                        default:
                            statusLabel = 'En cours de création';
                    }
                }
                return statusLabel;
            },
 
            /**
            * @ngdoc method
            * @name getProgramClausesAsHtml
            * @methodOf program.services:programService
            * @description
            * Get legal clauses of program
            * 
            * @param {number} programId Id of program
            * 
            * @returns {object} Program clauses
            */
            getProgramClausesAsHtml: function (programId) {
                return Restangular.one('legal').one('programs', programId).all('clauses').getList()
                    .then(function (response) {
                        return response.data;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            },
 
            /**
            * @ngdoc method
            * @name getProgramsProvisionFooterAsHtml
            * @methodOf program.services:programService
            * @description
            * Get provision footer as HTML
            *   
            * @returns {object} Program provision footer
            */
            getProgramsProvisionFooterAsHtml: function () {
                return Restangular.one('legal').all('programs').one('provision').one('footer').get()
                    .then(function (response) {
                        return response;
                    }, function (fallback) {
                        return $q.reject(fallback);
                    });
            }
        };
    }

    return programService;
});
