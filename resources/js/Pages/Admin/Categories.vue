<script setup>
import { Head, useForm, router, Link } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ref, watch, computed } from 'vue';
import { Plus, Trash2, AlertCircle, Edit, Save, Search, ChevronLeft, ChevronRight, ChevronUp, ChevronDown, Tags, X as XIcon } from 'lucide-vue-next';

const props = defineProps({
  categories: Object,
  filters: Object
});

const searchQuery = ref(props.filters?.search || '');

let searchTimeout;
watch(searchQuery, (value) => {
  clearTimeout(searchTimeout);
  searchTimeout = setTimeout(() => {
    router.get('/admin/categories', { search: value }, {
      preserveState: true,
      preserveScroll: true,
      replace: true
    });
  }, 300);
});

const selectedItems = ref([]);

const selectAll = computed({
  get: () => {
    return props.categories?.data?.length > 0 && selectedItems.value.length === props.categories.data.length;
  },
  set: (value) => {
    if (value) {
      selectedItems.value = props.categories.data.map(t => t.id);
    } else {
      selectedItems.value = [];
    }
  }
});

const bulkDelete = () => {
  if (confirm(`¿Estás seguro de eliminar ${selectedItems.value.length} categorías? Esto eliminará todos los neumáticos asociados. Esta acción no se puede deshacer.`)) {
    router.post('/admin/categories/bulk-delete', { ids: selectedItems.value }, {
      preserveScroll: true,
      onSuccess: () => {
        selectedItems.value = [];
      }
    });
  }
};

const form = useForm({
  name: ''
});

const isEditing = ref(false);
const editingCategoryId = ref(null);
const showCategoryModal = ref(false);

const closeCategoryModal = () => {
  showCategoryModal.value = false;
  isEditing.value = false;
  editingCategoryId.value = null;
  form.reset();
};

const openCreateModal = () => {
  closeCategoryModal(); // reset first
  form.defaults({ name: '' });
  showCategoryModal.value = true;
};

const editCategory = (category) => {
  isEditing.value = true;
  editingCategoryId.value = category.id;
  form.name = category.name;
  form.defaults({ name: category.name });
  showCategoryModal.value = true;
};

const submit = () => {
  if (isEditing.value) {
    form.post(`/admin/categories/${editingCategoryId.value}`, {
      onSuccess: () => closeCategoryModal(),
      preserveScroll: true
    });
  } else {
    form.post('/admin/categories', {
      onSuccess: () => closeCategoryModal(),
      preserveScroll: true
    });
  }
};

const moveCategory = (category, direction) => {
  router.post(`/admin/categories/${category.id}/move`, { direction }, {
    preserveScroll: true
  });
};

const showDeleteModal = ref(false);
const categoryToDelete = ref(null);

const confirmDelete = (category) => {
  categoryToDelete.value = category;
  showDeleteModal.value = true;
};

const deleteCategory = () => {
  if (categoryToDelete.value) {
    router.delete(`/admin/categories/${categoryToDelete.value.id}`, {
      onSuccess: () => {
        showDeleteModal.value = false;
        categoryToDelete.value = null;
      }
    });
  }
};
</script>

