<?php

namespace Health;

use Composer\Installer\PackageEvent;
use Composer\Package\CompletePackage;

/**
 * Utility for integration Health apps into the Health website theme.
 */
class App {

  const PACKAGE_MANAGER_NPM = 2;

  const PACKAGE_MANAGER_YARN = 1;

  /**
   * Full path to app registry file.
   *
   * @var string
   */
  protected $appRegistryFile;

  /**
   * Is the script running in CI mode.
   *
   * @var bool
   */
  protected $ciMode = FALSE;

  /**
   * List of support app types.
   *
   * @var array
   */
  protected static $supportedAppTypes = [
    'health-app-angular',
    'health-app-default',
    'health-app-react',
  ];

  /**
   * Package event.
   *
   * @var Composer\Installer\PackageEvent
   */
  protected $packageEvent;

  /**
   * The package manager used to control dependencies.
   *
   * @var string
   */
  protected $dependencyManager = 'npm';

  /**
   * Package name.
   *
   * @var string
   */
  protected $packageName;

  /**
   * Path to root of ASK project.
   *
   * @var string
   */
  protected $projectPath;

  /**
   * Path to installed app.
   *
   * @var string
   */
  protected $appPath;

  /**
   * App type.
   *
   * @var string
   */
  protected $appType;

  /**
   * Instance of class responsible for handling terminal input and output.
   *
   * @var Composer\IO\IOInterface
   */
  protected $consoleIO;

  /**
   * Name of theme in which app should be added to.
   *
   * @var string
   */
  protected $destinationTheme;

  /**
   * Defines if the app should be a production or development build.
   *
   * @var bool
   */
  protected $isProductionBuild;

  /**
   * Define if CSS files should be removed from app build files.
   *
   * @var bool
   */
  protected $removeCss;

  /**
   * The maching name of the theme in which to install the app.
   *
   * @var string
   */
  protected $targetTheme = '';

  /**
   * Constructor.
   */
  public function __construct(PackageEvent $packageEvent) {
    $this->packageEvent = $packageEvent;

    // Set installation path of the app.
    $package = $packageEvent->getOperation()->getPackage();
    $installationManager = $packageEvent->getComposer()->getInstallationManager();
    $relativeAppPath = $installationManager->getInstallPath($package);
    $vendorDirectory = $this
      ->packageEvent
      ->getComposer()
      ->getConfig()
      ->get('vendor-dir');
    $this->projectPath = dirname($vendorDirectory);
    $this->relativeAppPath = $relativeAppPath;
    $this->appPath = $this->projectPath . '/' . $relativeAppPath;
    $this->appRegistryFile = $this->projectPath . '/apps/apps.json';

    // Set app type.
    $this->appType = $package->getType();

    // Set terminal I/O interface.
    $this->consoleIO = $packageEvent->getIO();

    // Get package name.
    $path_components = explode('/', rtrim($relativeAppPath, '/'));
    $this->packageName = end($path_components);

    // Check if in CI mode. Used by Github workflow.
    $this->ciMode = empty(getenv('HEALTH_CI_MODE')) ? FALSE : TRUE;
  }

  /**
   * Selects the correct package manager to use to install the application.
   *
   * @return string
   *   The name of the package manager to use (e.g. npm, yarn)
   */
  protected function choosePackageManager() {
    if ($this->ciMode === TRUE) {
      // Use npm as the package manager when running in CI mode.
      $response = $this::PACKAGE_MANAGER_NPM;
    }
    else {
      $response = $this
        ->consoleIO
        ->ask("\n\nLock files have been found for both npm and yarn package manager systems. Which package manager would you like to use? [enter the number of the package manager to use]\n\n[1] yarn\n[2] npm\n\n");
      while (!in_array($response, [1, 2])) {
        $response = $this
          ->consoleIO
          ->ask("Not a valid selection. Which package manager would you like to use? [enter the number of the package manager to use]\n\n[1] yarn\n[2]npm\n\n");
      }
    }

    switch ($response) {

      case $this::PACKAGE_MANAGER_YARN:
        $packageManager = 'yarn';
        break;

      case $this::PACKAGE_MANAGER_NPM:
        $packageManager = 'npm';
        break;

    }

    return $packageManager;
  }

