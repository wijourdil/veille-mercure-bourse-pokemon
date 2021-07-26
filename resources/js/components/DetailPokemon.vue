<template>
  <div class="content fiche pb-3 has-text-centered">
    <span class="is-size-5 is-block">
      # {{ pokemon.id }} {{ pokemon.name }}
    </span>
    <span class="is-size-5 is-block">
      DO {{ pokemon.do }}
    </span>
    <figure v-if="pokemon.image" class="image is-128x128 is-inline-block">
      <img :src="pokemon.image">
    </figure>
  </div>
</template>

<script>
import {Pokemon} from "../classes/Pokemon";

export default {
  name: "DetailPokemon",
  props: {
    pokemon: {
      required: true,
      type: Pokemon
    }
  }
}
</script>

<style scoped>
.fiche {
  width: 200px;
}
</style>
