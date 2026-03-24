<template>
  <div class="container">
    <h1>Laravel 11 + Vue Storefront</h1>
    <p>Fabric: {{ state.fabric_id || 'none' }} | Total: ${{ (state.total / 100).toFixed(2) }}</p>
    <div class="grid">
      <button v-for="fabric in fabrics" :key="fabric.id" @click="selectFabric(fabric)">
        {{ fabric.name }} (+${{ (fabric.price_adjustment / 100).toFixed(2) }})
      </button>
    </div>
    <button @click="saveConfig">Save</button>
    <button @click="placeOrder">Order</button>
    <p>{{ message }}</p>
  </div>
</template>

<script setup>
import { onMounted, reactive, ref } from 'vue';
const api = '/api/v1';
const fabrics = ref([]);
const message = ref('');
const state = reactive({
  email: 'demo@example.com',
  fabric_id: '',
  options: { lapel: 'notch', vents: 'single', pockets: 'flap' },
  total: 0,
  configId: '',
});

async function loadFabrics() {
  const data = await fetch(`${api}/fabrics`).then((r) => r.json());
  fabrics.value = data.data || [];
  if (fabrics.value.length) await selectFabric(fabrics.value[0]);
}

async function selectFabric(fabric) {
  state.fabric_id = fabric.id;
  const preview = await fetch(`${api}/configurations/price-preview`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ fabric_id: state.fabric_id, options: state.options }),
  }).then((r) => r.json());
  state.total = preview.total || 0;
}

async function saveConfig() {
  const data = await fetch(`${api}/me/saved-configurations`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: state.email, fabric_id: state.fabric_id, options: state.options }),
  }).then((r) => r.json());
  state.configId = data.data?.id || '';
  message.value = `Saved ${state.configId}`;
}

async function placeOrder() {
  const data = await fetch(`${api}/orders`, {
    method: 'POST',
    headers: { 'Content-Type': 'application/json' },
    body: JSON.stringify({ email: state.email, configuration_id: state.configId, total: state.total }),
  }).then((r) => r.json());
  message.value = `Order ${data.data?.order_number || 'N/A'}`;
}

onMounted(loadFabrics);
</script>

<style scoped>
.container { font-family: Inter, Arial, sans-serif; max-width: 900px; margin: 20px auto; }
.grid { display: grid; gap: 8px; }
button { padding: 8px 10px; border-radius: 6px; border: 1px solid #cbd5e1; }
</style>
