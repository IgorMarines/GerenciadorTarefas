<template>
    <v-dialog v-model="isOpen" max-width="600px">
      <v-card>
        <v-card-title>
          <span class="headline">Criar Tarefa</span>
        </v-card-title>
  
        <v-card-text>
          <v-text-field v-model="task.title" label="Título" required />
          <v-textarea v-model="task.description" label="Descrição" required />
          <v-select v-model="task.status" :items="statusOptions" label="Status" required />
        </v-card-text>
  
        <v-card-actions>
          <v-btn text @click="close">Cancelar</v-btn>
          <v-btn color="primary" @click="createTask">Criar Tarefa</v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>
  </template>
  
  <script setup>
  import { ref } from 'vue';
  import axios from 'axios';
  
  // Definição do modal
  const isOpen = ref(false);
  
  // Dados da nova tarefa
  const task = ref({
    title: '',
    description: '',
    status: 'pending',
  });
  
  // Status disponíveis
  const statusOptions = ['pending', 'in_progress', 'blocked', 'completed'];
  
  // Função para fechar o modal
  const close = () => {
    isOpen.value = false;
  };
  
  // Função para criar a tarefa
  const createTask = async () => {
    try {
      await axios.post('/task/criarTarefa', task.value);
      close();
      // Você pode emitir um evento ou chamar uma função para atualizar a lista de tarefas.
    } catch (error) {
      console.error('Erro ao criar a tarefa:', error);
    }
  };
  </script>
  
  <style scoped>
  /* Personalização do estilo do modal, se necessário */
  </style>
  