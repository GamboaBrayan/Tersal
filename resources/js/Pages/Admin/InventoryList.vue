<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ref, watch } from 'vue';
import { Edit2, Trash2, Search, Plus, AlertCircle, Package, Upload, ChevronLeft, ChevronRight } from 'lucide-vue-next';

const props = defineProps({
  tires: Object,
  filters: Object
});

const searchQuery = ref(props.filters?.search || '');

let searchTimeout;
watch(searchQuery, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/admin/inventory', { search: value }, {
      preserveState: true,
      preserveScroll: true,
      replace: true
    });
  }, 300);
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

const importInput = ref(null);
const isImporting = ref(false);
const importProgress = ref(0);
let progressInterval = null;

const startProgressPolling = () => {
  importProgress.value = 0;
  progressInterval = setInterval(async () => {
    try {
      const response = await fetch('/admin/import-progress');
      const data = await response.json();
      if (data.progress !== null) {
        importProgress.value = data.progress;
      }
    } catch (e) {
      console.error(e);
    }
  }, 1000);
};

const stopProgressPolling = () => {
  if (progressInterval) {
    clearInterval(progressInterval);
    progressInterval = null;
  }
};

const handleImport = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  isImporting.value = true;
  startProgressPolling();
  const formData = new FormData();
  formData.append('file', file);
  
  router.post('/admin/inventory/import', formData, {
    onSuccess: () => {
      stopProgressPolling();
      importProgress.value = 100;
      setTimeout(() => {
        isImporting.value = false;
        importProgress.value = 0;
      }, 3000);
      if (importInput.value) importInput.value.value = '';
    },
    onError: () => {
      stopProgressPolling();
      isImporting.value = false;
      if (importInput.value) importInput.value.value = '';
    }
  });
};
</script>

<template>
  <AdminLayout>
    <Head title="Inventario" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Inventario de Llantas</h1>
        <div class="flex items-center gap-2">
          <input type="file" ref="importInput" @change="handleImport" class="hidden" accept=".xlsx,.xls,.csv" />
          <button @click="$refs.importInput.click()" :disabled="isImporting" class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all shadow-sm hover:-translate-y-0.5 flex-shrink-0 disabled:opacity-50" title="Importar Excel">
            <Upload class="w-6 h-6" />
          </button>
          <Link href="/admin/inventory/create" class="inline-flex items-center justify-center w-12 h-12 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-sm flex-shrink-0" title="Nuevo Neumático">
            <Plus class="w-6 h-6" />
          </Link>
        </div>
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
                <th class="p-4">Código</th>
                <th class="p-4">Neumático</th>
                <th class="p-4">Medida</th>
                <th class="p-4">Precio</th>
                <th class="p-4">Stock</th>
                <th class="p-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="tire in tires.data" :key="tire.id" class="hover:bg-gray-50 transition-colors">
                <td class="p-4 font-bold text-gray-600 text-xs whitespace-nowrap">
                  {{ tire.product_code || '-' }}
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                      <img v-if="tire.images_json && tire.images_json.length > 0" :src="`/storage/${tire.images_json[0]}`" class="w-full h-full object-cover" />
                      <Package class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" v-else />
                    </div>
                    <div>
                      <div class="font-bold text-gray-900 text-sm sm:text-base">{{ tire.brand?.name }} {{ tire.model }}</div>
                      <div class="text-xs text-gray-500 font-medium mb-1" v-if="tire.year || tire.version">
                        {{ tire.year }} {{ tire.version }}
                      </div>
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
              <tr v-if="tires.data.length === 0">
                <td colspan="6" class="p-8 text-center text-gray-500 text-sm sm:text-base">
                  No se encontraron neumáticos.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Footer: Total and Pagination -->
      <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm font-bold text-gray-500">
          Total: <span class="text-gray-900">{{ tires.total || 0 }}</span> neumáticos
        </div>
        
        <div class="flex flex-wrap justify-center gap-1 sm:gap-2" v-if="tires.links && tires.links.length > 3">
          <template v-for="(link, idx) in tires.links" :key="idx">
            <Link 
              v-if="link.url"
              :href="link.url" 
              :class="[
                'px-3 py-2 sm:px-4 sm:py-2 border rounded-lg text-xs sm:text-sm font-bold transition-colors flex items-center justify-center',
                link.active ? 'bg-primary text-white border-primary' : 'bg-white border-gray-200 text-gray-700 hover:bg-gray-50'
              ]"
            >
              <ChevronLeft v-if="link.label.toLowerCase().includes('previous')" class="w-4 h-4" />
              <ChevronRight v-else-if="link.label.toLowerCase().includes('next')" class="w-4 h-4" />
              <span v-else v-html="link.label"></span>
            </Link>
            <span 
              v-else
              :class="[
                'px-3 py-2 sm:px-4 sm:py-2 border border-gray-200 rounded-lg text-xs sm:text-sm font-bold text-gray-400 bg-gray-50 flex items-center justify-center'
              ]"
            >
              <ChevronLeft v-if="link.label.toLowerCase().includes('previous')" class="w-4 h-4" />
              <ChevronRight v-else-if="link.label.toLowerCase().includes('next')" class="w-4 h-4" />
              <span v-else v-html="link.label"></span>
            </span>
          </template>
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

    <!-- Toast de Progreso de Importación -->
    <div v-if="isImporting" class="fixed top-4 right-4 z-[200] bg-white rounded-xl shadow-2xl border border-gray-100 p-4 min-w-[280px] flex items-center gap-4 animate-bounce-in">
      <div class="relative w-10 h-10 flex items-center justify-center flex-shrink-0">
        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
          <path class="text-gray-100" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
          <path class="text-primary transition-all duration-300" stroke-dasharray="100, 100" :stroke-dashoffset="100 - importProgress" stroke-width="3" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" />
        </svg>
        <span class="absolute text-[10px] font-bold text-gray-700" v-if="importProgress < 100">{{ importProgress }}%</span>
        <span class="absolute text-green-500" v-else>
          <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
        </span>
      </div>
      <div>
        <h4 class="text-sm font-bold text-gray-900">{{ importProgress < 100 ? 'Importando Llantas...' : '¡Importación Completada!' }}</h4>
        <p class="text-xs text-gray-500">{{ importProgress < 100 ? 'Por favor espera, no cierres esta ventana.' : 'Todos los datos han sido cargados.' }}</p>
      </div>
    </div>
  </AdminLayout>
</template>