<template>
  <AdminLayout>
    <Head title="Categorías" />
    
    <div class="px-4 sm:px-8 py-6 sm:py-10">
      <!-- Encabezado -->
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center mb-6 sm:mb-8 gap-4">
        <div>
          <h1 class="text-2xl sm:text-3xl font-black text-gray-900">Categorías de Llantas</h1>
          <p class="text-sm sm:text-base text-gray-500 mt-2">Gestiona las clasificaciones principales (ej: Autos, Camionetas).</p>
        </div>
        <div class="flex items-center gap-2">
          <button @click="openCreateModal" class="inline-flex items-center justify-center w-12 h-12 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md hover:-translate-y-0.5 flex-shrink-0" title="Agregar Categoría">
            <Plus class="w-6 h-6" />
          </button>
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
            <XIcon class="w-5 h-5" />
          </button>
        </div>
      </div>

      <!-- Search Bar -->
      <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 mb-6 flex items-center">
        <div class="relative w-full max-w-md">
          <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
            <Search class="h-5 w-5 text-gray-400" />
          </div>
          <input v-model="searchQuery" type="text" placeholder="Buscar categorías..." class="h-12 w-full pl-10 pr-4 rounded-xl border border-gray-200 bg-gray-50 text-sm focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent transition-all">
        </div>
      </div>

      <!-- Tabla de Categorías -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="p-5 sm:p-6 border-b border-gray-100 flex items-center justify-between">
          <h2 class="text-lg font-bold text-gray-900">Listado de Categorías</h2>
          <span class="bg-blue-50 text-primary text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full">{{ categories.total }} Categorías</span>
        </div>
        <div class="overflow-x-auto">
          <table class="w-full text-left border-collapse table-fixed min-w-[500px]">
            <thead>
              <tr class="bg-gray-50 border-b border-gray-200 text-xs uppercase tracking-wider text-gray-500 font-bold">
                <th class="p-4 w-12">
                  <input type="checkbox" v-model="selectAll" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                </th>
                <th class="p-4">Categoría</th>
                <th class="p-4 text-center">Neumáticos Asignados</th>
                <th class="p-4 text-right">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="category in categories.data" :key="category.id" class="hover:bg-gray-50 transition-colors" :class="{'bg-primary/5': selectedItems.includes(category.id)}">
                <td class="p-4">
                  <input type="checkbox" :value="category.id" v-model="selectedItems" class="rounded border-gray-300 text-primary focus:ring-primary h-4 w-4">
                </td>
                <td class="p-4">
                  <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-lg border border-gray-200 bg-white flex items-center justify-center flex-shrink-0 p-1">
                      <Tags class="w-5 h-5 text-gray-400" />
                    </div>
                    <div class="flex flex-col">
                      <span class="font-bold text-gray-900 text-sm sm:text-base truncate">{{ category.name }}</span>
                    </div>
                  </div>
                </td>
                <td class="p-4 text-center text-gray-500 text-sm font-medium">
                  {{ category.tires_count }} llantas
                </td>
                <td class="p-4 text-right flex items-center justify-end gap-2">
                  <div class="flex items-center bg-gray-100 rounded-lg mr-2 overflow-hidden border border-gray-200" v-if="!searchQuery">
                    <button @click="moveCategory(category, 'up')" :disabled="categories.current_page === 1 && categories.data.indexOf(category) === 0" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-gray-900 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                      <ChevronUp class="w-4 h-4" />
                    </button>
                    <div class="w-px h-4 bg-gray-300"></div>
                    <button @click="moveCategory(category, 'down')" :disabled="categories.current_page === categories.last_page && categories.data.indexOf(category) === categories.data.length - 1" class="w-8 h-8 flex items-center justify-center text-gray-500 hover:bg-gray-200 hover:text-gray-900 transition-colors disabled:opacity-30 disabled:cursor-not-allowed">
                      <ChevronDown class="w-4 h-4" />
                    </button>
                  </div>
                  <button @click="editCategory(category)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-blue-50 text-blue-600 hover:bg-blue-100 transition-colors cursor-pointer" title="Editar">
                    <Edit class="w-4 h-4" />
                  </button>
                  <button @click="confirmDelete(category)" class="w-8 h-8 inline-flex items-center justify-center rounded-lg bg-red-50 text-action hover:bg-red-100 transition-colors cursor-pointer" title="Eliminar">
                    <Trash2 class="w-4 h-4" />
                  </button>
                </td>
              </tr>
              <tr v-if="categories.data.length === 0">
                <td colspan="4" class="p-8 text-center text-gray-500 text-sm sm:text-base">
                  No hay categorías registradas.
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
      
      <!-- Footer: Total and Pagination -->
      <div class="mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="text-sm font-bold text-gray-500">
          Total: <span class="text-gray-900">{{ categories.total || 0 }}</span> categorías
        </div>
        
        <div class="flex flex-wrap justify-center gap-1 sm:gap-2" v-if="categories.links && categories.links.length > 3">
          <template v-for="(link, idx) in categories.links" :key="idx">
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

    <!-- Modal: Agregar/Editar Categoría -->
    <div v-if="showCategoryModal" class="fixed inset-0 z-[90] flex items-center justify-center bg-gray-900/40 backdrop-blur-sm p-4">
      <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden transform transition-all">
        <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
          <h2 class="text-xl font-bold text-gray-900">{{ isEditing ? 'Editar Categoría' : 'Nueva Categoría' }}</h2>
          <button type="button" @click="closeCategoryModal" class="text-gray-400 hover:text-gray-600 transition-colors">
            <XIcon class="w-6 h-6" />
          </button>
        </div>
        <form @submit.prevent="submit" class="p-6 flex flex-col gap-5">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nombre de la Categoría <span class="text-red-500">*</span></label>
            <input type="text" v-model="form.name" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base" placeholder="Ej: Autos">
            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1">{{ form.errors.name }}</div>
          </div>

          <div class="pt-4 flex items-center gap-3 justify-end border-t border-gray-100 mt-2">
            <button type="button" @click="closeCategoryModal" class="inline-flex items-center justify-center h-12 px-6 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-all cursor-pointer whitespace-nowrap text-sm sm:text-base">
              Cancelar
            </button>
            <button type="submit" :disabled="form.processing || !form.isDirty" class="inline-flex items-center justify-center gap-2 h-12 px-6 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-all cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed shadow-md hover:-translate-y-0.5 whitespace-nowrap text-sm sm:text-base">
              <Save v-if="isEditing" class="w-5 h-5" />
              <Plus v-else class="w-5 h-5" />
              {{ isEditing ? 'Guardar Cambios' : 'Agregar Categoría' }}
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
            <h3 class="text-xl font-bold text-gray-900">Eliminar Categoría</h3>
          </div>
          <p class="text-gray-600 text-sm sm:text-base">
            ¿Estás seguro de eliminar la categoría <strong class="text-gray-900">{{ categoryToDelete?.name }}</strong>? Se eliminarán también en cascada <strong>TODAS</strong> las llantas asociadas a ella. Esta acción es irreversible.
          </p>
        </div>
        <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end">
          <button @click="showDeleteModal = false" type="button" class="px-4 py-2 font-bold text-gray-700 bg-white border border-gray-300 rounded-xl hover:bg-gray-50 transition-colors text-sm sm:text-base">
            Cancelar
          </button>
          <button @click="deleteCategory" type="button" class="px-4 py-2 font-bold text-white bg-action rounded-xl hover:bg-red-700 transition-colors shadow-md text-sm sm:text-base">
            Sí, Eliminar
          </button>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>