  /**
   * Execute a shell command.
   *
   * @param string $command
   *   Command to execute.
   * @param string $success_message
   *   Message to display in console if command executed successfully. Default
   *   value is '[DONE]'.
   */
  protected function executeCommand(string $command, string $success_message = '[DONE]') {
    exec($command, $output, $exit_code);
    if ($exit_code == 0) {
      $this
        ->consoleIO
        ->write($success_message);
    }
    else {
      $this
        ->consoleIO
        ->writeError("[WARNING] Something went wrong whilst running `$command`. Exit code: $exit_code");
    }
  }

  /**
   * Generic clean up tasks which should be applied to application.
   */
  protected function postIntegrationCleanup() {
    // Convert Git repo to non-Git controlled file directory. This is required
    // to be able to commit the app files to Health project codebase.
    $git_related_files_folders = [
      '.git',
      '.gitignore',
    ];
    $this
      ->consoleIO
      ->write("Checking if project uses Git... ");
    foreach ($git_related_files_folders as $file) {
      $git_file_path = $this->appPath . $file;
      if (file_exists($git_file_path)) {
        $command = 'cd ' . escapeshellcmd($this->appPath) . ' && rm -Rf ' . escapeshellcmd($git_file_path);
        $this->executeCommand($command, "Removing " . $git_file_path);
      }
    }
    $this
      ->consoleIO
      ->write("[DONE]");
  }

  /**
   * Get the content of the app registry file.
   *
   * @return array
   *   Registry of currently installed apps listed in the app registry file.
   */
  protected function getAppRegistry() {
    $data = file_get_contents($this->appRegistryFile);

    return json_decode($data);
  }

  /**
   * Write list of installed apps to the app registry file.
   *
   * @param array $data
   *   List of installed apps.
   */
  protected function setAppRegistry(array $data) {
    $success = TRUE;
    $data = json_encode($data);
    if (file_put_contents($this->appRegistryFile, $data) === FALSE) {
      $this
        ->consoleIO
        ->write('Error setting app registry.');
      $success = FALSE;
    }

    return $success;
  }

  /**
   * Get list of supported app package types.
   *
   * @return array
   *   List of supported app package types.
   *
   * @access public
   */
  public static function getSupportedAppTypes() {
    return static::$supportedAppTypes;
  }

  /**
   * Check if package type is one of the supported app types.
   *
   * @param Composer\Package\CompletePackage $package
   *   Instance of CompletePackage class.
   *
   * @return bool
   *   Whether or not the current package type is an instance of a supported
   *   app type.
   *
   * @access public
   */
  public static function isApp(CompletePackage $package) {
    $appTypes = static::getSupportedAppTypes();
    if (in_array($package->getType(), $appTypes)) {
      return TRUE;
    }
    return FALSE;
  }

