<template>
  <div class="container is-fluid">

    <h1 class="title">Bienvenue sur la Bourse Pokémon</h1>
    <h2 class="subtitle">Venez ici échanger des Pokémon du monde entier</h2>

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

      <div class="field is-grouped">
        <div class="control">
          <button @click="nextStep()" class="button is-link">Continuer</button>
        </div>
      </div>
    </div>

    <div v-if="currentStep === 2">
      <div class="content">
        <p>Bienvenue {{ dresseur.name }} !</p>
        <p>Le Pokémon qui vous a été assigné est {{ pokemon.name }}.</p>
      </div>
    </div>

  </div>
</template>

<script>
export default {
  name: "BoursePokemon",
  data() {
    return {
      currentStep: 1,
      pokemon: {
        name: 'Missingno'
      },
      dresseur: {
        name: 'Blue'
      }
    };
  },
  methods: {
    async nextStep() {
      const response = await fetch('/api/pokemon/random');
      this.pokemon = await response.json();

      this.currentStep++;
    }
  }
}
</script>

<style scoped>

</style>
