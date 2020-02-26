(window.webpackJsonp=window.webpackJsonp||[]).push([[9],{201:function(t,e,r){"use strict";r.r(e);var n=r(2),o=r(7),a=(r(10),r(36)),s={props:{formName:{type:String,required:!0},isCreating:{type:Boolean,required:!1,default:!1},formData:{type:Object,required:!1,default:function(){}}},data:function(){return{}},computed:{cwd:function(){return[{name:this.$t("Параметри"),mark:"parameters"}]},fetchUserUrl:function(){return"tours/users-autocomplete"}},methods:{onClickCancel:function(){this.$emit("cancel")},onSubmit:function(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1];this.$emit("submit",t,e)}}},i=r(1);function u(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function c(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?u(r,!0).forEach((function(e){l(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):u(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function l(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var f={components:{ModelForm:Object(i.a)(s,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-form",{ref:"form",attrs:{data:t.formData,name:t.formName},on:{submit:t.onSubmit},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("el-form",{attrs:{disabled:n.processing,"label-position":"top"},nativeOn:{submit:function(t){return t.preventDefault(),o(t)}}},[r("el-form-item",{attrs:{label:t.$t("User"),error:n.formatErrors("user_id")}},[r("v-autocomplete",{attrs:{id:"users-list",url:t.fetchUserUrl},scopedSlots:t._u([{key:"default",fn:function(e){var o=e.items;e.fetching;return[r("el-select",{attrs:{placeholder:t.$t("User")},model:{value:n.data.user_id,callback:function(e){t.$set(n.data,"user_id",e)},expression:"form.data.user_id"}},t._l(o,(function(t){return r("el-option",{key:t.id,attrs:{label:t.user_name,value:t.id}})})),1)]}}],null,!0)})],1),t._v(" "),r("el-button",{attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Save"))+"\n        ")]),t._v(" "),t.isCreating?r("el-button",{attrs:{type:"primary"},on:{click:function(e){return t.onSubmit(n,!0)}}},[t._v(t._s(t.$t("Save and continue"))+"\n        ")]):t._e(),t._v(" "),r("el-button",{on:{click:t.onClickCancel}},[t._v(t._s(t.$t("Cancel"))+"\n        ")])],1)]}}])})}),[],!1,null,null,null).exports},mixins:[a.b],data:function(){return{isCreating:!1,editingValues:[],model:null,pagination:null,items:[],formData:null}},beforeRouteEnter:function(t,e,r){return r((function(e){return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(e.$filters("tour-users").restoreFromUrl(t.name));case 2:e.lock(e.list());case 3:case"end":return r.stop()}}))}))},computed:c({backRoute:function(){return{name:"tours.view",params:{tourId:this.$routeParam("tourId")}}}},Object(n.b)([{name:"list.fetch",process:"tour-users@list.fetch"},{name:"create.store",process:"tour-users@create.store"},{name:"delete.destroy",process:"tour-users@delete.destroy"}])),methods:c({getUserName:function(t){return"".concat(t.first_name," ").concat(t.last_name)},toggleCreateMode:function(){this.$data.isCreating=!this.$data.isCreating,this.$data.isCreating&&this.create()},onChangePage:function(t){this.$filters("tour-users").data.page=t,this.list()}},Object(o.mapActions)("tours/users",{list:function(t){var e,r,n,o;return regeneratorRuntime.async((function(a){for(;;)switch(a.prev=a.next){case 0:return a.next=2,regeneratorRuntime.awrap(t("list",{tourId:this.$routeParam("tourId")}));case 2:e=a.sent,r=e.items,n=e.pagination,o=e.tour,this.$data.model=o,this.$data.items=r,this.$data.pagination=n;case 9:case"end":return a.stop()}}),null,this)},store:function(t,e){var r,n=arguments;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return r=n.length>2&&void 0!==n[2]&&n[2],o.next=3,regeneratorRuntime.awrap(t("store",{tourId:this.$routeParam("tourId"),form:e}));case 3:return o.next=5,regeneratorRuntime.awrap(this.list());case 5:this.$ee.emit("tours@list.fetch"),r?e.setData({}):this.toggleCreateMode();case 7:case"end":return o.stop()}}),null,this)},create:function(t){return regeneratorRuntime.async((function(t){for(;;)switch(t.prev=t.next){case 0:this.formData={tour_id:this.$routeParam("tourid")};case 1:case"end":return t.stop()}}),null,this)},destroy:function(t,e){return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(this.$vConfirmDelete(this.$t("Do you confirm deleting user?")));case 2:return r.next=4,regeneratorRuntime.awrap(t("destroy",{tourId:this.$routeParam("tourId"),userId:e}));case 4:this.list(),this.$ee.emit("tickets@list.fetch");case 6:case"end":return r.stop()}}),null,this)}}))},m=Object(i.a)(f,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.$t("Tour users"),"back-route":t.backRoute},scopedSlots:t._u([{key:"right",fn:function(){return[!t.isCreating&&t.$can("tours-users.edit")?r("button",{staticClass:"btn",attrs:{"aria-label":t.$t("Add"),type:"button"},on:{click:t.toggleCreateMode}},[r("i",{staticClass:"c-icon-add2"})]):t._e()]},proxy:!0}])})]},proxy:!0},{key:"body",fn:function(){return[t.isCreating&&t.formData?r("div",{staticClass:"mb-20"},[r("model-form",{attrs:{"is-creating":!0,"form-data":t.formData,"form-name":"new-value"},on:{submit:t.store,cancel:t.toggleCreateMode}})],1):t._e(),t._v(" "),r("v-content-list",{attrs:{items:t.items,pagination:t.pagination,unbox:""},on:{"change-page":t.onChangePage},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.item;return[r("v-list-item",{key:n.id,attrs:{data:n,"title-by":t.getUserName,"description-by":"email"},scopedSlots:t._u([{key:"aside",fn:function(){return[r("div",{staticClass:"c-list__aside"},[r("div",{staticClass:"c-list__aside-item"},[t.$can("tours-users.edit")?r("el-button",{attrs:{type:"text",icon:"fal fa-trash-alt"},on:{click:function(e){return t.destroy(n.id)}}}):t._e()],1)])]},proxy:!0}],null,!0)})]}}])})]},proxy:!0}])})}),[],!1,null,null,null);e.default=m.exports},227:function(t,e,r){"use strict";r.r(e);var n=r(7),o=(r(239),r(10),r(36));function a(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function s(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var i={components:{ModelForm:r(254).a},mixins:[o.b],data:function(){return{formData:null}},beforeRouteEnter:function(t,e,r){return r((function(t){return t.lock(t.create())}))},computed:{backRoute:function(){return{name:"tours.list"}}},methods:function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?a(r,!0).forEach((function(e){s(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):a(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({onClickCancel:function(){this.$refs.form.restore(),this.$router.push(this.backRoute)}},Object(n.mapActions)("tours",{create:function(t){return regeneratorRuntime.async((function(t){for(;;)switch(t.prev=t.next){case 0:case"end":return t.stop()}}))},store:function(t,e){var r,n;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return o.next=2,regeneratorRuntime.awrap(t("store",{form:e}));case 2:return r=o.sent,n=r.form,o.next=6,regeneratorRuntime.awrap(e.setData(n));case 6:this.$ee.emit("tours@list.fetch"),this.$router.push(this.backRoute);case 8:case"end":return o.stop()}}),null,this)}}))},u=r(1),c=Object(u.a)(i,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.$t("New tour"),"back-route":t.backRoute}})]},proxy:!0},{key:"body",fn:function(){return[r("v-form",{ref:"form",attrs:{data:t.formData,name:"tours"},on:{submit:t.store},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("model-form",{attrs:{form:n},on:{submit:o}},[r("el-button",{attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Create"))+"\n                ")]),t._v(" "),r("el-button",{on:{click:t.onClickCancel}},[t._v(t._s(t.$t("Cancel"))+"\n                ")])],1)]}}])})]},proxy:!0}])})}),[],!1,null,null,null);e.default=c.exports},230:function(t,e,r){"use strict";r.r(e);var n=r(7),o=(r(10),r(36)),a=r(254),s=r(2);function i(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function u(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?i(r,!0).forEach((function(e){c(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):i(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function c(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var l={mixins:[o.b],components:{ModelForm:a.a},data:function(){return{formData:null}},beforeRouteEnter:function(t,e,r){return r((function(t){return t.lock(t.edit())}))},beforeRouteUpdate:function(t,e,r){t.params.tourId!==e.params.tourId&&this.lock(this.edit()),r()},computed:u({backRoute:function(){return{name:"tours.view",params:{tourId:this.$routeParam("tourId")}}}},Object(s.b)([{name:"tours.update",process:"tours@edit.update"}])),methods:u({onClickCancel:function(){this.$refs.form.restore(),this.$router.push(this.backRoute)}},Object(n.mapActions)("tours",{edit:function(t){var e,r;return regeneratorRuntime.async((function(n){for(;;)switch(n.prev=n.next){case 0:return n.next=2,regeneratorRuntime.awrap(t("edit",{tourId:this.$routeParam("tourId")}));case 2:e=n.sent,r=e.form,this.$data.formData=r;case 5:case"end":return n.stop()}}),null,this)},update:function(t,e){var r,n;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return o.next=2,regeneratorRuntime.awrap(t("update",{tourId:this.$routeParam("tourId"),form:e}));case 2:return r=o.sent,n=r.form,o.next=6,regeneratorRuntime.awrap(e.setData(n));case 6:this.$ee.emit("tours@list.fetch"),this.$router.push(this.backRoute);case 8:case"end":return o.stop()}}),null,this)}}))},f=r(1),m=Object(f.a)(l,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.$t("Edit tour"),"back-route":t.backRoute}})]},proxy:!0},{key:"body",fn:function(){return[r("v-form",{ref:"form",attrs:{data:t.formData,name:"tours"},on:{submit:t.update},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("model-form",{attrs:{form:n},on:{submit:o}},[r("el-button",{attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Save"))+"\n                ")]),t._v(" "),r("el-button",{on:{click:t.onClickCancel}},[t._v(t._s(t.$t("Cancel"))+"\n                ")])],1)]}}])})]},proxy:!0}])})}),[],!1,null,null,null);e.default=m.exports},239:function(t,e,r){"use strict";var n=r(23),o=r(6);n.b.register("tours.list",(function(t){t.push(Object(o.$t)("Tours"),"tours.list")})),n.b.register("tours.view",(function(t,e){t.parent("tours.list"),t.push(e.name,{name:"tours.view",params:{tourId:e.id}})})),e.a=n.b},254:function(t,e,r){"use strict";var n={props:{form:{type:Object,required:!0},disabled:{type:Boolean,default:!1}},data:function(){return{lock:!0,term:""}},computed:{iconClass:function(){return this.lock?"fal fa-lock-alt":"fal fa-unlock-alt"}},methods:{addItem:function(){this.form.data.bookeo_id.push("")},removeItem:function(t){var e=this.form.data.bookeo_id.findIndex((function(e){return t==e}));e>=0&&this.form.data.bookeo_id.splice(e,1)},onSearch:function(t){this.$data.term=t},onSubmit:function(){this.$emit("submit",this.$props.form)},toggle:function(){this.$data.lock=!this.$data.lock}},beforeMount:function(){this.form.data.bookeo_id||(this.form.data.bookeo_id=[""])}},o=r(1),a=Object(o.a)(n,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("el-form",{attrs:{disabled:t.disabled,"label-position":"top"},nativeOn:{submit:function(e){return e.preventDefault(),t.onSubmit(e)}}},[r("el-form-item",{attrs:{label:t.$t("Name"),error:t.form.formatErrors("name"),required:""}},[r("el-input",{model:{value:t.form.data.name,callback:function(e){t.$set(t.form.data,"name",e)},expression:"form.data.name"}})],1),t._v(" "),r("el-form-item",{staticClass:"mb-0",attrs:{label:t.$t("Bookeo identifier"),error:t.form.formatErrors("bookeo_id"),required:""}},[t._l(t.form.data.bookeo_id,(function(e,n){return[r("el-input",{key:n,staticClass:"mb-20",model:{value:t.form.data.bookeo_id[n],callback:function(e){t.$set(t.form.data.bookeo_id,n,e)},expression:"form.data.bookeo_id[n]"}},[n==t.form.data.bookeo_id.length-1?r("i",{staticClass:"el-input__icon",class:"fal fa-plus",attrs:{slot:"suffix"},on:{click:function(e){return e.preventDefault(),t.addItem()}},slot:"suffix"}):r("i",{staticClass:"el-input__icon",class:"fal fa-times",attrs:{slot:"suffix"},on:{click:function(r){return r.preventDefault(),t.removeItem(e)}},slot:"suffix"})])]}))],2),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Currency"),error:t.form.formatErrors("currency"),required:""}},[r("el-input",{staticClass:"c-slugify",class:{"c-slugify--lock is-disabled":t.lock},attrs:{readonly:t.lock},model:{value:t.form.data.currency,callback:function(e){t.$set(t.form.data,"currency",e)},expression:"form.data.currency"}},[r("i",{staticClass:"el-input__icon",class:t.iconClass,attrs:{slot:"suffix"},on:{click:function(e){return e.preventDefault(),t.toggle(e)}},slot:"suffix"})])],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Title «No options»"),error:t.form.formatErrors("no_options_title")}},[r("el-input",{model:{value:t.form.data.no_options_title,callback:function(e){t.$set(t.form.data,"no_options_title",e)},expression:"form.data.no_options_title"}})],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Active"),error:t.form.formatErrors("is_active")}},[r("el-switch",{model:{value:t.form.data.is_active,callback:function(e){t.$set(t.form.data,"is_active",e)},expression:"form.data.is_active"}})],1),t._v(" "),r("v-divider"),t._v(" "),t._t("default")],2)}),[],!1,null,null,null);e.a=a.exports}}]);