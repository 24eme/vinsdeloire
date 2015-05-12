# acExceptionNotifierPlugin (for symfony 1.3/1.4) #

Allows you to send by e-mails errors 500 (Exception type) that occur in production environments.

## Installation ##

  * Install the plugin (via a package)

        symfony plugin:install acExceptionNotifierPlugin

  * Install the plugin (via a git clone)
    
        git clone git://gitorious.org/acexceptionnotifierplugin/acexceptionnotifierplugin.git acExceptionNotifierPlugin

  * Activate the plugin in the `config/ProjectConfiguration.class.php`
  
        [php]
        class ProjectConfiguration extends sfProjectConfiguration
        {
          public function setup()
          {
            $this->enablePlugins(array(
              'sfDoctrinePlugin', 
              'acExceptionNotifierPlugin',
              '...'
            ));
          }
        }

  * Configure the plugin in the `config/app.yml` of the project (create it if it doesn't exist)
  
        [yaml]
        all:
          ...
          ac_exception_notifier:
            enabled: true
            email:
              from: "no-reply@email.email"
              from_name: "acExceptionNotifierPlugin"
              to: ["your_email@email.email", "another_email@email.email"]
              subject: "500 | Internal Server Error | Exception"
          ...
        
  * Clear you cache

        symfony cc