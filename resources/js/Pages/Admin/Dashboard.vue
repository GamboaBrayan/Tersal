<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { Package, AlertCircle } from 'lucide-vue-next';

defineProps({
  totalProducts: Number,
  outOfStock: Number
});
</script>

<template>
  <AdminLayout>
    <Head title="Admin Dashboard" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <h1 class="text-2xl sm:text-3xl font-black text-gray-900 mb-6 sm:mb-8">Panel de Control</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Total Products Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 flex items-center gap-4 sm:gap-6">
          <div class="w-12 h-12 sm:w-14 sm:h-14 bg-blue-50 text-primary rounded-xl flex items-center justify-center flex-shrink-0">
            <Package class="w-6 h-6 sm:w-8 sm:h-8" />
          </div>
          <div>
            <div class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Llantas Activas</div>
            <div class="text-2xl sm:text-3xl font-black text-gray-900">{{ totalProducts }}</div>
          </div>
        </div>

        <!-- Out of Stock Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 shadow-sm border border-gray-100 flex items-center gap-4 sm:gap-6">
          <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-50 text-action rounded-xl flex items-center justify-center flex-shrink-0">
            <AlertCircle class="w-6 h-6 sm:w-8 sm:h-8" />
          </div>
          <div>
            <div class="text-xs sm:text-sm font-bold text-gray-500 uppercase tracking-wider mb-1">Sin Stock (Agotadas)</div>
            <div class="text-2xl sm:text-3xl font-black text-gray-900">{{ outOfStock }}</div>
          </div>
        </div>
      </div>

      <div class="mt-8 sm:mt-12 bg-white rounded-2xl shadow-sm border border-gray-100 p-6 sm:p-8">
        <h2 class="text-lg sm:text-xl font-bold text-gray-900 mb-4">Acciones Rápidas</h2>
        <div class="flex flex-col sm:flex-row gap-3 sm:gap-4">
          <Link href="/admin/inventory/create" class="inline-flex items-center justify-center h-12 px-6 bg-primary text-white font-bold rounded-xl hover:bg-blue-800 transition-colors shadow-sm">
            + Agregar Nuevo Neumático
          </Link>
          <Link href="/admin/settings" class="inline-flex items-center justify-center h-12 px-6 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors shadow-sm">
            Configurar WhatsApp
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
