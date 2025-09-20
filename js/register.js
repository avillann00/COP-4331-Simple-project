document.getElementById('registerForm').addEventListener('submit', function(e){
  e.preventDefault()

  const firstName = document.getElementById('firstName').value 
  const lastName = document.getElementById('lastName').value 
  const username = document.getElementById('username').value 
  const password = document.getElementById('password').value 
  const passwordConfirm = document.getElementById('passwordConfirm').value 

  if(password !== passwordConfirm){
    document.getElementById('message').textContent = 'Passwords do not match'
    return
  }

  fetch('/LAMPAPI/Register.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({ firstName: firstName, lastName: lastName, login: username, password: password })
  })
  .then(response => response.json())
  .then(data => {
    if(data.error){
      document.getElementById('message').textContent = data.error
    }
    else{
      document.getElementById('message').textContent = `Welcome ${data.firstName} ${data.lastName}`
      window.location.href = 'login.html'
    }
  })
  .catch(error => console.error('Error: ', error))
})
