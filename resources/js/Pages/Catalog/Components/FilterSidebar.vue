<script setup>
import { ref } from 'vue';
import { router } from '@inertiajs/vue3';
import { ChevronDown, HelpCircle } from 'lucide-vue-next';

const props = defineProps({
  brands: Array,
  filters: Object,
  widths: Array,
  profiles: Array,
  rims: Array
});

const currentFilters = ref({
  width: props.filters.width || null,
  profile: props.filters.profile || null,
  rim: props.filters.rim || null,
  brand_id: props.filters.brand_id || null,
  terrain_type: props.filters.terrain_type || null,
  price_min: props.filters.price_min || null,
  price_max: props.filters.price_max || null,
});

const showTooltip = ref(false);
const isBrandsExpanded = ref(true);

const emit = defineEmits(['applied']);

const isWidthDropdownOpen = ref(false);
const isProfileDropdownOpen = ref(false);
const isRimDropdownOpen = ref(false);

const widthSearchQuery = ref('');
const profileSearchQuery = ref('');
const rimSearchQuery = ref('');

import { computed } from 'vue';

const filteredWidths = computed(() => {
  if (!widthSearchQuery.value) return props.widths || [];
  return (props.widths || []).filter(w => String(w).includes(widthSearchQuery.value));
});

const filteredProfiles = computed(() => {
  if (!profileSearchQuery.value) return props.profiles || [];
  return (props.profiles || []).filter(p => String(p).includes(profileSearchQuery.value));
});

const filteredRims = computed(() => {
  if (!rimSearchQuery.value) return props.rims || [];
  return (props.rims || []).filter(r => String(r).includes(rimSearchQuery.value));
});

const selectWidth = (val) => { currentFilters.value.width = val; isWidthDropdownOpen.value = false; };
const selectProfile = (val) => { currentFilters.value.profile = val; isProfileDropdownOpen.value = false; };
const selectRim = (val) => { currentFilters.value.rim = val; isRimDropdownOpen.value = false; };

const applyFilters = () => {
  router.get('/catalog', currentFilters.value, { preserveState: true });
  emit('applied');
};

const setTerrain = (terrain) => {
  currentFilters.value.terrain_type = currentFilters.value.terrain_type === terrain ? null : terrain;
};

