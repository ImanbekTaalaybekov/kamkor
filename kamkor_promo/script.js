const modal = document.getElementById('installModal')
const openButton = document.getElementById('guideButton')
const closeButton = document.getElementById('closeModal')

const openModal = () => {
  modal.classList.add('active')
  modal.setAttribute('aria-hidden', 'false')
}

const closeModal = () => {
  modal.classList.remove('active')
  modal.setAttribute('aria-hidden', 'true')
}

openButton?.addEventListener('click', openModal)
closeButton?.addEventListener('click', closeModal)
modal?.addEventListener('click', event => {
  if (event.target === modal) closeModal()
})

document.addEventListener('keydown', event => {
  if (event.key === 'Escape') closeModal()
})
