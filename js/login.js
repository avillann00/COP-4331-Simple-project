document.getElementById('loginForm').addEventListener('submit', function(e){
  e.preventDefault()

  const username = document.getElementById('username').value 
  const password = document.getElementById('password').value 

  fetch('Login.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ username: username, password: password })
  })
  .then(response => response.json())
  .then(data => {
    if(data.error){
      document.getElementById('message').textContent = data.error
    }
    else{
      document.getElementById('message').textContent = `Welcome ${data.username}`
      // redirect to dashboard.html
      // window.location.href = 'dashboard.html'
    }
  })
  .catch(error => console.error('Error: ', error))
})
