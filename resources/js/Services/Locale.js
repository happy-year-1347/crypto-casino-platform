// Languages of the player site. Keep in sync with app/Support/Locales.php and lang/*.json
export const LANGUAGES = [
    { code: 'en',    label: 'English',   flag: '/assets/images/lang/united-states-of-america.svg' },
    { code: 'pt_BR', label: 'Português', flag: '/assets/images/lang/brasil.svg' },
    { code: 'es',    label: 'Español',   flag: '/assets/images/lang/spain.svg' },
    { code: 'fr',    label: 'Français',  flag: '/assets/images/lang/france.svg' },
    { code: 'de',    label: 'Deutsch',   flag: '/assets/images/lang/germany.svg' },
];

export const FALLBACK = 'en';
const STORAGE_KEY = 'lang';

// pt-BR, pt, es-MX, en_US ... -> supported code or null
export function normalize(code) {
    if (!code) return null;
    const value = String(code).trim().replace('-', '_');
    const exact = LANGUAGES.find(l => l.code.toLowerCase() === value.toLowerCase());
    if (exact) return exact.code;

    const base = value.split('_')[0].toLowerCase();
    if (base === 'pt') return 'pt_BR';
    return LANGUAGES.some(l => l.code === base) ? base : null;
}

// the language the player picked on this device
export function storedLanguage() {
    try { return normalize(localStorage.getItem(STORAGE_KEY)); } catch (e) { return null; }
}

export function storeLanguage(code) {
    try { localStorage.setItem(STORAGE_KEY, code); } catch (e) {}
}

function siteDefault() {
    try {
        const setting = JSON.parse(localStorage.getItem('setting'));
        return normalize(setting && setting.default_language);
    } catch (e) {
        return null;
    }
}

function browserLanguage() {
    const list = navigator.languages && navigator.languages.length ? navigator.languages : [navigator.language];
    for (const code of list) {
        const found = normalize(code);
        if (found) return found;
    }
    return null;
}

// first paint: player's choice > browser language > admin default > English
export function initialLanguage() {
    return storedLanguage() || browserLanguage() || siteDefault() || FALLBACK;
}

// <html lang="..."> and the value sent to the API
let current = null;

export function setCurrentLanguage(code) {
    current = normalize(code) || FALLBACK;
    document.documentElement.setAttribute('lang', current.replace('_', '-'));
}

export function currentLanguage() {
    return current || initialLanguage();
}
