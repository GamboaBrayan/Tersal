import { ref, reactive } from 'vue';
import { usePage } from '@inertiajs/vue3';

const STORAGE_KEY = 'cookie_consent';
const EXPIRATION_DAYS = 365; // 12 months

// Global state
const showBanner = ref(false);
const preferences = reactive({
    necessary: true,
    analytics: false,
    marketing: false,
});

export function useCookieConsent() {
    const checkConsent = () => {
        const stored = localStorage.getItem(STORAGE_KEY);
        if (!stored) {
            showBanner.value = true;
            return;
        }

        try {
            const data = JSON.parse(stored);
            const now = Date.now();
            const expirationTime = EXPIRATION_DAYS * 24 * 60 * 60 * 1000;

            if (now - data.timestamp > expirationTime) {
                // Expired
                showBanner.value = true;
            } else {
                // Valid, load preferences
                preferences.necessary = data.necessary;
                preferences.analytics = data.analytics;
                preferences.marketing = data.marketing;
                
                loadScripts();
            }
        } catch (e) {
            showBanner.value = true;
        }
    };

    const saveConsent = (prefs) => {
        const data = {
            ...prefs,
            necessary: true, // Always true
            timestamp: Date.now(),
        };

        localStorage.setItem(STORAGE_KEY, JSON.stringify(data));
        
        // Update reactive state
        preferences.necessary = data.necessary;
        preferences.analytics = data.analytics;
        preferences.marketing = data.marketing;
        
        showBanner.value = false;
        loadScripts();
    };

    const acceptAll = () => {
        saveConsent({
            necessary: true,
            analytics: true,
            marketing: true,
        });
    };

    const rejectNonEssential = () => {
        saveConsent({
            necessary: true,
            analytics: false,
            marketing: false,
        });
    };

    const loadScripts = () => {
        if (preferences.analytics) {
            const page = usePage();
            const gaId = page.props.ga_id;
            
            if (gaId && !window[`ga_loaded_${gaId}`]) {
                const script = document.createElement('script');
                script.src = `https://www.googletagmanager.com/gtag/js?id=${gaId}`;
                script.async = true;
                document.head.appendChild(script);

                const inlineScript = document.createElement('script');
                inlineScript.text = `
                    window.dataLayer = window.dataLayer || [];
                    function gtag(){dataLayer.push(arguments);}
                    gtag('js', new Date());
                    gtag('config', '${gaId}');
                `;
                document.head.appendChild(inlineScript);
                
                window[`ga_loaded_${gaId}`] = true;
            }
        }
        
        if (preferences.marketing) {
            // e.g. initialize Facebook Pixel
            // console.log('Marketing scripts loaded');
        }
    };

    return {
        showBanner,
        preferences,
        checkConsent,
        saveConsent,
        acceptAll,
        rejectNonEssential,
    };
}
