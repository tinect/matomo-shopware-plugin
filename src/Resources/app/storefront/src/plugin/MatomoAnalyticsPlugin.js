export default class MatomoAnalyticsPlugin extends window.PluginBaseClass {
    init() {
        this.cookieEnabledName = 'matomo-analytics-enabled';
        window.matomoCookieActive = Boolean(
            this.getCookieItem(this.cookieEnabledName)
        );

        document.$emitter.subscribe(
            'CookieConfiguration_Update',
            this.handleCookies.bind(this)
        );

        window.mTrackCall();
    }

    handleCookies(cookieUpdateEvent) {
        const updatedCookies = cookieUpdateEvent.detail;

        if (
            !Object.prototype.hasOwnProperty.call(
                updatedCookies,
                this.cookieEnabledName
            )
        ) {
            return;
        }

        if (updatedCookies[this.cookieEnabledName]) {
            return;
        }

        this.removeCookies();
    }

    removeCookies() {
        const allCookies = document.cookie.split(';');
        const gaCookieRegex = /^(_pk)/;

        allCookies.forEach((cookie) => {
            const cookieName = cookie.split('=')[0].trim();
            if (!cookieName.match(gaCookieRegex)) {
                return;
            }

            document.cookie = `${cookieName}= ; expires = Thu, 01 Jan 1970 00:00:00 GMT;path=/`;
        });
    }

    getCookieItem(key) {
        if (!key) {
            return false;
        }

        const name = key + '=';
        const allCookies = document.cookie.split(';');

        for (let i = 0; i < allCookies.length; i++) {
            let singleCookie = allCookies[i];

            while (singleCookie.charAt(0) === ' ') {
                singleCookie = singleCookie.substring(1);
            }

            if (singleCookie.indexOf(name) === 0) {
                return singleCookie.substring(name.length, singleCookie.length);
            }
        }

        return false;
    }
}
