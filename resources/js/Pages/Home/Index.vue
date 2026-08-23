<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref, onMounted, computed, onUnmounted } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { Search, HelpCircle, MessageCircle, Truck, ChevronDown, ShieldCheck, CheckCircle, ChevronLeft, ChevronRight } from 'lucide-vue-next';
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
  if (type === 'trim') { if (!selectedVehicle.value.yearName && !selectedVehicle.value.yearSlug) return; isTrimDropdownOpen.value = !isTrimDropdownOpen.value; }
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
const vehicleTrims = ref([]);

const selectedVehicle = ref({
  makeName: '',
  makeSlug: '',
  modelName: '',
  modelSlug: '',
  yearName: '',
  yearSlug: '',
  trimName: '',
  trimSlug: ''
});

const isMakeDropdownOpen = ref(false);
const isModelDropdownOpen = ref(false);
const isYearDropdownOpen = ref(false);
const isTrimDropdownOpen = ref(false);

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
  selectedVehicle.value.trimName = '';
  selectedVehicle.value.trimSlug = '';
  isMakeDropdownOpen.value = false;
  vehicleModels.value = [];
  vehicleYears.value = [];
  vehicleTrims.value = [];
  
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
  selectedVehicle.value.trimName = '';
  selectedVehicle.value.trimSlug = '';
  isModelDropdownOpen.value = false;
  vehicleYears.value = [];
  vehicleTrims.value = [];
  
  try {
    const response = await axios.get('/api/vehicles/years', { 
      params: { make: selectedVehicle.value.makeSlug, model: model.slug } 
    });
    vehicleYears.value = response.data;
  } catch (error) { console.error(error); }
};

const selectYear = async (year) => {
  selectedVehicle.value.yearName = year.name;
  selectedVehicle.value.yearSlug = year.slug;
  selectedVehicle.value.trimName = '';
  selectedVehicle.value.trimSlug = '';
  isYearDropdownOpen.value = false;
  vehicleTrims.value = [];
  
  try {
    const response = await axios.get('/api/vehicles/trims', { 
      params: { 
        make: selectedVehicle.value.makeSlug, 
        model: selectedVehicle.value.modelSlug,
        year: year.slug
      } 
    });
    vehicleTrims.value = response.data;
  } catch (error) { console.error(error); }
};

const selectTrim = (trim) => {
  selectedVehicle.value.trimName = trim.name;
  selectedVehicle.value.trimSlug = trim.slug;
  isTrimDropdownOpen.value = false;
};

const searchByVehicle = () => {
  if (!selectedVehicle.value.yearSlug) return;
  const params = {
    vehicle_make: selectedVehicle.value.makeSlug,
    vehicle_model: selectedVehicle.value.modelSlug,
    vehicle_year: selectedVehicle.value.yearSlug
  };
  if (selectedVehicle.value.trimSlug) {
    params.vehicle_trim = selectedVehicle.value.trimSlug;
  }
  router.get('/catalog', params);
};

const brandsScrollContainer = ref(null);
let animationFrameId = null;
let isHoveringBrands = false;

const scrollBrands = (direction) => {
  if (brandsScrollContainer.value) {
    const scrollAmount = 300;
    const newScrollPosition = brandsScrollContainer.value.scrollLeft + (direction === 'left' ? -scrollAmount : scrollAmount);
    brandsScrollContainer.value.scrollTo({
      left: newScrollPosition,
      behavior: 'smooth'
    });
  }
};

const startAutoScroll = () => {
  if (isHoveringBrands || !brandsScrollContainer.value) return;

  const container = brandsScrollContainer.value;
  container.scrollLeft += 1;

  if (container.scrollLeft >= container.scrollWidth / 2) {
    container.scrollLeft = 0;
  }

  animationFrameId = requestAnimationFrame(startAutoScroll);
};

const handleMouseEnter = () => {
  isHoveringBrands = true;
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
    animationFrameId = null;
  }
};

const handleMouseLeave = () => {
  isHoveringBrands = false;
  if (!animationFrameId) {
    animationFrameId = requestAnimationFrame(startAutoScroll);
  }
};

const currentPromoIndex = ref(0);
const nextPromo = () => {
  if (props.promotions && props.promotions.length > 0) {
    currentPromoIndex.value = (currentPromoIndex.value + 1) % props.promotions.length;
  }
};
const prevPromo = () => {
  if (props.promotions && props.promotions.length > 0) {
    currentPromoIndex.value = (currentPromoIndex.value - 1 + props.promotions.length) % props.promotions.length;
  }
};