const clearFilters = () => {
  currentFilters.value = {
    width: null,
    profile: null,
    rim: null,
    brand_id: null,
    terrain_type: null,
    price_min: null,
    price_max: null,
  };
  applyFilters();
};
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-6">
    <!-- Cabecera de Filtros -->
    <div class="mb-6 flex justify-end">
      <button type="button" @click="clearFilters" class="text-xs text-action hover:text-red-800 font-bold hover:underline cursor-pointer flex items-center gap-1">
        Borrar Filtros
      </button>
    </div>

    <!-- Medidas -->
    <div class="mb-8">
      <h3 class="font-bold text-primary mb-4">Medidas</h3>
      <div class="space-y-4">
        <!-- Ancho -->
        <div :class="['relative', isWidthDropdownOpen ? 'z-50' : 'z-30']">
          <label class="text-xs text-gray-500 mb-1 block">Ancho</label>
          <div 
            @click="isWidthDropdownOpen = !isWidthDropdownOpen; if(isWidthDropdownOpen) widthSearchQuery = ''"
            class="w-full h-10 px-3 rounded border border-gray-200 bg-white cursor-pointer flex items-center justify-between text-sm text-gray-900 relative"
          >
            <span :class="{'text-gray-400': !currentFilters.width}">{{ currentFilters.width || 'Todos' }}</span>
            <ChevronDown class="w-4 h-4 text-gray-500" />
          </div>
          
          <div v-if="isWidthDropdownOpen" class="absolute mt-1 w-full max-h-60 overflow-hidden bg-white border border-gray-200 shadow-xl rounded-lg flex flex-col left-0 z-50">
            <div class="p-2 border-b border-gray-100 bg-gray-50">
              <input 
                type="text" 
                v-model="widthSearchQuery" 
                placeholder="Buscar..." 
                class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                @click.stop
              >
            </div>
            <div class="p-2 overflow-y-auto">
              <div class="grid grid-cols-2 gap-1">
                <button 
                  @click.stop="selectWidth(null)"
                  type="button"
                  class="text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100 transition-colors"
                >Todos</button>
                <button 
                  v-for="w in filteredWidths" 
                  :key="w"
                  @click.stop="selectWidth(w)"
                  type="button"
                  class="text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100 transition-colors"
                >{{ w }}</button>
              </div>
            </div>
          </div>
          <div v-if="isWidthDropdownOpen" @click="isWidthDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default"></div>
        </div>

        <!-- Alto -->
        <div :class="['relative', isProfileDropdownOpen ? 'z-40' : 'z-20']">
          <label class="text-xs text-gray-500 mb-1 block">Alto</label>
          <div 
            @click="isProfileDropdownOpen = !isProfileDropdownOpen; if(isProfileDropdownOpen) profileSearchQuery = ''"
            class="w-full h-10 px-3 rounded border border-gray-200 bg-white cursor-pointer flex items-center justify-between text-sm text-gray-900 relative"
          >
            <span :class="{'text-gray-400': !currentFilters.profile}">{{ currentFilters.profile || 'Todos' }}</span>
            <ChevronDown class="w-4 h-4 text-gray-500" />
          </div>
          
          <div v-if="isProfileDropdownOpen" class="absolute mt-1 w-full max-h-60 overflow-hidden bg-white border border-gray-200 shadow-xl rounded-lg flex flex-col left-0 z-50">
            <div class="p-2 border-b border-gray-100 bg-gray-50">
              <input 
                type="text" 
                v-model="profileSearchQuery" 
                placeholder="Buscar..." 
                class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                @click.stop
              >
            </div>
            <div class="p-2 overflow-y-auto">
              <div class="grid grid-cols-2 gap-1">
                <button 
                  @click.stop="selectProfile(null)"
                  type="button"
                  class="text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100 transition-colors"
                >Todos</button>
                <button 
                  v-for="p in filteredProfiles" 
                  :key="p"
                  @click.stop="selectProfile(p)"
                  type="button"
                  class="text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100 transition-colors"
                >{{ p }}</button>
              </div>
            </div>
          </div>
          <div v-if="isProfileDropdownOpen" @click="isProfileDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default"></div>
        </div>

        <!-- Rin -->
        <div :class="['relative', isRimDropdownOpen ? 'z-30' : 'z-10']">
          <label class="text-xs text-gray-500 mb-1 block">Rin</label>
          <div 
            @click="isRimDropdownOpen = !isRimDropdownOpen; if(isRimDropdownOpen) rimSearchQuery = ''"
            class="w-full h-10 px-3 rounded border border-gray-200 bg-white cursor-pointer flex items-center justify-between text-sm text-gray-900 relative"
          >
            <span :class="{'text-gray-400': !currentFilters.rim}">{{ currentFilters.rim || 'Todos' }}</span>
            <ChevronDown class="w-4 h-4 text-gray-500" />
          </div>
          
          <div v-if="isRimDropdownOpen" class="absolute mt-1 w-full max-h-60 overflow-hidden bg-white border border-gray-200 shadow-xl rounded-lg flex flex-col left-0 z-50">
            <div class="p-2 border-b border-gray-100 bg-gray-50">
              <input 
                type="text" 
                v-model="rimSearchQuery" 
                placeholder="Buscar..." 
                class="w-full px-2 py-1.5 border border-gray-300 rounded text-sm focus:ring-1 focus:ring-primary focus:border-primary outline-none"
                @click.stop
              >
            </div>
            <div class="p-2 overflow-y-auto">
              <div class="grid grid-cols-2 gap-1">
                <button 
                  @click.stop="selectRim(null)"
                  type="button"
                  class="text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100 transition-colors"
                >Todos</button>
                <button 
                  v-for="r in filteredRims" 
                  :key="r"
                  @click.stop="selectRim(r)"
                  type="button"
                  class="text-left px-2 py-1.5 text-sm rounded hover:bg-gray-100 transition-colors"
                >{{ r }}</button>
              </div>
            </div>
          </div>
          <div v-if="isRimDropdownOpen" @click="isRimDropdownOpen = false" class="fixed inset-0 z-40 bg-transparent cursor-default"></div>
        </div>
      </div>
      
    </div>

    <!-- Sección Marcas -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-4">
        <button type="button" @click="isBrandsExpanded = !isBrandsExpanded" class="flex items-center gap-2 font-bold text-primary focus:outline-none hover:text-gray-900 transition-colors w-full text-left">
          Marca
          <ChevronDown :class="{'rotate-180': isBrandsExpanded}" class="w-4 h-4 transition-transform ml-auto" />
        </button>
      </div>
      <div v-show="isBrandsExpanded" class="space-y-3 max-h-48 overflow-y-auto pr-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full">
        <label class="flex items-center gap-3 cursor-pointer group">
          <input type="radio" v-model="currentFilters.brand_id" :value="null" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
          <span class="text-sm text-gray-600 group-hover:text-gray-900">Todas las marcas</span>
        </label>
        <label v-for="brand in brands" :key="brand.id" class="flex items-center gap-3 cursor-pointer group">
          <input type="radio" v-model="currentFilters.brand_id" :value="brand.id" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
          <span class="text-sm text-gray-600 group-hover:text-gray-900">{{ brand.name }}</span>
        </label>
      </div>
    </div>

    <!-- Sección Terreno -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-4">
        <h3 class="font-bold text-primary">Terreno</h3>
        <div class="relative cursor-pointer" @click="showTooltip = !showTooltip" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
          <HelpCircle class="w-4 h-4 text-white bg-gray-900 rounded-full fill-gray-900" />
          
          <div :class="[showTooltip ? 'opacity-100 visible' : 'opacity-0 invisible']" class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-xl shadow-lg transition-all z-20">
            <div class="font-bold mb-1 border-b border-gray-700 pb-1 flex justify-between items-center">
              <span>Tipos de Terreno:</span>
            </div>
            <ul class="space-y-1 mt-1">
              <li><strong class="text-action">H/T:</strong> Asfalto, ciudad (Suave)</li>
              <li><strong class="text-action">A/T:</strong> 50% Asfalto, 50% Tierra</li>
              <li><strong class="text-action">M/T:</strong> Off-Road, Barro extremo</li>
            </ul>
            <div class="absolute top-full left-1/2 -translate-x-1/2 border-4 border-transparent border-t-gray-900"></div>
          </div>
        </div>
      </div>
      <div class="flex bg-gray-50 rounded-lg p-1 border border-gray-200">
        <button @click="setTerrain('H/T')" :class="{'bg-white shadow-sm font-bold text-primary': currentFilters.terrain_type === 'H/T', 'text-gray-500 hover:text-gray-700': currentFilters.terrain_type !== 'H/T'}" class="flex-1 py-2 text-xs rounded transition-all">
          H/T
        </button>
        <button @click="setTerrain('A/T')" :class="{'bg-white shadow-sm font-bold text-primary': currentFilters.terrain_type === 'A/T', 'text-gray-500 hover:text-gray-700': currentFilters.terrain_type !== 'A/T'}" class="flex-1 py-2 text-xs rounded transition-all">
          A/T
        </button>
        <button @click="setTerrain('M/T')" :class="{'bg-white shadow-sm font-bold text-primary': currentFilters.terrain_type === 'M/T', 'text-gray-500 hover:text-gray-700': currentFilters.terrain_type !== 'M/T'}" class="flex-1 py-2 text-xs rounded transition-all">
          M/T
        </button>
      </div>
    </div>

    <!-- Sección Precio -->
    <div>
      <h3 class="font-bold text-primary mb-4">Precio</h3>
      <div class="flex gap-4">
        <div class="flex-1">
          <label class="text-xs text-gray-500 mb-1 block">Mínimo (S/.)</label>
          <input type="number" v-model="currentFilters.price_min" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
        </div>
        <div class="flex-1">
          <label class="text-xs text-gray-500 mb-1 block">Máximo (S/.)</label>
          <input type="number" v-model="currentFilters.price_max" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
        </div>
      </div>
    </div>

    <!-- Botón Aplicar -->
    <div class="mt-8">
      <button @click="applyFilters" class="w-full h-12 bg-action text-white font-bold rounded-lg hover:bg-red-700 transition-colors shadow-md flex items-center justify-center gap-2">
        APLICAR FILTROS
      </button>
    </div>
  </div>
</template>
