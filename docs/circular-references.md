---
layout: default
title: Circular References
---

# Circular References

This library fully supports recursive references.  Consider the following example:

```json
{
  "author": {
    "properties": {
        "name": {
          "type": "string"
        },
        "co-author": {
            "$ref": "#/author" // circular reference
          }
        }
    }
  }
};
```

## Resolving

If the dereferencer attempted to fully resolve this reference, the dereferencer would continue looping infintely.  Instead of resolving references immediately, the $ref is replaced with a [lazy proxy object](https://github.com/league/json-reference/blob/master/src/Reference.php).  The reference is only resolved when it's accessed.

Because circular object references are possible, make sure your code accessing the dereferenced object does not get stuck in an infinite loop!
