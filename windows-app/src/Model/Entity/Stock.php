<?php
namespace App\Model\Entity;

use Cake\ORM\Entity;

/**
 * Stock Entity.
 *
 * @property int $id
 * @property int $device_id
 * @property \App\Model\Entity\Device $device
 * @property float $minimum
 * @property float $maximum
 * @property string $tick_name
 * @property \Cake\I18n\Time $created
 * @property \Cake\I18n\Time $modified
 */
class Stock extends Entity
{

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
