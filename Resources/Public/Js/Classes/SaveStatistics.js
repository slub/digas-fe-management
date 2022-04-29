class SaveStatistics {

    constructor() {
        this.options = {
            downloadWorkLinkCls: 'download-document.work',
            downloadPageLinkCls: 'download-document.page',
        }
        this.documentId = document.getElementById('save-search-title');

        // initialize listener
        if (document.querySelector(`.${this.options.downloadWorkLinkCls}`)) {
            this.initializeListener();
        }

    }

    /**
     * send ajax request
     */
    sendRequestOld() {
        let that = this,
            XMLHttp,
            params = this.saveSearchTitle.name+'='+this.saveSearchTitle.value;

        XMLHttp = new XMLHttpRequest();
        XMLHttp.onreadystatechange = function(){
        }

        XMLHttp.open("POST", '/?type=20182126', true);
        XMLHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XMLHttp.send(params);
    }

    initializeListener() {
        this.downloadWorkLinks = document.querySelectorAll(`.${this.options.downloadWorkLinkCls}`);
        this.downloadPageLinks = document.querySelectorAll(`.${this.options.downloadPageLinkCls}`);

        if (this.downloadLinks !== null) {
            this.downloadLinks.forEach((downloadLink) => {
                const listEntry = downloadLink.parentNode;

                downloadLink.addEventListener('click', (event) => {
                        this.sendRequest(event, downloadLink);
                    });
            });
        }
    }

    /**
     * send ajax request
     */
     sendRequest(id, page) {
        let that = this,
            XMLHttp,
            params = 'id'+'='+id;

        XMLHttp = new XMLHttpRequest();
        XMLHttp.onreadystatechange = function(){
        }

        XMLHttp.open("POST", '/?type=20182126', true);
        XMLHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        XMLHttp.send(params);
    }


}

new SaveStatistics();
