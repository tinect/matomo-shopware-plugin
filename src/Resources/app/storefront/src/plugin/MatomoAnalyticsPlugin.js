import Plugin from "src/plugin-system/plugin.class";
import CookieStorageHelper from "src/helper/storage/cookie-storage.helper";
import {COOKIE_CONFIGURATION_UPDATE} from "src/plugin/cookie/cookie-configuration.plugin";

export default class MatomoAnalyticsPlugin extends Plugin {
    init() {
        this.cookieEnabledName = 'matomo-analytics-enabled';
        window.matomoCookieActive = Boolean(CookieStorageHelper.getItem(this.cookieEnabledName));

        this.startMatomoAnalytics();
    }

    startMatomoAnalytics() {
        window.mTrackCall();
    }

    handleCookieChangeEvent() {
        document.$emitter.subscribe(COOKIE_CONFIGURATION_UPDATE, this.handleCookies.bind(this));
    }

    handleCookies(cookieUpdateEvent) {
        const updatedCookies = cookieUpdateEvent.detail;

        if (!Object.prototype.hasOwnProperty.call(updatedCookies, this.cookieEnabledName)) {
            return;
        }

        if (updatedCookies[this.cookieEnabledName]) {
            return;
        }

        this.removeCookies();
    }

    removeCookies() {
        if (!CookieStorageHelper.isSupported()) {
            return;
        }

        const allCookies = document.cookie.split(';');
        const gaCookieRegex = /^(_pk)/;

        allCookies.forEach(cookie => {
            const cookieName = cookie.split('=')[0].trim();
            if (!cookieName.match(gaCookieRegex)) {
                return;
            }

            CookieStorageHelper.removeItem(cookieName);
        });
    }
}
