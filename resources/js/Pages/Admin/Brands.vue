<script setup>
import { Head, useForm, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { Plus, Trash2, AlertCircle, Tag, Check, X as XIcon, Edit, Save } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
  brands: Array
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
        <button @click="openCreateModal" class="inline-flex items-center justify-center gap-2 h-12 px-6 sm:px-8 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-all shadow-md hover:-translate-y-0.5 whitespace-nowrap">
          <Plus class="w-5 h-5" /> Agregar Marca
        </button>
      </div>

      <!-- Tabla de Marcas -->
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
                <th class="p-4 text-right w-1/4">Acciones</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
              <tr v-for="brand in brands" :key="brand.id" class="hover:bg-gray-50 transition-colors">
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
  </AdminLayout>
</template>
