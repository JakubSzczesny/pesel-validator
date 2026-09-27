# Walidator PESEL

Walidator numeru PESEL napisany w PHP 8.2 i Symfony 5.4.

Walidacja jest zrobiona jako constraint Symfony (`App\Validator\Pesel`), więc można go użyć w formularzu, API albo na polu encji. Sprawdzane jest:

- czy numer ma 11 cyfr,
- czy zawiera poprawną datę urodzenia (z uwzględnieniem stulecia zakodowanego w miesiącu),
- czy zgadza się cyfra kontrolna.

Dla poprawnego numeru zwracana jest też data urodzenia i płeć.

## Uruchomienie

Docker:

```bash
docker compose up --build
```

Bez Dockera (PHP 8.2 + Composer):

```bash
composer install
php -S localhost:8000 -t public public/index.php
```

Aplikacja bedzie dostępna pod adresem http://localhost:8000.

## API

```bash
curl -X POST http://localhost:8000/api/pesel -H "Content-Type: application/json" -d '{"pesel": "44051401359"}'
```

```json
{"pesel": "44051401359", "valid": true, "birthDate": "1944-05-14", "gender": "mężczyzna"}
```

## CLI

```bash
php bin/console app:pesel:validate 44051401359
```

## Testy

```bash
php vendor/bin/phpunit
```

albo w kontenerze:

```bash
docker compose run --rm app php vendor/bin/phpunit
```
