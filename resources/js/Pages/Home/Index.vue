<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { Search, Info, CheckCircle2, MessageCircle, Truck, ChevronDown } from 'lucide-vue-next';
import axios from 'axios';

const props = defineProps({
  brands: Array,
  promotions: Array,
  widths: Array,
  profiles: Array,
  rims: Array
});

const activeTab = ref('medida');

const searchFilters = ref({
  width: '',
  profile: '',
  rim: ''
});

const isWidthDropdownOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const isRimDropdownOpen = ref(false);

const widthSearchQuery = ref('');
const profileSearchQuery = ref('');
const rimSearchQuery = ref('');

const filteredWidths = computed(() => {
  if (!widthSearchQuery.value) return props.widths;
  return props.widths.filter(w => String(w).includes(widthSearchQuery.value));
});

const filteredProfiles = computed(() => {
  if (!profileSearchQuery.value) return props.profiles;
  return props.profiles.filter(p => String(p).includes(profileSearchQuery.value));
});

const filteredRims = computed(() => {
  if (!rimSearchQuery.value) return props.rims;
  return props.rims.filter(r => String(r).includes(rimSearchQuery.value));
});

const selectWidth = (val) => { searchFilters.value.width = val; isWidthDropdownOpen.value = false; };
const selectProfile = (val) => { searchFilters.value.profile = val; isProfileDropdownOpen.value = false; };
const selectRim = (val) => { searchFilters.value.rim = val; isRimDropdownOpen.value = false; };

const searchByMeasure = () => {
  const params = {};
  if (searchFilters.value.width) params.width = searchFilters.value.width;
  if (searchFilters.value.profile) params.profile = searchFilters.value.profile;
  if (searchFilters.value.rim) params.rim = searchFilters.value.rim;
  router.get('/catalog', params);
};

const getDiscountPercentage = (price, offerPrice) => {
  if (!price || !offerPrice) return 0;
  return Math.round(((price - offerPrice) / price) * 100);
};

// --- VEHICLE SEARCH LOGIC ---
const vehicleMakes = ref([]);
const vehicleModels = ref([]);
const vehicleYears = ref([]);

const selectedVehicle = ref({
  make: '',
  model: '',
  year: ''
});

const isMakeDropdownOpen = ref(false);
const isModelDropdownOpen = ref(false);
const isYearDropdownOpen = ref(false);

const makeSearchQuery = ref('');
const modelSearchQuery = ref('');

const groupedMakes = computed(() => {
  if (!vehicleMakes.value) return [];
  const makesArray = Array.isArray(vehicleMakes.value) ? vehicleMakes.value : [];
  let sorted = makesArray.sort((a, b) => a.name.localeCompare(b.name));
  
  if (makeSearchQuery.value) {
    const q = makeSearchQuery.value.toLowerCase();
    sorted = sorted.filter(m => m.name.toLowerCase().includes(q));
  }
  
  return sorted;
});

const filteredModels = computed(() => {
  if (!vehicleModels.value) return [];
  const modelsArray = Array.isArray(vehicleModels.value) ? vehicleModels.value : [];
  
  if (modelSearchQuery.value) {
    const q = modelSearchQuery.value.toLowerCase();
    return modelsArray.filter(m => m.name.toLowerCase().includes(q));
  }
  
  return modelsArray;
});

onMounted(() => {
  fetchMakes();
});

const fetchMakes = async () => {
  try {
    const response = await axios.get('/api/vehicles/makes');
    vehicleMakes.value = response.data;
  } catch (error) {
    console.error('Error fetching makes:', error);
  }
};

const selectMake = async (makeName) => {
  selectedVehicle.value.make = makeName;
  selectedVehicle.value.model = '';
  selectedVehicle.value.year = '';
  isMakeDropdownOpen.value = false;
  vehicleModels.value = [];
  
  try {
    const response = await axios.get('/api/vehicles/models', { params: { make: makeName } });
    vehicleModels.value = response.data;
  } catch (error) { console.error(error); }
};

const selectModel = async (modelName) => {
  selectedVehicle.value.model = modelName;
  selectedVehicle.value.year = '';
  isModelDropdownOpen.value = false;
  vehicleYears.value = [];
  
  try {
    const response = await axios.get('/api/vehicles/years', { 
      params: { make: selectedVehicle.value.make, model: modelName } 
    });
    vehicleYears.value = response.data;
  } catch (error) { console.error(error); }
};

