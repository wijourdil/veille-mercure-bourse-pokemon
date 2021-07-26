<template>
  <div class="container is-fluid">

    <h1 class="title">Bienvenue sur la Bourse Pokémon</h1>
    <h2 class="subtitle">Venez ici échanger des Pokémon du monde entier</h2>
    <hr>

    <div class="columns">
      <div class="column">
        <div v-if="currentStep === 1">
          <div class="content">
            <p>Avant de pouvoir accéder à la Bourse Pokémon, veuillez renseigner votre nom de dresseur&nbsp;:</p>
          </div>

          <div class="field">
            <label class="label">Votre nom de dresseur</label>
            <div class="control">
              <input v-model="dresseur.name" class="input" type="text" placeholder="Votre nom de dresseur" required>
            </div>
          </div>

          <div class="content">
            <p>Un Pokémon vous sera assigné au hasard afin de commencer vos échanges.</p>
          </div>

          <div class="field is-grouped">
            <div class="control">
              <button @click="nextStep()" class="button is-link">Continuer</button>
            </div>
          </div>
        </div>

        <div v-if="currentStep === 2">
          <div class="content">
            <p>Bienvenue
              <span class="has-text-link has-text-weight-bold">{{ dresseur.name }}</span> ! (ID: {{ dresseur.id }})</p>
            <p>Votre Pokémon actuel est :</p>
            <detail-pokemon :pokemon="pokemon"></detail-pokemon>
          </div>

          <div class="field is-grouped">
            <div class="control">
              <button v-if="waitingForTrade" disabled class="button is-link">En attente d'échange</button>
              <button v-if="waitingForTrade" @click="cancelPokemonTrade()" class="button is-danger">Annuler l'échange</button>
              <button v-if="!waitingForTrade" @click="proposePokemonToTrade()" class="button is-link">Le proposer à l'échange !</button>
            </div>
          </div>
        </div>
      </div>

      <div v-show="currentStep === 2" class="column">
        <pokedex/>
      </div>
    </div>

    <trade-modal/>

  </div>
</template>

<script>
import DetailPokemon from "./DetailPokemon";
import {Pokemon} from "../classes/Pokemon";
import TradeModal from "./TradeModal";
import {eventHub} from "../app";
import Pokedex from "./Pokedex";

export default {
  name: "BoursePokemon",
  components: {Pokedex, TradeModal, DetailPokemon},
  data() {
    return {
      currentStep: 1,
      waitingForTrade: false,
      pokemon: new Pokemon({
        id: '???',
        name: 'Missingno',
        image: 'https://cdn2.bulbagarden.net/upload/b/b7/Missingno.png',
        do: '???'
      }),
      dresseur: {
        id: this.randomId(),
        name: this.randomName(),
      },
      eventSource: null
    };
  },
  created() {
    eventHub.$on('pokemon.update', (pokemon) => {
      this.pokemon = pokemon;
    });
  },
  methods: {
    async nextStep() {
      const response = await axios.get('/api/pokemon/random');
      const pokemonObject = response.data;

      this.pokemon = new Pokemon({
        id: pokemonObject.id,
        name: pokemonObject.name,
        image: pokemonObject.image,
        do: this.dresseur.name
      });

      eventHub.$emit('pokedex.update', this.pokemon);

      this.currentStep++;
    },

    async cancelPokemonTrade() {
      await axios.delete('/api/pokemon/trade/'.concat(this.dresseur.id, '/', this.pokemon.id));

      this.waitingForTrade = false;
      this.closeEventSource();
    },

    proposePokemonToTrade() {
      this.waitingForTrade = true;

      const url = new URL('https://localhost/.well-known/mercure');
      url.searchParams.append('topic', 'https://example.com/pokemon/trade/' + this.dresseur.id);

      this.eventSource = new EventSource(url);
      this.eventSource.onmessage = this.handleEventSourceMessage;

      axios.post('/api/pokemon/trade/', {
        'pokemon_id': this.pokemon.id,
        'trainer_id': this.dresseur.id,
        'do': this.pokemon.do
      });
    },

    handleEventSourceMessage(response) {
      this.closeEventSource();

      const pokemonData = JSON.parse(response.data);
      const comingPokemon = new Pokemon({
        id: pokemonData.id,
        name: pokemonData.name,
        image: pokemonData.image,
        do: pokemonData.do
      });

      this.waitingForTrade = false;

      eventHub.$emit('modal.show', {
        'leaving': new Pokemon(this.pokemon),
        'coming': comingPokemon
      });
    },

    closeEventSource() {
      this.eventSource.close();
      this.eventSource = null;
    },

    randomId() {
      return Math.random().toString(16).replace('0.', '');
    },

    randomName() {
      const names = ['Blue', 'Red', 'Sacha', 'Regis', 'Alfredo', 'Jesse', 'James', 'Ramoloss'];

      return names[Math.floor(Math.random() * names.length)];
    }
  },
}
</script>

<style scoped>

</style>
