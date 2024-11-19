<template>
    <v-snackbar v-model="visible" :color="snackbarColor" :timeout="3000">
        <strong>{{ title }}</strong>
        <p>{{ description }}</p>
        <v-btn variant="elevated" @click="visible = false">Fechar</v-btn>
    </v-snackbar>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  
  const visible = ref(false);
  const title = ref('');
  const description = ref('');
  const snackbarColor = ref('success'); // Valor inicial (success)
  
  const showSnackbar = ({ type, title: snackbarTitle, description: snackbarDescription }) => {
    title.value = snackbarTitle;
    description.value = snackbarDescription;
    // Define a cor com base no tipo (success = verde, error = vermelho)
    snackbarColor.value = type === 'error' ? 'red' : 'green'; 
    visible.value = true;
  };
  
  // Expondo o método showSnackbar para que o componente pai possa chamá-lo
  defineExpose({
    showSnackbar
  });
  </script>
  