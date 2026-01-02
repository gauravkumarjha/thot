<?php


namespace Magecomp\Gstcharge\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddDirectoryCountryRegion implements DataPatchInterface
{



    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    ) {

        $this->moduleDataSetup = $moduleDataSetup;
    }
    public function apply()
    {
        $this->moduleDataSetup->startSetup();
        $setup = $this->moduleDataSetup;
        $table = 'directory_country_region';
       
         $this->moduleDataSetup->getConnection()->update($table, [ 'state_code' => '35'], $where = "`default_name` = 'Andaman and Nicobar Islands'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '37'], $where= "`default_name` = 'Andhra Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '12'], $where= "`default_name` = 'Arunachal Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '18'], $where= "`default_name` = 'Assam'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '10'], $where= "`default_name` = 'Bihar'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '04'], $where= "`default_name` = 'Chandigarh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '22'], $where= "`default_name` = 'Chhattisgarh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '26'], $where= "`default_name` = 'Dadra and Nagar Haveli'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '25'], $where= "`default_name` = 'Daman and Diu'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '07'], $where= "`default_name` = 'Delhi'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '30'], $where= "`default_name` = 'Goa'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '24'], $where= "`default_name` = 'Gujarat'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '06'], $where= "`default_name` = 'Haryana'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '02'], $where= "`default_name` = 'Himachal Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '01'], $where= "`default_name` = 'Jammu and Kashmir'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '20'], $where= "`default_name` = 'Jharkhand'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '29'], $where= "`default_name` = 'Karnataka'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '32'], $where= "`default_name` = 'Kerala'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '31'], $where= "`default_name` = 'Lakshadweep'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '23'], $where= "`default_name` = 'Madhya Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '27'], $where= "`default_name` = 'Maharashtra'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '14'], $where= "`default_name` = 'Manipur'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '17'], $where= "`default_name` = 'Meghalaya'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '15'], $where= "`default_name` = 'Mizoram'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '13'], $where= "`default_name` = 'Nagaland'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '21'], $where= "`default_name` = 'Odisha'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '34'], $where= "`default_name` = 'Puducherry'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '03'], $where= "`default_name` = 'Punjab'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '08'], $where= "`default_name` = 'Rajasthan'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '11'], $where= "`default_name` = 'Sikkim'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '33'], $where= "`default_name` = 'Tamil Nadu'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '36'], $where= "`default_name` = 'Telangana'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '16'], $where= "`default_name` = 'Tripura'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '09'], $where= "`default_name` = 'Uttar Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '05'], $where= "`default_name` = 'Uttarakhand'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '19'], $where= "`default_name` = 'West Bengal'");
        $this->moduleDataSetup->endSetup();
    }
    public function getAliases()
    {
        return [];
    }
    public static function getDependencies()
    {
        return [];
    }
}
