(window.webpackJsonp=window.webpackJsonp||[]).push([[20],{197:function(t,e,r){"use strict";r.r(e);var o=r(7),s=(r(10),r(36)),n=(r(2),{props:{form:{type:Object,required:!0}},computed:{isChanged:function(){return this.$props.form.changed("is_recruited")||this.$props.form.changed("is_enquired")||this.$props.form.changed("is_finished")||this.$props.form.changed("tour")||this.$props.form.changed("comment")||this.$props.form.changed("disabled_options")},enabledOptions:{get:function(){var t=this;return this.form.data.tour.tour_options.map((function(t){return t.id})).filter((function(e){return!t.form.data.disabled_options.includes(e)}))},set:function(t){var e=this.form.data.tour.tour_options.map((function(t){return t.id}));this.form.data.disabled_options=e.filter((function(e){return!t.includes(e)}))}}},methods:{onSubmit:function(){this.$emit("submit",this.$props.form)}}}),i=r(1),a=Object(i.a)(n,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("el-form",{attrs:{"label-position":"top"},nativeOn:{submit:function(e){return e.preventDefault(),t.onSubmit(e)}}},[r("el-row",[r("el-col",{attrs:{span:12}},[t.$can("rosters.permit")?r("el-form-item",{attrs:{label:t.$t("Roster recruited"),error:t.form.formatErrors("is_recruited")}},[r("el-switch",{attrs:{disabled:t.form.data.is_finished},model:{value:t.form.data.is_recruited,callback:function(e){t.$set(t.form.data,"is_recruited",e)},expression:"form.data.is_recruited"}})],1):r("v-form-preview",{attrs:{label:t.$t("Roster recruited"),value:t.form.data.is_recruited}}),t._v(" "),t.$can("rosters.process")?r("el-form-item",{attrs:{label:t.$t("Completed"),error:t.form.formatErrors("is_finished")}},[r("el-switch",{attrs:{disabled:!t.form.data.is_recruited},model:{value:t.form.data.is_finished,callback:function(e){t.$set(t.form.data,"is_finished",e)},expression:"form.data.is_finished"}})],1):r("v-form-preview",{attrs:{label:t.$t("Completed"),value:t.form.data.is_finished}})],1),t._v(" "),t.$can("rosters.permit")||t.$can("rosters.process")?r("el-col",{attrs:{span:12}},[t.form.data.tour.tour_options.length?r("el-form-item",{attrs:{label:t.$t("Options")}},[r("el-checkbox-group",{model:{value:t.enabledOptions,callback:function(e){t.enabledOptions=e},expression:"enabledOptions"}},t._l(t.form.data.tour.tour_options,(function(e){return r("div",{key:e.id,staticClass:"mt-10"},[r("el-checkbox",{attrs:{disabled:t.form.data.is_recruited,label:e.id},model:{value:t.enabledOptions,callback:function(e){t.enabledOptions=e},expression:"enabledOptions"}},[t._v(t._s(e.name))])],1)})),0)],1):t._e()],1):t._e()],1),t._v(" "),t.isChanged?[r("el-form-item",{attrs:{error:t.form.formatErrors("comment")}},[r("el-input",{attrs:{type:"textarea",autosize:{minRows:2,maxRows:4},placeholder:"Insert comment"},model:{value:t.form.data.comment,callback:function(e){t.$set(t.form.data,"comment",e)},expression:"form.data.comment"}})],1),t._v(" "),r("el-button",{staticClass:"mb-20",attrs:{type:"success","native-type":"submit"}},[t._v(t._s(t.$t("Apply"))+"\n        ")])]:t._e()],2)}),[],!1,null,null,null).exports,l={props:{form:{type:Object,required:!0}},computed:{isChanged:function(){return this.form.changed("images")},cwd:function(){return[{name:this.$t("Roster"),mark:"rosters_receipts"},{name:"receipt",mark:this.$routeParam("rosterId")}]}},methods:{onSubmit:function(){this.$emit("submit",this.$props.form)}}},m=Object(i.a)(l,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("el-form",{attrs:{"label-position":"top"},nativeOn:{submit:function(e){return e.preventDefault(),t.onSubmit(e)}}},[r("v-form-gallery-list",{attrs:{size:"md",cwd:t.cwd},model:{value:t.form.data.images,callback:function(e){t.$set(t.form.data,"images",e)},expression:"form.data.images"}}),t._v(" "),t.isChanged?r("el-button",{staticClass:"mt-20",attrs:{type:"success","native-type":"submit"}},[t._v("\n        "+t._s(t.$t("Apply"))+"\n    ")]):t._e()],1)}),[],!1,null,null,null).exports,c=r(248);function u(t,e){var r=Object.keys(t);if(Object.getOwnPropertySymbols){var o=Object.getOwnPropertySymbols(t);e&&(o=o.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),r.push.apply(r,o)}return r}function d(t,e,r){return e in t?Object.defineProperty(t,e,{value:r,enumerable:!0,configurable:!0,writable:!0}):t[e]=r,t}var p={mixins:[s.b,s.c],components:{ProcessForm:a,VCommentsList:c.a,Receipts:m},data:function(){return{model:null,comments:[],isProcessing:!1,formData:null,backRoute:{name:"rosters.list"},images:[]}},beforeRouteEnter:function(t,e,r){return r((function(t){return t.lock(t.view())}))},beforeRouteUpdate:function(t,e,r){t.params.bookingId!==e.params.bookingId&&this.lock(this.view(t.params.bookingId)),r()},computed:{noOptionsTitle:function(){return this.model.tour.no_options_titile?this.model.tour.no_options_titile:this.$t("Without options")},ticketsOptionGroups:function(){var t=[];return this.model.tickets_by_types.forEach((function(e){t.includes(e.option_name)||t.push(e.option_name)})),t}},methods:function(t){for(var e=1;e<arguments.length;e++){var r=null!=arguments[e]?arguments[e]:{};e%2?u(r,!0).forEach((function(e){d(t,e,r[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(r)):u(r).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(r,e))}))}return t}({isOptionActive:function(t){return!!t.is_active&&!this.model.disabled_options.includes(t.id)},toggleProcessMode:function(){this.isProcessing=!this.isProcessing}},Object(o.mapActions)("rosters",{view:function(t){var e,r,o=arguments;return regeneratorRuntime.async((function(s){for(;;)switch(s.prev=s.next){case 0:return e=o.length>1&&void 0!==o[1]?o[1]:null,s.next=3,regeneratorRuntime.awrap(t("view",{rosterId:e||this.$routeParam("rosterId")}));case 3:r=s.sent,this.$data.model=r.view,this.formData=r.view,this.comments=r.comments,this.images=[];case 8:case"end":return s.stop()}}),null,this)},update:function(t,e){var r;return regeneratorRuntime.async((function(o){for(;;)switch(o.prev=o.next){case 0:return o.next=2,regeneratorRuntime.awrap(t("update",{rosterId:this.$routeParam("rosterId"),form:e}));case 2:return r=o.sent,o.next=5,regeneratorRuntime.awrap(e.setData(r.view));case 5:this.$data.model=r.view,this.comments=r.comments,this.images=[],this.$ee.emit("rosters@list.fetch");case 9:case"end":return o.stop()}}),null,this)}}))},f=Object(i.a)(p,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("v-sidebar-layout",{attrs:{locked:t.locked},scopedSlots:t._u([{key:"header",fn:function(){return[r("v-sidebar-layout-header",{attrs:{title:t.$t("Roster detail page"),"back-route":t.backRoute}})]},proxy:!0},{key:"body",fn:function(){return[t.$can("tours.list")?r("v-form-preview",{attrs:{label:t.noLabels?null:t.$t("Tour")}},[r("router-link",{key:t.model.tour.id,style:"margin-left: 0px",attrs:{to:{name:"tours.view",params:{tourId:t.model.tour.id}},exact:""}},[t._v("\n                    "+t._s(t.model.tour.name)+"\n            ")])],1):r("v-form-preview",{attrs:{label:t.$t("Tour"),value:t.model.tour.name}}),t._v(" "),t.isGuide?t._e():r("v-form-preview",{attrs:{label:t.$t("Guide"),value:t.model.user.full_name}}),t._v(" "),t.model.tour.tour_options.length?r("v-form-preview",{attrs:{label:t.noLabels?null:t.$t("Options")}},t._l(t.model.tour.tour_options,(function(e){return r("div",{key:t.model.id+"_"+e.id},[r("i",{class:[t.isOptionActive(e)?"u-color-success fal fa-check":"u-color-danger fal fa-times"],staticStyle:{"min-width":"15px"}}),t._v(" \n                "+t._s(e.name)+"\n            ")])})),0):r("v-form-preview",{attrs:{label:t.$t("Options"),noValue:t.$t("No additional options")}}),t._v(" "),r("v-divider"),t._v(" "),t._l(t.ticketsOptionGroups,(function(e){return r("v-form-preview",{key:e,attrs:{label:e||t.noOptionsTitle}},[r("el-row",{staticClass:"pl-5"},[t._l(t.model.tickets_by_types,(function(o){return[o.option_name==e?r("el-col",{key:e+o.id,attrs:{span:8}},[r("v-form-preview",{staticClass:"mb-10",attrs:{label:o.name}},[t._v("\n                            "+t._s(o.quantity)+" "+t._s(t.$t("tickets"))+"\n                        ")])],1):t._e()]}))],2)],1)})),t._v(" "),r("v-divider",{attrs:{hrClass:"mb-10 mt-0"}}),t._v(" "),r("el-row",[r("el-col",{attrs:{span:8}},[r("span",{staticClass:"c-form-preview__label",staticStyle:{display:"contents"}},[t._v(t._s(t.$t("Total price")))])]),t._v(" "),r("el-col",{attrs:{span:8}},[r("v-form-preview",{staticClass:"mb-10"},[r("b",[t._v(t._s(t.model.total_price)+" "+t._s(t.model.tour.currency))])])],1)],1),t._v(" "),r("v-nav-list-group",[r("v-nav-list",[r("v-nav-list-item",{attrs:{route:{name:"bookings.roster-bookings",params:{tourId:t.$routeParam("rosterId")}},title:t.$t("Bookings")}})],1)],1),t._v(" "),r("v-divider"),t._v(" "),r("v-form",{ref:"form",attrs:{data:t.formData,name:"roster"},on:{submit:t.update},scopedSlots:t._u([{key:"default",fn:function(t){var e=t.form,o=t.submit;return[r("process-form",{attrs:{form:e},on:{submit:o}})]}}])}),t._v(" "),r("el-collapse",[r("el-collapse-item",{attrs:{name:"1"}},[r("template",{slot:"title"},[r("label",{staticClass:"el-form-item__label"},[t._v("Receipts")])]),t._v(" "),r("v-form",{ref:"form",staticClass:"ml-0 mr-0",attrs:{data:t.formData,name:"roster_receipts"},on:{submit:t.update},scopedSlots:t._u([{key:"default",fn:function(t){var e=t.form,o=t.submit;return[r("receipts",{attrs:{form:e},on:{submit:o}})]}}])})],2),t._v(" "),t.comments.length?r("el-collapse-item",{attrs:{name:"2"}},[r("template",{slot:"title"},[r("p",[t._v("Comments "),r("b",[t._v("("+t._s(t.comments.length)+")")])])]),t._v(" "),r("v-comments-list",{attrs:{items:t.comments}})],2):t._e()],1)]},proxy:!0},{key:"footer",fn:function(){},proxy:!0}])})}),[],!1,null,null,null);e.default=f.exports},248:function(t,e,r){"use strict";var o={props:{items:{type:Array,required:!0}}},s=r(1),n=Object(s.a)(o,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("el-timeline",{staticClass:"pl-0"},[t._l(t.items,(function(e){return r("el-timeline-item",{key:e.id},[t._v("\n       "+t._s(e.comment)+"\n       "),r("p",{staticStyle:{color:"#7d878a"}},[r("span",[t._v(t._s(e.created_at))]),t._v(" "),r("b",[t._v(t._s(e.first_name)+" "+t._s(e.last_name))])])])})),t._v(" "),r("el-timeline-item",[t._v(" ")])],2)}),[],!1,null,null,null);e.a=n.exports}}]);