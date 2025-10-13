import SearchWidgetPlugin from 'src/plugin/header/search-widget.plugin';

export default class SearchWidgetPluginExtension extends SearchWidgetPlugin {
    init() {
        super.init();

        this.$emitter.subscribe('afterSuggest', () => {
            let term = null;
            let count = false;

            const dataField = document.getElementsByClassName(
                'matomo-search-suggest-helper'
            )[0];
            if (dataField) {
                const value = JSON.parse(dataField.dataset['value']);
                term = value['term'];
                count = value['count'];
            }

            window._paq = window._paq || [];
            window._paq.push(['trackSiteSearch', term, 'suggestSearch', count]);
        });
    }
}
