# API Calls

* [Users](#users)
    * [Register](#register)

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
* `password.validation.required`
* `password.validation.string`
