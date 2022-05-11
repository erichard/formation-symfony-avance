<?php

declare(strict_types=1);

namespace Deployer;

require 'recipe/symfony4.php';

// Project name
set('application', 'oms');

// Project repository
set('repository', 'git@gitlab.com:groupe-royer/oms.git');

set('env', [
    'APP_ENV' => 'prod',
]);

set('composer_options', '{{composer_action}} --no-dev --optimize-autoloader --apcu-autoloader  --no-interaction');

// Shared files/dirs between deploys
add('shared_files', ['.env.local']);
add('shared_dirs', ['var/imports']);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts
host('oms.erwan-richard.tech')
    ->stage('preprod')
    ->set('deploy_path', '~');

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

task('deploy:compile', function () {
    runLocally('yarn install && yarn encore production');
    upload('public/build', '{{release_path}}/public');
});
before('deploy:cache:clear', 'deploy:compile');

task('deploy:restart-worker', function () {
    run('cd {{release_path}}; bin/console messenger:stop-workers');
});

task('deploy:dump-env', function () {
    run('cd {{release_path}};
        php composer.phar dump-env');
});

task('deploy:restart-php', function () {
    run('cd {{release_path}};
        curl -sLO https://github.com/gordalina/cachetool/releases/download/8.4.0/cachetool.phar ;
        chmod +x cachetool.phar;
        php cachetool.phar opcache:reset --fcgi=/run/php/oms.sock;
        rm cachetool.phar');
});

task('deploy:adminer:download', function () {
    run('curl -sL -o {{release_path}}/public/adminer.php https://www.adminer.org/latest.php');
});

before('deploy:symlink', 'deploy:adminer:download');
before('deploy:symlink', 'database:migrate');
#after('deploy:vendors', 'deploy:dump-env');

after('deploy:symlink', 'deploy:restart-worker');
after('deploy:symlink', 'deploy:restart-php');

task('database:download', function () {
    run('pg_dump -Fc oms -f {{deploy_path}}/shared/preprod.dump');
    download('{{deploy_path}}/shared/preprod.dump', 'var/database/');
});
