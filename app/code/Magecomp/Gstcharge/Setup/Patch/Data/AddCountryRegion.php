<?php


namespace Magecomp\Gstcharge\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;

class AddCountryRegion implements DataPatchInterface
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
        $table = 'directory_country_region_name';
       
         $this->moduleDataSetup->getConnection()->update($table, [ 'state_code' => '35'], $where = "`name` = 'Andaman and Nicobar Islands'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '37'], $where= "`name` = 'Andhra Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '12'], $where= "`name` = 'Arunachal Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '18'], $where= "`name` = 'Assam'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '10'], $where= "`name` = 'Bihar'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '04'], $where= "`name` = 'Chandigarh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '22'], $where= "`name` = 'Chhattisgarh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '26'], $where= "`name` = 'Dadra and Nagar Haveli'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '25'], $where= "`name` = 'Daman and Diu'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '07'], $where= "`name` = 'Delhi'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '30'], $where= "`name` = 'Goa'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '24'], $where= "`name` = 'Gujarat'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '06'], $where= "`name` = 'Haryana'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '02'], $where= "`name` = 'Himachal Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '01'], $where= "`name` = 'Jammu and Kashmir'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '20'], $where= "`name` = 'Jharkhand'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '29'], $where= "`name` = 'Karnataka'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '32'], $where= "`name` = 'Kerala'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '31'], $where= "`name` = 'Lakshadweep'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '23'], $where= "`name` = 'Madhya Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '27'], $where= "`name` = 'Maharashtra'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '14'], $where= "`name` = 'Manipur'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '17'], $where= "`name` = 'Meghalaya'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '15'], $where= "`name` = 'Mizoram'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '13'], $where= "`name` = 'Nagaland'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '21'], $where= "`name` = 'Odisha'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '34'], $where= "`name` = 'Puducherry'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '03'], $where= "`name` = 'Punjab'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '08'], $where= "`name` = 'Rajasthan'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '11'], $where= "`name` = 'Sikkim'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '33'], $where= "`name` = 'Tamil Nadu'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '36'], $where= "`name` = 'Telangana'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '16'], $where= "`name` = 'Tripura'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '09'], $where= "`name` = 'Uttar Pradesh'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '05'], $where= "`name` = 'Uttarakhand'");
           $this->moduleDataSetup->getConnection()->update($table, ['state_code' => '19'], $where= "`name` = 'West Bengal'");
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
