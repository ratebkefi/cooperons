/*global angular*/
define([
    'config/config'
], function (appConfig) {
    const config = {
        loading: {
            timeOut: 700
        },
        toastr: {
            closeButton: true,
            closeMethod: 'fadeOut',
            closeDuration: 300,
            closeEasing: 'swing',
            progressBar: true,
        }
    };

    angular.extend(config, appConfig);

    return config;
});