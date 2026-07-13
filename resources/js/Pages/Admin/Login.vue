<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { Lock } from 'lucide-vue-next';

const form = useForm({
  email: '',
  password: '',
});

const submit = () => {
  form.post('/admin/login', {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <Head title="Admin Login" />
    
    <div class="max-w-md w-full space-y-8 bg-white p-10 rounded-2xl shadow-xl border border-gray-100">
      <div>
        <div class="flex items-center justify-center mb-6">
          <img src="/images/logo.png" alt="Tersal Logo" class="h-24 w-auto object-contain" />
        </div>
        <p class="mt-2 text-center text-sm text-gray-600 font-medium uppercase tracking-wider">
          Panel de Administración
        </p>
      </div>
      
      <form class="mt-8 space-y-6" @submit.prevent="submit">
        <div class="space-y-4">
          <div>
            <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
            <input id="email" type="email" v-model="form.email" required 
              class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50 transition-all" 
              placeholder="admin@tersal.com" />
            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1">{{ form.errors.email }}</div>
          </div>
          
          <div>
            <label for="password" class="block text-sm font-medium text-gray-700">Contraseña</label>
            <input id="password" type="password" v-model="form.password" required 
              class="mt-1 block w-full px-4 py-3 rounded-xl border border-gray-200 text-sm focus:ring-2 focus:ring-primary focus:border-transparent bg-gray-50 transition-all" 
              placeholder="••••••••" />
            <div v-if="form.errors.password" class="text-red-500 text-xs mt-1">{{ form.errors.password }}</div>
          </div>
        </div>

        <div>
          <button type="submit" :disabled="form.processing" 
            class="group relative w-full flex justify-center items-center gap-2 py-3 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-primary hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary shadow-lg transition-all">
            <Lock class="w-4 h-4" />
            Iniciar Sesión
          </button>
        </div>
      </form>
    </div>
  </div>
</template>
