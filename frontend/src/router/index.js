import { createRouter, createWebHistory } from 'vue-router'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'login',
      component: () => import('../views/LoginView.vue')
    },
    {
      path: '/admin',
      name: 'admin-dashboard',
      component: () => import('../views/admin/AdminDashboard.vue')
    },
    {
      path: '/admin/students',
      name: 'admin-students',
      component: () => import('../views/admin/AdminStudents.vue')
    },
    {
      path: '/admin/courses',
      name: 'admin-courses',
      component: () => import('../views/admin/AdminCourses.vue')
    },
    {
      path: '/admin/enrollments',
      name: 'admin-enrollments',
      component: () => import('../views/admin/AdminEnrollments.vue')
    },
    {
      path: '/admin/notices',
      name: 'admin-notices',
      component: () => import('../views/admin/AdminNotices.vue')
    },
    {
      path: '/admin/library',
      name: 'admin-library',
      component: () => import('../views/admin/AdminLibrary.vue')
    },
    {
      path: '/admin/academics',
      name: 'admin-academics',
      component: () => import('../views/admin/AdminAcademics.vue')
    },
    {
      path: '/student',
      name: 'student-dashboard',
      component: () => import('../views/student/StudentDashboard.vue')
    },
    {
      path: '/student/enroll',
      name: 'student-enroll',
      component: () => import('../views/student/StudentEnrollment.vue')
    },
    {
      path: '/student/fees',
      name: 'student-fees',
      component: () => import('../views/student/StudentFees.vue')
    },
    {
      path: '/student/academic',
      name: 'student-academic',
      component: () => import('../views/student/StudentAcademic.vue')
    },
    {
      path: '/student/library',
      name: 'student-library',
      component: () => import('../views/student/StudentLibrary.vue')
    }
  ]
})

router.beforeEach((to, from, next) => {
  const user = localStorage.getItem('user')
  const role = localStorage.getItem('role')

  if (to.name !== 'login' && !user) {
    next({ name: 'login' })
  } else if (to.name === 'login' && user) {
    if (role === 'admin') {
      next({ name: 'admin-dashboard' })
    } else {
      next({ name: 'student-dashboard' })
    }
  } else if (to.path.startsWith('/admin') && role !== 'admin') {
    next({ name: 'student-dashboard' })
  } else if (to.path.startsWith('/student') && role !== 'student') {
    next({ name: 'admin-dashboard' })
  } else {
    next()
  }
})

export default router
