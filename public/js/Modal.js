class Modal {

    constructor(modalId, openButtonIds, closeButtonIds) {

        this.modal = document.getElementById(modalId)
        if (this.modal === null)
            return

        const modalWindow = this.modal.children[0]
        if (modalWindow.querySelector(`#close-button-${modalId}`) === null) {
            const closeButton = document.createElement('button')
            closeButton.innerHTML = '×'
            closeButton.setAttribute('id', `close-button-${modalId}`)
            closeButton.setAttribute('class', 'close-button')
            modalWindow.appendChild(closeButton)
        }

        this.openButtons = []
        openButtonIds.forEach(openButtonId => {
            const openButton = document.getElementById(openButtonId)
            openButton.addEventListener('click', this.openModal.bind(this))
            this.openButtons.push(openButton)
        })

        this.closeButtons = []
        closeButtonIds.push(`close-button-${modalId}`)
        closeButtonIds.forEach(closeButtonId => {
            const closeButton = document.getElementById(closeButtonId)
            closeButton.addEventListener('click', this.closeModal.bind(this))
            this.closeButtons.push(closeButton)
        })

    }

    openModal(event) {
        event.preventDefault()
        this.modal.style.display = 'flex'
        this.modal.querySelectorAll('form').forEach(form => {
            form.reset()
        })
    }

    closeModal(event) {
        event.preventDefault()
        setTimeout(() => {this.modal.style.display = 'none'}, 100)
    }

}
