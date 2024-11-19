<script setup>
import { ref, onMounted, watch } from 'vue';
import draggable from 'vuedraggable';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head } from '@inertiajs/vue3';
import axios from 'axios';
import Snackbar from '@/Components/Snackbar/Snackbar.vue';

// Referências
const snackbar = ref(null);
const showModal = ref(false);
const showEditModal = ref(false);
const newTaskTitle = ref('');
const newTaskDescription = ref('');
const newTaskStatus = ref('pending');
const selectedStatus = ref('');
const sortBy = ref('');
const orderAsc = ref('asc');

// Variáveis para o modal de edição
const editTaskId = ref(null);
const nameOwnerCard = ref(null);
const editTaskTitle = ref('');
const editTaskDescription = ref('');
const editTaskStatus = ref('');

// Colunas de tarefas para agrupamento por status
const taskColumns = ref([
    { title: 'Pendente', value: 'pending', color: '#FFEB3B', items: [] },
    { title: 'Em Andamento', value: 'in_progress', color: '#03A9F4', items: [] },
    { title: 'Bloqueada', value: 'blocked', color: '#F44336', items: [] },
    { title: 'Concluída', value: 'completed', color: '#4CAF50', items: [] },
]);

const statusFilters = ref([
    { title: 'Todos', value: 'all' },
    { title: 'Pendente', value: 'pending' },
    { title: 'Em Andamento', value: 'in_progress' },
    { title: 'Bloqueada', value: 'blocked' },
    { title: 'Concluída', value: 'completed' },
]);

const sortedBy = ref([
    { title: '', value: '' },
    { title: 'Data de Criação', value: 'created_at' },
    { title: 'Data de Atualização', value: 'updated_at' }
])

const orderBy = ref([
    { title: 'Crescente', value: 'asc' },
    { title: 'Decrescente', value: 'desc' }
]);

// Função para buscar tarefas com filtros e ordenação
const fetchTasks = async () => {
    try {
        // Monta os query params com base nos valores selecionados
        const params = {
            status: selectedStatus.value || 'all',  // Passa o status selecionado, se houver
            orderedBy: sortBy.value || '',  // Passa o campo de ordenação selecionado, ou usa 'created_at' como padrão
            sortedBy: orderAsc.value || 'asc'
        };

        // Faz a requisição com os query params
        const response = await axios.get('/task/listarTarefas', { params });
        let tasks = response.data;

        // Agrupamento das tarefas por status
        taskColumns.value.forEach(column => {
            column.items = tasks.filter(task => task.status === column.value);
        });
    } catch (error) {
        console.error('Erro ao buscar tarefas:', error);
    }
};


// Função para abrir o modal de edição e carregar os dados da tarefa
const openEditModal = (task) => {
    editTaskId.value = task.id;
    nameOwnerCard.value = task.name;
    editTaskTitle.value = task.title;
    editTaskDescription.value = task.description;
    editTaskStatus.value = task.status;
    showEditModal.value = true;
};

// Atualiza status da tarefa após arrastar
const updateTaskStatus = async (evt, columnValue) => {
    const taskId = evt.added.element.id;
    const newStatus = columnValue;

    try {
        await axios.put(`/task/atualizarStatusTarefa/${taskId}`, { status: newStatus });
        snackbar.value.showSnackbar({
            type: 'success',
            title: 'Tarefa Atualizada',
            description: `A tarefa foi atualizada com sucesso!`,
        });
    } catch (error) {
        console.error(`Erro ao atualizar status da tarefa ${taskId}:`, error);
        snackbar.value.showSnackbar({
            type: 'error',
            title: 'Erro ao Atualizar Tarefa',
            description: `Erro ao atualizar a tarefa ${taskId}.`,
        });
    }
};

// Função para atualizar a tarefa
const updateTask = async () => {
    try {
        await axios.put(`/task/atualizarTarefa/${editTaskId.value}`, {
            title: editTaskTitle.value,
            description: editTaskDescription.value,
            status: editTaskStatus.value,
        });

        snackbar.value.showSnackbar({
            type: 'success',
            title: 'Tarefa Atualizada',
            description: `A tarefa "${editTaskTitle.value}" foi atualizada com sucesso!`,
        });
        fetchTasks();
        showEditModal.value = false;
    } catch (error) {
        console.error('Erro ao atualizar tarefa:', error);
        snackbar.value.showSnackbar({
            type: 'error',
            title: 'Erro ao Atualizar Tarefa',
            description: 'Erro ao atualizar a tarefa.',
        });
    }
};

// Cria uma nova tarefa
const createTask = async () => {
    try {
        const response = await axios.post('/task/criarTarefa', {
            title: newTaskTitle.value,
            description: newTaskDescription.value,
            status: newTaskStatus.value,
        });

        snackbar.value.showSnackbar({
            type: 'success',
            title: 'Tarefa Criada',
            description: `A tarefa "${newTaskTitle.value}" foi criada com sucesso!`,
        });

        fetchTasks();
        showModal.value = false;
    } catch (error) {
        console.error('Erro ao criar tarefa:', error);
        snackbar.value.showSnackbar({
            type: 'error',
            title: 'Erro ao Criar Tarefa',
            description: `Erro ao criar a tarefa.`,
        });
    }
};

const deleteTask = async (taskId) => {
    try {
        const response = await axios.delete(`/task/deletarTarefa/${taskId}`)
        snackbar.value.showSnackbar({
            type: 'success',
            title: 'Tarefa Deletada',
            description: `A tarefa foi deletada com sucesso!`,
        });
    }
    catch (error) {
        console.error('Erro ao deletar tarefa:', error);
        snackbar.value.showSnackbar({
            type: 'error',
            title: 'Erro ao Deletar Tarefa',
            description: `Erro ao deletar a tarefa.`,
        });
    }
    fetchTasks();
}

