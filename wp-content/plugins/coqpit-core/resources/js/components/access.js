export default class {

  constructor() {

    this.container = document.querySelector('.displayed-menus')

    if(this.container){
      this.parents = this.container.querySelectorAll('li.parent')
      this.parents.forEach((parent) => {

        const name = parent.querySelector('.parent-settings .name')
        const parentLabel = parent.querySelector('.parent-settings label')
        const parentInput = parent.querySelector('.parent-settings input[type="checkbox"]')
        const childrenInputs = parent.querySelectorAll('ul.children input[type="checkbox"]')

        name.addEventListener('click', this.toggleChildrenWrapper.bind(this, parent, name))
        parentLabel.addEventListener('click', this.checkInputPropagation.bind(this, parentInput, childrenInputs))

        childrenInputs.forEach((input) => {
          input.addEventListener('change', this.checkParent.bind(this, parentInput, childrenInputs))
        })

      })
    }

  }

  checkParent = (parentInput, childrenInputs, event) => {
    if(!event.target.checked){
      parentInput.checked = false
    }

    const disabledInputs = []
    childrenInputs.forEach((childInput) => {
      if(childInput.checked){
        disabledInputs.push(childInput)
      }
    })

    if(disabledInputs.length === childrenInputs.length){
      parentInput.checked = true
    }
  }

  checkInputPropagation = (parentInput, children, event) => {
    children.forEach((input) => {
      if(!input.parentNode.classList.contains('is-locked')){
        input.checked = parentInput.checked
      }
    })
  }

  toggleChildrenWrapper = (parent, name, event) => {
    event.preventDefault()

    const childrenWrapper = parent.querySelector('.children-wrapper')
    const childrenList = parent.querySelector('ul.children')

    if(name.classList.contains('opened')){
      name.classList.remove('opened')
      childrenWrapper.style.height = '0px'
    } else {
      name.classList.add('opened')
      const sizes = childrenList.getBoundingClientRect()
      childrenWrapper.style.height = sizes.height + 'px'
    }

  }

}