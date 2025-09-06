document.getElementById('searchBox').addEventListener('input', function(e){
  e.preventDefault()

  search = document.getElementById('search').value

  if(search.trim() === ''){
    return
  }

  fetch(`Search.php?search=${encodeURIComponent(search)}`, {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    }
  })
  .then(response => response.json())
  .then(data => {
    if(!data.error){
      // result boxes
      const firstNameBox = document.getElementById('first-name-box').textContent = ''
      const lastNameBox = document.getElementById('last-name-box').textContent = ''
      const emailBox = document.getElementById('email-box').textContent = ''
      const phoneBox = document.getElementById('phone-box').textContent = ''

      data.results.forEach(contact => {
        // create result divs
        const firstNameDiv = document.createElement('div')
        const lastNameDiv = document.createElement('div')
        const emailDiv = document.createElement('div')
        const phoneDiv = document.createElement('div')

        // add corresponding info
        firstNameDiv.textContent = contact.firstName
        lastNameDiv.textContent = contact.lastName
        emailDiv.textContent = contact.email
        phoneDiv.textContent = contact.phone

        // add divs to thier result boxes
        firstNameBox.appendChild(firstNameDiv)
        lastNameBox.appendChild(lastNameDiv)
        emailBox.appendChild(emailDiv)
        phoneBox.appendChild(phoneDiv)
      })
    }
    else{
      document.getElementById('first-name-box').textContent = 'Error'
      document.getElementById('last-name-box').textContent = 'Error'
      document.getElementById('email-box').textContent = 'Error'
      document.getElementById('phone-box').textContent = 'Error'
    }
  })
  .catch(error => console.error('Error getting contact: ', error))
})
