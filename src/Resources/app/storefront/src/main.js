import MatomoAnalyticsPlugin from "./plugin/MatomoAnalyticsPlugin";

// Necessary for the webpack hot module reloading server
if (module.hot) {
    module.hot.accept();
}

window.PluginManager.register('MatomoAnalytics', MatomoAnalyticsPlugin);

