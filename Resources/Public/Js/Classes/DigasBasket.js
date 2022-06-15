class DigasBasket {
    constructor() {
        this.options = {
            basketLinkCls: 'tx-dlf-request',
            basketLinkActiveCls: 'tx-dlf-request--restricted',
            basketIdExistsCls: 'tx-dlf-request--in-basket',
            basketEntryCls: 'fe-management-basket-entry',
            basketRemoveLinkCls: 'fe-management-basket-remove-link',
            basketOverviewCls: 'fe-management-basket',
            basketMapCls: 'digas-view-map', /* geosearch-frontend */
            basketListingCls: 'tx-dlf-listview',
            listIdentifierCls: 'dd.tx-dlf-metadata-record_id, span.tx-dlf-metadata-record_id', /* <dd> in Kitodo, <span> in geosearch-frontend */
            listRestrictionCLs: 'dd.tx-dlf-metadata-restrictions, span.tx-dlf-metadata-restrictions',
            basketLinkCountCls: 'basket-link-counter',
            restrictionContext: {
                'de': 'ja',
                'de-DE': 'ja',
                'en': 'yes',
                'en-GB': 'yes',
                'en-US': 'yes'
            },
            cookieName: 'dlf-requests'
        }
        this.basketIds = this.getBasketIdsFromCookie();
        this.htmlLang = document.documentElement.lang;
        this.restrictionString = null;
        this.basketEntriesCount = 0;

        // initialize basket link count
        this.basketLinkCounter = document.querySelector(`.${this.options.basketLinkCountCls}`);
        this.updateBasketCount(this.basketIds.length);

        // initialize basket overview
        if (document.querySelector(`.${this.options.basketOverviewCls}`)) {
            this.initializeBasketOverview();
        }

        // initialize listing view / map view
        if (document.querySelector(`.${this.options.basketListingCls}`) ||
            document.querySelector(`.${this.options.basketMapCls}`)) {
            this.getRestrictionString();
            this.initializeListing();
        }
    }

    updateBasketCount(basketCount) {
        if (basketCount > 0) {
            this.basketLinkCounter.textContent = '(' + basketCount.toString() + ')';
        } else {
            this.basketLinkCounter.textContent = '';
        }
    }

    initializeBasketOverview() {
        this.basketEntries = document.querySelectorAll(`.${this.options.basketEntryCls}`);

        if (this.basketEntries !== null) {
            this.basketEntriesCount = this.basketEntries.length;
            this.basketEntries.forEach((basketEntry) => {
                const basketRemoveLink = basketEntry.querySelector(`.${this.options.basketRemoveLinkCls}`);

                if (typeof basketRemoveLink !== 'undefined') {
                    if (typeof basketRemoveLink.dataset.id !== 'undefined') {
                        basketRemoveLink.addEventListener('click', (event) => {
                            this.onBasketLinkRemoveClick(event, basketRemoveLink, basketEntry);
                        });
                    }
                }
            });
        }
    }

    initializeListing() {
        this.basketLinks = document.querySelectorAll(`.${this.options.basketLinkCls}`);
        if (this.basketLinks !== null) {
            this.basketLinks.forEach((basketLink) => {
                const listEntry = basketLink.parentNode.parentNode;

                const entryIdentifier = listEntry.querySelector(`${this.options.listIdentifierCls}`);
                const entryRestriction = listEntry.querySelector(`${this.options.listRestrictionCLs}`);

                if (entryRestriction !== null) {
                    if (entryRestriction.textContent.trim() !== this.restrictionString) {
                        return;
                    }
                }
                basketLink.classList.add(this.options.basketLinkActiveCls);

                if (entryIdentifier !== null && typeof entryIdentifier.dataset.id !== 'undefined') {
                    if (this.basketIds.includes(entryIdentifier.dataset.id)) {
                        basketLink.classList.add(this.options.basketIdExistsCls);
                    }
                    basketLink.addEventListener('click', (event) => {
                        this.onBasketLinkClick(event, basketLink, entryIdentifier.dataset.id);
                    });
                }
            });
        }
    }

    getRestrictionString() {
        if (typeof this.options.restrictionContext[this.htmlLang] !== 'undefined') {
            this.restrictionString = this.options.restrictionContext[this.htmlLang];
        }
    }

    getBasketIdsFromCookie() {
        const basketCookie = Cookies.get(this.options.cookieName);

        if (typeof basketCookie === 'undefined') {
            return [];
        }
        return JSON.parse(basketCookie);
    }

    updateBasketCookie(dlfId) {
        let basketIds = this.getBasketIdsFromCookie();

        if (basketIds.includes(dlfId)) {
            basketIds = basketIds.filter(id => id !== dlfId);
        } else {
            basketIds = [...basketIds, dlfId];
        }

        Cookies.set(this.options.cookieName, JSON.stringify(basketIds));
        this.updateBasketCount(basketIds.length);
    }

    onBasketLinkClick(event, basketLink, identifier) {
        event.preventDefault();
        event.stopPropagation();

        if (typeof identifier !== 'undefined') {
            this.updateBasketCookie(identifier);
            basketLink.classList.toggle(this.options.basketIdExistsCls);
        }
    }

    onBasketLinkRemoveClick(event, basketRemoveLink, basketEntry) {
        event.preventDefault();
        event.stopPropagation();

        // @todo translations
        if (confirm('Wollen Sie diesen Datensatz wirklich aus dem Warenkorb entfernen?')) {
            this.updateBasketCookie(basketRemoveLink.dataset.id);
            basketEntry.remove();
            this.basketEntriesCount--;

            // reload page if last document was deleted to remove request form
            if (this.basketEntriesCount < 1) {
                location.reload();
            }
        }
    }
}

new DigasBasket();
