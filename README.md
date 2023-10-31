# JWT abstraction library

I often have the need to let a program
just call an object to encode/decode a payload,
without fussing about keys, algorithms etc.

The 'coder' object can be prepared and then
given to a consumer that only has to call
decode/encode.

This mini interface provides that functionality
and can be used for JWS, JWE and Nested Webtoken.

It need an implementation, e.g. for
web-token/jwt.