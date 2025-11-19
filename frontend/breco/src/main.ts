import { createApp } from 'vue'
import { createPinia } from 'pinia'

import App from '@/presentation/app/App.vue'
import router from '@/presentation/router'
import '@/presentation/shared/styles/main.css'

const app = createApp(App)

app.use(createPinia())
app.use(router)

app.mount('#app')
