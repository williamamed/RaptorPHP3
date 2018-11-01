/**
 * Raptor - Integration PHP 5 framework
 *
 * @author      William Amed <watamayo90@gmail.com>, Otto Haus <ottohaus@gmail.com>
 * @copyright   2014 
 * @link        http://dinobyte.net
 * @version     2.0.1
 * @package     Raptor
 *
 * MIT LICENSE
 *
 * Permission is hereby granted, free of charge, to any person obtaining
 * a copy of this software and associated documentation files (the
 * "Software"), to deal in the Software without restriction, including
 * without limitation the rights to use, copy, modify, merge, publish,
 * distribute, sublicense, and/or sell copies of the Software, and to
 * permit persons to whom the Software is furnished to do so, subject to
 * the following conditions:
 *
 * The above copyright notice and this permission notice shall be
 * included in all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF
 * MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND
 * NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE
 * LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION
 * OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION
 * WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
 */
Raptor.controlActions=function() {
    if(!Raptor.getActions){
                
                console.info("You are using the function Raptor.getActions() provided by the Syntarsus Module wich is not linked to this route.");
                return;
            }
            
    if(window.Ext){
            
            var actions = Raptor.getActions();
            if (actions != false) {
                var actionsSize = actions.length;
                var selector = new Array();
                for (var i = 0; i < actionsSize; i++) {
                    selector.push('[privilegeName=' + actions[i] + '] ');
                }
                var sel=selector.join(',');
                var all = Ext.ComponentQuery.query('[?privilegeName]');
                Ext.each(all, function(name, index, countriesItSelf) {
                    name.hide();
                });
                
                var compo = Ext.ComponentQuery.query(sel);
                
                Ext.each(compo, function(name, index, countriesItSelf) {
                    name.show();
                });
            }
    }
    if(window.jQuery){
            
            var actions = Raptor.getActions();
            if (actions != false) {
                var actionsSize = actions.length;
               
                $("[privilegeName]").hide();
                
                var selector = new Array();
                for (var i = 0; i < actionsSize; i++) {
                    selector.push("[privilegeName='" + actions[i] + "'] ");
                }
                var sel=selector.join(',');
                
                $(sel).show();
            }
    }
}
Raptor.core={
    storage:{}
}

