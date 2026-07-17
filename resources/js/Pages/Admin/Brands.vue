<script setup>
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { Plus, Trash2, AlertCircle, Tag, Check, X as XIcon, Edit, Save, Upload, Search, ChevronLeft, ChevronRight } from 'lucide-vue-next';
import { ref, watch } from 'vue';

const props = defineProps({
  brands: Object,
  filters: Object
});

const searchQuery = ref(props.filters?.search || '');

let searchTimeout;
watch(searchQuery, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/admin/brands', { search: value }, {
      preserveState: true,
      preserveScroll: true,
      replace: true
    });
  }, 300);
});

const form = useForm({
  name: '',
  logo: null,
  show_on_home: true
});

const fileInput = ref(null);
const logoPreview = ref(null);
const isEditing = ref(false);
const editingBrandId = ref(null);
const showBrandModal = ref(false);

const handleFileChange = (e) => {
  const file = e.target.files[0];
  if (file) {
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
  } else {
    form.logo = null;
    logoPreview.value = null;
  }
};

const closeBrandModal = () => {
  showBrandModal.value = false;
  isEditing.value = false;
  editingBrandId.value = null;
  form.reset();
  if (fileInput.value) fileInput.value.value = '';
  logoPreview.value = null;
};

const openCreateModal = () => {
  closeBrandModal(); // reset first
  showBrandModal.value = true;
};

const editBrand = (brand) => {
  isEditing.value = true;
  editingBrandId.value = brand.id;
  form.name = brand.name;
  form.show_on_home = !!brand.show_on_home;
  form.logo = null;
  
  if (brand.logo_url) {
    logoPreview.value = '/storage/' + brand.logo_url;
  } else {
    logoPreview.value = null;
  }
  showBrandModal.value = true;
};