  /**
   * Process app.
   *
   * Determines the type of app and processes according to that type.
   *
   * @access protected
   */
  protected function processApp() {
    // Confirm which theme the app should be added to.
    $themePaths = glob($this->projectPath . '/themes/*', GLOB_ONLYDIR);
    foreach ($themePaths as $index => $path) {
      $pathParts = explode('/', $path);
      $name = end($pathParts);
      $options[++$index] = '[' . $index . '] ' . $name;
      $themeMapping[$index] = $name;
    }

    if ($this->ciMode) {
      $theme_name = 'health';
      $index = array_search($theme_name, $themeMapping);
      $theme = $themeMapping[$index];
    }
    else {
      $text = "In which theme should the app or tool be installed? Enter the number corresponding to the theme:\n\n";
      $text .= implode("\n", $options);
      $text .= "\n\n";
      $response = $this
        ->consoleIO
        ->ask($text);
      while (!array_key_exists($response, $themeMapping)) {
        $response = $this
          ->consoleIO
          ->ask("Not a valid choice.\n\n" . $text);
      }
      $theme = $themeMapping[$response];
    }
    $this->destinationTheme = $theme;

    // Confirm if app is a production or development build.
    $isProductionBuild = $this
      ->consoleIO
      ->askConfirmation("Is this a production build? (Y, n) [default = Y]: ", TRUE);
    $this->isProductionBuild = (empty($isProductionBuild)) ? FALSE : TRUE;

    // Check if recognised app and process according to type.
    switch ($this->appType) {
      case 'health-app-angular':
        $this->processAngularApp();
        break;

      case 'health-app-react':
        $this->processReactApp();
        break;

      case 'health-app-default':
        // For projects not connected with a specific framework.
        $this->processDefaultApp();
        break;
    }

    // Generic postintegration cleanup tasks.
    $this->postIntegrationCleanup();

    // Move application directory to destination theme.
    $sourcePath = rtrim($this->appPath, '/');
    $themeAppsPath = implode('/', [
      $this->projectPath,
      'themes',
      $this->destinationTheme,
      'apps',
    ]);
    $destinationPath = implode('/', [
      $themeAppsPath,
      $this->packageName,
    ]);

    try {
      // Add app information to the app registry.
      $app_registry = $this->getAppRegistry();
      $app_registry[] = [
        'app' => $this->packageName,
        'theme' => $this->destinationTheme,
      ];
      $this->setAppRegistry($app_registry);

      // Move app files into destination theme.
      if (!file_exists($themeAppsPath)) {
        mkdir($themeAppsPath, 0766, TRUE);
      }
      if (rename($sourcePath, $destinationPath) === TRUE) {
        $this
          ->consoleIO
          ->write('Moved app to the ' . $this->destinationTheme . ' theme.');
      }
      else {
        throw new AppIntegrationException('Failed to move app to the ' . $this->destinationTheme . ' theme.');
      }
      // Create a stub in the original location. When removing a Composer
      // package, Composer will check the package exists in the originally
      // installed location. If this location does not exist then it will not
      // trigger any package uninstall hooks.
      if (mkdir($sourcePath) === TRUE) {
        $stubFile = $sourcePath . '/.gitkeep';
        file_put_contents($stubFile, '');
      }
      else {
        throw new AppIntegrationException('Failed to create original package stub.');
      };

      // Display the location of the newly installed app.
      if (file_exists($destinationPath)) {
        $this
          ->consoleIO
          ->write('App has been installed in the following location: ' . $destinationPath);
      }
      else {
        throw new AppIntegrationException('Oops, the app cannot be found in the expected location.');
      }
    }
    catch (AppIntegrationException $e) {
      $this
        ->consoleIO
        ->write('ERROR: ' . $e->getMessage());
    }
  }

  /**
   * Post install/update processing for Angular apps.
   */
  protected function processAngularApp() {
    // @todo Add processing specific to Angular based apps.
  }

  /**
   * Process default app type.
   */
  protected function processDefaultApp() {
    // Install dependencies.
    $this
      ->consoleIO
      ->write("Checking for NPM dependencies... ");

    if (file_exists($this->appPath . 'package.json')) {
      $installNpmDependencies = $this
        ->consoleIO
        ->askConfirmation("A package.json file has been detected. Would you like to install any NPM dependencies? (Y, n): [default = Y]", TRUE);
      if ($installNpmDependencies) {
        $this
          ->consoleIO
          ->write("Installing dependencies... ");
        $command = 'cd ' . escapeshellcmd($this->appPath) . ' && npm install';
        $this->executeCommand($command);
      }
      else {
        $this
          ->consoleIO
          ->write("Skipping installation of any NPM dependencies.");
      }
    }
  }

