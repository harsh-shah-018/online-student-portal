import { ref, computed } from 'vue'
import { defineStore } from 'pinia'
import { useRouter } from 'vue-router'

export const useCounterStore = defineStore('counter', () => {
  const router = useRouter()
  
  const user = ref(JSON.parse(localStorage.getItem('user')) || null)
  const role = ref(localStorage.getItem('role') || null)

  const isAuthenticated = computed(() => !!user.value)

  function login(userData, userRole) {
    user.value = userData
    role.value = userRole
    localStorage.setItem('user', JSON.stringify(userData))
    localStorage.setItem('role', userRole)
  }

  function logout() {
    user.value = null
    role.value = null
    localStorage.removeItem('user')
    localStorage.removeItem('role')
    router.push('/')
  }

  return { user, role, isAuthenticated, login, logout }
})
