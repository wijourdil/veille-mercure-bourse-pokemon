import Vue from "vue";

require('./bootstrap');

Vue.component('bourse-pokemon', require('./components/BoursePokemon.vue').default);

export const eventHub = new Vue();

const app = new Vue({
    el: '#app'
});
