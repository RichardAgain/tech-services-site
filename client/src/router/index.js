import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Register from '@/views/auth/RegisterView.vue'
import { authGuard } from './guards'
import TechniciansView from '../views/TechniciansView.vue'
import ServicesView from '../views/ServicesView.vue'
import LogInView from '@/views/auth/LogInView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
    },
    {
      path: '/login',
      name: 'login',
      component: LogInView
    },
    {
      path: '/register',
      name: 'register',
      component: RegisterView
    },
    {
      path: '/technicians',
      name: 'technicians',
      component: TechniciansView,
      beforeEnter: authGuard
    },
    {
      path: '/services',
      name: 'services',
      component: ServicesView,
      beforeEnter: authGuard
    }
  ]
})

export default router
