<template>
  <div ref="modal" class="modal">
    <div class="modal-background"></div>
    <div class="modal-content box">
      <p class="block has-text-centered is-size-4">L'échange a été effectué !</p>

      <div class="block is-flex is-flex-direction-row is-justify-content-space-evenly is-align-items-self-end">
        <div class="has-text-centered">
          <span class="has-text-danger has-text-weight-bold is-size-5">Au revoir,</span>
          <detail-pokemon v-if="leavingPokemon" :pokemon="leavingPokemon"/>
        </div>
        <figure class="image is-128x128">
          <img src="https://openmoji.org/data/color/svg/2B0C.svg">
        </figure>
        <div class="has-text-centered">
          <span class="has-text-success has-text-weight-bold is-size-5">Bienvenue,</span>
          <detail-pokemon v-if="comingPokemon" :pokemon="comingPokemon"/>
        </div>
      </div>

      <div class="block buttons has-addons is-right">
        <button class="button is-link" @click="validateThenHide()">OK !</button>
      </div>
    </div>
  </div>
</template>

<script>
import {eventHub} from "../app";
import DetailPokemon from "./DetailPokemon";
import {Pokemon} from "../classes/Pokemon";

export default {
  name: "TradeModal",
  components: {DetailPokemon},
  data() {
    return {
      leavingPokemon: null,
      comingPokemon: null,
    };
  },
  created() {
    eventHub.$on('modal.show', (pokemons) => {
      this.leavingPokemon = pokemons.leaving;
      this.comingPokemon = pokemons.coming;
      this.show();
    })
  },
  methods: {
    show() {
      this.$refs.modal.classList.add('is-active');
    },
    hide() {
      this.$refs.modal.classList.remove('is-active');
    },
    validateThenHide() {
      eventHub.$emit('pokemon.update', new Pokemon(this.comingPokemon));
      eventHub.$emit('pokedex.update', this.comingPokemon);
      this.hide();

      this.leavingPokemon = null;
      this.comingPokemon = null;
    }
  }
}
</script>
