
# FSSK - Laravel
> Full Stack Starter Kit Laravel Playground

Full Stack Starter Kit, but using PHP/Laravel for server instead of Node.

## Installing / Getting started

First, clone the project. Copy `server/.env.example` to `server/.env` and `client/.env.example` to `client/.env`

Run the following command:

```shell
docker-compose up -d
```

This will first build the image based off the project's `Dockerfile`.  After the image is built, it will start and the current working directory will be mounted to the app container's `/opt/src`.

This spins up a postgres instance, starts client at `http://localhost:3000`
and starts server at `http://localhost:4000`. Server calls are proxied, so `http://localhost:3000/api/users` will hit `http://localhost:4000/api/users` automagically.

To init the database:

```shell
docker exec -it fssk-laravel-server php server/artisan migrate --seed
```

Log in to the todo app with `test@earthlinginteractive.com`, password `test`.

## Developing

### Built With

The current technologies used by the starter kit are as follows:

| Type | Selected Technology | Reasoning |
| ---- | ------------------- | --------- |
| Transpiler | [TypeScript](https://www.typescriptlang.org/) | Static types make for code that is less buggy and easier to reason about.  A basic TypeScript cheatsheet can be found [here](https://www.sitepen.com/blog/2013/12/31/typescript-cheat-sheet/) and more extensive documentation [here](https://www.typescriptlang.org/docs/tutorial.html) and [here](https://www.sitepen.com/blog/2013/12/31/definitive-guide-to-typescript/) |
| View Library | [React](https://facebook.github.io/react/) | Component-based views that encourage single-directional data flow |
| Client-side State Management | [MobX](https://github.com/mobxjs/mobx) | Simpler than Redux and requires less boilerplate |
| Backend Server | [Laravel](https://laravel.com/docs/5.5) | Well documented and widely supported web framework |
| API Protocol | REST | A familiar paradigm to most developers |
| Data Mapping Framework | [Eloquent ORM](https://laravel.com/docs/5.5/eloquent) | Included with Laravel |
| Database Migrations | [Laravel Migrations](https://laravel.com/docs/5.5/migrations) | Provided by Laravel, so no additional dependencies |
| Data Store | [PostgreSQL](https://www.postgresql.org/) | Open source, rock solid, industry standard |
| Package Manager | [npm](https://www.npmjs.com/) / [composer](https://getcomposer.org/) | The battle-tested choices for node/php development |
| Containerization | [Docker](https://www.docker.com/) | Containers make deployment easy |
| Testing Framework | [Jest](https://facebook.github.io/jest/)  / [PHPUnit](https://phpunit.de/) | Complete testing package with an intuitive syntax |
| Linter | [tslint](https://github.com/palantir/tslint) | Keeps your TypeScript code consistent |

### Prerequisites

- Docker

### Setting up Dev

See Getting Started section for steps.

Once spun up, you can shell into the client or server instances like:

```shell
docker exec -it fssk-laravel-client bash
```

```shell
docker exec -it fssk-laravel-server bash
```

### Building

Build client side code:

```shell
cd client/ && npm run build
```

### Deploying / Publishing

The production Dockerfile lives at `deploy/prod.docker` and contains all the instructions required to build a
docker image that will run the application.

There is a GitLab CI file at the project root that contains instructions for deploying via GitLab to Rancher-based environments.
Out of the box, three environments are supported: `test`, `stage`, and `master` (aka production).  Each environment should have a
corresponding branch of the same name in git.  Changes flow from:
```
[feature branch] --> test --> stage --> master
```

To deploy code to an environment:

1. Make sure that you have access to the gitlab remote by registering your public SSH key with your gitlab account
1. Make sure that the gitlab remote has been added to your repo:
   ```bash
   git add remote deploy <your_gitlab_project_url>
   ```
1. Check out the branch for the environment you want to deploy:
   ```bash
   git checkout master
   ```
1. Push that branch to the GitLab remote:
   ```bash
   git push deploy
   ```
1. Check GitLab for the status of the deployment pipeline jobs.

When setting up the GitLab project, make sure to set all of the variables used in the `.gitlab-ci.yml` file in the Environment Variables section of the CI/CD Settings.
The $RANCHER_SERVICE_* environment variables should match the name of the service in Rancher.


To eek out best performance, should also run `php server/artisan config:cache` and `php server/artisan route:cache`, and make sure `APP_DEBUG` is false and `NODE_ENV=production` and `APP_ENV=production`.


#### Example Deployment
There is an example version of the fssk-laravel project itself running at https://fssk-laravel.ei-app.com
The rancher URL is https://rancher.earthlinginteractive.com/env/1a5/apps/stacks/1st7/services/1s553/containers?tags=ei-app&which=all and
the associated GitLab project is https://git.ei-platform.com/EarthlingInteractive/StarterKits/fssk-laravel
Since this starter kit doesn't use the `test` and `stage` environments, only the `master` branch of this repository is configured to deploy on rancher.

#### Testing Production Builds Locally

To test a production build locally, run:

```shell
docker-compose -f docker-compose-prod.yml up -d
```

This command will build the client & server code and spin up the server in a docker instance with http://localhost:4000/ pointing to client's index.html.
The static client-side files are being served using php.  This configuration is intended to be deployed on a rancher-based
environment.

**Note:** When switching back and forth between the local dev and prod builds, if you see docker errors complaining about the network not being found,
try running the `docker-compose down` command before switching.


## Configuration

See the .env.example files in client and server directories.

## Tests

Client and Server code each have their own tests, using Jest.

```shell
npm test
```

and 

```shell
cd server && ./vendor/bin/phpunit
```

## Artisan

Laravel has a CLI tool called [Artisan](https://laravel.com/docs/5.5/artisan). To use it:

```shell
docker exec -it fssk-laravel-server php server/artisan YOUR_COMMAND
```

Do `list` to see available commands.

### How to make a new API endpoint

- Make Model and DB Migration:

```
php artisan make:model Todo -m
```

-  Make Controller:

```
php artisan make:controller TodoController --resource --model=Todo
```

-  Add Routes

```
Route::apiResource('todos', 'TodoController');
```

-  Add Authorization Policies:

```
php artisan make:policy TodoPolicy --model=Todo
```

Register policy in `AuthServiceProvider`:

```
Todo::class => TodoPolicy::class,
```


## Style guide

TBD

## Api Reference

TBD

## Database

Using postgres v9.6. For local development, database runs in docker container. `server/database` contains init script, migrations, and seeds.

You can connect to the database with your favorite client at `localhost:5432`!

#### Run migrations:

```shell
php artisan migrate
```

#### Run seeds:

```shell
php artisan db:seed
```

#### Create new seeds:

```shell
php artisan make:seeder TodosTableSeeder
```

Add it to `DatabaseSeeder.php`:

```
$this->call(TodosTableSeeder::class);
```

## Licensing

[MIT License](LICENSE.md)

---

## Tips and Tricks

### Windows Line Endings

Make sure git globally has line endings set to LF.  This needs to be set ***before*** cloning the project.

- For windows: `git config --global core.autocrlf false`
- For linux/mac: `git config --global core.autocrlf input`

If you forget to do this in windows, you make get errors starting docker like `file not found`. 
Update the line endings of any files that are crlf to lf and try again.

### Windows Watching

In order for file changes to be picked up by the watchers in client side code, be sure to set `CHOKIDAR_USEPOLLING=true`
in the `.env` file. 
