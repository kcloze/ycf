<?php
namespace Ycf\Model;

use Ycf\Model\ModelBase;

class ModelPdo extends ModelBase
{

    public function testInsert()
    {
        $data['pName']  = 'fww';
        $data['pValue'] = '总过万佛无法';
        $insert         = $this->db->query("INSERT INTO pdo_test( pName,pValue) VALUES ( :pName,:pValue)", $data);
        if ($insert > 0) {
            echo $this->db->lastInsertId() . "\r\n";
        } else {
            echo false . "\r\n";
        }

    }

    public function testQuery()
    {
        return $this->db->query("select  *  from pdo_test limit 1");

    }

}
