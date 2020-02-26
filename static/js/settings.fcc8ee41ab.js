(window.webpackJsonp=window.webpackJsonp||[]).push([[6],{196:function(t,e,r){"use strict";r.r(e);var n=r(7),o=r(10),s=r(23),i=r(6);s.b.register("settings.edit",(function(t){t.push(Object(i.$t)("Settings"),"settings.edit")}));var a=s.b,c=r(2);function u(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function l(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var p={props:{form:{required:!0,type:Object}},computed:function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?u(r,!0).forEach((function(e){l(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):u(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({},Object(n.mapState)("system",["user"])),data:function(){return{}},mounted:function(){},destroyed:function(){},methods:{}},f=r(1),m=Object(f.a)(p,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-panel",{staticClass:"c-user-general",attrs:{title:t.$t("Tour options"),description:t.$t("Tour options management"),annotated:""}},[r("v-panel",[r("div",{staticClass:"u-unwrap-bottom"},t._l(t.form.data.options,(function(e,n){return r("el-form-item",{key:n,staticClass:"el-form-item--inline",attrs:{label:e.name,error:t.form.formatErrors("options")}},[r("el-switch",{model:{value:e.is_active,callback:function(r){t.$set(e,"is_active",r)},expression:"item.is_active"}})],1)})),1)])],1)}),[],!1,null,null,null).exports;function b(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function d(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var v={data:function(){return{formData:{days:1},importList:[]}},beforeMount:function(){this.fetchImportsList()},destroyed:function(){},methods:function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?b(r,!0).forEach((function(e){d(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):b(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({initiator:function(t){var e=t.row;t.rowIndex;return e.user?"importList.user":"type"},fetchImportsList:function(){var t,e;return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(o.default.dispatch("settings/importsList"));case 2:t=r.sent,e=t.items,this.importList=e.map((function(t){return t.initiator=t.user?t.user.full_name:t.type,t}));case 5:case"end":return r.stop()}}),null,this)}},Object(n.mapActions)("settings",{update:function(t,e){return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(t("makeImport",{form:e}));case 2:this.fetchImportsList();case 3:case"end":return r.stop()}}),null,this)}}))};function O(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function y(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var g={components:{OptionsBlock:m,ImportsBlock:Object(f.a)(v,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-panel",{attrs:{title:t.$t("Booking import"),description:t.$t("Manual booking import"),annotated:""}},[r("v-panel",[r("div",{staticClass:"c-user-roles"},[r("v-form",{ref:"form",attrs:{data:t.formData,name:"import"},on:{submit:t.update},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("el-row",[r("el-col",{attrs:{span:12}},[r("label",{staticClass:"el-form-item__label"},[t._v(t._s(t.$t("Days ahead")))]),t._v(" "),r("el-input-number",{attrs:{label:t.$t("Days ahead"),size:"small",min:1,max:10},model:{value:n.data.days,callback:function(e){t.$set(n.data,"days",e)},expression:"form.data.days"}})],1),t._v(" "),r("el-col",{staticClass:"u-text-right",attrs:{span:12}},[r("el-button",{staticClass:"mt-40",attrs:{size:"small",type:"success"},on:{click:o}},[t._v(t._s(t.$t("Import bookings")))])],1)],1)]}}])}),t._v(" "),r("v-divider"),t._v(" "),r("el-table",{staticStyle:{width:"100%"},attrs:{data:t.importList}},[r("el-table-column",{attrs:{prop:"created_at",label:"Date",width:"180"}}),t._v(" "),r("el-table-column",{attrs:{prop:"created",label:"Created",width:"80"}}),t._v(" "),r("el-table-column",{attrs:{prop:"updated",label:"Updated",width:"80"}}),t._v(" "),r("el-table-column",{attrs:{prop:"status",label:"status"}}),t._v(" "),r("el-table-column",{attrs:{prop:"initiator",label:"Initiator"}})],1)],1)])],1)}),[],!1,null,null,null).exports},props:{form:{required:!0,type:Object}},computed:function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?O(r,!0).forEach((function(e){y(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):O(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({},Object(n.mapState)("system",["user"])),methods:{onSubmit:function(){this.$emit("submit",this.$props.form)}}};function h(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function w(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?h(r,!0).forEach((function(e){j(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):h(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function j(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var _={form:{current:"user",clearable:!0},components:{ModelForm:Object(f.a)(g,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{staticClass:"u-page"},[r("el-form",{attrs:{"label-position":"top"},nativeOn:{submit:function(e){return e.preventDefault(),t.onSubmit(e)}}},[r("options-block",{attrs:{form:t.form}}),t._v(" "),r("el-button",{directives:[{name:"show",rawName:"v-show",value:!1,expression:"false"}],attrs:{"native-type":"submit"}})],1),t._v(" "),r("imports-block")],1)}),[],!1,null,null,null).exports},data:function(){return{user:null,permissions:[],leaf_only_permissions:null,roles:[],tourOptions:[],grouped:[]}},computed:w({breadcrumbs:function(){return a.generate("settings.edit")}},Object(c.b)([{name:"edit",process:"users@edit",deep:!0}])),beforeRouteEnter:function(t,e,r){var n,s;return regeneratorRuntime.async((function(t){for(;;)switch(t.prev=t.next){case 0:return t.next=2,regeneratorRuntime.awrap(o.default.dispatch("settings/optionsList"));case 2:n=t.sent,s=n.items,r((function(t){var e=t.groupOptions(s);t.$thisForm().setData({options:e}),t.fill({grouped:e,tourOptions:s})}));case 5:case"end":return t.stop()}}))},methods:w({groupOptions:function(t){var e=[];return t.forEach((function(t){var r=e.findIndex((function(e){return e.name==t.name}));r<0?e.push({name:t.name,is_active:t.is_active}):e[r].is_active=e[r].is_active||t.is_active})),e},fill:function(t){this.tourOptions=t.tourOptions,this.grouped=t.grouped},cancel:function(){this.$thisForm().restore(),this.$router.push({name:"users.list"})}},Object(n.mapActions)("settings",{update:function(t){var e,r,n;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return o.next=2,regeneratorRuntime.awrap(t("optionsUpdate",{form:this.$thisForm()}));case 2:e=o.sent,r=e.form,n=this.groupOptions(r),this.fill({grouped:n,tourOptions:r});case 6:case"end":return o.stop()}}),null,this)}}))},P=Object(f.a)(_,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-layout",{attrs:{breadcrumbs:t.breadcrumbs},scopedSlots:t._u([{key:"actions",fn:function(){return[r("el-button",{attrs:{loading:t.psEdit,size:"small",type:"success"},on:{click:t.update}},[t._v(t._s(t.$t("Save"))+"\n        ")]),t._v(" "),r("el-button",{attrs:{loading:t.psEdit,size:"small",type:"default"},on:{click:t.cancel}},[t._v(t._s(t.$t("Cancel"))+"\n        ")])]},proxy:!0},{key:"content",fn:function(){return[r("v-scroll",[r("div",{staticClass:"col-12 u-bg"},[r("div",{staticClass:"col-inner"},[r("model-form",{attrs:{form:t.$thisForm()},on:{submit:t.update}})],1)])])]},proxy:!0}])})}),[],!1,null,null,null);e.default=P.exports}}]);