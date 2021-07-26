<template>
  <div>
    <div class="content has-text-centered">
      <p class="subtitle">Mon Pokédex : {{ totalPokemonCaught }} / {{ totalPokemon }}</p>
    </div>
    <figure v-for="pokemon in pokedex" class="image is-64x64 is-inline-block" :class="{'is-transparent': !pokemon.caught}">
      <img :src="pokemon.image">
    </figure>
  </div>
</template>

<script>
import {eventHub} from "../app";

export default {
  name: "Pokedex",
  data() {
    return {
      pokedex: []
    }
  },
  computed: {
    totalPokemonCaught() {
      return this.pokedex.filter(item => item.caught).length;
    },
    totalPokemon() {
      return this.pokedex.length;
    }
  },
  async created() {
    const response = await axios.get('/api/pokemon/all');

    response.data.forEach(item => {
      this.pokedex.push({
        id: item.id,
        name: item.name,
        image: item.sprite,
        caught: false
      });
    });

    eventHub.$on('pokedex.update', pokemon => {
      this.pokedex.forEach(item => {
        if (pokemon.id == item.id) {
          item.caught = true;
        }
      });
    })
  }
}
</script>

<style scoped>
.image.is-transparent {
  opacity: 0.33;
}
</style>
