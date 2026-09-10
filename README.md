# Soffkanten

Ett forum där man skapar ett konto, går med i grupper och pratar om det
man är intresserad av.

Uppgift 2: Let's talk about it i kursen Systemutveckling FSU25D på Medieinstitutet.

## Teknik

- PHP 8.3 med Apache
- MySQL 8
- Docker
- PDO med prepared statements

Inga ramverk, ingen JavaScript. Allt renderas i PHP.

## Kom igång

```
git clone https://github.com/shakurmohamoud/soffkanten.git
cd soffkanten
docker compose up -d
```

Öppna sedan http://localhost:8080.

Databasen sätts upp automatiskt första gången. `db/schema.sql` monteras in
i MySQL-containern och körs när den startar, så du behöver inte göra något
extra.

Databasen är tom första starten. Registrera ett konto på /registrera.php

Datan sparas i volymen `dbdata` och finns kvar efter `docker compose down`.

## Var finns vad

**Sidor** ligger i `html/`, **funktioner** i `src/`, **databasen** i
`db/schema.sql`.

| Funktion              | Fil                                                        |
| --------------------- | ---------------------------------------------------------- |
| Skapa konto, logga in | `html/registrera.php`, `html/logga-in.php`, `src/auth.php` |
| Skapa grupp           | `html/skapa-grupp.php`, `src/grupper.php`                  |
| Ansöka och godkänna   | `html/grupper.php`, `html/grupp.php`, `src/grupper.php`    |
| Roller                | `sattRoll` i `src/grupper.php`                             |
| Diskussioner och svar | `html/amne.php`, `src/diskussioner.php`                    |
| Inbjudningslänk       | `html/inbjudan.php`, `src/inbjudningar.php`                |

Att endast admin kan godkänna ansökningar och ändra roller ligger i
databasen, inte bara i PHP. Titta på självjoinen i `godkannAnsokan` och
`sattRoll`.

## Lösenorden i docker-compose.yml

Databasens lösenord står i klartext i `docker-compose.yml`, så att
projektet går att klona och köra direkt utan att man behöver fixa en `.env`
först. I ett riktigt projekt hade de legat i miljövariabler och aldrig
hamnat i repot.