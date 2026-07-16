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

const applyFilters = () => {
  router.get('/catalog', currentFilters.value, { preserveState: true });
};

const setTerrain = (terrain) => {
  currentFilters.value.terrain_type = currentFilters.value.terrain_type === terrain ? null : terrain;
  applyFilters();
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
    <!-- Clear Filters Header -->
    <div class="mb-6 flex justify-end">
      <button type="button" @click="clearFilters" class="text-xs text-action hover:text-red-800 font-bold hover:underline cursor-pointer flex items-center gap-1">
        Borrar Filtros
      </button>
    </div>

    <!-- Medidas -->
    <div class="mb-8">
      <h3 class="font-bold text-primary mb-4">Medidas</h3>
      <div class="space-y-4">
        <div>
          <label class="text-xs text-gray-500 mb-1 block">Ancho</label>
          <select v-model="currentFilters.width" @change="applyFilters" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
            <option :value="null">Todos</option>
            <option v-for="w in widths" :key="w" :value="w">{{ w }}</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-500 mb-1 block">Perfil</label>
          <select v-model="currentFilters.profile" @change="applyFilters" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
            <option :value="null">Todos</option>
            <option v-for="p in profiles" :key="p" :value="p">{{ p }}</option>
          </select>
        </div>
        <div>
          <label class="text-xs text-gray-500 mb-1 block">Aro</label>
          <select v-model="currentFilters.rim" @change="applyFilters" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
            <option :value="null">Todos</option>
            <option v-for="r in rims" :key="r" :value="r">{{ r }}</option>
          </select>
        </div>
      </div>
    </div>

    <!-- Brands -->
    <div class="mb-8">
      <div class="flex items-center justify-between mb-4">
        <button type="button" @click="isBrandsExpanded = !isBrandsExpanded" class="flex items-center gap-2 font-bold text-primary focus:outline-none hover:text-gray-900 transition-colors w-full text-left">
          Marca
          <ChevronDown :class="{'rotate-180': isBrandsExpanded}" class="w-4 h-4 transition-transform ml-auto" />
        </button>
      </div>
      <div v-show="isBrandsExpanded" class="space-y-3 max-h-48 overflow-y-auto pr-2 [&::-webkit-scrollbar]:w-1.5 [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 [&::-webkit-scrollbar-thumb]:rounded-full">
        <label class="flex items-center gap-3 cursor-pointer group">
          <input type="radio" v-model="currentFilters.brand_id" :value="null" @change="applyFilters" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
          <span class="text-sm text-gray-600 group-hover:text-gray-900">Todas las marcas</span>
        </label>
        <label v-for="brand in brands" :key="brand.id" class="flex items-center gap-3 cursor-pointer group">
          <input type="radio" v-model="currentFilters.brand_id" :value="brand.id" @change="applyFilters" class="w-4 h-4 text-primary border-gray-300 focus:ring-primary">
          <span class="text-sm text-gray-600 group-hover:text-gray-900">{{ brand.name }}</span>
        </label>
      </div>
    </div>

    <!-- Terrain -->
    <div class="mb-8">
      <div class="flex items-center gap-2 mb-4">
        <h3 class="font-bold text-primary">Terreno</h3>
        <!-- Tooltip trigger -->
        <div class="relative cursor-pointer" @click="showTooltip = !showTooltip" @mouseenter="showTooltip = true" @mouseleave="showTooltip = false">
          <HelpCircle class="w-4 h-4 text-white bg-gray-900 rounded-full fill-gray-900" />
          
          <!-- Tooltip content -->
          <div :class="[showTooltip ? 'opacity-100 visible' : 'opacity-0 invisible']" class="absolute left-1/2 -translate-x-1/2 bottom-full mb-2 w-64 p-3 bg-gray-900 text-white text-xs rounded-xl shadow-lg transition-all z-20">
            <div class="font-bold mb-1 border-b border-gray-700 pb-1 flex justify-between items-center">
              <span>Tipos de Terreno:</span>
            </div>
            <ul class="space-y-1 mt-1">
              <li><strong class="text-action">H/T:</strong> Asfalto, ciudad (Suave)</li>
              <li><strong class="text-action">A/T:</strong> 50% Asfalto, 50% Tierra</li>
              <li><strong class="text-action">M/T:</strong> Off-Road, Barro extremo</li>
            </ul>
            <!-- Arrow -->
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

    <!-- Price -->
    <div>
      <h3 class="font-bold text-primary mb-4">Precio</h3>
      <div class="flex gap-4">
        <div class="flex-1">
          <label class="text-xs text-gray-500 mb-1 block">Mínimo (S/.)</label>
          <input type="number" v-model="currentFilters.price_min" @blur="applyFilters" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
        </div>
        <div class="flex-1">
          <label class="text-xs text-gray-500 mb-1 block">Máximo (S/.)</label>
          <input type="number" v-model="currentFilters.price_max" @blur="applyFilters" class="w-full h-10 px-3 rounded border border-gray-200 text-sm focus:ring-1 focus:ring-primary focus:border-primary">
        </div>
      </div>
    </div>
  </div>
</template>
