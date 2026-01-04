<template>
  <div>
    <h1 class="text-2xl font-bold mb-4">Managers System</h1>
    <div class="bg-white shadow rounded-lg p-6">
      <p>Managers list will be here (connected to backend).</p>
      <!-- Example list -->
      <ul class="mt-4">
        <li v-for="manager in managers" :key="manager.id" class="border-b py-2">
           {{ manager.name }} - {{ manager.email }} 
           <span class="text-xs bg-gray-200 px-2 py-1 rounded">{{ manager.roles[0]?.name }}</span>
        </li>
      </ul>
      <button @click="fetchManagers" class="mt-4 bg-indigo-600 text-white px-4 py-2 rounded">Refresh</button>
    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue'
import api from '../../api/axios'

const managers = ref([])

const fetchManagers = async () => {
  const res = await api.get('/api/managers')
  managers.value = res.data.data
}

onMounted(() => {
  fetchManagers()
})
</script>