  /**
   * Post install/update processing for Angular apps.
   */
  protected function processReactApp() {
    try {
      $removeCss = $this
        ->consoleIO
        ->askConfirmation("Remove static CSS links from project? (y, N): [default = N]", FALSE);
      $this->removeCss = (empty($removeCss)) ? FALSE : TRUE;

      // Add relevant environment variables.
      $this->setReactEnvironmentalVariables();

      // Install dependencies.
      $commands = [
        'npm' => [
          'install' => 'npm install',
          'build' => 'npm run build',
        ],
        'yarn' => [
          'install' => 'yarn install',
          'build' => 'yarn build',
        ],
      ];
      if (file_exists($this->appPath . 'yarn.lock') && !file_exists($this->appPath . 'package-lock.json')) {
        $this->dependencyManager = 'yarn';
      }
      elseif (file_exists($this->appPath . 'package-lock.json') && !file_exists($this->appPath . 'yarn.lock')) {
        $this->dependencyManager = 'npm';
      }
      elseif (file_exists($this->appPath . 'package-lock.json') && file_exists($this->appPath . 'yarn.lock')) {
        $this->dependencyManager = $this->choosePackageManager();
      }
      elseif (file_exists($this->appPath . 'yarn.json') && !file_exists($this->appPath . 'package.json')) {
        $this->dependencyManager = 'yarn';
      }
      elseif (file_exists($this->appPath . 'package.json') && !file_exists($this->appPath . 'yarn.json')) {
        $this->dependencyManager = 'npm';
      }
      elseif (file_exists($this->appPath . 'package.json') && file_exists($this->appPath . 'yarn.json')) {
        $this->dependencyManager = $this->choosePackageManager();
      }
      else {
        throw new AppIntegrationException('Could not find yarn or npm configuration for this project.');
      }

      $this
        ->consoleIO
        ->write('Installing dependencies using ' . $this->dependencyManager . ' ...');
      $command = 'cd ' . escapeshellcmd($this->appPath) . ' && ' . $commands[$this->dependencyManager]['install'];
      $this->executeCommand($command);

      // Build app.
      $this
        ->consoleIO
        ->write('Building React project using ' . $this->dependencyManager . ' ...');
      if (file_exists($this->appPath . '.env.local')) {
        $command = 'cd ' . escapeshellcmd($this->appPath) . ' && ' . $commands[$this->dependencyManager]['build'];
        $this->executeCommand($command);
      }

      // Remove source files.
      if ($this->isProductionBuild && file_exists($this->appPath . "src")) {
        $this
          ->consoleIO
          ->write("Remove React project source files... ", FALSE);
        $command = 'cd ' . escapeshellcmd($this->appPath) . ' && rm -Rf ./src';
        $this->executeCommand($command);
      }
      else {
        $this
          ->consoleIO
          ->write("Non-production build. React project source files have been retained.");
      }
    }
    catch (AppIntegrationException $e) {
      $this
        ->consoleIO
        ->write('ERROR: ' . $e->getMessage());
    }

    // Remove static assets.
    $this->processAppAssets();
  }

  /**
   * Package post install handler.
   *
   * @param Composer\Installer\PackageEvent $event
   *   Instance of PackageEvent class.
   */
  public static function postPackageInstall(PackageEvent $event) {
    $package = $event
      ->getOperation()
      ->getPackage();
    if (static::isApp($package)) {
      $app = new App($event);
      $app->processApp();
    }
  }

  /**
   * Package post uninstall handler.
   */
  public static function postPackageUninstall(PackageEvent $event) {
    $package = $event
      ->getOperation()
      ->getPackage();
    if (static::isApp($package)) {
      $app = new App($event);
      $app->remove();
    }
  }

