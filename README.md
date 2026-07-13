# Tersal - Catálogo de Neumáticos

Tersal es una plataforma web moderna para la búsqueda, visualización y gestión de un catálogo de neumáticos. Permite a los usuarios encontrar el ajuste perfecto para su vehículo utilizando filtros técnicos avanzados y proporciona una experiencia fluida desde la exploración hasta el contacto.

## Tecnologías Utilizadas

- **Backend:** Laravel 11.x (PHP 8.2+)
- **Frontend:** Vue 3 (Composition API, `<script setup>`)
- **Enrutamiento y Estado:** Inertia.js
- **Estilos:** Tailwind CSS v4
- **Empaquetador:** Vite
- **Iconos:** Lucide Vue
- **Base de Datos:** MySQL / SQLite

## Características Principales

### Para los Clientes (Frontend)
- **Buscador Interactivo:** Búsqueda técnica avanzada por Ancho, Perfil y Aro.
- **Catálogo Dinámico:** Filtrado en tiempo real por marcas, tipo de terreno (H/T, A/T, M/T) y rangos de precio.
- **Guía de Neumáticos:** Interfaz educativa interactiva para enseñar al usuario sobre la lectura de las medidas del neumático.
- **Integración con WhatsApp:** Botones dinámicos que envían mensajes pre-llenados con la información específica del producto consultado.
- **Diseño Responsivo:** Interfaz limpia y adaptable a dispositivos móviles, tablets y computadoras, con animaciones sutiles (efectos de desenfoque, degradados, tarjetas con elevación).

### Para el Administrador (Dashboard)
- Panel de control protegido para la gestión de todo el contenido.
- **Gestión de Marcas:** Crear, editar, y eliminar marcas del catálogo.
- **Gestión de Neumáticos (Inventario):** Registrar y modificar neumáticos (medidas, precio normal, precio de oferta, imágenes, marca, modelo y descripciones técnicas).
- **Gestión de Promociones:** Marcar productos como ofertas especiales (automáticamente reflejadas en la portada).
- **Preguntas Frecuentes (FAQ):** Editor dinámico para crear, ordenar y organizar las dudas más comunes de los clientes.
- **Configuración Global:** Ajuste centralizado del número de WhatsApp de soporte y ventas.

## Estructura Principal del Proyecto

El proyecto sigue la arquitectura monolítica moderna de Laravel con Inertia:

- `app/Http/Controllers/` - Lógica de backend (ej. `CatalogController`, `DashboardController`).
- `app/Models/` - Modelos Eloquent (`Brand`, `Tire`, `Faq`).
- `resources/js/Pages/` - Vistas y subcomponentes Vue para cada sección (ej. `Home/Index.vue`, `Catalog/Index.vue`).
- `resources/js/Shared/` - Componentes reutilizables globales (`Header.vue`, `Footer.vue`, etc.).
- `routes/web.php` - Definición de rutas públicas y protegidas.
- `resources/css/app.css` - Estilos globales y reglas CSS nativas adicionales a Tailwind.

## Requisitos del Sistema

- PHP >= 8.2
- Composer
- Node.js (>= 18.x) y NPM / PNPM
- Base de datos (MySQL 8+ o SQLite)

## Instalación y Configuración Local

1. **Clonar el repositorio y entrar al directorio:**
   ```bash
   git clone <url-del-repositorio>
   cd TERSAL
   ```

2. **Instalar dependencias de PHP y Node:**
   ```bash
   composer install
   npm install # o pnpm install
   ```

3. **Configurar el entorno:**
   Duplica el archivo de configuración base y ajusta las credenciales de base de datos según tu entorno:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Migrar la base de datos:**
   ```bash
   php artisan migrate
   ```

5. **Levantar los servidores de desarrollo:**
   Necesitarás dos ventanas de terminal abiertas en la raíz del proyecto.
   
   En la primera (Backend):
   ```bash
   php artisan serve
   ```
   
   En la segunda (Compilación en tiempo real del Frontend):
   ```bash
   npm run dev # o pnpm run dev
   ```

6. ¡Listo! Accede a `http://localhost:8000` en tu navegador.
