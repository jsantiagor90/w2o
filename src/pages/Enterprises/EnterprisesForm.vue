<template>
  <div class="full-width q-pa-lg items-start">
    <q-card>
      <q-card-section>
        <enterprises-form />
      </q-card-section>
    </q-card>
  </div>
</template>

<script setup>
import EnterprisesForm from '../../components/enterprises/EnterprisesFormComponent'
</script>

<style scoped>
  .full-width {
    width: 100%;
  }
</style>
