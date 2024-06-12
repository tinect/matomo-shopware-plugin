window.PluginManager.register('MatomoAnalytics', () => import('./plugin/MatomoAnalyticsPlugin'));

window.PluginManager.override('SearchWidget', () => import('./plugin/override/header/SearchWidgetPluginExtension'), '[data-search-widget]');
