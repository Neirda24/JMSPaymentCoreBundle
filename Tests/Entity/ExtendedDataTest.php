<?php

namespace JMS\Payment\CoreBundle\Tests\Entity;

use PHPUnit\Framework\TestCase;
use InvalidArgumentException;
use JMS\Payment\CoreBundle\Entity\ExtendedData;

class ExtendedDataTest extends TestCase
{
    public function testRemoveIgnoresIfKeyDoesNotExist()
    {
        $data = new ExtendedData();

        $this->assertFalse($data->has('foo'));
        $data->remove('foo');
        $this->assertFalse($data->has('foo'));
    }

    public function testRemove()
    {
        $data = new ExtendedData();

        $this->assertFalse($data->has('foo'));
        $data->set('foo', 'foo', false);
        $this->assertTrue($data->has('foo'));
        $data->remove('foo');
        $this->assertFalse($data->has('foo'));
    }

    public function testIsEncryptionRequiredThrowsExceptionOnNonExistentKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $extendedData = new ExtendedData();
        $extendedData->isEncryptionRequired('foo');
    }

    public function testMayBePersistedThrowsExceptionOnNonExistentKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $extendedData = new ExtendedData();
        $extendedData->mayBePersisted('foo');
    }

    public function testGetThrowsExceptionOnNonExistentKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $extendedData = new ExtendedData();
        $extendedData->get('foo');
    }

    public function testSetThrowsExceptionOnEncryptionOfNonPersistedValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $extendedData = new ExtendedData();
        $extendedData->set('foo', 'bar', true, false);
    }

    /**
     * @dataProvider getTestData
     */
    public function testWithSomeData($name, $value, $encrypt, $persist)
    {
        $extendedData = new ExtendedData();
        $extendedData->set($name, $value, $encrypt, $persist);

        $this->assertEquals($value, $extendedData->get($name));

        if ($encrypt) {
            $this->assertTrue($extendedData->isEncryptionRequired($name));
        } else {
            $this->assertFalse($extendedData->isEncryptionRequired($name));
        }

        if ($persist) {
            $this->assertTrue($extendedData->mayBePersisted($name));
        } else {
            $this->assertFalse($extendedData->mayBePersisted($name));
        }
    }

    public function getTestData()
    {
        return [['account_holder', 'fooholder', false, true], ['account_number', '1234567890', true, true], ['account_cvv', '666', false, false]];
    }
}
