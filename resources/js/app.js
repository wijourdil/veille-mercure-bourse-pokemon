import Vue from "vue";

require('./bootstrap');

Vue.component('bourse-pokemon', require('./components/BoursePokemon.vue').default);

const app = new Vue({
    el: '#app'
});
