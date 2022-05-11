class SaveSearch {

  constructor(saveSearchForm) {
    this.saveForm = saveSearchForm;
    this.saveSearchContainer = document.getElementById('save-search-container');
    this.saveSearchTitle = document.getElementById('save-search-title');
    this.formSubmit();
  }

  /**
   * prevent form submit, send request via ajax
   */
  formSubmit() {
    let that = this;
    this.saveForm.addEventListener('submit',function(e){
      e.preventDefault();
      if ($('.tx-dlf-search-form').length) {
        that.sendRequest();
      }
      else {
        that.saveSearchContainer.innerHTML = 'Configuration error.'
      }
    });
  }

  /**
   * send ajax request
   */
  sendRequest() {
    let that = this,
        XMLHttp,
        params = this.saveSearchTitle.name+'='+this.saveSearchTitle.value;


    //read search params from search form
    $.each($('.tx-dlf-search-form').serializeArray(), function(index, value) {
      if (value.name != 'tx_dlf[hashed]' && value.name != 'tx_dlf[encrypted]') {
        params+= '&'+value.name+'='+value.value;
      }
    });

    XMLHttp = new XMLHttpRequest();
    XMLHttp.onreadystatechange = function(){
      if (XMLHttp.readyState == 4 && XMLHttp.status == 200){
        that.saveSearchContainer.innerHTML = XMLHttp.responseText;
      }
    }

    XMLHttp.open("POST", '/?type=199988', true);
    XMLHttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    XMLHttp.send(params);
  }
}

let saveSearchForm = document.getElementById('save-search-form');
if (typeof(saveSearchForm) != 'undefined' && saveSearchForm != null){
  new SaveSearch(saveSearchForm);
}
