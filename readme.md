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

### GET /api/user/verify/mobile
Verify the user's inputted code with the code sent to the user's mobile device on registration.

Required parameters:
1. code

Response on success:
Status code: 200
Response:
```
{
    message: 'Mobile number verified successfully'
}
```

### GET /api/user/mobile-verification-code/resend
Resend mobile verification code

Response on success:
Status code: 200
Response:
```
{
    message: 'Mobile verification code resent'
}
```

### POST /api/brand/create
Create a user's brand

Required fields:
1. Name
2. Address
3. About

Response on success:
Status code: 201
Response:
```
{
    "message": "User's brand created successfully"
}
```

### PATCH /api/user
Update a users details

Fields:
1. firstname (optional)
2. lastname (optional)
3. email (optional)
4. mobile (optional)

Response on success:
Status code: 200
Response:
```
{
    "message": "User details updated successfully",
    "user": [User object]
}
```
