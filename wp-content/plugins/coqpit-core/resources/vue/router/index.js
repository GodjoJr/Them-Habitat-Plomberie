import { createRouter, createWebHashHistory } from 'vue-router'
import Dashboard from '../pages/Dashboard.vue'
import Templates from '../pages/Templates.vue'
import Plugins from '../pages/Plugins.vue'
import Options from '../pages/Options.vue'

const router = createRouter({
  history: createWebHashHistory(),
  routes: [
    {
      path: '/',
      name: 'settings',
      component: Options
    },
    {
      path: '/templates',
      name: 'templates',
      component: Templates
    },
    {
      path: '/plugins',
      name: 'plugins',
      component: Plugins
    }
  ]
})

export default router
