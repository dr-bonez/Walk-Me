<?php
namespace App\Model\Entity;

use Cake\Auth\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * Walker Entity.
 *
 * @property int $id
 * @property string $email
 * @property string $password
 * @property string $bio
 * @property bool $active
 * @property float $lat
 * @property float $lng
 * @property string $name
 */
class Walker extends Entity
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
    protected function _setPassword($value)
    {
        $hasher = new DefaultPasswordHasher();
        return $hasher->hash($value);
    }

    protected $_accessible = [
        '*' => true,
        'id' => false,
    ];
}
