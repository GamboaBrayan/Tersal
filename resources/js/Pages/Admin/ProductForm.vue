<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ArrowLeft, Save, UploadCloud, X } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
  tire: Object,
  brands: Array
});

const isEditing = !!props.tire;

const form = useForm({
  brand_id: props.tire?.brand_id || '',
  model: props.tire?.model || '',
  year: props.tire?.year || '',
  version: props.tire?.version || '',
  width: props.tire?.width || '',
  profile: props.tire?.profile || '',
  rim: props.tire?.rim || '',
  load_index: props.tire?.load_index || '',
  speed_rating: props.tire?.speed_rating || '',
  terrain_type: props.tire?.terrain_type || '',
  is_run_flat: props.tire?.is_run_flat || false,
  description: props.tire?.description || '',
  price: props.tire?.price || '',
  offer_price: props.tire?.offer_price || '',
  stock: props.tire?.stock ?? 10,
  status: props.tire?.status ?? true,
  images: [],
  existing_images: props.tire?.images_json || []
});

const previewImages = ref([]);
const fileInput = ref(null);

const handleFileUpload = (e) => {
  const files = Array.from(e.target.files);
  files.forEach(file => {
    form.images.push(file);
    previewImages.value.push(URL.createObjectURL(file));
  });
};

const removeNewImage = (index) => {
  form.images.splice(index, 1);
  previewImages.value.splice(index, 1);
};

const removeExistingImage = (index) => {
  form.existing_images.splice(index, 1);
};

const submit = () => {
  if (isEditing) {
    // We must use POST with _method=PUT to support multipart/form-data properly in Laravel
    form.transform((data) => ({
      ...data,
      _method: 'PUT',
    })).post(`/admin/inventory/${props.tire.id}`, {
      onSuccess: () => router.visit('/admin/inventory')
    });
  } else {
    form.post('/admin/inventory', {
      onSuccess: () => router.visit('/admin/inventory')
    });
  }
};
</script>

