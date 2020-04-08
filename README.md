# Lumen Game
## Structure
1. Router `routes/web.php`
2. Controller `app/Http/Controllers/PlayerController.php`
3. Model `app/Models/Player.php`
4. JSON data `storage/app/players.json`
5. Unit test (TODO) `tests/Unit/ActionTest.php`

## Setup
1. Copy `.env.example` -> `.env`
1. Run `docker-compose up -d --build`
2. Send POST `action` request from the Postman collection with the JSON payload (`lumen-game.postman_collection.json`)
