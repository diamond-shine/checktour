(window.webpackJsonp=window.webpackJsonp||[]).push([[7],{200:function(t,e,r){"use strict";r.r(e);var n=r(2),o=r(7),a=(r(10),r(36)),i={props:{formName:{type:String,required:!0},isCreating:{type:Boolean,required:!1,default:!1},formData:{type:Object,required:!1,default:function(){}}},data:function(){return{}},computed:{cwd:function(){return[{name:this.$t("Параметри"),mark:"parameters"}]},fetchTourOptionsUrl:function(){return"tickets/".concat(this.$props.formData.ticket_id,"/options-autocomplete")}},methods:{onClickCancel:function(){this.$emit("cancel")},onSubmit:function(t){var e=arguments.length>1&&void 0!==arguments[1]&&arguments[1];this.$emit("submit",t,e)},tourOptionsProvider:function(t){this.$data.tour_options=t}}},s=r(1);function c(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function u(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?c(r,!0).forEach((function(e){l(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):c(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function l(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var f={components:{ModelForm:Object(s.a)(i,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-form",{ref:"form",attrs:{data:t.formData,name:t.formName},on:{submit:t.onSubmit},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("el-form",{attrs:{disabled:n.processing,"label-position":"top"},nativeOn:{submit:function(t){return t.preventDefault(),o(t)}}},[r("el-form-item",{attrs:{label:t.$t("Option"),error:n.formatErrors("tour_option_id")}},[r("v-autocomplete",{attrs:{id:"ticket-options-list",url:t.fetchTourOptionsUrl},scopedSlots:t._u([{key:"default",fn:function(e){var o=e.items;e.fetching;return[r("el-select",{attrs:{placeholder:t.$t("Option")},model:{value:n.data.tour_option_id,callback:function(e){t.$set(n.data,"tour_option_id",e)},expression:"form.data.tour_option_id"}},t._l(o,(function(t){return r("el-option",{key:t.id,attrs:{label:t.name,value:t.id}})})),1)]}}],null,!0)})],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Price"),error:n.formatErrors("price")}},[r("el-input",{attrs:{maxlength:255},model:{value:n.data.price,callback:function(e){t.$set(n.data,"price",e)},expression:"form.data.price"}})],1),t._v(" "),r("el-button",{attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Save"))+"\n        ")]),t._v(" "),t.isCreating?r("el-button",{attrs:{type:"primary"},on:{click:function(e){return t.onSubmit(n,!0)}}},[t._v(t._s(t.$t("Save and continue"))+"\n        ")]):t._e(),t._v(" "),r("el-button",{on:{click:t.onClickCancel}},[t._v(t._s(t.$t("Cancel"))+"\n        ")])],1)]}}])})}),[],!1,null,null,null).exports},mixins:[a.b],data:function(){return{isCreating:!1,editingValues:[],model:null,pagination:null,items:[],formData:null}},beforeRouteEnter:function(t,e,r){return r((function(e){return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(e.$filters("ticket-options").restoreFromUrl(t.name));case 2:e.lock(e.list());case 3:case"end":return r.stop()}}))}))},computed:u({backRoute:function(){return{name:"tickets.view",params:{ticketId:this.$routeParam("ticketId")}}}},Object(n.b)([{name:"list.fetch",process:"ticket-options@list.fetch"},{name:"create.store",process:"ticket-options@create.store"},{name:"create.update",process:"ticket-options@create.update"},{name:"delete.destroy",process:"ticket-options@delete.destroy"}])),methods:u({toggleCreateMode:function(){this.$data.isCreating=!this.$data.isCreating,this.$data.isCreating&&this.create()},toggleEditMode:function(t){var e=this.editingValues.indexOf(t);e>-1?this.editingValues.splice(e,1):this.editingValues.push(t)},onChangePage:function(t){this.$filters("ticket-options").data.page=t,this.list()}},Object(o.mapActions)("tickets/options",{list:function(t){var e,r,n,o;return regeneratorRuntime.async((function(a){for(;;)switch(a.prev=a.next){case 0:return a.next=2,regeneratorRuntime.awrap(t("list",{ticketId:this.$routeParam("ticketId")}));case 2:e=a.sent,r=e.items,n=e.pagination,o=e.ticket,this.$data.model=o,this.$data.items=r,this.$data.pagination=n;case 9:case"end":return a.stop()}}),null,this)},store:function(t,e){var r,n=arguments;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return r=n.length>2&&void 0!==n[2]&&n[2],o.next=3,regeneratorRuntime.awrap(t("store",{ticketId:this.$routeParam("ticketId"),form:e}));case 3:return o.next=5,regeneratorRuntime.awrap(this.list());case 5:this.$ee.emit("tickets@list.fetch"),r||this.toggleCreateMode();case 7:case"end":return o.stop()}}),null,this)},update:function(t,e){return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(t("update",{ticketId:this.$routeParam("ticketId"),optionId:e.data.id,form:e}));case 2:return r.next=4,regeneratorRuntime.awrap(this.list());case 4:this.toggleEditMode(e.data.id);case 5:case"end":return r.stop()}}),null,this)},create:function(t){var e;return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(t("create",{ticketId:this.$routeParam("ticketId")}));case 2:e=r.sent,this.formData=e.form;case 4:case"end":return r.stop()}}),null,this)},destroy:function(t,e){return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(this.$vConfirmDelete(this.$t("Do you confirm deleting option?")));case 2:return r.next=4,regeneratorRuntime.awrap(t("destroy",{ticketId:this.$routeParam("ticketId"),optionId:e}));case 4:this.list(),this.$ee.emit("tickets@list.fetch");case 6:case"end":return r.stop()}}),null,this)}}))},d=Object(s.a)(f,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.model.name+" "+t.$t("options"),"back-route":t.backRoute},scopedSlots:t._u([{key:"right",fn:function(){return[!t.isCreating&&t.$can("tickets.edit")?r("button",{staticClass:"btn",attrs:{"aria-label":t.$t("Add"),type:"button"},on:{click:t.toggleCreateMode}},[r("i",{staticClass:"c-icon-add2"})]):t._e()]},proxy:!0}])})]},proxy:!0},{key:"body",fn:function(){return[t.isCreating&&t.formData?r("div",{staticClass:"mb-20"},[r("model-form",{attrs:{"is-creating":!0,"form-data":t.formData,"form-name":"new-value"},on:{submit:t.store,cancel:t.toggleCreateMode}})],1):t._e(),t._v(" "),r("v-content-list",{attrs:{items:t.items,pagination:t.pagination,unbox:""},on:{"change-page":t.onChangePage},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.item;return[t.editingValues.includes(n.id)?r("div",{staticClass:"p-20 u-border-bottom"},[r("model-form",{attrs:{"form-name":"edit-value"+n.id,"form-data":n},on:{submit:t.update,cancel:function(e){return t.toggleEditMode(n.id)}}})],1):r("v-list-item",{key:n.id,attrs:{data:n,"title-by":"tour_option.name","description-by":"price"},scopedSlots:t._u([{key:"aside",fn:function(){return[r("div",{staticClass:"c-list__aside"},[r("div",{staticClass:"c-list__aside-item"},[t.$can("ticket-options.edit")?r("el-button",{attrs:{type:"text",icon:"fal fa-pencil"},on:{click:function(e){return t.toggleEditMode(n.id)}}}):t._e()],1),t._v(" "),r("div",{staticClass:"c-list__aside-item"},[t.$can("tickets.edit")?r("el-button",{attrs:{type:"text",icon:"fal fa-trash-alt"},on:{click:function(e){return t.destroy(n.id)}}}):t._e()],1)])]},proxy:!0}],null,!0)})]}}])})]},proxy:!0}])})}),[],!1,null,null,null);e.default=d.exports},219:function(t,e,r){"use strict";r.r(e);var n=r(7),o=(r(246),r(10),r(36));function a(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function i(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var s={components:{ModelForm:r(252).a},mixins:[o.b],data:function(){return{formData:null,tours:[]}},beforeRouteEnter:function(t,e,r){return r((function(t){return t.lock(t.create())}))},computed:{backRoute:function(){return{name:"tickets.list"}}},methods:function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?a(r,!0).forEach((function(e){i(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):a(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({onClickCancel:function(){this.$refs.form.restore(),this.$router.push(this.backRoute)}},Object(n.mapActions)("tickets",{create:function(t){var e;return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(t("create"));case 2:e=r.sent,this.tours=e.tours,this.$data.formData=e.form,this.$routeParam("tourId")&&(this.formData.tour_id=parseInt(this.$routeParam("tourId")));case 6:case"end":return r.stop()}}),null,this)},store:function(t,e){var r,n;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return o.next=2,regeneratorRuntime.awrap(t("store",{form:e}));case 2:return r=o.sent,n=r.form,o.next=6,regeneratorRuntime.awrap(e.setData(n));case 6:this.$ee.emit("tickets@list.fetch"),this.$router.push(this.backRoute);case 8:case"end":return o.stop()}}),null,this)}}))},c=r(1),u=Object(c.a)(s,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.$t("New ticket"),"back-route":t.backRoute}})]},proxy:!0},{key:"body",fn:function(){return[r("v-form",{ref:"form",attrs:{data:t.formData,name:"tickets"},on:{submit:t.store},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("model-form",{attrs:{form:n,tours:t.tours},on:{submit:o}},[r("el-button",{attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Create"))+"\n                ")]),t._v(" "),r("el-button",{on:{click:t.onClickCancel}},[t._v(t._s(t.$t("Cancel"))+"\n                ")])],1)]}}])})]},proxy:!0}])})}),[],!1,null,null,null);e.default=u.exports},222:function(t,e,r){"use strict";r.r(e);var n=r(7),o=(r(10),r(36)),a=r(252),i=r(2);function s(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,n)}return r}function c(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?s(r,!0).forEach((function(e){u(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):s(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}function u(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var l={mixins:[o.b],components:{ModelForm:a.a},data:function(){return{formData:null,tours:[]}},beforeRouteEnter:function(t,e,r){return r((function(t){return t.lock(t.edit())}))},beforeRouteUpdate:function(t,e,r){t.params.ticketId!==e.params.ticketId&&this.lock(this.edit()),r()},computed:c({backRoute:function(){return{name:"tickets.view",params:{ticketId:this.$routeParam("ticketId")}}}},Object(i.b)([{name:"tickets.update",process:"tickets@edit.update"}])),methods:c({onClickCancel:function(){this.$refs.form.restore(),this.$router.push(this.backRoute)}},Object(n.mapActions)("tickets",{edit:function(t){var e;return regeneratorRuntime.async((function(r){for(;;)switch(r.prev=r.next){case 0:return r.next=2,regeneratorRuntime.awrap(t("edit",{ticketId:this.$routeParam("ticketId")}));case 2:e=r.sent,this.tours=e.tours,this.$data.formData=e.form;case 5:case"end":return r.stop()}}),null,this)},update:function(t,e){var r,n;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return o.next=2,regeneratorRuntime.awrap(t("update",{ticketId:this.$routeParam("ticketId"),form:e}));case 2:return r=o.sent,n=r.form,o.next=6,regeneratorRuntime.awrap(e.setData(n));case 6:this.$ee.emit("tickets@list.fetch"),this.$router.push(this.backRoute);case 8:case"end":return o.stop()}}),null,this)}}))},f=r(1),d=Object(f.a)(l,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.$t("Edit ticket"),"back-route":t.backRoute}})]},proxy:!0},{key:"body",fn:function(){return[r("v-form",{ref:"form",attrs:{data:t.formData,name:"tickets"},on:{submit:t.update},scopedSlots:t._u([{key:"default",fn:function(e){var n=e.form,o=e.submit;return[r("model-form",{attrs:{form:n,tours:t.tours},on:{submit:o}},[r("el-button",{attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Save"))+"\n                ")]),t._v(" "),r("el-button",{on:{click:t.onClickCancel}},[t._v(t._s(t.$t("Cancel"))+"\n                ")])],1)]}}])})]},proxy:!0}])})}),[],!1,null,null,null);e.default=d.exports},239:function(t,e,r){"use strict";var n=r(23),o=r(6);n.b.register("tours.list",(function(t){t.push(Object(o.$t)("Tours"),"tours.list")})),n.b.register("tours.view",(function(t,e){t.parent("tours.list"),t.push(e.name,{name:"tours.view",params:{tourId:e.id}})})),e.a=n.b},246:function(t,e,r){"use strict";var n=r(23),o=(r(239),r(6));n.b.register("tickets.list",(function(t){t.push(Object(o.$t)("Tickets"),"tickets.list")})),n.b.register("tickets.list",(function(t,e){t.parent("tours.view",e),t.push(Object(o.$t)("Tickets"),"tickets.list")})),e.a=n.b},252:function(t,e,r){"use strict";var n={props:{tours:{type:Array,required:!0},form:{type:Object,required:!0},disabled:{type:Boolean,default:!1}},data:function(){return{lock:!0,term:""}},computed:{iconClass:function(){return this.lock?"fal fa-lock-alt":"fal fa-unlock-alt"}},methods:{onSearch:function(t){this.$data.term=t},onSubmit:function(){this.$emit("submit",this.$props.form)},toggle:function(){this.$data.lock=!this.$data.lock}}},o=r(1),a=Object(o.a)(n,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("el-form",{attrs:{disabled:t.disabled,"label-position":"top"},nativeOn:{submit:function(e){return e.preventDefault(),t.onSubmit(e)}}},[r("el-form-item",{attrs:{label:t.$t("Name"),error:t.form.formatErrors("name"),required:""}},[r("el-input",{model:{value:t.form.data.name,callback:function(e){t.$set(t.form.data,"name",e)},expression:"form.data.name"}})],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Bookeo ticket type identifier"),error:t.form.formatErrors("bookeo_type"),required:""}},[r("el-input",{model:{value:t.form.data.bookeo_type,callback:function(e){t.$set(t.form.data,"bookeo_type",e)},expression:"form.data.bookeo_type"}})],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Tour"),error:t.form.formatErrors("tour_id"),required:""}},[r("el-select",{attrs:{disabled:"",placeholder:t.$t("Tour")},model:{value:t.form.data.tour_id,callback:function(e){t.$set(t.form.data,"tour_id",e)},expression:"form.data.tour_id"}},t._l(t.tours,(function(t){return r("el-option",{key:t.id,attrs:{label:t.name,value:t.id}})})),1)],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Price"),error:t.form.formatErrors("price"),required:""}},[r("el-input",{model:{value:t.form.data.price,callback:function(e){t.$set(t.form.data,"price",e)},expression:"form.data.price"}})],1),t._v(" "),r("el-form-item",{attrs:{label:t.$t("Active"),error:t.form.formatErrors("is_active")}},[r("el-switch",{model:{value:t.form.data.is_active,callback:function(e){t.$set(t.form.data,"is_active",e)},expression:"form.data.is_active"}})],1),t._v(" "),r("v-divider"),t._v(" "),t._t("default")],2)}),[],!1,null,null,null);e.a=a.exports}}]);