  /**
   * Remove static assets from relevant build files.
   *
   * Process relevant file to remove links to static assess which are not
   * required. For example, the app should use the Health Design System CSS
   * assets from the main site and not it's own compiled version.
   */
  protected function processAppAssets() {
    switch ($this->appType) {
      case 'health-app-angular':
        break;

      case 'health-app-react':
        // Remove <link> elements referring to CSS assets. These are not
        // required add app should use the main site's CSS.
        if ($this->removeCss) {
          $indexFilePath = $this->appPath . 'build/index.html';
          $static_css_links = [];
          if (file_exists($indexFilePath)) {
            $this
              ->consoleIO
              ->write('Removing <link> elements form index.html file... ', FALSE);
            $dom = new \DOMDocument();
            if (@$dom->loadHTMLFile($indexFilePath)) {
              $links = $dom->getElementsByTagName('link');
              foreach ($links as $link) {
                $regex_pattern = '/\/static\/css/';
                if (preg_match($regex_pattern, $link->getAttribute('href'))) {
                  array_unshift($static_css_links, $link);
                }
              }
              foreach ($static_css_links as $link) {
                $link
                  ->parentNode
                  ->removeChild($link);
              }
              // Write modified markup back to index.html file.
              if (!empty(file_put_contents($indexFilePath, $dom->saveHTML()))) {
                $this
                  ->consoleIO
                  ->write("[DONE]");
              }
              else {
                $this
                  ->consoleIO
                  ->write("[FAILED]\nError occurred when trying to write modified markup to index.html file.");
              }
            }
            else {
              $this
                ->consoleIO
                ->write("[FAILED]\nError occurred while reading index.html file.");
            }
          }
        }
        break;
    }
  }

  /**
   * Remove app from relevant theme.
   */
  protected function remove() {
    $app_registry = $this->getAppRegistry();
    foreach ($app_registry as $index => $item) {
      if ($item->app === $this->packageName) {
        $app_path = implode('/', [
          $this->projectPath,
          'themes',
          $item->theme,
          'apps',
          $this->packageName,
        ]);

        try {
          // Remove app files from theme.
          if (file_exists($app_path)) {
            if ($this->removeDirectory($app_path)) {
              $this
                ->consoleIO
                ->write($this->packageName . ' app files have been successfully removed.');
            }
            else {
              throw new AppIntegrationException('Failed to remove ' . $this->packageName . ' app files.');
            }
          }

          // Remove package from app registry.
          unset($app_registry[$index]);
        }
        catch (AppIntegrationException $e) {
          $this
            ->consoleIO
            ->write($e->getMessage());
        }
      }
    }
    $this->setAppRegistry($app_registry);
  }

  /**
   * Remove a directory including all files and sub-directories.
   *
   * The code for this function is derived from
   * https://www.beliefmedia.com.au/php-delete-directory-contents.
   *
   * @param string $dir
   *   Full path to the directory to be removed.
   *
   * @return bool
   *   Whether or not the directory was successfully removed.
   */
  protected function removeDirectory($dir) {
    if (is_dir($dir)) {
      $objects = scandir($dir);
      foreach ($objects as $object) {
        if ($object != '.' && $object != '..') {
          (filetype($dir . '/' . $object) == 'dir') ? $this->removeDirectory($dir . '/' . $object) : unlink($dir . '/' . $object);
        }
      }
      reset($objects);

      return rmdir($dir) ? TRUE : FALSE;
    }
  }

  /**
   * Create environmental variables file for React project.
   */
  protected function setReactEnvironmentalVariables() {
    $variables = [];
    $output = '# Local environmental variables used by Reach. These are generated automattically by Composer.' . PHP_EOL;
    $localEnvFilePath = $this->appPath . '.env.local';

    if ($this->isProductionBuild) {
      // Set environmental variables for production build.
      $variables['REACT_APP_PATH'] = '/themes/custom/' . $this->destinationTheme . '/apps/' . $this->packageName . '/build';
      $variables['PUBLIC_URL'] = '/themes/custom/' . $this->destinationTheme . '/apps/' . $this->packageName . '/build';
      $variables['REACT_APP_PRODUCTION'] = 1;
    }
    else {
      // Set environmental variables for development build.
      $variables['REACT_APP_PATH'] = '';
      $variables['REACT_APP_PRODUCTION'] = 0;
    }
    foreach ($variables as $key => $value) {
      $output .= $key . '=' . $value . PHP_EOL;
    }
    try {
      if (!empty($variables) && !file_put_contents($localEnvFilePath, $output)) {
        throw new AppIntegrationException('Error creating .env.local file');
      }
    }
    catch (AppIntegrationException $e) {
      $this
        ->consoleIO
        ->write($e->getMessage());
    }
  }

}
