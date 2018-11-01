/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
angular.module('myApp')

.controller("platformCtrl", function($scope,$http,$location) {
    //$scope.msg = "I love London";
    $('#btn-config').click(function() {
        //$(this).attr({disabled: true});
        //UIR.load.show('Configuring Raptor....')
    })
    

    $('[data-toggle="tooltip"]').tooltip();
    $scope.submitForm = function(isValid) {

        // check to make sure the form is completely valid
        if (isValid) {
            
            //UIR.load.show('Configuring Raptor....')
            //userForm.submit()
            $scope.user.token=Raptor.getToken();
            $http.post('site/platform/register/save',$scope.user,{ignoreRequest:true})
                    .then(function(response){
                        console.log(response)
                        
                        if(response.data.cod==Raptor.INFO){
                            $scope.user={}
                            Raptor.msg.info("La plataforma fue registrada en el portal.")
                            $location.path('/plataforma/configuracion')
                        }else{
                            Raptor.msg.error(response.data.msg)
                        }
                    },function(response){
                        if(response.data.msg)
                            Raptor.msg.error(response.data.msg)
                        else
                            Raptor.msg.error(response.data)
                        
                    })
          
        }
        
    };

});
