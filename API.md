# API Calls

* [Authentication](#authentication)
    * [Login](#login)
    * [Authenticated User](#authenticated-user)
* [Users](#users)
    * [Register](#register)
    
## Authentication

### Login

`POST /api/auth`

#### Parameters

Parameter | Type | Required
--------- | ---- | --------
`email` | email | Yes
`password` | string | Yes

#### Response

HTTP Status Code `204`

#### Error Response

HTTP Status Codes:

* `422`: Validation Errors
* `500`: Server Error

##### Validation Errors

* `email.validation.required`
* `password.validation.required`

### Authenticated User

`GET /api/me`

#### Response

HTTP Status Code `200`

```json
{
  "user": {
    "uuid": "1234-5678-90AB-CDEF",
    "email": "example@email.com"
  }
}
```

#### Error Response

* `401`: Not Authenticated
* `500`: Server Error

### Logout

`POST /api/logout`

#### Response

HTTP Status Code `204`

#### Error Response

* `401`: Not Authenticated
* `500`: Server Error

## Users

### Register

`POST /api/users`

#### Parameters

Parameter | Type | Required
--------- | ---- | --------
`email` | email | Yes
`password` | string | Yes

#### Response

HTTP Status Code `201`

```json
{
  "user": {
    "uuid": "1234-5678-90AB-CDEF",
    "email": "example@email.com"
  }
}
```

#### Error Response

HTTP Status Codes:

* `422`: Validation Errors
* `500`: Server Error

##### Validation Errors

* `email.validation.required`
* `email.validation.email`
* `email.validation.user_not_unique`
* `password.validation.required`
* `password.validation.string`

### Update Data

`PATCH /api/users`

#### Parameters

Parameter | Type | Required
`email` | email | No

#### Response

HTTP Status Code `200`

```json
{
  "user": {
    "uuid": "1234-5678-90AB-CDEF",
    "email": "example@email.com"
  }
}
```

#### Error Response

HTTP Status Codes:

* `401`: Not Authenticated
* `422`: Validation Errors
* `500`: Server Error

##### Validation Errors

* `email.validation.email`
* `email.validation.user_not_unique`
