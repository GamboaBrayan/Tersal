<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { Plus, Trash2, AlertCircle, Tag } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
  brands: Array
});

const form = useForm({
  name: ''
});

const submit = () => {
  form.post('/admin/brands', {
    onSuccess: () => form.reset(),
    preserveScroll: true
  });
};

const showDeleteModal = ref(false);
const brandToDelete = ref(null);

const confirmDelete = (brand) => {
  brandToDelete.value = brand;
  showDeleteModal.value = true;
};

const deleteBrand = () => {
  if (brandToDelete.value) {
    router.delete(`/admin/brands/${brandToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false;
        brandToDelete.value = null;
      }
    });
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Marcas" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <!-- Header -->
      <div class="mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Marcas de Llantas</h1>
        <p class="text-sm sm:text-base text-gray-500 mt-2">Agrega nuevas marcas para poder catalogar tus neumáticos correctamente.</p>
      </div>

      <div class="space-y-6 sm:space-y-8">
        
        <!-- Left Col: Add Brand -->
        <div>
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-6">
            <h2 class="text-lg font-bold text-gray-900 mb-4">Nueva Marca</h2>
            <form @submit.prevent="submit" class="flex flex-col sm:flex-row gap-3 sm:gap-4">
              <div class="flex-grow">
                <input type="text" v-model="form.name" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base" placeholder="Ej: Michelin">
                <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
              </div>
              <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 h-12 px-6 sm:px-8 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:-translate-y-0.5 whitespace-nowrap w-full sm:w-auto text-sm sm:text-base">
                <Plus class="w-5 h-5" /> Agregar Marca
              </button>
            </form>
          </div>
        </div>

        <!-- Right Col: Brand List -->
        <div>
          <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between">
              <h2 class="text-lg font-bold text-gray-900">Listado de Marcas</h2>
              <span class="bg-blue-50 text-primary text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full">{{ brands.length }} Marcas</span>
            </div>
            <div class="overflow-x-auto">
              <table class="w-full text-left border-collapse table-fixed min-w-[500px]">
                <thead>
                  <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                    <th class="p-4 w-1/2">Marca</th>
                    <th class="p-4 text-center w-1/4">Neumáticos Asignados</th>
                    <th class="p-4 text-right w-1/4">Acción</th>
                  </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                  <tr v-for="brand in brands" :key="brand.id" class="hover:bg-gray-50 transition-colors">
                    <td class="p-4">
                      <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-gray-100 flex items-center justify-center text-gray-400 flex-shrink-0">
                          <Tag class="w-4 h-4" />
                        </div>
                        <span class="font-bold text-gray-900 text-sm sm:text-base truncate">{{ brand.name }}</span>
                      </div>
                    </td>
                    <td class="p-4 text-center text-gray-500 text-sm">
                      {{ brand.tires_count }} llantas
                    </td>
                    <td class="p-4 text-right">
                      <button @click="confirmDelete(brand)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-50 text-action hover:bg-red-100 transition-colors cursor-pointer" title="Eliminar">
                        <Trash2 class="w-4 h-4" />
                      </button>
                    </td>
                  </tr>
                  <tr v-if="brands.length === 0">
                    <td colspan="3" class="p-8 text-center text-gray-500 text-sm sm:text-base">
                      No hay marcas registradas.
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

      </div>
    </div>

    <!-- Delete Modal -->
    <div v-if="showDeleteModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md overflow-hidden transform transition-all">
        <div class="p-6">
          <div class="flex items-center gap-4 mb-4">
            <div class="flex-shrink-0 w-12 h-12 rounded-full bg-red-100 flex items-center justify-center">
              <AlertCircle class="w-6 h-6 text-action" />
            </div>
            <h3 class="text-xl font-bold text-gray-900">Eliminar Marca</h3>
          </div>
          <p class="text-gray-600 text-sm sm:text-base">
            ¿Estás seguro de eliminar la marca <strong class="text-gray-900">{{ brandToDelete?.name }}</strong>? Se eliminarán también en cascada <strong>TODAS</strong> las llantas asociadas a ella. Esta acción es irreversible.
          </p>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end">
          <button @click="showDeleteModal = false" type="button" class="px-4 py-2 font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors text-sm sm:text-base">
            Cancelar
          </button>
          <button @click="deleteBrand" type="button" class="px-4 py-2 font-bold text-white bg-action rounded-xl hover:bg-red-700 transition-colors shadow-md text-sm sm:text-base">
            Sí, Eliminar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
