# mod_minute - One-minute paper

A small Moodle activity for the classic One-minute paper technique.

Students answer a short prompt such as:

> In approximately one minute, write the main concept you learned.

Features:

- configurable response character limit;
- optional opening and closing dates;
- one editable response per participant while the activity is open;
- teacher report with all responses and CSV export;
- optional same-IP-as-teacher restriction;
- optional browser geolocation restriction using a teacher reference point and radius;
- group-aware report;
- Privacy API support.

## Requirements

- Moodle 4.5 or later.
- HTTPS is normally required by browsers when geolocation is enabled.

## Presence validation notes

IP comparison uses the client IP detected by Moodle. Sites behind reverse proxies must configure Moodle and the proxy
correctly, otherwise all users may appear with the proxy address.

Browser geolocation is not a cryptographic proof of physical presence. It is useful as a classroom check, but
device/browser location can be unavailable or manipulated on a compromised client.
