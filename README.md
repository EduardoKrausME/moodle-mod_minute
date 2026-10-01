# mod_minute - One-minute paper

A Moodle activity for the classic One-minute paper technique, where students answer a short reflection prompt such as:

> In approximately one minute, write the main concept you learned.

## How it works

The teacher defines the prompt, response limit and optional opening and closing dates. Each participant keeps one editable
response while the activity is open, and the teacher can review all answers in a group-aware report or export them to
CSV.

The activity can optionally restrict responses to the same detected IP as the teacher or to a geographic radius around a
teacher reference point.

## Presence validation

IP comparison uses the client IP detected by Moodle, so environments behind reverse proxies need their normal proxy
configuration to expose the appropriate client address.

Browser geolocation is a classroom validation aid rather than cryptographic proof of physical presence, because device
location can be unavailable or manipulated on a compromised client.

Stored learner data is exposed through Moodle's Privacy API.
