<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ref, watch, computed } from 'vue';
import { Search, Plus, Package, ChevronLeft, ChevronRight, X, Loader2, Download, Upload, Trash2, Edit2 } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
  promotions: Object,
  filters: Object
});

const searchQuery = ref(props.filters?.search || '');

let searchTimeout;
watch(searchQuery, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/admin/promotions', { search: value }, {
      preserveState: true,
      preserveScroll: true,
      replace: true
    });
  }, 800);
});

const selectedItems = ref([]);

const selectAll = computed({
  get: () => {
    return props.promotions?.data?.length > 0 && selectedItems.value.length === props.promotions.data.length;
  },
  set: (value) => {
    if (value) {
      selectedItems.value = props.promotions.data.map(t => t.id);
    } else {
      selectedItems.value = [];
    }
  }
});

const bulkDelete = () => {
  if (confirm(`¿Estás seguro de eliminar ${selectedItems.value.length} promociones? Esta acción no se puede deshacer.`)) {
    router.post('/admin/promotions/bulk-delete', { ids: selectedItems.value }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedItems.value = [];
      }
    });
  }
};

const removeFromPromotions = (tire) => {
  router.post(`/admin/promotions/${tire.id}/toggle`, { is_promoted: false }, {
    preserveScroll: true
  });
};


const importPromoInput = ref(null);
const isImportingPromo = ref(false);
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

const handleImportPromo = (e) => {
  const file = e.target.files[0];
  if (!file) return;
  
  isImportingPromo.value = true;
  startProgressPolling();
  const formData = new FormData();
  formData.append('file', file);
  
  router.post('/admin/inventory/import-promotions', formData, {
    onSuccess: () => {
      stopProgressPolling();
      importProgress.value = 100;
      setTimeout(() => {
        isImportingPromo.value = false;
        importProgress.value = 0;
      }, 3000);
      if (importPromoInput.value) importPromoInput.value.value = '';
    },
    onError: () => {
      stopProgressPolling();
      isImportingPromo.value = false;
      if (importPromoInput.value) importPromoInput.value.value = '';
    }
  });
};

</script>

<template>
  <AdminLayout>
    <Head title="Promociones" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <!-- Header -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6 sm:mb-8">
        <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Neumáticos en Promoción</h1>
        <div class="flex items-center gap-2">
          <!-- Input Oculto -->
          <input type="file" ref="importPromoInput" @change="handleImportPromo" class="hidden" accept=".xlsx,.xls,.csv" />
          
          <div class="flex border border-orange-200 rounded-xl overflow-hidden shadow-sm">
            <a href="/admin/inventory/template-promotions" class="inline-flex items-center justify-center w-12 h-12 bg-orange-50 text-orange-600 hover:bg-orange-100 transition-colors" title="Descargar Plantilla Promociones">
              <Download class="w-5 h-5" />
            </a>
            <button @click="$refs.importPromoInput.click()" :disabled="isImportingPromo" class="inline-flex items-center justify-center px-4 h-12 bg-white text-orange-600 hover:bg-orange-50 transition-colors text-sm font-bold disabled:opacity-50" title="Importar Excel de Promociones">
              <Upload class="w-5 h-5 mr-2" />
              Importar Excel Especial
            </button>
          </div>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex items-center">
        <div class="relative w-full max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="h-5 w-5 text-gray-400" />
          </div>
          <input v-model="searchQuery" type="text" placeholder="Buscar promociones..." class="h-12 w-full pl-10 pr-4 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
      </div>

      <!-- Bulk Actions Bar -->
      <div v-if="selectedItems.length > 0" class="bg-gray-900 text-white p-4 rounded-2xl shadow-lg mb-6 flex items-center justify-between animate-fade-in-up">
        <div class="flex items-center gap-3">
          <div class="bg-white/20 text-white px-3 py-1 rounded-lg font-bold text-sm">
            {{ selectedItems.length }} seleccionados
          </div>
        </div>
        <div class="flex items-center gap-3">
          <button @click="bulkDelete" class="px-4 py-2 bg-action text-white text-sm font-bold rounded-xl hover:bg-red-600 transition-colors shadow-sm flex items-center gap-2">
            Eliminar
          </button>
          <button @click="selectedItems = []" class="p-2 text-gray-400 hover:text-white transition-colors" title="Cancelar selección">
            <X class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Data Table -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <th class="p-4 w-12">
                  <input type="checkbox" v-model="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                </th>
                <th class="p-4">Código</th>
                <th class="p-4">Neumático</th>
                <th class="p-4">Medida</th>
                <th class="p-4">Precio Oferta</th>
                <th class="p-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="tire in promotions.data" :key="tire.id" class="hover:bg-gray-50 transition-colors" :class="{'bg-primary/5': selectedItems.includes(tire.id)}">
                <td class="p-4">
                  <input type="checkbox" :value="tire.id" v-model="selectedItems" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                </td>
                <td class="p-4 font-bold text-gray-600 text-xs whitespace-nowrap">
                  {{ tire.product_code || '-' }}
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-4">
                    <div class="w-10 h-10 sm:w-12 sm:h-12 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                      <img v-if="tire.image_urls && tire.image_urls.length > 0" :src="tire.image_urls[0]" class="w-full h-full object-cover" />
                      <Package class="w-5 h-5 sm:w-6 sm:h-6 text-gray-400" v-else />
                    </div>
                    <div>
                      <div class="font-bold text-gray-900 text-sm sm:text-base">{{ tire.brand?.name }} {{ tire.model }}</div>
                      <div class="text-xs text-gray-500 font-medium mb-1" v-if="tire.year || tire.version">
                        {{ tire.year }} {{ tire.version }}
                      </div>
                    </div>
                  </div>
                </td>
                <td class="p-4">
                  <span class="font-bold text-gray-700 block mb-1 whitespace-nowrap">{{ tire.width }}{{ tire.profile && tire.profile > 0 ? '/' + tire.profile : '' }} R{{ tire.rim }}</span>
                </td>
                <td class="p-4">
                  <div class="font-bold text-primary text-sm sm:text-base">{{ tire.currency === 'USD' ? '$' : 'S/.' }} {{ tire.offer_price || tire.price }}</div>
                  <div v-if="tire.offer_price" class="text-xs text-gray-400 line-through">{{ tire.currency === 'USD' ? '$' : 'S/.' }} {{ tire.price }}</div>
                </td>
                <td class="p-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <Link :href="`/admin/inventory/${tire.id}/edit`" class="px-3 py-1.5 flex items-center justify-center rounded-lg bg-blue-50 text-primary hover:bg-blue-100 transition-colors text-sm font-bold gap-1">
                      <Edit2 class="w-4 h-4" />
                      <span class="hidden sm:inline">Editar</span>
                    </Link>
                    <button @click="removeFromPromotions(tire)" class="px-3 py-1.5 flex items-center justify-center rounded-lg bg-red-50 text-action hover:bg-red-100 transition-colors text-sm font-bold gap-1">
                      <Trash2 class="w-4 h-4" />
                      <span class="hidden sm:inline">Quitar</span>
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="promotions.data.length === 0">
                <td colspan="6" class="p-8 text-center text-gray-500 text-sm sm:text-base">
                  No hay neumáticos promocionados.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Footer: Total and Pagination -->
      <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm font-bold text-gray-500">
          Total: <span class="text-gray-900">{{ promotions.total || 0 }}</span> promociones
        </div>
        
        <div class="flex flex-wrap justify-center gap-1 sm:gap-2" v-if="promotions.links && promotions.links.length > 3">
          <template v-for="(link, idx) in promotions.links" :key="idx">
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
  </AdminLayout>
</template>
