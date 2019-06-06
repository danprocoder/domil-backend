# DOMIL APIs

### POST /api/user/create
Creates a new user account

Required Fields:
1. email
2. mobile
3. password

Response on success:
Status code: 201
Response:
```
{
    token: [Session token],
    user: [User object],
}
```

### POST /api/user/auth
Authenticates the user

Required Fields:
1. email
2. password

Response on success:
Status code: 200
Response:
```
{
    token: [Session token],
    user: [User object]
}
```

### POST /api/user/verify/mobile
Verify the user's inputted code with the code sent to the user's mobile device on registration.
