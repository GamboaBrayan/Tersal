<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed, onUnmounted } from 'vue';
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
  rims: Array,
  heroImages: Array
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

const currentHeroImageIndex = ref(0);
let heroInterval;

onMounted(() => {
  fetchMakes();
  if (props.heroImages && props.heroImages.length > 1) {
    heroInterval = setInterval(() => {
      currentHeroImageIndex.value = (currentHeroImageIndex.value + 1) % props.heroImages.length;
    }, 5000);
  }
});

onUnmounted(() => {
  if (heroInterval) {
    clearInterval(heroInterval);
  }
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
        <div class="absolute inset-0 z-0 bg-gray-900 overflow-hidden">
          <template v-if="heroImages && heroImages.length > 0">
            <div v-for="(img, index) in heroImages" :key="img"
                 class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out"
                 :class="index === currentHeroImageIndex ? 'opacity-100 z-0' : 'opacity-0 -z-10'">
              <img :src="'/storage/' + img" class="w-full h-full object-cover object-[75%_top] lg:object-top" alt="Tire Background">
            </div>
          </template>
          <template v-else>
            <img src="/images/hero3.png" class="absolute inset-0 w-full h-full object-cover object-[75%_top] lg:object-top" alt="Tire Background">
          </template>
          <!-- Difuminado superior para fusionar con el header negro -->
          <div class="absolute top-0 left-0 w-full h-40 md:h-64 bg-gradient-to-b from-black via-black/50 to-transparent z-10"></div>
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
                    <svg class="w-12 sm:w-14 h-6 sm:h-8 text-white fill-current -scale-x-100" viewBox="0 0 123 40" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M103.94,23.97c5.39,0,9.76,4.37,9.76,9.76c0,5.39-4.37,9.76-9.76,9.76c-5.39,0-9.76-4.37-9.76-9.76 C94.18,28.34,98.55,23.97,103.94,23.97L103.94,23.97z M23,29.07v3.51h3.51C26.09,30.86,24.73,29.49,23,29.07L23,29.07z M26.52,34.87H23v3.51C24.73,37.97,26.09,36.6,26.52,34.87L26.52,34.87z M20.71,38.39v-3.51H17.2 C17.62,36.6,18.99,37.96,20.71,38.39L20.71,38.39z M17.2,32.59h3.51v-3.51C18.99,29.49,17.62,30.86,17.2,32.59L17.2,32.59z M105.09,29.07v3.51h3.51C108.18,30.86,106.82,29.49,105.09,29.07L105.09,29.07z M108.6,34.87h-3.51v3.51 C106.82,37.97,108.18,36.6,108.6,34.87L108.6,34.87z M102.8,38.39v-3.51h-3.51C99.71,36.6,101.07,37.96,102.8,38.39L102.8,38.39z M99.28,32.59h3.51v-3.51C101.07,29.49,99.71,30.86,99.28,32.59L99.28,32.59z M49.29,12.79c-1.54-0.35-3.07-0.35-4.61-0.28 C56.73,6.18,61.46,2.07,75.57,2.9l-1.94,12.87L50.4,16.65c0.21-0.61,0.33-0.94,0.37-1.55C50.88,13.36,50.86,13.15,49.29,12.79 L49.29,12.79z M79.12,3.13L76.6,15.6l24.13-0.98c2.48-0.1,2.91-1.19,1.41-3.28c-0.68-0.95-1.44-1.89-2.31-2.82 C93.59,1.86,87.38,3.24,79.12,3.13L79.12,3.13z M0.46,27.28H1.2c0.46-2.04,1.37-3.88,2.71-5.53c2.94-3.66,4.28-3.2,8.65-3.99 l24.46-4.61c5.43-3.86,11.98-7.3,19.97-10.2C64.4,0.25,69.63-0.01,77.56,0c4.54,0.01,9.14,0.28,13.81,0.84 c2.37,0.15,4.69,0.47,6.97,0.93c2.73,0.55,5.41,1.31,8.04,2.21l9.8,5.66c2.89,1.67,3.51,3.62,3.88,6.81l1.38,11.78h1.43v6.51 c-0.2,2.19-1.06,2.52-2.88,2.52h-2.37c0.92-20.59-28.05-24.11-27.42,1.63H34.76c3.73-17.75-14.17-23.91-22.96-13.76 c-2.67,3.09-3.6,7.31-3.36,12.3H2.03c-0.51-0.24-0.91-0.57-1.21-0.98c-1.05-1.43-0.82-5.74-0.74-8.23 C0.09,27.55-0.12,27.28,0.46,27.28L0.46,27.28z M21.86,23.97c5.39,0,9.76,4.37,9.76,9.76c0,5.39-4.37,9.76-9.76,9.76 c-5.39,0-9.76-4.37-9.76-9.76C12.1,28.34,16.47,23.97,21.86,23.97L21.86,23.97z"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">Auto</span>
                  </div>
                  <!-- SUV -->
                  <div class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-default">
                    <svg class="w-12 sm:w-14 h-6 sm:h-8 text-white fill-current" viewBox="0 0 260 140" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M246,90.011V59.995c0-5.523-4.48-9.995-10-9.995h-50L156.97,6.416C155.11,3.634,152.34,2,149,2H28 c-5.52,0-10,4.446-10,9.969V30h-8c-4.42,0-8,3.56-8,7.983v40.022C2,82.427,5.58,86,10,86h8v20h16.458 c2.8-15.959,16.702-28.066,33.462-28.066c16.75,0,30.708,12.107,33.518,28.066h72.958c2.8-15.959,16.764-28.066,33.524-28.066 c16.75,0,30.624,12.107,33.434,28.066H250c4.42,0,8-3.563,8-7.985v-8.004H246z M86,50H30V13.97h56V50z M98,50V13.97h48L170,50H98z M68,138c-14.336,0-26.083-11.706-26.083-26.051s11.664-26.014,26-26.014s26,11.669,26,26.014S82.336,138,68,138z M67.917,99.943 c-6.617,0-12,5.386-12,12.006c0,6.621,5.383,12.006,12,12.006s12-5.386,12-12.006C79.917,105.329,74.534,99.943,67.917,99.943z M208,138c-14.337,0-26.083-11.706-26.083-26.051s11.663-26.014,26-26.014s26,11.669,26,26.014S222.337,138,208,138z M207.917,99.943c-6.617,0-12,5.386-12,12.006c0,6.621,5.383,12.006,12,12.006s12-5.386,12-12.006 C219.917,105.329,214.534,99.943,207.917,99.943z"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">SUV</span>
                  </div>
                  <!-- 4x4 (PICKUP) -->
                  <div class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-default">
                    <svg class="w-12 sm:w-14 h-6 sm:h-8 text-white fill-current" viewBox="0 95 404 190" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M403.199,262.416l-5.651-52.739c-2.235-20.858-19.739-36.587-40.716-36.587h-39.109l-51.999-55.058 c-9.461-10.018-22.791-15.764-36.571-15.764H174.82c-16.428,0-29.793,13.365-29.793,29.793v41.028H16.7c-5.523,0-10,4.478-10,10 v70.961c-3.899,1.365-6.7,5.066-6.7,9.431c0,5.522,4.477,10,10,10h26.843c5.656,16.004,20.929,27.506,38.845,27.506 s33.19-11.501,38.846-27.506h180.519c5.656,16.004,20.929,27.506,38.846,27.506s33.19-11.501,38.846-27.506h20.512 c2.837,0,5.542-1.205,7.438-3.316C402.591,268.055,403.501,265.237,403.199,262.416z M251.184,131.765l39.029,41.325h-81.935 v-50.821h20.875C237.455,122.269,245.484,125.729,251.184,131.765z M165.027,132.062c0-5.399,4.393-9.793,9.793-9.793h13.458 v50.821h-23.251V132.062z M96.88,259.795c0,11.686-9.507,21.192-21.192,21.192c-11.685,0-21.192-9.507-21.192-21.192 c0-11.685,9.507-21.191,21.192-21.191C87.374,238.604,96.88,248.11,96.88,259.795z M75.688,218.604 c-20.567,0-37.66,15.151-40.708,34.878H26.7V193.09h100.953v60.392h-11.256C113.349,233.755,96.255,218.604,75.688,218.604z M355.091,259.795c0,11.686-9.507,21.192-21.192,21.192s-21.192-9.507-21.192-21.192c0-11.685,9.507-21.191,21.192-21.191 S355.091,248.11,355.091,259.795z M374.607,253.481c-3.047-19.727-20.141-34.878-40.708-34.878s-37.661,15.151-40.708,34.878 H147.652V193.09h209.18c10.731,0,19.687,8.047,20.83,18.718l4.465,41.674H374.607z"/>
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
            <div class="max-w-[1050px] mx-auto p-6 sm:p-10 text-left bg-white/95 backdrop-blur-xl shadow-[0_8px_40px_rgb(0,0,0,0.08)] rounded-[2rem] border border-gray-100/50">

            <!-- Título Principal del Buscador -->
            <h2 class="text-xl sm:text-2xl font-black text-red-500 mb-6 uppercase tracking-wide text-center">
              Encuentra tus Neumáticos aquí:
            </h2>

            <!-- Tabs and Help Link Header -->
            <div class="relative flex justify-center items-end border-b border-gray-100 mb-8">
              <!-- Pestañas -->
              <div class="flex w-full sm:w-auto min-w-[300px]">
                <button @click="activeTab = 'medida'" :class="{'border-action text-action': activeTab === 'medida', 'border-transparent text-gray-400 hover:text-gray-600': activeTab !== 'medida'}" class="flex-1 pb-4 text-sm font-extrabold uppercase tracking-wider border-b-2 transition-all text-center">
                  Por Medida
                </button>
                <button @click="activeTab = 'vehiculo'" :class="{'border-action text-action': activeTab === 'vehiculo', 'border-transparent text-gray-400 hover:text-gray-600': activeTab !== 'vehiculo'}" class="flex-1 pb-4 text-sm font-extrabold uppercase tracking-wider border-b-2 transition-all text-center">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Ancho</label>
                    <div 
                      @click="toggleDropdown('width')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-medium': !searchFilters.width, 'font-bold': searchFilters.width}">{{ searchFilters.width ? searchFilters.width : 'Ej: 225' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                    
                    <div v-if="isWidthDropdownOpen" class="absolute mt-2 w-full sm:w-[300px] max-h-80 overflow-hidden bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl flex flex-col left-0 z-50">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Altura</label>
                    <div 
                      @click="toggleDropdown('profile')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-medium': !searchFilters.profile, 'font-bold': searchFilters.profile}">{{ searchFilters.profile ? searchFilters.profile : 'Ej: 45' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                    
                    <div v-if="isProfileDropdownOpen" class="absolute mt-2 w-full sm:w-[300px] max-h-80 overflow-hidden bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl flex flex-col left-0 z-50">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Rin</label>
                    <div 
                      @click="toggleDropdown('rim')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-medium': !searchFilters.rim, 'font-bold': searchFilters.rim}">{{ searchFilters.rim ? searchFilters.rim : 'Ej: 18' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                    
                    <div v-if="isRimDropdownOpen" class="absolute mt-2 w-full sm:w-[300px] max-h-80 overflow-hidden bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl flex flex-col left-0 z-50">
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
                    <button type="submit" @click.prevent="searchByMeasure" class="w-full h-14 flex items-center justify-center gap-2 bg-action text-white text-sm font-bold rounded-xl hover:bg-red-700 transition-all shadow-[0_4px_14px_0_rgb(220,38,38,0.39)] hover:shadow-[0_6px_20px_rgba(220,38,38,0.23)] cursor-pointer relative z-20 pointer-events-auto">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Marca</label>
                    <div 
                      @click="toggleDropdown('make')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-medium': !selectedVehicle.makeName, 'font-bold': selectedVehicle.makeName}">{{ selectedVehicle.makeName || 'Seleccionar Marca' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                    
                    <!-- Desplegable -->
                    <div v-if="isMakeDropdownOpen" class="absolute mt-2 w-full sm:w-[450px] md:w-[550px] max-h-80 overflow-hidden bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl flex flex-col left-0 z-50">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Modelo</label>
                    <div 
                      @click="toggleDropdown('model')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 flex items-center justify-between text-gray-900 relative transition-colors"
                      :class="[(selectedVehicle.makeSlug || selectedVehicle.makeName) ? 'bg-gray-50 hover:bg-gray-100/50 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50/50']"
                    >
                      <span :class="{'text-gray-400 font-medium': !selectedVehicle.modelName, 'font-bold': selectedVehicle.modelName}">{{ selectedVehicle.modelName || 'Seleccionar Modelo' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                    
                    <div v-if="isModelDropdownOpen" class="absolute mt-2 w-full sm:w-[400px] max-h-80 overflow-hidden bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl flex flex-col left-0 z-50">
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
                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-wide mb-2">Año</label>
                    <div 
                      @click="toggleDropdown('year')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 flex items-center justify-between text-gray-900 relative transition-colors"
                      :class="[(selectedVehicle.modelSlug || selectedVehicle.modelName) ? 'bg-gray-50 hover:bg-gray-100/50 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50/50']"
                    >
                      <span :class="{'text-gray-400 font-medium': !selectedVehicle.yearName, 'font-bold': selectedVehicle.yearName}">{{ selectedVehicle.yearName || 'Seleccionar Año' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none" />
                    </div>
                    
                    <div v-if="isYearDropdownOpen" class="absolute mt-2 w-full sm:w-[300px] max-h-80 overflow-y-auto bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl p-4 left-0 z-50">
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
                    <button @click.prevent="searchByVehicle" type="button" :disabled="!selectedVehicle.yearName && !selectedVehicle.yearSlug" class="w-full h-14 px-2 flex items-center justify-center gap-1.5 sm:gap-2 bg-action text-white text-xs xl:text-sm font-bold rounded-xl hover:bg-red-700 transition-all shadow-[0_4px_14px_0_rgb(220,38,38,0.39)] hover:shadow-[0_6px_20px_rgba(220,38,38,0.23)] cursor-pointer disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none relative z-30 pointer-events-auto leading-tight text-center">
                      <Search class="w-4 h-4 shrink-0 pointer-events-none" />
                      <span>VER COMPATIBLES</span>
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
                <h4 class="font-black text-sm uppercase tracking-wide">Neumáticos Certificados</h4>
                <p class="text-xs text-gray-400 mt-0.5">Calidad garantizada</p>
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
          
          <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            <div v-for="tire in promotions" :key="tire.id" class="bg-white rounded-2xl shadow-[0_4px_20px_rgb(0,0,0,0.03)] border-transparent overflow-hidden hover:shadow-[0_12px_40px_rgb(0,0,0,0.08)] hover:-translate-y-1 transition-all duration-300 flex flex-col max-w-sm mx-auto w-full group">
              <div class="relative pt-[85%] bg-gray-50/50">
                <img :src="tire.images_json && tire.images_json.length ? '/storage/'+tire.images_json[0] : 'https://images.unsplash.com/photo-1620065095360-6644bcce8937?auto=format&fit=crop&q=80&w=400&h=400'" class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" alt="Tire" />
                <div class="absolute top-3 left-3 bg-action text-white text-[10px] font-bold px-2 py-1 rounded shadow-[0_4px_10px_rgb(220,38,38,0.3)] flex items-center gap-1">
                  OFERTA
                  <span class="bg-white text-action px-1 rounded text-[10px]">{{ getDiscountPercentage(tire.price, tire.offer_price) }}%</span>
                </div>
              </div>
              <div class="p-5 flex-grow flex flex-col">
                <div class="text-[10px] font-bold text-gray-400 uppercase tracking-wider mb-1">{{ tire.brand?.name || 'Marca' }}</div>
                <h3 class="text-sm font-bold text-gray-800 mb-3 leading-tight">{{ tire.width }}/{{ tire.profile }} R{{ tire.rim }} {{ tire.model }}</h3>
                <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                  <div>
                    <div class="text-xs text-gray-400 line-through">S/. {{ tire.price }}</div>
                    <div class="text-xl font-black text-gray-900 leading-none mt-1">S/. {{ tire.offer_price }}</div>
                  </div>
                  <Link :href="`/catalog/${tire.id}`" class="h-9 px-4 flex items-center justify-center bg-action text-white font-bold rounded-lg hover:bg-red-700 shadow-[0_4px_12px_rgb(220,38,38,0.3)] hover:shadow-[0_6px_16px_rgb(220,38,38,0.4)] transition-all text-xs">
                    Ver más
                  </Link>
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
      <section class="py-20 bg-white border-t border-gray-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-2xl sm:text-3xl font-black text-gray-900 mb-14 tracking-tight">Cómo comprar en <span class="text-action">3 simples pasos</span></h2>
          <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
            <div class="flex flex-col items-center">
              <div class="w-20 h-20 bg-red-50 text-action rounded-[2rem] flex items-center justify-center mb-6 shadow-sm">
                <Search class="w-8 h-8" stroke-width="2.5" />
              </div>
              <h3 class="font-bold text-lg mb-3 text-gray-800">1. Encuentra tu medida</h3>
              <p class="text-gray-500 text-sm leading-relaxed max-w-xs">Usa nuestro buscador para ubicar el neumático exacto que necesita tu vehículo.</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-20 h-20 bg-red-50 text-action rounded-[2rem] flex items-center justify-center mb-6 shadow-sm">
                <MessageCircle class="w-8 h-8" stroke-width="2.5" />
              </div>
              <h3 class="font-bold text-lg mb-3 text-gray-800">2. Consulta stock</h3>
              <p class="text-gray-500 text-sm leading-relaxed max-w-xs">Haz clic en el botón de WhatsApp. Te atenderemos y confirmaremos en minutos.</p>
            </div>
            <div class="flex flex-col items-center">
              <div class="w-20 h-20 bg-red-50 text-action rounded-[2rem] flex items-center justify-center mb-6 shadow-sm">
                <Truck class="w-8 h-8" stroke-width="2.5" />
              </div>
              <h3 class="font-bold text-lg mb-3 text-gray-800">3. Coordina la entrega</h3>
              <p class="text-gray-500 text-sm leading-relaxed max-w-xs">Paga de forma segura y recibe o instala tus neumáticos el mismo día.</p>
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
