# OmniKassa 2 bug OrderItems.name AN..max 50

**Question Pronamic** on **11 jan. 2019 10:40**:

We hebben via een aantal van jullie OmniKassa 2 klanten een probleem ontdekt. Volgens de documentatie mag de OrderItems.name maximaal 50 tekens zijn (AN..max 50) Verder op in de documentatie staat hierover nog het volgende:

> **A.. Max nn**  
> A field that consists of letters and other characters, such as ".", "@", etc. This field contains a maximum of nn characters.

We kwamen bijvoorbeeld een klant tegen met volgende product naam met 48 tekens:

> W&N Artists Aquarel 692 Viridian (s3) - tube 5ml

OmniKassa 2 antwoord echter met de volgende foutmelding:

```
the item name is too long, maximum length is [50]
```

Na veel onderzoek zijn we tot de ontdekking gekomen dat het probleem hem in tekens zoals &, < en > zit. De volgende namen met 50 tekens werken namelijk niet:

- `1234567890123456789012345678901234567890123456789&`
- `1234567890123456789012345678901234567890123456789<`
- `1234567890123456789012345678901234567890123456789>`

En de volgende namen wel:

- `123456789012345678901234567890123456789012345&`
- `1234567890123456789012345678901234567890123456<`
- `1234567890123456789012345678901234567890123456>`

We vermoeden dat jullie verkeerd om gaan met de `&`, `<` en `>` tekens. Waarschijnlijk worden deze tekens omgezet naar de HTML-entities en daarna de lengte berekend:

- `&` » `&amp;`
- `<` » `&lt;`
- `>` » `&gt;`

Het probleem is overigens niet beperkt tot bovenstaande tekens ook bij tekens die op bovenstaande tekens lijken gaat het fout. Het gaat dan bijvoorbeeld om de volgende tekens:

- `＆` Fullwidth Ampersand (https://unicode-table.com/en/FF06/)
- `﹤` Small Less-Than Sign (https://unicode-table.com/en/FE64/)
- `＜` Fullwidth Less-Than Sign (https://unicode-table.com/en/FF1C/)
- `﹥` Small Greater-Than Sign (https://unicode-table.com/en/FE65/)
- `＞` Fullwidth Greater-Than Sign (https://unicode-table.com/en/FF1E/)

Het zou fijn zijn als dit snel opgelost kan worden, omdat we nu niet op een juiste manier de namen kunnen inkorten. We ontvangen graag even een bevestiging van dit probleem en horen graag wanneer dit opgelost is. Alvast bedankt.
