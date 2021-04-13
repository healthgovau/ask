composer install
ahoy stop
docker image remove ask_solr
docker image remove ask_solr
ahoy up
ahoy drush site:install --existing-config
composer require drupal/migrate_tools
ahoy drush en -y health_sample_content
ahoy drush cr
#ahoy drush mim --all
#ahoy drush pm-uninstall -y health_sample_content
#ahoy drush en -y health_sample_content
#ahoy drush cr
#ahoy drush mim --all
#ahoy drush pm-uninstall -y migrate
#ahoy drush pm-uninstall -y migrate_plus
#ahoy drush pm-uninstall -y migrate_tools
ahoy drush cr
ahoy login

