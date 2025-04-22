import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import LogIn from '@/views/auth/LogInView.vue'
import Register from '@/views/auth/RegisterView.vue'
import { authGuard } from './guards'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    // {
    //   path: '/pay',
    //   name: 'pay',
    //   component: PaymentView,
    //   beforeEnter: authGuard
    // },
    {
      path: '/login',
      name: 'login',
      component: LogIn
    },
    {
      path: '/register',
      name: 'register',
      component: Register
    }
  ]
})

export default router
