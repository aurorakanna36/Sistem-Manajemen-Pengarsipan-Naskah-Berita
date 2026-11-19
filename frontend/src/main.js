import { createApp } from "vue";
import App from "./App.vue";
import router from "./router/index.js";
import "./assets/main.css";
import "./assets/admin-theme.css";
import "./assets/kontributor-theme.css";
import './assets/css/animations.css'

createApp(App).use(router).mount("#app");
