export default class {

  constructor() {

    this.selector = document.getElementById('coqpit-emails-smtp-secure-field')
    this.port = document.getElementById('coqpit-emails-smtp-port-field')

    if(this.selector){
      this.selector.addEventListener('change', this.updatePort.bind(this))
    }

  }

  updatePort = (e) => {
    if(e.target.value === 'ssl'){
      this.port.value = 465
    } else if(e.target.value === 'tls'){
      this.port.value = 587
    } else {
      this.port.value = 25
    }
  }

}