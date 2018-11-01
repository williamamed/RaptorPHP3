/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
angular.module('myApp')

.controller("configureController", function($scope,$http,$location) {
    //$scope.msg = "I love London";
    $('.switch').switchUIR();
    
    $('[data-toggle="tooltip"]').tooltip();
    $scope.submitForm = function(isValid) {

        // check to make sure the form is completely valid
        if (isValid) {
            
            //UIR.load.show('Configuring Raptor....')
            //userForm.submit()
            $http.post('site/platform/register/save',$scope.user,{ignoreRequest:true})
                    .then(function(response){
                        console.log(response)
                        $scope.user={}
                        
                        Raptor.msg.info("La plataforma fue registrada en el portal.")
                        $location.path('/plataforma/configuracion')
                    },function(response){
                        
                        Raptor.msg.error(response.data.msg)
                        
                    })
            
        }
        
    };

});

