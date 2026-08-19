<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ref, watch } from 'vue';
import { Search, Plus, Package, ChevronLeft, ChevronRight, X, Loader2 } from 'lucide-vue-next';
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
  }, 300);
});

const removeFromPromotions = (tire) => {
  router.post(`/admin/promotions/${tire.id}/toggle`, { is_promoted: false }, {
    preserveScroll: true
  });
};

const showAddModal = ref(false);
const searchInventoryQuery = ref('');
const searchResults = ref([]);
const isSearching = ref(false);

const openAddModal = () => {
  showAddModal.value = true;
  searchInventoryQuery.value = '';
  searchResults.value = [];
  fetchInventoryTires();
};

const fetchInventoryTires = async () => {
  isSearching.value = true;
  try {
    const response = await axios.get('/admin/promotions/search', {
      params: { search: searchInventoryQuery.value }
    });
    searchResults.value = response.data;
  } catch (e) {
    console.error(e);
  } finally {
    isSearching.value = false;
  }
};

let inventorySearchTimeout;
watch(searchInventoryQuery, () => {
  clearTimeout(inventorySearchTimeout);
  inventorySearchTimeout = setTimeout(() => {
    fetchInventoryTires();
  }, 300);
});

const addToPromotions = (tire) => {
  router.post(`/admin/promotions/${tire.id}/toggle`, { is_promoted: true }, {
    preserveScroll: true,
    onSuccess: () => {
      // Remove from search results locally for immediate feedback
      searchResults.value = searchResults.value.filter(t => t.id !== tire.id);
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
          <button @click="openAddModal" class="inline-flex items-center justify-center px-4 h-12 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-sm flex-shrink-0">
            <Plus class="w-5 h-5 mr-2" />
            Añadir de Inventario
          </button>
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

      <!-- Data Table -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse min-w-[700px]">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <th class="p-4">Código</th>
                <th class="p-4">Neumático</th>
                <th class="p-4">Medida</th>
                <th class="p-4">Precio Oferta</th>
                <th class="p-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="tire in promotions.data" :key="tire.id" class="hover:bg-gray-50 transition-colors">
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
                <td class="p-4 font-bold text-gray-700 text-sm sm:text-base">
                  {{ tire.width }}/{{ tire.profile }} R{{ tire.rim }}
                </td>
                <td class="p-4">
                  <div class="font-bold text-primary text-sm sm:text-base">S/. {{ tire.offer_price || tire.price }}</div>
                  <div v-if="tire.offer_price" class="text-xs text-gray-400 line-through">S/. {{ tire.price }}</div>
                </td>
                <td class="p-4 text-right">
                  <button @click="removeFromPromotions(tire)" class="px-3 py-1.5 flex items-center justify-center rounded-lg bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors ml-auto text-sm font-bold">
                    Quitar
                  </button>
                </td>
              </tr>
              <tr v-if="promotions.data.length === 0">
                <td colspan="5" class="p-8 text-center text-gray-500 text-sm sm:text-base">
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

    <!-- Add Modal -->
    <div v-if="showAddModal" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[80vh]">
        <div class="p-4 border-b border-gray-100 flex items-center justify-between">
          <h3 class="text-lg font-bold text-gray-900">Añadir a Promociones</h3>
          <button @click="showAddModal = false" class="text-gray-400 hover:text-gray-700">
            <X class="w-6 h-6" />
          </button>
        </div>
        
        <div class="p-4 bg-gray-50 border-b border-gray-100">
          <div class="relative w-full">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
              <Search class="h-5 w-5 text-gray-400" />
            </div>
            <input v-model="searchInventoryQuery" type="text" placeholder="Buscar en el inventario general..." class="h-10 w-full pl-10 pr-4 rounded-xl border border-gray-200 bg-white text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
          </div>
        </div>

        <div class="flex-grow overflow-y-auto p-4">
          <div v-if="isSearching" class="flex justify-center items-center py-8">
            <Loader2 class="w-8 h-8 text-primary animate-spin" />
          </div>
          <div v-else-if="searchResults.length === 0" class="text-center text-gray-500 py-8">
            No se encontraron resultados en el inventario general.
          </div>
          <div v-else class="space-y-3">
            <div v-for="tire in searchResults" :key="tire.id" class="flex items-center justify-between p-3 border border-gray-200 rounded-xl bg-white hover:border-gray-300 transition-colors">
              <div class="flex items-center gap-3">
                <div class="w-10 h-10 bg-gray-100 rounded-lg overflow-hidden flex-shrink-0 flex items-center justify-center">
                  <img v-if="tire.image_urls && tire.image_urls.length > 0" :src="tire.image_urls[0]" class="w-full h-full object-cover" />
                  <Package class="w-5 h-5 text-gray-400" v-else />
                </div>
                <div>
                  <div class="font-bold text-gray-900 text-sm">{{ tire.brand?.name }} {{ tire.model }}</div>
                  <div class="text-xs text-gray-500">{{ tire.width }}/{{ tire.profile }} R{{ tire.rim }} | Precio: S/. {{ tire.offer_price || tire.price }}</div>
                </div>
              </div>
              <button @click="addToPromotions(tire)" class="px-3 py-1.5 bg-action text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-colors flex-shrink-0">
                Añadir
              </button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
