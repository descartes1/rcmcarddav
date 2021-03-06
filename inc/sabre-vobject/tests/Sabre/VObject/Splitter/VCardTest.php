<?php

namespace Sabre\VObject\Splitter;

use Sabre\VObject;

class VCardTest extends \PHPUnit_Framework_TestCase {

    function createStream($data) {

        $stream = fopen('php://memory','r+');
        fwrite($stream, $data);
        rewind($stream);
        return $stream;

    }

    function testVCardImportValidVCard() {
        $data = <<<EOT
BEGIN:VCARD
UID:foo
END:VCARD
EOT;
        $tempFile = $this->createStream($data);

        $objects = new VCard($tempFile);

        $count = 0;
        while($objects->getNext()) {
            $count++;
        }
        $this->assertEquals(1, $count);

    }

    function testVCardImportValidVCardsWithCategories() {
        $data = <<<EOT
BEGIN:VCARD
UID:card-in-foo1-and-foo2
CATEGORIES:foo1,foo2
END:VCARD
BEGIN:VCARD
UID:card-in-foo1
CATEGORIES:foo1
END:VCARD
BEGIN:VCARD
UID:card-in-foo3
CATEGORIES:foo3
END:VCARD
BEGIN:VCARD
UID:card-in-foo1-and-foo3
CATEGORIES:foo1\,foo3
END:VCARD
EOT;
        $tempFile = $this->createStream($data);

        $splitter = new VCard($tempFile);

        $count = 0;
        while($object=$splitter->getNext()) {
            $count++;
        }
        $this->assertEquals(4, $count);

    }

    function testVCardImportEndOfData() {
        $data = <<<EOT
BEGIN:VCARD
UID:foo
END:VCARD
EOT;
        $tempFile = $this->createStream($data);

        $objects = new VCard($tempFile);
        $object=$objects->getNext();

        $this->assertNull($objects->getNext());


    }

    /**
     * @expectedException \Sabre\VObject\ParseException 
     */
    function testVCardImportCheckInvalidArgumentException() {
        $data = <<<EOT
BEGIN:FOO
END:FOO
EOT;
        $tempFile = $this->createStream($data);

        $objects = new VCard($tempFile);
        while($objects->getNext()) { }

    }

    function testVCardImportMultipleValidVCards() {
        $data = <<<EOT
BEGIN:VCARD
UID:foo
END:VCARD
BEGIN:VCARD
UID:foo
END:VCARD
EOT;
        $tempFile = $this->createStream($data);

        $objects = new VCard($tempFile);

        $count = 0;
        while($objects->getNext()) {
            $count++;
        }
        $this->assertEquals(2, $count);

    }

    function testVCardImportVCardWithoutUID() {
        $data = <<<EOT
BEGIN:VCARD
END:VCARD
EOT;
        $tempFile = $this->createStream($data);

        $objects = new VCard($tempFile);

        $count = 0;
        while($objects->getNext()) {
            $count++;
        }

        $this->assertEquals(1, $count);
    }

}
