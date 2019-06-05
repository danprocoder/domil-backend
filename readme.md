# DOMIL APIs

#### User registration
`POST /api/user/create`

Required Fields:
1. email
2. mobile
3. password

Response on success:
Status code: 200
Response:
```
{
    token: [Session token],
    data: [User object],
}
```
