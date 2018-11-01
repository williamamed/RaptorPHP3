Ext.define('GestRol.view.RolList', {
    extend: 'Ext.tree.Panel',
    alias: 'widget.rollist',
    rootVisible: true,
    store: 'Rol',
    title: 'Rol Manager',
    iconCls:'icon-user',
    height:400,
    initComponent: function() {
        
        
        this.dockedItems = [{
            dock: 'top',
            xtype: 'toolbar',
            items: [{
                xtype: 'button',
                text: Raptor.getTag('add'),
                hidden:true,
                privilegeName:'insert',
                action:'addrol',
                iconCls:'icon-add'
            },{
                xtype: 'button',
                text: Raptor.getTag('edit'),
                hidden:true,
                privilegeName:'edit',
                disabled:true,
                action:'updaterol',
                iconCls:'icon-edit'  
            },{
                xtype: 'button',
                text: Raptor.getTag('delete'),
                privilegeName:'delete',
                disabled:true,
                hidden:true,
                action:'deleterol',
                iconCls:'icon-del'  
            },{
                xtype: 'button',
                text: Raptor.getTag('priv'),
                privilegeName:'asignprivilege',
                disabled:true,
                hidden:true,
                action:'privilegeasign',
                iconCls:'icon-privilege'  
            }]
        }];
        
        this.callParent();
    }
});

