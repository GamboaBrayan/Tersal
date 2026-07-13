<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ref, computed } from 'vue';
import { Edit2, Trash2, Search, Plus, AlertCircle, Package } from 'lucide-vue-next';

const props = defineProps({
  tires: Array
});

const searchQuery = ref('');

const filteredTires = computed(() => {
  if (!searchQuery.value) return props.tires;
  const q = searchQuery.value.toLowerCase();
  return props.tires.filter(tire => {
    return tire.model.toLowerCase().includes(q) || 
           (tire.brand && tire.brand.name.toLowerCase().includes(q)) ||
           `${tire.width}/${tire.profile} R${tire.rim}`.includes(q);
  });
});

const showDeleteModal = ref(false);
const tireToDelete = ref(null);

const confirmDelete = (tire) => {
  tireToDelete.value = tire;
  showDeleteModal.value = true;
};

const deleteTire = () => {
  if (tireToDelete.value) {
    router.delete(`/admin/inventory/${tireToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false;
        tireToDelete.value = null;
      }
    });
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Inventario" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Inventario de Llantas</h1>
        <Link href="/admin/inventory/create" class="inline-flex items-center gap-2 h-12 px-6 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-sm w-full sm:w-auto justify-center">
          <Plus class="w-5 h-5" /> Nuevo Neumático
        </Link>
      </div>

      <!-- Search Bar -->
      <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex items-center">
        <div class="relative w-full max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="h-5 w-5 text-gray-400" />
          </div>
          <input v-model="searchQuery" type="text" placeholder="Buscar por marca, modelo o medida..." class="h-12 w-full pl-10 pr-4 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <th class="p-4">Neumático</th>
                <th class="p-4">Medida</th>
                <th class="p-4">Precio</th>
                <th class="p-4">Stock</th>
                <th class="p-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="tire in filteredTires" :key="tire.id" class="hover:bg-gray-50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                      <img v-if="tire.images_json && tire.images_json.length > 0" :src="`/storage/${tire.images_json[0]}`" class="w-full h-full object-cover" />
                      <Package class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" v-else />
                    </div>
                    <div>
                      <div class="font-bold text-gray-900 text-sm sm:text-base">{{ tire.brand?.name }} {{ tire.model }}</div>
                      <div class="text-xs text-gray-500 line-clamp-1">{{ tire.description || 'Sin descripción' }}</div>
                    </div>
                  </div>
                </td>
                <td class="p-4 font-bold text-gray-700 text-sm sm:text-base">
                  {{ tire.width }}/{{ tire.profile }} R{{ tire.rim }}
                </td>
                <td class="p-4">
                  <div class="font-bold text-primary text-sm sm:text-base">S/. {{ tire.offer_price || tire.price }}</div>
                  <div v-if="tire.offer_price" class="text-xs text-gray-400 line-through">S/. {{ tire.price }}</div>
                </td>
                <td class="p-4">
                  <span :class="[
                    'inline-flex items-center px-2 py-0.5 sm:px-2.5 sm:py-0.5 rounded-full text-[10px] sm:text-xs font-bold',
                    tire.stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-action'
                  ]">
                    {{ tire.stock > 0 ? tire.stock + ' disp.' : 'Agotado' }}
                  </span>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="`/admin/inventory/${tire.id}/edit`" class="w-8 h-8 flex items-center justify-center rounded-lg bg-blue-50 text-primary hover:bg-blue-100 transition-colors">
                      <Edit2 class="w-4 h-4" />
                    </Link>
                    <button @click="confirmDelete(tire)" class="w-8 h-8 flex items-center justify-center rounded-lg bg-red-50 text-action hover:bg-red-100 transition-colors">
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="filteredTires.length === 0">
                <td colspan="5" class="p-8 text-center text-gray-500 text-sm sm:text-base">
                  No se encontraron neumáticos.
                </td>
              </tr>
            </tbody>
          </table>
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
            <h3 class="text-xl font-bold text-gray-900">Eliminar Neumático</h3>
          </div>
          <p class="text-gray-600 text-sm sm:text-base">
            ¿Estás seguro de eliminar <strong class="text-gray-900">{{ tireToDelete?.brand?.name }} {{ tireToDelete?.model }}</strong>? Esta acción no se puede deshacer.
          </p>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end">
          <button @click="showDeleteModal = false" type="button" class="px-4 py-2 font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors text-sm sm:text-base">
            Cancelar
          </button>
          <button @click="deleteTire" type="button" class="px-4 py-2 font-bold text-white bg-action rounded-xl hover:bg-red-700 transition-colors shadow-md text-sm sm:text-base">
            Sí, Eliminar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
