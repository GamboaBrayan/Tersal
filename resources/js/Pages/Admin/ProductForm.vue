<script setup>
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import AdminLayout from './Components/AdminLayout.vue';
import { ArrowLeft, Save, UploadCloud, X, Plus } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
  tire: Object,
  brands: Array,
  categories: Array
});

const isEditing = !!props.tire;

const form = useForm({
  brand_id: props.tire?.brand_id || '',
  category_id: props.tire?.category_id || '',
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
  status: props.tire?.status ?? true,
  images: [],
  image_urls: [],
  existing_images: props.tire?.images_json || []
});

const newUrl = ref('');

const addUrl = () => {
  const totalImages = form.images.length + form.existing_images.length + form.image_urls.length;
  if (newUrl.value && totalImages < 3) {
    form.image_urls.push(newUrl.value);
    newUrl.value = '';
  }
};

const removeUrl = (index) => {
  form.image_urls.splice(index, 1);
};

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
    })).post(`/admin/inventory/${props.tire.id}`);
  } else {
    form.post('/admin/inventory');
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
            <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-gray-100/50 p-6 sm:p-10">
              <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-6 sm:mb-8 flex items-center gap-2">
                <span class="w-2 h-6 bg-primary rounded-full"></span>
                Especificaciones Técnicas
              </h2>
              
              <div class="grid grid-cols-1 md:grid-cols-3 gap-5 sm:gap-7 mb-5 sm:mb-7">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Marca <span class="text-red-500">*</span></label>
                  <select v-model="form.brand_id" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                    <option value="" disabled>Seleccione una marca</option>
                    <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                  </select>
                  <div v-if="form.errors.brand_id" class="text-red-500 text-xs mt-1">{{ form.errors.brand_id }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Categoría <span class="text-red-500">*</span></label>
                  <select v-model="form.category_id" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                    <option value="" disabled>Seleccione una categoría</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                  </select>
                  <div v-if="form.errors.category_id" class="text-red-500 text-xs mt-1">{{ form.errors.category_id }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Modelo <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.model" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.model" class="text-red-500 text-xs mt-1">{{ form.errors.model }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-7 mb-5 sm:mb-7">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Año</label>
                  <input type="text" pattern="\d*" maxlength="4" placeholder="Ej: 2024" @input="form.year = form.year.replace(/\D/g, '')" v-model="form.year" class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.year" class="text-red-500 text-xs mt-1">{{ form.errors.year }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Versión</label>
                  <input type="text" v-model="form.version" class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.version" class="text-red-500 text-xs mt-1">{{ form.errors.version }}</div>
                </div>
              </div>

              <div class="grid grid-cols-3 gap-4 sm:gap-7 mb-5 sm:mb-7">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Ancho <span class="text-red-500">*</span></label>
                  <input type="number" min="0" v-model="form.width" required class="w-full h-12 px-3 sm:px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.width" class="text-red-500 text-xs mt-1">{{ form.errors.width }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Alto <span class="text-red-500">*</span></label>
                  <input type="number" min="0" v-model="form.profile" required class="w-full h-12 px-3 sm:px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.profile" class="text-red-500 text-xs mt-1">{{ form.errors.profile }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Rin <span class="text-red-500">*</span></label>
                  <input type="number" min="0" v-model="form.rim" required class="w-full h-12 px-3 sm:px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.rim" class="text-red-500 text-xs mt-1">{{ form.errors.rim }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-7 mb-5 sm:mb-7">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Índice de Carga <span class="text-red-500">*</span></label>
                  <input type="number" min="0" v-model="form.load_index" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.load_index" class="text-red-500 text-xs mt-1">{{ form.errors.load_index }}</div>
                </div>
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Índice de Velocidad <span class="text-red-500">*</span></label>
                  <input type="text" v-model="form.speed_rating" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary uppercase text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.speed_rating" class="text-red-500 text-xs mt-1">{{ form.errors.speed_rating }}</div>
                </div>
              </div>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-5 sm:gap-7 mb-5 sm:mb-7">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Tipo de Terreno</label>
                  <select v-model="form.terrain_type" class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700">
                    <option value="">Seleccione o deje en blanco</option>
                    <option value="H/T">H/T (Highway Terrain)</option>
                    <option value="A/T">A/T (All Terrain)</option>
                    <option value="M/T">M/T (Mud Terrain)</option>
                  </select>
                  <div v-if="form.errors.terrain_type" class="text-red-500 text-xs mt-1">{{ form.errors.terrain_type }}</div>
                </div>
                
                <div class="flex items-center">
                  <label class="flex items-center justify-between px-5 py-3 border border-gray-200/60 rounded-xl bg-gray-50/50 cursor-pointer hover:bg-gray-50 hover:border-gray-300 transition-colors w-full h-12 sm:mt-6">
                    <span class="font-medium text-gray-700 text-sm sm:text-base">Tecnología Run Flat</span>
                    <input type="checkbox" v-model="form.is_run_flat" class="w-5 h-5 text-primary rounded border-gray-300 focus:ring-primary/20 focus:ring-offset-0 transition-shadow">
                  </label>
                </div>
              </div>

              <div>
                <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Descripción</label>
                <textarea v-model="form.description" rows="4" class="w-full p-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-primary/20 focus:border-primary text-sm sm:text-base text-gray-700 resize-none"></textarea>
                <div v-if="form.errors.description" class="text-red-500 text-xs mt-1">{{ form.errors.description }}</div>
              </div>
            </div>
          </div>

          <!-- Right Column: Images and Pricing -->
          <div class="space-y-6 sm:space-y-8">
            
            <!-- Card 1: Media Manager -->
            <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-gray-100/50 p-6 sm:p-8">
              <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-6 sm:mb-8 flex items-center gap-2">
                <span class="w-2 h-6 bg-blue-500 rounded-full"></span>
                Gestor Multimedia
              </h2>
              
              <div class="border-2 border-dashed border-gray-200/80 rounded-2xl p-6 sm:p-8 text-center bg-gray-50/30 hover:bg-gray-50 transition-colors cursor-pointer mb-5" @click="$refs.fileInput.click()">
                <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-sm mx-auto mb-4">
                  <UploadCloud class="w-8 h-8 text-blue-500" />
                </div>
                <div class="font-bold text-gray-700 text-sm sm:text-base">Subir imágenes locales</div>
                <div class="text-xs text-gray-400 mt-1">PNG, JPG hasta 2MB (Máx. 3 en total)</div>
                <input type="file" multiple ref="fileInput" @change="handleFileUpload" class="hidden" accept="image/*">
              </div>
              <div v-if="form.errors.images" class="text-red-500 text-xs mt-1 mb-4">{{ form.errors.images }}</div>

              <!-- Input de URL -->
              <div class="flex items-center gap-2">
                <input type="url" v-model="newUrl" @keyup.enter="addUrl" placeholder="Añadir URL de imagen (http://...)" class="flex-grow h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 focus:ring-2 focus:ring-blue-500/20 focus:border-blue-500 text-sm transition-colors text-gray-700">
                <button type="button" @click="addUrl" :disabled="(form.images.length + form.existing_images.length + form.image_urls.length) >= 3" class="w-12 h-12 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 hover:text-blue-600 transition-all shadow-sm shrink-0 flex items-center justify-center disabled:opacity-50 disabled:hover:text-gray-700">
                  <Plus class="w-5 h-5" />
                </button>
              </div>

              <!-- Previews -->
              <div class="mt-6 space-y-4">
                <!-- Existing -->
                <div v-for="(img, idx) in form.existing_images" :key="'ex'+idx" class="relative group w-full h-32 rounded-xl overflow-hidden bg-gray-100 border border-gray-200">
                  <img :src="img.startsWith('http') ? img : `${usePage().props.storage_url}/${img}`" class="w-full h-full object-cover">
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
                <!-- URLs -->
                <div v-for="(img, idx) in form.image_urls" :key="'url'+idx" class="relative group w-full h-32 rounded-xl overflow-hidden bg-gray-100 border border-blue-500">
                  <img :src="img" class="w-full h-full object-cover" onerror="this.src='https://placehold.co/400x400?text=Error+de+URL'">
                  <div class="absolute inset-0 bg-blue-500/10"></div>
                  <button type="button" @click="removeUrl(idx)" class="absolute top-2 right-2 w-8 h-8 bg-white/90 text-action rounded-full flex items-center justify-center shadow-sm opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition-opacity">
                    <X class="w-4 h-4" />
                  </button>
                </div>
              </div>
            </div>

            <!-- Card 3: Inventory Pricing -->
            <div class="bg-white rounded-3xl shadow-[0_4px_20px_rgba(0,0,0,0.02)] border border-gray-100/50 p-6 sm:p-8">
              <h2 class="text-base sm:text-lg font-bold text-gray-800 mb-6 sm:mb-8 flex items-center gap-2">
                <span class="w-2 h-6 bg-green-500 rounded-full"></span>
                Inventario y Precios
              </h2>
              
              <div class="space-y-5 sm:space-y-6">
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Precio Regular (S/.) <span class="text-red-500">*</span></label>
                  <input type="number" min="0" step="0.01" v-model="form.price" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-base sm:text-lg font-bold text-gray-800">
                  <div v-if="form.errors.price" class="text-red-500 text-xs mt-1">{{ form.errors.price }}</div>
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2 flex items-center justify-between">
                    Precio Oferta (S/.) <span class="text-[10px] bg-gray-100 px-2 py-0.5 rounded text-gray-400">Opcional</span>
                  </label>
                  <input type="number" min="0" step="0.01" v-model="form.offer_price" class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-base sm:text-lg font-bold text-action">
                  <div v-if="form.errors.offer_price" class="text-red-500 text-xs mt-1">{{ form.errors.offer_price }}</div>
                </div>
                
                <div>
                  <label class="block text-xs font-semibold text-gray-500 uppercase tracking-wider mb-2">Stock Físico <span class="text-red-500">*</span></label>
                  <input type="number" min="0" v-model="form.stock" required class="w-full h-12 px-4 rounded-xl border border-gray-200/60 bg-gray-50/50 hover:bg-gray-50 transition-colors focus:ring-2 focus:ring-green-500/20 focus:border-green-500 text-sm sm:text-base text-gray-700">
                  <div v-if="form.errors.stock" class="text-red-500 text-xs mt-1">{{ form.errors.stock }}</div>
                </div>

                <div>
                  <label class="flex items-center justify-between px-5 py-4 border border-gray-200/60 rounded-xl bg-gray-50/50 cursor-pointer hover:bg-gray-50 hover:border-gray-300 transition-colors">
                    <span class="font-medium text-gray-700 text-sm sm:text-base">Visible en Catálogo</span>
                    <input type="checkbox" v-model="form.status" class="w-5 h-5 text-green-500 rounded border-gray-300 focus:ring-green-500/20 focus:ring-offset-0 transition-shadow">
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
        <button type="submit" :disabled="form.processing || !form.isDirty" class="inline-flex items-center justify-center gap-2 h-12 px-8 bg-action text-white font-bold rounded-xl hover:bg-red-700 transition-colors shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed w-full sm:w-auto text-sm sm:text-base">
          <Save class="w-5 h-5" /> Guardar Cambios
        </button>
      </div>
    </form>
  </AdminLayout>
</template>