// Hooks de ciclo de vida
onMounted(fetchTasks);
watch([selectedStatus, sortBy, orderAsc], () => {
    fetchTasks();
}, { immediate: true });

</script>

<template>

    <Head title="Dashboard - Lista de Tarefas" />

    <AuthenticatedLayout>
        <v-container fluid>
            <!-- Botão para abrir o modal de criação -->
            <v-btn @click="showModal = true" color="#051020" class="mb-4">Criar Tarefa</v-btn>

            <!-- Filtros -->
            <v-row>
                <v-col cols="12" md="4">
                    <v-select v-model="selectedStatus" :items="statusFilters" item-title="title" item-value="value"
                        label="Filtrar por Status" class="elegant-field" outlined placeholder="Todos" />
                </v-col>
                <v-col cols="12" md="4">
                    <v-select v-model="sortBy" :items="sortedBy" label="Ordenar por" class="elegant-field" outlined />
                </v-col>
                <v-col cols="12" md="4" v-if="sortBy">
                    <v-select :items="orderBy" v-model="orderAsc" label="Ordenar Crescente" class="elegant-field"
                        outlined />
                </v-col>
                <v-col cols="12" md="4">
                    <v-btn @click="selectedStatus = ''; sortBy = 'created_at'" color="secondary" outlined>
                        <v-icon>mdi-close-circle</v-icon>
                    </v-btn>
                </v-col>
            </v-row>

            <!-- Colunas de Tarefas (listas agrupadas por status) -->
            <v-row>
                <v-col cols="12" md="3" v-for="column in taskColumns" :key="column.value">
                    <v-card :style="{ backgroundColor: column.color }" class="task-card">
                        <v-card-title>
                            <v-icon left>{{ column.value === 'pending' ? 'mdi-alert-circle' : 'mdi-progress-clock'
                                }}</v-icon>
                            {{ column.title }}
                        </v-card-title>
                        <v-card-text>
                            <draggable class="list-group" :list="column.items" group="tasks"
                                @change="(evt) => updateTaskStatus(evt, column.value)" itemKey="id">
                                <template #item="{ element }">
                                    <v-card class="list-group-item">
                                        <v-card-text>{{ element.title }}</v-card-text>
                                        <v-btn small icon @click="openEditModal(element)">
                                            <v-icon>mdi-pencil</v-icon>
                                        </v-btn>
                                        <v-btn small icon @click="deleteTask(element.id)">
                                            <v-icon>mdi-delete</v-icon>
                                        </v-btn>
                                    </v-card>
                                </template>
                            </draggable>
                        </v-card-text>
                    </v-card>
                </v-col>
            </v-row>
        </v-container>

        <!-- Modal para criar uma nova tarefa -->
        <v-dialog v-model="showModal" max-width="500px">
            <v-card class="modal">
                <v-card-title>
                    <span class="headline">Criar Nova Tarefa</span>
                </v-card-title>
                <v-card-text>
                    <v-text-field v-model="newTaskTitle" color="primary" label="Título da Tarefa" outlined
                        class="elegant-field" />
                    <v-text-field v-model="newTaskDescription" color="primary" label="Descrição da Tarefa" outlined
                        class="elegant-field" />
                    <v-select v-model="newTaskStatus" :items="taskColumns" item-title="title" item-value="value"
                        label="Status" outlined class="elegant-field" />
                </v-card-text>
                <v-card-actions>
                    <v-btn variant="flat" @click="showModal = false" color="secondary">Cancelar</v-btn>
                    <v-btn variant="flat" @click="createTask" color="primary">Criar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Modal para editar uma tarefa -->
        <v-dialog v-model="showEditModal" max-width="500px">
            <v-card class="modal">
                <v-card-title>
                    <span class="headline">Editar Tarefa</span>
                </v-card-title>

                <!-- Nome do Dono com destaque -->
                <v-card-subtitle class="owner-name">
                    <v-icon left>mdi-account-circle</v-icon>
                    <span>{{ nameOwnerCard }}</span>
                </v-card-subtitle>

                <v-card-text>
                    <v-text-field v-model="editTaskTitle" color="primary" label="Título da Tarefa" outlined
                        class="elegant-field" />
                    <v-text-field v-model="editTaskDescription" color="primary" label="Descrição da Tarefa" outlined
                        class="elegant-field" />
                    <v-select v-model="editTaskStatus" :items="taskColumns" item-title="title" item-value="value"
                        label="Status" outlined class="elegant-field" />
                </v-card-text>

                <v-card-actions>
                    <v-btn variant="flat" @click="showEditModal = false" color="secondary">Cancelar</v-btn>
                    <v-btn variant="flat" @click="updateTask" color="primary">Salvar</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>

        <!-- Snackbar -->
        <Snackbar ref="snackbar" />
    </AuthenticatedLayout>
</template>

<style scoped>
.elegant-field
{
    margin-bottom: 20px;
}

.task-card
{
    margin-bottom: 20px;
    color: #fff;
    cursor: grab;
}

.list-group-item
{
    display: flex;
    height: 70px;
    background: #1E2A38;
    color: #fff;
    padding: 10px;
    justify-content: space-around;
    align-items: center;
    margin-bottom: 10px;
}

.owner-name
{
    font-size: 18px;
    font-weight: bold;
    color: #03A9F4;
    margin-bottom: 15px;
    display: flex;
    align-items: center;
}

.owner-name span
{
    margin-left: 8px;
}

.owner-name v-icon
{
    color: #03A9F4;
}
</style>
