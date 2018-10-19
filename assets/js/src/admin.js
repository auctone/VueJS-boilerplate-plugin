import Vue from 'vue';
import Wrapper from '../vue/wrapper.vue';
window.vue_app = new Vue({
	el: '#rr-starter-app',
	template: '<Wrapper/>',
	components: {Wrapper},
});