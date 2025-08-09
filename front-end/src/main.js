import './assets/main.css'
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import { library } from '@fortawesome/fontawesome-svg-core'
import {
  faBars,
  faBell,
  faCalendarAlt,
  faEnvelope,
  faHome,
  faLock,
  faMapMarkedAlt,
  faPlus,
  faUser,
} from '@fortawesome/free-solid-svg-icons'
import router from './router'
import App from './App.vue'
import 'leaflet/dist/leaflet.css';



import { createApp } from 'vue'

library.add(
  faEnvelope,
  faLock,
  faUser,
  faBars,
  faBell,
  faHome,
  faCalendarAlt,
  faBars,
  faPlus,
  faMapMarkedAlt,
)

const app = createApp(App)

app.component('font-awesome-icon', FontAwesomeIcon)
app.use(router)

app.mount('#app')
