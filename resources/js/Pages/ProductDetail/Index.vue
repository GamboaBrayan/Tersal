<script setup>
import { Head, Link } from '@inertiajs/vue3';
import { ref } from 'vue';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import { CheckCircle2, Shield, Check } from 'lucide-vue-next';

const props = defineProps({
  tire: Object,
  relatedTires: {
    type: Array,
    default: () => []
  }
});

const defaultImage = 'https://images.unsplash.com/photo-1620065095360-6644bcce8937?auto=format&fit=crop&q=80&w=600&h=600';
const activeImageIndex = ref(0);
const quantity = ref(1);

const increaseQuantity = () => { if (quantity.value < 12) quantity.value++; };
const decreaseQuantity = () => { if (quantity.value > 1) quantity.value--; };

// Image helper function for related tires
const getFirstImage = (t) => {
  return t.images_json && t.images_json.length ? '/storage/' + t.images_json[0] : defaultImage;
};

const images = props.tire.images_json && props.tire.images_json.length 
  ? props.tire.images_json.map(img => '/storage/' + img) 
  : [defaultImage, defaultImage, defaultImage]; // Mock multiple images if missing

const totalAmount = () => {
  const price = props.tire.has_discount ? props.tire.offer_price : props.tire.price;
  return (price * quantity.value).toFixed(2);
};

const whatsappText = () => {
  return encodeURIComponent(`¡Hola! Estoy interesado en el neumático ${props.tire.brand?.name || ''} ${props.tire.model} en la medida ${props.tire.width}/${props.tire.profile} R${props.tire.rim} que vi en su web. Deseo confirmar el stock para ${quantity.value} unidades y coordinar la atención.`);
};

const getDiscountPercentage = (price, offerPrice) => {
  if (!price || !offerPrice) return 0;
  return Math.round(((price - offerPrice) / price) * 100);
};
</script>