const submit = () => {
  if (isEditing.value) {
    form.post(`/admin/brands/${editingBrandId.value}`, {
      onSuccess: () => closeBrandModal(),
      preserveScroll: true
    });
  } else {
    form.post('/admin/brands', {
      onSuccess: () => closeBrandModal(),
      preserveScroll: true
    });
  }
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
  
  router.post('/admin/brands/import', formData, {
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
    <Head title="Marcas" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Marcas de Llantas</h1>
          <p class="text-sm sm:text-base text-gray-500 mt-2">Gestiona el catálogo de marcas de tus neumáticos.</p>
        </div>
        <div class="flex items-center gap-2">
          <input type="file" ref="importInput" @change="handleImport" class="hidden" accept=".xlsx,.xls,.csv" />
          <button @click="$refs.importInput.click()" :disabled="isImporting" class="inline-flex items-center justify-center w-12 h-12 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all shadow-sm hover:-translate-y-0.5 flex-shrink-0 disabled:opacity-50" title="Importar Excel">
            <Upload class="w-6 h-6" />
          </button>
          <button @click="openCreateModal" class="inline-flex items-center justify-center w-12 h-12 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md hover:-translate-y-0.5 flex-shrink-0" title="Agregar Marca">
            <Plus class="w-6 h-6" />
          </button>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex items-center">
        <div class="relative w-full max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="h-5 w-5 text-gray-400" />
          </div>
          <input v-model="searchQuery" type="text" placeholder="Buscar marcas..." class="h-12 w-full pl-10 pr-4 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
      </div>

      <!-- Tabla de Marcas -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between">
          <h2 class="text-lg font-bold text-gray-900">Listado de Marcas</h2>
          <span class="bg-blue-50 text-primary text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full">{{ brands.total }} Marcas</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse table-fixed min-w-[500px]">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <th class="p-4 w-1/2">Marca</th>
                <th class="p-4 text-center w-1/4">Neumáticos Asignados</th>
                <th class="p-4 text-right w-1/4">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="brand in brands.data" :key="brand.id" class="hover:bg-gray-50 transition-colors">
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg border border-gray-200 bg-white flex items-center justify-center flex-shrink-0 p-1">
                      <img v-if="brand.logo_url" :src="'/storage/' + brand.logo_url" class="max-w-full max-h-full object-contain" />
                      <Tag v-else class="w-5 h-5 text-gray-400" />
                    </div>
                    <div class="flex flex-col">
                      <span class="font-bold text-gray-900 text-sm sm:text-base truncate">{{ brand.name }}</span>
                      <span v-if="brand.show_on_home" class="text-xs text-green-600 font-medium flex items-center gap-1 mt-0.5"><Check class="w-3 h-3"/> Visible en Inicio</span>
                      <span v-else class="text-xs text-gray-400 font-medium flex items-center gap-1 mt-0.5"><XIcon class="w-3 h-3"/> Oculto</span>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-center text-gray-500 text-sm font-medium">
                  {{ brand.tires_count }} llantas
                </td>
                <td class="p-4 text-right">
                  <button @click="editBrand(brand)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors cursor-pointer mr-2" title="Editar">
                    <Edit class="w-4 h-4" />
                  </button>
                  <button @click="confirmDelete(brand)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-50 text-action hover:bg-red-100 transition-colors cursor-pointer" title="Eliminar">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <tr v-if="brands.data.length === 0">
                <td colspan="3" class="p-8 text-center text-gray-500 text-sm sm:text-base">
                  No hay marcas registradas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Footer: Total and Pagination -->
      <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm font-bold text-gray-500">
          Total: <span class="text-gray-900">{{ brands.total || 0 }}</span> marcas
        </div>
        
        <div class="flex flex-wrap justify-center gap-1 sm:gap-2" v-if="brands.links && brands.links.length > 3">
          <template v-for="(link, idx) in brands.links" :key="idx">
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

    <!-- Modal: Agregar/Editar Marca -->
    <div v-if="showBrandModal" class="fixed inset-0 z-[90] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
          <h2 class="text-xl font-bold text-gray-900">{{ isEditing ? 'Editar Marca' : 'Nueva Marca' }}</h2>
          <button type="button" @click="closeBrandModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <XIcon class="w-6 h-6" />
          </button>
        </div>
        <form @submit.prevent="submit" class="p-6 flex flex-col gap-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Marca</label>
            <input type="text" v-model="form.name" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base" placeholder="Ej: Michelin">
            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
          </div>
          
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Logo (Opcional)</label>
            <input type="file" ref="fileInput" @change="handleFileChange" accept="image/*" class="w-full h-12 px-4 py-2 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base file:mr-4 file:py-1 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
            <div v-if="form.errors.logo" class="text-red-500 text-xs mt-1">{{ form.errors.logo }}</div>
          </div>

          <div v-if="logoPreview" class="w-20 h-20 rounded-lg overflow-hidden border border-gray-200 bg-white flex items-center justify-center p-1">
            <img :src="logoPreview" class="max-w-full max-h-full object-contain">
          </div>

          <div class="flex items-center gap-2 mt-2">
            <input type="checkbox" id="show_on_home" v-model="form.show_on_home" class="w-4 h-4 text-primary bg-gray-100 border-gray-300 rounded focus:ring-primary">
            <label for="show_on_home" class="text-sm font-medium text-gray-700 cursor-pointer">Mostrar en el carrusel de inicio</label>
          </div>

          <div class="pt-4 flex items-center gap-3 justify-end border-t border-gray-100 mt-2">
            <button type="button" @click="closeBrandModal" class="inline-flex items-center justify-center h-12 px-6 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all cursor-pointer whitespace-nowrap text-sm sm:text-base">
              Cancelar
            </button>
            <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 h-12 px-6 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:-translate-y-0.5 whitespace-nowrap text-sm sm:text-base">
              <Save v-if="isEditing" class="w-5 h-5" />
              <Plus v-else class="w-5 h-5" />
              {{ isEditing ? 'Guardar Cambios' : 'Agregar Marca' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Modal: Confirmar Eliminación -->
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
        <h4 class="text-sm font-bold text-gray-900">{{ importProgress < 100 ? 'Importando Marcas...' : '¡Importación Completada!' }}</h4>
        <p class="text-xs text-gray-500">{{ importProgress < 100 ? 'Por favor espera, no cierres esta ventana.' : 'Todos los datos han sido cargados.' }}</p>
      </div>
    </div>
  </AdminLayout>
</template>
