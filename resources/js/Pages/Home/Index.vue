<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { Search, Info, CheckCircle2, MessageCircle, Truck, ChevronDown, ShieldCheck, Car, CarFront, CheckCircle } from 'lucide-vue-next';
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

const toggleDropdown = (type) => {
  if (type === 'width') { isWidthDropdownOpen.value = !isWidthDropdownOpen.value; if (isWidthDropdownOpen.value) widthSearchQuery.value = ''; }
  if (type === 'profile') { isProfileDropdownOpen.value = !isProfileDropdownOpen.value; if (isProfileDropdownOpen.value) profileSearchQuery.value = ''; }
  if (type === 'rim') { isRimDropdownOpen.value = !isRimDropdownOpen.value; if (isRimDropdownOpen.value) rimSearchQuery.value = ''; }
  if (type === 'make') { isMakeDropdownOpen.value = !isMakeDropdownOpen.value; if (isMakeDropdownOpen.value) makeSearchQuery.value = ''; }
  if (type === 'model') { if (!selectedVehicle.value.makeName && !selectedVehicle.value.makeSlug) return; isModelDropdownOpen.value = !isModelDropdownOpen.value; if (isModelDropdownOpen.value) modelSearchQuery.value = ''; }
  if (type === 'year') { if (!selectedVehicle.value.modelName && !selectedVehicle.value.modelSlug) return; isYearDropdownOpen.value = !isYearDropdownOpen.value; }
};

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
  makeName: '',
  makeSlug: '',
  modelName: '',
  modelSlug: '',
  yearName: '',
  yearSlug: ''
});

const isMakeDropdownOpen = ref(false);
const isModelDropdownOpen = ref(false);
const isYearDropdownOpen = ref(false);

const makeSearchQuery = ref('');
const modelSearchQuery = ref('');

