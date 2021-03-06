<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class BasePoliticalFundraisingType extends sfDoctrineRecord
{
  public function setTableDefinition()
  {
    $this->setTableName('political_fundraising_type');
    $this->hasColumn('name', 'string', 50, array('type' => 'string', 'notnull' => true, 'notblank' => true, 'length' => '50'));

    $this->option('collate', 'utf8_unicode_ci');
    $this->option('charset', 'utf8');
  }

  public function setUp()
  {
    $this->hasMany('PoliticalFundraising', array('local' => 'id',
                                                 'foreign' => 'type_id'));
  }
}