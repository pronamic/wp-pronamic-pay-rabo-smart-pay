# OmniKassa 2.0 Documentation

## Order announce `description`

**Question Pronamic** on **maandag 15 oktober 2018 10:48**:

> Daarnaast staat bij het `Order announce` veld `description` dat het `AN..max 35` formaat van toepassing is. In de voorbeelden daaronder geven jullie echter de volgende `description`:
> 
> 
> > Aankoop mijn webwinkel ordernummer 123
> 
> Deze beschrijving is `38` tekens langs en is dus langer als de toegestane `35` tekens. Kunnen jullie toelichten hoe dit precies zit en wat nou wel/niet is toegestaan? We willen problemen met de signature e.d. namelijk graag voorkomen. We horen het graag!

**Answer Rabobank** on **19 okt. 2018 16:26**:

Answer by phone, but was somehting like:

> De beschrijving mag maximaal `35` tekens zijn, de documentatie is onjuist.

## Error `order could not be restricted to AFTERPAY, POI with id 5000 can only be restricted to payment brands: BANCONTACT, IDEAL, MAESTRO, MASTERCARD, PAYPAL, VISA, V_PAY`

**Question Pronamic** on **maandag 15 oktober 2018 10:48**:

> Als ontwikkelaar van de WordPress betalen plugin Pronamic Pay zouden we graag de AfterPay betaalmethode die jullie aanbieden willen gaan testen. We zijn momenteel namelijk de Pronamic Pay betalen plugin aan het uitbreiden met achteraf betalen opties. Hierdoor zal de OmniKassa 2.0 AfterPay optie straks ook beschikbaar zijn voor een aantal populaire WordPress plugins zoals: Gravity Forms, Ninja Forms, Formidable Forms, WooCommerce, MemberPress, etc. Momenteel kunnen we onze implementatie echter niet testen vanwege de volgende foutmelding:
> 
> `order could not be restricted to AFTERPAY, POI with id 5001 can only be restricted to payment brands: BANCONTACT, IDEAL, MAESTRO, MASTERCARD, PAYPAL, VISA, V_PAY`
>
> Zouden jullie misschien alle betaalmethodes binnen ons OmniKassa 2.0 account willen activeren zodat wij dit goed kunnen testen?

**Question Pronamic** on **22 okt. 2018 08:56**:

> Helaas werkt het nog niet, we krijgen nog de volgende foutmelding retour:
> 
> `order could not be restricted to AFTERPAY, POI with id 5000 can only be restricted to payment brands: BANCONTACT, IDEAL, MAESTRO, MASTERCARD, PAYPAL, VISA, V_PAY`
>
> ISO 8601 timestamp van testen:
> `2018-10-22T06:33:37+00:00`
> 
> Je verder telefonisch aan dat de beschrijving echt maar maximaal 35 tekens mag zijn, kan het kloppen dat een langere beschrijving wel gewoon werkt?
> 
> Verder waren we nog benieuwd of er een nog restricties zijn qua tekens binnen het AN-formaat. Binnen de iDEAL Basic aansluiting waren speciale tekens (íóéóî..etc.) bijvoorbeeld niet toegestaan.

**Answer Rabobank** on **24 okt. 2018 13:38**:

> Onderstaande staat uit bij onze IT-developer. Zodra ik bericht heb hoor je dat gelijk.

**Answer Rabobank** on **25 okt. 2018 08:41**:

> Onze it-developers hebben naar onderstaande transactie gekeken en kunnen deze niet vinden in de database. Klopt het tijdstip voor de afterpay transactie?
> 
> Zij geven het volgende aan:
> > I checked as well and could not find any Pronamic entries around 2018-10-22T06:33:37+00:00. Please verify with merchant.
> 
> Kan jij een dubbelcheck doen of wellicht een ander voorbeeld geven?

**Question Pronamic** on **25 okt. 2018 09:18**:

> Ik heb zojuist opnieuw een test gedaan, zal tussen 09:10 en 09:15 Nederlandse tijd geweest zijn. We krijgen nog steeds de volgende foutmelding terug:
> 
> `order could not be restricted to AFTERPAY, POI with id 5000 can only be restricted to payment brands: BANCONTACT, IDEAL, MAESTRO, MASTERCARD, PAYPAL, VISA, V_PAY`
> 
> Kan de IT-developer ook niet zelf testen waarom we bovenstaande foutmelding krijgen? We testen trouwens op de sandbox: https://betalen.rabobank.nl/omnikassa-api-sandbox/.
> 
> Rond 09:16 nog een test gedaan:
>

**Request:**

```
{"timestamp":"2018-10-25T07:15:56+00:00","merchantOrderId":"733","description":"WordPress plugin","orderItems":[{"id":"417","name":"WordPress plugin - Optie A","quantity":1,"amount":{"currency":"EUR","amount":1210},"tax":{"currency":"EUR","amount":210},"category":"DIGITAL"}],"amount":{"currency":"EUR","amount":1210},"shippingDetail":{"firstName":"Remco","middleName":null,"lastName":"Tolsma","street":"Burgemeester Wuiteweg","houseNumber":"39b","houseNumberAddition":"b","postalCode":"9203 KA","city":"Drachten","countryCode":"NL"},"billingDetail":{"firstName":"Remco","middleName":null,"lastName":"Tolsma","street":"Burgemeester Wuiteweg","houseNumber":"39b","houseNumberAddition":"b","postalCode":"9203 KA","city":"Drachten","countryCode":"NL"},"customerInformation":{"emailAddress":"info@remcotolsma.nl","dateOfBirth":"22-09-1985","gender":"M"},"language":"nl","merchantReturnURL":"https:\/\/www.remcotolsma.nl\/?payment=733&key=pay_5bd16daa453a4","paymentBrand":"AFTERPAY","paymentBrandForce":"FORCE_ONCE","signature":"b8b9507057c7d53aeaece009fa7c5c62ee1dc78ffbcc12fa675f03b0a3fec2b69f39f4f946cb05c96a6139f52701412c5b4e9028a8a8ba1a1864d852221888fb"}
```

**Response:**

```
{"errorCode":5024,"consumerMessage":"order could not be restricted to AFTERPAY, POI with id 5000 can only be restricted to payment brands: BANCONTACT, IDEAL, MAESTRO, MASTERCARD, PAYPAL, VISA, V_PAY"}
```

**Question Pronamic** on **26 okt. 2018 15:54**:

> Description zit er wel gewoon in, zie ook mijn request in voorgaande e-mail. De foutmelding die we terug krijgen zegt ook niet iets over een `description`.
> 
> `order could not be restricted to AFTERPAY, POI with id 5000 can only be restricted to payment brands: BANCONTACT, IDEAL, MAESTRO, MASTERCARD, PAYPAL, VISA, V_PAY`
> 
> Als het al in de `description` zit dan moet sowieso ook de foutmelding duidelijker lijkt mij. Passen jullie de documentatie ook nog over de lengte van de description? En krijgen we ook nog antwoord op welke tekens zijn toegestaan binnen AN?

**Question Pronamic** on **5 nov. 2018 17:16**:

> Heb je nog tijd gehad om naar voorgaand bericht te krijgen? Wij, onze en jullie klanten willen graag verder.

**Answers Rabobank** on **06.11.2018 - 14:11**:

Answer by phone, but was somehting like:

> De `description` in de `orderItems` ontbreekt, deze is verplicht voor AfterPay.

According the OmniKassa 2.0 documentation (`v1.5`) the `OrderItem.description` parameter is optional.
After some testing it seems this is not the case for AfterPay payments where `OrderItem.description` is required.
