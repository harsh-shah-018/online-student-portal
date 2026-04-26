<template>
  <div class="max-w-md mx-auto mt-16 bg-white p-8 rounded-xl shadow-lg border border-gray-100 backdrop-blur-sm bg-white/90">
    <div class="text-center mb-8">
      <h2 class="text-3xl font-extrabold text-gray-900 mb-2">Welcome Back</h2>
      <p class="text-gray-500">Sign in to your EduPortal account</p>
    </div>

    <form @submit.prevent="handleLogin" class="space-y-6">
      <div class="space-y-4">
        <BaseInput 
          v-model="form.email" 
          label="Email Address" 
          type="email" 
          placeholder="Enter your email" 
          required 
        />
        <BaseInput 
          v-model="form.password" 
          label="Password" 
          type="password" 
          placeholder="••••••••" 
          required 
        />
      </div>

      <div class="flex items-center gap-4 mt-4">
        <label class="flex items-center text-sm text-gray-700">
          <input type="radio" v-model="form.role" value="student" class="mr-2 text-blue-600 focus:ring-blue-500"> Student
        </label>
        <label class="flex items-center text-sm text-gray-700">
          <input type="radio" v-model="form.role" value="admin" class="mr-2 text-blue-600 focus:ring-blue-500"> Administrator
        </label>
      </div>

      <div v-if="error" class="p-3 bg-red-50 text-red-600 text-sm rounded-md border border-red-100">
        <i class="pi pi-exclamation-circle mr-1"></i> {{ error }}
      </div>

      <BaseButton 
        type="submit" 
        :label="loading ? 'Signing in...' : 'Sign In'" 
        icon="pi pi-sign-in" 
        class="w-full justify-center mt-6" 
        :disabled="loading"
      />
    </form>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import BaseInput from '@/components/base/BaseInput.vue'
import BaseButton from '@/components/base/BaseButton.vue'
import API_BASE_URL from '@/config/api'

const router = useRouter()
const loading = ref(false)
const error = ref('')

const form = reactive({
  email: '',
  password: '',
  role: 'student'
})

const handleLogin = async () => {
  loading.value = true
  error.value = ''
  try {
    const response = await fetch(`${API_BASE_URL}/api/auth/login.php`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(form)
    })
    
    const data = await response.json()
    
    if (response.ok) {
      localStorage.setItem('user', JSON.stringify(data.user))
      localStorage.setItem('role', data.role)
      
      if (data.role === 'admin') {
        router.push('/admin')
      } else {
        router.push('/student')
      }
    } else {
      error.value = data.message || 'Login failed'
    }
  } catch (err) {
    error.value = err || err.message;
    console.error(err)
  } finally {
    loading.value = false
  }
}
</script>
