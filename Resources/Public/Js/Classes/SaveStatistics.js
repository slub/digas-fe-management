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
    sendRequest(countType = 'work') {

        let that = this,
            XMLHttp,
            params = 'tx_digasfemanagement_statistic[id]'+'='+this.documentId
                    +'&tx_digasfemanagement_statistic[countType]'+'='+countType;

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

        if (this.documentId !== null) {
            this.sendRequest('workview');
        }

        if (this.downloadWorkLinks !== null) {
            this.downloadWorkLinks.forEach((downloadLink) => {
                downloadLink.addEventListener('click', (event) => {
                        this.sendRequest('work');
                });
            });
        }

        if (this.downloadPageLinks !== null) {
            this.downloadPageLinks.forEach((downloadLink) => {
                downloadLink.addEventListener('click', (event) => {
                        this.sendRequest('page');
                });
            });
        }
    }

}

new SaveStatistics();