let promoInterval = null;
const startPromoAutoPlay = () => {
  if (!promoInterval) {
    promoInterval = setInterval(() => {
      nextPromo();
    }, 5000);
  }
};
const stopPromoAutoPlay = () => {
  if (promoInterval) {
    clearInterval(promoInterval);
    promoInterval = null;
  }
};

onMounted(() => {
  // Pequeño delay para asegurar que el DOM está listo y tiene width
  setTimeout(() => {
    animationFrameId = requestAnimationFrame(startAutoScroll);
  }, 500);
  
  startPromoAutoPlay();
});

onUnmounted(() => {
  if (animationFrameId) {
    cancelAnimationFrame(animationFrameId);
  }
  stopPromoAutoPlay();
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Inicio" />
    <WhatsAppFloatingBtn />

    <Header />

    <main class="flex-grow relative z-10">
      <!-- Sección Principal (Hero) -->
      <section class="relative flex flex-col">
        <div class="absolute inset-0 z-0 bg-gray-900 overflow-hidden">
          <template v-if="heroImages && heroImages.length > 0">
            <div v-for="(img, index) in heroImages" :key="img"
                 class="absolute inset-0 w-full h-full transition-opacity duration-1000 ease-in-out"
                 :class="index === currentHeroImageIndex ? 'opacity-100 z-0' : 'opacity-0 -z-10'">
              <img :src="img" class="w-full h-full object-cover object-[75%_top] lg:object-top" alt="Tire Background">
            </div>
          </template>
          <template v-else>
            <img src="/images/hero3.png" class="absolute inset-0 w-full h-full object-cover object-[75%_top] lg:object-top" alt="Tire Background">
          </template>
          <!-- Difuminado superior para fusionar con el header negro -->
          <div class="absolute top-0 left-0 w-full h-40 md:h-20 bg-gradient-to-b from-black via-black/0 to-transparent z-10"></div>
        </div>
        
        <!-- Spacer Block -->
        <div class="relative w-full">
          <!-- Text Overlay (Desktop only) -->
          <div class="hidden md:flex absolute inset-0 w-full pointer-events-none z-10">
            
            <!-- Slide 1 Text -->
            <div class="absolute inset-0 w-full px-4 sm:px-6 md:pl-[1%] lg:pl-[2%] xl:pl-[3%] 2%] 2xl:pl-[4%] flex flex-col justify-center items-start transition-all duration-700 ease-out transform pt-12" :class="currentHeroImageIndex === 0 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
              <span class="text-red-600 font-extrabold uppercase tracking-wide text-sm lg:text-base mb-3 drop-shadow-sm">LÍNEA PCR | AUTO, SUV Y CAMIONETA</span>
              <h1 class="text-white font-black text-4xl lg:text-5xl xl:text-6xl 2xl:text-[4rem] leading-[1.05] uppercase tracking-tight drop-shadow-lg">
                Encuentra el <br>
                neumático perfecto <br>
                <span class="text-red-600">para tu vehículo</span>
              </h1>
              
              <!-- Vehículos Disponibles (Extra Section) -->
              <div class="mt-10 flex flex-col items-start gap-4">
                <span class="text-white font-semibold text-lg lg:text-xl drop-shadow-md">Más de 5,000 medidas disponibles.</span>
                <div class="flex items-center gap-8 lg:gap-12 mt-2">
                  <!-- AUTO -->
                  <Link href="/catalog?category_id[]=1" class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-pointer pointer-events-auto">
                    <svg class="w-12 sm:w-14 h-6 sm:h-8 text-white fill-current -scale-x-100" viewBox="0 0 123 40" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M103.94,23.97c5.39,0,9.76,4.37,9.76,9.76c0,5.39-4.37,9.76-9.76,9.76c-5.39,0-9.76-4.37-9.76-9.76 C94.18,28.34,98.55,23.97,103.94,23.97L103.94,23.97z M23,29.07v3.51h3.51C26.09,30.86,24.73,29.49,23,29.07L23,29.07z M26.52,34.87H23v3.51C24.73,37.97,26.09,36.6,26.52,34.87L26.52,34.87z M20.71,38.39v-3.51H17.2 C17.62,36.6,18.99,37.96,20.71,38.39L20.71,38.39z M17.2,32.59h3.51v-3.51C18.99,29.49,17.62,30.86,17.2,32.59L17.2,32.59z M105.09,29.07v3.51h3.51C108.18,30.86,106.82,29.49,105.09,29.07L105.09,29.07z M108.6,34.87h-3.51v3.51 C106.82,37.97,108.18,36.6,108.6,34.87L108.6,34.87z M102.8,38.39v-3.51h-3.51C99.71,36.6,101.07,37.96,102.8,38.39L102.8,38.39z M99.28,32.59h3.51v-3.51C101.07,29.49,99.71,30.86,99.28,32.59L99.28,32.59z M49.29,12.79c-1.54-0.35-3.07-0.35-4.61-0.28 C56.73,6.18,61.46,2.07,75.57,2.9l-1.94,12.87L50.4,16.65c0.21-0.61,0.33-0.94,0.37-1.55C50.88,13.36,50.86,13.15,49.29,12.79 L49.29,12.79z M79.12,3.13L76.6,15.6l24.13-0.98c2.48-0.1,2.91-1.19,1.41-3.28c-0.68-0.95-1.44-1.89-2.31-2.82 C93.59,1.86,87.38,3.24,79.12,3.13L79.12,3.13z M0.46,27.28H1.2c0.46-2.04,1.37-3.88,2.71-5.53c2.94-3.66,4.28-3.2,8.65-3.99 l24.46-4.61c5.43-3.86,11.98-7.3,19.97-10.2C64.4,0.25,69.63-0.01,77.56,0c4.54,0.01,9.14,0.28,13.81,0.84 c2.37,0.15,4.69,0.47,6.97,0.93c2.73,0.55,5.41,1.31,8.04,2.21l9.8,5.66c2.89,1.67,3.51,3.62,3.88,6.81l1.38,11.78h1.43v6.51 c-0.2,2.19-1.06,2.52-2.88,2.52h-2.37c0.92-20.59-28.05-24.11-27.42,1.63H34.76c3.73-17.75-14.17-23.91-22.96-13.76 c-2.67,3.09-3.6,7.31-3.36,12.3H2.03c-0.51-0.24-0.91-0.57-1.21-0.98c-1.05-1.43-0.82-5.74-0.74-8.23 C0.09,27.55-0.12,27.28,0.46,27.28L0.46,27.28z M21.86,23.97c5.39,0,9.76,4.37,9.76,9.76c0,5.39-4.37,9.76-9.76,9.76 c-5.39,0-9.76-4.37-9.76-9.76C12.1,28.34,16.47,23.97,21.86,23.97L21.86,23.97z"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">Auto</span>
                  </Link>
                  <!-- SUV -->
                  <Link href="/catalog?category_id[]=2" class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-pointer pointer-events-auto">
                    <svg class="w-12 sm:w-14 h-6 sm:h-8 text-white fill-current" viewBox="0 0 260 140" xmlns="http://www.w3.org/2000/svg">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M246,90.011V59.995c0-5.523-4.48-9.995-10-9.995h-50L156.97,6.416C155.11,3.634,152.34,2,149,2H28 c-5.52,0-10,4.446-10,9.969V30h-8c-4.42,0-8,3.56-8,7.983v40.022C2,82.427,5.58,86,10,86h8v20h16.458 c2.8-15.959,16.702-28.066,33.462-28.066c16.75,0,30.708,12.107,33.518,28.066h72.958c2.8-15.959,16.764-28.066,33.524-28.066 c16.75,0,30.624,12.107,33.434,28.066H250c4.42,0,8-3.563,8-7.985v-8.004H246z M86,50H30V13.97h56V50z M98,50V13.97h48L170,50H98z M68,138c-14.336,0-26.083-11.706-26.083-26.051s11.664-26.014,26-26.014s26,11.669,26,26.014S82.336,138,68,138z M67.917,99.943 c-6.617,0-12,5.386-12,12.006c0,6.621,5.383,12.006,12,12.006s12-5.386,12-12.006C79.917,105.329,74.534,99.943,67.917,99.943z M208,138c-14.337,0-26.083-11.706-26.083-26.051s11.663-26.014,26-26.014s26,11.669,26,26.014S222.337,138,208,138z M207.917,99.943c-6.617,0-12,5.386-12,12.006c0,6.621,5.383,12.006,12,12.006s12-5.386,12-12.006 C219.917,105.329,214.534,99.943,207.917,99.943z"/>
                    </svg>
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">SUV</span>
                  </Link>
                  <!-- 4x4 (PICKUP) -->
                  <Link href="/catalog?category_id[]=3" class="flex flex-col items-center gap-3 opacity-90 hover:opacity-100 transition-opacity cursor-pointer pointer-events-auto">
                    <img src="/images/4x4.png?v=3" class="w-12 sm:w-14 h-6 sm:h-8 object-contain brightness-0 invert -scale-x-100 scale-[1.7] transform-gpu" alt="4x4">
                    <span class="text-white text-sm lg:text-base font-bold uppercase tracking-widest">4x4</span>
                  </Link>
                </div>
              </div>
            </div>

            <!-- Slide 2 Text -->
            <div class="absolute inset-0 w-full px-4 sm:px-6 md:pl-[1%] lg:pl-[2%] xl:pl-[3%] 2xl:pl-[4%] flex flex-col justify-center items-start transition-all duration-700 ease-out transform pt-12" :class="currentHeroImageIndex === 1 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
              <span class="text-action font-extrabold uppercase tracking-wide text-sm lg:text-base mb-3 drop-shadow-sm">LÍNEA TBR | TRANSPORTE, CARGA Y PASAJEROS</span>
              <h1 class="text-white font-black text-4xl lg:text-5xl xl:text-6xl 2xl:text-[4rem] leading-[1.05] uppercase tracking-tight drop-shadow-lg">
                NEUMÁTICOS<br>
                PARA FLOTAS QUE <br>
                <span class="text-action">NO SE DETIENEN</span>
              </h1>
              <span class="text-white font-semibold text-lg lg:text-xl drop-shadow-md mt-6">Mayor rendimiento en cada kilómetro.</span>
            </div>

            <!-- Slide 3 Text -->
            <div class="absolute inset-0 w-full px-4 sm:px-6 md:pl-[1%] lg:pl-[2%] xl:pl-[3%] 2xl:pl-[4%] flex flex-col justify-center items-start transition-all duration-700 ease-out transform pt-12" :class="currentHeroImageIndex === 2 ? 'opacity-100 translate-y-0' : 'opacity-0 translate-y-8'">
              <span class="text-action font-extrabold uppercase tracking-wide text-sm lg:text-base mb-3 drop-shadow-sm">LÍNEA OTR | MINERÍA, CONSTRUCCIÓN Y AGRÍCOLA</span>
              <h1 class="text-white font-black text-4xl lg:text-5xl xl:text-6xl 2xl:text-[4rem] leading-[1.05] uppercase tracking-tight drop-shadow-lg">
                NEUMÁTICOS PARA<br>
                LAS OPERACIONES <br>
                <span class="text-action">MÁS EXIGENTES</span>
              </h1>
              <span class="text-white font-semibold text-lg lg:text-xl drop-shadow-md mt-6">Preparados para cualquier terreno.</span>
            </div>

          </div>
          
          <!-- Spacer to show the image proporcionalmente -->
          <div class="w-full pb-[100%] sm:pb-[60%] md:pb-[20%] lg:pb-[29%]"></div>
        </div>
        
        <!-- Buscador Block (Pushing the header up) -->
        <div class="max-w-7xl mx-auto w-full px-4 sm:px-6 lg:px-8 relative z-40 text-center pointer-events-auto">
          <!-- Buscador overlapping exactly 50% on the bottom border minus 3 pixels -->
          <div class="transform translate-y-[calc(50%-3px)] relative z-20">
            <div class="max-w-[1050px] mx-auto px-6 py-5 sm:px-8 sm:py-5 text-left bg-white/80 backdrop-blur-none shadow-[0_8px_40px_rgb(0,0,0,0.08)] rounded-[1rem] border border-gray-100/50">

            <!-- Título Principal del Buscador -->
            <h2 class="text-xl sm:text-2xl font-black text-action mb-4 uppercase tracking-wide text-left">
              Encuentra tus Neumáticos aquí:
            </h2>

            <!-- Tabs and Help Link Header -->
            <div class="relative flex justify-start items-end border-b border-gray-100 mb-6">
              <!-- Pestañas -->
              <div class="flex w-full sm:w-auto min-w-[280px] gap-2">
                <button @click="activeTab = 'medida'" :class="{'border-action text-action': activeTab === 'medida', 'border-transparent text-black hover:text-gray-600': activeTab !== 'medida'}" class="flex-1 pb-4 text-sm font-extrabold uppercase tracking-wider border-b-2 transition-all text-left">
                  Por Medida
                </button>
                <button @click="activeTab = 'vehiculo'" :class="{'border-action text-action': activeTab === 'vehiculo', 'border-transparent text-black hover:text-gray-600': activeTab !== 'vehiculo'}" class="flex-1 pb-4 text-sm font-extrabold uppercase tracking-wider border-b-2 transition-all text-left">
                  Por Vehículo
                </button>
              </div>
              <!-- Link -->
              <div class="hidden sm:block absolute right-0 pb-4">
                <Link href="/guide" class="text-black hover:text-gray-900 text-sm font-semibold flex items-center gap-1 transition-colors">
                  <HelpCircle class="w-5 h-5" /> ¿No sabes tu medida? <span class="text-action">Te ayudamos</span>
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
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Ancho</label>
                    <div 
                      @click="toggleDropdown('width')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-normal': !searchFilters.width, 'font-bold': searchFilters.width}">{{ searchFilters.width ? searchFilters.width : 'Ej: 225' }}</span>
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
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Perfil</label>
                    <div 
                      @click="toggleDropdown('profile')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-normal': !searchFilters.profile, 'font-bold': searchFilters.profile}">{{ searchFilters.profile ? searchFilters.profile : 'Ej: 45' }}</span>
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
                            placeholder="Buscar perfil..." 
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
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Aro</label>
                    <div 
                      @click="toggleDropdown('rim')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-normal': !searchFilters.rim, 'font-bold': searchFilters.rim}">{{ searchFilters.rim ? searchFilters.rim : 'Ej: 18' }}</span>
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
                            placeholder="Buscar aro..." 
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
                <!-- 5 columns for large screens to fit all inline -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 mb-2">
                  
                  <!-- Dropdown Marca -->
                  <div :class="['relative', isMakeDropdownOpen ? 'z-50' : 'z-20']">
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Marca</label>
                    <div 
                      @click="toggleDropdown('make')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 bg-gray-50 hover:bg-gray-100/50 cursor-pointer flex items-center justify-between text-gray-900 relative transition-colors"
                    >
                      <span :class="{'text-gray-400 font-normal': !selectedVehicle.makeName, 'font-bold': selectedVehicle.makeName}">{{ selectedVehicle.makeName || 'Seleccionar Marca' }}</span>
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
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Modelo</label>
                    <div 
                      @click="toggleDropdown('model')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 flex items-center justify-between text-gray-900 relative transition-colors"
                      :class="[(selectedVehicle.makeSlug || selectedVehicle.makeName) ? 'bg-gray-50 hover:bg-gray-100/50 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50/50']"
                    >
                      <span :class="{'text-gray-400 font-normal': !selectedVehicle.modelName, 'font-bold': selectedVehicle.modelName}">{{ selectedVehicle.modelName || 'Seleccionar Modelo' }}</span>
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
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Año</label>
                    <div 
                      @click="toggleDropdown('year')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 flex items-center justify-between text-gray-900 relative transition-colors"
                      :class="[(selectedVehicle.modelSlug || selectedVehicle.modelName) ? 'bg-gray-50 hover:bg-gray-100/50 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50/50']"
                    >
                      <span :class="{'text-gray-400 font-normal': !selectedVehicle.yearName, 'font-bold': selectedVehicle.yearName}">{{ selectedVehicle.yearName || 'Seleccionar Año' }}</span>
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

                  <!-- Dropdown Versión -->
                  <div :class="['relative', isTrimDropdownOpen ? 'z-50' : 'z-20']">
                    <label class="block text-xs font-bold text-black uppercase tracking-wide mb-2">Versión <span class="font-normal text-[10px] lowercase text-gray-400">(opcional)</span></label>
                    <div 
                      @click="toggleDropdown('trim')"
                      class="w-full h-14 px-4 rounded-xl border border-gray-100 flex items-center justify-between text-gray-900 relative transition-colors"
                      :class="[(selectedVehicle.yearSlug || selectedVehicle.yearName) ? 'bg-gray-50 hover:bg-gray-100/50 cursor-pointer' : 'opacity-50 cursor-not-allowed bg-gray-50/50']"
                    >
                      <span :class="{'text-gray-400 font-normal': !selectedVehicle.trimName, 'font-bold': selectedVehicle.trimName}" class="truncate pr-2">{{ selectedVehicle.trimName || 'Seleccionar Versión' }}</span>
                      <ChevronDown class="w-4 h-4 text-gray-400 pointer-events-none shrink-0" />
                    </div>
                    
                    <div v-if="isTrimDropdownOpen" class="absolute mt-2 w-full sm:w-[350px] max-h-80 overflow-y-auto bg-white border border-gray-100 shadow-[0_10px_40px_rgb(0,0,0,0.08)] rounded-2xl p-4 right-0 lg:left-0 lg:right-auto z-50">
                      <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                        <button 
                          v-for="trim in vehicleTrims" 
                          :key="trim.slug"
                          @click.stop="selectTrim(trim)"
                          type="button"
                          class="text-left px-3 py-2 text-sm rounded hover:bg-red-50 hover:text-action transition-colors truncate"
                          :title="trim.name"
                        >
                          {{ trim.name }}
                        </button>
                        <div v-if="vehicleTrims.length === 0" class="col-span-full text-center text-gray-500 py-4 text-sm">
                          No hay versiones disponibles
                        </div>
                      </div>
                    </div>
                    <div v-if="isTrimDropdownOpen" @click="isTrimDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default w-full h-full"></div>
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
          <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-[320px] sm:pt-[280px] md:pt-[220px] lg:pt-[160px] pb-16 sm:pb-16">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-0">
            <!-- Feature 1 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start border-b border-white/50 sm:border-b-0 sm:border-r">
              <Truck class="w-8 h-8 text-white shrink-0" stroke-width="1.5" />
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">Envíos a todo el Perú</h4>
                <p class="text-xs text-gray-400 mt-0.5">Rápidos y seguros</p>
              </div>
            </div>
            <!-- Feature 2 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start border-b border-white/50 sm:border-b-0 lg:border-r">
              <ShieldCheck class="w-8 h-8 text-white shrink-0" stroke-width="1.5" />
              <div>
                <h4 class="font-black text-sm uppercase tracking-wide">Neumáticos Certificados</h4>
                <p class="text-xs text-gray-400 mt-0.5">Calidad garantizada</p>
              </div>
            </div>
            <!-- Feature 3 -->
            <div class="flex items-center gap-4 px-2 lg:px-6 py-6 sm:py-0 justify-center lg:justify-start border-b border-white/50 sm:border-b-0 sm:border-r">
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
      <!-- Carrusel de Marcas (Estilo Píldora) -->
      <section v-if="brands && brands.length > 0" class="bg-gray-50 pb-8 pt-4 sm:-mt-12 relative z-20">
        <div class="w-11/12 max-w-[1600px] mx-auto">
          <div 
            class="bg-white rounded-[2.5rem] shadow-lg border border-gray-100 p-2 sm:p-4 flex items-center relative"
            @mouseenter="handleMouseEnter"
            @mouseleave="handleMouseLeave"
          >
            
            <!-- Botón Izquierda -->
            <button @click="scrollBrands('left')" class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors text-gray-500 focus:outline-none cursor-pointer">
              <ChevronLeft class="w-5 h-5" />
            </button>

            <!-- Contenedor scrolleable (Loop Infinito) -->
            <div ref="brandsScrollContainer" class="flex-grow flex items-center gap-8 sm:gap-14 overflow-x-hidden px-4 [scrollbar-width:none] [&::-webkit-scrollbar]:hidden">
              <template v-for="n in 5" :key="`group-${n}`">
                <Link 
                  :href="`/catalog?brand_id=${brand.id}`" 
                  v-for="brand in brands" 
                  :key="`${n}-${brand.id}`" 
                  class="shrink-0 flex items-center justify-center h-12 sm:h-16 transition-all duration-300 opacity-80 hover:opacity-100 cursor-pointer"
                >
                  <img v-if="brand.logo_full_url" :src="brand.logo_full_url" :alt="brand.name" class="max-w-[100px] sm:max-w-[140px] max-h-full object-contain transition-all duration-300" />
                  <span v-else class="text-lg sm:text-xl font-black text-gray-400 hover:text-gray-900 transition-colors">{{ brand.name }}</span>
                </Link>
              </template>
            </div>

            <!-- Botón Derecha -->
            <button @click="scrollBrands('right')" class="flex-shrink-0 w-8 h-8 sm:w-10 sm:h-10 rounded-full flex items-center justify-center hover:bg-gray-100 transition-colors text-gray-500 focus:outline-none cursor-pointer">
              <ChevronRight class="w-5 h-5" />
            </button>
          </div>
        </div>
      </section>

      <!-- Promociones -->
      <section class="py-16 bg-gray-50 relative z-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <div v-if="promotions && promotions.length > 0" class="flex flex-col lg:flex-row items-center gap-8 lg:gap-12">
            <!-- Izquierda: Texto y Botón -->
            <div class="w-full lg:w-1/2">
              <div class="inline-block bg-blue-50 text-primary font-bold text-xs px-3 py-1 rounded-md mb-4 uppercase tracking-wider">
                OFERTAS ESPECIALES
              </div>
              <h2 class="text-4xl md:text-5xl lg:text-4xl xl:text-5xl font-black text-gray-900 leading-tight mb-4">
                Aprovecha nuestras <br class="hidden xl:block"> promociones&nbsp;exclusivas
              </h2>
              <p class="text-black-500 font-medium mb-8 text-lg">
                Descuentos por tiempo limitado en las mejores marcas.
              </p>
              <Link href="/catalog" class="inline-flex items-center gap-2 bg-action text-white px-6 py-3 rounded-lg font-bold hover:bg-red-700 transition-colors shadow-lg hover:shadow-xl">
                Ver todo el catálogo &rarr;
              </Link>
            </div>

            <!-- Derecha: Carrusel de Producto -->
            <div class="w-full lg:w-1/2 flex items-center justify-center gap-4 sm:gap-6" @mouseenter="stopPromoAutoPlay" @mouseleave="startPromoAutoPlay">
              <!-- Flecha Izquierda -->
              <button @click="prevPromo" class="hidden sm:flex flex-shrink-0 w-10 h-10 rounded-full bg-white shadow items-center justify-center hover:bg-gray-50 transition-colors text-gray-400 focus:outline-none z-30 cursor-pointer">
                <ChevronLeft class="w-5 h-5 mx-auto" />
              </button>

              <!-- Tarjeta (Link) -->
              <Link :href="`/catalog/${promotions[currentPromoIndex].id}`" class="w-full bg-white rounded-[1rem] shadow-[0_10px_40px_rgb(0,0,0,0.06)] hover:shadow-[0_15px_50px_rgb(0,0,0,0.1)] p-6 md:p-8 flex flex-col md:flex-row items-center gap-8 transition-shadow cursor-pointer relative z-20 min-h-[320px]">
                <!-- Descuento Badge -->
                <div class="absolute top-6 right-6 bg-action text-white text-sm font-black px-3 py-2 rounded-lg shadow-md z-10 text-center leading-none">
                  -{{ getDiscountPercentage(promotions[currentPromoIndex].price, promotions[currentPromoIndex].offer_price) }}%<br>
                  <span class="text-[10px] font-normal uppercase">OFF</span>
                </div>

                <!-- Imagen Neumático -->
                <div class="w-full md:w-5/12 flex-shrink-0 flex justify-center h-48 md:h-full items-center">
                  <img :src="promotions[currentPromoIndex].image_urls && promotions[currentPromoIndex].image_urls.length ? promotions[currentPromoIndex].image_urls[0] : 'https://images.unsplash.com/photo-1620065095360-6644bcce8937?auto=format&fit=crop&q=80&w=400&h=400'" 
                       class="max-h-full max-w-full object-contain group-hover:scale-105 transition-transform duration-300" alt="Tire" />
                </div>

                <!-- Detalles Neumático -->
                <div class="w-full md:w-7/12 flex flex-col justify-center h-full">
                  <div class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">{{ promotions[currentPromoIndex].brand?.name || 'Marca' }}</div>
                  <h3 class="text-2xl font-black text-gray-800 mb-2 leading-tight uppercase">{{ promotions[currentPromoIndex].model }}</h3>
                  <div class="text-sm text-gray-500 mb-8">{{ promotions[currentPromoIndex].width }}{{ promotions[currentPromoIndex].profile && promotions[currentPromoIndex].profile > 0 ? '/' + promotions[currentPromoIndex].profile : '' }} R{{ promotions[currentPromoIndex].rim }}</div>
                  
                  <div class="flex flex-col mt-auto">
                    <div class="flex items-end gap-3 mb-4 md:mb-0 md:absolute md:bottom-8">
                      <div class="text-3xl font-black text-action leading-none">{{ promotions[currentPromoIndex].category?.name === 'TBR' ? '$' : 'S/.' }} {{ promotions[currentPromoIndex].offer_price }}</div>
                      <div class="text-sm text-gray-400 line-through mb-1">{{ promotions[currentPromoIndex].category?.name === 'TBR' ? '$' : 'S/.' }} {{ promotions[currentPromoIndex].price }}</div>
                    </div>
                  </div>
                </div>
              </Link>

              <!-- Flecha Derecha -->
              <button @click="nextPromo" class="hidden sm:flex flex-shrink-0 w-10 h-10 rounded-full bg-white shadow items-center justify-center hover:bg-gray-50 transition-colors text-gray-400 focus:outline-none z-30 cursor-pointer">
                <ChevronRight class="w-5 h-5 mx-auto" />
              </button>
            </div>
            
            <!-- Controles Móviles (Flechas debajo de la tarjeta en móvil) -->
            <div class="flex justify-center gap-4 mt-6 sm:hidden w-full">
              <button @click="prevPromo" class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-400 focus:outline-none cursor-pointer">
                <ChevronLeft class="w-5 h-5" />
              </button>
              <button @click="nextPromo" class="w-10 h-10 rounded-full bg-white shadow flex items-center justify-center hover:bg-gray-50 transition-colors text-gray-400 focus:outline-none cursor-pointer">
                <ChevronRight class="w-5 h-5" />
              </button>
            </div>
          </div>

          <div v-if="!promotions || promotions.length === 0" class="py-12 text-center text-gray-500">
            No hay promociones activas en este momento.
          </div>
        </div>
      </section>

      <!-- Pasos de Compra -->
      <section class="py-20 bg-gray-100 border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
          <h2 class="text-3xl font-black text-gray-900 mb-3 tracking-tight">Cómo comprar en <span class="text-action">3 simples pasos</span></h2>
          <div class="w-8 h-1 bg-action mx-auto mb-16 rounded-full"></div>
          
          <div class="relative flex flex-col md:flex-row justify-center items-center gap-8 lg:gap-12">
            <!-- Línea Punteada Conectora (Fondo) -->
            <div class="hidden md:block absolute top-1/2 left-[15%] right-[15%] h-[2px] border-t-[2px] border-dashed border-gray-300 -translate-y-1/2 z-0"></div>
            
            <!-- Paso 1 -->
            <div class="relative z-10 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow p-6 lg:p-8 flex flex-col md:flex-row items-center md:items-start gap-4 lg:gap-5 w-full md:w-1/3 max-w-[360px] text-center md:text-left">
              <div class="flex-shrink-0 w-14 h-14 lg:w-16 lg:h-16 bg-action text-white rounded-full flex items-center justify-center shadow-md">
                <Search class="w-6 h-6 lg:w-7 lg:h-7" stroke-width="2" />
              </div>
              <div class="mt-2 md:mt-0">
                <h4 class="font-bold text-gray-900 text-sm lg:text-base mb-2">1. Encuentra tu medida</h4>
                <p class="text-xs lg:text-sm text-black leading-relaxed">Usa nuestro buscador para encontrar el neumático exacto que necesita tu vehículo.</p>
              </div>
            </div>

            <!-- Paso 2 -->
            <div class="relative z-10 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow p-6 lg:p-8 flex flex-col md:flex-row items-center md:items-start gap-4 lg:gap-5 w-full md:w-1/3 max-w-[360px] text-center md:text-left">
              <div class="hidden md:block absolute -left-4 lg:-left-6 top-1/2 -translate-y-1/2 w-3 h-3 bg-action rounded-full z-20 shadow-sm ring-4 ring-gray-100"></div>
              <div class="flex-shrink-0 w-14 h-14 lg:w-16 lg:h-16 bg-action text-white rounded-full flex items-center justify-center shadow-md">
                <MessageCircle class="w-6 h-6 lg:w-7 lg:h-7" stroke-width="2" />
              </div>
              <div class="mt-2 md:mt-0">
                <h4 class="font-bold text-gray-900 text-sm lg:text-base mb-2">2. Consulta stock</h4>
                <p class="text-xs lg:text-sm text-black leading-relaxed">Haz clic en el botón de WhatsApp. Te atenderemos y confirmaremos disponibilidad en minutos.</p>
              </div>
            </div>

            <!-- Paso 3 -->
            <div class="relative z-10 bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.08)] transition-shadow p-6 lg:p-8 flex flex-col md:flex-row items-center md:items-start gap-4 lg:gap-5 w-full md:w-1/3 max-w-[360px] text-center md:text-left">
              <div class="hidden md:block absolute -left-4 lg:-left-6 top-1/2 -translate-y-1/2 w-3 h-3 bg-action rounded-full z-20 shadow-sm ring-4 ring-gray-100"></div>
              <div class="flex-shrink-0 w-14 h-14 lg:w-16 lg:h-16 bg-action text-white rounded-full flex items-center justify-center shadow-md">
                <Truck class="w-6 h-6 lg:w-7 lg:h-7" stroke-width="2" />
              </div>
              <div class="mt-2 md:mt-0">
                <h4 class="font-bold text-gray-900 text-sm lg:text-base mb-2">3. Coordina la entrega</h4>
                <p class="text-xs lg:text-sm text-black leading-relaxed">Paga de forma segura y recibe o instala tus neumáticos el mismo día. ¡Así de fácil!</p>
              </div>
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
