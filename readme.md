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


### POST /api/brand
Create a user's brand

Required fields:
1. Name
2. Address
3. About
4. logo (optional)

Response on success:
Status code: 201
Response:
```
{
    "message": "User's brand created successfully"
}
```


### PATCH /api/brand
Update a user's brand details

Fields:
1. Name (optional)
2. Address (optional)
3. About (optional)
4. logo_url (optional)

Response on success:
Status code: 200
Response:
```
{
    "message": "User brand details updated successfully",
    "brand": [Brand object]
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


### POST /api/brand/{brand_id}/job
Post a job to a brand

Fields:
1. title (required)
2. description (required)
3. attachment (optional, array)

Response on success:
Status code: 200
Response:
```
{
    "message": "Job posted to [brand name] successfully"
}
```


### GET /api/brand/jobs
Get all jobs posted to the logged in brand

Response on success:
Status code: 200
```json
{
    "jobs": [ ...all jobs ]
}
```


### GET /api/customer/jobs
Get all jobs posted by the logged in customer

Response on success:
Status code: 200
```json
{
    "jobs": [ ...all jobs ]
}
```
