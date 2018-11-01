Ext.define('GestUser.model.UserModel', {
    extend: 'Ext.data.Model',
    fields: ['id', 'fullname', 'username','icon','idRol', 'rolname', 'password', 'state', 'email'],
    proxy: {
        type: 'ajax',
        api: {
            read: 'user/list',
            update: 'user/edit',
            create: 'user/insert',
            destroy: 'user/delete'
        },
        actionMethods: {//Esta Linea es necesaria para el metodo de llamada POST o GET
            read: 'POST'
        },
        reader: {
            type: 'json',
            root: 'rows',
            successProperty: 'success',
            totalProperty: 'result'
        }
    }
});