Raptor.msg={
    defaultTech:'extjs',
    show:function(cod){
        var rest=new Array();
        if(arguments.length>1)
            for(var i=1,cant=arguments.length;i<cant;i++){
                rest.push(arguments[i]);
            }
        switch (cod){
            
            case 1:{
                    this.info.apply(this,rest);
                    break;
            }
            case 2:{
                    this.confirm.apply(this,rest);
                    break;
            }
            case 3:{
                    this.error.apply(this,rest);
                    break;
            }
            case 4:{
                    return this.wait.apply(this,rest);
                    break;
            }
            case 5:{
                    return this.exception.apply(this,rest);
                    break;
            }
        }
    },
    info:function(){
       if(Raptor.msg[this.defaultTech].info){
           Raptor.msg[this.defaultTech].info.apply(this,arguments);
           return;
       }
       if(Raptor.msg.extjs.info){
           Raptor.msg.extjs.info.apply(this,arguments);
           return;
       }
       if(Raptor.msg.bootstrap.info){
           Raptor.msg.bootstrap.info.apply(this,arguments);
           return;
       }
    },
    confirm:function(){
       if(Raptor.msg[this.defaultTech].confirm){
           Raptor.msg[this.defaultTech].confirm.apply(this,arguments);
           return;
       }
       if(Raptor.msg.extjs.confirm){
           Raptor.msg.extjs.confirm.apply(this,arguments);
           return;
       }
       if(Raptor.msg.bootstrap.confirm){
           Raptor.msg.bootstrap.confirm.apply(this,arguments);
           return;
       }
    },
    wait:function(){
       if(Raptor.msg[this.defaultTech].wait){
           return Raptor.msg[this.defaultTech].wait.apply(this,arguments);
           
       }
       if(Raptor.msg.extjs.wait){
           return Raptor.msg.extjs.wait.apply(this,arguments);
           
       }
       if(Raptor.msg.bootstrap.wait){
           return Raptor.msg.bootstrap.wait.apply(this,arguments);
           
       } 
    },
    error:function(){
        if(Raptor.msg[this.defaultTech].error){
           Raptor.msg[this.defaultTech].error.apply(this,arguments);
           return;
       }
       if(Raptor.msg.extjs.error){
           Raptor.msg.extjs.error.apply(this,arguments);
           return;
       }
       if(Raptor.msg.bootstrap.error){
           Raptor.msg.bootstrap.error.apply(this,arguments);
           return;
       }
    },
    exception:function(){
        if(Raptor.msg[this.defaultTech].exception){
           Raptor.msg[this.defaultTech].exception.apply(this,arguments);
           return;
       }
       if(Raptor.msg.extjs.exception){
           Raptor.msg.extjs.exception.apply(this,arguments);
           return;
       }
       if(Raptor.msg.bootstrap.exception){
           Raptor.msg.bootstrap.exception.apply(this,arguments);
           return;
       }
    },
    extjs:{},
    bootstrap:{},
    tags: {
        es:{
            yes:'S�',
            no: 'No',
            close:'Cerrar',
            acept:'Aceptar'
        },
        en:{
            yes:'Yes',
            no: 'No',
            close:'Cerrar',
            acept:'Acept'
        }
    },
    getLang:function(tag){
        switch (Raptor.getLanguage()){
            case 'es':{
                    return this.tags.es[tag];
                    break;
            }
            case 'en':{
                    return this.tags.en[tag];
                    break;
            }
        }
    }
}
Raptor.Animated=function(source){
    if(window.Ext){
            Ext.onReady(function() {
            
            var f = Ext.DomHelper.append(Ext.getBody(), {
                tag: 'div'
            }, true);

            var ancho = Ext.getBody();
            Ext.DomHelper.applyStyles(f, {
                'width': '80px', height: '95px', 'z-index': '1000000','border-radius':'5px'
            });
            var src=Raptor.getBundleResource('Raptor/img/anim2.gif');
            if(source)
                src=source;
            var im = Ext.DomHelper.append(f, {
                            tag: 'img',
                            src:src,
                            'width': '80', height: '95'
            }, true);
            f.position('absolute', 1000000, ancho.getWidth(true) - 100, ancho.getHeight() - 100);
            f.show({duration: 1000});
            f.flagVision = true;

            setInterval(function() {
                if (f.flagVision) {
                    f.fadeOut({
                        opacity: 0, //can be any value between 0 and 1 (e.g. .5)
                        easing: 'easeOut',
                        duration: 1000,
                        remove: false,
                        useDisplay: false
                    });
                    f.flagVision = false;
                } else {
                    f.show({
                        opacity: 0, //can be any value between 0 and 1 (e.g. .5)
                        easing: 'easeIn',
                        duration: 1000,
                        remove: false,
                        useDisplay: false
                    });

                    f.flagVision = true;
                }
            }, 20000);
        })
    }
};

