import './bootstrap';
import { createApp } from 'vue';
import ElementPlus from 'element-plus';
import 'element-plus/dist/index.css';
import * as ElementPlusIconsVue from '@element-plus/icons-vue';
import en from 'element-plus/dist/locale/en.mjs';

const app = createApp({
    data() {
        return {
            mobileMenuOpen: false,
            Sunny: ElementPlusIconsVue.Sunny,
        };
    },
});

// Alle Element Plus Icons global registrieren
for (const [key, component] of Object.entries(ElementPlusIconsVue)) {
    app.component(key, component);
}

app.use(ElementPlus, { locale: en });

// Alle Vue-Komponenten aus dem components-Verzeichnis automatisch registrieren
const modules = import.meta.glob('./components/*.vue', { eager: true });
for (const path in modules) {
    const mod = modules[path];
    const name = path.split('/').pop().replace('.vue', '');
    app.component(name, mod.default || mod);
}

app.mount('#app');
