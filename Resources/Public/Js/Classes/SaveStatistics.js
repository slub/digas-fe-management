class SaveStatistics {

    constructor() {
        this.options = {
            downloadWorkLinkCls: 'download-document.work',
            downloadPageLinkCls: 'download-document.page',
        }
        this.documentId = document.querySelector('.dlf-identifier').dataset.id;

        // initialize listener
        if (document.querySelector(`.${this.options.downloadWorkLinkCls}`)) {
            this.initializeListener();
        }

    }

    /**
     * send ajax request
     */
    sendRequest() {
        let that = this,
            XMLHttp,
            params = 'id'+'='+this.documentId;

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

        if (this.downloadWorkLinks !== null) {
            this.downloadWorkLinks.forEach((downloadLink) => {
                downloadLink.addEventListener('click', (event) => {
                        this.sendRequest(event, downloadLink);
                    });
            });
        }
    }

}

new SaveStatistics();