const selectYear = (yearName) => {
  selectedVehicle.value.year = yearName;
  isYearDropdownOpen.value = false;
};

const searchByVehicle = () => {
  if (!selectedVehicle.value.year) return;
  router.get('/catalog', {
    vehicle_make: selectedVehicle.value.make,
    vehicle_model: selectedVehicle.value.model,
    vehicle_year: selectedVehicle.value.year
  });
};
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Inicio" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow">
      <!-- Sección Principal (Hero) -->
      <section class="relative">
        <div class="absolute inset-0 z-0 bg-gray-900">
          <img src="/images/hero2.png" class="w-full h-full object-cover object-center" alt="Tire Background">
        </div>
        
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
          <!-- Spacer to show the image -->
          <div class="h-[150px] sm:h-[200px] md:h-[200px] lg:h-[320px]"></div>

          <!-- Buscador overlapping exactly 50% on the bottom border minus 3 pixels -->
          <div class="transform translate-y-[calc(50%-3px)] relative z-20">
            <div class="max-w-4xl mx-auto p-6 sm:p-8 text-left bg-white/95 backdrop-blur-md shadow-2xl rounded-3xl border border-white/20">
              <!-- Pestañas -->
              <div class="flex border-b border-gray-200 mb-6">
              <button @click="activeTab = 'medida'" :class="{'border-gray-900 text-gray-900': activeTab === 'medida', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'medida'}" class="flex-1 pb-4 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors text-center">
                Por Medida
              </button>
              <button @click="activeTab = 'vehiculo'" :class="{'border-gray-900 text-gray-900': activeTab === 'vehiculo', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'vehiculo'}" class="flex-1 pb-4 text-sm font-bold uppercase tracking-wider border-b-2 transition-colors text-center">
                Por Vehículo
              </button>
            </div>

            <!-- Contenido: Búsqueda por Medida -->
            <div v-if="activeTab === 'medida'">
              <div class="flex justify-end mb-2">
                <Link href="/guide" class="text-action text-sm font-semibold flex items-center gap-1 hover:underline">
                  <Info class="w-4 h-4" /> ¿Dónde veo mi medida?
                </Link>
              </div>
              <form @submit.prevent="searchByMeasure">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  
                  <!-- Dropdown Ancho -->
                  <div :class="['relative', isWidthDropdownOpen ? 'z-50' : 'z-10']">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Ancho</label>
                    <div 
                      @click="isWidthDropdownOpen = !isWidthDropdownOpen; if(isWidthDropdownOpen) widthSearchQuery = ''"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !searchFilters.width}">{{ searchFilters.width || 'Todos' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500" />
                    </div>
                    
                    <div v-if="isWidthDropdownOpen" class="absolute mt-1 w-full sm:w-[300px] max-h-80 overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-xl flex flex-col left-0 z-50">
                      <!-- Buscador interno -->
                      <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                          <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                          <input 
                            type="text" 
                            v-model="widthSearchQuery" 
                            placeholder="Buscar ancho..." 
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-action focus:border-action outline-none transition-shadow"
                            @click.stop
                          >
                        </div>
                      </div>

                      <div class="p-4 overflow-y-auto max-h-60">
                        <div class="grid grid-cols-3 gap-2">
                          <button 
                            @click.stop="selectWidth('')"
                            type="button"
                            class="text-center px-2 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          >Todos</button>
                          <button 
                            v-for="w in filteredWidths" 
                            :key="w"
                            @click.stop="selectWidth(w)"
                            type="button"
                            class="text-center px-2 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          >
                            {{ w }}
                          </button>
                          <div v-if="filteredWidths.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                            No se encontraron medidas
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="isWidthDropdownOpen" @click="isWidthDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-screen h-screen"></div>
                  </div>

                  <!-- Dropdown Alto -->
                  <div :class="['relative', isProfileDropdownOpen ? 'z-50' : 'z-10']">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Alto</label>
                    <div 
                      @click="isProfileDropdownOpen = !isProfileDropdownOpen; if(isProfileDropdownOpen) profileSearchQuery = ''"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !searchFilters.profile}">{{ searchFilters.profile || 'Todos' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500" />
                    </div>
                    
                    <div v-if="isProfileDropdownOpen" class="absolute mt-1 w-full sm:w-[300px] max-h-80 overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-xl flex flex-col left-0 z-50">
                      <!-- Buscador interno -->
                      <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                          <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                          <input 
                            type="text" 
                            v-model="profileSearchQuery" 
                            placeholder="Buscar alto..." 
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-action focus:border-action outline-none transition-shadow"
                            @click.stop
                          >
                        </div>
                      </div>

                      <div class="p-4 overflow-y-auto max-h-60">
                        <div class="grid grid-cols-3 gap-2">
                          <button 
                            @click.stop="selectProfile('')"
                            type="button"
                            class="text-center px-2 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          >Todos</button>
                          <button 
                            v-for="p in filteredProfiles" 
                            :key="p"
                            @click.stop="selectProfile(p)"
                            type="button"
                            class="text-center px-2 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          >
                            {{ p }}
                          </button>
                          <div v-if="filteredProfiles.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                            No se encontraron medidas
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="isProfileDropdownOpen" @click="isProfileDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-screen h-screen"></div>
                  </div>

                  <!-- Dropdown Rin -->
                  <div :class="['relative', isRimDropdownOpen ? 'z-50' : 'z-10']">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Rin</label>
                    <div 
                      @click="isRimDropdownOpen = !isRimDropdownOpen; if(isRimDropdownOpen) rimSearchQuery = ''"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !searchFilters.rim}">{{ searchFilters.rim || 'Todos' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500" />
                    </div>
                    
                    <div v-if="isRimDropdownOpen" class="absolute mt-1 w-full sm:w-[300px] max-h-80 overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-xl flex flex-col left-0 z-50">
                      <!-- Buscador interno -->
                      <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                          <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                          <input 
                            type="text" 
                            v-model="rimSearchQuery" 
                            placeholder="Buscar rin..." 
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-action focus:border-action outline-none transition-shadow"
                            @click.stop
                          >
                        </div>
                      </div>

                      <div class="p-4 overflow-y-auto max-h-60">
                        <div class="grid grid-cols-3 gap-2">
                          <button 
                            @click.stop="selectRim('')"
                            type="button"
                            class="text-center px-2 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          >Todos</button>
                          <button 
                            v-for="r in filteredRims" 
                            :key="r"
                            @click.stop="selectRim(r)"
                            type="button"
                            class="text-center px-2 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          >
                            {{ r }}
                          </button>
                          <div v-if="filteredRims.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                            No se encontraron medidas
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="isRimDropdownOpen" @click="isRimDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-screen h-screen"></div>
                  </div>

                </div>
                <button type="submit" class="w-full h-12 flex items-center justify-center gap-2 bg-action text-white font-bold rounded-lg hover:bg-red-700 transition-colors shadow-md cursor-pointer">
                  <Search class="w-5 h-5" /> BUSCAR NEUMÁTICOS
                </button>
              </form>
            </div>

            <!-- Contenido: Búsqueda por Vehículo -->
            <div v-if="activeTab === 'vehiculo'">
              <form @submit.prevent="searchByVehicle">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
                  
                  <!-- Dropdown Marca -->
                  <div :class="['relative', isMakeDropdownOpen ? 'z-50' : 'z-10']">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Marca</label>
                    <div 
                      @click="isMakeDropdownOpen = !isMakeDropdownOpen; if(isMakeDropdownOpen) makeSearchQuery = ''"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !selectedVehicle.make}">{{ selectedVehicle.make || 'Seleccionar Marca' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500" />
                    </div>
                    
                    <!-- Desplegable -->
                    <div v-if="isMakeDropdownOpen" class="absolute mt-1 w-full sm:w-[450px] md:w-[550px] max-h-80 overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-xl flex flex-col left-0 z-50">
                      <!-- Buscador interno -->
                      <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                          <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                          <input 
                            type="text" 
                            v-model="makeSearchQuery" 
                            placeholder="Buscar marca..." 
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-action focus:border-action outline-none transition-shadow"
                            @click.stop
                          >
                        </div>
                      </div>
                      
                      <!-- Lista de opciones -->
                      <div class="p-4 overflow-y-auto max-h-60">
                        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 gap-2">
                          <button 
                            v-for="make in groupedMakes" 
                            :key="make.slug"
                            @click.stop="selectMake(make.name)"
                            type="button"
                            class="text-left px-3 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                            :title="make.name"
                          >
                            {{ make.name }}
                          </button>
                          <div v-if="groupedMakes.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                            <span v-if="vehicleMakes.length === 0">Cargando marcas...</span>
                            <span v-else>No se encontraron marcas</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="isMakeDropdownOpen" @click="isMakeDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-screen h-screen"></div>
                  </div>

                  <!-- Dropdown Modelo -->
                  <div :class="['relative', isModelDropdownOpen ? 'z-50' : 'z-10']">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Modelo</label>
                    <div 
                      @click="selectedVehicle.make && (isModelDropdownOpen = !isModelDropdownOpen); if(isModelDropdownOpen) modelSearchQuery = ''"
                      :class="['w-full h-12 px-4 rounded-lg border border-gray-300 bg-white flex items-center justify-between text-gray-900 relative', selectedVehicle.make ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50']"
                    >
                      <span :class="{'text-gray-400': !selectedVehicle.model}">{{ selectedVehicle.model || 'Seleccionar Modelo' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500" />
                    </div>
                    
                    <div v-if="isModelDropdownOpen" class="absolute mt-1 w-full sm:w-[400px] max-h-80 overflow-hidden bg-white border border-gray-200 shadow-2xl rounded-xl flex flex-col left-0 z-50">
                      <!-- Buscador interno -->
                      <div class="p-3 border-b border-gray-100 bg-gray-50">
                        <div class="relative">
                          <Search class="w-4 h-4 absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400" />
                          <input 
                            type="text" 
                            v-model="modelSearchQuery" 
                            placeholder="Buscar modelo..." 
                            class="w-full pl-9 pr-4 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-action focus:border-action outline-none transition-shadow"
                            @click.stop
                          >
                        </div>
                      </div>

                      <div class="p-4 overflow-y-auto max-h-60">
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                          <button 
                            v-for="model in filteredModels" 
                            :key="model.slug"
                            @click.stop="selectModel(model.name)"
                            type="button"
                            class="text-left px-3 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                            :title="model.name"
                          >
                            {{ model.name }}
                          </button>
                          <div v-if="filteredModels.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                            <span v-if="vehicleModels.length === 0">Cargando modelos...</span>
                            <span v-else>No se encontraron modelos</span>
                          </div>
                        </div>
                      </div>
                    </div>
                    <div v-if="isModelDropdownOpen" @click="isModelDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-screen h-screen"></div>
                  </div>

                  <!-- Dropdown Año -->
                  <div :class="['relative', isYearDropdownOpen ? 'z-50' : 'z-10']">
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wide mb-2">Año</label>
                    <div 
                      @click="selectedVehicle.model && (isYearDropdownOpen = !isYearDropdownOpen)"
                      :class="['w-full h-12 px-4 rounded-lg border border-gray-300 bg-white flex items-center justify-between text-gray-900 relative', selectedVehicle.model ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50']"
                    >
                      <span :class="{'text-gray-400': !selectedVehicle.year}">{{ selectedVehicle.year || 'Seleccionar Año' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500" />
                    </div>
                    
                    <div v-if="isYearDropdownOpen" class="absolute mt-1 w-full sm:w-[300px] max-h-80 overflow-y-auto bg-white border border-gray-200 shadow-2xl rounded-xl p-4 left-0 z-50">
                      <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <button 
                          v-for="year in vehicleYears" 
                          :key="year.slug"
                          @click.stop="selectYear(year.name)"
                          type="button"
                          class="text-left px-3 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate text-center"
                          :title="year.name"
                        >
                          {{ year.name }}
                        </button>
                        <div v-if="vehicleYears.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                          Cargando años...
                        </div>
                      </div>
                    </div>
                    <div v-if="isYearDropdownOpen" @click="isYearDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-screen h-screen"></div>
                  </div>

                </div>
                <button type="submit" :disabled="!selectedVehicle.year" class="w-full h-12 flex items-center justify-center gap-2 bg-action text-white font-bold rounded-lg hover:bg-red-700 transition-colors shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed relative z-30">
                  <Search class="w-5 h-5" /> VER NEUMÁTICOS COMPATIBLES
                </button>
              </form>
            </div>
          </div>
        </div>
        </div>
      </section>

      <!-- Carrusel de Marcas -->
      <section v-if="brands && brands.length > 0" class="pt-[320px] md:pt-[240px] lg:pt-[200px] pb-10 bg-white border-b border-gray-100 overflow-hidden relative z-0">
        <div class="marquee-container group flex w-full">
          <div v-for="n in 10" :key="n" class="marquee-content flex shrink-0 gap-12 pr-12 items-center justify-start min-w-max" :style="{ animationDuration: Math.max(brands.length * 4, 10) + 's' }">
            <Link :href="`/catalog?brand_id=${brand.id}`" v-for="brand in brands" :key="`${n}-${brand.id}`" class="shrink-0 flex items-center justify-center w-32 h-20 transition-all duration-300 opacity-70 hover:opacity-100 cursor-pointer">
              <img v-if="brand.logo_url" :src="'/storage/' + brand.logo_url" :alt="brand.name" class="max-w-full max-h-full object-contain grayscale hover:grayscale-0 transition-all duration-300" />
              <span v-else class="text-xl font-black text-gray-400 hover:text-gray-900 transition-colors">{{ brand.name }}</span>
            </Link>
          </div>
        </div>
      </section>

      <!-- Promociones -->
      <section class="py-16 bg-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          <div class="flex items-center justify-between mb-8">
            <h2 class="text-3xl font-black text-gray-900">Ofertas Especiales</h2>
            <Link href="/catalog" class="text-primary font-bold hover:underline">Ver todo el catálogo &rarr;</Link>
          </div>
          
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <div v-for="tire in promotions" :key="tire.id" class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col max-w-sm mx-auto w-full">
              <div class="relative pt-[85%] bg-gray-100">
                <img :src="tire.images_json && tire.images_json.length ? '/storage/'+tire.images_json[0] : 'https://images.unsplash.com/photo-1620065095360-6644bcce8937?auto=format&fit=crop&q=80&w=400&h=400'" class="absolute inset-0 w-full h-full object-cover" alt="Tire" />
                <div class="absolute top-2 left-2 bg-action text-white text-[10px] font-bold px-2 py-1 rounded shadow-sm flex items-center gap-1">
                  OFERTA
                  <span class="bg-white text-action px-1 rounded text-[10px]">{{ getDiscountPercentage(tire.price, tire.offer_price) }}%</span>
                </div>
              </div>
              <div class="p-4 flex-grow flex flex-col">
                <div class="text-[10px] font-bold text-gray-500 uppercase tracking-wider mb-1">{{ tire.brand?.name || 'Marca' }}</div>
                <h3 class="text-sm font-bold text-gray-900 mb-2 leading-tight">{{ tire.width }}/{{ tire.profile }} R{{ tire.rim }} {{ tire.model }}</h3>
                <div class="mt-auto pt-3 border-t border-gray-100 flex items-center justify-between">
                  <div>
                    <div class="text-xs text-gray-400 line-through">S/. {{ tire.price }}</div>
                    <div class="text-lg font-black text-gray-900 leading-none mt-1">S/. {{ tire.offer_price }}</div>
                  </div>
                  <a :href="'https://wa.me/' + $page.props.global_whatsapp + '?text=' + encodeURIComponent(`¡Hola! Estoy interesado en el neumático ${tire.brand?.name || ''} ${tire.model} en la medida ${tire.width}/${tire.profile} R${tire.rim} que vi en su web. Deseo confirmar el stock.`)" target="_blank" class="h-8 px-3 flex items-center justify-center bg-action text-white font-bold rounded hover:bg-red-700 transition-colors text-xs">
                    Ver más
                  </a>
                </div>
              </div>
            </div>
            <div v-if="!promotions || promotions.length === 0" class="col-span-full py-12 text-center text-gray-500">
              No hay promociones activas en este momento.
            </div>
          </div>
        </div>
      </section>

      <!-- Pasos de Compra -->
      <section class="py-16 bg-white border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-3xl font-black text-gray-900 mb-12">Cómo comprar por WhatsApp en 3 pasos</h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                <Search class="w-8 h-8" />
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-900">1. Encuentra tu medida</h3>
              <p class="text-gray-600 text-sm">Usa nuestro buscador para ubicar el neumático exacto que necesita tu vehículo.</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                <MessageCircle class="w-8 h-8" />
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-900">2. Consulta stock</h3>
              <p class="text-gray-600 text-sm">Haz clic en el botón rojo de WhatsApp. Te atenderemos en minutos.</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-16 h-16 bg-blue-50 text-primary rounded-full flex items-center justify-center mb-4">
                <Truck class="w-8 h-8" />
              </div>
              <h3 class="font-bold text-lg mb-2 text-gray-900">3. Coordina la entrega</h3>
              <p class="text-gray-600 text-sm">Paga de forma segura y recibe o instala tus neumáticos el mismo día.</p>
            </div>
          </div>
        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>

<style scoped>
.marquee-container:hover .marquee-content {
  animation-play-state: paused;
}
.marquee-content {
  animation: marquee 30s linear infinite;
}
@keyframes marquee {
  0% { transform: translateX(0); }
  100% { transform: translateX(-100%); }
}
</style>
