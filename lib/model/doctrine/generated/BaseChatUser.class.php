<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BaseChatUser extends sfDoctrineRecord
{
  public function setTableDefinition()
  {
    $this->setTableName('chat_user');
    $this->hasColumn('user_id', 'integer', null, array('type' => 'integer', 'notnull' => true));
    $this->hasColumn('room', 'integer', null, array('type' => 'integer', 'notnull' => true));
    $this->hasColumn('updated_at', 'timestamp', null, array('type' => 'timestamp', 'notnull' => true));

    $this->option('collate', 'utf8_unicode_ci');
    $this->option('charset', 'utf8');
  }

  public function setUp()
  {
    $this->hasOne('sfGuardUser as User', array('local' => 'user_id',
                                               'foreign' => 'id',
                                               'onDelete' => 'CASCADE',
                                               'onUpdate' => 'CASCADE'));
  }
}