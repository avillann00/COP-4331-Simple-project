const urlParams = new URLSearchParams(window.location.search)
const userId = urlParams.get('userId')

document.getElementById('logout').addEventListener('click', function(e){
  e.preventDefault()

  window.location.href = 'login.html'
})

document.addEventListener('DOMContentLoaded', async function(e){
  if(!userId){
    document.getElementById('message').textContent = 'No user id'
    return
  }

  fetch('User.php', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ userId })
  })
  .then(response => response.json())
  .then(data => {
    if(data.error){
      document.getElementById('message').textContent = 'Error getting user'
    }
    else{
      document.getElementById('username').textContent = data.username
    }
  })
  .catch(error => {
    console.error('Error getting user: ', error)
    document.getElementById('message').textContent = 'Error getting user'
  })
})

document.getElementById('deleteAccount').addEventListener('click', function(e){
  e.preventDefault()

  // make pop up box asking for confirmation

  fetch('User.php', {
    method: 'DELETE',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ userId })
  })
  .then(response => response.json())
  .then(data => {
    if(data.error){
      document.getElementById('message').textContent = data.error
    }
    else{
      document.getElementById('message').textContent = 'Account successfully deleted'
      window.location.href = 'Register.html'
    }
  })
  .catch(error => {
    console.error('Error deleting account: ', error)
    document.getElementById('message').textContent = error
  })
})
