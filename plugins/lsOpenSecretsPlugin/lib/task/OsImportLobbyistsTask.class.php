<?php

class OsImportLobbyistsTask extends sfBaseTask
{
  protected $db = null;
  protected $rawDb = null;
  protected $insertStmt = null;
  protected $selectStmt = null;

  protected function configure()
  {
    $this->namespace        = 'opensecrets';
    $this->name             = 'import-lobbyists';
    $this->briefDescription = 'Imports all OpenSecrets lobbyist records into raw DB';
    $this->detailedDescription = '';
		
    $this->addArgument('application', null, 'The application', 'frontend');
    $this->addOption('env', null, sfCommandOption::PARAMETER_REQUIRED, 'The environment', 'dev');
    $this->addOption('limit', null, sfCommandOption::PARAMETER_REQUIRED, 'Maximum number of OpenSecrets lobbyist records to process', 1000);
    $this->addOption('debug_mode', null, sfCommandOption::PARAMETER_REQUIRED, 'Show debugging info', false);
  }

  protected function execute($arguments = array(), $options = array())
  {
    //set start time
    $time = microtime(true);

    //connect to raw database
    $this->init($arguments, $options);
    
    $count = 0;

    if ($options['debug_mode'])
    {
      print("Processing OpenSecrets lobbyist records...\n");
    }

    //open file
    $file = fopen(sfConfig::get('sf_root_dir') . '/data/opensecrets/lob_lobbyist.txt', 'r');
            
    //process rows
    while ($data = fgetcsv($file, 1000, ',', '|'))
    {
      if (count($data) != 8) { continue; }

      //parse the lobbyist name
      $data = $this->parseName($data);

      $this->insertData($data);
      
      $count++;
      
      if ($options['debug_mode'])
      {
        print("Processed lobbyist for lobbying record " . $data[0] . ": " . $data[1] . "\n");
        flush();
      }
    }


    $duration = microtime(true) - $time;
    print($count . " OpenSecrets lobbyist records processed in " . $duration . " s\n");

    
    //DONE
    LsCli::beep();
  }
  
  
  protected function init($arguments, $options)
  {
    //connect to DB
    $configuration = ProjectConfiguration::getApplicationConfiguration($arguments['application'], $options['env'], true); 
    $databaseManager = new sfDatabaseManager($configuration);
    $databaseManager->initialize($configuration);      
    $db = $databaseManager->getDatabase('main');
    $this->db = Doctrine_Manager::connection($db->getParameter('dsn'));  
    $rawDb = $databaseManager->getDatabase('raw');
    $this->rawDb = Doctrine_Manager::connection($rawDb->getParameter('dsn'));  

    //create insert statement
    $valStr = str_repeat('?, ', 13);
    $valStr = substr($valStr, 0, -2);
    $insertSql = 'INSERT INTO os_lobbyist VALUES (\'\', ' . $valStr . ')';
    $this->insertStmt = $this->rawDb->prepare($insertSql);
  }
  
  
  protected function insertData(Array $data)
  {
    $data = array_map(array($this, 'emptyToNull'), $data);

    if (!$this->insertStmt->execute($data))
    {
      throw new Exception("Couldn't insert data: (" . implode(',', $data) . ")");
    }
    
    $rowCount = $this->insertStmt->rowCount();
    
    if ($rowCount != 1)
    {
      throw new Exception("Data insert affected " . $rowCount . " rows (" . implode(',', $data) . ")");
    }
  }
  

  protected function parseName(Array $data)
  {
    $person = PersonTable::parseCommaName($data[2], null, true);
    $nameFields = array('name_first', 'name_middle', 'name_last', 'name_suffix', 'name_nick');
    
    foreach ($nameFields as $field)
    {
      $data[] = $person[$field];
    }
    
    return $data;
  }

  
  protected function emptyToNull($value)
  {
    return ($value === '') ? null : $value;
  }  
}