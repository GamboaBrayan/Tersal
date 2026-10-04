<script setup>
import { Head, Link, usePage } from '@inertiajs/vue3';
import Header from '../../Shared/Header.vue';
import Footer from '../../Shared/Footer.vue';
import WhatsAppFloatingBtn from '../../Shared/WhatsAppFloatingBtn.vue';
import {  
  Headphones, 
  CheckCircle, 
  Sparkles,
  Target,
  Binoculars,
  Gem,
  Users,
  Search,
  ShieldCheck
} from 'lucide-vue-next';
</script>

<template>
  <div class="min-h-screen flex flex-col bg-white">
    <Head title="Sobre Nosotros - Tersal" />
    <Header />
    <WhatsAppFloatingBtn />

    <main class="flex-grow">
      <!-- 1. HERO SECTION (Fondo oscuro con técnico / taller y texto lateral) -->
      <section class="relative bg-black text-white min-h-[460px] md:min-h-[520px] flex items-center overflow-hidden">
        <!-- Imagen nítida alineada a la derecha como en el diseño de referencia -->
        <div class="absolute inset-y-0 right-0 w-full lg:w-3/5 h-full pointer-events-none">
          <img 
            src="/images/about-workshop.jpg" 
            alt="Tersal Asesoría y Neumáticos" 
            class="w-full h-full object-cover object-right md:object-center"
          />
          <!-- Degradado de negro a transparente para fusionarse perfectamente con el lado izquierdo -->
          <div class="absolute inset-0 bg-gradient-to-r from-black via-black/60 to-transparent lg:via-black/30"></div>
          <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-transparent to-black/30 lg:hidden"></div>
        </div>
        
        <!-- Degradado base para asegurar contraste de texto a la izquierda -->
        <div class="absolute inset-0 bg-gradient-to-r from-black via-black/90 to-transparent lg:w-1/2 z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 md:py-24 relative z-10 w-full">
          <div class="max-w-md lg:max-w-[480px] space-y-6">
            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black tracking-tight text-white">
              TERSAL
            </h1>
            
            <div class="space-y-3">
              <!-- Barra roja horizontal decorativa antes del subtítulo -->
              <div class="w-12 h-1 bg-action rounded-full"></div>
              <h2 class="text-xl sm:text-2xl lg:text-[25px] font-black text-white leading-snug">
                Especialistas en neumáticos y en encontrar la opción adecuada para cada vehículo.
              </h2>
            </div>

            <div class="space-y-4 text-gray-300 text-sm sm:text-base leading-relaxed font-normal">
              <p>
                En Tersal entendemos que elegir un neumático no debería reducirse a un simple precio. Por eso, analizamos las características de cada vehículo, el tipo de uso y las necesidades del cliente para recomendar la opción que realmente corresponde.
              </p>
              <p>
                Trabajamos con neumáticos para vehículos particulares, SUV, camionetas, vehículos comerciales y aplicaciones especializadas, ofreciendo soluciones PCR, TBR y OTR de las marcas más prestigiosas a nivel internacional.
              </p>
            </div>
          </div>
        </div>
      </section>

      <!-- 2. QUIÉNES SOMOS SECTION (Texto a la izquierda + 3 categorías de vehículos PCR, TBR, OTR a la derecha) -->
      <section class="py-16 md:py-24 bg-white relative overflow-hidden">
        <!-- Fondo de trama sutil con puntos -->
        <div class="absolute inset-0 bg-[radial-gradient(#d1d5db_1px,transparent_1px)] [background-size:20px_20px] opacity-40 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10 items-center">
            
            <!-- Columna Izquierda: Contenido -->
            <div class="lg:col-span-6 space-y-5">
              <span class="text-action font-extrabold uppercase tracking-widest text-xs sm:text-sm block">
                QUIÉNES SOMOS
              </span>
              <h2 class="text-2xl sm:text-3xl lg:text-[34px] xl:text-[37px] font-black text-black leading-tight tracking-tight">
                <span class="whitespace-nowrap">No vendemos neumáticos.</span><br />
                <span class="text-action">Te ayudamos a encontrar el adecuado.</span>
              </h2>
              
              <div class="space-y-4 text-gray-700 text-sm sm:text-base leading-relaxed font-normal pt-1 max-w-xl">
                <p>
                  Tersal nace con la idea de que cada vehículo y cada conductor tienen necesidades diferentes. Por eso, combinamos conocimiento del sector, asesoría profesional y herramientas digitales para ofrecer una experiencia de compra más clara, segura y confiable.
                </p>
                <p>
                  Nuestro compromiso es brindarte atención personalizada, productos originales y recomendaciones basadas en el uso real de tu vehículo, para que tomes la mejor decisión.
                </p>
              </div>
            </div>

            <!-- Columna Derecha: 3 Banners de categorías (PCR, TBR, OTR) con corte oblicuo / diseño de tarjetas -->
            <div class="lg:col-span-6">
              <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 md:gap-4">
                
                <!-- 1. PCR -->
                <div class="group relative h-[320px] sm:h-[380px] rounded-xl overflow-hidden shadow-lg border border-gray-100 flex flex-col justify-end bg-black">
                  <img 
                    src="/images/category-pcr.jpg" 
                    alt="PCR - Vehículos particulares, SUV y camionetas" 
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                  />
                  <!-- Degradado oscuro inferior para contraste del texto -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                  
                  <div class="relative z-10 p-5">
                    <h3 class="text-2xl sm:text-3xl font-black text-white tracking-wider">
                      PCR
                    </h3>
                    <div class="w-8 h-0.5 bg-action my-2"></div>
                    <p class="text-xs text-gray-200 font-medium leading-snug">
                      Vehículos particulares, SUV y camionetas.
                    </p>
                  </div>
                </div>

                <!-- 2. TBR -->
                <div class="group relative h-[320px] sm:h-[380px] rounded-xl overflow-hidden shadow-lg border border-gray-100 flex flex-col justify-end bg-black">
                  <img 
                    src="/images/category-tbr.jpg" 
                    alt="TBR - Camiones y buses" 
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                  />
                  <!-- Degradado oscuro inferior para contraste del texto -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                  
                  <div class="relative z-10 p-5">
                    <h3 class="text-2xl sm:text-3xl font-black text-white tracking-wider">
                      TBR
                    </h3>
                    <div class="w-8 h-0.5 bg-action my-2"></div>
                    <p class="text-xs text-gray-200 font-medium leading-snug">
                      Camiones y buses.
                    </p>
                  </div>
                </div>

                <!-- 3. OTR -->
                <div class="group relative h-[320px] sm:h-[380px] rounded-xl overflow-hidden shadow-lg border border-gray-100 flex flex-col justify-end bg-black">
                  <img 
                    src="/images/category-otr.jpg" 
                    alt="OTR - Aplicaciones fuera de carretera" 
                    class="absolute inset-0 w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                  />
                  <!-- Degradado oscuro inferior para contraste del texto -->
                  <div class="absolute inset-0 bg-gradient-to-t from-black via-black/50 to-transparent"></div>
                  
                  <div class="relative z-10 p-5">
                    <h3 class="text-2xl sm:text-3xl font-black text-white tracking-wider">
                      OTR
                    </h3>
                    <div class="w-8 h-0.5 bg-action my-2"></div>
                    <p class="text-xs text-gray-200 font-medium leading-snug">
                      Aplicaciones fuera de carretera.
                    </p>
                  </div>
                </div>

              </div>
            </div>

          </div>
        </div>
      </section>

      <!-- 3. PILARES: MISIÓN, VISIÓN Y VALORES -->
      <section class="py-16 md:py-20 bg-[#fafafa]/50 relative z-10">
        <!-- Fondo con trama de puntos sutil en toda la sección -->
        <div class="absolute inset-0 bg-[radial-gradient(#d1d5db_1.2px,transparent_1.2px)] [background-size:22px_22px] opacity-60 pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
          <div class="grid grid-cols-1 md:grid-cols-3 gap-6 lg:gap-8 items-stretch">
            
            <!-- Card Misión -->
            <div class="bg-white px-8 pt-9 pb-8 sm:px-10 sm:pt-10 sm:pb-8 rounded-[20px] border border-gray-200/90 shadow-[0_4px_25px_-5px_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 flex flex-col items-start text-left">
              <div class="text-action">
                <Target class="w-10 h-10 stroke-[1.8]" />
              </div>
              <!-- Raya roja debajo del icono -->
              <div class="w-9 h-1 bg-action rounded-full mt-3.5 mb-5"></div>

              <h3 class="text-2xl sm:text-[26px] font-black text-black mb-3 tracking-tight">
                Misión
              </h3>
              <p class="text-gray-600 text-sm sm:text-[15px] leading-relaxed font-normal">
                Ayudar a cada cliente a encontrar el neumático adecuado para su vehículo y propósito, mediante asesoría profesional, atención personalizada y productos de calidad.
              </p>
            </div>

            <!-- Card Visión -->
            <div class="bg-white px-8 pt-9 pb-8 sm:px-10 sm:pt-10 sm:pb-8 rounded-[20px] border border-gray-200/90 shadow-[0_4px_25px_-5px_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 flex flex-col items-start text-left">
              <div class="text-action">
                <Binoculars class="w-10 h-10 stroke-[1.8]" />
              </div>
              <!-- Raya roja debajo del icono -->
              <div class="w-9 h-1 bg-action rounded-full mt-3.5 mb-5"></div>

              <h3 class="text-2xl sm:text-[26px] font-black text-black mb-3 tracking-tight">
                Visión
              </h3>
              <p class="text-gray-600 text-sm sm:text-[15px] leading-relaxed font-normal">
                Ser una empresa referente en el mercado peruano de neumáticos, reconocida por su asesoría, confianza y experiencia de compra.
              </p>
            </div>

            <!-- Card Valores -->
            <div class="bg-white px-8 pt-9 pb-8 sm:px-10 sm:pt-10 sm:pb-8 rounded-[20px] border border-gray-200/90 shadow-[0_4px_25px_-5px_rgba(0,0,0,0.05)] hover:shadow-md transition-all duration-300 flex flex-col items-start text-left">
              <div class="text-action">
                <Gem class="w-10 h-10 stroke-[1.8]" />
              </div>
              <!-- Raya roja debajo del icono -->
              <div class="w-9 h-1 bg-action rounded-full mt-3.5 mb-5"></div>

              <h3 class="text-2xl sm:text-[26px] font-black text-black mb-3 tracking-tight">
                Valores
              </h3>
              <ul class="text-gray-700 text-sm sm:text-[15px] space-y-1.5 font-normal">
                <li class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-action flex-shrink-0"></span>
                  <span>Confianza</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-action flex-shrink-0"></span>
                  <span>Transparencia</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-action flex-shrink-0"></span>
                  <span>Orientación al cliente</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-action flex-shrink-0"></span>
                  <span>Seguridad</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-action flex-shrink-0"></span>
                  <span>Responsabilidad</span>
                </li>
                <li class="flex items-center gap-2">
                  <span class="w-1.5 h-1.5 rounded-full bg-action flex-shrink-0"></span>
                  <span>Innovación</span>
                </li>
              </ul>
            </div>

          </div>
        </div>
      </section>

      <!-- 4. NUESTRA PROMESA (Auto con punteros / viñetas a la derecha) -->
      <section class="py-16 md:py-24 bg-gray-50 border-t border-gray-200/80 relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
          
          <!-- Encabezado de sección -->
          <div class="text-center max-w-3xl mx-auto mb-12 sm:mb-16 space-y-3">
            <span class="text-action font-extrabold uppercase tracking-widest text-xs sm:text-sm block">
              NUESTRA PROMESA
            </span>
            <h2 class="text-3xl sm:text-4xl lg:text-5xl font-black text-black tracking-tight leading-tight">
              <span>Elegir bien empieza por</span>
              <span class="block text-action">entender lo que necesitas</span>
            </h2>
            <p class="text-gray-600 text-sm sm:text-base font-normal max-w-2xl mx-auto leading-relaxed pt-1">
              En Tersal nos comprometemos a brindarte una asesoría clara, honesta y personalizada para que tomes la mejor decisión para tu vehículo y propósito.
            </p>
          </div>

          <!-- Contenedor Ilustración + Items de promesa -->
          <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-center">
            
            <!-- Lado Izquierdo: Auto -->
            <div class="lg:col-span-7 flex justify-center items-center">
              <div class="relative w-full max-w-lg lg:max-w-xl">
                <img 
                  src="/images/about-car-promise.jpg" 
                  alt="Vehículo de Nuestra Promesa" 
                  class="w-full h-auto object-contain drop-shadow-md rounded-2xl"
                />
              </div>
            </div>

            <!-- Lado Derecho: 3 Cards elegantes sin flecha -->
            <div class="lg:col-span-5 space-y-4">
              
              <!-- Item 1: Asesoría personalizada -->
              <div class="flex items-center gap-3">
                <!-- Punto rojo de conexión -->
                <div class="hidden sm:flex items-center">
                  <div class="w-8 h-[2px] bg-gray-200"></div>
                  <div class="w-2.5 h-2.5 rounded-full bg-action -ml-1"></div>
                </div>

                <div class="flex items-center gap-4 bg-white px-5 py-4 sm:px-6 sm:py-5 rounded-2xl border border-gray-100 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.04)] flex-1 hover:shadow-md transition-shadow">
                  <div class="w-12 h-12 rounded-xl bg-red-50 text-action flex items-center justify-center flex-shrink-0">
                    <Users class="w-6 h-6 stroke-[1.8]" />
                  </div>
                  <div class="flex-1">
                    <h4 class="text-base sm:text-[17px] font-black text-black tracking-tight leading-snug">
                      Asesoría personalizada
                    </h4>
                    <p class="text-xs sm:text-sm text-gray-500 font-normal leading-relaxed mt-0.5">
                      Conocemos tu vehículo, el uso que le das y tus necesidades antes de recomendarte una opción.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Item 2: Selección adecuada -->
              <div class="flex items-center gap-3">
                <!-- Punto rojo de conexión -->
                <div class="hidden sm:flex items-center">
                  <div class="w-8 h-[2px] bg-gray-200"></div>
                  <div class="w-2.5 h-2.5 rounded-full bg-action -ml-1"></div>
                </div>

                <div class="flex items-center gap-4 bg-white px-5 py-4 sm:px-6 sm:py-5 rounded-2xl border border-gray-100 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.04)] flex-1 hover:shadow-md transition-shadow">
                  <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center flex-shrink-0">
                    <Search class="w-6 h-6 stroke-[1.8]" />
                  </div>
                  <div class="flex-1">
                    <h4 class="text-base sm:text-[17px] font-black text-black tracking-tight leading-snug">
                      Selección adecuada
                    </h4>
                    <p class="text-xs sm:text-sm text-gray-500 font-normal leading-relaxed mt-0.5">
                      Evaluamos medida, aplicación, características y alternativas disponibles para encontrar el neumático que corresponde.
                    </p>
                  </div>
                </div>
              </div>

              <!-- Item 3: Información transparente -->
              <div class="flex items-center gap-3">
                <!-- Punto rojo de conexión -->
                <div class="hidden sm:flex items-center">
                  <div class="w-8 h-[2px] bg-gray-200"></div>
                  <div class="w-2.5 h-2.5 rounded-full bg-action -ml-1"></div>
                </div>

                <div class="flex items-center gap-4 bg-white px-5 py-4 sm:px-6 sm:py-5 rounded-2xl border border-gray-100 shadow-[0_2px_12px_-2px_rgba(0,0,0,0.04)] flex-1 hover:shadow-md transition-shadow">
                  <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center flex-shrink-0">
                    <ShieldCheck class="w-6 h-6 stroke-[1.8]" />
                  </div>
                  <div class="flex-1">
                    <h4 class="text-base sm:text-[17px] font-black text-black tracking-tight leading-snug">
                      Información transparente
                    </h4>
                    <p class="text-xs sm:text-sm text-gray-500 font-normal leading-relaxed mt-0.5">
                      Te explicamos las diferencias entre las opciones para que puedas tomar una decisión clara y con confianza.
                    </p>
                  </div>
                </div>
              </div>

            </div>

          </div>

        </div>
      </section>
    </main>

    <Footer />
  </div>
</template>
