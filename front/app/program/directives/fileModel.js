/**
 * @ngdoc directive
 * @name program.directives:fileModel
 *   
 * @description
 * Read files in html
 *  
 * @example
   <example>
     <file name="payment.html">
        <input file-model></input> 
     </file>
   </example>
 */
define([], function () {
    'use strict';
    fileModel.$inject = ['$parse'];
  
    function fileModel($parse) {
        return {
        restrict: 'A',
        link: function(scope, element, attrs) {
            var model = $parse(attrs.fileModel);
            var modelSetter = model.assign;
            
            element.bind('change', function(){
                scope.$apply(function(){
                    modelSetter(scope, element[0].files[0]);
                });
            });
        }
        };
    }
    return fileModel;
});