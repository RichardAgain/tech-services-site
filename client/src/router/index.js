import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import { authGuard } from './guards'
import TechniciansView from '../views/technicians/TechniciansView.vue'

import LogInView from '@/views/auth/LogInView.vue'
import RegisterView from '@/views/auth/RegisterView.vue'
import TasksView from '@/views/tasks/TasksView.vue'
import ApplicationsViews from '@/views/applications/ApplicationsViews.vue'
import TechnicianView from '@/views/technicians/TechnicianView.vue'
import ApplicateView from '@/views/applicate/ApplicateView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView,
      beforeEnter: authGuard
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
      path: '/tecnicos',
      name: 'technicians',
      component: TechniciansView,
      beforeEnter: authGuard
    },
    {
      path: '/tecnicos/:id',
      name: 'technician',
      component: TechnicianView,
      beforeEnter: authGuard,
    },
    {
      path: '/tareas',
      name: 'tasks',
      component: TasksView,
      beforeEnter: authGuard
    },
    {
      path: '/solicitudes',
      name: 'applications',
      component: ApplicationsViews,
      beforeEnter: authGuard
    },
    {
      path: '/aplicar',
      name: 'apply',
      component: ApplicateView,
      beforeEnter: authGuard
    }
  ]
})

export default router
