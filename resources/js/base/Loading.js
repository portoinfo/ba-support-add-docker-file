class Loading {
  constructor() {
    var loader = document.getElementById('loader');
    if(!loader) {
        document.getElementsByTagName('body')[0].insertAdjacentHTML('afterbegin', '<div class="modal-loading-overlay"><div class="loader-spinna"></div></div>');
    }
    this.body = document.getElementsByTagName('body')[0];
  }

  hide() {
    this.body.classList.remove('loading');
  }

  show() {
    this.body.classList.add('loading');
  }

  disabledPaceJs(show = false) {
    if(show)
      this.body.classList.add('pace-force-disabled');
    else
      this.body.classList.remove('pace-force-disabled');
  }
}
export default Loading;