if(window.Ext){
    
        Ext.require('Ext.form.field.Base');
        
        Ext.onReady(function() {
          Raptor.core.storage.container = Ext.DomHelper.append(Ext.getBody(), {
                        tag: 'span'
          }, true);
            Raptor.core.storage.ancho1 = Ext.getBody();
            Raptor.core.storage.container.position('absolute', 1000000, Raptor.core.storage.ancho1.getWidth(true) - 330, 40);

            Ext.form.field.Base.override({
            constructor: function() {


                 this.callParent(arguments);
                 if(this.allowBlank==false)
                    this.labelSeparator='<span>: </span><b style="color:red;font-size:12px"> *</b>';
            }
        });
        
        if(window.parent && window.parent.Ext){
            Ext.getBody().on('click',function(){
            	if(window.parent.Ext&&window.parent!=window)
                	window.parent.Ext.menu.Manager.hideAll();
            });

        }
        
        Ext.Ajax.on('requestcomplete', function(conn, response, options){

                        var respText = response.responseText;
                        var resp=Ext.decode(respText);
                        if (resp!=undefined&&resp.cod!=undefined&&resp.cod==5){
                            Raptor.msg.extjs.exception(resp.msg,resp.trace);
                        }
        });

        Ext.Ajax.on('requestexception', function(conn, response, options){

                        var respText = response.responseText;
                        var resp=Ext.decode(respText);
                        if (resp!=undefined&&resp.cod!=undefined&&resp.cod==5){
                                Raptor.msg.extjs.exception(resp.msg,resp.trace);
                        }
        });

        Ext.Ajax.on('beforerequest', function (conn, options) {
                            if (!(/^http:.*/.test(options.url) || /^https:.*/.test(options.url))) {
                               if(Raptor)
                                   var token=Raptor.getToken();
                                if(!options.params)
                                   options.params={token:token};
                               else
                                   options.params.token=token;                       
                            }


                            }, this);

        });
        
        Raptor.msg.extjs.info=function(msg,duration,background,type){
               var f = Ext.DomHelper.append(Ext.getBody(), {
                            tag: 'span',
                            cls: 'msg-raptor2'
                        }, true);
                        
                        var ancho = Ext.getBody();
                        var back='#402878';
                        if(background)
                               back=background;
                        Ext.DomHelper.applyStyles(f, {
                            'width': '300px', 'z-index': '1000000',
                             'padding':'0px',
                             'margin-bottom':'5px',
                             'background-color': back,
                             'color':'#fff',
                             'cursor':'pointer',
                             'display':'inline-block',
                             'font':'bold 100%/2.1 "Lucida Grande", Tahoma, sans-serif',
                             'text-decoration':'none',
                             'text-shadow':'0 1px 0 rgba(0,0,0,0.5)',
                             '-moz-user-select':'none',
                             '-webkit-user-select':'none',
                             'user-select':'none',
                             'position':'relative'
                             
                        });
          
                        f.hide();
                         var title = Ext.DomHelper.append(f, {
                            tag: 'div',
                            html: '<span class="" >X</span>'
                        }, true);
                        Ext.DomHelper.applyStyles(title, {
                             
                             'margin-top':'-14px',
                             'margin-right':'-14px',
                             'float': 'right',
                             'color':'#fff',
                             'background':'#b4201c',
                             'width':'20px',
                             'height':'20px',
                             'font':'bold 100%/2.1 "Lucida Grande", Tahoma, sans-serif',
                             'font-size':'9px',
                             'text-align':'center',
                             'text-shadow':'0 1px 0 rgba(0,0,0,0.5)',
                             '-moz-user-select':'none',
                             '-webkit-user-select':'none',
                             'user-select':'none',
                             'position':'relative'
                             
                        });
                        var titleb='';
                        switch (type){
                            case Raptor.ERROR:{
                                 titleb="Ops !!";   
                                 break;   
                            }
                            case Raptor.INFO:{
                                 titleb="";   
                                 break;      
                            }
                            default:{
                                 titleb="";   
                                 break;         
                            }
                        }
                        var im = Ext.DomHelper.append(f, {
                            tag: 'span',
                            html: '<span class="icon-info" style="width:20px;height:20px; float:left;margin:2px;"></span><b>'+titleb+'</b>',
                            cls: 'x-window-dlg',
                            style:{
                                background:'rgba(0,0,0,0.2)',
                                padding:'5px',
                                display:'block'
                            }
                        }, true);
                        

                        var tex = Ext.DomHelper.append(f, {
                            tag: 'span',

                            html: msg,
                            style:{
                                boxSizing: 'content-box !important',
                                padding:'5px'
                            }

                        }, true);
                        
                        
                        
                        Ext.DomHelper.applyStyles(tex, {
                            padding:'8px',boxSizing: 'content-box !important',
                            display:'block',
                            'font':'normal 100%/2.1 "Lucida Grande", Tahoma, sans-serif',
                            
                        });
                        f.appendTo(Raptor.core.storage.container);
                        
                        f.fadeIn({
                            opacity: 1, //can be any value between 0 and 1 (e.g. .5)
                            easing: 'easeOut',
                            duration: 1500
                        });
                        var timeTo=15;
                        if(duration){
                            timeTo=duration;
                        }

                        var time=setTimeout(function() {
                                im.hide(true);
                                tex.hide(true);
                             Ext.DomHelper.applyStyles(f, {

                                 'border-radius':'0px',
                                 'padding':'0px',
                                 'margin-bottom':'0apx'
                            });
                             Ext.fly(f).setHeight(0, {
                                duration : 500, // animation will have a duration of .5 seconds
                                // will change the content to "finished"
                                callback: function(){  f.remove(); }
                            });


                        }, timeTo*1000); 

                        title.on('click',function(){
                                clearTimeout(time);
                                im.hide(true);
                                tex.hide(true);
                                Ext.DomHelper.applyStyles(f, {
                                    'border-radius': '0px',
                                    'padding': '0px',
                                    'margin-bottom': '0apx'
                                });
                                Ext.fly(f).setHeight(0, {
                                    duration: 500, // animation will have a duration of .5 seconds
                                    // will change the content to "finished"
                                    callback: function() {
                                        f.remove();
                                    }
                                });
                        })
        };
        
        Raptor.msg.extjs.confirm=function(msg,fn,scope){
            var arg=arguments;
            
            Ext.Msg.confirm('', msg, function(n) {
                        var params=new Array();
                        params.push(n);
                        if(arg.length>3)
                        for(var i=3,cant=arg.length;i<cant;i++){
                            params.push(arg[i]);
                        }
                        if (n === 'yes') {
                            fn.apply(scope,params);
                        }
                  });
        };
        
        Raptor.msg.extjs.wait=function(msg){
            return Ext.Msg.wait(msg,'',{
                                progress: true,
            //                    title: '',
                                closable: false,
                                modal: true,
                                width: 350
             });
        };
        
        Raptor.msg.extjs.error=function(msg,float,fn,scope){
            if(float===undefined || float===true){
                Raptor.msg.extjs.info(msg,undefined,'#990033',Raptor.ERROR);
            }else{
                 var buttons = new Array(Ext.MessageBox.OK, Ext.MessageBox.OKCANCEL, Ext.MessageBox.OK);
                 var title = new Array('', '', '');
                 var icons = new Array(Ext.MessageBox.INFO, Ext.MessageBox.QUESTION, Ext.MessageBox.ERROR);
                 Ext.MessageBox.show({
                            title: title[0],
                            msg: msg,
                            //animEl: Ext.getBody(),
                            buttons: buttons[0],
                            icon: icons[2],
                            fn: fn,
                            scope: scope
                 }); 
            }
        };
        
        Raptor.msg.extjs.exception=function(msg,trace){
           var win=new Ext.Window({
                              title:'Exception',
                              modal:true,
                              autoHeight:true,
                              closeAction:'destroy',
                              layout:'anchor',
                              iconCls: 'icon-excep',
                              width:600,
                              buttons:[{
                                      xtype:'button',
                                      text:'Close',
                                      iconCls:'icon-cancel',
                                      handler:function(){
                                          win.close();
                                      },
                                      scope:this
                              }],
                              items:[{
                                      xtype:'container',
                                      layout:'anchor',
                                      anchor:'100%',

                                      height:'100%',
                                      items:[{
                                                xtype:'container',
                                                margin:'5 5 5 5',
                                                padding:10,
                                                html:msg,
                                                anchor:'100%'
                                        },{
                                              xtype:'fieldset',
                                              margin:'5 15 5 5',
                                              title: 'Trace',
                                              collapsible: true,

                                              collapsed:true,
                                              layout:'fit',
                                              anchor:'100%',
                                              items:{
                                                  xtype:'container',
                                                  html:trace
                                              }
                                      }]
                              }]
                           }); 
                 win.show();
                 return win;
        };
}
if(window.jQuery){
        !function(t){"function"==typeof define&&define.amd?define(["jquery"],t):t("object"==typeof exports?require("jquery"):jQuery)}(function(t){function s(s){var e=!1;return t('[data-notify="container"]').each(function(i,n){var a=t(n),o=a.find('[data-notify="title"]').text().trim(),r=a.find('[data-notify="message"]').html().trim(),l=o===t("<div>"+s.settings.content.title+"</div>").html().trim(),d=r===t("<div>"+s.settings.content.message+"</div>").html().trim(),g=a.hasClass("alert-"+s.settings.type);return l&&d&&g&&(e=!0),!e}),e}function e(e,n,a){var o={content:{message:"object"==typeof n?n.message:n,title:n.title?n.title:"",icon:n.icon?n.icon:"",url:n.url?n.url:"#",target:n.target?n.target:"-"}};a=t.extend(!0,{},o,a),this.settings=t.extend(!0,{},i,a),this._defaults=i,"-"===this.settings.content.target&&(this.settings.content.target=this.settings.url_target),this.animations={start:"webkitAnimationStart oanimationstart MSAnimationStart animationstart",end:"webkitAnimationEnd oanimationend MSAnimationEnd animationend"},"number"==typeof this.settings.offset&&(this.settings.offset={x:this.settings.offset,y:this.settings.offset}),(this.settings.allow_duplicates||!this.settings.allow_duplicates&&!s(this))&&this.init()}var i={element:"body",position:null,type:"info",allow_dismiss:!0,allow_duplicates:!0,newest_on_top:!1,showProgressbar:!1,placement:{from:"top",align:"right"},offset:20,spacing:10,z_index:1031,delay:5e3,timer:1e3,url_target:"_blank",mouse_over:null,animate:{enter:"animated fadeInDown",exit:"animated fadeOutUp"},onShow:null,onShown:null,onClose:null,onClosed:null,icon_type:"class",template:'<div data-notify="container" class="col-xs-11 col-sm-4 alert alert-{0}" role="alert"><button type="button" aria-hidden="true" class="close" data-notify="dismiss">&times;</button><span data-notify="icon"></span> <span data-notify="title">{1}</span> <span data-notify="message">{2}</span><div class="progress" data-notify="progressbar"><div class="progress-bar progress-bar-{0}" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%;"></div></div><a href="{3}" target="{4}" data-notify="url"></a></div>'};String.format=function(){for(var t=arguments[0],s=1;s<arguments.length;s++)t=t.replace(RegExp("\\{"+(s-1)+"\\}","gm"),arguments[s]);return t},t.extend(e.prototype,{init:function(){var t=this;this.buildNotify(),this.settings.content.icon&&this.setIcon(),"#"!=this.settings.content.url&&this.styleURL(),this.styleDismiss(),this.placement(),this.bind(),this.notify={$ele:this.$ele,update:function(s,e){var i={};"string"==typeof s?i[s]=e:i=s;for(var n in i)switch(n){case"type":this.$ele.removeClass("alert-"+t.settings.type),this.$ele.find('[data-notify="progressbar"] > .progress-bar').removeClass("progress-bar-"+t.settings.type),t.settings.type=i[n],this.$ele.addClass("alert-"+i[n]).find('[data-notify="progressbar"] > .progress-bar').addClass("progress-bar-"+i[n]);break;case"icon":var a=this.$ele.find('[data-notify="icon"]');"class"===t.settings.icon_type.toLowerCase()?a.removeClass(t.settings.content.icon).addClass(i[n]):(a.is("img")||a.find("img"),a.attr("src",i[n]));break;case"progress":var o=t.settings.delay-t.settings.delay*(i[n]/100);this.$ele.data("notify-delay",o),this.$ele.find('[data-notify="progressbar"] > div').attr("aria-valuenow",i[n]).css("width",i[n]+"%");break;case"url":this.$ele.find('[data-notify="url"]').attr("href",i[n]);break;case"target":this.$ele.find('[data-notify="url"]').attr("target",i[n]);break;default:this.$ele.find('[data-notify="'+n+'"]').html(i[n])}var r=this.$ele.outerHeight()+parseInt(t.settings.spacing)+parseInt(t.settings.offset.y);t.reposition(r)},close:function(){t.close()}}},buildNotify:function(){var s=this.settings.content;this.$ele=t(String.format(this.settings.template,this.settings.type,s.title,s.message,s.url,s.target)),this.$ele.attr("data-notify-position",this.settings.placement.from+"-"+this.settings.placement.align),this.settings.allow_dismiss||this.$ele.find('[data-notify="dismiss"]').css("display","none"),(this.settings.delay<=0&&!this.settings.showProgressbar||!this.settings.showProgressbar)&&this.$ele.find('[data-notify="progressbar"]').remove()},setIcon:function(){"class"===this.settings.icon_type.toLowerCase()?this.$ele.find('[data-notify="icon"]').addClass(this.settings.content.icon):this.$ele.find('[data-notify="icon"]').is("img")?this.$ele.find('[data-notify="icon"]').attr("src",this.settings.content.icon):this.$ele.find('[data-notify="icon"]').append('<img src="'+this.settings.content.icon+'" alt="Notify Icon" />')},styleDismiss:function(){this.$ele.find('[data-notify="dismiss"]').css({position:"absolute",right:"10px",top:"5px",zIndex:this.settings.z_index+2})},styleURL:function(){this.$ele.find('[data-notify="url"]').css({backgroundImage:"url(data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7)",height:"100%",left:0,position:"absolute",top:0,width:"100%",zIndex:this.settings.z_index+1})},placement:function(){var s=this,e=this.settings.offset.y,i={display:"inline-block",margin:"0px auto",position:this.settings.position?this.settings.position:"body"===this.settings.element?"fixed":"absolute",transition:"all .5s ease-in-out",zIndex:this.settings.z_index},n=!1,a=this.settings;switch(t('[data-notify-position="'+this.settings.placement.from+"-"+this.settings.placement.align+'"]:not([data-closing="true"])').each(function(){e=Math.max(e,parseInt(t(this).css(a.placement.from))+parseInt(t(this).outerHeight())+parseInt(a.spacing))}),this.settings.newest_on_top===!0&&(e=this.settings.offset.y),i[this.settings.placement.from]=e+"px",this.settings.placement.align){case"left":case"right":i[this.settings.placement.align]=this.settings.offset.x+"px";break;case"center":i.left=0,i.right=0}this.$ele.css(i).addClass(this.settings.animate.enter),t.each(Array("webkit-","moz-","o-","ms-",""),function(t,e){s.$ele[0].style[e+"AnimationIterationCount"]=1}),t(this.settings.element).append(this.$ele),this.settings.newest_on_top===!0&&(e=parseInt(e)+parseInt(this.settings.spacing)+this.$ele.outerHeight(),this.reposition(e)),t.isFunction(s.settings.onShow)&&s.settings.onShow.call(this.$ele),this.$ele.one(this.animations.start,function(){n=!0}).one(this.animations.end,function(){s.$ele.removeClass(s.settings.animate.enter),t.isFunction(s.settings.onShown)&&s.settings.onShown.call(this)}),setTimeout(function(){n||t.isFunction(s.settings.onShown)&&s.settings.onShown.call(this)},600)},bind:function(){var s=this;if(this.$ele.find('[data-notify="dismiss"]').on("click",function(){s.close()}),this.$ele.mouseover(function(){t(this).data("data-hover","true")}).mouseout(function(){t(this).data("data-hover","false")}),this.$ele.data("data-hover","false"),this.settings.delay>0){s.$ele.data("notify-delay",s.settings.delay);var e=setInterval(function(){var t=parseInt(s.$ele.data("notify-delay"))-s.settings.timer;if("false"===s.$ele.data("data-hover")&&"pause"===s.settings.mouse_over||"pause"!=s.settings.mouse_over){var i=(s.settings.delay-t)/s.settings.delay*100;s.$ele.data("notify-delay",t),s.$ele.find('[data-notify="progressbar"] > div').attr("aria-valuenow",i).css("width",i+"%")}t<=-s.settings.timer&&(clearInterval(e),s.close())},s.settings.timer)}},close:function(){var s=this,e=parseInt(this.$ele.css(this.settings.placement.from)),i=!1;this.$ele.attr("data-closing","true").addClass(this.settings.animate.exit),s.reposition(e),t.isFunction(s.settings.onClose)&&s.settings.onClose.call(this.$ele),this.$ele.one(this.animations.start,function(){i=!0}).one(this.animations.end,function(){t(this).remove(),t.isFunction(s.settings.onClosed)&&s.settings.onClosed.call(this)}),setTimeout(function(){i||(s.$ele.remove(),s.settings.onClosed&&s.settings.onClosed(s.$ele))},600)},reposition:function(s){var e=this,i='[data-notify-position="'+this.settings.placement.from+"-"+this.settings.placement.align+'"]:not([data-closing="true"])',n=this.$ele.nextAll(i);this.settings.newest_on_top===!0&&(n=this.$ele.prevAll(i)),n.each(function(){t(this).css(e.settings.placement.from,s),s=parseInt(s)+parseInt(e.settings.spacing)+t(this).outerHeight()})}}),t.notify=function(t,s){var i=new e(this,t,s);return i.notify},t.notifyDefaults=function(s){return i=t.extend(!0,{},i,s)},t.notifyClose=function(s){"warning"===s&&(s="danger"),"undefined"==typeof s||"all"===s?t("[data-notify]").find('[data-notify="dismiss"]').trigger("click"):"success"===s||"info"===s||"warning"===s||"danger"===s?t(".alert-"+s+"[data-notify]").find('[data-notify="dismiss"]').trigger("click"):s?t(s+"[data-notify]").find('[data-notify="dismiss"]').trigger("click"):t('[data-notify-position="'+s+'"]').find('[data-notify="dismiss"]').trigger("click")},t.notifyCloseExcept=function(s){"warning"===s&&(s="danger"),"success"===s||"info"===s||"warning"===s||"danger"===s?t("[data-notify]").not(".alert-"+s).find('[data-notify="dismiss"]').trigger("click"):t("[data-notify]").not(s).find('[data-notify="dismiss"]').trigger("click")}});

        Raptor.msg.bootstrap.info=function(msg,options){
            var opt={
                type: 'info'
            }
            var message={
                icon: 'glyphicon glyphicon-info-sign',
                title: '',
                message: ''
            }
            if(typeof options=='object'){
                opt=options;
            }else{
                
            }
            if(typeof msg=='object'){
                message=msg;
            }else{
                message.message=msg
            }
            $.notify(message,opt);
            
            return;
            var classe='alert-info';
            if(background)
                classe=background;
            var info=$('<div class="alert '+classe+' alert-dismissible" role="alert" style="position: fixed;right: 15px;top: 50px;z-index:100000"></div>');
            var btn=$('<button class="close" data-dismiss="alert" type="button"></button>');
            btn.append('<span aria-hidden="true">�</span>');
            btn.append('<span class="sr-only">Close</span>');
            info.append(btn);
            info.append('<p>'+msg+'</p>');
            $('body').append(info);
            
            var dur=10;
            if(duration!==undefined){
                dur=duration;
            }
            setTimeout(function(){
                info.alert('close');
            },dur*1000);
            
        }
        
        Raptor.msg.bootstrap.error=function(msg){
            var message={
                icon: 'glyphicon glyphicon-info-sign',
                title: '',
                message: ''
            }
            if(typeof msg=='object'){
                message=msg;
            }else{
                message.message=msg
            }
            Raptor.msg.bootstrap.info(message,{
                type:'danger'
            });
        }
        
        Raptor.msg.bootstrap.wait=function(msg){
            
           var m=$('body').Loading({
               msg:msg,
               runIncrement: true
           });
           m.show();
           m.startIncrement();
           return m;
        };
        
        Raptor.msg.bootstrap.confirm=function(title,msg,fn,scope){
            var modal=$('<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true"></div>');
            var dialog=$('<div class="modal-dialog"></div>');
            var content=$('<div class="modal-content"></div>');
            var header=$('<div class="modal-header"><h4 class="modal-title" >'+title+'</h4><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div>');
            var body=$('<div class="modal-body"></div>');
            var footer=$('<div class="modal-footer"></div>');
            
            var no=$('<button type="button" class="btn btn-default" data-dismiss="modal">'+Raptor.msg.getLang('no')+'</button>');
            var yes=$('<button type="button" class="btn btn-primary">'+Raptor.msg.getLang('yes')+'</button>');
            footer.append(yes);
            footer.append(no);
            if(!fn)
                fn=function(){};
            if(!scope)
                scope=this;
            no.click(function(){
                fn.apply(scope,['no']);
            });
            yes.click(function(){
                fn.apply(scope,['yes']);
                $(modal).modal('hide');
            });
            modal.append(dialog);
            dialog.append(content);
            content.append(header);
            content.append(body);
            content.append(footer);
            body.append(msg);
            $('body').append(modal);
            $(modal).modal('show');
        };
        
        Raptor.msg.bootstrap.exception=function(title,msg,trace){
            var modal=$('<div class="modal fade" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true"></div>');
            var dialog=$('<div class="modal-dialog"></div>');
            var content=$('<div class="modal-content"></div>');
            var header=$('<div class="modal-header"><h4 class="modal-title" >'+title+'</h4><button type="button" class="close" data-dismiss="modal"><span aria-hidden="true">&times;</span><span class="sr-only">Close</span></button></div>');
            var body=$('<div class="modal-body"></div>');
            var footer=$('<div class="modal-footer"></div>');
            
            var no=$('<button type="button" class="btn btn-default" data-dismiss="modal">'+Raptor.msg.getLang('close')+'</button>');
            
            
            footer.append(no);
            
            modal.append(dialog);
            dialog.append(content);
            content.append(header);
            content.append(body);
            content.append(footer);
            body.append(msg);
            body.append('<hr>');
            body.append(trace);
            $('body').append(modal);
            $(modal).modal('show');
        };
        
        (function($){

    var Cargando = function(options){
        this.options = $.extend(this.defaults,  options);
        this.init();
    };

    Cargando.prototype = {
        constructor: Cargando
        ,defaults : {
            msg: ''
            ,id: 'idProcedandoMsg'
            ,percent: 0
            ,recursive: true
            ,holder: 'body'
            ,textColor: '#3A87AD'
            ,runIncrement: false
        }
        ,init : function(){
            $('#'+this.options.id).remove();
            
            var $superContainer = $('<div class="procesando-supercontainer" style="text-align:center;background-color: rgba(255,255,255,0.7); z-index: 1100; position: fixed;left:0;top:0;bottom:0;right:0;"></div>')
                .attr('id', this.options.id);
            var $container = $('<div class="progress" style=""></div>');
            var $progress = $('<div class="progress-bar progress-bar-success progress-bar-striped"></div>');
//            var $bar = $('<div style="width: 0%;" class="bar"></div>');
//            $progress.append($bar);
            var $texto = $('<strong></strong>').css({color: this.options.textColor});
            if(this.options.msg && this.options.msg != ''){
                $texto.html(this.options.msg);
            }else{
                $texto.hide();
            }
            var $location=$('<div style="position:absolute;top:50%;width:100%;padding:10px;"></div>');
            $location.append($texto);
            $location.append($container);
            $superContainer.append($location);
            $container.append($progress);
            $superContainer
                .hide()
                .appendTo(this.options.holder);
            //console.log($bar);return;
            this.resetIncrementStatus();
            this.options.cargando = $superContainer;
            if(this.options.runIncrement)
                this.incrementPercent();
            
            return this;
        }
        ,incrementPercent : function(){
            var el = this;
            if(this.options.percent == 100 || !this.options.runIncrement){
                if(this.options.recursive)
                    this.options.percent=0;
                else{
                this.destroy();
				return;
                }
			}
            this.options.percent+=5;
			
            this.options.cargando.find('.progress-bar').css('width', this.options.percent+'%');
            
            setTimeout(function(){
               el.incrementPercent()
            }, 400);
        },
		setPercent : function(percent){
            var el = this;
			this.options.percent=percent;
            if(this.options.percent >= 100 || !this.options.runIncrement){
                this.destroy();
				return;
			}
            
			
            this.options.cargando.find('.progress-bar').css('width', this.options.percent+'%');
            //setTimeout(this.incrementPercent, 250);
        }
        ,show : function(){
            if(!this.options.cargando)
                this.init()
            this.options.cargando.show();
            
            //this.options.cargando.find('.bar').css({'width': '100%'});
            return this;
        }
        ,hide : function(){
            $('#'+this.options.id).fadeOut('fast');
            return this;
        }
        ,destroy : function(){
            var el = this;
            $('#'+this.options.id).fadeOut('fast', function(){
                var data = $(el.options.holder).data();
                $(data).removeProp('Loading');
            });
            return null;
        }
        ,removeMsg : function(){
            if(this.options.cargando){
                var visible = this.options.cargando.is(':visible')
                this.options.cargando.remove();
                this.options.cargando = undefined;
//                if(visible){
//                    this.init()
//                        .show();
//                }
            }
            return this;
        }
        ,setMsg : function(msg){
            this.options.msg = msg;
            if(this.options.cargando){
                var visible = this.options.cargando.is(':visible')
                this.removeMsg();
                if(visible){
                    this.init()
                        .show();
                }
            }
            return this;
        }
        ,startIncrement : function(){
            this.options.runIncrement = true;
            this.incrementPercent();
            return this;
        }
        ,stopIncrement : function(){
            this.options.runIncrement = false;
            return this;
        }
        ,resetIncrementStatus : function(){
            this.options.percent = 0;
            return this;
        }
    };

    var  noConflictVuProcesando = $.fn.Loading;
        

    /*Agrega el procesando el primero de la coleccion seleccionada.*/
    $.fn.Loading = function(options){
        if(this.length){
            var  $element = $(this[0])
                ,plugin = $element.data('Loading');
            if(typeof  options == 'string' &&  plugin){
                if(typeof plugin[options] == 'function')
                    return plugin[options]();
                else
                    return plugin.setMsg(options);
            }else if (    plugin
                && typeof  options == 'object'
                && !(options instanceof Array)
                )
            {
                plugin.destroy();
                options = $.extend(options, {holder: $element});
            }else if(typeof  options == 'string'){
                options = $.extend({msg: options}, {holder: $element});
            }

            if(!plugin){
                plugin = new Cargando(options);
                $element.data('Loading', plugin);
            }

            return plugin;
        }
    }

    
        /*Resolucion de conflictos*/
        $.fn.Loading.noConflict = function () {
            $.fn.Loading = noConflictVuProcesando;
            return this;
        }
    })(jQuery);

}
Raptor.INFO=1;
Raptor.QUESTON=2;
Raptor.ERROR=3;
Raptor.WAIT=4;
Raptor.EXCEPTION=5;
Raptor.DATA=6;