<template>
  <AdminLayout>
    <Head :title="isEditing ? 'Editar Neumático' : 'Nuevo Neumático'" />
    
    <form @submit.prevent="submit" class="flex flex-col h-full">
      <div class="px-4 sm:px-8 py-6 sm:py-10 flex-grow">
        <!-- Header -->
        <div class="flex items-center gap-3 sm:gap-4 mb-6 sm:mb-8">
          <Link href="/admin/inventory" class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-sm text-gray-500 hover:text-primary transition-colors shrink-0">
            <ArrowLeft class="w-5 h-5" />
          </Link>
          <h1 class="text-xl sm:text-3xl font-black text-gray-900 leading-tight">
            {{ isEditing ? 'Editar Neumático' : 'Nuevo Neumático' }}
          </h1>
        </div>

        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 sm:gap-8">
          <!-- Left Column: Specs -->
          <div class="xl:col-span-2 space-y-6 sm:space-y-8">
            
            <!-- Card 2: Technical Specifications -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
              <h2 class="text-lg sm:text-xl font-bold text-primary mb-4 sm:mb-6 border-b border-gray-100 pb-4">Especificaciones Técnicas</h2>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Marca</label>
                  <select v-model="form.brand_id" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    <option value="" disabled>Seleccione una marca</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                  </select>
                  <div v-if="form.errors.brand_id" class="text-red-500 text-xs mt-1">{{ form.errors.brand_id }}</div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Modelo</label>
                  <input type="text" v-model="form.model" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.model" class="text-red-500 text-xs mt-1">{{ form.errors.model }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Año</label>
                  <input type="text" v-model="form.year" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.year" class="text-red-500 text-xs mt-1">{{ form.errors.year }}</div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Versión</label>
                  <input type="text" v-model="form.version" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.version" class="text-red-500 text-xs mt-1">{{ form.errors.version }}</div>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-3 sm:gap-6 mb-4 sm:mb-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Ancho</label>
                  <input type="number" min="0" v-model="form.width" required class="w-full h-12 px-2 sm:px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.width" class="text-red-500 text-xs mt-1">{{ form.errors.width }}</div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Alto</label>
                  <input type="number" min="0" v-model="form.profile" required class="w-full h-12 px-2 sm:px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.profile" class="text-red-500 text-xs mt-1">{{ form.errors.profile }}</div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Rin</label>
                  <input type="number" min="0" v-model="form.rim" required class="w-full h-12 px-2 sm:px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.rim" class="text-red-500 text-xs mt-1">{{ form.errors.rim }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Índice de Carga</label>
                  <input type="number" min="0" v-model="form.load_index" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.load_index" class="text-red-500 text-xs mt-1">{{ form.errors.load_index }}</div>
                </div>
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Cód. de Velocidad</label>
                  <input type="text" v-model="form.speed_rating" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent uppercase text-sm sm:text-base">
                  <div v-if="form.errors.speed_rating" class="text-red-500 text-xs mt-1">{{ form.errors.speed_rating }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4 sm:gap-6 mb-4 sm:mb-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Tipo de Terreno</label>
                  <select v-model="form.terrain_type" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                    <option value="">Seleccione o deje en blanco</option>
                    <option value="H/T">H/T (Highway Terrain)</option>
                    <option value="A/T">A/T (All Terrain)</option>
                    <option value="M/T">M/T (Mud Terrain)</option>
                  </select>
                  <div v-if="form.errors.terrain_type" class="text-red-500 text-xs mt-1">{{ form.errors.terrain_type }}</div>
                </div>
                
                <div class="flex items-center">
                  <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors w-full h-12 sm:mt-6">
                    <input type="checkbox" v-model="form.is_run_flat" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                    <span class="font-bold text-gray-700 text-sm sm:text-base">Tecnología Run Flat</span>
                  </label>
                </div>
              </div>

              <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Descripción</label>
                <textarea v-model="form.description" rows="4" class="w-full p-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base"></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
              </div>
            </div>
          </div>

          <!-- Right Column: Images and Pricing -->
          <div class="space-y-6 sm:space-y-8">
            
            <!-- Card 1: Media Manager -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
              <h2 class="text-lg sm:text-xl font-bold text-primary mb-4 sm:mb-6 border-b border-gray-100 pb-4">Gestor Multimedia</h2>
              
              <div class="border-2 border-dashed border-gray-300 rounded-xl p-6 sm:p-8 text-center bg-gray-50 hover:bg-gray-100 transition-colors cursor-pointer" @click="$refs.fileInput.click()">
                <UploadCloud class="w-8 h-8 sm:w-10 sm:h-10 text-gray-400 mx-auto mb-2" />
                <div class="font-bold text-gray-700 text-sm sm:text-base">Haz click para subir imágenes</div>
                <div class="text-[10px] sm:text-xs text-gray-500 mt-1">PNG, JPG hasta 2MB</div>
                <input type="file" multiple ref="fileInput" @change="handleFileUpload" class="hidden" accept="image/*">
              </div>
              <div v-if="form.errors.images" class="text-red-500 text-xs mt-1">{{ form.errors.images }}</div>

              <!-- Previews -->
              <div class="mt-6 space-y-4">
                <!-- Existing -->
                <div v-for="(img, idx) in form.existing_images" :key="'ex'+idx" class="relative group w-full h-32 rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                  <img :src="`/storage/${img}`" class="w-full h-full object-cover">
                  <button type="button" @click="removeExistingImage(idx)" class="absolute top-2 right-2 w-8 h-8 bg-white/90 text-action rounded-full flex items-center justify-center shadow-sm opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <X class="w-4 h-4" />
                  </button>
                </div>
                <!-- New -->
                <div v-for="(img, idx) in previewImages" :key="'new'+idx" class="relative group w-full h-32 rounded-xl overflow-hidden bg-gray-100 border border-primary">
                  <img :src="img" class="w-full h-full object-cover">
                  <div class="absolute inset-0 bg-primary/10"></div>
                  <button type="button" @click="removeNewImage(idx)" class="absolute top-2 right-2 w-8 h-8 bg-white/90 text-action rounded-full flex items-center justify-center shadow-sm opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <X class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Card 3: Inventory Pricing -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5 sm:p-8">
              <h2 class="text-lg sm:text-xl font-bold text-primary mb-4 sm:mb-6 border-b border-gray-100 pb-4">Inventario y Precios</h2>
              
              <div class="space-y-4 sm:space-y-6">
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Precio Regular (S/.)</label>
                  <input type="number" min="0" step="0.01" v-model="form.price" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-base sm:text-lg font-bold">
                  <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Precio Oferta (S/.) <span class="font-normal text-gray-400">- Opcional</span></label>
                  <input type="number" min="0" step="0.01" v-model="form.offer_price" class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-base sm:text-lg font-bold text-action">
                  <div v-if="form.errors.offer_price" class="text-red-500 text-xs mt-1">{{ form.errors.offer_price }}</div>
                </div>
                
                <div>
                  <label class="block text-sm font-bold text-gray-700 mb-1">Stock Físico</label>
                  <input type="number" min="0" v-model="form.stock" required class="w-full h-12 px-4 rounded-xl border border-gray-200 bg-gray-50 focus:ring-2 focus:ring-primary focus:border-transparent text-sm sm:text-base">
                  <div v-if="form.errors.stock" class="text-red-500 text-xs mt-1">{{ form.errors.stock }}</div>
                </div>

                <div>
                  <label class="flex items-center gap-3 p-4 border border-gray-200 rounded-xl bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors">
                    <input type="checkbox" v-model="form.status" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary">
                    <span class="font-bold text-gray-700 text-sm sm:text-base">Visible en Catálogo</span>
                  </label>
                </div>
              </div>
            </div>

          </div>
        </div>
      </div>

      <!-- Sticky Bottom Bar -->
      <div class="bg-white border-t border-gray-200 px-4 sm:px-8 py-4 sticky bottom-0 z-10 flex flex-col-reverse sm:flex-row justify-end gap-3 sm:gap-4 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
        <Link href="/admin/inventory" class="inline-flex items-center justify-center h-12 px-8 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors w-full sm:w-auto text-sm sm:text-base">
          Cancelar
        </Link>
        <button type="submit" :disabled="form.processing" class="inline-flex items-center justify-center gap-2 h-12 px-8 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto text-sm sm:text-base">
          <Save class="w-5 h-5" /> Guardar Cambios
        </button>
      </div>
    </form>
  </AdminLayout>
</template>
