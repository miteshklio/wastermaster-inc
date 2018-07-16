from __future__ import with_statement
from fabric.api import local, cd, hide, put, env, sudo
import time
import os

"""
Deployment Scripts

REQUIREMENTS:
`brew install gnu-tar`
`pip install Fabric`

USAGE:

1) Set an environment variable called `DEPLOY_BOX` with the production machine ssh creds, like 'root@127.0.0.1'.
2) Make sure your webserver is pointing to your `current` directory. This is where the symlink is placed for your releases.
3) Fill out the config variables to your liking.

To deploy a new release, run `fab deploy:<name_of_branch>`
To rollback a release, run `fab rollback` and fill in the appropriate responses.

"""

""" Config """
# This uses your ssh config for your machine for passwordless access
env.use_ssh_config = True

# Name our release
release_name = 'release-' + time.strftime("%d-%m-%Y-%H-%M-%S")

# Machines to execute remove commands on.
# Create an environment variable called `WASTEMASTER_DEPLOY_BOX` with something like 'root@127.0.0.1'
env_name = 'WASTEMASTER_DEPLOY_BOX'
try:
	env.hosts = os.environ[env_name]
except KeyError:
	env.hosts = ''

# Root directory to store `releases` and `current` directory
release_dir = '/home/public_html/app/'
config_file = '/home/public_html/config/.env.app'

# Database name
db_name = 'wastemaster'


def stage():
    env.hosts = os.environ['WASTEMASTER_STAGING_BOX']
    
    global config_file
    config_file = '/home/public_html/config/.env.staging'


def rollback():
    with cd(release_dir + 'releases'):
        with hide('output'):
            # get most recently created dirs
            releases = sudo('ls -tr -1')
            for release in releases.split():
                print release
            rollback = raw_input('Release to roolback to?:')
            delete = raw_input('Release to delete?:')

    # rollback
    with cd(release_dir):
        sudo('ln -nsf releases/' + rollback + ' current')

    # delete
    with cd(release_dir + 'releases'):
        sudo('rm -r -f ' + delete)

    sudo('service php7.0-fpm reload')


def deploy(branch):
    # Checkout branch
    local('git checkout ' + branch)
    local('bower install')
    local('gulp')
    local('composer install --no-ansi --no-interaction --no-progress --optimize-autoloader')
    local('composer dump-autoload -o')
    #local('vendor/bin/phpunit')
    
    # Add branch to release_name
    global release_name
    release_name = branch + '-' + release_name

    # Create tar file for production
    local('php artisan cache:clear')
    local('php artisan view:clear')
    with hide('output'):
        local('gtar -zcvf ' + release_name + '.tar.gz * --exclude=node_modules')

    # Deploy
    with cd(release_dir):
        sudo('mkdir -p releases')

    with cd(release_dir + 'releases'):
        put(
                release_name + '.tar.gz',
                release_dir + 'releases/',
                use_sudo=True
        )
        sudo('mkdir -p ' + release_name)
        with hide('output'):
            sudo('tar -xvzf ' + release_name + '.tar.gz -C ' + release_name) 
        
        sudo('rm ' + release_name + '.tar.gz')

    with cd(release_dir + 'releases/' + release_name):
        sudo('chmod -R 777 storage')
        sudo('ln -nsf ' + config_file + ' .env')
        sudo('php artisan migrate')

    with cd(release_dir):
        sudo('ln -nsf releases/' + release_name + ' current')
        sudo('sudo service php7.0-fpm reload')

    # Clean up
    local('rm ' + release_name + '.tar.gz')


"""
Execute Salt Scripts
"""


def run_salt(env_type = 'production'):
    # Transfer salt scripts
    put('srv/', '/', use_sudo=True)
    
    # Transfer deploy script
    sudo('mkdir -p ~/deploy')
   
    if env_type == 'production': 
        put('deploy/production', '~/deploy/', use_sudo=True)
        sudo('chmod u+x ~/deploy/production')
        sudo('./deploy/production')
    else:
        put('deploy/staging', '~/deploy/', use_sudo=True)
        sudo('chmod u+x ~/deploy/staging')
        sudo('./deploy/staging')


def server():
    local('sudo php artisan serve --host=0.0.0.0 --port=80')


def dev_install():
    local('composer install')
    create_database()
    local('chmod -R 777 storage/framework')
    local('npm install -g bower')
    local('bower install')
    gulp()
    local('cp .env.example .env')
    print "Make sure to update your .env file"


def commit(branch):
    local('gulp')
    local('composer dump-autoload -o')
    local('git push origin ' + branch)
    local('git checkout ' + branch)
    local('git pull origin ' + branch)
    local('php artisan migrate')
    local('vendor/bin/phpunit')
    local('git add --all')
    local('git commit')
    local('git push origin ' + branch)


def name_app():
    app_name = raw_input('What is the name of your theme? (HINT: its not named `app`)')
    if app_name == 'app':
        local('echo "nope"')
    else:
        local("sed -i '' 's/wastemaster/" + app_name + "/g' *.py")
        local("sed -i '' 's/wastemaster/" + app_name + "/g' bower.json")
        local("sed -i '' 's/wastemaster/" + app_name.capitalize() + "/g' composer.json")
        local("sed -i '' 's/WASTEMASTER/" + app_name.upper() + "/g' *.py")
        local("sed -i '' 's/wastemaster/" + app_name.capitalize() + "/g' *.py")
        local('mv src/wastemaster src/' + app_name.capitalize())
        local('composer dump-autoload -o')


def create_database():
    database = raw_input('Do you need to create a database? (y|n)')
    if database == 'y':
        user = raw_input('mysql user:')
        password = raw_input('mysql pass:')
        local('echo "create database ' + db_name + '" | mysql -u' + user + ' -p' + password)
    else:
        local('echo "okay..."')


def data_refresh():
    check = raw_input('This completely destroys any existing data. Still execute?: (y|n) ')
    if check == 'y':
        local('php artisan migrate:refresh')
        local('php artisan db:seed')


def gulp():
    local('npm install --global gulp')
    local('npm install')
    local('gulp --production')
