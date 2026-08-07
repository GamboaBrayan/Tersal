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
      <h1 class="text-xl sm:text-2xl font-semibold text-gray-800 mb-6 sm:mb-8">Panel de Control</h1>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
        <!-- Total Products Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 flex items-center gap-4 sm:gap-6 transition-shadow hover:shadow-sm">
          <div class="w-12 h-12 sm:w-14 sm:h-14 bg-primary/5 text-primary rounded-xl flex items-center justify-center flex-shrink-0 border border-primary/10">
            <Package class="w-6 h-6 sm:w-7 sm:h-7" />
          </div>
          <div>
            <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Llantas Activas</div>
            <div class="text-2xl sm:text-3xl font-semibold text-gray-800">{{ totalProducts }}</div>
          </div>
        </div>

        <!-- Out of Stock Metric -->
        <div class="bg-white rounded-2xl p-5 sm:p-6 border border-gray-100 flex items-center gap-4 sm:gap-6 transition-shadow hover:shadow-sm">
          <div class="w-12 h-12 sm:w-14 sm:h-14 bg-red-50/50 text-red-500 rounded-xl flex items-center justify-center flex-shrink-0 border border-red-100">
            <AlertCircle class="w-6 h-6 sm:w-7 sm:h-7" />
          </div>
          <div>
            <div class="text-xs sm:text-sm font-medium text-gray-500 mb-1">Sin Stock (Agotadas)</div>
            <div class="text-2xl sm:text-3xl font-semibold text-gray-800">{{ outOfStock }}</div>
          </div>
        </div>
      </div>

      <div class="mt-8 sm:mt-12 bg-white rounded-2xl border border-gray-100 p-6 sm:p-8">
        <h2 class="text-lg font-medium text-gray-800 mb-5">Acciones Rápidas</h2>
        <div class="flex flex-col sm:flex-row gap-3">
          <Link href="/admin/inventory/create" class="inline-flex items-center justify-center h-11 px-6 bg-primary/90 text-white font-medium rounded-xl hover:bg-primary transition-colors text-sm">
            + Agregar Nuevo Neumático
          </Link>
          <Link href="/admin/settings" class="inline-flex items-center justify-center h-11 px-6 bg-gray-50 text-gray-600 font-medium rounded-xl hover:bg-gray-100 transition-colors border border-gray-200/50 text-sm">
            Configurar WhatsApp
          </Link>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