<template>
  <div class="min-h-screen flex flex-col bg-gray-50">
    <Head :title="`${tire.brand?.name} ${tire.model}`" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 w-full">
      <!-- Breadcrumbs -->
      <nav class="flex text-sm text-gray-500 mb-8" aria-label="Breadcrumb">
        <ol class="inline-flex items-center space-x-1 md:space-x-3">
          <li class="inline-flex items-center">
            <Link href="/" class="hover:text-gray-900 transition-colors">Inicio</Link>
          </li>
          <li>
            <div class="flex items-center">
              <span class="mx-2">></span>
              <Link href="/catalog" class="hover:text-gray-900 transition-colors">Catálogo</Link>
            </div>
          </li>
          <li aria-current="page">
            <div class="flex items-center">
              <span class="mx-2">></span>
              <span class="text-gray-900 font-medium">{{ tire.brand?.name }} {{ tire.model }}</span>
            </div>
          </li>
        </ol>
      </nav>

      <!-- Top Section -->
      <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 bg-white p-8 rounded-2xl shadow-sm border border-gray-100 mb-12">
        <!-- Left: Image Gallery -->
        <div>
          <!-- Main Image -->
          <div class="aspect-square bg-gray-100 rounded-xl mb-4 overflow-hidden relative">
            <div v-if="tire.has_discount" class="absolute top-4 left-4 bg-action text-white text-sm font-bold px-3 py-1 rounded shadow-sm z-10 flex items-center gap-1">
              OFERTA
              <span class="bg-white text-action px-1 rounded text-xs">{{ getDiscountPercentage(tire.price, tire.offer_price) }}%</span>
            </div>
            <img :src="images[activeImageIndex]" class="w-full h-full object-cover" alt="Product Image">
          </div>
          <!-- Thumbnails -->
          <div class="grid grid-cols-4 gap-4">
            <button v-for="(img, idx) in images" :key="idx" @click="activeImageIndex = idx"
                    :class="{'ring-2 ring-primary border-transparent': activeImageIndex === idx, 'border-gray-200 hover:border-gray-300': activeImageIndex !== idx}"
                    class="aspect-square bg-gray-100 rounded-lg border overflow-hidden transition-all focus:outline-none">
              <img :src="img" class="w-full h-full object-cover" alt="Thumbnail">
            </button>
          </div>
        </div>

        <!-- Right: Actions -->
        <div class="flex flex-col">
          <div class="flex justify-between items-start mb-4">
            <div>
              <div class="text-sm font-black text-gray-900 uppercase tracking-widest mb-2">{{ tire.brand?.name || 'MARCA' }}</div>
              <h1 class="text-3xl md:text-4xl font-black text-gray-900 leading-tight mb-2">{{ tire.model }}</h1>
              <div class="text-xl text-gray-600 font-medium">{{ tire.width }}/{{ tire.profile }} R{{ tire.rim }} {{ tire.load_index }}{{ tire.speed_rating }}</div>
            </div>
            <div class="bg-green-50 text-green-700 px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 border border-green-200 shadow-sm">
              <CheckCircle2 class="w-3 h-3" /> Stock Disponible
            </div>
          </div>

          <p class="text-gray-600 mb-8 line-clamp-3 leading-relaxed">
            {{ tire.description || 'Neumático de alto rendimiento diseñado para proporcionar agarre excepcional, seguridad en condiciones húmedas y secas, y una experiencia de conducción superior para los conductores más exigentes.' }}
          </p>

          <div class="mb-8">
            <div v-if="tire.has_discount" class="text-sm text-gray-400 line-through mb-1">Precio Regular: S/. {{ tire.price }}</div>
            <div class="flex items-end gap-2">
              <span class="text-4xl font-black text-primary">S/. {{ tire.has_discount ? tire.offer_price : tire.price }}</span>
              <span class="text-gray-500 font-medium mb-1">c/u</span>
            </div>
            <div class="text-xs text-gray-400 mt-1">Precio incluye IGV</div>
          </div>

          <div class="bg-gray-50 p-6 rounded-xl border border-gray-100 mb-8">
            <div class="flex items-center justify-between mb-6">
              <div class="font-bold text-gray-900 text-sm tracking-wider">CANTIDAD</div>
              <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden bg-white shadow-sm">
                <button @click="decreaseQuantity" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors focus:outline-none">-</button>
                <div class="w-10 h-10 flex items-center justify-center font-bold text-gray-900 border-l border-r border-gray-300">{{ quantity }}</div>
                <button @click="increaseQuantity" class="w-10 h-10 flex items-center justify-center text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors focus:outline-none">+</button>
              </div>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-200">
              <div class="text-sm text-gray-600 font-medium">Total a pagar:</div>
              <div class="text-xl font-bold text-gray-900">S/. {{ totalAmount() }}</div>
            </div>
          </div>

          <a :href="'https://wa.me/' + $page.props.global_whatsapp + '?text=' + whatsappText()" target="_blank" class="w-full h-14 flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 bg-action text-white font-bold text-sm sm:text-lg rounded-xl hover:bg-red-700 transition-colors shadow-lg mb-6 leading-tight px-4 text-center">
            <span>Consultar por WhatsApp</span>
          </a>

          <div class="space-y-3">
            <div class="flex items-center gap-3 text-sm text-gray-700 font-medium">
              <Check class="w-5 h-5 text-green-600" /> Instalación Gratis en Talleres Autorizados
            </div>
            <div class="flex items-center gap-3 text-sm text-gray-700 font-medium">
              <Shield class="w-5 h-5 text-green-600" /> Garantía de fábrica por defectos
            </div>
          </div>
        </div>
      </div>

      <!-- Static Specs Section -->
      <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden mb-12">
        <div class="p-8">
          <h2 class="text-2xl font-black text-gray-900 mb-6">Especificaciones Técnicas</h2>
          <div class="space-y-4 max-w-3xl">
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="text-sm font-bold text-gray-500">ANCHO</span>
              <span class="text-sm font-bold text-primary">{{ tire.width }} mm</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="text-sm font-bold text-gray-500">PERFIL</span>
              <span class="text-sm font-bold text-primary">{{ tire.profile }}%</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="text-sm font-bold text-gray-500">DIÁMETRO DE ARO</span>
              <span class="text-sm font-bold text-primary">{{ tire.rim }}"</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="text-sm font-bold text-gray-500">ÍNDICE DE CARGA</span>
              <span class="text-sm font-bold text-primary">{{ tire.load_index }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="text-sm font-bold text-gray-500">RATING DE VELOCIDAD</span>
              <span class="text-sm font-bold text-primary">{{ tire.speed_rating }}</span>
            </div>
            <div class="flex justify-between py-3 border-b border-gray-100">
              <span class="text-sm font-bold text-gray-500">RUN FLAT</span>
              <span class="text-sm font-bold text-primary">{{ tire.is_run_flat ? 'Sí' : 'No' }}</span>
            </div>
          </div>
        </div>
      </div>

      <!-- Related Tires Carousel -->
      <div v-if="relatedTires.length > 0">
        <h2 class="text-2xl font-black text-gray-900 mb-6">Llantas Relacionadas</h2>
        
        <div class="flex overflow-x-auto gap-6 pb-6 snap-x snap-mandatory hide-scrollbar">
          <div v-for="relTire in relatedTires" :key="relTire.id" class="snap-start flex-none w-72 bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg transition-all">
            <Link :href="`/catalog/${relTire.id}`" class="block">
              <div class="aspect-square bg-gray-100 overflow-hidden relative">
                <div v-if="relTire.has_discount" class="absolute top-2 left-2 bg-action text-white text-xs font-bold px-2 py-1 rounded shadow-sm z-10 flex items-center gap-1">
                  OFERTA
                  <span class="bg-white text-action px-1 rounded text-[10px]">{{ getDiscountPercentage(relTire.price, relTire.offer_price) }}%</span>
                </div>
                <img :src="getFirstImage(relTire)" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              </div>
              <div class="p-5">
                <div class="text-xs font-black text-gray-400 uppercase tracking-wider mb-1">{{ relTire.brand?.name }}</div>
                <h3 class="text-lg font-bold text-gray-900 mb-2 truncate">{{ relTire.model }}</h3>
                <div class="text-sm text-gray-600 font-medium mb-3">{{ relTire.width }}/{{ relTire.profile }} R{{ relTire.rim }}</div>
                
                <div class="flex items-end gap-2">
                  <span class="text-xl font-black text-primary">S/. {{ relTire.has_discount ? relTire.offer_price : relTire.price }}</span>
                  <span v-if="relTire.has_discount" class="text-xs text-gray-400 line-through mb-1">S/. {{ relTire.price }}</span>
                </div>
              </div>
            </Link>
          </div>
        </div>
      </div>
    </main>

    <Footer />
  </div>
</template>
