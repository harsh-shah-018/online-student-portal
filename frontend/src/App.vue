<template>
  <div class="min-h-screen bg-gray-50 text-gray-900 font-sans">
    <header class="bg-white shadow-sm sticky top-0 z-50">
      <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
        <div class="flex items-center gap-2">
          <i class="pi pi-graduation-cap text-blue-600 text-2xl"></i>
          <h1 class="text-xl font-bold bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">EduPortal System</h1>
        </div>
        <nav v-if="$route.name !== 'login'" class="flex items-center gap-4">
          <button @click="logout" class="text-sm font-medium text-gray-600 hover:text-red-600 transition-colors">
            <i class="pi pi-sign-out mr-1"></i> Logout
          </button>
        </nav>
      </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <RouterView />
    </main>
    <Toast />
  </div>
</template>

<script setup>
import { RouterView, useRouter } from 'vue-router'
import Toast from 'primevue/toast'

const router = useRouter()

const logout = () => {
  localStorage.removeItem('user')
  localStorage.removeItem('role')
  router.push('/')
}
</script>

<style>
body {
  margin: 0;
  font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
  background-color: #f9fafb;
}
</style>