const groupedMakes = computed(() => {
  if (!vehicleMakes.value) return [];
  const makesArray = Array.isArray(vehicleMakes.value) ? vehicleMakes.value : [];
  let sorted = [...makesArray].sort((a, b) => a.name.localeCompare(b.name));
  
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

const selectMake = async (make) => {
  selectedVehicle.value.makeName = make.name;
  selectedVehicle.value.makeSlug = make.slug;
  selectedVehicle.value.modelName = '';
  selectedVehicle.value.modelSlug = '';
  selectedVehicle.value.yearName = '';
  selectedVehicle.value.yearSlug = '';
  isMakeDropdownOpen.value = false;
  vehicleModels.value = [];
  
  try {
    const response = await axios.get('/api/vehicles/models', { params: { make: make.slug } });
    vehicleModels.value = response.data;
  } catch (error) { console.error(error); }
};

const selectModel = async (model) => {
  selectedVehicle.value.modelName = model.name;
  selectedVehicle.value.modelSlug = model.slug;
  selectedVehicle.value.yearName = '';
  selectedVehicle.value.yearSlug = '';
  isModelDropdownOpen.value = false;
  vehicleYears.value = [];
  
  try {
    const response = await axios.get('/api/vehicles/years', { 
      params: { make: selectedVehicle.value.makeSlug, model: model.slug } 
    });
    vehicleYears.value = response.data;
  } catch (error) { console.error(error); }
};

const selectYear = (year) => {
  selectedVehicle.value.yearName = year.name;
  selectedVehicle.value.yearSlug = year.slug;
  isYearDropdownOpen.value = false;
};

const searchByVehicle = () => {
  if (!selectedVehicle.value.yearSlug) return;
  router.get('/catalog', {
    vehicle_make: selectedVehicle.value.makeSlug,
    vehicle_model: selectedVehicle.value.modelSlug,
    vehicle_year: selectedVehicle.value.yearSlug
  });
};
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Inicio" />
    <WhatsAppFloatingBtn />

    <div class="relative z-50">
      <Header />
    </div>

    <main class="flex-grow relative z-10">
      <!-- Sección Principal (Hero) -->
      <section class="relative flex flex-col">
        <div class="absolute inset-0 z-0 bg-gray-900">
          <img src="/images/hero3.png" class="w-full h-full object-cover object-[75%_top] lg:object-top" alt="Tire Background">
          <!-- Difuminado superior para fusionar con el header negro -->
          <div class="absolute top-0 left-0 w-full h-40 md:h-64 bg-gradient-to-b from-black via-black/50 to-transparent"></div>
        </div>
        
        <!-- Spacer Block -->
        <div class="relative w-full">
          <!-- Text Overlay (Desktop only) -->
          <div class="hidden md:flex absolute inset-0 w-full pointer-events-none z-10">
            <div class="w-full px-4 sm:px-6 md:pl-[1%] lg:pl-[2%] xl:pl-[3%] 2xl:pl-[4%] flex flex-col justify-center items-start pt-12">
              <span class="text-red-600 font-extrabold uppercase tracking-wide text-sm lg:text-base mb-3 drop-shadow-sm">Rendimiento que te lleva más lejos</span>
              <h1 class="text-white font-black text-5xl lg:text-6xl xl:text-[4.5rem] 2xl:text-7xl leading-[1.05] uppercase tracking-tight drop-shadow-lg">
                Encuentra el <br>
                neumático perfecto <br>
                <span class="text-red-600">para tu vehículo</span>
              </h1>
              
              <!-- Vehículos Disponibles (Extra Section) -->
              <div class="mt-10 flex flex-col items-start gap-4">
                <span class="text-white font-semibold text-lg lg:text-xl drop-shadow-md">Más de 5,000 medidas disponibles.</span>
                <div class="flex items-center gap-8 lg:gap-12 mt-2">
                  <!-- AUTO -->
                  <div class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-default">
                    <svg class="w-12 h-12 text-white stroke-[1.5]" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                      <path d="M19 17h2c.6 0 1-.4 1-1v-3c0-.9-.7-1.7-1.5-1.9C18.7 10.6 16 10 16 10s-1.3-1.4-2.2-2.3c-.5-.4-1.1-.7-1.8-.7H5c-.6 0-1.1.4-1.4.9l-1.4 2.9A3.7 3.7 0 0 0 2 12v4c0 .6.4 1 1 1h2"/>
                      <circle cx="7" cy="17" r="2.5"/><path d="M9.5 17h5"/><circle cx="17" cy="17" r="2.5"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">Auto</span>
                  </div>
                  <!-- SUV -->
                  <div class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-default">
                    <svg class="w-12 h-12 text-white stroke-[1.5] -scale-x-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="7" cy="17" r="2.5"/><circle cx="17" cy="17" r="2.5"/>
                      <path d="M9.5 17h5M4.5 17H3v-4c0-.6.4-1.2.9-1.4l1.6-.7A3 3 0 0 1 6.5 11h.5l2-3.5A2 2 0 0 1 10.7 6h7.3a2 2 0 0 1 2 2v9h-1.5"/>
                      <path d="M13 6v5h7"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">SUV</span>
                  </div>
                  <!-- 4x4 (PICKUP) -->
                  <div class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-default">
                    <svg class="w-12 h-12 text-white stroke-[1.5] -scale-x-100" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round">
                      <circle cx="7" cy="17" r="2.5"/><circle cx="17" cy="17" r="2.5"/>
                      <path d="M9.5 17h5M4.5 17H3v-4c0-.6.4-1.2.9-1.4l1.6-.7A3 3 0 0 1 6.5 11h.5l2-3.5A2 2 0 0 1 10.7 6h3.6a2 2 0 0 1 1.7 1H16v4h5v6h-1.5"/>
                      <path d="M16 11v6"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">4x4</span>
                  </div>
                </div>
              </div>
            </div>
          </div>
          
          <!-- Spacer to show the image proporcionalmente -->
          <div class="w-full pb-[100%] sm:pb-[60%] md:pb-[20%] lg:pb-[29%]"></div>
        </div>
        
        <!-- Buscador Block (Pushing the header up) -->
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-40 text-center pointer-events-auto">
          <!-- Buscador overlapping exactly 50% on the bottom border minus 3 pixels -->
          <div class="transform translate-y-[calc(50%-3px)] relative z-20">
            <div class="max-w-[1050px] mx-auto p-6 sm:p-8 text-left bg-white/95 backdrop-blur-md shadow-2xl rounded-3xl border border-white/20">

            <!-- Título Principal del Buscador -->
            <h2 class="text-xl sm:text-2xl font-black text-red-500 mb-6 uppercase tracking-wide text-center">
              Encuentra tus Neumáticos aquí:
            </h2>

            <!-- Tabs and Help Link Header -->
            <div class="relative flex justify-center items-end border-b border-gray-200 mb-6">
              <!-- Pestañas -->
              <div class="flex w-full sm:w-auto min-w-[300px]">
                <button @click="activeTab = 'medida'" :class="{'border-gray-900 text-gray-900': activeTab === 'medida', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'medida'}" class="flex-1 pb-4 text-sm font-extrabold uppercase tracking-wider border-b-2 transition-colors text-center">
                  Por Medida
                </button>
                <button @click="activeTab = 'vehiculo'" :class="{'border-gray-900 text-gray-900': activeTab === 'vehiculo', 'border-transparent text-gray-500 hover:text-gray-700': activeTab !== 'vehiculo'}" class="flex-1 pb-4 text-sm font-extrabold uppercase tracking-wider border-b-2 transition-colors text-center">
                  Por Vehículo
                </button>
              </div>
              <!-- Link -->
              <div class="hidden sm:block absolute right-0 pb-4">
                <Link href="/guide" class="text-gray-600 hover:text-gray-900 text-xs font-semibold flex items-center gap-1 transition-colors">
                  <Info class="w-4 h-4" /> ¿No sabes tu medida?
                </Link>
              </div>
            </div>

            <!-- Contenido: Búsqueda por Medida -->
            <div v-if="activeTab === 'medida'">
              <form @submit.prevent="searchByMeasure">
                <!-- 4 columns for large screens: 3 dropdowns + 1 button -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 mb-2">
                
                  <!-- Dropdown Ancho -->
                  <div :class="['relative', isWidthDropdownOpen ? 'z-50' : 'z-20']" class="col-span-1">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Ancho</label>
                    <div 
                      @click="toggleDropdown('width')"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !searchFilters.width}">{{ searchFilters.width ? searchFilters.width : 'Ej: 225' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500 pointer-events-none" />
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
                    <div v-if="isWidthDropdownOpen" @click="isWidthDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
                  </div>

                  <!-- Dropdown Alto -->
                  <div :class="['relative', isProfileDropdownOpen ? 'z-50' : 'z-20']" class="col-span-1">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Altura</label>
                    <div 
                      @click="toggleDropdown('profile')"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !searchFilters.profile}">{{ searchFilters.profile ? searchFilters.profile : 'Ej: 45' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500 pointer-events-none" />
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
                    <div v-if="isProfileDropdownOpen" @click="isProfileDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
                  </div>

                  <!-- Dropdown Rin -->
                  <div :class="['relative', isRimDropdownOpen ? 'z-50' : 'z-20']" class="col-span-1">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Rin</label>
                    <div 
                      @click="toggleDropdown('rim')"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !searchFilters.rim}">{{ searchFilters.rim ? searchFilters.rim : 'Ej: 18' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500 pointer-events-none" />
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
                    <div v-if="isRimDropdownOpen" @click="isRimDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
                  </div>

                  <!-- Submit Button -->
                  <div class="flex items-end relative z-20">
                    <button type="submit" @click.prevent="searchByMeasure" class="w-full h-12 flex items-center justify-center gap-2 bg-action text-white text-sm font-bold rounded-lg hover:bg-red-700 transition-colors shadow-md cursor-pointer relative z-20 pointer-events-auto">
                      <Search class="w-4 h-4 pointer-events-none" /> BUSCAR MIS NEUMÁTICOS
                    </button>
                  </div>
                </div>
              </form>
            </div>

            <!-- Contenido: Búsqueda por Vehículo -->
            <div v-if="activeTab === 'vehiculo'">
              <form @submit.prevent="searchByVehicle">
                <!-- 4 columns for large screens to fit all inline -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 mb-2">
                  
                  <!-- Dropdown Marca -->
                  <div :class="['relative', isMakeDropdownOpen ? 'z-50' : 'z-20']">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Marca</label>
                    <div 
                      @click="toggleDropdown('make')"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white cursor-pointer flex items-center justify-between text-gray-900 relative"
                    >
                      <span :class="{'text-gray-400': !selectedVehicle.makeName}">{{ selectedVehicle.makeName || 'Seleccionar Marca' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500 pointer-events-none" />
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
                            @click.stop="selectMake(make)"
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
                    <div v-if="isMakeDropdownOpen" @click="isMakeDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
                  </div>

                  <!-- Dropdown Modelo -->
                  <div :class="['relative', isModelDropdownOpen ? 'z-50' : 'z-20']">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Modelo</label>
                    <div 
                      @click="toggleDropdown('model')"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white flex items-center justify-between text-gray-900 relative"
                      :class="[(selectedVehicle.makeSlug || selectedVehicle.makeName) ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50']"
                    >
                      <span :class="{'text-gray-400': !selectedVehicle.modelName}">{{ selectedVehicle.modelName || 'Seleccionar Modelo' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500 pointer-events-none" />
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
                            @click.stop="selectModel(model)"
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
                    <div v-if="isModelDropdownOpen" @click="isModelDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
                  </div>

                  <!-- Dropdown Año -->
                  <div :class="['relative', isYearDropdownOpen ? 'z-50' : 'z-20']">
                    <label class="block text-xs font-extrabold text-gray-700 uppercase tracking-wide mb-2">Año</label>
                    <div 
                      @click="toggleDropdown('year')"
                      class="w-full h-12 px-4 rounded-lg border border-gray-300 bg-white flex items-center justify-between text-gray-900 relative"
                      :class="[(selectedVehicle.modelSlug || selectedVehicle.modelName) ? 'cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50']"
                    >
                      <span :class="{'text-gray-400': !selectedVehicle.yearName}">{{ selectedVehicle.yearName || 'Seleccionar Año' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-500 pointer-events-none" />
                    </div>
                    
                    <div v-if="isYearDropdownOpen" class="absolute mt-1 w-full sm:w-[300px] max-h-80 overflow-y-auto bg-white border border-gray-200 shadow-2xl rounded-xl p-4 left-0 z-50">
                      <div class="grid grid-cols-3 sm:grid-cols-4 gap-2">
                        <button 
                          v-for="year in vehicleYears" 
                          :key="year.slug"
                          @click.stop="selectYear(year)"
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
                    <div v-if="isYearDropdownOpen" @click="isYearDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
                  </div>

                  <!-- Submit Button -->
                  <div class="flex items-end">
                    <button @click.prevent="searchByVehicle" type="button" :disabled="!selectedVehicle.yearName && !selectedVehicle.yearSlug" class="w-full h-12 flex items-center justify-center gap-2 bg-action text-white font-bold rounded-lg hover:bg-red-700 transition-colors shadow-md cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed relative z-30 pointer-events-auto">
                      <Search class="w-5 h-5 pointer-events-none" /> VER NEUMÁTICOS COMPATIBLES
                    </button>
                  </div>
                </div>
              </form>
            </div>
          </div>
        </div>
        </div>
        
        <!-- Barra de Características (Features Bar) merged into Hero -->
        <div class="relative z-10 text-white w-full">
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[320px] sm:pt-[280px] md:pt-[220px] lg:pt-[160px] pb-10">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-0">
            <!-- Feature 1 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start border-b border-white/10 sm:border-b-0 sm:border-r">
              <Truck class="w-8 h-8 text-white shrink-0" stroke-width="1.5" />
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">Envíos a todo el Perú</h4>
                <p class="text-xs text-gray-400 mt-0.5">Rápidos y seguros</p>
              </div>
            </div>
            <!-- Feature 2 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start border-b border-white/10 sm:border-b-0 lg:border-r">
              <ShieldCheck class="w-8 h-8 text-white shrink-0" stroke-width="1.5" />
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">Productos Originales</h4>
                <p class="text-xs text-gray-400 mt-0.5">Garantía de fábrica</p>
              </div>
            </div>
            <!-- Feature 3 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start border-b border-white/10 sm:border-b-0 sm:border-r">
              <svg class="w-8 h-8 text-white shrink-0" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/>
              </svg>
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">Asesoría por WhatsApp</h4>
                <p class="text-xs text-gray-400 mt-0.5">Te ayudamos a elegir</p>
              </div>
            </div>
            <!-- Feature 4 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start">
              <CheckCircle class="w-8 h-8 text-white shrink-0" stroke-width="1.5" />
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">Garantía de fábrica</h4>
                <p class="text-xs text-gray-400 mt-0.5">Respaldo total</p>
              </div>
            </div>
          </div>
        </div>
        </div>
      </section>
      <!-- Carrusel de Marcas -->
      <section v-if="brands && brands.length > 0" class="py-12 bg-white border-b border-gray-100 overflow-hidden relative z-0">
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
