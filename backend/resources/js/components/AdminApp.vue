<template>
  <div class="container">
    <h1>Laravel 11 + Vue Admin</h1>
    <button @click="loadFabrics">Refresh Fabrics</button>
    <ul>
      <li v-for="fabric in fabrics" :key="fabric.id">{{ fabric.id }} - {{ fabric.name }}</li>
    </ul>
    <button @click="loadOrders">Refresh Orders</button>
    <ul>
      <li v-for="order in orders" :key="order.order_number">{{ order.order_number }} - {{ order.email }}</li>
    </ul>
  </div>
</template>

<script setup>
import { onMounted, ref } from 'vue';
const headers = { 'X-Admin-Key': 'demo-admin-key' };
const fabrics = ref([]);
const orders = ref([]);

async function loadFabrics() {
  const data = await fetch('/api/v1/admin/fabrics', { headers }).then((r) => r.json());
  fabrics.value = data.data || [];
}

async function loadOrders() {
  const data = await fetch('/api/v1/admin/orders', { headers }).then((r) => r.json());
  orders.value = data.data || [];
}

onMounted(async () => {
  await loadFabrics();
  await loadOrders();
});
</script>

<style scoped>
.container { font-family: Inter, Arial, sans-serif; max-width: 900px; margin: 20px auto; }
</style>
