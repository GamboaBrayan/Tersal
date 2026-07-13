<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
  tire: Object
});

const defaultImage = 'https://images.unsplash.com/photo-1620065095360-6644bcce8937?auto=format&fit=crop&q=80&w=400&h=400';

const getDiscountPercentage = (price, offerPrice) => {
  if (!price || !offerPrice) return 0;
  return Math.round(((price - offerPrice) / price) * 100);
};
</script>

<template>
  <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col h-full relative">
    <div v-if="tire.has_discount" class="absolute top-2 left-2 bg-action text-white text-xs font-bold px-2 py-1 rounded shadow-sm z-10 flex items-center gap-1">
      OFERTA
      <span class="bg-white text-action px-1 rounded text-[10px]">{{ getDiscountPercentage(tire.price, tire.offer_price) }}%</span>
    </div>
    
    <Link :href="`/catalog/${tire.id}`" class="block relative pt-[100%] bg-gray-100 group overflow-hidden">
      <img :src="tire.images_json && tire.images_json.length ? '/storage/'+tire.images_json[0] : defaultImage" 
           class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" 
           alt="Tire" />
    </Link>
    
    <div class="p-4 flex-grow flex flex-col">
      <div class="flex items-center justify-center mb-3">
        <div class="bg-gray-900 text-white text-xs font-black px-3 py-1 uppercase tracking-widest">
          {{ tire.brand?.name || 'MARCA' }}
        </div>
      </div>
      
      <div class="text-center mb-4 flex-grow">
        <h3 class="text-xl font-black text-gray-900 leading-tight mb-1">{{ tire.width }}/{{ tire.profile }} R{{ tire.rim }}</h3>
        <p class="text-sm text-gray-600 font-medium">{{ tire.model }}</p>
      </div>
      
      <div class="text-center mb-4">
        <div v-if="tire.has_discount" class="text-xs text-gray-400 line-through">Precio Regular: S/. {{ tire.price }}</div>
        <div class="text-xs text-gray-500 font-medium mt-1">Precio Especial: <span class="text-xl font-black text-gray-900">S/. {{ tire.has_discount ? tire.offer_price : tire.price }}</span></div>
      </div>
      
      <Link :href="`/catalog/${tire.id}`" 
         class="w-full h-12 flex items-center justify-center bg-action text-white font-bold rounded hover:bg-red-700 transition-colors shadow-sm text-sm">
        Ver Detalles
      </Link>
    </div>
  </div>
</template>
