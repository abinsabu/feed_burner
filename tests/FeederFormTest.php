<?php
namespace App\Tests;

use App\Form\FeederFormType;
use App\Entity\Feeder;
use Symfony\Component\Form\Test\TypeTestCase;

class FeederFormTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData['title'] = 'ABC';
        $formData['feed_url'] = 'https://xkcd.com/atom.xml';
        $formData['description'] = 'ABC ABC';
        $model = new Feeder();
        // $formData will retrieve data from the form submission; pass it as the second argument
        $form = $this->factory->create(FeederFormType::class, $model);

        $expected = new Feeder();
        $expected->setTitle('ABC');
        $expected->setFeedUrl('https://xkcd.com/atom.xml');
        $expected->setDescription('ABC ABC');
        $form->submit($formData);

        // This check ensures there are no transformation failures
        $this->assertTrue($form->isSynchronized());

        // check that $formData was modified as expected when the form was submitted
        $this->assertEquals($expected, $model);
    }
}