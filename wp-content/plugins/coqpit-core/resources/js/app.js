// import { createApp } from 'vue'
// import { createPinia } from 'pinia'
import { documentReady } from './utils'
import UserAccess from './components/access'
import Emails from './components/emails'

/*
import App from '../vue/App.vue'
import router from '../vue/router'

if(document.getElementById('coqpit-core-settings')){

  const pinia = createPinia()
  const app = createApp(App)

  app.use(pinia)
  app.use(router)
  app.mount('#coqpit-core-settings')

}*/

documentReady(() => {
  new Emails()
  new UserAccess()
})
