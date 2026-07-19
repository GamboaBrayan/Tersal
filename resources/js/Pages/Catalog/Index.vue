<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import { ChevronLeft, ChevronRight, Filter } from 'lucide-vue-next';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import ProductCard from './Components/ProductCard.vue';
import FilterSidebar from './Components/FilterSidebar.vue';

const isMobileFiltersOpen = ref(false);

defineProps({
  tires: Object,
  isVehicleSearch: Boolean,
  recommendedTires: Array,
  alternativeTires: Array,
  recommendedSizes: Array,
  alternativeSizes: Array,
  brands: Array,
  filters: Object,
  widths: Array,
  profiles: Array,
  rims: Array
});
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head title="Catálogo" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <div class="flex items-center justify-between mb-8">
        <h1 :class="{'text-lg font-normal text-gray-900': tires?.total > 0, 'text-base font-normal text-gray-600': !tires?.total}">
          {{ tires?.total || 0 }} resultados <span v-if="filters.width" class="text-primary ml-2">{{ filters.width }}/{{ filters.profile }} R{{ filters.rim }}</span>
        </h1>
        <div class="flex items-center gap-3">
          <!-- Botón de filtros móvil -->
          <button @click="isMobileFiltersOpen = !isMobileFiltersOpen" class="lg:hidden h-10 px-4 flex items-center justify-center gap-2 bg-gray-100 rounded border border-gray-300 text-sm font-bold text-gray-700">
            <Filter class="w-4 h-4" /> Filtros
          </button>

          <span class="text-sm font-medium text-gray-600 hidden sm:inline">Ordenar por:</span>
          <select class="h-10 px-4 rounded border border-gray-300 text-sm focus:ring-1 focus:ring-primary focus:border-primary bg-white hidden sm:block">
            <option>Recomendados</option>
            <option>Menor Precio</option>
            <option>Mayor Precio</option>
          </select>
        </div>
      </div>

      <div class="flex flex-col lg:flex-row gap-8">
        <!-- Barra Lateral (Filtros) -->
        <aside :class="{'hidden lg:block': !isMobileFiltersOpen, 'block': isMobileFiltersOpen}" class="w-full lg:w-1/4 flex-shrink-0">
          <FilterSidebar :brands="brands" :filters="filters" :widths="widths" :profiles="profiles" :rims="rims" @applied="isMobileFiltersOpen = false" />
        </aside>

        <!-- Cuadrícula de Productos -->
        <div class="w-full lg:w-3/4">
          
          <!-- Búsqueda por Vehículo Layout -->
          <template v-if="isVehicleSearch">
            <div class="mb-8">
              <h2 class="text-lg text-gray-600 mb-2">Resultados para tu vehículo: <span class="font-bold text-gray-900">{{ filters.vehicle_make }} {{ filters.vehicle_model }} {{ filters.vehicle_year }}</span></h2>
            </div>
            
            <!-- Recomendados -->
            <div class="mb-12">
              <h2 class="text-2xl font-black text-gray-900 mb-2 flex items-center gap-2">
                Recomendados (Medida Original)
              </h2>
              
              <!-- Tags de Medidas -->
              <div v-if="recommendedSizes?.length" class="flex flex-wrap gap-2 mb-6">
                <span v-for="(size, idx) in recommendedSizes" :key="idx" class="px-3 py-1 bg-green-100 text-green-800 text-sm font-bold rounded-full border border-green-200">
                  {{ size.width }}/{{ size.profile }} R{{ size.rim }}
                </span>
              </div>

              <div v-if="recommendedTires?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <ProductCard v-for="tire in recommendedTires" :key="tire.id" :tire="tire" />
              </div>
              <div v-else class="bg-blue-50 text-blue-700 p-6 rounded-xl border border-blue-100 flex items-start gap-4">
                <svg class="w-6 h-6 flex-shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                  <h3 class="font-bold mb-1">Sin stock en medida original</h3>
                  <p class="text-sm">Actualmente no contamos con la medida exacta de fábrica, pero revisa nuestras opciones alternativas compatibles a continuación.</p>
                </div>
              </div>
            </div>

            <!-- Alternativas -->
            <div>
              <h2 class="text-2xl font-black text-gray-900 mb-2">Alternativas Compatibles</h2>
              
              <!-- Tags de Medidas -->
              <div v-if="alternativeSizes?.length" class="flex flex-wrap gap-2 mb-6">
                <span v-for="(size, idx) in alternativeSizes" :key="idx" class="px-3 py-1 bg-gray-100 text-gray-700 text-sm font-bold rounded-full border border-gray-200">
                  {{ size.width }}/{{ size.profile }} R{{ size.rim }}
                </span>
              </div>

              <div v-if="alternativeTires?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
                <ProductCard v-for="tire in alternativeTires" :key="tire.id" :tire="tire" />
              </div>
              <div v-else class="bg-gray-50 text-gray-500 p-6 rounded-xl border border-gray-100 text-center">
                No encontramos medidas alternativas compatibles en stock.
              </div>
            </div>
          </template>

          <!-- Búsqueda Estándar Layout -->
          <template v-else>
            <div v-if="tires?.data?.length > 0" class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-6">
              <ProductCard v-for="tire in tires.data" :key="tire.id" :tire="tire" />
            </div>
            
            <div v-else class="bg-white rounded-xl shadow-sm border border-gray-100 p-16 text-center">
              <div class="text-gray-400 mb-4">
                <svg class="w-16 h-16 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
              </div>
              <h3 class="text-lg font-bold text-gray-900 mb-2">No encontramos resultados</h3>
              <p class="text-gray-500">Intenta ajustando los filtros de búsqueda.</p>
            </div>
          </template>

          <!-- Paginación -->
          <div class="mt-12 flex justify-center" v-if="tires?.links && tires.links.length > 3">
            <div class="flex flex-wrap justify-center gap-1 sm:gap-2">
              <template v-for="(link, idx) in tires.links" :key="idx">
                <Link 
                  v-if="link.url"
                  :href="link.url" 
                  :class="[
                    'px-4 py-2 border rounded-lg text-sm font-bold transition-colors flex items-center justify-center',
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
                    'px-4 py-2 border border-gray-200 rounded-lg text-sm font-bold text-gray-400 bg-gray-50 flex items-center justify-center'
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
      </div>
    </main>

    <Footer />
  </div>
</template>
