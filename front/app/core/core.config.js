/**
 * The main config of our application
 *
 * @returns {void}
 */
define([
    './decorator/$mdDialogDecorator',
    './services/jwtTokenGetter'
], function ($mdDialogDecorator, jwtTokenGetter) {
    'use strict';

    var moduleNme = 'core.config';

    angular.module(moduleNme, [])
        .config(function ($provide, $httpProvider, $locationProvider, jwtInterceptorProvider) {
            $locationProvider.html5Mode(true);
            $provide.decorator('$mdDialog', $mdDialogDecorator);

            jwtInterceptorProvider.tokenGetter = jwtTokenGetter;
            $httpProvider.interceptors.push('jwtInterceptor');
        })
        .run(function ($rootScope, Restangular, common, interceptor, config) {

            common.loadSession();

            // Shared functions used in HTML
            $rootScope.backRoute = common.getBackRoute;
            $rootScope.uploadedFile = common.getUploadedFile;

            toastr.options = config.toastr;

            Restangular.setBaseUrl(config.apiBaseUri);
            Restangular.addRequestInterceptor(interceptor.interceptRequest);
            Restangular.addResponseInterceptor(interceptor.interceptResponse);
            Restangular.setErrorInterceptor(interceptor.interceptError);

            $rootScope.$on('$stateChangeStart', interceptor.onStateChangeStart);
            $rootScope.$on('$stateChangeSuccess', interceptor.onStateChangeSuccess);
        });

    return moduleNme;


});