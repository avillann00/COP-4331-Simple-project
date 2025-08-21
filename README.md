# POOS Simple Project

Members:
- Dylan
- Austin
- Isaac
- Jai

## **Best Practice**

### **Repo/Branch Management** 

Do not push directly to main

Do not work on main
- Create a feature branch for each task 
```bash
git checkout -b feature/<branch-name>
```

### **Commit Messages** 

Use a clear format similar to this one

[\<type\>] Scope: short description

ex:
- [feature] API: Added New Endpoint
- [fix] Users: Fixed Email Verification

### **Pull Requests** 

Open a pull request to main for every change
- Include a clear title and description
- Request a review or ask in Discord

### **Code Quality** 

Test code extensively

Write clear, readable code with meaningful variable names

Include comments when necessary

Use a .env for any sensitive credentials

## **Local Testing** 

I created a docker-compose.yml file that allows you to test locally

In order to use:
- Make sure to have Docker and Docker Compose installed on your machine (installation varies by OS)
- From the project root (where docker-compose.yml is located)
```bash
docker compose up -d
```
- Open your browser at http://localhost:8080
- when done run
```bash
docker compose down
```

note:
- If your having permission issues prefix your commands with `sudo` 
