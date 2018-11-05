/*
 * Created With Raptor
 * Copyrigth 2013
 */


Ext.application({
    name: 'GestUser',
//    paths:{
//        'GestUser': Raptor.getBundleResource('Raptor2/Syntarsus/user/js/app')
//    },
//    requires: ['Ext.container.Viewport'],
    // automatically create an instance of AM.view.Viewport
    autoCreateViewport: true,
    launch:function(){
        
    },
    models: ['UserModel','RolModel','EstructureModel'],    
    stores: ['User','Rol','Estructure','State'],
    controllers: ['Users','Roles']
});

hermes.on('raptor-login',function(){
    
})



