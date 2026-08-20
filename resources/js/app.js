import './bootstrap';
import { createApp } from 'vue';
import 'element-plus/dist/index.css';

// Nur die Element-Plus-Komponenten, die diese Anwendung wirklich benutzt, und
// zwar aus ihren eigenen Modulen. Namensimporte aus dem Paket-Root werden NICHT
// tree-geshaked - der Barrel zieht jede Komponente mit und das Bundle bleibt bei
// rund 920 kB. Die Liste muss die Blade-Templates mit abdecken, weil kein
// Bundler-Plugin die durchsucht; ein fehlender Eintrag zeigt sich als Vue-
// Warnung "failed to resolve component" und das Element bleibt roh im DOM.
import { ElAlert } from 'element-plus/es/components/alert/index';
import { ElAutocomplete } from 'element-plus/es/components/autocomplete/index';
import { ElButton } from 'element-plus/es/components/button/index';
import { ElCard } from 'element-plus/es/components/card/index';
import { ElCheckbox } from 'element-plus/es/components/checkbox/index';
import { ElDatePicker } from 'element-plus/es/components/date-picker/index';
import { ElDialog } from 'element-plus/es/components/dialog/index';
import { ElDropdown, ElDropdownItem, ElDropdownMenu } from 'element-plus/es/components/dropdown/index';
import { ElIcon } from 'element-plus/es/components/icon/index';
import { ElInput } from 'element-plus/es/components/input/index';
import { ElLink } from 'element-plus/es/components/link/index';
import { ElLoadingDirective } from 'element-plus/es/components/loading/index';
import { ElMenu, ElMenuItem, ElSubMenu } from 'element-plus/es/components/menu/index';
import { ElPagination } from 'element-plus/es/components/pagination/index';
import { ElOption, ElSelect } from 'element-plus/es/components/select/index';
import { ElSwitch } from 'element-plus/es/components/switch/index';
import { ElTable, ElTableColumn } from 'element-plus/es/components/table/index';
import { ElTimePicker } from 'element-plus/es/components/time-picker/index';
import { ElTooltip } from 'element-plus/es/components/tooltip/index';

// Ebenso nur die tatsaechlich verwendeten Icons statt der rund tausend, die
// `import * as ElementPlusIconsVue` mitbringen wuerde.
import {
    Avatar,
    CircleCheckFilled,
    Delete,
    DocumentCopy,
    EditPen,
    House,
    Medal,
    Notebook,
    Plus,
    Star,
    Sunny,
    Tools,
    Trophy,
    Upload,
    User,
    UserFilled,
} from '@element-plus/icons-vue';

const elementComponents = [
    ElAlert, ElAutocomplete, ElButton, ElCard, ElCheckbox, ElDatePicker,
    ElDialog, ElDropdown, ElDropdownItem, ElDropdownMenu, ElIcon, ElInput,
    ElLink, ElMenu, ElMenuItem, ElOption, ElPagination, ElSelect, ElSubMenu,
    ElSwitch, ElTable, ElTableColumn, ElTimePicker, ElTooltip,
];

const icons = {
    Avatar, CircleCheckFilled, Delete, DocumentCopy, EditPen, House, Medal,
    Notebook, Plus, Star, Sunny, Tools, Trophy, Upload, User, UserFilled,
};

const app = createApp({
    data() {
        return {
            mobileMenuOpen: false,
            // Das Icon des Theme-Umschalters wird im Blade als :icon gebunden.
            Sunny,
        };
    },
});

elementComponents.forEach((component) => app.component(component.name, component));
for (const [name, component] of Object.entries(icons)) {
    app.component(name, component);
}

app.directive('loading', ElLoadingDirective);

// Alle Vue-Komponenten aus dem components-Verzeichnis automatisch registrieren
const modules = import.meta.glob('./components/*.vue', { eager: true });
for (const path in modules) {
    const mod = modules[path];
    const name = path.split('/').pop().replace('.vue', '');
    app.component(name, mod.default || mod);
}

app.mount('#app');
