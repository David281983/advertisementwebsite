# Ads Project

A classifieds platform built with Symfony 6.4 — users publish advertisements with photos and
location, browse and search listings, and save favourites.

Built as a learning project to get hands-on with Doctrine relations, Symfony security and
authorization, file uploads and third-party API integration.




---

## Features

- **Advertisements** — create, edit and delete, with 1–10 photos each
- **Photo gallery** — one cover image in the listing, full gallery on the ad page
- **Geocoding** — location autocomplete via the [Photon](https://photon.komoot.io) API,
  storing name plus latitude/longitude for future distance search
- **Search & filters** — free-text search over title and description, partial location match,
  sort by newest or oldest
- **Pagination** — Doctrine `Paginator` with `fetchJoinCollection` so limits count entities,
  not joined rows
- **Favourites** — save ads, see how many people saved each one, browse a "top liked" page
- **Accounts** — registration with email verification, profiles with avatar, bio and contact details
- **Authorization** — a Doctrine voter decides who may edit or delete each ad; buttons are
  hidden accordingly

## Tech stack

| | |
|---|---|
| Backend | PHP 8.4+, Symfony 6.4, Doctrine ORM |
| Database | MariaDB 10.8.3 |
| Frontend | Twig, Tailwind CSS, AssetMapper (no build step) |
| Mail | Symfony Mailer — Mailpit locally, Brevo SMTP in production |
| Infra | Docker Compose (MariaDB, Mailpit, Adminer) |

## Running locally

```bash
git clone <repo-url>
cd project
composer install
```

Start the containers:

```bash
docker compose up -d
```

Create `.env.local` with your own values:

```
DATABASE_URL="mysql://root:root@127.0.0.1:3306/adsproject?serverVersion=10.8.3-MariaDB&charset=utf8mb4"
APP_SECRET=<any random string>
```

Set up the database and load demo data:

```bash
php bin/console doctrine:database:create
php bin/console doctrine:migrations:migrate
php bin/console doctrine:fixtures:load
```

```bash
symfony server:start
```

The app runs at `http://localhost:8000`. Sent emails land in Mailpit at
`http://localhost:8025`.

## Notes

A few things worth calling out:

- **N+1 queries.** The listing page issued one extra query per advertisement. The Symfony
  profiler traced it to `User::$userProfile` — an inverse-side `OneToOne`, which Doctrine
  cannot lazy-load and therefore fetches eagerly for every hydrated user. Adding the profile
  to the join brought every page down to four queries regardless of how many ads it shows.
- **Repository design.** A single `findAllQuery()` takes named arguments for joins, filters
  and sorting, so new combinations are a call rather than a new method.
- **Environment variables win over `.env` files.** The Symfony CLI injects the Docker
  container's mail settings automatically, which silently overrode the production SMTP
  configuration during testing.

## Possible improvements

- Categories with per-category attributes (rooms, area, mileage)
- Distance search using the stored coordinates
- Resend the verification email, and roll back registration if sending fails
- Automated tests