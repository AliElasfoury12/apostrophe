## BASE URL
    http://localhost:8000

## 1.Register User

### Path
    POST /register

### Headers
    Content-Type : application/json

### Request
```json
    {
        "name":"ali",
        "email":"ali@gmail.comm",
        "password":"12345Ai$",
        "password_confirm":"12345Ai$"
    }
```

### Response (201)
```json
    {
        "message": "User Created Successfully",
        "user": {
            "id": 1,
            "name": "ali",
            "email": "ali@gmail.com",
        }
    }
```

## 2.Login User

### Path
    POST /login

### Headers
    Content-Type : application/json 

### Request
```json
    {
        "email":"ali@gmail.comm",
        "password":"12345Ai$",
    }
```

### Response (200)
```json
    {
        "message": "User Loggedin Successfully",
        "user": {
            "id": 1,
            "name": "ali",
            "email": "ali@gmail.com",
            "role": "user"
        },
        "token": "eyJhbGdvIjoiSFMyNTYiLCJ0eXBlIjoiSldUIn0.eyJjcmVhdGVkX2F0IjoxNzY0OTEwOTA4LCJleHBpcmVzX2F0IjoxNzY3NTAyOTA4LCJ1c2VyIjp7ImlkIjo4LCJuYW1lIjoiYWxpIiwiZW1haWwiOiJhbGk3QGdtYWlsLmNvbSIsInJvbGUiOiJ1c2VyIn0sInR5cGUiOiJhY2Nlc3NfdG9rZW4ifQ._PBTtu1F6Z6PozYcAP2SaZj5w9D5pCSJtDDIyxkCGdE"
    }
```

## 3.Get Users

### Path
    GET /users

### Headers
    Authorization: "Bearer {$auth-bearer-token}" 

### Response (200)

new_token(optional)
```json
    {
        "users": [
            {
                "id": 1,
                "name": "ali",
                "email": "ali@gmail.com",
                "role": "admin"
            },
            {
                "id": 2,
                "name": "ali2",
                "email": "ali2@gmail.com",
                "role": "user"
            }
        ],
        "new_token": "eyJhbGdvIjoiSFMyNTYiLCJ0eXBlIjoiSldUIn0.eyJjcmVhdGVkX2F0IjoxNzY0OTEwOTA4LCJleHBpcmVzX2F0IjoxNzY3NTAyOTA4LCJ1c2VyIjp7ImlkIjo4LCJuYW1lIjoiYWxpIiwiZW1haWwiOiJhbGk3QGdtYWlsLmNvbSIsInJvbGUiOiJ1c2VyIn0sInR5cGUiOiJhY2Nlc3NfdG9rZW4ifQ._PBTtu1F6Z6PozYcAP2SaZj5w9D5pCSJtDDIyxkCGdE"
    }
```

## 4.Update User

### Path
    POST /users/{id}
  
### Headers
    Content-Type : application/json 
    Authorization: Bearer {$auth-bearer-token} 

### Request
```json
    {
        "_method":"PATCH",
        "name":"ali1",
        "email":"ali1@gmail.comm",
    }
```

### Response (200)

new_token(optional)
```json
    {
        "users": [
            {
                "id": 1,
                "name": "ali",
                "email": "ali@gmail.com",
                "role": "admin"
            },
            {
                "id": 2,
                "name": "ali2",
                "email": "ali2@gmail.com",
                "role": "user"
            }
        ],
        "new_token": "eyJhbGdvIjoiSFMyNTYiLCJ0eXBlIjoiSldUIn0.eyJjcmVhdGVkX2F0IjoxNzY0OTEwOTA4LCJleHBpcmVzX2F0IjoxNzY3NTAyOTA4LCJ1c2VyIjp7ImlkIjo4LCJuYW1lIjoiYWxpIiwiZW1haWwiOiJhbGk3QGdtYWlsLmNvbSIsInJvbGUiOiJ1c2VyIn0sInR5cGUiOiJhY2Nlc3NfdG9rZW4ifQ._PBTtu1F6Z6PozYcAP2SaZj5w9D5pCSJtDDIyxkCGdE"
    }
```

## 5.Change User Password

### Path
    POST /change_password/{user_id}

### Headers
    Content-Type : application/json
    Authorization: Bearer {$auth-bearer-token} 

### Request
```json
    {
        "_method":"PATCH",
        "password":"12345Ai$",
        "new_password":"12345Ai#",
        "new_password_confirm":"12345Ai#"
    }
```

### Response (200)

```json
    {
        "message": "User Password Updated Successfully",
        "user": {
            "id": 1,
            "name": "ali1",
            "email": "ali1@gmail.com",
            "role": "admin"
        },
        "new_token": "eyJhbGdvIjoiSFMyNTYiLCJ0eXBlIjoiSldUIn0.eyJjcmVhdGVkX2F0IjoxNzY0OTEwOTA4LCJleHBpcmVzX2F0IjoxNzY3NTAyOTA4LCJ1c2VyIjp7ImlkIjo4LCJuYW1lIjoiYWxpIiwiZW1haWwiOiJhbGk3QGdtYWlsLmNvbSIsInJvbGUiOiJ1c2VyIn0sInR5cGUiOiJhY2Nlc3NfdG9rZW4ifQ._PBTtu1F6Z6PozYcAP2SaZj5w9D5pCSJtDDIyxkCGdE"
    }
```

## 6.Delete User

### Path
    DELETE /users/{user_id}

### Headers
    Content-Type : application/json
    Authorization: Bearer {$auth-bearer-token} 


### Response (200)

```json
    {
        "message": "User Deleted Successfully",
        "user": {
            "id": 8,
            "name": "ali",
            "email": "ali7@gmail.com",
            "role": "user"
        }
    }
```