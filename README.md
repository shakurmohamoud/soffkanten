# Soffkanten

A forum where you create an account, join groups and talk about what
you are interested in.

## Tech

- PHP 8.3 with Apache
- MySQL 8
- Docker
- PDO with prepared statements

No frameworks and no JavaScript. Everything is rendered in PHP.

## Get started

```
git clone https://github.com/shakurmohamoud/soffkanten.git
cd soffkanten
docker compose up -d
```

Then open http://localhost:8080.

The database sets itself up the first time. `db/schema.sql` is mounted into
the MySQL container and runs when it starts, so you do not have to do
anything extra.

The database is empty on the first start. Create an account on /registrera.php

The data is stored in the volume `dbdata` and stays there after
`docker compose down`.

## Where things are

**Pages** are in `html/`, **functions** in `src/`, **the database** in
`db/schema.sql`.

| Feature                 | File                                                       |
| ----------------------- | ---------------------------------------------------------- |
| Create account, log in  | `html/registrera.php`, `html/logga-in.php`, `src/auth.php` |
| Create a group          | `html/skapa-grupp.php`, `src/grupper.php`                  |
| Apply and approve       | `html/grupper.php`, `html/grupp.php`, `src/grupper.php`    |
| Roles                   | `sattRoll` in `src/grupper.php`                            |
| Discussions and replies | `html/amne.php`, `src/diskussioner.php`                    |
| Invite link             | `html/inbjudan.php`, `src/inbjudningar.php`                |

Only admins can approve applications and change roles. That rule lives in
the database, not just in PHP. Look at the self join in `godkannAnsokan`
and `sattRoll`.

## The passwords in docker-compose.yml

The database passwords are in plain text in `docker-compose.yml`, so the
project can be cloned and run right away without setting up a `.env` first.
In a real project they would live in environment variables and never end up
in the repo.
